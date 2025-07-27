// Auction Page JavaScript

// Sample auction data
const auctions = [
    {
        id: 1,
        title: "Abstract Harmony",
        artist: "Marina Kovač",
        category: "paintings",
        status: "live",
        price: 75000,
        bidders: 12,
        endTime: "2025-01-25T18:30:00",
        image: "https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=400&h=300&fit=crop",
        description: "Oil on canvas, 80x100cm. A stunning piece exploring the balance between chaos and order..."
    },
    {
        id: 2,
        title: "Bronze Elegance",
        artist: "Ahmed Hassan",
        category: "sculptures",
        status: "upcoming",
        price: 120000,
        watching: 28,
        startTime: "2025-01-26T15:00:00",
        image: "https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop",
        description: "Limited edition bronze sculpture, 45cm height. Masterful craftsmanship showcasing..."
    },
    {
        id: 3,
        title: "Urban Reflections",
        artist: "Sarah Chen",
        category: "photography",
        status: "live",
        price: 35000,
        bidders: 8,
        endTime: "2025-01-25T20:15:00",
        image: "https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=400&h=300&fit=crop",
        description: "Limited edition fine art photography print, 70x50cm. Captures the soul of modern..."
    },
    {
        id: 4,
        title: "Classical Portrait",
        artist: "Elena Popović",
        category: "paintings",
        status: "ended",
        price: 180000,
        winner: "user_1847",
        image: "https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=400&h=300&fit=crop",
        description: "Oil on canvas masterpiece, 90x70cm. Exquisite portraiture technique from renowned..."
    },
    {
        id: 5,
        title: "Digital Dreams",
        artist: "Marcus Rodriguez",
        category: "mixed-media",
        status: "upcoming",
        price: 95000,
        watching: 15,
        startTime: "2025-01-27T19:00:00",
        image: "https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop",
        description: "Mixed media on canvas with digital elements, 100x80cm. A groundbreaking fusion of..."
    },
    {
        id: 6,
        title: "Sunset Serenity",
        artist: "Omar Farouk",
        category: "paintings",
        status: "live",
        price: 45000,
        bidders: 6,
        endTime: "2025-01-25T22:00:00",
        image: "https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=400&h=300&fit=crop",
        description: "Acrylic on canvas landscape, 60x80cm. Breathtaking color palette capturing the magic..."
    },
    {
        id: 7,
        title: "Modern Forms",
        artist: "Layla Mahmoud",
        category: "sculptures",
        status: "upcoming",
        price: 85000,
        watching: 22,
        startTime: "2025-01-28T16:00:00",
        image: "https://images.unsplash.com/photo-1594736797933-d0ac6a4d5d0e?w=400&h=300&fit=crop",
        description: "Contemporary ceramic sculpture, 60cm height. Bold geometric forms that challenge traditional boundaries..."
    },
    {
        id: 8,
        title: "Collage Dreams",
        artist: "Nadia Rostom",
        category: "mixed-media",
        status: "live",
        price: 62000,
        bidders: 9,
        endTime: "2025-01-26T14:20:00",
        image: "https://images.unsplash.com/photo-1549490349-8643362247b5?w=400&h=300&fit=crop",
        description: "Mixed media collage, 75x95cm. Innovative layering techniques creating depth and narrative..."
    },
    {
        id: 9,
        title: "City Lights",
        artist: "Karim El-Sharif",
        category: "photography",
        status: "ended",
        price: 28000,
        winner: "art_collector_92",
        image: "https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop",
        description: "Night photography series, 50x70cm print. Stunning urban landscapes captured during golden hour..."
    },
    {
        id: 10,
        title: "Geometric Abstractions",
        artist: "Isabella Torres",
        category: "paintings",
        status: "upcoming",
        price: 67000,
        watching: 18,
        startTime: "2025-01-29T14:00:00",
        image: "https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=400&h=300&fit=crop",
        description: "Contemporary abstract painting, 85x110cm. Bold geometric patterns in vibrant colors..."
    },
    {
        id: 11,
        title: "Marble Elegance",
        artist: "Roberto Silva",
        category: "sculptures",
        status: "live",
        price: 150000,
        bidders: 14,
        endTime: "2025-01-26T16:45:00",
        image: "https://images.unsplash.com/photo-1594736797933-d0ac6a4d5d0e?w=400&h=300&fit=crop",
        description: "Carved marble sculpture, 75cm height. Classical technique meets contemporary vision..."
    },
    {
        id: 12,
        title: "Street Stories",
        artist: "Maya Johnson",
        category: "photography",
        status: "ended",
        price: 42000,
        winner: "photo_enthusiast_23",
        image: "https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=400&h=300&fit=crop",
        description: "Documentary photography series, 60x90cm prints. Capturing authentic moments of urban life..."
    }
];

