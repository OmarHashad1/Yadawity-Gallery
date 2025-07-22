// Auction Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeAuctionPage();
});

function initializeAuctionPage() {
    initializeTimers();
    initializeFilters();
    initializeWatchlist();
    initializeLoadMore();
    updateCartCount();
    updateWishlistCount();
}

// Timer functionality for live auctions
function initializeTimers() {
    const timers = document.querySelectorAll('.auctionTimer[data-end-time]');
    
    timers.forEach(timer => {
        const endTime = new Date(timer.dataset.endTime).getTime();
        updateTimer(timer, endTime);
        
        // Update every minute
        setInterval(() => updateTimer(timer, endTime), 60000);
    });

    // Handle upcoming auction timers
    const upcomingTimers = document.querySelectorAll('.auctionTimer[data-start-time]');
    upcomingTimers.forEach(timer => {
        const startTime = new Date(timer.dataset.startTime).getTime();
        updateUpcomingTimer(timer, startTime);
        
        // Update every hour for upcoming auctions
        setInterval(() => updateUpcomingTimer(timer, startTime), 3600000);
    });
}

function updateTimer(timer, endTime) {
    const now = new Date().getTime();
    const timeLeft = endTime - now;
    
    if (timeLeft > 0) {
        const hours = Math.floor(timeLeft / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        
        const timeSpan = timer.querySelector('.timeRemaining');
        if (timeSpan) {
            timeSpan.textContent = `${hours}h ${minutes}m`;
        }
    } else {
        // Auction ended
        const timeSpan = timer.querySelector('.timeRemaining');
        if (timeSpan) {
            timeSpan.textContent = 'Ended';
        }
        
        // Convert card to ended state
        const card = timer.closest('.auctionCard');
        if (card) {
            card.classList.remove('live');
            card.classList.add('ended');
            
            const status = card.querySelector('.auctionStatus');
            if (status) {
                status.classList.remove('live');
                status.classList.add('ended');
                status.innerHTML = '<i class="fas fa-check"></i><span>ENDED</span>';
            }
            
            const bidBtn = card.querySelector('.bidNowBtn');
            if (bidBtn) {
                bidBtn.className = 'viewDetailsBtn';
                bidBtn.innerHTML = '<i class="fas fa-eye"></i>View Details';
            }
        }
    }
}

function updateUpcomingTimer(timer, startTime) {
    const now = new Date().getTime();
    const timeUntilStart = startTime - now;
    
    if (timeUntilStart > 0) {
        const days = Math.floor(timeUntilStart / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeUntilStart % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        
        const timeSpan = timer.querySelector('.timeRemaining');
        if (timeSpan) {
            if (days > 0) {
                timeSpan.textContent = `Starts in ${days}d ${hours}h`;
            } else {
                timeSpan.textContent = `Starts in ${hours}h`;
            }
        }
    } else {
        // Auction should have started
        const timeSpan = timer.querySelector('.timeRemaining');
        if (timeSpan) {
            timeSpan.textContent = 'Starting soon';
        }
    }
}

// Filter functionality
function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const priceFilter = document.getElementById('priceFilter');
    const sortFilter = document.getElementById('sortFilter');

    [statusFilter, categoryFilter, priceFilter, sortFilter].forEach(filter => {
        if (filter) {
            filter.addEventListener('change', applyFilters);
        }
    });
}

function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value;
    const categoryFilter = document.getElementById('categoryFilter').value;
    const priceFilter = document.getElementById('priceFilter').value;
    const sortFilter = document.getElementById('sortFilter').value;

    const cards = document.querySelectorAll('.auctionCard');
    let visibleCards = [];

    cards.forEach(card => {
        let shouldShow = true;

        // Status filter
        if (statusFilter) {
            const cardStatus = card.classList.contains('live') ? 'live' :
                             card.classList.contains('upcoming') ? 'upcoming' :
                             card.classList.contains('ended') ? 'ended' : '';
            if (cardStatus !== statusFilter) {
                shouldShow = false;
            }
        }

        // Category filter
        if (categoryFilter && shouldShow) {
            const cardCategory = card.dataset.category;
            if (cardCategory !== categoryFilter) {
                shouldShow = false;
            }
        }

        // Price filter
        if (priceFilter && shouldShow) {
            const cardPrice = parseInt(card.dataset.price);
            const [min, max] = priceFilter.includes('-') ? 
                priceFilter.split('-').map(p => parseInt(p)) : 
                priceFilter.includes('+') ? [parseInt(priceFilter), Infinity] : [0, Infinity];
            
            if (cardPrice < min || cardPrice > max) {
                shouldShow = false;
            }
        }

        if (shouldShow) {
            card.classList.remove('hidden');
            visibleCards.push(card);
        } else {
            card.classList.add('hidden');
        }
    });

    // Apply sorting to visible cards
    if (sortFilter && visibleCards.length > 0) {
        sortCards(visibleCards, sortFilter);
    }
}

