<?php
// Step 2: Choose Car Additions
// This page allows users to select additional features like GPS, Radio, etc.

session_start();

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'];
$api    = 'https://solace.ist.rit.edu/~it4527/Toni-s_Garage_Final/backend/api.php';
$apiKey = 'YOUR_SUPER_SECRET_KEY_HERE';

function http_get_json($url, $apiKey)
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'X-API-Key: ' . $apiKey
            ],
        ]);
        $resp   = curl_exec($ch);
        $err    = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if ($resp === false) {
            return ['ok' => false, 'error' => "Failed to fetch $url ($err)"];
        }
        $data = json_decode($resp, true);
        return is_array($data) ? $data : [
            'ok'    => false,
            'error' => "Invalid JSON (HTTP $status)",
            'raw'   => $resp
        ];
    }
    $ctx = stream_context_create([
        'http' => [
            'method'  => 'GET',
            'header'  => "Accept: application/json\r\nX-API-Key: $apiKey\r\n",
            'timeout' => 10
        ]
    ]);
    $resp = @file_get_contents($url, false, $ctx);
    if ($resp === false) {
        return ['ok' => false, 'error' => "Failed to fetch $url"];
    }
    $data = json_decode($resp, true);
    return is_array($data) ? $data : ['ok' => false, 'error' => 'Invalid JSON', 'raw' => $resp];
}

// Get vehicle ID from query string
$vehicleId = filter_input(INPUT_GET, 'vehicle_id', FILTER_VALIDATE_INT);
$vehicle   = null;
$errorMsg  = '';

if (!$vehicleId) {
    $errorMsg = 'Missing or invalid vehicle ID.';
} else {
    $response = http_get_json($api . '?action=vehicle&id=' . $vehicleId, $apiKey);
    if (empty($response['ok']) || empty($response['data'])) {
        $errorMsg = $response['error'] ?? 'Vehicle not found.';
    } else {
        $vehicle = $response['data'];
    }
}

// Available additions (hardcoded for now, can be fetched from API later) - Changed with our interior features
$additions = [
    ['id' => 1, 'name' => 'Heated Seats',          'price' => 350,  'description' => 'Heating system for front and rear seats','heated_seats' => 1, 'ac' => 0, 'smart_screen' => 0, 'custom_steering' => 0, 'icon' => 'bi-thermometer-sun'],
    ['id' => 2, 'name' => 'Automatic Air Conditioning', 'price' => 450,  'description' => 'Dual-zone automatic climate control','heated_seats' => 0, 'ac' => 1, 'smart_screen' => 0, 'custom_steering' => 0, 'icon' => 'bi-snow'],
    ['id' => 3, 'name' => 'Smart Touch Screen',    'price' => 600,  'description' => '10-inch digital infotainment display','heated_seats' => 0, 'ac' => 0, 'smart_screen' => 1, 'custom_steering' => 0, 'icon' => 'bi-tablet'],
    ['id' => 4, 'name' => 'Custom Steering Wheel', 'price' => 500,  'description' => 'Sport leather steering wheel with controls', 'heated_seats' => 0, 'ac' => 0, 'smart_screen' => 0, 'custom_steering' => 1, 'icon' => 'bi-circle-square'],
];




?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Choose Additions - Toni's Garage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="styles.css" />
</head>

