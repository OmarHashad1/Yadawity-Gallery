// Yadawity Navbar Component JavaScript

// Initialize navbar functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all navbar functions
    initializeNavbar();
});

function initializeNavbar() {
    // Mobile menu toggle
    setupMobileMenu();
    
    // Search functionality
    setupSearch();
    
    // User dropdown
    setupUserDropdown();
    
    // Navbar scroll effects
    
    // Cart and wishlist counters
    setupCounters();
    
    // Active page detection
    setActivePage();
}

// Mobile Menu Toggle
function setupMobileMenu() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const bars = document.querySelectorAll('.bar');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
        });

        // Close mobile menu when clicking on links
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
            }
        });
    }
}

// Search Functionality
function setupSearch() {
    const searchInput = document.querySelector('.search-input');
    const mobileSearchInput = document.querySelector('.mobile-search-input');
    const searchBtn = document.querySelector('.search-btn');
    const mobileSearchOverlay = document.querySelector('.mobile-search-overlay');
    const mobileSearchClose = document.querySelector('.mobile-search-close');
    const mobileSearchIcon = document.querySelector('.nav-icon-link.mobile-search');

    // Desktop search
    if (searchBtn && searchInput) {
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            performSearch(searchInput.value);
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch(this.value);
            }
        });

        // Search suggestions (placeholder functionality)
        searchInput.addEventListener('input', function() {
            // You can implement search suggestions here
            console.log('Searching for:', this.value);
        });
    }

    // Mobile search overlay
    if (mobileSearchIcon && mobileSearchOverlay) {
        mobileSearchIcon.addEventListener('click', function(e) {
            e.preventDefault();
            openMobileSearch();
        });
    }

    if (mobileSearchClose) {
        mobileSearchClose.addEventListener('click', function() {
            closeMobileSearch();
        });
    }

    if (mobileSearchOverlay) {
        mobileSearchOverlay.addEventListener('click', function(e) {
            if (e.target === mobileSearchOverlay) {
                closeMobileSearch();
            }
        });
    }

    if (mobileSearchInput) {
        mobileSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch(this.value);
                closeMobileSearch();
            }
        });
    }
}

function performSearch(query) {
    if (query.trim()) {
        console.log('Performing search for:', query);
        // Implement your search logic here
        // For example: window.location.href = `/search?q=${encodeURIComponent(query)}`;
        
        // Placeholder alert - replace with actual search functionality
        alert(`Searching for: "${query}"`);
    }
}

function openMobileSearch() {
    const overlay = document.querySelector('.mobile-search-overlay');
    const input = document.querySelector('.mobile-search-input');
    
    if (overlay) {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Focus on input after animation
        setTimeout(() => {
            if (input) input.focus();
        }, 300);
    }
}

function closeMobileSearch() {
    const overlay = document.querySelector('.mobile-search-overlay');
    
    if (overlay) {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// User Dropdown
function setupUserDropdown() {
    const userDropdown = document.querySelector('.user-dropdown');
    const dropdownMenu = document.querySelector('.user-dropdown-menu');

    if (userDropdown && dropdownMenu) {
        // Handle dropdown for touch devices
        userDropdown.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Toggle dropdown on mobile/touch devices
            if (window.innerWidth <= 768) {
                dropdownMenu.style.opacity = dropdownMenu.style.opacity === '1' ? '0' : '1';
                dropdownMenu.style.visibility = dropdownMenu.style.visibility === 'visible' ? 'hidden' : 'visible';
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target)) {
                dropdownMenu.style.opacity = '0';
                dropdownMenu.style.visibility = 'hidden';
            }
        });
    }
}

// Navbar Scroll Effects


// Cart and Wishlist Counters
function setupCounters() {
    // These functions would typically connect to your cart/wishlist data
    updateCartCount();
    updateWishlistCount();
}

function updateCartCount(count = 3) {
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = count;
        cartCount.style.display = count > 0 ? 'flex' : 'none';
    }
}

function updateWishlistCount(count = 7) {
    const wishlistCount = document.querySelector('.wishlist-count');
    if (wishlistCount) {
        wishlistCount.textContent = count;
        wishlistCount.style.display = count > 0 ? 'flex' : 'none';
    }
}

// Active Page Detection
function setActivePage() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        
        // Check if the link's href matches the current path
        const linkPath = new URL(link.href).pathname;
        if (linkPath === currentPath || 
            (linkPath !== '/' && currentPath.includes(linkPath))) {
            link.classList.add('active');
        }
    });
    
    // If no link matches, set home as active for root path
    if (currentPath === '/' || currentPath === '/index.html') {
        const homeLink = document.querySelector('.nav-link[href*="index"], .nav-link[href="/"], .nav-link[href="#home"]');
        if (homeLink) {
            homeLink.classList.add('active');
        }
    }
}

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Smooth scrolling for anchor links
function setupSmoothScroll() {
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Initialize smooth scrolling
setupSmoothScroll();

// Export functions for external use
window.YadawityNavbar = {
    updateCartCount,
    updateWishlistCount,
    setActivePage,
    openMobileSearch,
    closeMobileSearch,
    performSearch
};

// Handle resize events
window.addEventListener('resize', debounce(function() {
    // Close mobile menu on resize to larger screen
    if (window.innerWidth > 992) {
        const navMenu = document.querySelector('.nav-menu');
        const navToggle = document.querySelector('.nav-toggle');
        
        if (navMenu) navMenu.classList.remove('active');
        if (navToggle) navToggle.classList.remove('active');
    }
}, 250));


const artisanFilterButtons = document.querySelectorAll('.artisan-category-btn');

artisanFilterButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Remove active class from all buttons
        artisanFilterButtons.forEach(btn => btn.classList.remove('active'));
        // Add active class to clicked button
        button.classList.add('active');

        const filterValue = button.dataset.filter;
        console.log('Filter selected:', filterValue);
    });
});
const slider = document.getElementById('cardSlider');

function slideRight() {
  slider.scrollBy({ left: 320, behavior: 'smooth' });
}

function slideLeft() {
  slider.scrollBy({ left: -320, behavior: 'smooth' });
}