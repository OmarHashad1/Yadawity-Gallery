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
    setupScrollEffects();
    
    // Cart and wishlist counters
    setupCounters();
    
    // Active page detection
    setActivePage();
}

// Mobile Menu Toggle - Connected to Burger Menu
function setupMobileMenu() {
    const navToggle = document.querySelector('.navToggle');
    const burgerMenuOverlay = document.getElementById('burgerMenuOverlay');

    if (navToggle && burgerMenuOverlay) {
        navToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Use global burger menu functions if available
            if (typeof window.toggleBurgerMenu === 'function') {
                window.toggleBurgerMenu();
            } else {
                // Fallback: manually toggle burger menu
                burgerMenuOverlay.classList.toggle('active');
                document.body.style.overflow = burgerMenuOverlay.classList.contains('active') ? 'hidden' : '';
            }
        });
    }
}

// Search Functionality
function setupSearch() {
    const searchInput = document.querySelector('.searchInput');
    const searchBtn = document.querySelector('.searchBtn');

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
function setupScrollEffects() {
    const navbar = document.querySelector('.navbar');
    let lastScrollY = window.scrollY;

    if (navbar) {
        window.addEventListener('scroll', function() {
            const currentScrollY = window.scrollY;
            
            // Add/remove background based on scroll position
            if (currentScrollY > 50) {
                navbar.style.background = 'rgba(250, 248, 243, 0.95)';
                navbar.style.boxShadow = '0 4px 30px rgba(107, 68, 35, 0.15)';
            } else {
                navbar.style.background = 'rgba(250, 248, 243, 0.98)';
                navbar.style.boxShadow = '0 4px 30px rgba(107, 68, 35, 0.1)';
            }

            // Hide/show navbar on scroll (optional)
            // Uncomment the following lines if you want auto-hide functionality
            /*
            if (currentScrollY > lastScrollY && currentScrollY > 200) {
                navbar.style.transform = 'translateY(-100%)';
            } else {
                navbar.style.transform = 'translateY(0)';
            }
            */
            
            lastScrollY = currentScrollY;
        });
    }
}

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
