// Wishlist Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeWishlistPage();
});

function initializeWishlistPage() {
    initializeWishlistActions();
    initializeFilters();
    // Fetch wishlist from API and render items, then wire item actions
    fetchWishlistFromAPI();
    updateCartCount();
}

// Fetch wishlist data from server API and render
function fetchWishlistFromAPI() {
    const endpoint = '/API/getWishlist.php';

    fetch(endpoint, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(json => {
        if (!json || json.success !== true) {
            // If not logged in or no wishlist, show empty state
            console.warn('Wishlist API error:', json);
            showEmptyState();
            if (json && json.message) showNotification(json.message, 'error');
            return;
        }

        const items = json.data && json.data.wishlist_items ? json.data.wishlist_items : [];
        renderWishlistItems(items);
        updateWishlistStats();
        updateWishlistCount();
    })
    .catch(err => {
        console.error('Failed to fetch wishlist:', err);
        showEmptyState();
        showNotification('Unable to load wishlist. Please try again later.', 'error');
    });
}

// Render wishlist items into the DOM
function renderWishlistItems(items) {
    const container = document.querySelector('.wishlistGrid');
    const emptyState = document.querySelector('.emptyWishlist');
    const wishlistStats = document.querySelector('.wishlistStats');
    const wishlistActions = document.querySelector('.wishlistActions');

    if (!container) return;

    container.innerHTML = '';

    if (!items || items.length === 0) {
        showEmptyState();
        return;
    }

    // Build each item
    items.forEach(item => {
        const art = item.artwork || {};
        const artist = item.artist || {};
        const price = art.price ? Math.round(art.price) : 0;
        const category = art.type || '';
        // Determine image source (supports full URLs, server-relative paths, or stored filenames)
        // Prefer a ready-to-use URL provided by the API
        let imgSrc = art.artwork_image_url || art.artwork_image || '';

        // If the returned value is just a filename (no protocol or leading slash),
        // assume it's stored under uploads/artworks/ which other APIs use.
        if (imgSrc && !imgSrc.startsWith('http') && !imgSrc.startsWith('/') && !imgSrc.startsWith('./') && !imgSrc.startsWith('../')) {
            imgSrc = './uploads/artworks/' + imgSrc;
        }

        // Final fallback to placeholder
        if (!imgSrc) imgSrc = './image/placeholder-artwork.jpg';

        const isAvailable = art.is_available ? true : false;

        const itemEl = document.createElement('div');
        itemEl.className = 'wishlistItem';
        itemEl.dataset.category = category;
        itemEl.dataset.price = price;

        itemEl.innerHTML = `
            <div class="wishlistImageContainer">
                <img src="${imgSrc}" alt="${escapeHtml(art.title || 'Artwork')}" class="wishlistImage" />
                <div class="wishlistBadge ${isAvailable ? 'available' : 'limited'}">
                    <i class="fas ${isAvailable ? 'fa-check-circle' : 'fa-hourglass-half'}"></i>
                    <span>${isAvailable ? 'Available' : 'Limited'}</span>
                </div>
                <button class="removeBtn" title="Remove from Wishlist">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="wishlistInfo">
                <h3 class="wishlistTitle">${escapeHtml(art.title || '')}</h3>
                <p class="wishlistArtist">by ${escapeHtml(artist.name || '')}</p>
                <p class="wishlistDescription">${escapeHtml((art.description || '').slice(0, 160))}${(art.description && art.description.length>160)?'...':''}</p>
                <div class="wishlistPricing">
                    <span class="wishlistPrice">EGP ${price.toLocaleString()}</span>
                    <span class="wishlistStatus ${isAvailable ? '' : 'limited'}">${isAvailable ? 'In Stock' : 'Only a few left'}</span>
                </div>
                <div class="wishlistActions">
                    <button class="addToCartBtn">
                        <i class="fas fa-shopping-cart"></i>
                        Add to Cart
                    </button>
                    <button class="viewDetailsBtn" data-artwork-id="${art.artwork_id}">
                        <i class="fas fa-eye"></i>
                        View Details
                    </button>
                </div>
                <div class="wishlistMeta">
                    <span class="addedDate">Added: ${escapeHtml(item.added_to_wishlist || '')}</span>
                </div>
            </div>
        `;

        container.appendChild(itemEl);
    });

    // Wire up event handlers for dynamically added items
    initializeItemActions();
}

// Simple HTML escaper to avoid injecting raw data
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
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
