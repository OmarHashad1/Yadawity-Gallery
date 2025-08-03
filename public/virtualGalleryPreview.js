// Product Preview JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initializeImageGallery();
    initializeAddToCart();
    initializeWishlist();
    initializeSocialShare();
    initializeSimilarProducts();
    initializeNavbarIntegration();
    initializeBurgerMenuIntegration();
    
    // Add loading animations
    initializeLoadingAnimations();
    
    // Initialize smooth scrolling
    initializeSmoothScrolling();
});

// Navbar Integration
function initializeNavbarIntegration() {
    // Update cart count display
    updateCartCount();
    
    // Handle search functionality
    const searchInput = document.getElementById('navbar-search');
    const searchButton = document.getElementById('search-button');
    
    if (searchInput && searchButton) {
        searchButton.addEventListener('click', function(e) {
            e.preventDefault();
            performSearch(searchInput.value);
        });
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch(this.value);
            }
        });
    }
}

// Burger Menu Integration
function initializeBurgerMenuIntegration() {
    const navToggle = document.getElementById('nav-toggle');
    const burgerMenuOverlay = document.getElementById('burgerMenuOverlay');
    const burgerMenuClose = document.getElementById('burgerMenuClose');
    const burgerSearchInput = document.getElementById('burgerSearchInput');
    const burgerSearchButton = document.getElementById('burgerSearchButton');
    const burgerUserDropdown = document.getElementById('burgerUserDropdown');
    const burgerUserAccount = document.getElementById('burgerUserAccount');
    const burgerUserMenu = document.getElementById('burgerUserMenu');
    
    // Open burger menu
    if (navToggle && burgerMenuOverlay) {
        navToggle.addEventListener('click', function() {
            burgerMenuOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Close burger menu
    if (burgerMenuClose) {
        burgerMenuClose.addEventListener('click', function() {
            burgerMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    // Close on overlay click
    if (burgerMenuOverlay) {
        burgerMenuOverlay.addEventListener('click', function(e) {
            if (e.target === burgerMenuOverlay) {
                burgerMenuOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }
    
    // Burger menu search
    if (burgerSearchButton && burgerSearchInput) {
        burgerSearchButton.addEventListener('click', function() {
            performSearch(burgerSearchInput.value);
        });
        
        burgerSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch(this.value);
            }
        });
    }
    
    // User dropdown toggle in burger menu
    if (burgerUserAccount && burgerUserMenu) {
        burgerUserAccount.addEventListener('click', function(e) {
            e.preventDefault();
            burgerUserMenu.classList.toggle('active');
        });
    }
    
    // Close user dropdown when clicking elsewhere
    document.addEventListener('click', function(e) {
        if (burgerUserDropdown && !burgerUserDropdown.contains(e.target)) {
            if (burgerUserMenu) {
                burgerUserMenu.classList.remove('active');
            }
        }
    });
    
    // Update burger menu cart counts
    updateBurgerMenuCounts();
}

// Search functionality
function performSearch(query) {
    if (query.trim()) {
        showNotification(`Searching for: "${query}"`, 'info');
        // Implement actual search functionality here
        console.log('Searching for:', query);
    } else {
        showNotification('Please enter a search term', 'error');
    }
}

// Update burger menu counts
function updateBurgerMenuCounts() {
    const cartCount = document.getElementById('cart-count');
    const wishlistCount = document.getElementById('wishlist-count');
    const burgerCartCount = document.getElementById('burgerCartCount');
    const burgerWishlistCount = document.getElementById('burgerWishlistCount');
    
    if (cartCount && burgerCartCount) {
        burgerCartCount.textContent = cartCount.textContent || '0';
    }
    
    if (wishlistCount && burgerWishlistCount) {
        burgerWishlistCount.textContent = wishlistCount.textContent || '0';
    }
}

// Image Gallery Functions
function initializeImageGallery() {
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.getElementById('mainImage');
    const zoomBtn = document.getElementById('zoomBtn');
    
    // Thumbnail click handlers
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            // Remove active class from all thumbnails
            thumbnails.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked thumbnail
            this.classList.add('active');
            
            // Change main image
            const newSrc = this.getAttribute('data-main');
            mainImage.src = newSrc;
            mainImage.alt = this.alt;
        });
    });
    
    // Zoom functionality
    if (zoomBtn) {
        zoomBtn.addEventListener('click', function() {
            openImageModal(mainImage.src, mainImage.alt);
        });
    }
    
    // Click on main image to zoom
    mainImage.addEventListener('click', function() {
        openImageModal(this.src, this.alt);
    });
}

function openImageModal(src, alt) {
    // Create modal overlay
    const modal = document.createElement('div');
    modal.className = 'imageModal';
    modal.innerHTML = `
        <div class="imageModalOverlay">
            <div class="imageModalContainer">
                <button class="imageModalClose">
                    <i class="fas fa-times"></i>
                </button>
                <img src="${src}" alt="${alt}" class="imageModalImage">
            </div>
        </div>
    `;
    
    // Add modal styles
    const modalStyles = `
        .imageModal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .imageModalOverlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .imageModalContainer {
            position: relative;
            max-width: 90vw;
            max-height: 90vh;
        }
        
        .imageModalImage {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .imageModalClose {
            position: absolute;
            top: -40px;
            right: 0;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 10px;
        }
    `;
    
    // Add styles to head
    const styleSheet = document.createElement('style');
    styleSheet.textContent = modalStyles;
    document.head.appendChild(styleSheet);
    
    // Add modal to body
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
    
    // Close modal functionality
    const closeBtn = modal.querySelector('.imageModalClose');
    const overlay = modal.querySelector('.imageModalOverlay');
    
    function closeModal() {
        document.body.removeChild(modal);
        document.head.removeChild(styleSheet);
        document.body.style.overflow = '';
    }
    
    closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            closeModal();
        }
    });
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
}

