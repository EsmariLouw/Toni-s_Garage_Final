<?php
  // absolute URL to api.php in this folder
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host   = $_SERVER['HTTP_HOST'];

  // server structure herd fix for RIT structure
  $api = 'https://solace.ist.rit.edu/~it4527/BackEnd/backend/api.php';


  $apiKey = 'YOUR_SUPER_SECRET_KEY_HERE'; // must match api.php

  function http_get_json($url, $apiKey) {

    if (function_exists('curl_init')) {

      $ch = curl_init($url);

      curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => ['Accept: application/json', 'X-API-Key: ' . $apiKey],
      ]);

      $resp = curl_exec($ch);
      $err  = curl_error($ch);
      $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
      
      if ($resp === false) return ['ok' => false, 'error' => "Failed to fetch $url ($err)"];
      $data = json_decode($resp, true);
      return is_array($data) ? $data : ['ok' => false, 'error' => "Invalid JSON (HTTP $status)", 'raw' => $resp];
    }

    $ctx = stream_context_create([
      'http' => [
        'method' => 'GET',
        'header' => "Accept: application/json\r\nX-API-Key: $apiKey\r\n",
        'timeout' => 10
      ]
    ]);

    $resp = @file_get_contents($url, false, $ctx);
    if ($resp === false) return ['ok' => false, 'error' => "Failed to fetch $url"];
    $data = json_decode($resp, true);
    return is_array($data) ? $data : ['ok' => false, 'error' => 'Invalid JSON', 'raw' => $resp];
  }

  // call the API to get featured vehicles
  $featured = http_get_json($api . '?action=featured', $apiKey);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Car Dealership - Find Your Perfect Ride</title>
  <!-- bootstrap -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <!-- icons -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="styles.css" />
</head>

