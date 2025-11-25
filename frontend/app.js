// Smooth scroll for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add scroll effect to header with smooth animation
let lastScroll = 0;
const header = document.querySelector('header');
let isScrolling;

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    // Clear the timeout throughout the scroll
    window.clearTimeout(isScrolling);
    
    // Only hide/show after scrolling more than 5px to prevent jitter
    if (Math.abs(currentScroll - lastScroll) > 5) {
        if (currentScroll <= 0) {
            header.style.transform = 'translateY(0)';
            header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.3)';
        } else if (currentScroll > lastScroll && currentScroll > 80) {
            // Scrolling down & past threshold
            header.style.transform = 'translateY(-100%)';
        } else if (currentScroll < lastScroll) {
            // Scrolling up
            header.style.transform = 'translateY(0)';
            header.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.5)';
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
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe all feature cards
document.addEventListener('DOMContentLoaded', () => {
    const featureCards = document.querySelectorAll('.feature-card');
    featureCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `all 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });

    // Observe vehicle cards
    const vehicleCards = document.querySelectorAll('.vehicle-card');
    vehicleCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `all 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });
});

// Add loading animation
window.addEventListener('load', () => {
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.5s';
    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 100);
});

// Bootstrap carousel auto-cycle with longer interval
document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.querySelector('#vehicleCarousel');
    if (carousel) {
        const bsCarousel = new bootstrap.Carousel(carousel, {
            interval: 5000,
            wrap: true,
            keyboard: true
        });
    }
});

console.log('Car Dealership Website Loaded Successfully!');