<body>
    <header>
        <div class="navbar">
            <div class="logo" onclick="window.location.href='index.php'" style="cursor:pointer;">Toni's garage</div>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="inventory.php">Inventory</a>
                <a href="about.php">About</a>
                <a href="index.php#contact">Contact</a>
            </nav>
            <div class="right-buttons">
                <?php
                if (!empty($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <button class="login-btn" onclick="window.location.href='logout.php'">
                        Log out
                    </button>
                <?php else: ?>
                    <button class="logout-btn" onclick="window.location.href='login.php'">
                        Log in
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="mb-4">Choose Car Additions</h1>

                    <!-- Progress Steps -->
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-center" style="flex: 1;">
                                <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px; font-weight: bold;">1</div>
                                <p class="mt-2 mb-0 text-muted"><small>Select Vehicle</small></p>
                            </div>
                            <div class="flex-grow-1 mx-2">
                                <hr class="my-0">
                            </div>
                            <div class="text-center" style="flex: 1;">
                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px; font-weight: bold;">2</div>
                                <p class="mt-2 mb-0"><strong>Choose Additions</strong></p>
                            </div>
                            <div class="flex-grow-1 mx-2">
                                <hr class="my-0">
                            </div>
                            <div class="text-center" style="flex: 1;">
                                <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px; font-weight: bold;">3</div>
                                <p class="mt-2 mb-0 text-muted"><small>Payment</small></p>
                            </div>
                        </div>
                    </div>

                    <?php if ($errorMsg): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($errorMsg); ?>
                        </div>
                        <a href="inventory.php" class="btn btn-outline-secondary">
                            ← Back to Inventory
                        </a>
                    <?php elseif ($vehicle): ?>
                        <!-- Selected Vehicle Summary -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="bi bi-car-front"></i> Selected Vehicle</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <img src="<?php echo htmlspecialchars($vehicle['vehicle_image_url'] ?? 'https://via.placeholder.com/200x150?text=No+Image'); ?>"
                                            alt="<?php echo htmlspecialchars($vehicle['name']); ?>"
                                            class="img-fluid rounded"
                                            onerror="this.src='https://via.placeholder.com/200x150?text=No+Image';">
                                    </div>
                                    <div class="col-md-9">
                                        <h4><?php echo htmlspecialchars($vehicle['name']); ?></h4>
                                        <p class="text-muted mb-2"><?php echo htmlspecialchars($vehicle['type_name'] ?? 'Vehicle'); ?> • <?php echo htmlspecialchars($vehicle['model'] ?? ''); ?></p>
                                        <h5 class="text-primary mb-0">Base Price: <?php echo number_format((int)($vehicle['price'] ?? 0)); ?>€</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additions Selection -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Available Additions</h4>
                                <p class="mb-0 small">Select any additional features you would like to add to your vehicle</p>
                            </div>
                            <div class="card-body">
                                <form id="additionsForm">
                                    <input type="hidden" name="vehicle_id" value="<?php echo (int)$vehicle['vehicle_id']; ?>">

                                    <div class="row g-3">
                                        <?php foreach ($additions as $addition): ?>
                                            <div class="col-md-6">
                                                <div class="form-check p-3 border rounded h-100 addition-card"
                                                    style="cursor: pointer; transition: all 0.3s;"
                                                    onmouseover="this.style.backgroundColor='#f8f9fa';"
                                                    onmouseout="this.style.backgroundColor='';">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="additions[]"
                                                        value="<?php echo $addition['id']; ?>"
                                                        id="addition<?php echo $addition['id']; ?>"
                                                        data-price="<?php echo $addition['price']; ?>"
                                                        onchange="updateTotalPrice()">
                                                    <label class="form-check-label w-100" for="addition<?php echo $addition['id']; ?>" style="cursor: pointer;">
                                                        <div class="d-flex align-items-start">
                                                            <div class="me-3">
                                                                <i class="bi <?php echo $addition['icon']; ?>" style="font-size: 2rem; color: #ff6b35;"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h5 class="mb-1"><?php echo htmlspecialchars($addition['name']); ?></h5>
                                                                <p class="text-muted mb-2 small"><?php echo htmlspecialchars($addition['description']); ?></p>
                                                                <strong class="text-primary">+<?php echo number_format($addition['price']); ?>€</strong>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Price Summary -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="mb-3">Price Summary</h5>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Base Vehicle Price:</span>
                                            <strong id="basePrice"><?php echo number_format((int)($vehicle['price'] ?? 0)); ?>€</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Selected Additions:</span>
                                            <strong id="additionsPrice">0€</strong>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <h5>Total Price:</h5>
                                            <h4 class="text-primary" id="totalPrice"><?php echo number_format((int)($vehicle['price'] ?? 0)); ?>€</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="vehicle.php?id=<?php echo (int)$vehicle['vehicle_id']; ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Vehicle Details
                            </a>
                            <button class="btn btn-primary btn-lg" onclick="proceedToPayment()" id="proceedBtn">
                                Continue to Payment <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer id="contact">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Toni's Garage</h3>
                <p>Your trusted partner in finding the perfect vehicle.</p>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="inventory.php">Inventory</a></li>
                    <li><a href="about.php">About Us</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact</h4>
                <ul>
                    <li>https://discord.gg/yc3B4swt</li>
                    <li>RIT Croatia, Dubrovnik</li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Hours</h4>
                <ul>
                    <li>Mon-Fri: 9:00 AM - 8:00 PM</li>
                    <li>Saturday/Sunday: Vacation</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 Car Dealership. Built for course project.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="app.js"></script>
    <script>
        const basePrice = <?php echo (int)($vehicle['price'] ?? 0); ?>;

        // Update price when additions are selected
        function updateTotalPrice() {
            let additionsPrice = 0;
            const selectedAdditions = document.querySelectorAll('input[name="additions[]"]:checked');

            selectedAdditions.forEach(checkbox => {
                additionsPrice += parseInt(checkbox.dataset.price);
            });

            document.getElementById('additionsPrice').textContent = additionsPrice.toLocaleString() + '€';
            const total = basePrice + additionsPrice;
            document.getElementById('totalPrice').textContent = total.toLocaleString() + '€';
        }

        // Make addition cards clickable
        document.querySelectorAll('.addition-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'LABEL') {
                    const checkbox = this.querySelector('input[type="checkbox"]');
                    checkbox.checked = !checkbox.checked;
                    updateTotalPrice();
                }
            });
        });

        function proceedToPayment() {
            const form = document.getElementById('additionsForm');
            const formData = new FormData(form);
            const vehicleId = formData.get('vehicle_id');
            const additions = formData.getAll('additions[]');

            // Build query string for payment page
            const params = new URLSearchParams();
            params.append('vehicle_id', vehicleId);
            additions.forEach(id => params.append('additions[]', id));

            // Redirect to payment page
            window.location.href = 'payment.php?' + params.toString();
        }
    </script>
</body>

</html>