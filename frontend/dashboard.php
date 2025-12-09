<?php
// 简易用户仪表盘（个人页）
session_start();

// 需要登录；未登录则跳转到登录页（保留跳转参数）
if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $target = urlencode($_SERVER['REQUEST_URI'] ?? 'dashboard.php');
    header("Location: login.php?redirect={$target}");
    exit;
}

// 占位的用户信息（如有则取 session）
$userName = $_SESSION['user_name'] ?? 'User';
$userEmail = $_SESSION['user_email'] ?? '';

// 占位的车辆列表（后续替换为后端接口数据）
$myVehicles = [
    [
        'name' => 'Sample Vehicle 1',
        'model' => 'Model X',
        'price' => 12000,
        'status' => 'Published'
    ],
    [
        'name' => 'Sample Vehicle 2',
        'model' => 'Model Y',
        'price' => 18500,
        'status' => 'Draft'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Dashboard - Toni's Garage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
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
                <a href="dashboard.php" style="font-weight:700;">My Dashboard</a>
            </nav>
            <div class="right-buttons">
                <button class="login-btn" onclick="window.location.href='index.php'">
                    Logged in as <?php echo htmlspecialchars($userName); ?>
                </button>
            </div>
        </div>
    </header>

    <main class="py-5">
        <div class="container">
            <h1 class="mb-4">My Dashboard</h1>
            <p class="text-muted mb-4">Welcome back, <?php echo htmlspecialchars($userName); ?><?php echo $userEmail ? ' (' . htmlspecialchars($userEmail) . ')' : ''; ?>.</p>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">My Vehicles</h5>
                    <small class="text-muted">Replace this placeholder list with backend data when ready.</small>
                </div>
                <div class="card-body">
                    <?php if (!empty($myVehicles)): ?>
                        <div class="row g-3">
                            <?php foreach ($myVehicles as $vehicle): ?>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($vehicle['name']); ?></h5>
                                        <p class="mb-1 text-muted"><?php echo htmlspecialchars($vehicle['model']); ?></p>
                                        <p class="mb-1"><strong><?php echo number_format($vehicle['price']); ?>€</strong></p>
                                        <span class="badge <?php echo ($vehicle['status'] === 'Published') ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo htmlspecialchars($vehicle['status']); ?>
                                        </span>
                                        <div class="mt-3 d-flex gap-2">
                                            <button class="btn btn-outline-secondary btn-sm" disabled>Edit</button>
                                            <button class="btn btn-outline-danger btn-sm" disabled>Delete</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">You have no vehicles posted yet.</p>
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
</body>

</html>