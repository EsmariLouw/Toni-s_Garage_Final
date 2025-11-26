// Smooth scroll for navigation links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
    }
  });
});

// Add scroll effect to header with smooth animation
let lastScroll = 0;
const header = document.querySelector("header");
let isScrolling;

window.addEventListener("scroll", () => {
  const currentScroll = window.pageYOffset;

  // Clear the timeout throughout the scroll
  window.clearTimeout(isScrolling);

  // Only hide/show after scrolling more than 5px to prevent jitter
  if (Math.abs(currentScroll - lastScroll) > 5) {
    if (currentScroll <= 0) {
      header.style.transform = "translateY(0)";
      header.style.boxShadow = "0 2px 10px rgba(0, 0, 0, 0.3)";
    } else if (currentScroll > lastScroll && currentScroll > 80) {
      // Scrolling down & past threshold
      header.style.transform = "translateY(-100%)";
    } else if (currentScroll < lastScroll) {
      // Scrolling up
      header.style.transform = "translateY(0)";
      header.style.boxShadow = "0 4px 20px rgba(0, 0, 0, 0.5)";
    }

    lastScroll = currentScroll;
  }

  // Set a timeout to run after scrolling ends
  isScrolling = setTimeout(() => {
    lastScroll = currentScroll;
  }, 66);
});

// Add animation on scroll for feature cards
const observerOptions = {
  threshold: 0.1,
  rootMargin: "0px 0px -50px 0px",
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = "1";
      entry.target.style.transform = "translateY(0)";
    }
  });
}, observerOptions);

// Observe all feature cards
document.addEventListener("DOMContentLoaded", () => {
  const featureCards = document.querySelectorAll(".feature-card");
  featureCards.forEach((card, index) => {
    card.style.opacity = "0";
    card.style.transform = "translateY(30px)";
    card.style.transition = `all 0.6s ease ${index * 0.1}s`;
    observer.observe(card);
  });

  // Observe vehicle cards
  const vehicleCards = document.querySelectorAll(".vehicle-card");
  vehicleCards.forEach((card, index) => {
    card.style.opacity = "0";
    card.style.transform = "translateY(30px)";
    card.style.transition = `all 0.6s ease ${index * 0.1}s`;
    observer.observe(card);
  });
});

// Add loading animation
window.addEventListener("load", () => {
  document.body.style.opacity = "0";
  document.body.style.transition = "opacity 0.5s";
  setTimeout(() => {
    document.body.style.opacity = "1";
  }, 100);
});

// API Configuration
const API_BASE_URL = "https://localhost/api.php";
const API_ENDPOINT = `${API_BASE_URL}?action=vehicles`;

// Fetch vehicles from API
async function fetchVehicles() {
  try {
    const response = await fetch(API_ENDPOINT);

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    // Handle different response structures
    const vehicles = data.vehicles || data || [];

    if (!Array.isArray(vehicles) || vehicles.length === 0) {
      throw new Error("No vehicles data received");
    }

    return vehicles;
  } catch (error) {
    console.error("Error fetching vehicles:", error);
    showErrorMessage("Failed to load vehicles. Please try again later.");
    return [];
  }
}

// Format price to display as daily rate
function formatPrice(price) {
  if (typeof price === "number") {
    return `${price}€/day`;
  }
  return price;
}

// Truncate description to first few sentences
function truncateDescription(description, maxLength = 80) {
  if (!description) return "No surprises! What you see is what you get.";

  if (description.length <= maxLength) {
    return description;
  }

  // Try to cut at sentence boundary
  const truncated = description.substring(0, maxLength);
  const lastPeriod = truncated.lastIndexOf(".");
  const lastExclamation = truncated.lastIndexOf("!");
  const lastQuestion = truncated.lastIndexOf("?");

  const lastSentenceEnd = Math.max(lastPeriod, lastExclamation, lastQuestion);

  if (lastSentenceEnd > maxLength * 0.5) {
    return truncated.substring(0, lastSentenceEnd + 1);
  }

  return truncated + "...";
}

// Generate vehicle card HTML
function createVehicleCard(vehicle) {
  const features = vehicle.features || {};
  const imageUrl = vehicle.image_url || "media/default-car.png";
  const name = vehicle.name || "Unknown Vehicle";
  const price = formatPrice(vehicle.price);
  const description = truncateDescription(vehicle.description);
  const transmission = features.transmission || "Automatic";
  const model = features.model || "";

  // Build specs based on available data
  let specsHTML = "";

  if (model) {
    specsHTML += `
              <div class="spec">
                  <i class="bi bi-car-front"></i>
                  <span>${model}</span>
              </div>
          `;
  }

  specsHTML += `
          <div class="spec">
              <i class="bi bi-gear"></i>
              <span>${transmission}</span>
          </div>
      `;

  if (features.ac) {
    specsHTML += `
              <div class="spec">
                  <i class="bi bi-snow"></i>
                  <span>Air conditioning</span>
              </div>
          `;
  }

  if (features.smart_screen) {
    specsHTML += `
              <div class="spec">
                  <i class="bi bi-display"></i>
                  <span>Smart Screen</span>
              </div>
          `;
  }

  return `
          <div class="col-md-4">
              <div class="vehicle-card">
                  <div class="vehicle-image-container">
                      <img
                          src="${imageUrl}"
                          alt="${name}"
                          class="vehicle-img"
                          onerror="this.src='media/default-car.png'"
                      />
                  </div>
                  <div class="vehicle-info">
                      <h3 class="vehicle-name">${name.toUpperCase()}</h3>
                      <p class="vehicle-price">${price}</p>
                      <p class="vehicle-tagline">${description}</p>
                      <div class="vehicle-specs">
                          ${specsHTML}
                      </div>
                      <p class="insurance-badge">Full Insurance</p>
                      <button class="book-btn">BOOK NOW</button>
                  </div>
              </div>
          </div>
      `;
}

