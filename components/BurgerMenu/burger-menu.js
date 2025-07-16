/**
 * Burger Menu Component JavaScript
 * Handles burger menu functionality, interactions, and animations
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
    }

    bindElements() {
        this.overlay = document.getElementById('burgerMenuOverlay');
        this.container = document.querySelector('.burgerMenuContainer');
        this.closeBtn = document.getElementById('burgerMenuClose');
        this.userDropdown = document.querySelector('.burgerUserDropdown');
        this.userMenu = document.getElementById('burgerUserMenu');
        this.searchInput = document.getElementById('burgerSearchInput');
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

        // User dropdown toggle
        if (this.userDropdown) {
            const userAccount = document.getElementById('burgerUserAccount');
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
        const searchBtn = document.getElementById('burgerSearchButton');
        
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
        const navLinks = document.querySelectorAll('.burgerNavLink');
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
            const navToggle = document.querySelector('.navToggle');
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
        
        const searchContainer = document.querySelector('.burgerSearchContainer');
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

    updateActiveNavLink() {
        const currentPage = window.location.pathname.split('/').pop() || 'index.html';
        const navLinks = document.querySelectorAll('.burgerNavLink');
        
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
        // Update cart counter
        const cartCount = this.getCartCount();
        const cartCountElement = document.getElementById('burgerCartCount');
        if (cartCountElement) {
            cartCountElement.textContent = cartCount;
            cartCountElement.style.display = cartCount > 0 ? 'flex' : 'none';
        }

        // Update wishlist counter
        const wishlistCount = this.getWishlistCount();
        const wishlistCountElement = document.getElementById('burgerWishlistCount');
        if (wishlistCountElement) {
            wishlistCountElement.textContent = wishlistCount;
            wishlistCountElement.style.display = wishlistCount > 0 ? 'flex' : 'none';
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
        const loginLogout = document.getElementById('burgerLoginLogout');
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

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BurgerMenu;
}