// Global variables for pagination and filtering
let filteredAuctions = [...auctions];
let activeFilters = {};
let currentPage = 1;
let auctionsPerPage = 6; // Show 6 auctions per page
let totalPages = 1;

// DOM elements
const searchInput = document.getElementById("searchInput");
const categoryFilter = document.getElementById("categoryFilter");
const statusFilter = document.getElementById("statusFilter");
const minPriceInput = document.getElementById("minPrice");
const maxPriceInput = document.getElementById("maxPrice");
const activeFiltersContainer = document.getElementById("activeFilters");
const searchResults = document.getElementById("searchResults");
const auctionGrid = document.querySelector(".auctionGrid");
const noResults = document.getElementById("noResults");

document.addEventListener('DOMContentLoaded', function() {
    initializeAuctionPage();
});

function initializeAuctionPage() {
    // Initialize data-driven auction grid
    filteredAuctions = [...auctions];
    totalPages = Math.ceil(auctions.length / auctionsPerPage);
    
    // Use paginated rendering from the start
    renderPaginatedAuctions();
    updatePaginationControls();
    setupEventListeners();
    
    initializeTimers();
    initializeWatchlist();
    updateCartCount();
    updateWishlistCount();
}

// Setup event listeners for filters and search
function setupEventListeners() {
    // Search input
    if (searchInput) {
        searchInput.addEventListener("input", debounce(applyFilters, 300));
    }

    // Filter dropdowns and inputs
    if (categoryFilter) categoryFilter.addEventListener("change", applyFilters);
    if (statusFilter) statusFilter.addEventListener("change", applyFilters);
    if (minPriceInput) minPriceInput.addEventListener("input", debounce(applyFilters, 300));
    if (maxPriceInput) maxPriceInput.addEventListener("input", debounce(applyFilters, 300));
}

// Render auctions on the page
function renderAuctions(auctionsToRender) {
    // Clear existing auctions
    auctionGrid.innerHTML = '';
    noResults.style.display = 'none';

    if (auctionsToRender.length === 0) {
        noResults.style.display = 'block';
        return;
    }

    // Create auction cards
    auctionsToRender.forEach(auction => {
        const card = document.createElement("div");
        card.className = `auctionCard ${auction.status}`;
        card.dataset.category = auction.category;
        card.dataset.price = auction.price;

        card.innerHTML = `
            <div class="auctionImage">
                <img src="${auction.image}" alt="${auction.title}">
            </div>
            <div class="auctionInfo">
                <h3 class="auctionTitle">${auction.title}</h3>
                <p class="auctionArtist">${auction.artist}</p>
                <div class="auctionMeta">
                    <span class="auctionStatus ${auction.status}">${auction.status.charAt(0).toUpperCase() + auction.status.slice(1)}</span>
                    <span class="biddersCount">${auction.bidders ? auction.bidders + ' bidders' : ''}</span>
                </div>
                <div class="auctionPrice">
                    ${auction.status === 'ended' ? 'Sold for' : 'Current bid'}: <span class="priceValue">${formatPrice(auction.price)}</span>
                </div>
                <div class="auctionTimer" data-end-time="${auction.endTime}">
                    <span class="timeRemaining"></span>
                </div>
                <div class="auctionActions">
                    <a href="#" class="bidNowBtn" onclick="openAuctionPreview(${auction.id}); return false;">
                        <i class="fas fa-gavel"></i> ${auction.status === 'live' ? 'Bid Now' : 'View Details'}
                    </a>
                    <a href="#" class="watchBtn ${auction.watching ? '' : 'disabled'}" onclick="toggleWatchlist(event, ${auction.id}); return false;">
                        <i class="far fa-heart"></i> ${auction.watching ? 'Watching' : 'Watch'}
                    </a>
                </div>
            </div>
        `;

        auctionGrid.appendChild(card);
    });

    // Re-initialize timers for newly rendered auctions
    initializeTimers();
}

// Pagination functions
function previousPage() {
    if (currentPage > 1) {
        currentPage--;
        renderPaginatedAuctions();
        updatePaginationControls();
        scrollToTop();
    }
}

