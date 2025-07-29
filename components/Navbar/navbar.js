// Yadawity Modern Navbar Component JavaScript

// User role simulation - In production, this would come from your server/API
let currentUser = {
    name: 'Guest User',
    role: 'visitor', // 'visitor', 'buyer', 'artist'
    isLoggedIn: false
};

// Initialize navbar functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeNavbar();
    loadUserData();
});

function initializeNavbar() {
    // Initialize all navbar functions
    setupMobileMenu();
    setupSearch();
    setupUserDropdown();
    setupScrollEffects();
    setupCounters();
    setActivePage();
    setupAnimations();
}

// Load user data from localStorage or API
function loadUserData() {
    // Simulate loading user data from localStorage or API
    const savedUser = localStorage.getItem('currentUser');
    if (savedUser) {
        currentUser = JSON.parse(savedUser);
    }
    
    updateUserInterface();
}

// Update UI based on user role
function updateUserInterface() {
    const userNameElement = document.getElementById('user-name');
    const userRoleElement = document.getElementById('user-role');
    const artistSection = document.getElementById('artist-section');
    const loginLogout = document.getElementById('login-logout');
    
    if (userNameElement) {
        userNameElement.textContent = currentUser.name;
    }
    
    if (userRoleElement) {
        userRoleElement.textContent = currentUser.role.charAt(0).toUpperCase() + currentUser.role.slice(1);
    }
    
    // Show/hide artist section based on role
    if (artistSection) {
        if (currentUser.role === 'artist' && currentUser.isLoggedIn) {
            artistSection.style.display = 'block';
            artistSection.style.animation = 'slideDown 0.4s ease-out';
        } else {
            artistSection.style.display = 'none';
        }
    }
    
    // Update login/logout button
    if (loginLogout) {
        if (currentUser.isLoggedIn) {
            loginLogout.innerHTML = '<i class="fas fa-sign-out-alt"></i><span>Logout</span>';
            loginLogout.onclick = logout;
        } else {
            loginLogout.innerHTML = '<i class="fas fa-sign-in-alt"></i><span>Login</span>';
            loginLogout.onclick = () => window.location.href = 'login.html';
        }
    }
}

// Simulate user login (for demo purposes)
function simulateLogin(userType = 'buyer') {
    currentUser = {
        name: userType === 'artist' ? 'Jane Artist' : 'John Buyer',
        role: userType,
        isLoggedIn: true
    };
    
    localStorage.setItem('currentUser', JSON.stringify(currentUser));
    updateUserInterface();
    
    // Show success animation
    showNotification(`Welcome back, ${currentUser.name}!`, 'success');
}

// Logout function
function logout() {
    currentUser = {
        name: 'Guest User',
        role: 'visitor',
        isLoggedIn: false
    };
    
    localStorage.removeItem('currentUser');
    updateUserInterface();
    showNotification('Logged out successfully', 'info');
}

// Enhanced Mobile Menu Toggle
function setupMobileMenu() {
    const navToggle = document.querySelector('.nav-toggle');
    const burgerMenuOverlay = document.getElementById('burgerMenuOverlay');
    const navbar = document.querySelector('.navbar');

    if (navToggle && burgerMenuOverlay) {
        navToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Add smooth animation to toggle button
            navToggle.classList.toggle('active');
            
            // Use global burger menu functions if available
            if (typeof window.toggleBurgerMenu === 'function') {
                window.toggleBurgerMenu();
            } else {
                // Fallback: manually toggle burger menu with animation
                burgerMenuOverlay.classList.toggle('active');
                document.body.style.overflow = burgerMenuOverlay.classList.contains('active') ? 'hidden' : '';
                
                // Add blur effect to navbar
                if (burgerMenuOverlay.classList.contains('active')) {
                    navbar.style.backdropFilter = 'blur(10px)';
                } else {
                    navbar.style.backdropFilter = 'blur(20px)';
                }
            }
        });
    }
}

// Enhanced Search Functionality
function setupSearch() {
    const searchInput = document.querySelector('.search-input');
    const searchBtn = document.querySelector('.search-btn');
    let searchTimeout;

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

        // Real-time search suggestions with debouncing
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length > 2) {
                    showSearchSuggestions(this.value);
                } else {
                    hideSearchSuggestions();
                }
            }, 300);
        });

        // Enhanced focus effects
        searchInput.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        searchInput.addEventListener('blur', function() {
            setTimeout(() => {
                this.parentElement.classList.remove('focused');
                hideSearchSuggestions();
            }, 200);
        });
    }
}

