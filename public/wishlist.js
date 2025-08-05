// Wishlist Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeWishlistPage();
});

function initializeWishlistPage() {
    initializeWishlistActions();
    initializeFilters();
    initializeItemActions();
    updateWishlistStats();
    updateCartCount();
    updateWishlistCount();
}

// Wishlist Actions
function initializeWishlistActions() {
    const shareBtn = document.getElementById('shareWishlistBtn');
    const clearBtn = document.getElementById('clearWishlistBtn');

    if (shareBtn) {
        shareBtn.addEventListener('click', shareWishlist);
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', clearWishlist);
    }
}

function shareWishlist() {
    if (navigator.share) {
        navigator.share({
            title: 'My Yadawity Wishlist',
            text: 'Check out my curated collection of favorite artworks!',
            url: window.location.href
        }).catch(console.error);
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showNotification('Wishlist link copied to clipboard!', 'success');
        }).catch(() => {
            showNotification('Unable to share wishlist', 'error');
        });
    }
}

function clearWishlist() {
    if (confirm('Are you sure you want to clear your entire wishlist? This action cannot be undone.')) {
        const wishlistItems = document.querySelectorAll('.wishlistItem');
        wishlistItems.forEach(item => {
            item.remove();
        });
        
        showEmptyState();
        updateWishlistStats();
        showNotification('Wishlist cleared successfully', 'info');
    }
}

// Filter functionality
function initializeFilters() {
    const categoryFilter = document.getElementById('categoryFilter');
    const sortFilter = document.getElementById('sortFilter');

    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterWishlistItems);
    }

    if (sortFilter) {
        sortFilter.addEventListener('change', sortWishlistItems);
    }
}

function filterWishlistItems() {
    const categoryFilter = document.getElementById('categoryFilter');
    const selectedCategory = categoryFilter.value;
    const items = document.querySelectorAll('.wishlistItem');

    items.forEach(item => {
        const itemCategory = item.dataset.category;
        
        if (!selectedCategory || itemCategory === selectedCategory) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });

    updateVisibleItemsCount();
}

function sortWishlistItems() {
    const sortFilter = document.getElementById('sortFilter');
    const sortBy = sortFilter.value;
    const container = document.querySelector('.wishlistGrid');
    const items = Array.from(container.querySelectorAll('.wishlistItem'));

    items.sort((a, b) => {
        switch (sortBy) {
            case 'price-low':
                return parseInt(a.dataset.price) - parseInt(b.dataset.price);
            case 'price-high':
                return parseInt(b.dataset.price) - parseInt(a.dataset.price);
            case 'name':
                const titleA = a.querySelector('.wishlistTitle').textContent;
                const titleB = b.querySelector('.wishlistTitle').textContent;
                return titleA.localeCompare(titleB);
            case 'recent':
            default:
                return 0; // Keep original order for recent
        }
    });

    // Re-append sorted items
    items.forEach(item => container.appendChild(item));
}

// Item Actions
function initializeItemActions() {
    const removeButtons = document.querySelectorAll('.removeBtn');
    const addToCartButtons = document.querySelectorAll('.addToCartBtn');
    const viewDetailsButtons = document.querySelectorAll('.viewDetailsBtn');

    removeButtons.forEach(btn => {
        btn.addEventListener('click', removeFromWishlist);
    });

    addToCartButtons.forEach(btn => {
        btn.addEventListener('click', addToCart);
    });

    viewDetailsButtons.forEach(btn => {
        btn.addEventListener('click', viewDetails);
    });
}

function removeFromWishlist(event) {
    const wishlistItem = event.target.closest('.wishlistItem');
    const itemTitle = wishlistItem.querySelector('.wishlistTitle').textContent;
    
    if (confirm(`Remove "${itemTitle}" from your wishlist?`)) {
        wishlistItem.style.transform = 'scale(0.8)';
        wishlistItem.style.opacity = '0';
        
        setTimeout(() => {
            wishlistItem.remove();
            updateWishlistStats();
            updateWishlistCount();
            checkEmptyState();
            showNotification('Item removed from wishlist', 'info');
        }, 300);
    }
}