<body>
  <!-- header changed -->
  <header>
    <div class="navbar">
      <div class="logo">Toni's garage</div>
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

  <!-- Above fold -->
  <section class="hero" id="home">
    <div class="hero-content">
      <div class="hero-badge">Premium Dealership</div>
      <h1>Find Your Perfect Ride</h1>
      <p class="hero-subtitle">
        Discover premium vehicles with transparent pricing, flexible
        scheduling, and exceptional service. Your dream car is just a click
        away.
      </p>
      <div class="hero-buttons">
        <button
          class="main-btn"
          onclick="window.location.href='inventory.php'">
          Browse Inventory
          <svg
            width="20"
            height="20"
            viewBox="0 0 20 20"
            fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path
              d="M7.5 15L12.5 10L7.5 5"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
        </button>
        <button
          class="secondary-btn"
          onclick="window.location.href='#features'">
          Learn More
        </button>
      </div>
    </div>
    <div class="hero-image">
      <img
        src="media/tesla-modelY.jpg"
        alt="Premium Vehicle - Tesla Model Y"
        class="hero-img" />
    </div>
  </section>

  <!-- Features -->
  <section class="features" id="features">
    <div class="features-header">
      <span class="section-badge">Why Choose Us</span>
      <h2>Our Features</h2>
      <p class="section-subtitle">
        Experience the difference with our customer-first approach
      </p>
    </div>
    <div class="feature-grid">
      <div class="feature-card">
        <div class="feature-icon">
          <svg
            width="40"
            height="40"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path
              d="M12 2L2 7L12 12L22 7L12 2Z"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round" />
            <path
              d="M2 17L12 22L22 17"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round" />
            <path
              d="M2 12L12 17L22 12"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
        </div>
        <h3>Transparent Pricing</h3>
        <p>
          Clear daily rates, deposits, and fees. No hidden costs or surprises
          at checkout. What you see is what you pay.
        </p>
        <a href="#" class="feature-link">Learn more â†’</a>
      </div>
      <div class="feature-card featured">
        <div class="featured-badge">Most Popular</div>
        <div class="feature-icon">
          <svg
            width="40"
            height="40"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <circle
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="2" />
            <path
              d="M12 6V12L16 14"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round" />
          </svg>
        </div>
        <h3>Flexible Scheduling</h3>
        <p>
          Pick up today or plan ahead. Modify your bookings up to 24 hours
          before. We work around your schedule.
        </p>
        <a href="#" class="feature-link">Learn more â†’</a>
      </div>
      <div class="feature-card">
        <div class="feature-icon">
          <svg
            width="40"
            height="40"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path
              d="M9 12L11 14L15 10"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round" />
            <circle
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="2" />
          </svg>
        </div>
        <h3>Quality Vehicles</h3>
        <p>
          Well-maintained fleet with verified maintenance records. Every
          vehicle undergoes rigorous inspection.
        </p>
        <a href="#" class="feature-link">Learn more â†’</a>
      </div>
    </div>
  </section>

  <!-- Featured vehicles => slider -->
  <section class="featured-vehicles" id="special-deals">
    <div class="container">
      <div class="features-header text-center mb-5">
        <span class="section-badge">Special Deals</span>
        <h2>Featured Vehicles</h2>
        <p class="section-subtitle">Check out our best deals this week</p>
      </div>

      <!-- Carousel -->
      <div
        id="vehicleCarousel"
        class="carousel slide"
        data-bs-ride="carousel">
        <div class="carousel-indicators">
          <button
            type="button"
            data-bs-target="#vehicleCarousel"
            data-bs-slide-to="0"
            class="active"></button>
          <button
            type="button"
            data-bs-target="#vehicleCarousel"
            data-bs-slide-to="1"></button>
          <button
            type="button"
            data-bs-target="#vehicleCarousel"
            data-bs-slide-to="2"></button>

          <button class="carousel-control-prev" type="button" data-bs-target="#vehicleCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>

          <button class="carousel-control-next" type="button" data-bs-target="#vehicleCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>



        </div>

        <div class="carousel-inner">

          <?php
          if (!empty($featured['ok']) && !empty($featured['data'])):

            $chunks = array_chunk($featured['data'], 3); // 3 cards per slide
            $isFirst = true;

            foreach ($chunks as $group):
          ?>
              <div class="carousel-item <?php echo $isFirst ? 'active' : ''; ?>">
                <div class="row g-4">

                  <?php foreach ($group as $vehicle):
                    $features = json_decode($vehicle['features'], true);
                  ?>
                    <div class="col-md-4">
                      <div class="vehicle-card">

                        <div class="vehicle-image-container">
                          <img
                            src="<?php echo htmlspecialchars($vehicle['image_url']); ?>"
                            alt="<?php echo htmlspecialchars($vehicle['name']); ?>"
                            class="vehicle-img" />
                        </div>

                        <div class="vehicle-info">
                          <h3 class="vehicle-name">
                            <?php echo htmlspecialchars($vehicle['name']); ?>
                          </h3>

                          <p class="vehicle-price">
                            <?php echo number_format($vehicle['price'], 0); ?>â‚¬
                          </p>

                          <p class="vehicle-tagline">
                            <?php echo htmlspecialchars($vehicle['description']); ?>
                          </p>

                          <div class="vehicle-specs">

                            <div class="spec">
                              <i class="bi bi-car-front"></i>
                              <span><?php echo htmlspecialchars($features['model'] ?? 'N/A'); ?></span>
                            </div>

                            <div class="spec">
                              <i class="bi bi-gear"></i>
                              <span><?php echo htmlspecialchars($features['transmission'] ?? 'Manual'); ?></span>
                            </div>

                            <div class="spec">
                              <i class="bi bi-snow"></i>
                              <span><?php echo !empty($features['ac']) ? 'Air Conditioning' : 'No AC'; ?></span>
                            </div>

                            <div class="spec">
                              <i class="bi bi-display"></i>
                              <span><?php echo !empty($features['smart_screen']) ? 'Smart Screen' : 'Standard Display'; ?></span>
                            </div>

                          </div>

                          <p class="insurance-badge">
                            Sold: <?php echo (int)$vehicle['number_sold']; ?>
                          </p>

                          <button class="book-btn"
                            onclick="window.location.href='vehicle.php?id=<?php echo (int)$vehicle['id']; ?>'">
                            BOOK NOW
                          </button>

                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>

                </div>
              </div>

            <?php
              $isFirst = false;
            endforeach;

          else:
            ?>
            <div class="carousel-item active">
              <div class="text-center text-danger p-5">
                <strong>No featured vehicles available.</strong>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
  </section>

  <!-- Inventory -->
  <section class="cta-section" id="inventory">
    <div class="cta-content">
      <h2>Ready to Find Your Perfect Vehicle?</h2>
      <p>
        Browse our extensive inventory of premium vehicles and drive away
        today
      </p>
      <button
        class="cta-btn"
        onclick="window.location.href='inventory.php'">
        Browse Our Inventory
        <svg
          width="20"
          height="20"
          viewBox="0 0 20 20"
          fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path
            d="M7.5 15L12.5 10L7.5 5"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round" />
        </svg>
      </button>
    </div>
  </section>

  <!-- Footer -->
  <footer id="contact">
    <div class="footer-content">
      <div class="footer-section">
        <h3>Toni's Garage</h3>
        <p>Your trusted partner in finding the perfect vehicle.</p>
      </div>
      <div class="footer-section">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="#home">Home</a></li>
          <li><a href="#features">Features</a></li>
          <li><a href="#special-deals">Special Deals</a></li>
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
      <p>Â© 2025 Car Dealership. Built for course project.</p>
    </div>
  </footer>

  <!-- bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="app.js"></script>
</body>
</html>