/**
 * Burger Menu Component JavaScript - HTML5 Semantic Version
 * Handles burger menu functionality, interactions, and animations
 * Exact same functionality as navbar with HTML5 compliance
 */

class BurgerMenu {
    constructor() {
        this.overlay = null;
        this.container = null;
        this.closeBtn = null;
        this.userDropdown = null;
        this.userMenu = null;
        this.searchInput = null;
        this.isOpen = false;
        
        this.init();
    }

    init() {
        this.bindElements();
        this.bindEvents();
        this.updateActiveNavLink();
        this.updateCounters();
        this.updateUserInterface();
        
        // Set up periodic sync with main navbar
        this.setupUserSync();
    }

    setupUserSync() {
        // Listen for user change events from navbar/demo panel
        window.addEventListener('userUpdated', () => {
            this.refreshUserInterface();
        });
        
        // Also listen for storage changes from other tabs/windows
        window.addEventListener('storage', (e) => {
            if (e.key === 'currentUser') {
                this.updateUserInterface();
            }
        });
        
        // Fallback: Check for user changes every 2 seconds (reduced frequency)
        setInterval(() => {
            const navbarUser = this.getUserFromNavbar();
            const currentUser = this.getUserFromStorage();
            
            // If navbar has different user info, update burger menu
            if (navbarUser && (
                navbarUser.name !== currentUser.name || 
                navbarUser.role !== currentUser.role ||
                navbarUser.isLoggedIn !== currentUser.isLoggedIn
            )) {
                // Update localStorage with navbar data
                localStorage.setItem('currentUser', JSON.stringify(navbarUser));
                // Refresh burger menu interface
                this.updateUserInterface();
            }
        }, 5000); // Increased to 5 seconds since we have event listeners now
    }

    bindElements() {
        // Updated selectors for HTML5 semantic structure
        this.overlay = document.getElementById('burger-menu-overlay');
        this.container = document.querySelector('.burger-menu-container');
        this.closeBtn = document.getElementById('burger-menu-close');
        this.userDropdown = document.querySelector('.burger-user-dropdown');
        this.userMenu = document.getElementById('burger-user-dropdown-menu');
        this.searchInput = document.getElementById('burger-search-input');
    }

