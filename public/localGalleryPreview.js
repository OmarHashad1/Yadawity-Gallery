 
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all functionality
        initializeImageGallery();
        initializeAddToCart();
        initializeWishlist();
        initializeSocialShare();
        
        // Update button text for physical gallery
        const addToCartBtn = document.getElementById('addToCartBtn');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function() {
                const productTitle = document.querySelector('.productTitle').textContent;
                this.innerHTML = '<i class="fas fa-check"></i> Visit Scheduled';
                this.style.backgroundColor = 'var(--green-accent)';
                
                showNotification(`Your visit to "${productTitle}" has been scheduled!`, 'success');
                
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-calendar-alt"></i> Schedule Visit';
                    this.style.backgroundColor = 'linear-gradient(135deg, var(--brown-medium) 0%, var(--brown-light) 100%)';
                }, 2000);
            });
        }
    });

    // Image Gallery Functions
    function initializeImageGallery() {
        const mainImage = document.getElementById('mainImage');
        const zoomBtn = document.getElementById('zoomBtn');
        
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

    // Notification Function
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
