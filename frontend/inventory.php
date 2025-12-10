<?php

session_start();
// absolute URL to api.php
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'];
$api = 'https://solace.ist.rit.edu/~it4527/Toni-s_Garage_Final/backend/api.php';
$apiKey = 'YOUR_SUPER_SECRET_KEY_HERE';

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

// fetch all vehicles and types
$vehicles = http_get_json($api . '?action=vehicles', $apiKey);
$types = http_get_json($api . '?action=types', $apiKey);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Browse Inventory - Toni's Garage</title>
  <!-- bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="styles.css" />
</head>

<body>
  <!-- Header -->
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

  <!-- Hero -->
  <section class="inventory-hero">
    <h1>Browse Our Inventory</h1>
    <p>Explore our extensive collection of quality vehicles. Find your perfect match today.</p>
  </section>

  <!-- Filters -->
  <section class="filters-section">
    <div class="filters-container">
      <div class="filter-group">
        <label for="typeFilter">Vehicle Type</label>
        <select id="typeFilter">
          <option value="">All Types</option>
          <?php
          if (!empty($types['ok']) && !empty($types['data'])):
            foreach ($types['data'] as $type):
          ?>
              <option value="<?php echo htmlspecialchars($type['type_name']); ?>">
                <?php echo htmlspecialchars($type['type_name']); ?>
                (<?php echo (int)$type['vehicles_available']; ?>)
              </option>
          <?php
            endforeach;
          endif;
          ?>
        </select>
      </div>

      <div class="filter-group">
        <label for="minPrice">Min Price (€)</label>
        <input type="number" id="minPrice" placeholder="0" min="0" step="1000">
      </div>

      <div class="filter-group">
        <label for="maxPrice">Max Price (€)</label>
        <input type="number" id="maxPrice" placeholder="50000" min="0" step="1000">
      </div>

      <div class="filter-group">
        <label for="fuelFilter">Fuel Type</label>
        <select id="fuelFilter">
          <option value="">All Fuel Types</option>
          <option value="Petrol">Petrol</option>
          <option value="Diesel">Diesel</option>
          <option value="Electric">Electric</option>
          <option value="Hybrid">Hybrid</option>
        </select>
      </div>

      <button class="filter-btn" onclick="applyFilters()">
        <i class="bi bi-funnel"></i> Apply Filters
      </button>
      <button class="reset-btn" onclick="resetFilters()">
        <i class="bi bi-arrow-clockwise"></i> Reset
      </button>
    </div>
  </section>

  <!-- Inventory -->
  <section class="inventory-section">
    <div class="results-header">
      <div class="results-count" id="resultsCount">
        Loading vehicles...
      </div>
      <select class="sort-select" id="sortSelect" onchange="sortVehicles()">
        <option value="default">Sort by: Default</option>
        <option value="price-asc">Price: Low to High</option>
        <option value="price-desc">Price: High to Low</option>
        <option value="name-asc">Name: A to Z</option>
        <option value="name-desc">Name: Z to A</option>
      </select>
    </div>

    <div class="inventory-grid" id="inventoryGrid">
      <!-- vehicles loaded here by JavaScript -->
    </div>

    <div class="no-results" id="noResults" style="display: none;">
      <i class="bi bi-search"></i>
      <h3>No vehicles found</h3>
      <p>Try adjusting your filters to see more results</p>
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
  
  <!--  easier to follow, NOT in separate file -->
  <script>
    // store all vehicles data
    let allVehicles = <?php echo json_encode($vehicles['data'] ?? []); ?>;
    let filteredVehicles = [...allVehicles];

    // initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
      displayVehicles(filteredVehicles);
      updateResultsCount(filteredVehicles.length);
    });

    function displayVehicles(vehicles) {
      const grid = document.getElementById('inventoryGrid');
      const noResults = document.getElementById('noResults');

      if (vehicles.length === 0) {
        grid.style.display = 'none';
        noResults.style.display = 'block';
        return;
      }

      grid.style.display = 'grid';
      noResults.style.display = 'none';

      grid.innerHTML = vehicles.map(vehicle => `
        <div class="inventory-vehicle-card">
          <div class="inventory-vehicle-image">
            <img src="${vehicle.vehicle_image_url || 'https://via.placeholder.com/400x250?text=No+Image'}" 
                 alt="${vehicle.name}" 
                 onerror="this.src='https://via.placeholder.com/400x250?text=No+Image'">
          </div>
          <div class="inventory-vehicle-info">
            <div class="inventory-vehicle-type">${vehicle.type_name || 'Vehicle'}</div>
            <h3 class="inventory-vehicle-name">${vehicle.name}</h3>
            <p class="inventory-vehicle-model">${vehicle.model}</p>
            <p class="inventory-vehicle-price">${parseInt(vehicle.price).toLocaleString()}€</p>
            
            <div class="inventory-vehicle-specs">
              <div class="inventory-spec">
                <i class="bi bi-calendar"></i>
                <span>${vehicle.doc || 'N/A'}</span>
              </div>
              <div class="inventory-spec">
                <i class="bi bi-fuel-pump"></i>
                <span>${vehicle.fuel || 'N/A'}</span>
              </div>
              <div class="inventory-spec">
                <i class="bi bi-speedometer"></i>
                <span>${vehicle.mileage || 'N/A'}</span>
              </div>
              <div class="inventory-spec">
                <i class="bi bi-check-circle"></i>
                <span>${vehicle.owned_before ? 'Pre-owned' : 'New'}</span>
              </div>
            </div>
            
            <div class="d-flex gap-2 mt-3">
              <a href="vehicle.php?id=${vehicle.vehicle_id}" class="inventory-book-btn flex-grow-1 text-center">
                View Details
              </a>
              <a href="additions.php?vehicle_id=${vehicle.vehicle_id}" class="inventory-book-btn flex-grow-1 text-center" style="background-color: #ff6b35; border-color: #ff6b35;">
                <i class="bi bi-cart-plus"></i> Buy Now
              </a>
            </div>
          </div>
        </div>
      `).join('');
    }

    function applyFilters() {
      const typeFilter = document.getElementById('typeFilter').value.toLowerCase();
      const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
      const maxPrice = parseFloat(document.getElementById('maxPrice').value) || Infinity;
      const fuelFilter = document.getElementById('fuelFilter').value.toLowerCase();

      filteredVehicles = allVehicles.filter(vehicle => {
        const matchesType = !typeFilter || (vehicle.type_name || '').toLowerCase() === typeFilter;
        const matchesPrice = vehicle.price >= minPrice && vehicle.price <= maxPrice;
        const matchesFuel = !fuelFilter || (vehicle.fuel || '').toLowerCase() === fuelFilter;

        return matchesType && matchesPrice && matchesFuel;
      });

      displayVehicles(filteredVehicles);
      updateResultsCount(filteredVehicles.length);
      
      // reset sort to default
      document.getElementById('sortSelect').value = 'default';
    }

    function resetFilters() {
      document.getElementById('typeFilter').value = '';
      document.getElementById('minPrice').value = '';
      document.getElementById('maxPrice').value = '';
      document.getElementById('fuelFilter').value = '';
      document.getElementById('sortSelect').value = 'default';
      
      filteredVehicles = [...allVehicles];
      displayVehicles(filteredVehicles);
      updateResultsCount(filteredVehicles.length);
    }

    function sortVehicles() {
      const sortValue = document.getElementById('sortSelect').value;
      
      switch(sortValue) {
        case 'price-asc':
          filteredVehicles.sort((a, b) => a.price - b.price);
          break;
        case 'price-desc':
          filteredVehicles.sort((a, b) => b.price - a.price);
          break;
        case 'name-asc':
          filteredVehicles.sort((a, b) => a.name.localeCompare(b.name));
          break;
        case 'name-desc':
          filteredVehicles.sort((a, b) => b.name.localeCompare(a.name));
          break;
        default:
          //reset => original order
          filteredVehicles = allVehicles.filter(v => filteredVehicles.some(fv => fv.vehicle_id === v.vehicle_id));
      }
      
      displayVehicles(filteredVehicles);
    }

    function updateResultsCount(count) {
      const resultsCount = document.getElementById('resultsCount');
      resultsCount.textContent = `Showing ${count} vehicle${count !== 1 ? 's' : ''}`;
    }
  </script>
</body>

</html>