function performSearch(query) {
    if (query.trim()) {
        console.log('Performing search for:', query);
        
        // Add loading animation to search button
        const searchBtn = document.querySelector('.search-btn');
        if (searchBtn) {
            searchBtn.style.animation = 'spin 1s linear infinite';
            searchBtn.innerHTML = '<i class="fas fa-spinner"></i>';
            
            // Simulate search delay
            setTimeout(() => {
                searchBtn.style.animation = '';
                searchBtn.innerHTML = '<i class="fas fa-search"></i>';
                
                // In production, redirect to search results
                showNotification(`Searching for "${query}"...`, 'info');
            }, 1000);
        }
    }
}

function showSearchSuggestions(query) {
    // Simulate search suggestions - in production, this would be an API call
    const suggestions = [
        'Impressionist Paintings',
        'Modern Sculpture',
        'Portrait Photography',
        'Abstract Art',
        'Digital Art Course'
    ].filter(item => item.toLowerCase().includes(query.toLowerCase()));

    if (suggestions.length > 0) {
        console.log('Showing suggestions:', suggestions);
        // Here you would show actual suggestions dropdown
    }
}

function hideSearchSuggestions() {
    console.log('Hiding search suggestions');
    // Here you would hide the suggestions dropdown
}

// Enhanced User Dropdown
function setupUserDropdown() {
    const userAccount = document.getElementById('user-account');
    const userMenu = document.getElementById('user-menu');
    let hideTimeout;

    if (userAccount && userMenu) {
        // Enhanced hover effects with smooth animations
        userAccount.parentElement.addEventListener('mouseenter', function() {
            clearTimeout(hideTimeout);
            userMenu.style.display = 'block';
            setTimeout(() => {
                userMenu.classList.add('show');
            }, 10);
        });

        userAccount.parentElement.addEventListener('mouseleave', function() {
            userMenu.classList.remove('show');
            hideTimeout = setTimeout(() => {
                userMenu.style.display = 'none';
            }, 300);
        });

        // Add click handler for mobile
        userAccount.addEventListener('click', function(e) {
            e.preventDefault();
            if (window.innerWidth <= 768) {
                userMenu.classList.toggle('show');
            }
        });
    }
}

// Enhanced Scroll Effects
function setupScrollEffects() {
    const navbar = document.querySelector('.navbar');
    let lastScrollY = window.scrollY;
    let ticking = false;

    function updateNavbar() {
        const scrollY = window.scrollY;
        
        if (scrollY > 100) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }

        // Hide navbar on scroll down, show on scroll up
        if (scrollY > lastScrollY && scrollY > 200) {
            navbar.style.transform = 'translateY(-100%)';
        } else {
            navbar.style.transform = 'translateY(0)';
        }

        lastScrollY = scrollY;
        ticking = false;
    }

    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(updateNavbar);
            ticking = true;
        }
    }

    window.addEventListener('scroll', requestTick, { passive: true });
}

// Enhanced Counters with Animation
function setupCounters() {
    updateCartCount();
    updateWishlistCount();
}

function updateCartCount() {
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        // Simulate cart count from localStorage
        const count = localStorage.getItem('cartCount') || 0;
        animateCounter(cartCount, count);
    }
}

function updateWishlistCount() {
    const wishlistCount = document.getElementById('wishlist-count');
    if (wishlistCount) {
        // Simulate wishlist count from localStorage
        const count = localStorage.getItem('wishlistCount') || 0;
        if (count > 0) {
            wishlistCount.style.display = 'flex';
            animateCounter(wishlistCount, count);
        } else {
            wishlistCount.style.display = 'none';
        }
    }
}

function animateCounter(element, newValue) {
    const currentValue = parseInt(element.textContent) || 0;
    if (currentValue !== newValue) {
        element.style.animation = 'pulse 0.3s ease-in-out';
        setTimeout(() => {
            element.textContent = newValue;
            element.style.animation = '';
        }, 150);
    }
}

// Setup smooth animations
function setupAnimations() {
    // Add intersection observer for navbar animations
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.6s ease-out';
            }
        });
    }, observerOptions);

    // Observe navbar elements
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        observer.observe(link);
    });
}

// Set Active Page
function setActivePage() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });
}

// Utility function to show notifications
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: var(--white);
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 20px var(--shadow-medium);
        z-index: 10000;
        animation: slideInRight 0.3s ease-out;
        border-left: 4px solid ${type === 'success' ? 'var(--green-accent)' : type === 'error' ? 'var(--red-accent)' : 'var(--gold-accent)'};
    `;

    document.body.appendChild(notification);

    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Additional CSS animations
const additionalStyles = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }

    .user-dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
    }

    .search-container.focused .search-btn {
        background: linear-gradient(135deg, var(--primary-brown) 0%, var(--secondary-brown) 100%);
        transform: scale(1.1);
    }
`;

// Inject additional styles
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);

// Export functions for use in other scripts
window.NavbarController = {
    simulateLogin,
    logout,
    updateCartCount,
    updateWishlistCount,
    showNotification
};

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