// Add to Cart Functionality
function initializeAddToCart() {
    const addToCartBtn = document.getElementById('addToCartBtn');
    
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const productTitle = document.querySelector('.productTitle').textContent;
            const productPrice = document.querySelector('.currentPrice .amount').textContent;
            
            // Simulate adding to cart
            this.innerHTML = '<i class="fas fa-check"></i> Access Purchased';
            this.style.backgroundColor = 'var(--green-accent)';
            
            // Show notification
            showNotification(`${productTitle} access has been purchased!`, 'success');
            
            // Update cart count (if cart count element exists)
            updateCartCount();
            
            // Reset button after 2 seconds
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-shopping-bag"></i> Purchase Access';
                this.style.backgroundColor = 'var(--primary-brown)';
            }, 2000);
        });
    }
}

// Wishlist Functionality
function initializeWishlist() {
    const wishlistBtn = document.getElementById('wishlistBtn');
    
    if (wishlistBtn) {
        wishlistBtn.addEventListener('click', function() {
            const productTitle = document.querySelector('.productTitle').textContent;
            
            this.classList.toggle('active');
            
            if (this.classList.contains('active')) {
                this.innerHTML = '<i class="fas fa-heart"></i>';
                showNotification(`${productTitle} added to wishlist!`, 'success');
            } else {
                this.innerHTML = '<i class="far fa-heart"></i>';
                showNotification(`${productTitle} removed from wishlist!`, 'info');
            }
        });
    }
}

// Social Share Functionality
function initializeSocialShare() {
    const shareButtons = document.querySelectorAll('.shareBtn');
    
    shareButtons.forEach(button => {
        button.addEventListener('click', function() {
            const platform = this.getAttribute('data-platform');
            const url = window.location.href;
            const title = document.querySelector('.productTitle').textContent;
            const artist = document.querySelector('.artistName').textContent;
            const shareText = `Check out "${title}" by ${artist}`;
            
            let shareUrl = '';
            
            switch (platform) {
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                    break;
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(url)}`;
                    break;
                case 'pinterest':
                    const imageUrl = document.getElementById('mainImage').src;
                    shareUrl = `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(url)}&media=${encodeURIComponent(imageUrl)}&description=${encodeURIComponent(shareText)}`;
                    break;
                case 'whatsapp':
                    shareUrl = `https://wa.me/?text=${encodeURIComponent(shareText + ' ' + url)}`;
                    break;
            }
            
            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
        });
    });
}

// Similar Products Functionality
function initializeSimilarProducts() {
    const quickViewButtons = document.querySelectorAll('.quickViewBtn');
    const wishlistButtons = document.querySelectorAll('.addToWishlistBtn');
    
    // Quick View functionality
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productCard = this.closest('.productCard');
            const productTitle = productCard.querySelector('.productCardTitle').textContent;
            
            showNotification(`Quick view for "${productTitle}" - Feature coming soon!`, 'info');
        });
    });
    
    // Wishlist functionality for similar products
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productCard = this.closest('.productCard');
            const productTitle = productCard.querySelector('.productCardTitle').textContent;
            
            this.classList.toggle('active');
            
            if (this.classList.contains('active')) {
                this.innerHTML = '<i class="fas fa-heart"></i>';
                this.style.backgroundColor = 'var(--red-accent)';
                this.style.color = 'white';
                showNotification(`${productTitle} added to wishlist!`, 'success');
            } else {
                this.innerHTML = '<i class="far fa-heart"></i>';
                this.style.backgroundColor = 'white';
                this.style.color = 'var(--red-accent)';
                showNotification(`${productTitle} removed from wishlist!`, 'info');
            }
        });
    });
}