function nextPage() {
    if (currentPage < totalPages) {
        currentPage++;
        renderPaginatedAuctions();
        updatePaginationControls();
        scrollToTop();
    }
}

function goToPage(page) {
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        renderPaginatedAuctions();
        updatePaginationControls();
        scrollToTop();
    }
}

function renderPaginatedAuctions() {
    // Calculate start and end indices for current page
    const startIndex = (currentPage - 1) * auctionsPerPage;
    const endIndex = startIndex + auctionsPerPage;
    const paginatedAuctions = filteredAuctions.slice(startIndex, endIndex);
    
    renderAuctionsGrid(paginatedAuctions);
    updateAuctionCount();
}

function renderAuctionsGrid(auctionsToRender) {
    auctionGrid.innerHTML = '';
    
    if (auctionsToRender.length === 0) {
        auctionGrid.style.display = 'none';
        noResults.style.display = 'block';
        // Hide pagination when no results
        const paginationSection = document.querySelector('.pagination-section');
        if (paginationSection) {
            paginationSection.style.display = 'none';
        }
        return;
    } else {
        auctionGrid.style.display = 'grid';
        noResults.style.display = 'none';
        // Show pagination when there are results
        const paginationSection = document.querySelector('.pagination-section');
        if (paginationSection) {
            paginationSection.style.display = totalPages > 1 ? 'block' : 'none';
        }
    }

    auctionsToRender.forEach(auction => {
        const auctionCard = document.createElement('div');
        auctionCard.className = `auctionCard ${auction.status}`;
        auctionCard.dataset.category = auction.category;
        auctionCard.dataset.price = auction.price;

        const statusHTML = getStatusHTML(auction);
        const timerHTML = getTimerHTML(auction);
        const priceHTML = getPriceHTML(auction);
        const actionsHTML = getActionsHTML(auction);

        auctionCard.innerHTML = `
            <div class="auctionImageContainer">
                <img 
                    src="${auction.image}" 
                    alt="${auction.title}"
                    class="auctionImage"
                />
                ${statusHTML}
                ${timerHTML}
                ${auction.status === 'ended' ? '<div class="soldOverlay"><span>SOLD</span></div>' : ''}
            </div>
            
            <div class="auctionInfo">
                <h3 class="auctionTitle">${auction.title}</h3>
                <p class="auctionArtist">by ${auction.artist}</p>
                <p class="auctionDescription">${auction.description}</p>
                
                <div class="auctionPricing">
                    ${priceHTML}
                    <div class="bidsCount">
                        ${getBidsCountHTML(auction)}
                    </div>
                </div>
                
                <div class="auctionActions">
                    ${actionsHTML}
                </div>
            </div>
        `;

        auctionGrid.appendChild(auctionCard);
    });

    // Re-initialize timers and watchlist for new cards
    initializeTimers();
    initializeWatchlist();
}

function getStatusHTML(auction) {
    const statusClass = auction.status;
    const statusIcon = auction.status === 'live' ? 'fas fa-circle' : 
                     auction.status === 'upcoming' ? 'fas fa-calendar' : 'fas fa-check';
    const statusText = auction.status === 'live' ? 'LIVE' : 
                      auction.status === 'upcoming' ? 'UPCOMING' : 'SOLD';
    
    return `<div class="auctionStatus ${statusClass}">
                <i class="${statusIcon}"></i>
                <span>${statusText}</span>
            </div>`;
}

function getTimerHTML(auction) {
    if (auction.status === 'live' && auction.endTime) {
        return `<div class="auctionTimer" data-end-time="${auction.endTime}">
                    <i class="fas fa-clock"></i>
                    <span class="timeRemaining">Calculating...</span>
                </div>`;
    } else if (auction.status === 'upcoming' && auction.startTime) {
        return `<div class="auctionTimer" data-start-time="${auction.startTime}">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="timeRemaining">Calculating...</span>
                </div>`;
    }
    return '';
}

function getPriceHTML(auction) {
    const label = auction.status === 'ended' ? 'Final Price' : 
                 auction.status === 'upcoming' ? 'Starting Bid' : 'Current Bid';
    
    return `<div class="currentBid">
                <span class="bidLabel">${label}</span>
                <span class="bidAmount">EGP ${auction.price.toLocaleString()}</span>
            </div>`;
}