    bindEvents() {
        // Close button
        if (this.closeBtn) {
            this.closeBtn.addEventListener('click', () => this.close());
        }

        // Overlay click to close
        if (this.overlay) {
            this.overlay.addEventListener('click', (e) => {
                if (e.target === this.overlay) {
                    this.close();
                }
            });
        }

        // User dropdown toggle - Updated selector
        if (this.userDropdown) {
            const userAccount = document.getElementById('burger-user-account-btn');
            if (userAccount) {
                userAccount.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.toggleUserDropdown();
                });
            }
        }

        // Search functionality
        this.bindSearchEvents();

        // Escape key to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });

        // Navigation link clicks
        this.bindNavigationEvents();
    }

    bindSearchEvents() {
        const searchBtn = document.getElementById('burger-search-btn');
        
        if (searchBtn) {
            searchBtn.addEventListener('click', () => this.performSearch());
        }

        if (this.searchInput) {
            this.searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.performSearch();
                }
            });

            this.searchInput.addEventListener('input', (e) => {
                this.handleSearchInput(e.target.value);
            });
        }
    }

    bindNavigationEvents() {
        const navLinks = document.querySelectorAll('.burger-nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                // Add loading state
                this.showLoading(link);
                // Close menu after short delay
                setTimeout(() => this.close(), 300);
            });
        });
    }

    open() {
        if (this.overlay) {
            this.isOpen = true;
            this.overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Focus management removed - no automatic focus on search input
        }
    }

    close() {
        if (this.overlay) {
            this.isOpen = false;
            this.overlay.classList.remove('active');
            document.body.style.overflow = '';
            
            // Close user dropdown if open
            if (this.userDropdown) {
                this.userDropdown.classList.remove('active');
            }
            
            // Reset nav-toggle state when burger menu closes
            const navToggle = document.querySelector('.nav-toggle');
            if (navToggle) {
                navToggle.classList.remove('active');
            }
        }
    }

    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    toggleUserDropdown() {
        if (this.userDropdown) {
            this.userDropdown.classList.toggle('active');
        }
    }

    performSearch() {
        if (this.searchInput) {
            const query = this.searchInput.value.trim();
            if (query) {
                console.log('Searching for:', query);
                // Implement your search logic here
                // Example: window.location.href = `search.html?q=${encodeURIComponent(query)}`;
                
                // Show search feedback
                this.showSearchFeedback('Searching...');
                
                // Close menu after search
                setTimeout(() => this.close(), 500);
            }
        }
    }

    handleSearchInput(value) {
        // Implement search suggestions or live search here
        if (value.length > 2) {
            // Show search suggestions
            console.log('Search suggestions for:', value);
        }
    }

    showSearchFeedback(message) {
        // Create temporary feedback element
        const feedback = document.createElement('div');
        feedback.textContent = message;
        feedback.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--primaryBrown);
            color: white;
            padding: 8px 12px;
            border-radius: 0 0 8px 8px;
            font-size: 0.8rem;
            text-align: center;
            z-index: 10;
        `;
        
        const searchContainer = document.querySelector('.burger-search-container');
        if (searchContainer) {
            searchContainer.style.position = 'relative';
            searchContainer.appendChild(feedback);
            
            setTimeout(() => {
                if (feedback.parentNode) {
                    feedback.parentNode.removeChild(feedback);
                }
            }, 2000);
        }
    }

    showLoading(element) {
        const originalText = element.textContent;
        element.textContent = 'Loading...';
        element.style.opacity = '0.7';
        
        setTimeout(() => {
            element.textContent = originalText;
            element.style.opacity = '1';
        }, 1000);
    }

    updateUserInterface() {
        // Get the most current user data by prioritizing navbar over localStorage
        const navbarUser = this.getUserFromNavbar();
        const storageUser = this.getUserFromStorage();
        
        // Use navbar data if available, otherwise use storage data
        let currentUser = navbarUser || storageUser;
        
        // If navbar and storage differ, sync them
        if (navbarUser && storageUser && (
            navbarUser.name !== storageUser.name || 
            navbarUser.role !== storageUser.role ||
            navbarUser.isLoggedIn !== storageUser.isLoggedIn
        )) {
            localStorage.setItem('currentUser', JSON.stringify(navbarUser));
            currentUser = navbarUser;
        }
        
        // Update UI elements
        const userNameElement = document.getElementById('burger-user-name');
        const userRoleElement = document.getElementById('burger-user-role');
        const artistSection = document.getElementById('burger-artist-section');
        const loginLogout = document.getElementById('burger-login-logout');
        
        // Update user info in dropdown header
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
            } else {
                artistSection.style.display = 'none';
            }
        }
        
        // Update login/logout button
        if (loginLogout) {
            if (currentUser.isLoggedIn) {
                loginLogout.innerHTML = '<i class="fas fa-sign-out-alt"></i><span>Logout</span>';
                loginLogout.href = '#';
                loginLogout.onclick = (e) => {
                    e.preventDefault();
                    this.logout();
                };
            } else {
                loginLogout.innerHTML = '<i class="fas fa-sign-in-alt"></i><span>Login</span>';
                loginLogout.href = 'login.html';
                loginLogout.onclick = null;
            }
        }
    }

    getUserFromNavbar() {
        // Try to get user info from main navbar elements
        const navbarUserName = document.getElementById('user-name');
        const navbarUserRole = document.getElementById('user-role');
        
        if (navbarUserName && navbarUserRole) {
            const name = navbarUserName.textContent.trim();
            const role = navbarUserRole.textContent.trim().toLowerCase();
            
            // Check if user is actually logged in (not showing default "Guest User")
            if (name && name !== 'Guest User' && name !== '') {
                return {
                    name: name,
                    role: role,
                    isLoggedIn: true
                };
            } else if (name === 'Guest User') {
                return {
                    name: 'Guest User',
                    role: 'visitor',
                    isLoggedIn: false
                };
            }
        }
        
        return null;
    }

    getUserFromStorage() {
        // Load user data from localStorage or use default
        let currentUser = {
            name: 'Guest User',
            role: 'visitor',
            isLoggedIn: false
        };
        
        const savedUser = localStorage.getItem('currentUser');
        if (savedUser) {
            try {
                currentUser = JSON.parse(savedUser);
            } catch (e) {
                console.warn('Invalid user data in localStorage');
            }
        }
        
        return currentUser;
    }

    // Method to refresh user interface (call this when user changes)
    refreshUserInterface() {
        this.updateUserInterface();
        this.updateCounters();
    }

    logout() {
        // Update localStorage
        const currentUser = {
            name: 'Guest User',
            role: 'visitor',
            isLoggedIn: false
        };
        
        localStorage.setItem('currentUser', JSON.stringify(currentUser));
        
        // Update interface
        this.updateUserInterface();
        
        // Close burger menu
        this.close();
        
        // Show notification (if available)
        if (typeof showNotification === 'function') {
            showNotification('Logged out successfully', 'info');
        }
        
        // Redirect to home page
        setTimeout(() => {
            window.location.href = 'index.html';
        }, 1000);
    }

    updateActiveNavLink() {
        const currentPage = window.location.pathname.split('/').pop() || 'index.html';
        const navLinks = document.querySelectorAll('.burger-nav-link');
        
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPage || (currentPage === '' && href === 'index.html')) {
                link.classList.add('active');
                link.style.background = 'rgba(107, 68, 35, 0.15)';
                link.style.borderColor = 'var(--primary-brown)';
            } else {
                link.classList.remove('active');
                link.style.background = '';
                link.style.borderColor = '';
            }
        });
    }

    updateCounters() {
        // Update cart counter - Updated selectors
        const cartCount = this.getCartCount();
        const cartCountElement = document.getElementById('burger-cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = cartCount;
            // Always show cart count even if 0, but style differently
            cartCountElement.style.display = 'flex';
            if (cartCount === 0) {
                cartCountElement.style.opacity = '0.5';
            } else {
                cartCountElement.style.opacity = '1';
            }
        }

        // Update wishlist counter - Updated selectors
        const wishlistCount = this.getWishlistCount();
        const wishlistCountElement = document.getElementById('burger-wishlist-count');
        if (wishlistCountElement) {
            wishlistCountElement.textContent = wishlistCount;
            // Always show wishlist count even if 0, but style differently
            wishlistCountElement.style.display = 'flex';
            if (wishlistCount === 0) {
                wishlistCountElement.style.opacity = '0.5';
            } else {
                wishlistCountElement.style.opacity = '1';
            }
        }
    }

    getCartCount() {
        // Implement your cart count logic here
        // Example: return JSON.parse(localStorage.getItem('cart') || '[]').length;
        return 3; // Placeholder
    }

    getWishlistCount() {
        // Implement your wishlist count logic here
        // Example: return JSON.parse(localStorage.getItem('wishlist') || '[]').length;
        return 7; // Placeholder
    }

    updateLoginStatus() {
        const loginLogout = document.getElementById('burger-login-logout');
        const isLoggedIn = this.checkLoginStatus();
        
        if (loginLogout) {
            if (isLoggedIn) {
                loginLogout.innerHTML = '<i class="fas fa-sign-out-alt"></i><span>Logout</span>';
                loginLogout.href = '#logout';
            } else {
                loginLogout.innerHTML = '<i class="fas fa-sign-in-alt"></i><span>Login</span>';
                loginLogout.href = 'login.html';
            }
        }
    }

    checkLoginStatus() {
        // Implement your login status check here
        // Example: return !!localStorage.getItem('user-token');
        return false; // Placeholder
    }

    // Public methods for external control
    static getInstance() {
        if (!window.burgerMenuInstance) {
            window.burgerMenuInstance = new BurgerMenu();
        }
        return window.burgerMenuInstance;
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    BurgerMenu.getInstance();
});

// Expose global methods for external burger menu control
window.openBurgerMenu = () => {
    const instance = BurgerMenu.getInstance();
    instance.open();
};

window.closeBurgerMenu = () => {
    const instance = BurgerMenu.getInstance();
    instance.close();
};

window.toggleBurgerMenu = () => {
    const instance = BurgerMenu.getInstance();
    instance.toggle();
};

// Add method to refresh burger menu when user data changes
window.refreshBurgerMenu = () => {
    const instance = BurgerMenu.getInstance();
    instance.refreshUserInterface();
};

// Listen for user changes in the main application
window.addEventListener('userUpdated', () => {
    const instance = BurgerMenu.getInstance();
    instance.refreshUserInterface();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BurgerMenu;
}