// Utility Functions
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Add notification styles
    const notificationStyles = `
        .notification {
            position: fixed;
            top: 100px;
            right: 20px;
            background-color: white;
            color: var(--text-dark);
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            max-width: 300px;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            border-left: 4px solid var(--primary-brown);
        }
        
        .notification-success {
            border-left-color: var(--green-accent);
        }
        
        .notification-error {
            border-left-color: var(--red-accent);
        }
        
        .notification-info {
            border-left-color: var(--primary-brown);
        }
        
        .notification.show {
            transform: translateX(0);
        }
    `;
    
    // Add styles if not already added
    if (!document.querySelector('#notification-styles')) {
        const styleSheet = document.createElement('style');
        styleSheet.id = 'notification-styles';
        styleSheet.textContent = notificationStyles;
        document.head.appendChild(styleSheet);
    }
    
    // Add notification to body
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

function updateCartCount() {
    const cartCount = document.getElementById('cart-count');
    const burgerCartCount = document.getElementById('burgerCartCount');
    if (cartCount) {
        let currentCount = parseInt(cartCount.textContent) || 0;
        cartCount.textContent = currentCount + 1;
        
        // Update burger menu count too
        if (burgerCartCount) {
            burgerCartCount.textContent = currentCount + 1;
        }
    }
}

// Loading Animations
function initializeLoadingAnimations() {
    const images = document.querySelectorAll('img');
    
    images.forEach(img => {
        img.style.opacity = '0';
        img.style.transition = 'opacity 0.5s ease';
        
        img.addEventListener('load', function() {
            this.style.opacity = '1';
        });
        
        // If image is already loaded
        if (img.complete) {
            img.style.opacity = '1';
        }
    });
    
    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe elements that should animate on scroll
    const animatedElements = document.querySelectorAll('.productCard, .sectionHeader, .productInfo');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

// Smooth Scrolling
function initializeSmoothScrolling() {
    const navLinks = document.querySelectorAll('a[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                const offsetTop = targetElement.offsetTop - 100; // Account for fixed navbar
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// Enhanced Image Modal with better UX
function openImageModal(src, alt) {
    // Create modal overlay
    const modal = document.createElement('div');
    modal.className = 'imageModal';
    modal.innerHTML = `
        <div class="imageModalOverlay">
            <div class="imageModalContainer">
                <button class="imageModalClose" aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>
                <div class="imageModalLoader">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <img src="${src}" alt="${alt}" class="imageModalImage" style="opacity: 0;">
                <div class="imageModalInfo">
                    <h3>${alt}</h3>
                    <p>Click and drag to pan • Use mouse wheel to zoom</p>
                </div>
            </div>
        </div>
    `;
    
    // Enhanced modal styles
    const modalStyles = `
        .imageModal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: modalFadeIn 0.3s ease;
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .imageModalOverlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .imageModalContainer {
            position: relative;
            max-width: 90vw;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .imageModalImage {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.5);
            transition: opacity 0.3s ease;
            cursor: grab;
        }
        
        .imageModalImage:active {
            cursor: grabbing;
        }
        
        .imageModalLoader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 2rem;
        }
        
        .imageModalClose {
            position: absolute;
            top: -60px;
            right: 0;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .imageModalClose:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            transform: rotate(90deg) scale(1.1);
        }
        
        .imageModalInfo {
            margin-top: 20px;
            text-align: center;
            color: white;
        }
        
        .imageModalInfo h3 {
            font-size: 1.5rem;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .imageModalInfo p {
            font-size: 0.9rem;
            opacity: 0.7;
        }
        
        @media (max-width: 768px) {
            .imageModalClose {
                top: -50px;
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
            
            .imageModalInfo {
                padding: 0 20px;
            }
        }
    `;
    
    // Add styles to head
    const styleSheet = document.createElement('style');
    styleSheet.textContent = modalStyles;
    document.head.appendChild(styleSheet);
    
    // Add modal to body
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
    
    const modalImage = modal.querySelector('.imageModalImage');
    const loader = modal.querySelector('.imageModalLoader');
    
    // Show image when loaded
    modalImage.addEventListener('load', function() {
        loader.style.display = 'none';
        this.style.opacity = '1';
    });
    
    // Close modal functionality
    const closeBtn = modal.querySelector('.imageModalClose');
    const overlay = modal.querySelector('.imageModalOverlay');
    
    function closeModal() {
        modal.style.animation = 'modalFadeIn 0.3s ease reverse';
        setTimeout(() => {
            if (document.body.contains(modal)) {
                document.body.removeChild(modal);
                document.head.removeChild(styleSheet);
                document.body.style.overflow = '';
            }
        }, 300);
    }
    
    closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            closeModal();
        }
    });
    
    // Close on escape key
    const handleEscape = function(e) {
        if (e.key === 'Escape') {
            closeModal();
            document.removeEventListener('keydown', handleEscape);
        }
    };
    document.addEventListener('keydown', handleEscape);
}