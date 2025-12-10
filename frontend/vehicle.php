<?php
// absolute URL to api.php (same as index.php & inventory.php)
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'];
$api    = 'https://solace.ist.rit.edu/~it4527/Toni-s_Garage_Final/backend/api.php';

$apiKey = 'YOUR_SUPER_SECRET_KEY_HERE'; // must match api.php

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

    // Fallback without cURL
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
    return is_array($data) ? $data : [
        'ok'    => false,
        'error' => 'Invalid JSON',
        'raw'   => $resp
    ];
}

// ===== get vehicle id from query string =====
$vehicleId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$vehicle   = null;
$errorMsg  = '';

if (!$vehicleId) {
    $errorMsg = 'Missing or invalid vehicle ID.';
} else {
    $response = http_get_json($api . '?action=vehicle&id=' . $vehicleId, $apiKey);

    if (empty($response['ok']) || empty($response['data'])) {
        $errorMsg = $response['error'] ?? 'Vehicle not found.';
    } else {
        $vehicle = $response['data']; // single row
    }
}

// helper for condition label
function vehicle_condition($owned_before)
{
    return !empty($owned_before) ? 'Pre-owned' : 'New';
}

// collect available colors
$colorCodes = [];
if ($vehicle) {
    foreach (['color_1', 'color_2', 'color_3', 'color_4'] as $field) {
        if (!empty($vehicle[$field])) {
            $colorCodes[] = ltrim($vehicle[$field], '#');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        <?php echo $vehicle ? htmlspecialchars($vehicle['name']) . ' - Toni\'s Garage' : 'Vehicle Details - Toni\'s Garage'; ?>
    </title>
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="styles.css" />
</head>

<body>
    <!-- Header (same as inventory.php) -->
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

            <?php if ($errorMsg): ?>
                <div class="alert alert-danger mt-4">
                    <?php echo htmlspecialchars($errorMsg); ?>
                </div>
                <a href="inventory.php" class="btn btn-outline-secondary mt-3">
                    ← Back to Inventory
                </a>
            <?php elseif ($vehicle): ?>

                <!-- Top hero: image + main info -->
            <div class="row g-4 align-items-center mb-5">
                <div class="col-md-6">

                    <?php
                    $mainImage = $vehicle['vehicle_image_url'] ?? 'https://via.placeholder.com/800x500?text=No+Image';
                    $interiorImage = $vehicle['interior_image_url'] ?? null;
                    ?>

                    <?php if (!empty($interiorImage)): ?>
                        <!-- Carousel -->
                        <div id="vehicleCarousel" class="carousel slide shadow-sm rounded" data-bs-ride="carousel">
                            <div class="carousel-inner rounded">

                                <!-- Main image -->
                                <div class="carousel-item active">
                                    <img
                                            src="<?php echo htmlspecialchars($mainImage); ?>"
                                            class="d-block w-100 img-fluid"
                                            alt="<?php echo htmlspecialchars($vehicle['name']); ?>"
                                            onerror="this.src='https://via.placeholder.com/800x500?text=No+Image';">
                                </div>

                                <!-- Interior image -->
                                <div class="carousel-item">
                                    <img
                                            src="<?php echo htmlspecialchars($interiorImage); ?>"
                                            class="d-block w-100 img-fluid"
                                            alt="Interior"
                                            onerror="this.remove();">
                                </div>

                            </div>

                            <!-- Controls -->
                            <button class="carousel-control-prev" type="button" data-bs-target="#vehicleCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>

                            <button class="carousel-control-next" type="button" data-bs-target="#vehicleCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        </div>

                    <?php else: ?>
                        <!-- Single image fallback -->
                        <img
                                src="<?php echo htmlspecialchars($mainImage); ?>"
                                alt="<?php echo htmlspecialchars($vehicle['name']); ?>"
                                class="img-fluid rounded shadow-sm"
                                onerror="this.src='https://via.placeholder.com/800x500?text=No+Image';" />
                    <?php endif; ?>

                </div>
                <div class="col-md-6">
                    <h1 class="mb-2">
                        <?php echo htmlspecialchars($vehicle['name']); ?>
                    </h1>
                    <p class="text-muted mb-1">
                        <?php echo htmlspecialchars(($vehicle['type_name'] ?? 'Vehicle') . ' • ' . ($vehicle['model'] ?? '')); ?>
                    </p>
                    <h2 class="text-primary mb-3">
                        <?php echo number_format((int)($vehicle['price'] ?? 0)) . '€'; ?>
                    </h2>

                    <div class="mb-3">
                            <span class="badge bg-<?php echo !empty($vehicle['owned_before']) ? 'warning' : 'success'; ?> me-2">
                                <?php echo vehicle_condition($vehicle['owned_before'] ?? 0); ?>
                            </span>
                        <?php if (!empty($vehicle['doc'])): ?>
                            <span class="badge bg-secondary">
                                    <i class="bi bi-calendar"></i>
                                    First registration: <?php echo htmlspecialchars($vehicle['doc']); ?>
                                </span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($vehicle['description'])): ?>
                        <p class="mb-4">
                            <?php echo nl2br(htmlspecialchars($vehicle['description'])); ?>
                        </p>
                    <?php endif; ?>

                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <a href="inventory.php" class="btn btn-outline-secondary">
                            ← Back to Inventory
                        </a>
                        <a href="additions.php?vehicle_id=<?php echo (int)$vehicle['vehicle_id']; ?>" class="btn btn-primary btn-lg">
                            <i class="bi bi-cart-plus"></i> Buy Now
                        </a>
                        <a href="index.php#contact" class="btn btn-outline-primary">
                            Contact Seller
                        </a>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-uppercase text-muted small mb-2">Fuel & Mileage</h6>
                                <p class="mb-1"><i class="bi bi-fuel-pump me-2"></i>
                                    Fuel: <?php echo htmlspecialchars($vehicle['fuel'] ?? 'N/A'); ?>
                                </p>
                                <p class="mb-0"><i class="bi bi-speedometer me-2"></i>
                                    Mileage: <?php echo htmlspecialchars($vehicle['mileage'] ?? 'N/A'); ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-uppercase text-muted small mb-2">Performance</h6>
                                <p class="mb-1"><i class="bi bi-gear-wide-connected me-2"></i>
                                    Engine: <?php echo htmlspecialchars($vehicle['engine_name'] ?? 'N/A'); ?>
                                    <?php if (!empty($vehicle['engine_type'])): ?>
                                        (<?php echo htmlspecialchars($vehicle['engine_type']); ?>)
                                    <?php endif; ?>
                                </p>
                                <p class="mb-1">
                                    <i class="bi bi-lightning-charge me-2"></i>
                                    Horsepower: <?php echo htmlspecialchars($vehicle['horse_power'] ?? 'N/A'); ?>
                                </p>
                                <p class="mb-0">
                                    <i class="bi bi-shift-fill me-2"></i>
                                    Transmission:
                                    <?php echo htmlspecialchars($vehicle['transmission_name'] ?? 'N/A'); ?>
                                    <?php if (!empty($vehicle['transmission_type'])): ?>
                                        (<?php echo htmlspecialchars($vehicle['transmission_type']); ?>)
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <!-- Additional info: colors, stats, seller -->
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <h5 class="mb-3">Available Colors</h5>
                            <?php if (!empty($colorCodes)): ?>
                                <div class="d-flex flex-wrap gap-3">
                                    <?php foreach ($colorCodes as $code): ?>
                                        <div class="text-center">
                                            <div
                                                    class="rounded border mb-1"
                                                    style="width:40px;height:40px;background-color:#<?php echo htmlspecialchars($code); ?>;">
                                            </div>
                                            <small>#<?php echo htmlspecialchars($code); ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">Color information not available.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <h5 class="mb-3">Statistics</h5>
                            <p class="mb-2">
                                <i class="bi bi-people-fill me-2"></i>
                                Units sold: <?php echo htmlspecialchars($vehicle['number_sold'] ?? 0); ?>
                            </p>
                            <p class="mb-0 text-muted small">
                                These stats give you an idea of how popular this model is.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <h5 class="mb-3">Seller Information</h5>
                            <p class="mb-1">
                                <i class="bi bi-person-circle me-2"></i>
                                <?php echo htmlspecialchars($vehicle['seller_name'] ?? 'Unknown seller'); ?>
                            </p>
                            <?php if (!empty($vehicle['seller_email'])): ?>
                                <p class="mb-1">
                                    <i class="bi bi-envelope me-2"></i>
                                    <a href="mailto:<?php echo htmlspecialchars($vehicle['seller_email']); ?>">
                                        <?php echo htmlspecialchars($vehicle['seller_email']); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            <p class="text-muted small mb-0">
                                Reach out for a test drive, financing options, or any additional questions.
                            </p>
                        </div>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </main>

    <!-- Footer (copied from inventory.php so everything matches) -->
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

    <!-- bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- global site JS (navbar / animations) -->
    <script src="app.js"></script>
</body>

</html>