function getBidsCountHTML(auction) {
    if (auction.status === 'live' && auction.bidders) {
        return `<i class="fas fa-users"></i>
                <span>${auction.bidders} bidders</span>`;
    } else if (auction.status === 'upcoming' && auction.watching) {
        return `<i class="fas fa-eye"></i>
                <span>${auction.watching} watching</span>`;
    } else if (auction.status === 'ended' && auction.winner) {
        return `<i class="fas fa-trophy"></i>
                <span>Won by ${auction.winner}</span>`;
    }
    return '';
}

function getActionsHTML(auction) {
    const mainButtonText = auction.status === 'live' ? 'Bid Now' : 
                          auction.status === 'upcoming' ? 'Pre-Register' : 'View Details';
    const mainButtonClass = auction.status === 'live' ? 'bidNowBtn' : 
                           auction.status === 'upcoming' ? 'preRegisterBtn' : 'viewDetailsBtn';
    const mainButtonIcon = auction.status === 'live' ? 'fas fa-gavel' : 
                          auction.status === 'upcoming' ? 'fas fa-bell' : 'fas fa-eye';
    
    const watchButtonDisabled = auction.status === 'ended' ? 'disabled' : '';
    const watchButtonTitle = auction.status === 'ended' ? 'Auction Ended' : 'Add to Watchlist';
    const watchButtonIcon = auction.status === 'ended' ? 'fas fa-check' : 'far fa-heart';
    
    return `<button class="${mainButtonClass}" onclick="openAuctionPreview('auction-${auction.id}')">
                <i class="${mainButtonIcon}"></i>
                ${mainButtonText}
            </button>
            <button class="watchBtn ${watchButtonDisabled}" title="${watchButtonTitle}">
                <i class="${watchButtonIcon}"></i>
            </button>`;
}

function updatePaginationControls() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const paginationNumbers = document.getElementById('paginationNumbers');

    // Update previous button
    if (prevBtn) {
        prevBtn.disabled = currentPage === 1;
    }

    // Update next button
    if (nextBtn) {
        nextBtn.disabled = currentPage === totalPages;
    }

    // Update pagination numbers
    if (paginationNumbers) {
        paginationNumbers.innerHTML = '';

        if (totalPages <= 1) {
            // Hide pagination if only one page
            const paginationSection = document.querySelector('.pagination-section');
            if (paginationSection) {
                paginationSection.style.display = 'none';
            }
            return;
        } else {
            // Show pagination if more than one page
            const paginationSection = document.querySelector('.pagination-section');
            if (paginationSection) {
                paginationSection.style.display = 'block';
            }
        }

        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        // Adjust start page if we're near the end
        if (endPage - startPage < maxVisiblePages - 1) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        // Add first page and dots if needed
        if (startPage > 1) {
            const firstPageBtn = document.createElement('button');
            firstPageBtn.className = 'pagination-number';
            firstPageBtn.textContent = '1';
            firstPageBtn.onclick = () => goToPage(1);
            paginationNumbers.appendChild(firstPageBtn);

            if (startPage > 2) {
                const dots = document.createElement('span');
                dots.className = 'pagination-dots';
                dots.textContent = '...';
                paginationNumbers.appendChild(dots);
            }
        }

        // Add visible page numbers
        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `pagination-number ${i === currentPage ? 'active' : ''}`;
            pageBtn.textContent = i;
            pageBtn.onclick = () => goToPage(i);
            paginationNumbers.appendChild(pageBtn);
        }

        // Add last page and dots if needed
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const dots = document.createElement('span');
                dots.className = 'pagination-dots';
                dots.textContent = '...';
                paginationNumbers.appendChild(dots);
            }

            const lastPageBtn = document.createElement('button');
            lastPageBtn.className = 'pagination-number';
            lastPageBtn.textContent = totalPages;
            lastPageBtn.onclick = () => goToPage(totalPages);
            paginationNumbers.appendChild(lastPageBtn);
        }
    }
}

