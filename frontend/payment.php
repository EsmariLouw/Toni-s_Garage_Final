<?php
// Step 3: Payment (Simulated)
// This page allows users to enter payment information and complete the purchase

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'];
$api    = 'https://solace.ist.rit.edu/~it4527/BackEnd/backend/api.php';
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

// Get vehicle ID and additions from query string
$vehicleId = filter_input(INPUT_GET, 'vehicle_id', FILTER_VALIDATE_INT);
$additionsIds = isset($_GET['additions']) ? $_GET['additions'] : [];
if (!is_array($additionsIds)) {
    $additionsIds = [$additionsIds];
}
$additionsIds = array_filter(array_map('intval', $additionsIds));

$vehicle = null;
$errorMsg = '';

// Available additions (same as additions.php)
$allAdditions = [
    ['id' => 1, 'name' => 'Heated Seats',          'price' => 350,  'description' => 'Heating system for front and rear seats', 'heated_seats' => 1, 'ac' => 0, 'smart_screen' => 0, 'custom_steering' => 0, 'icon' => 'bi-thermometer-sun'],
    ['id' => 2, 'name' => 'Automatic Air Conditioning', 'price' => 450,  'description' => 'Dual-zone automatic climate control', 'heated_seats' => 0, 'ac' => 1, 'smart_screen' => 0, 'custom_steering' => 0, 'icon' => 'bi-snow'],
    ['id' => 3, 'name' => 'Smart Touch Screen',    'price' => 600,  'description' => '10-inch digital infotainment display', 'heated_seats' => 0, 'ac' => 0, 'smart_screen' => 1, 'custom_steering' => 0, 'icon' => 'bi-tablet'],
    ['id' => 4, 'name' => 'Custom Steering Wheel', 'price' => 500,  'description' => 'Sport leather steering wheel with controls', 'heated_seats' => 0, 'ac' => 0, 'smart_screen' => 0, 'custom_steering' => 1, 'icon' => 'bi-circle-square'],
];

// Get selected additions
$selectedAdditions = [];
$additionsTotal = 0;
foreach ($additionsIds as $id) {
    if (isset($allAdditions[$id])) {
        $selectedAdditions[] = $allAdditions[$id];
        $additionsTotal += $allAdditions[$id]['price'];
    }
}

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

