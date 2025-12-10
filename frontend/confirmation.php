<?php
// Confirmation page after successful payment
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
/*
// Get order details from query parameters
$vehicleId = filter_input(INPUT_GET, 'vehicle_id', FILTER_VALIDATE_INT);
$additionsIds = isset($_GET['additions']) ? $_GET['additions'] : [];
if (!is_array($additionsIds)) {
    $additionsIds = [$additionsIds];
}
$additionsIds = array_filter(array_map('intval', $additionsIds));

$totalPrice = filter_input(INPUT_GET, 'total', FILTER_VALIDATE_INT);
*/
$orderNumber = strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));

$vehicle = 99;
$errorMsg = '';


// Available additions (same as additions.php and payment.php)
$allAdditions = [
    1 => ['id' => 1, 'name' => 'Heated Seats',          'price' => 350],
    2 => ['id' => 2, 'name' => 'Automatic Air Conditioning', 'price' => 450],
    3 => ['id' => 3, 'name' => 'Smart Touch Screen',    'price' => 600],
    4 => ['id' => 4, 'name' => 'Custom Steering Wheel', 'price' => 500],
];
/*
// Get selected additions
$selectedAdditions = [];
$additionsTotal = 0;
foreach ($additionsIds as $id) {
    if (isset($allAdditions[$id])) {
        $selectedAdditions[] = $allAdditions[$id];
        $additionsTotal += $allAdditions[$id]['price'];
    }
}*/
/*
if (!$vehicleId) {
    $errorMsg = 'Missing order information.';
} else {
    $response = http_get_json($api . '?action=vehicle&id=' . $vehicleId, $apiKey);
    if (empty($response['ok']) || empty($response['data'])) {
        $errorMsg = $response['error'] ?? 'Vehicle not found.';
    } else {
        $vehicle = $response['data'];
    }
}

$basePrice = $vehicle ? (int)($vehicle['price'] ?? 0) : 0;
$calculatedTotal = $basePrice + $additionsTotal;
*/
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Confirmation - Toni's Garage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="styles.css" />
    <style>
        .confirmation-hero {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 80px 40px;
            text-align: center;
        }

        .success-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .order-card {
            background: white;
            border-radius: 12px;
            padding: 50px 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-top: -40px;
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

    <?php if ($errorMsg): ?>
        <main class="py-5">
            <div class="container">
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($errorMsg); ?>
                </div>
                <a href="inventory.php" class="btn btn-primary">
                    ← Back to Inventory
                </a>
            </div>
        </main>
    <?php elseif ($vehicle): ?>

    <!-- Success Hero -->
    <section class="confirmation-hero">
        <div class="success-icon">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <h1>Thank You!</h1>
        <p class="mb-0">Your order has been confirmed.</p>
    </section>

    <main class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    
                    <div class="order-card text-center">
                        <h4 class="mb-4">Payment Successful</h4>
                        
                        <div class="mb-4">
                            <i class="bi bi-envelope-check" style="font-size: 64px; color: #28a745;"></i>
                        </div>
                        
                        <p class="lead mb-4">
                            A receipt has been sent to your email address.
                        </p>
                        
                        <p class="text-muted mb-4">
                            Order Number: <strong><?php echo htmlspecialchars($orderNumber); ?></strong>
                        </p>
                        
                        <hr class="my-4">
                        
                        <p class="mb-4">
                            Our team will contact you shortly to arrange delivery of your vehicle.
                        </p>
                        
                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="index.php" class="btn btn-primary">
                                Back to Home
                            </a>
                            <a href="inventory.php" class="btn btn-outline-secondary">
                                Browse More Vehicles
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <?php endif; ?>

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
</body>

</html>