// Render vehicles into carousel
function renderVehiclesCarousel(vehicles) {
  const carouselInner = document.querySelector(
    "#vehicleCarousel .carousel-inner"
  );
  const carouselIndicators = document.querySelector(
    "#vehicleCarousel .carousel-indicators"
  );

  if (!carouselInner) {
    console.error("Carousel inner element not found");
    return;
  }

  // Clear existing content
  carouselInner.innerHTML = "";
  if (carouselIndicators) {
    carouselIndicators.innerHTML = "";
  }

  // Group vehicles into slides (3 per slide)
  const vehiclesPerSlide = 3;
  const slides = [];

  for (let i = 0; i < vehicles.length; i += vehiclesPerSlide) {
    slides.push(vehicles.slice(i, i + vehiclesPerSlide));
  }

  // If no vehicles, show empty state
  if (slides.length === 0) {
    carouselInner.innerHTML = `
              <div class="carousel-item active">
                  <div class="row g-4">
                      <div class="col-12 text-center py-5">
                          <p>No vehicles available at the moment.</p>
                      </div>
                  </div>
              </div>
          `;
    return;
  }

  // Render each slide
  slides.forEach((slideVehicles, slideIndex) => {
    const isActive = slideIndex === 0 ? "active" : "";
    const vehiclesHTML = slideVehicles
      .map((vehicle) => createVehicleCard(vehicle))
      .join("");

    carouselInner.innerHTML += `
              <div class="carousel-item ${isActive}">
                  <div class="row g-4">
                      ${vehiclesHTML}
                  </div>
              </div>
          `;

    // Add carousel indicator
    if (carouselIndicators) {
      carouselIndicators.innerHTML += `
                  <button
                      type="button"
                      data-bs-target="#vehicleCarousel"
                      data-bs-slide-to="${slideIndex}"
                      class="${isActive}"
                      ${isActive ? 'aria-current="true"' : ""}
                      aria-label="Slide ${slideIndex + 1}"
                  ></button>
              `;
    }
  });

  // Reinitialize carousel after rendering
  const carousel = document.querySelector("#vehicleCarousel");
  if (carousel) {
    const bsCarousel = new bootstrap.Carousel(carousel, {
      interval: 5000,
      wrap: true,
      keyboard: true,
    });
  }

  // Re-observe vehicle cards for animations
  setTimeout(() => {
    const vehicleCards = document.querySelectorAll(".vehicle-card");
    vehicleCards.forEach((card, index) => {
      card.style.opacity = "0";
      card.style.transform = "translateY(30px)";
      card.style.transition = `all 0.6s ease ${index * 0.1}s`;
      observer.observe(card);
    });
  }, 100);
}

// Show error message
function showErrorMessage(message) {
  const carouselInner = document.querySelector(
    "#vehicleCarousel .carousel-inner"
  );
  if (carouselInner) {
    carouselInner.innerHTML = `
              <div class="carousel-item active">
                  <div class="row g-4">
                      <div class="col-12 text-center py-5">
                          <div class="alert alert-warning" role="alert">
                              <i class="bi bi-exclamation-triangle"></i>
                              <p class="mb-0">${message}</p>
                          </div>
                      </div>
                  </div>
              </div>
          `;
  }
}

// Show loading state
function showLoadingState() {
  const carouselInner = document.querySelector(
    "#vehicleCarousel .carousel-inner"
  );
  if (carouselInner) {
    carouselInner.innerHTML = `
              <div class="carousel-item active">
                  <div class="row g-4">
                      <div class="col-12 text-center py-5">
                          <div class="spinner-border text-primary" role="status">
                              <span class="visually-hidden">Loading...</span>
                          </div>
                          <p class="mt-3">Loading vehicles...</p>
                      </div>
                  </div>
              </div>
          `;
  }
}

// Initialize vehicles on page load
async function initializeVehicles() {
  showLoadingState();
  const vehicles = await fetchVehicles();

  if (vehicles.length > 0) {
    renderVehiclesCarousel(vehicles);
  }
}

// Bootstrap carousel auto-cycle with longer interval
document.addEventListener("DOMContentLoaded", () => {
  // Initialize vehicles from API
  initializeVehicles();

  // Existing carousel initialization (will be overridden by renderVehiclesCarousel)
  const carousel = document.querySelector("#vehicleCarousel");
  if (carousel) {
    const bsCarousel = new bootstrap.Carousel(carousel, {
      interval: 5000,
      wrap: true,
      keyboard: true,
    });
  }
});

console.log("Car Dealership Website Loaded Successfully!");