function sortCards(cards, sortBy) {
    const parent = cards[0].parentNode;
    
    cards.sort((a, b) => {
        switch (sortBy) {
            case 'ending-soon':
                // For live auctions, sort by time remaining
                const aTimer = a.querySelector('.auctionTimer[data-end-time]');
                const bTimer = b.querySelector('.auctionTimer[data-end-time]');
                if (aTimer && bTimer) {
                    const aEndTime = new Date(aTimer.dataset.endTime).getTime();
                    const bEndTime = new Date(bTimer.dataset.endTime).getTime();
                    return aEndTime - bEndTime;
                }
                return 0;
            
            case 'price-low':
                return parseInt(a.dataset.price) - parseInt(b.dataset.price);
            
            case 'price-high':
                return parseInt(b.dataset.price) - parseInt(a.dataset.price);
            
            case 'newest':
                // This would typically sort by creation date, using price as fallback
                return parseInt(b.dataset.price) - parseInt(a.dataset.price);
            
            default:
                return 0;
        }
    });

    // Re-append cards in sorted order
    cards.forEach(card => {
        parent.appendChild(card);
    });
}

// Watchlist functionality
function initializeWatchlist() {
    const watchBtns = document.querySelectorAll('.watchBtn:not(.disabled)');
    
    watchBtns.forEach(btn => {
        btn.addEventListener('click', toggleWatchlist);
    });
}

function toggleWatchlist(event) {
    event.preventDefault();
    const btn = event.currentTarget;
    const icon = btn.querySelector('i');
    
    if (icon.classList.contains('far')) {
        // Add to watchlist
        icon.classList.remove('far');
        icon.classList.add('fas');
        btn.style.color = '#d4af37';
        btn.style.borderColor = '#d4af37';
        
        // Show notification
        showNotification('Added to watchlist', 'success');
        
        // Update wishlist count
        updateWishlistCount();
    } else {
        // Remove from watchlist
        icon.classList.remove('fas');
        icon.classList.add('far');
        btn.style.color = '#8b6f47';
        btn.style.borderColor = '#e8e4df';
        
        showNotification('Removed from watchlist', 'info');
    }
}

// Load more functionality
function initializeLoadMore() {
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', loadMoreAuctions);
    }
}

function loadMoreAuctions() {
    const btn = document.getElementById('loadMoreBtn');
    const originalText = btn.innerHTML;
    
    // Show loading state
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    btn.disabled = true;
    
    // Simulate loading delay
    setTimeout(() => {
        // In a real application, this would fetch more auctions from an API
        showNotification('All auctions loaded', 'info');
        
        // Reset button
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        // Hide load more button if no more items
        btn.style.display = 'none';
    }, 1500);
}

// Open auction preview page
function openAuctionPreview(auctionId) {
    // In a real application, this would navigate to the auction preview page
    window.location.href = `auction-preview.html?id=${auctionId}`;
}

// Utility functions
function updateCartCount() {
    const cartCount = document.getElementById('cartCount');
    const burgerCartCount = document.getElementById('burgerCartCount');
    
    // Get cart count from localStorage or default to 0
    const count = localStorage.getItem('cartCount') || '0';
    
    if (cartCount) cartCount.textContent = count;
    if (burgerCartCount) burgerCartCount.textContent = count;
}

function updateWishlistCount() {
    const wishlistCount = document.getElementById('wishlistCount');
    const burgerWishlistCount = document.getElementById('burgerWishlistCount');
    
    // Get wishlist count from localStorage or default to 0
    let count = parseInt(localStorage.getItem('wishlistCount') || '0');
    
    // Count active watchlist items on page
    const activeWatchItems = document.querySelectorAll('.watchBtn .fas.fa-heart').length;
    count = Math.max(count, activeWatchItems);
    
    localStorage.setItem('wishlistCount', count.toString());
    
    if (wishlistCount) {
        wishlistCount.textContent = count;
        wishlistCount.style.display = count > 0 ? 'flex' : 'none';
    }
    if (burgerWishlistCount) {
        burgerWishlistCount.textContent = count;
    }
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle"></i>
        <span>${message}</span>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 2rem;
        right: 2rem;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#6b7280'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        z-index: 1000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Navbar functionality
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchBtn = document.getElementById('searchButton');
    const searchInput = document.getElementById('navbarSearch');
    
    if (searchBtn && searchInput) {
        searchBtn.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }
    
    // User dropdown
    const userAccount = document.getElementById('userAccount');
    const userMenu = document.getElementById('userMenu');
    
    if (userAccount && userMenu) {
        userAccount.addEventListener('click', function(e) {
            e.preventDefault();
            userMenu.classList.toggle('show');
        });
        
        document.addEventListener('click', function(e) {
            if (!userAccount.contains(e.target) && !userMenu.contains(e.target)) {
                userMenu.classList.remove('show');
            }
        });
    }
});

function performSearch() {
    const searchInput = document.getElementById('navbarSearch');
    const query = searchInput.value.trim();
    
    if (query) {
        // In a real application, this would perform the search
        showNotification(`Searching for: ${query}`, 'info');
        
        // Filter auctions based on search query
        filterAuctionsBySearch(query);
    }
}

function filterAuctionsBySearch(query) {
    const cards = document.querySelectorAll('.auctionCard');
    const searchTerm = query.toLowerCase();
    
    cards.forEach(card => {
        const title = card.querySelector('.auctionTitle').textContent.toLowerCase();
        const artist = card.querySelector('.auctionArtist').textContent.toLowerCase();
        const description = card.querySelector('.auctionDescription').textContent.toLowerCase();
        
        const isMatch = title.includes(searchTerm) || 
                       artist.includes(searchTerm) || 
                       description.includes(searchTerm);
        
        if (isMatch) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}