function addToCart(event) {
    const wishlistItem = event.target.closest('.wishlistItem');
    const itemTitle = wishlistItem.querySelector('.wishlistTitle').textContent;
    const itemPrice = wishlistItem.querySelector('.wishlistPrice').textContent;
    
    // Add to cart logic here
    updateCartCount();
    showNotification(`"${itemTitle}" added to cart`, 'success');
    
    // Change button temporarily
    const button = event.target.closest('.addToCartBtn');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i> Added!';
    button.style.background = '#22c55e';
    
    setTimeout(() => {
        button.innerHTML = originalText;
        button.style.background = '';
    }, 2000);
}

function viewDetails(event) {
    const wishlistItem = event.target.closest('.wishlistItem');
    const itemTitle = wishlistItem.querySelector('.wishlistTitle').textContent;
    
    // Navigate to product details page
    // This would typically navigate to a product page
    showNotification(`Viewing details for "${itemTitle}"`, 'info');
}

// Stats and State Management
function updateWishlistStats() {
    const items = document.querySelectorAll('.wishlistItem');
    const itemCount = items.length;
    
    // Update items count
    const itemsCountElement = document.querySelector('.statsNumber');
    if (itemsCountElement) {
        itemsCountElement.textContent = itemCount;
    }
    
    // Calculate total value
    let totalValue = 0;
    items.forEach(item => {
        const price = parseInt(item.dataset.price) || 0;
        totalValue += price;
    });
    
    const totalValueElement = document.querySelectorAll('.statsNumber')[2];
    if (totalValueElement) {
        totalValueElement.textContent = `EGP ${totalValue.toLocaleString()}`;
    }
    
    // Update artists count (example logic)
    const artistsCount = new Set(
        Array.from(items).map(item => 
            item.querySelector('.wishlistArtist').textContent
        )
    ).size;
    
    const artistsCountElement = document.querySelectorAll('.statsNumber')[1];
    if (artistsCountElement) {
        artistsCountElement.textContent = artistsCount;
    }
}

function updateVisibleItemsCount() {
    const visibleItems = document.querySelectorAll('.wishlistItem[style*="block"], .wishlistItem:not([style*="none"])');
    const itemsCountElement = document.querySelector('.statsNumber');
    if (itemsCountElement) {
        itemsCountElement.textContent = visibleItems.length;
    }
}

function checkEmptyState() {
    const items = document.querySelectorAll('.wishlistItem');
    const emptyState = document.querySelector('.emptyWishlist');
    const wishlistGrid = document.querySelector('.wishlistGrid');
    
    if (items.length === 0) {
        showEmptyState();
    }
}

function showEmptyState() {
    const emptyState = document.querySelector('.emptyWishlist');
    const wishlistGrid = document.querySelector('.wishlistGrid');
    const wishlistStats = document.querySelector('.wishlistStats');
    const wishlistActions = document.querySelector('.wishlistActions');
    
    if (emptyState && wishlistGrid) {
        wishlistGrid.style.display = 'none';
        wishlistStats.style.display = 'none';
        wishlistActions.style.display = 'none';
        emptyState.style.display = 'block';
    }
}

// Utility Functions
function updateCartCount() {
    // Get cart count from localStorage or API
    const cartCount = localStorage.getItem('cartCount') || '3';
    const cartCountElements = document.querySelectorAll('.cartCount, #cartCount, #burgerCartCount');
    
    cartCountElements.forEach(element => {
        if (element) {
            element.textContent = cartCount;
        }
    });
}

function updateWishlistCount() {
    const wishlistItems = document.querySelectorAll('.wishlistItem');
    const count = wishlistItems.length;
    const wishlistCountElements = document.querySelectorAll('.wishlistCount, #wishlistCount, #burgerWishlistCount');
    
    wishlistCountElements.forEach(element => {
        if (element) {
            element.textContent = count;
            element.style.display = count > 0 ? 'inline' : 'none';
        }
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#22c55e' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after delay
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Export functions for external use
window.wishlistFunctions = {
    addToWishlist: function(itemData) {
        // Function to add items to wishlist from other pages
        console.log('Adding to wishlist:', itemData);
    },
    removeFromWishlist: removeFromWishlist,
    clearWishlist: clearWishlist,
    updateWishlistStats: updateWishlistStats
};