$basePrice = $vehicle ? (int)($vehicle['price'] ?? 0) : 0;
$totalPrice = $basePrice + $additionsTotal;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Payment - Toni's Garage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="styles.css" />
    <style>
        .payment-form {
            background: #fff;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .form-control:focus {
            border-color: #ff6b35;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
        }

        .card-icon {
            font-size: 1.5rem;
            color: #666;
        }

        .security-badge {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            text-align: center;
            margin-top: 1rem;
        }

        .security-badge i {
            color: #28a745;
            font-size: 1.5rem;
        }
    </style>
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
                <button class="login-btn" onclick="window.location.href='login.html'">
                    Log in
                </button>
            </div>
        </div>
    </header>

    <main class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="mb-4">Complete Your Purchase</h1>

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
                                <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px; font-weight: bold;">2</div>
                                <p class="mt-2 mb-0 text-muted"><small>Choose Additions</small></p>
                            </div>
                            <div class="flex-grow-1 mx-2">
                                <hr class="my-0">
                            </div>
                            <div class="text-center" style="flex: 1;">
                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px; font-weight: bold;">3</div>
                                <p class="mt-2 mb-0"><strong>Payment</strong></p>
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
                        <div class="row">
                            <!-- Payment Form -->
                            <div class="col-lg-8 mb-4">
                                <div class="payment-form">
                                    <h3 class="mb-4"><i class="bi bi-credit-card"></i> Payment Information</h3>

                                    <form id="paymentForm" method="POST" action="../backend/api.php?action=payments">
                                        <input type="hidden" name="vehicle_id" value="<?php echo (int)$vehicle['vehicle_id']; ?>">
                                        <input type="hidden" name="additions" value="<?php echo htmlspecialchars(json_encode($additionsIds)); ?>">
                                        <input type="hidden" name="total_price" value="<?php echo $totalPrice; ?>">

                                        <!-- Cardholder Name -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="credit_holder_fname" class="form-label">First Name *</label>
                                                <input type="text" class="form-control" id="credit_holder_fname"
                                                    name="credit_holder_fname" required
                                                    placeholder="John">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="credit_holder_lname" class="form-label">Last Name *</label>
                                                <input type="text" class="form-control" id="credit_holder_lname"
                                                    name="credit_holder_lname" required
                                                    placeholder="Doe">
                                            </div>
                                        </div>

                                        <!-- Card Number -->
                                        <div class="mb-3">
                                            <label for="last_four" class="form-label">
                                                <i class="bi bi-credit-card card-icon"></i> Card Number *
                                            </label>
                                            <input type="text" class="form-control" id="last_four"
                                                name="last_four" required
                                                placeholder="1234 5678 9012 3456"
                                                maxlength="19"
                                                oninput="formatCardNumber(this)">
                                        </div>

                                        <!-- Card Details Row -->
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="expiry_month" class="form-label">Expiry Month *</label>
                                                <select class="form-control" id="expiry_month" name="expiry_month" required>
                                                    <option value="">Month</option>
                                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                                        <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>">
                                                            <?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                                        </option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="expiry_year" class="form-label">Expiry Year *</label>
                                                <select class="form-control" id="expiry_year" name="expiry_year" required>
                                                    <option value="">Year</option>
                                                    <?php
                                                    $currentYear = (int)date('Y');
                                                    for ($i = $currentYear; $i <= $currentYear + 10; $i++):
                                                    ?>
                                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="cvv" class="form-label">CVV *</label>
                                                <input type="text" class="form-control" id="cvv"
                                                    name="cvv" required
                                                    placeholder="123"
                                                    maxlength="4"
                                                    pattern="[0-9]{3,4}">
                                            </div>
                                        </div>

                                        <!-- Billing Address -->
                                        <hr class="my-4">
                                        <h5 class="mb-3">Billing Address</h5>

                                        <div class="mb-3">
                                            <label for="billing_address" class="form-label">Street Address *</label>
                                            <input type="text" class="form-control" id="billing_address"
                                                name="billing_address" required
                                                placeholder="123 Main Street">
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="billing_city" class="form-label">City *</label>
                                                <input type="text" class="form-control" id="billing_city"
                                                    name="billing_city" required
                                                    placeholder="Dubrovnik">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="billing_country" class="form-label">Country *</label>
                                                <input type="text" class="form-control" id="billing_country"
                                                    name="billing_country" required
                                                    placeholder="Croatia">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="billing_zip" class="form-label">ZIP Code *</label>
                                                <input type="text" class="form-control" id="billing_zip"
                                                    name="billing_zip" required
                                                    placeholder="20000">
                                            </div>
                                        </div>

                                        <!-- Security Notice -->
                                        <div class="security-badge">
                                            <i class="bi bi-shield-check"></i>
                                            <p class="mb-0 mt-2"><strong>Secure Payment</strong></p>
                                            <small class="text-muted">This is a simulated payment. No real transaction will be processed.</small>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="additions.php?vehicle_id=<?php echo (int)$vehicle['vehicle_id']; ?>"
                                                class="btn btn-outline-secondary">
                                                <i class="bi bi-arrow-left"></i> Back to Additions
                                            </a>
                                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                                <i class="bi bi-lock-fill"></i> Complete Purchase
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Order Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Vehicle Info -->
                                        <div class="mb-3">
                                            <img src="<?php echo htmlspecialchars($vehicle['vehicle_image_url'] ?? 'https://via.placeholder.com/200x150?text=No+Image'); ?>"
                                                alt="<?php echo htmlspecialchars($vehicle['name']); ?>"
                                                class="img-fluid rounded mb-2"
                                                onerror="this.src='https://via.placeholder.com/200x150?text=No+Image';">
                                            <h6><?php echo htmlspecialchars($vehicle['name']); ?></h6>
                                            <p class="text-muted small mb-0">
                                                <?php echo htmlspecialchars($vehicle['type_name'] ?? 'Vehicle'); ?> •
                                                <?php echo htmlspecialchars($vehicle['model'] ?? ''); ?>
                                            </p>
                                        </div>

                                        <hr>

                                        <!-- Price Breakdown -->
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>Base Price:</span>
                                                <strong><?php echo number_format($basePrice); ?>€</strong>
                                            </div>
                                        </div>

                                        <?php if (!empty($selectedAdditions)): ?>
                                            <div class="mb-2">
                                                <small class="text-muted">Additions:</small>
                                                <?php foreach ($selectedAdditions as $addition): ?>
                                                    <div class="d-flex justify-content-between small">
                                                        <span class="text-muted"><?php echo htmlspecialchars($addition['name']); ?>:</span>
                                                        <span>+<?php echo number_format($addition['price']); ?>€</span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="mb-2">
                                                <small class="text-muted">No additions selected</small>
                                            </div>
                                        <?php endif; ?>

                                        <hr>

                                        <div class="d-flex justify-content-between">
                                            <h5>Total:</h5>
                                            <h4 class="text-primary"><?php echo number_format($totalPrice); ?>€</h4>
                                        </div>
                                    </div>
                                </div>

                                <!-- Help Section -->
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h6><i class="bi bi-question-circle"></i> Need Help?</h6>
                                        <p class="small text-muted mb-0">
                                            This is a simulated payment system for educational purposes.
                                            No real payment will be processed.
                                        </p>
                                    </div>
                                </div>
                            </div>
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
        // Format card number with spaces
        function formatCardNumber(input) {
            let value = input.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            input.value = formattedValue;
        }

        // Form validation
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const cardNumber = document.getElementById('card_number').value.replace(/\s/g, '');
            const cvv = document.getElementById('cvv').value;

            // Basic validation
            if (cardNumber.length < 13 || cardNumber.length > 19) {
                e.preventDefault();
                alert('Please enter a valid card number (13-19 digits).');
                return false;
            }

            if (cvv.length < 3 || cvv.length > 4) {
                e.preventDefault();
                alert('Please enter a valid CVV (3-4 digits).');
                return false;
            }

            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        });
    </script>
</body>

</html>