function updateAuctionCount() {
    const startAuction = (currentPage - 1) * auctionsPerPage + 1;
    const endAuction = Math.min(currentPage * auctionsPerPage, filteredAuctions.length);
    
    // Update search results text
    if (searchResults) {
        if (filteredAuctions.length === auctions.length) {
            searchResults.innerHTML = `Showing ${startAuction}-${endAuction} of ${filteredAuctions.length} auctions`;
        } else {
            searchResults.innerHTML = `Found ${filteredAuctions.length} auctions - Showing ${startAuction}-${endAuction}`;
        }
        searchResults.style.display = 'block';
    }
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Clear all filters function
function clearAllFilters() {
    if (searchInput) searchInput.value = '';
    if (categoryFilter) categoryFilter.value = '';
    if (statusFilter) statusFilter.value = '';
    if (minPriceInput) minPriceInput.value = '';
    if (maxPriceInput) maxPriceInput.value = '';

    activeFilters = {};
    filteredAuctions = [...auctions];
    currentPage = 1;
    totalPages = Math.ceil(auctions.length / auctionsPerPage);

    renderActiveFilters();
    renderPaginatedAuctions();
    updatePaginationControls();

    if (searchResults) {
        searchResults.style.display = 'none';
    }
}

// Render active filters
function renderActiveFilters() {
    if (!activeFiltersContainer) return;
    
    activeFiltersContainer.innerHTML = '';

    Object.entries(activeFilters).forEach(([key, value]) => {
        if (value && value !== '' && value !== 0 && value !== Infinity) {
            const filterTag = document.createElement('div');
            filterTag.className = 'filter-tag';

            let displayValue = value;
            if (key === 'searchTerm') {
                displayValue = `Search: ${value}`;
            } else if (key === 'minPrice' || key === 'maxPrice') {
                displayValue = `${key === 'minPrice' ? 'Min' : 'Max'} Price: EGP ${value.toLocaleString()}`;
            } else {
                displayValue = `${key.charAt(0).toUpperCase() + key.slice(1)}: ${value}`;
            }

            filterTag.innerHTML = `
                <span>${displayValue}</span>
                <span class="remove-filter" onclick="removeFilter('${key}')">×</span>
            `;

            activeFiltersContainer.appendChild(filterTag);
        }
    });
}

// Remove individual filter
function removeFilter(filterKey) {
    switch (filterKey) {
        case 'searchTerm':
            if (searchInput) searchInput.value = '';
            break;
        case 'category':
            if (categoryFilter) categoryFilter.value = '';
            break;
        case 'status':
            if (statusFilter) statusFilter.value = '';
            break;
        case 'minPrice':
            if (minPriceInput) minPriceInput.value = '';
            break;
        case 'maxPrice':
            if (maxPriceInput) maxPriceInput.value = '';
            break;
    }
    applyFilters();
}

// Updated applyFilters function
function applyFilters() {
    const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
    const selectedCategory = categoryFilter ? categoryFilter.value : '';
    const selectedStatus = statusFilter ? statusFilter.value : '';
    const minPrice = minPriceInput ? Number.parseFloat(minPriceInput.value) || 0 : 0;
    const maxPrice = maxPriceInput ? Number.parseFloat(maxPriceInput.value) || Number.POSITIVE_INFINITY : Number.POSITIVE_INFINITY;

    // Reset active filters
    activeFilters = {};

    // Filter auctions
    filteredAuctions = auctions.filter(auction => {
        let matches = true;

        // Search term filter
        if (searchTerm) {
            const searchableText = `${auction.title} ${auction.artist} ${auction.category} ${auction.description}`.toLowerCase();
            matches = matches && searchableText.includes(searchTerm);
            if (searchTerm) activeFilters.searchTerm = searchTerm;
        }

        // Category filter
        if (selectedCategory) {
            matches = matches && auction.category === selectedCategory;
            activeFilters.category = selectedCategory;
        }

        // Status filter
        if (selectedStatus) {
            matches = matches && auction.status === selectedStatus;
            activeFilters.status = selectedStatus;
        }

        // Price filter
        if (minPrice > 0 || maxPrice < Number.POSITIVE_INFINITY) {
            matches = matches && auction.price >= minPrice && auction.price <= maxPrice;
            if (minPrice > 0) activeFilters.minPrice = minPrice;
            if (maxPrice < Number.POSITIVE_INFINITY) activeFilters.maxPrice = maxPrice;
        }

        return matches;
    });

    // Reset to first page when filters change
    currentPage = 1;
    totalPages = Math.ceil(filteredAuctions.length / auctionsPerPage);

    // Update UI
    renderActiveFilters();
    renderPaginatedAuctions();
    updatePaginationControls();
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

// Debounce function for search input
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

// Export functions for global access
window.applyFilters = applyFilters;
window.clearAllFilters = clearAllFilters;
window.removeFilter = removeFilter;
window.openAuctionPreview = openAuctionPreview;
window.previousPage = previousPage;
window.nextPage = nextPage;
window.goToPage = goToPage;
