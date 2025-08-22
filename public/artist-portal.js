// ==========================================
// ARTIST PORTAL JAVASCRIPT
// ==========================================

// Global Variables
let currentSection = 'dashboard';
let currentStep = 1;
let totalSteps = 3;

// ==========================================
// MOBILE MENU FUNCTIONALITY
// ==========================================

function initializeMobileMenu() {
    const mobileMenuIcon = document.getElementById('mobileMenuIcon');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('artistSidebar');
    
    // Mobile menu icon click handler
    if (mobileMenuIcon) {
        mobileMenuIcon.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }
    
    // Sidebar toggle button click handler
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 1024) {
            const sidebar = document.getElementById('artistSidebar');
            const mobileMenuIcon = document.getElementById('mobileMenuIcon');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            if (sidebar && sidebar.classList.contains('active')) {
                if (!sidebar.contains(e.target) && 
                    !mobileMenuIcon.contains(e.target) && 
                    (!sidebarToggle || !sidebarToggle.contains(e.target))) {
                    closeSidebar();
                }
            }
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        const sidebar = document.getElementById('artistSidebar');
        if (window.innerWidth > 1024 && sidebar) {
            sidebar.classList.remove('active');
        }
    });
}

function toggleSidebar() {
    const sidebar = document.getElementById('artistSidebar');
    const mobileMenuIcon = document.getElementById('mobileMenuIcon');
    
    if (sidebar) {
        sidebar.classList.toggle('active');
        
        // Update menu icon
        if (mobileMenuIcon) {
            mobileMenuIcon.classList.toggle('active');
            const icon = mobileMenuIcon.querySelector('i');
            if (icon) {
                if (sidebar.classList.contains('active')) {
                    icon.className = 'fas fa-times';
                } else {
                    icon.className = 'fas fa-bars';
                }
            }
        }
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('artistSidebar');
    const mobileMenuIcon = document.getElementById('mobileMenuIcon');
    
    if (sidebar) {
        sidebar.classList.remove('active');
        
        // Reset menu icon
        if (mobileMenuIcon) {
            mobileMenuIcon.classList.remove('active');
            const icon = mobileMenuIcon.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-bars';
            }
        }
    }
}

// ==========================================
// SIDEBAR NAVIGATION
// ==========================================

function initializeSidebarNavigation() {
    const sidebarLinks = document.querySelectorAll('.sidebarLink');
    
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetSection = this.getAttribute('data-section');
            if (targetSection) {
                switchSection(targetSection);
                
                // Close sidebar on mobile after navigation
                if (window.innerWidth <= 1024) {
                    closeSidebar();
                }
            }
        });
    });
}

function switchSection(sectionName) {
    // Hide all sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => {
        section.classList.remove('active');
    });
    
    // Show target section
    const targetSection = document.getElementById(`${sectionName}-section`);
    if (targetSection) {
        targetSection.classList.add('active');
    }
    
    // Update sidebar active state
    const sidebarLinks = document.querySelectorAll('.sidebarLink');
    sidebarLinks.forEach(link => {
        link.classList.remove('active');
    });
    
    const activeLink = document.querySelector(`[data-section="${sectionName}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
    
    currentSection = sectionName;
    
    // Special handling for auction section to ensure proper navigation
    if (sectionName === 'auction') {
        setTimeout(() => {
            // Don't interfere if navigation is in progress
            if (auctionNavigationInProgress) {
                console.log('Auction navigation in progress, skipping section switch initialization');
                return;
            }
            
            // Only reset to step 1 if no active step exists at all
            const hasActiveStep = document.querySelector('#addAuctionForm .formStep.active[data-step]');
            if (!hasActiveStep) {
                console.log('No active auction step found in switchSection, initializing to step 1');
                showAuctionStep(1);
                updateAuctionStepNavigation();
            } else {
                const currentStep = getCurrentAuctionStep();
                console.log('Active auction step found in switchSection:', currentStep);
                updateAuctionStepNavigation();
            }
        }, 150);
    }
    
    // Load statistics data when statistics section is accessed
    if (sectionName === 'statistics') {
        loadArtistStatistics();
    }
    
    // Load orders data when orders section is accessed
    if (sectionName === 'orders') {
        loadArtistOrders();
    }
    
    // Load reviews data when reviews section is accessed
    if (sectionName === 'reviews') {
        loadArtistReviews();
    }
    
    // Load profile data when profile section is accessed
    if (sectionName === 'profile') {
        console.log('📍 Profile section accessed, loading profile data...');
        setTimeout(() => {
            console.log('🔄 Triggering profile load...');
            loadArtistProfile();
        }, 300);
    }
}

// ==========================================
// DASHBOARD FUNCTIONALITY
// ==========================================

function initializeDashboard() {
    // Refresh button
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
            
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
                showNotification('Dashboard refreshed successfully!', 'success');
            }, 1500);
        });
    }
    
    // Initialize charts if Chart.js is available
    if (typeof Chart !== 'undefined') {
        initializeSalesChart();
    }
}

function initializeSalesChart() {
    const chartCanvas = document.getElementById('salesChart');
    if (!chartCanvas) return;
    
    const ctx = chartCanvas.getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [{
                label: 'Sales (EGP)',
                data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
                borderColor: '#6B4423',
                backgroundColor: 'rgba(107, 68, 35, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'EGP ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

// ==========================================
// REVIEWS FUNCTIONALITY
// ==========================================

// Global reviews state
let reviewsState = {
    currentPage: 1,
    totalPages: 1,
    filters: {
        type: 'all',
        rating: 'all',
        date: 'all'
    }
};

// Function to get user cookie for authentication
function getUserCookie() {
    const cookies = document.cookie.split(';');
    for (let cookie of cookies) {
        const [name, value] = cookie.trim().split('=');
        if (name === 'user_login') {
            return value;
        }
    }
    return null;
}

// Function to check user authentication
function ensureUserAuthentication() {
    return new Promise((resolve, reject) => {
        let userCookie = getUserCookie();
        
        if (!userCookie) {
            console.warn('No user_login cookie found. User needs to log in.');
            // Just reject without notification or redirect
            reject(new Error('No authentication cookie found'));
        } else {
            console.log('Using existing cookie:', userCookie);
            resolve(userCookie);
        }
    });
}

function initializeReviews() {
    console.log('Initializing reviews section...');
    
    // Refresh button
    const refreshReviewsBtn = document.getElementById('refreshReviewsBtn');
    if (refreshReviewsBtn) {
        refreshReviewsBtn.addEventListener('click', function() {
            loadArtistReviews();
        });
    }
    
    // Filter event listeners
    const reviewTypeFilter = document.getElementById('reviewType');
    const ratingFilter = document.getElementById('ratingFilter');
    const dateFilter = document.getElementById('dateFilter');
    
    if (reviewTypeFilter) {
        reviewTypeFilter.addEventListener('change', function() {
            reviewsState.filters.type = this.value;
            reviewsState.currentPage = 1;
            loadArtistReviews();
        });
    }
    
    if (ratingFilter) {
        ratingFilter.addEventListener('change', function() {
            reviewsState.filters.rating = this.value;
            reviewsState.currentPage = 1;
            loadArtistReviews();
        });
    }
    
    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            reviewsState.filters.date = this.value;
            reviewsState.currentPage = 1;
            loadArtistReviews();
        });
    }
    
    // Pagination event listeners
    const prevReviewsBtn = document.getElementById('prevReviewsBtn');
    const nextReviewsBtn = document.getElementById('nextReviewsBtn');
    
    if (prevReviewsBtn) {
        prevReviewsBtn.addEventListener('click', function() {
            if (reviewsState.currentPage > 1) {
                reviewsState.currentPage--;
                loadArtistReviews();
            }
        });
    }
    
    if (nextReviewsBtn) {
        nextReviewsBtn.addEventListener('click', function() {
            if (reviewsState.currentPage < reviewsState.totalPages) {
                reviewsState.currentPage++;
                loadArtistReviews();
            }
        });
    }
    
    // Load initial reviews
    loadArtistReviews();
}

async function loadArtistReviews() {
    try {
        const loadingElement = document.getElementById('reviewsLoading');
        const reviewsList = document.getElementById('reviewsList');
        const paginationContainer = document.getElementById('reviewsPagination');
        
        // Show loading state
        if (loadingElement) loadingElement.style.display = 'block';
        if (reviewsList) reviewsList.innerHTML = '';
        if (paginationContainer) paginationContainer.style.display = 'none';
        
        // Build query parameters
        const params = new URLSearchParams({
            page: reviewsState.currentPage,
            limit: 3,
            type: reviewsState.filters.type,
            date: reviewsState.filters.date
        });
        
        if (reviewsState.filters.rating !== 'all') {
            params.append('rating', reviewsState.filters.rating);
        }
        
        console.log('Making API request with params:', params.toString());
        
        const response = await fetch(`./API/getArtistReviews.php?${params}`, {
            method: 'GET',
            credentials: 'include', // This will include session cookies
            headers: {
                'Content-Type': 'application/json',
                'Cache-Control': 'no-cache'
            }
        });
        
        console.log('API Response status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('API Error Response:', errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('API Response data:', data);
        
        if (data.success) {
            // Update statistics
            updateReviewsStatistics(data.statistics);
            
            // Update pagination state
            reviewsState.totalPages = data.pagination.total_pages;
            
            // Render reviews
            renderReviews(data.reviews);
            
            // Update pagination
            updateReviewsPagination(data.pagination);
            
        } else {
            throw new Error(data.error || 'Failed to load reviews');
        }
        
    } catch (error) {
        console.error('Error loading reviews:', error);
        
        // Show empty state without notification
        const reviewsList = document.getElementById('reviewsList');
        if (reviewsList) {
            reviewsList.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-star-half-alt"></i>
                    <h3>No Reviews Found</h3>
                    <p>There are no reviews to display with the current filters.</p>
                </div>
            `;
        }
        
    } finally {
        const loadingElement = document.getElementById('reviewsLoading');
        if (loadingElement) loadingElement.style.display = 'none';
    }
}

function updateReviewsStatistics(stats) {
    const avgRatingElement = document.getElementById('averageRating');
    const totalReviewsElement = document.getElementById('totalReviews');
    const positiveReviewsElement = document.getElementById('positiveReviews');
    const recentReviewsElement = document.getElementById('recentReviews');
    
    if (avgRatingElement) avgRatingElement.textContent = stats.average_rating || '0.0';
    if (totalReviewsElement) totalReviewsElement.textContent = stats.total_reviews || '0';
    if (positiveReviewsElement) positiveReviewsElement.textContent = (stats.positive_percentage || 0) + '%';
    if (recentReviewsElement) recentReviewsElement.textContent = stats.recent_reviews || '0';
}

function renderReviews(reviews) {
    const reviewsList = document.getElementById('reviewsList');
    if (!reviewsList) return;
    
    if (reviews.length === 0) {
        reviewsList.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-star-half-alt"></i>
                <h3>No Reviews Yet</h3>
                <p>You haven't received any reviews with the current filters. Keep creating amazing artwork!</p>
            </div>
        `;
        return;
    }
    
    const reviewsHTML = reviews.map(review => `
        <div class="review-item" data-rating="${review.rating}" data-type="${review.type.toLowerCase()}">
            <div class="review-header">
                <div class="reviewer-info">
                    <div class="reviewer-avatar">
                        ${review.reviewer_name.charAt(0).toUpperCase()}
                    </div>
                    <div class="reviewer-details">
                        <h4 class="reviewer-name">${escapeHtml(review.reviewer_name)}</h4>
                        <div class="review-rating">
                            ${generateStarRating(review.rating)}
                        </div>
                    </div>
                </div>
                <div class="review-meta">
                    <span class="review-type ${review.type.toLowerCase()}" data-type="${review.type.toLowerCase()}">${review.type === 'artwork' ? 'ARTWORK' : 'COURSE'}</span>
                    <span class="review-time">${review.time_ago}</span>
                </div>
            </div>
            <div class="review-content">
                <h5 class="reviewed-item">${escapeHtml(review.item_title)}</h5>
                <p class="review-text">${escapeHtml(review.review_text)}</p>
            </div>
        </div>
    `).join('');
    
    reviewsList.innerHTML = reviewsHTML;
}

function updateReviewsPagination(pagination) {
    const paginationContainer = document.getElementById('reviewsPagination');
    const pageInfo = document.getElementById('reviewsPageInfo');
    const prevBtn = document.getElementById('prevReviewsBtn');
    const nextBtn = document.getElementById('nextReviewsBtn');
    
    console.log('Pagination data:', pagination);
    
    if (!paginationContainer) {
        console.error('Pagination container not found!');
        return;
    }
    
    // Always show pagination for debugging, then check if we actually need it
    paginationContainer.style.display = 'flex';
    
    if (pagination.total_pages <= 1) {
        console.log('Only 1 page, hiding pagination');
        paginationContainer.style.display = 'none';
        return;
    }
    
    console.log('Showing pagination - Current page:', pagination.current_page, 'Total pages:', pagination.total_pages);
    
    if (pageInfo) {
        pageInfo.textContent = `Page ${pagination.current_page} of ${pagination.total_pages}`;
    }
    
    if (prevBtn) {
        prevBtn.disabled = pagination.current_page <= 1;
    }
    
    if (nextBtn) {
        nextBtn.disabled = pagination.current_page >= pagination.total_pages;
    }
}

function generateStarRating(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<i class="fas fa-star" style="color: #ffd700; text-shadow: 0 1px 2px rgba(0,0,0,0.1);"></i>';
        } else {
            stars += '<i class="far fa-star" style="color: #e0d0c4;"></i>';
        }
    }
    return `<span class="star-rating">${stars} <span class="rating-text">(${rating}/5)</span></span>`;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ==========================================
// ORDERS FUNCTIONALITY
// ==========================================

// Global orders variables
let ordersData = [];
let filteredOrders = [];
let currentOrdersPage = 1;
let ordersPerPage = 10;

function initializeOrders() {
    // Order status filter
    const statusFilter = document.getElementById('orderStatusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            filterOrders();
        });
    }
    
    // Order search
    const orderSearch = document.getElementById('orderSearch');
    if (orderSearch) {
        orderSearch.addEventListener('input', debounce(function() {
            filterOrders();
        }, 300));
    }
    
    // Export button
    const exportBtn = document.getElementById('exportOrdersBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            exportOrders();
        });
    }
    
    // Refresh button
    const refreshBtn = document.getElementById('refreshOrdersBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            loadArtistOrders();
        });
    }
    
    // Pagination
    const prevBtn = document.getElementById('prevOrdersBtn');
    const nextBtn = document.getElementById('nextOrdersBtn');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (currentOrdersPage > 1) {
                currentOrdersPage--;
                displayOrders();
            }
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            const totalPages = Math.ceil(filteredOrders.length / ordersPerPage);
            if (currentOrdersPage < totalPages) {
                currentOrdersPage++;
                displayOrders();
            }
        });
    }
    
    // Load orders when initializing
    loadArtistOrders();
}

async function loadArtistOrders() {
    try {
        showOrdersLoading();
        
        const response = await fetch('/API/getArtistOrders.php', {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            ordersData = result.data.orders || [];
            filteredOrders = [...ordersData];
            currentOrdersPage = 1;
            
            updateOrdersStatistics(result.data.statistics);
            displayOrders();
            hideOrdersLoading();
            
            if (ordersData.length === 0) {
                showOrdersEmptyState();
            } else {
                showOrdersTable();
            }
        } else {
            throw new Error(result.message || 'Failed to load orders');
        }
    } catch (error) {
        console.error('Error loading artist orders:', error);
        hideOrdersLoading();
        showOrdersError(error.message);
    }
}

function showOrdersLoading() {
    const loadingState = document.getElementById('ordersLoadingState');
    const tableContainer = document.getElementById('ordersTableContainer');
    const emptyState = document.getElementById('ordersEmptyState');
    const errorState = document.getElementById('ordersErrorState');
    const pagination = document.getElementById('ordersPagination');
    
    if (loadingState) loadingState.style.display = 'block';
    if (tableContainer) tableContainer.style.display = 'none';
    if (emptyState) emptyState.style.display = 'none';
    if (errorState) errorState.style.display = 'none';
    if (pagination) pagination.style.display = 'none';
}

function hideOrdersLoading() {
    const loadingState = document.getElementById('ordersLoadingState');
    if (loadingState) loadingState.style.display = 'none';
}

function showOrdersEmptyState() {
    const emptyState = document.getElementById('ordersEmptyState');
    const tableContainer = document.getElementById('ordersTableContainer');
    const errorState = document.getElementById('ordersErrorState');
    const pagination = document.getElementById('ordersPagination');
    
    if (emptyState) emptyState.style.display = 'block';
    if (tableContainer) tableContainer.style.display = 'none';
    if (errorState) errorState.style.display = 'none';
    if (pagination) pagination.style.display = 'none';
}

function showOrdersError(message) {
    const errorState = document.getElementById('ordersErrorState');
    const errorMessage = document.getElementById('ordersErrorMessage');
    const tableContainer = document.getElementById('ordersTableContainer');
    const emptyState = document.getElementById('ordersEmptyState');
    const pagination = document.getElementById('ordersPagination');
    
    if (errorState) errorState.style.display = 'block';
    if (errorMessage) errorMessage.textContent = message;
    if (tableContainer) tableContainer.style.display = 'none';
    if (emptyState) emptyState.style.display = 'none';
    if (pagination) pagination.style.display = 'none';
}

function showOrdersTable() {
    const tableContainer = document.getElementById('ordersTableContainer');
    const emptyState = document.getElementById('ordersEmptyState');
    const errorState = document.getElementById('ordersErrorState');
    const pagination = document.getElementById('ordersPagination');
    
    if (tableContainer) tableContainer.style.display = 'block';
    if (emptyState) emptyState.style.display = 'none';
    if (errorState) errorState.style.display = 'none';
    if (pagination) pagination.style.display = 'block';
}

function updateOrdersStatistics(stats) {
    const totalOrdersCount = document.getElementById('totalOrdersCount');
    const totalRevenueAmount = document.getElementById('totalRevenueAmount');
    const pendingOrdersCount = document.getElementById('pendingOrdersCount');
    const deliveredOrdersCount = document.getElementById('deliveredOrdersCount');
    
    if (totalOrdersCount) totalOrdersCount.textContent = formatNumber(stats.total_orders || 0);
    if (totalRevenueAmount) totalRevenueAmount.textContent = 'EGP ' + formatNumber(stats.total_revenue || 0);
    if (pendingOrdersCount) pendingOrdersCount.textContent = formatNumber(stats.pending_orders || 0);
    if (deliveredOrdersCount) deliveredOrdersCount.textContent = formatNumber(stats.delivered_orders || 0);
}

function displayOrders() {
    const tbody = document.getElementById('ordersTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    // Calculate pagination
    const startIndex = (currentOrdersPage - 1) * ordersPerPage;
    const endIndex = startIndex + ordersPerPage;
    const pageOrders = filteredOrders.slice(startIndex, endIndex);
    
    // Generate table rows
    pageOrders.forEach(order => {
        const row = createOrderRow(order);
        tbody.appendChild(row);
    });
    
    // Update pagination controls
    updateOrdersPagination();
}

function createOrderRow(order) {
    const row = document.createElement('tr');
    
    // Format date
    const orderDate = new Date(order.order_details.order_date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
    
    // Create items summary
    const itemsText = order.items_count === 1 
        ? `${order.artist_items[0].artwork_title}` 
        : `${order.items_count} items`;
    
    // Status badge class - use statusBadge styling
    const statusClass = `statusBadge status-${order.order_details.status.toLowerCase()}`;
    
    row.innerHTML = `
        <td class="order-number">${escapeHtml(order.order_number)}</td>
        <td class="customer-name">${escapeHtml(order.buyer.buyer_name)}</td>
        <td class="order-items">
            <div class="items-summary">
                <span class="items-text">${escapeHtml(itemsText)}</span>
            </div>
        </td>
        <td class="artist-revenue">
            <strong>EGP ${formatNumber(order.artist_revenue)}</strong>
        </td>
        <td class="total-amount">EGP ${formatNumber(order.order_details.total_amount)}</td>
        <td class="order-status">
            <span class="${statusClass}">${capitalizeFirst(order.order_details.status)}</span>
        </td>
        <td class="order-date">${orderDate}</td>
        <td class="order-actions">
            <button class="actionBtn btn-view" onclick="viewOrderDetails(${order.order_id})" title="View Order Details">
                <i class="fas fa-eye"></i>
            </button>
        </td>
    `;
    
    return row;
}

function updateOrdersPagination() {
    const totalPages = Math.ceil(filteredOrders.length / ordersPerPage);
    const startIndex = (currentOrdersPage - 1) * ordersPerPage;
    const endIndex = Math.min(startIndex + ordersPerPage, filteredOrders.length);
    
    // Update page info
    const pageInfo = document.getElementById('ordersPageInfo');
    if (pageInfo) {
        pageInfo.textContent = `Page ${currentOrdersPage} of ${totalPages}`;
    }
    
    // Update navigation buttons
    const prevBtn = document.getElementById('prevOrdersBtn');
    const nextBtn = document.getElementById('nextOrdersBtn');
    
    if (prevBtn) {
        prevBtn.disabled = currentOrdersPage <= 1;
    }
    
    if (nextBtn) {
        nextBtn.disabled = currentOrdersPage >= totalPages;
    }
}

function filterOrders() {
    const statusFilter = document.getElementById('orderStatusFilter');
    const orderSearch = document.getElementById('orderSearch');
    
    const selectedStatus = statusFilter ? statusFilter.value : '';
    const searchTerm = orderSearch ? orderSearch.value.toLowerCase() : '';
    
    filteredOrders = ordersData.filter(order => {
        // Status filter
        const statusMatch = !selectedStatus || order.order_details.status === selectedStatus;
        
        // Search filter
        const searchMatch = !searchTerm || 
            order.order_number.toLowerCase().includes(searchTerm) ||
            order.buyer.buyer_name.toLowerCase().includes(searchTerm) ||
            order.artist_items.some(item => item.artwork_title.toLowerCase().includes(searchTerm));
        
        return statusMatch && searchMatch;
    });
    
    currentOrdersPage = 1;
    displayOrders();
}

function exportOrders() {
    if (filteredOrders.length === 0) {
        showNotification('No orders to export', 'warning');
        return;
    }
    
    try {
        const csvContent = generateOrdersCSV(filteredOrders);
        downloadCSV(csvContent, 'artist-orders.csv');
        showNotification('Orders exported successfully!', 'success');
    } catch (error) {
        console.error('Export error:', error);
        showNotification('Failed to export orders', 'error');
    }
}

function generateOrdersCSV(orders) {
    const headers = [
        'Order Number',
        'Customer Name',
        'Items Count',
        'Artist Revenue',
        'Total Amount',
        'Status',
        'Order Date',
        'Items'
    ];
    
    const rows = orders.map(order => [
        order.order_number,
        order.buyer.buyer_name,
        order.items_count,
        order.artist_revenue,
        order.order_details.total_amount,
        order.order_details.status,
        order.order_details.order_date,
        order.artist_items.map(item => `${item.artwork_title} (${item.quantity}x)`).join('; ')
    ]);
    
    return [headers, ...rows]
        .map(row => row.map(field => `"${String(field).replace(/"/g, '""')}"`).join(','))
        .join('\n');
}

function downloadCSV(content, filename) {
    const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

function viewOrderDetails(orderId) {
    const order = ordersData.find(o => o.order_id === orderId);
    if (!order) {
        showNotification('Order not found', 'error');
        return;
    }
    
    showOrderDetailsModal(order);
}

function showOrderDetails(orderId) {
    viewOrderDetails(orderId);
}

function showOrderDetailsModal(order) {
    const itemsList = order.artist_items.map(item => 
        `<div class="order-item">
            <span class="item-title">${escapeHtml(item.artwork_title)}</span>
            <span class="item-details">${item.quantity}x EGP ${formatNumber(item.price)} = EGP ${formatNumber(item.subtotal)}</span>
        </div>`
    ).join('');
    
    Swal.fire({
        title: `Order ${order.order_number}`,
        html: `
            <div class="order-details-modal">
                <div class="order-info">
                    <h4>Customer Information</h4>
                    <p><strong>Name:</strong> ${escapeHtml(order.buyer.buyer_name)}</p>
                    <p><strong>Order Date:</strong> ${new Date(order.order_details.order_date).toLocaleDateString()}</p>
                    <p><strong>Status:</strong> <span class="statusBadge status-${order.order_details.status.toLowerCase()}">${capitalizeFirst(order.order_details.status)}</span></p>
                </div>
                
                <div class="order-items">
                    <h4>Your Items</h4>
                    ${itemsList}
                </div>
                
                <div class="order-summary">
                    <h4>Revenue Summary</h4>
                    <p><strong>Your Revenue:</strong> EGP ${formatNumber(order.artist_revenue)}</p>
                    <p><strong>Total Order Value:</strong> EGP ${formatNumber(order.order_details.total_amount)}</p>
                </div>
            </div>
        `,
        width: '600px',
        showCloseButton: true,
        showConfirmButton: false,
        customClass: {
            container: 'order-details-swal'
        }
    });
}

function trackOrder(orderNumber) {
    showNotification(`Tracking information for order ${orderNumber} will be available soon`, 'info');
}

// Utility functions
function formatNumber(num) {
    return new Intl.NumberFormat('en-US').format(num);
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

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

// ==========================================
// PROFILE FUNCTIONALITY
// ==========================================

function initializeProfile() {
    // Character counters
    const bioTextarea = document.getElementById('artistBio');
    const bioCounter = document.getElementById('bioCharCount');
    
    if (bioTextarea && bioCounter) {
        bioTextarea.addEventListener('input', function() {
            bioCounter.textContent = this.value.length;
        });
        
        // Initialize counter
        bioCounter.textContent = bioTextarea.value.length;
    }
    
    // Achievement management
    initializeAchievements();
    
    // Password functionality
    initializePasswordFields();
    
    // Form submissions
    const artistInfoForm = document.getElementById('artistInfoForm');
    if (artistInfoForm) {
        artistInfoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveArtistInfo();
        });
    }
    
    const securityForm = document.getElementById('securityForm');
    if (securityForm) {
        securityForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveSecuritySettings();
        });
    }
}

function initializeAchievements() {
    const addBtn = document.getElementById('addAchievementBtn');
    const newAchievementInput = document.getElementById('newAchievement');
    
    if (addBtn && newAchievementInput) {
        addBtn.addEventListener('click', function() {
            addAchievement();
        });
        
        newAchievementInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addAchievement();
            }
        });
    }
    
    // Remove achievement buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('removeAchievement') || e.target.closest('.removeAchievement')) {
            e.preventDefault();
            const achievementItem = e.target.closest('.achievementItem');
            if (achievementItem) {
                const achievementText = achievementItem.querySelector('span').textContent;
                const achievementId = achievementItem.getAttribute('data-achievement-id');
                removeAchievement(achievementItem, achievementId, achievementText);
            }
        }
    });
}

async function addAchievement() {
    const input = document.getElementById('newAchievement');
    const list = document.getElementById('achievementsList');
    
    if (input && list && input.value.trim()) {
        const achievementText = input.value.trim();
        
        try {
            console.log('🏆 Adding achievement:', achievementText);
            
            // Call the dedicated add achievement API
            const response = await fetch('./API/addAchievement.php', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    achievement: achievementText
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success) {
                console.log('✅ Achievement saved successfully');
                
                // Create the UI element after successful save
                const achievementItem = document.createElement('div');
                achievementItem.className = 'achievementItem';
                achievementItem.setAttribute('data-achievement-id', result.data.achievement_id);
                achievementItem.innerHTML = `
                    <span>${achievementText}</span>
                    <button type="button" class="removeAchievement"><i class="fas fa-times"></i></button>
                `;
                
                // Add click handler for the remove button
                const removeBtn = achievementItem.querySelector('.removeAchievement');
                removeBtn.addEventListener('click', function() {
                    removeAchievement(achievementItem, result.data.achievement_id, achievementText);
                });
                
                list.appendChild(achievementItem);
                input.value = '';
                
                // Show success message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Achievement Added!',
                        text: 'Your new achievement has been successfully added to your profile.',
                        timer: 3000,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'swal-success-popup',
                            title: 'swal-success-title'
                        }
                    });
                }
            } else {
                throw new Error(result.message || 'Failed to save achievement');
            }
            
        } catch (error) {
            console.error('❌ Failed to add achievement:', error);
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Unable to Add Achievement',
                    text: 'Something went wrong while saving your achievement. Please check your connection and try again.',
                    confirmButtonText: 'Try Again',
                    confirmButtonColor: '#8B5A3C',
                    customClass: {
                        popup: 'swal-error-popup',
                        title: 'swal-error-title',
                        confirmButton: 'swal-confirm-btn'
                    }
                });
            } else {
                alert('Failed to save achievement. Please try again.');
            }
        }
    }
}

// Function to remove achievement using API
async function removeAchievement(achievementElement, achievementId, achievementName) {
    try {
        console.log('🗑️ Removing achievement:', { achievementId, achievementName });
        
        // Show confirmation dialog
        if (typeof Swal !== 'undefined') {
            const result = await Swal.fire({
                title: 'Remove Achievement',
                text: 'This achievement will be permanently removed from your profile. Are you sure you want to continue?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#8B5A3C',
                cancelButtonColor: '#6C757D',
                confirmButtonText: 'Remove Achievement',
                cancelButtonText: 'Keep It',
                customClass: {
                    popup: 'swal-custom-popup',
                    title: 'swal-custom-title',
                    confirmButton: 'swal-confirm-btn',
                    cancelButton: 'swal-cancel-btn'
                }
            });
            
            if (!result.isConfirmed) {
                return; // User cancelled
            }
        } else {
            if (!confirm('Are you sure you want to remove this achievement?')) {
                return; // User cancelled
            }
        }
        
        // Call the dedicated delete achievement API
        const response = await fetch('./API/deleteAchievement.php', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                achievement_id: achievementId,
                achievement_name: achievementName
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        if (result.success) {
            console.log('✅ Achievement deleted successfully');
            
            // Remove the UI element
            if (achievementElement && achievementElement.parentNode) {
                achievementElement.remove();
            }
            
            // Show success message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Achievement Removed',
                    text: 'Your achievement has been successfully removed from your profile.',
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'swal-success-popup',
                        title: 'swal-success-title'
                    }
                });
            }
        } else {
            throw new Error(result.message || 'Failed to delete achievement');
        }
        
    } catch (error) {
        console.error('❌ Failed to remove achievement:', error);
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Unable to Remove Achievement',
                text: 'Something went wrong while removing your achievement. Please try again in a moment.',
                confirmButtonText: 'Try Again',
                confirmButtonColor: '#8B5A3C',
                customClass: {
                    popup: 'swal-error-popup',
                    title: 'swal-error-title',
                    confirmButton: 'swal-confirm-btn'
                }
            });
        } else {
            alert('Failed to delete achievement. Please try again.');
        }
    }
}

function initializePasswordFields() {
    const newPasswordField = document.getElementById('newPassword');
    const confirmPasswordField = document.getElementById('confirmPassword');
    
    if (newPasswordField) {
        newPasswordField.addEventListener('input', function() {
            updatePasswordStrength(this.value);
            checkPasswordMatch();
        });
    }
    
    if (confirmPasswordField) {
        confirmPasswordField.addEventListener('input', checkPasswordMatch);
    }
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

function updatePasswordStrength(password) {
    const strengthBar = document.querySelector('.strengthFill');
    const strengthText = document.querySelector('.strengthText');
    
    if (!strengthBar || !strengthText) return;
    
    let strength = 0;
    let text = 'Very Weak';
    let color = '#ff4444';
    
    if (password.length >= 8) strength += 20;
    if (password.match(/[a-z]/)) strength += 20;
    if (password.match(/[A-Z]/)) strength += 20;
    if (password.match(/[0-9]/)) strength += 20;
    if (password.match(/[^a-zA-Z0-9]/)) strength += 20;
    
    if (strength >= 80) {
        text = 'Very Strong';
        color = '#22c55e';
    } else if (strength >= 60) {
        text = 'Strong';
        color = '#84cc16';
    } else if (strength >= 40) {
        text = 'Medium';
        color = '#f59e0b';
    } else if (strength >= 20) {
        text = 'Weak';
        color = '#f97316';
    }
    
    strengthBar.style.width = strength + '%';
    strengthBar.style.backgroundColor = color;
    strengthText.textContent = text;
}

function checkPasswordMatch() {
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    const matchIndicator = document.getElementById('matchIndicator');
    
    if (!newPassword || !confirmPassword || !matchIndicator) return;
    
    if (confirmPassword.value === '') {
        matchIndicator.textContent = '';
        return;
    }
    
    if (newPassword.value === confirmPassword.value) {
        matchIndicator.textContent = '✓ Passwords match';
        matchIndicator.style.color = '#22c55e';
    } else {
        matchIndicator.textContent = '✗ Passwords do not match';
        matchIndicator.style.color = '#ef4444';
    }
}

function saveArtistInfo() {
    showNotification('Profile updated successfully!', 'success');
}

function saveSecuritySettings() {
    showNotification('Security settings updated successfully!', 'success');
}

// ==========================================
// ARTWORK FORM FUNCTIONALITY
// ==========================================

function initializeArtworkForm() {
    
    const nextBtn = document.getElementById('nextStep');
    const prevBtn = document.getElementById('prevStep');
    const publishBtn = document.getElementById('publishBtn');
    
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (validateCurrentStep()) {
                nextStep();
            }
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            previousStep();
        });
    }
    
    if (publishBtn) {
        publishBtn.addEventListener('click', function(e) {
            e.preventDefault();
            publishArtwork();
        });
    }
    
    // Price calculation
    const priceInput = document.getElementById('artworkPrice');
    if (priceInput) {
        priceInput.addEventListener('input', function() {
            calculateNetPrice(this.value);
        });
    }
    
    // Description character counter
    const descInput = document.getElementById('artworkDescription');
    const descCounter = document.getElementById('descCharCount');
    
    if (descInput && descCounter) {
        descInput.addEventListener('input', function() {
            descCounter.textContent = this.value.length;
        });
    }
    
    // Set current year as default value for year field
    const yearInput = document.getElementById('artworkYear');
    if (yearInput) {
        const currentYear = new Date().getFullYear();
        yearInput.value = currentYear;
    }
    
    // Add real-time validation feedback
    addRealTimeValidation();
    
    // Image upload
    initializeImageUpload();
    
    // Form submission
    const artworkForm = document.getElementById('addArtworkForm');
    if (artworkForm) {
        artworkForm.addEventListener('submit', function(e) {
            e.preventDefault();
            publishArtwork();
        });
    }
}

function addRealTimeValidation() {
    // Get all form fields with indicators
    const fields = [
        { field: 'artworkName', indicator: 'artworkNameIndicator', error: 'artworkNameError' },
        { field: 'artworkPrice', indicator: 'artworkPriceIndicator', error: 'artworkPriceError' },
        { field: 'artworkCategory', indicator: 'artworkCategoryIndicator', error: 'artworkCategoryError' },
        { field: 'artworkStyle', indicator: 'artworkStyleIndicator', error: 'artworkStyleError' },
        { field: 'artworkWidth', indicator: 'artworkWidthIndicator', error: 'artworkWidthError' },
        { field: 'artworkHeight', indicator: 'artworkHeightIndicator', error: 'artworkHeightError' },
        { field: 'artworkDepth', indicator: 'artworkDepthIndicator', error: 'artworkDepthError' },
        { field: 'artworkYear', indicator: 'artworkYearIndicator', error: 'artworkYearError' },
        { field: 'artworkDescription', indicator: 'artworkDescriptionIndicator', error: 'artworkDescriptionError' }
    ];

    fields.forEach(({ field, indicator, error }) => {
        const fieldElement = document.getElementById(field);
        const indicatorElement = document.getElementById(indicator);
        const errorElement = document.getElementById(error);

        if (fieldElement) {
            // Add event listeners for real-time validation
            fieldElement.addEventListener('input', function() {
                validateSingleField(fieldElement, indicatorElement, errorElement);
            });
            
            fieldElement.addEventListener('blur', function() {
                validateSingleField(fieldElement, indicatorElement, errorElement);
            });
            
            fieldElement.addEventListener('change', function() {
                validateSingleField(fieldElement, indicatorElement, errorElement);
            });
        }
    });
}

function validateSingleField(field, indicator, errorElement) {
    const fieldId = field.id;
    
    switch (fieldId) {
        case 'artworkName':
            const title = field.value.trim();
            if (!title) {
                clearFieldState(field, indicator, errorElement);
            } else if (/^\d+$/.test(title)) {
                setFieldError(field, indicator, errorElement, 'Title should contain words, not just numbers');
            } else if (title.length < 3) {
                setFieldError(field, indicator, errorElement, 'Title should be at least 3 characters long');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'artworkPrice':
            const price = parseFloat(field.value);
            if (!field.value.trim()) {
                clearFieldState(field, indicator, errorElement);
            } else if (isNaN(price) || price <= 0) {
                setFieldError(field, indicator, errorElement, 'Price must be a positive number');
            } else if (price > 3000000) {
                setFieldError(field, indicator, errorElement, 'Price cannot exceed 3,000,000 EGP');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'artworkCategory':
        case 'artworkStyle':
            if (!field.value) {
                clearFieldState(field, indicator, errorElement);
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'artworkWidth':
            const width = parseFloat(field.value);
            if (!field.value.trim()) {
                clearFieldState(field, indicator, errorElement);
            } else if (isNaN(width) || width <= 0) {
                setFieldError(field, indicator, errorElement, 'Width must be a positive number');
            } else if (width < 1 || width > 1000) {
                setFieldError(field, indicator, errorElement, 'Width should be between 1cm and 1000cm');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'artworkHeight':
            const height = parseFloat(field.value);
            if (!field.value.trim()) {
                clearFieldState(field, indicator, errorElement);
            } else if (isNaN(height) || height <= 0) {
                setFieldError(field, indicator, errorElement, 'Height must be a positive number');
            } else if (height < 1 || height > 1000) {
                setFieldError(field, indicator, errorElement, 'Height should be between 1cm and 1000cm');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'artworkDepth':
            if (!field.value.trim()) {
                clearFieldState(field, indicator, errorElement);
                return; // Optional field, so return early
            }
            const depth = parseFloat(field.value);
            if (isNaN(depth) || depth < 0) {
                setFieldError(field, indicator, errorElement, 'Depth must be a positive number or empty');
            } else if (depth > 500) {
                setFieldError(field, indicator, errorElement, 'Depth should not exceed 500cm');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'artworkYear':
            if (!field.value.trim()) {
                clearFieldState(field, indicator, errorElement);
                return; // Optional field, so return early
            }
            const year = parseInt(field.value);
            const currentYear = new Date().getFullYear();
            if (isNaN(year)) {
                setFieldError(field, indicator, errorElement, 'Year must be a valid number');
            } else if (year < 1800) {
                setFieldError(field, indicator, errorElement, 'Year cannot be before 1800');
            } else if (year > currentYear) {
                setFieldError(field, indicator, errorElement, `Year cannot be in the future (current year: ${currentYear})`);
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'artworkDescription':
            const description = field.value.trim();
            if (!description) {
                clearFieldState(field, indicator, errorElement);
            } else if (description.length < 10) {
                setFieldError(field, indicator, errorElement, 'Description should be at least 10 characters long');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
    }
}

function validateCurrentStep() {
    const currentStepElement = document.querySelector(`.formStep[data-step="${currentStep}"]`);
    if (!currentStepElement) return false;
    
    let isValid = true;
    let errorMessages = [];
    
    // Step 1: Artwork Information validation
    if (currentStep === 1) {
        // 1. Title validation - should be words, not just numbers
        const titleField = document.getElementById('artworkName');
        const titleIndicator = document.getElementById('artworkNameIndicator');
        const titleError = document.getElementById('artworkNameError');
        
        if (titleField) {
            const title = titleField.value.trim();
            if (!title) {
                setFieldError(titleField, titleIndicator, titleError, 'Artwork title is required');
                errorMessages.push('Artwork title is required');
                isValid = false;
            } else if (/^\d+$/.test(title)) {
                setFieldError(titleField, titleIndicator, titleError, 'Artwork title should contain words, not just numbers');
                errorMessages.push('Artwork title should contain words, not just numbers');
                isValid = false;
            } else if (title.length < 3) {
                setFieldError(titleField, titleIndicator, titleError, 'Artwork title should be at least 3 characters long');
                errorMessages.push('Artwork title should be at least 3 characters long');
                isValid = false;
            } else {
                setFieldValid(titleField, titleIndicator, titleError);
            }
        }
        
        // 2. Price validation - maximum 3 million EGP
        const priceField = document.getElementById('artworkPrice');
        const priceIndicator = document.getElementById('artworkPriceIndicator');
        const priceError = document.getElementById('artworkPriceError');
        
        if (priceField) {
            const price = parseFloat(priceField.value);
            if (!priceField.value.trim()) {
                setFieldError(priceField, priceIndicator, priceError, 'Price is required');
                errorMessages.push('Price is required');
                isValid = false;
            } else if (isNaN(price) || price <= 0) {
                setFieldError(priceField, priceIndicator, priceError, 'Price must be a valid positive number');
                errorMessages.push('Price must be a valid positive number');
                isValid = false;
            } else if (price > 3000000) {
                setFieldError(priceField, priceIndicator, priceError, 'Price cannot exceed 3,000,000 EGP');
                errorMessages.push('Price cannot exceed 3,000,000 EGP');
                isValid = false;
            } else {
                setFieldValid(priceField, priceIndicator, priceError);
            }
        }
        
        // 3. Category validation
        const categoryField = document.getElementById('artworkCategory');
        const categoryIndicator = document.getElementById('artworkCategoryIndicator');
        const categoryError = document.getElementById('artworkCategoryError');
        
        if (categoryField) {
            const validCategories = ['paintings', 'sculptures', 'textiles', 'wooden_arts', 'ceramic_arts', 'fiber_arts'];
            const category = categoryField.value;
            if (!category) {
                setFieldError(categoryField, categoryIndicator, categoryError, 'Please select a category');
                errorMessages.push('Please select a category');
                isValid = false;
            } else {
                setFieldValid(categoryField, categoryIndicator, categoryError);
            }
        }
        
        // Style validation
        const styleField = document.getElementById('artworkStyle');
        const styleIndicator = document.getElementById('artworkStyleIndicator');
        const styleError = document.getElementById('artworkStyleError');
        
        if (styleField) {
            if (!styleField.value) {
                setFieldError(styleField, styleIndicator, styleError, 'Please select an art style');
                errorMessages.push('Please select an art style');
                isValid = false;
            } else {
                setFieldValid(styleField, styleIndicator, styleError);
            }
        }
        
        // 4. Width and Height validation - suitable for artworks
        const widthField = document.getElementById('artworkWidth');
        const widthIndicator = document.getElementById('artworkWidthIndicator');
        const widthError = document.getElementById('artworkWidthError');
        const heightField = document.getElementById('artworkHeight');
        const heightIndicator = document.getElementById('artworkHeightIndicator');
        const heightError = document.getElementById('artworkHeightError');
        
        if (widthField) {
            const width = parseFloat(widthField.value);
            if (!widthField.value.trim()) {
                setFieldError(widthField, widthIndicator, widthError, 'Width is required');
                errorMessages.push('Width is required');
                isValid = false;
            } else if (isNaN(width) || width <= 0) {
                setFieldError(widthField, widthIndicator, widthError, 'Width must be a positive number');
                errorMessages.push('Width must be a positive number');
                isValid = false;
            } else if (width < 1 || width > 1000) {
                setFieldError(widthField, widthIndicator, widthError, 'Width should be between 1cm and 1000cm (10 meters)');
                errorMessages.push('Width should be between 1cm and 1000cm (10 meters)');
                isValid = false;
            } else {
                setFieldValid(widthField, widthIndicator, widthError);
            }
        }
        
        if (heightField) {
            const height = parseFloat(heightField.value);
            if (!heightField.value.trim()) {
                setFieldError(heightField, heightIndicator, heightError, 'Height is required');
                errorMessages.push('Height is required');
                isValid = false;
            } else if (isNaN(height) || height <= 0) {
                setFieldError(heightField, heightIndicator, heightError, 'Height must be a positive number');
                errorMessages.push('Height must be a positive number');
                isValid = false;
            } else if (height < 1 || height > 1000) {
                setFieldError(heightField, heightIndicator, heightError, 'Height should be between 1cm and 1000cm (10 meters)');
                errorMessages.push('Height should be between 1cm and 1000cm (10 meters)');
                isValid = false;
            } else {
                setFieldValid(heightField, heightIndicator, heightError);
            }
        }
        
        // Depth validation (optional)
        const depthField = document.getElementById('artworkDepth');
        const depthIndicator = document.getElementById('artworkDepthIndicator');
        const depthError = document.getElementById('artworkDepthError');
        
        if (depthField && depthField.value.trim()) {
            const depth = parseFloat(depthField.value);
            if (isNaN(depth) || depth < 0) {
                setFieldError(depthField, depthIndicator, depthError, 'Depth must be a positive number or empty');
                errorMessages.push('Depth must be a positive number or empty');
                isValid = false;
            } else if (depth > 500) {
                setFieldError(depthField, depthIndicator, depthError, 'Depth should not exceed 500cm');
                errorMessages.push('Depth should not exceed 500cm');
                isValid = false;
            } else {
                setFieldValid(depthField, depthIndicator, depthError);
            }
        } else if (depthField) {
            clearFieldState(depthField, depthIndicator, depthError);
        }
        
        // 5. Year validation - current year or in the past
        const yearField = document.getElementById('artworkYear');
        const yearIndicator = document.getElementById('artworkYearIndicator');
        const yearError = document.getElementById('artworkYearError');
        
        if (yearField && yearField.value.trim()) {
            const year = parseInt(yearField.value);
            const currentYear = new Date().getFullYear();
            if (isNaN(year)) {
                setFieldError(yearField, yearIndicator, yearError, 'Year must be a valid number');
                errorMessages.push('Year must be a valid number');
                isValid = false;
            } else if (year < 1800) {
                setFieldError(yearField, yearIndicator, yearError, 'Year cannot be before 1800');
                errorMessages.push('Year cannot be before 1800');
                isValid = false;
            } else if (year > currentYear) {
                setFieldError(yearField, yearIndicator, yearError, `Year cannot be in the future (current year: ${currentYear})`);
                errorMessages.push(`Year cannot be in the future (current year: ${currentYear})`);
                isValid = false;
            } else {
                setFieldValid(yearField, yearIndicator, yearError);
            }
        } else if (yearField) {
            clearFieldState(yearField, yearIndicator, yearError);
        }
        
        // Description validation
        const descField = document.getElementById('artworkDescription');
        const descIndicator = document.getElementById('artworkDescriptionIndicator');
        const descError = document.getElementById('artworkDescriptionError');
        
        if (descField) {
            const description = descField.value.trim();
            if (!description) {
                setFieldError(descField, descIndicator, descError, 'Description is required');
                errorMessages.push('Description is required');
                isValid = false;
            } else if (description.length < 10) {
                setFieldError(descField, descIndicator, descError, 'Description should be at least 10 characters long');
                errorMessages.push('Description should be at least 10 characters long');
                isValid = false;
            } else {
                setFieldValid(descField, descIndicator, descError);
            }
        }
    } else {
        // Generic validation for other steps
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('error');
                isValid = false;
            } else {
                field.classList.remove('error');
            }
        });
        
        if (!isValid) {
            errorMessages.push('Please fill in all required fields');
        }
    }
    
    if (!isValid) {
        const errorMessage = errorMessages.length > 1 
            ? 'Please fix the following issues:<br>• ' + errorMessages.join('<br>• ')
            : errorMessages[0];
            
        Swal.fire({
            title: 'Validation Error',
            html: errorMessage,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423',
            toast: false,
            position: 'center'
        });
    }
    
    return isValid;
}

// Helper functions for field validation states
function setFieldError(field, indicator, errorElement, message) {
    field.classList.add('error');
    field.classList.remove('valid');
    
    if (indicator) {
        indicator.className = 'inputIndicator invalid';
    }
    
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.add('show');
    }
}

function setFieldValid(field, indicator, errorElement) {
    field.classList.remove('error');
    field.classList.add('valid');
    
    if (indicator) {
        indicator.className = 'inputIndicator valid';
    }
    
    if (errorElement) {
        errorElement.classList.remove('show');
        errorElement.textContent = '';
    }
}

function clearFieldState(field, indicator, errorElement) {
    field.classList.remove('error', 'valid');
    
    if (indicator) {
        indicator.className = 'inputIndicator';
    }
    
    if (errorElement) {
        errorElement.classList.remove('show');
        errorElement.textContent = '';
    }
}

function nextStep() {
    if (currentStep < totalSteps) {
        // Hide current step
        const currentStepElement = document.querySelector(`.formStep[data-step="${currentStep}"]`);
        if (currentStepElement) {
            currentStepElement.classList.remove('active');
        }
        
        // Update progress
        const currentProgressStep = document.querySelector(`.progressStep[data-step="${currentStep}"]`);
        if (currentProgressStep) {
            currentProgressStep.classList.remove('active');
            currentProgressStep.classList.add('completed');
        }
        
        currentStep++;
        
        // Show next step
        const nextStepElement = document.querySelector(`.formStep[data-step="${currentStep}"]`);
        if (nextStepElement) {
            nextStepElement.classList.add('active');
        }
        
        // Update progress
        const nextProgressStep = document.querySelector(`.progressStep[data-step="${currentStep}"]`);
        if (nextProgressStep) {
            nextProgressStep.classList.add('active');
        }
        
        updateStepNavigation();
        
        // Generate preview if we're on the last step
        if (currentStep === totalSteps) {
            generateArtworkPreview();
        }
    }
}

function previousStep() {
    if (currentStep > 1) {
        // Hide current step
        const currentStepElement = document.querySelector(`.formStep[data-step="${currentStep}"]`);
        if (currentStepElement) {
            currentStepElement.classList.remove('active');
        }
        
        // Update progress
        const currentProgressStep = document.querySelector(`.progressStep[data-step="${currentStep}"]`);
        if (currentProgressStep) {
            currentProgressStep.classList.remove('active');
        }
        
        currentStep--;
        
        // Show previous step
        const prevStepElement = document.querySelector(`.formStep[data-step="${currentStep}"]`);
        if (prevStepElement) {
            prevStepElement.classList.add('active');
        }
        
        // Update progress
        const prevProgressStep = document.querySelector(`.progressStep[data-step="${currentStep}"]`);
        if (prevProgressStep) {
            prevProgressStep.classList.add('active');
            prevProgressStep.classList.remove('completed');
        }
        
        updateStepNavigation();
    }
}

function updateStepNavigation() {
    const stepNum = document.getElementById('currentStepNum');
    const prevBtn = document.getElementById('prevStep');
    const nextBtn = document.getElementById('nextStep');
    const publishBtn = document.getElementById('publishBtn');
    
    if (stepNum) {
        stepNum.textContent = currentStep;
    }
    
    if (prevBtn) {
        prevBtn.style.display = currentStep > 1 ? 'block' : 'none';
    }
    
    if (nextBtn && publishBtn) {
        if (currentStep === totalSteps) {
            nextBtn.style.display = 'none';
            publishBtn.style.display = 'block';
        } else {
            nextBtn.style.display = 'block';
            publishBtn.style.display = 'none';
        }
    }
}

function calculateNetPrice(price) {
    const netPriceElement = document.getElementById('netPrice');
    if (netPriceElement) {
        const numPrice = parseFloat(price) || 0;
        const platformFee = numPrice * 0.05;
        const netPrice = numPrice - platformFee;
        netPriceElement.textContent = netPrice.toFixed(2);
    }
}

function initializeImageUpload() {
    // Initialize primary artwork image upload
    const artworkPrimaryUploadZone = document.getElementById('artworkPrimaryUploadZone');
    const artworkPrimaryFileInput = document.getElementById('artworkPrimaryImage');
    
    if (artworkPrimaryUploadZone && artworkPrimaryFileInput) {
        artworkPrimaryUploadZone.addEventListener('click', function() {
            artworkPrimaryFileInput.click();
        });
        
        artworkPrimaryUploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        artworkPrimaryUploadZone.addEventListener('dragleave', function() {
            this.classList.remove('dragover');
        });
        
        artworkPrimaryUploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            handleArtworkPrimaryImage(e.dataTransfer.files);
        });
        
        artworkPrimaryFileInput.addEventListener('change', function() {
            handleArtworkPrimaryImage(this.files);
        });
    }
    
    // Initialize additional artwork images upload
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('artworkImages');
    
    if (uploadZone && fileInput) {
        uploadZone.addEventListener('click', function() {
            fileInput.click();
        });
        
        uploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        uploadZone.addEventListener('dragleave', function() {
            this.classList.remove('dragover');
        });
        
        uploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });
        
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });
    }
}

function handleArtworkPrimaryImage(files) {
    if (files.length === 0) return;
    
    // Only take the first file for primary image
    const file = files[0];
    
    // Validate file type
    const validation = validateImageFile(file);
    if (!validation.isValid) {
        Swal.fire({
            title: 'Invalid File',
            text: validation.error,
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423'
        });
        return;
    }
    
    // Store the primary image
    artworkPrimaryImage = file;
    
    // Get the preview container
    const previewContainer = document.getElementById('artworkPrimaryImagePreview');
    
    // Clear previous preview
    previewContainer.innerHTML = '';
    
    // Create preview
    const reader = new FileReader();
    reader.onload = function(e) {
        const imagePreview = document.createElement('div');
        imagePreview.classList.add('artworkImagePreview');
        
        imagePreview.innerHTML = `
            <div class="imageContainer">
                <img src="${e.target.result}" alt="Primary Artwork Image" class="previewImage">
                <div class="imageOverlay">
                    <button type="button" class="removeImageBtn" onclick="removeArtworkPrimaryImage()" title="Remove Image">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                <div class="imageInfo">
                    ${Math.round(file.size / 1024)}KB
                </div>
                <div class="imageLabel">Primary Image</div>
            </div>
        `;
        
        previewContainer.appendChild(imagePreview);
    };
    reader.readAsDataURL(file);
}

function removeArtworkPrimaryImage() {
    // Clear the primary image
    artworkPrimaryImage = null;
    
    // Get the preview container and clear it
    const previewContainer = document.getElementById('artworkPrimaryImagePreview');
    previewContainer.innerHTML = '';
    
    // Reset the file input
    const fileInput = document.getElementById('artworkPrimaryImage');
    if (fileInput) {
        fileInput.value = '';
    }
}

function handleFiles(files) {
    const uploadedImages = document.getElementById('uploadedImages');
    if (!uploadedImages) return;
    
    // Check total number of images (limit to 9 additional images)
    const currentImageCount = uploadedImages.querySelectorAll('.artworkImagePreview').length;
    const totalAfterUpload = currentImageCount + files.length;
    
    if (totalAfterUpload > 9) {
        Swal.fire({
            title: 'Too Many Images',
            text: `You can upload maximum 9 additional images. Currently you have ${currentImageCount} images, trying to add ${files.length} more.`,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423'
        });
        return;
    }
    
    // Process files and add to artworkUploadedFiles array
    processArtworkFilesWithValidation(Array.from(files), uploadedImages);
}

async function processArtworkFilesWithValidation(files, uploadedImages) {
    // Show loading state for validation
    if (files.length > 0) {
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'uploadingMessage';
        loadingDiv.innerHTML = `
            <div class="uploadingContent">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Validating ${files.length} image${files.length > 1 ? 's' : ''}...</span>
            </div>
        `;
        uploadedImages.appendChild(loadingDiv);
    }

    const validFiles = [];
    const errors = [];
    
    for (const file of files) {
        // Basic validation first
        const basicValidation = validateImageFile(file);
        if (!basicValidation.isValid) {
            errors.push(`${file.name}: ${basicValidation.error}`);
            continue;
        }
        
        // Advanced dimension validation
        const dimensionValidation = await validateImageDimensions(file);
        if (!dimensionValidation.isValid) {
            errors.push(`${file.name}: ${dimensionValidation.error}`);
            continue;
        }
        
        validFiles.push({
            file: file,
            dimensions: {
                width: dimensionValidation.width,
                height: dimensionValidation.height
            }
        });
    }
    
    // Remove loading message
    const loadingMessage = uploadedImages.querySelector('.uploadingMessage');
    if (loadingMessage) {
        loadingMessage.remove();
    }
    
    // Show validation errors if any
    if (errors.length > 0) {
        Swal.fire({
            title: 'Image Validation Error',
            html: `The following files could not be uploaded:<br><br>• ${errors.join('<br>• ')}`,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423'
        });
        
        // If no valid files, return early
        if (validFiles.length === 0) return;
    }
    
    // Process valid files
    validFiles.forEach((fileData, index) => {
        const file = fileData.file;
        const dimensions = fileData.dimensions;
        
        // Add to uploaded files array
        artworkUploadedFiles.push(file);
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const imagePreview = document.createElement('div');
            imagePreview.className = 'artworkImagePreview';
            imagePreview.innerHTML = `
                <div class="imageContainer">
                    <img src="${e.target.result}" alt="Artwork Preview ${index + 1}" class="previewImage">
                    <div class="imageOverlay">
                        <button type="button" class="removeImageBtn" onclick="removeArtworkImage(this)" title="Remove Image">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <div class="imageInfo">
                        ${Math.round(file.size / 1024)}KB • ${dimensions.width}×${dimensions.height}px
                    </div>
                    <div class="imageLabel">Additional Image</div>
                </div>
            `;
            
            uploadedImages.appendChild(imagePreview);
        };
        reader.readAsDataURL(file);
    });
}

async function processFilesWithValidation(files, uploadedImages) {
    const validFiles = [];
    const errors = [];
    
    for (const file of files) {
        // Basic validation first
        const basicValidation = validateImageFile(file);
        if (!basicValidation.isValid) {
            errors.push(`${file.name}: ${basicValidation.error}`);
            continue;
        }
        
        // Advanced dimension validation
        const dimensionValidation = await validateImageDimensions(file);
        if (!dimensionValidation.isValid) {
            errors.push(`${file.name}: ${dimensionValidation.error}`);
            continue;
        }
        
        validFiles.push({
            file: file,
            dimensions: {
                width: dimensionValidation.width,
                height: dimensionValidation.height
            }
        });
    }
    
    // Remove loading message
    const loadingMessage = uploadedImages.querySelector('.uploadingMessage');
    if (loadingMessage) {
        loadingMessage.remove();
    }
    
    // Show validation errors if any
    if (errors.length > 0) {
        Swal.fire({
            title: 'Image Validation Error',
            html: `The following files could not be uploaded:<br><br>• ${errors.join('<br>• ')}`,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423'
        });
        
        // If no valid files, return early
        if (validFiles.length === 0) return;
    }
    
    // Process valid files
    validFiles.forEach((fileData, index) => {
        const file = fileData.file;
        const dimensions = fileData.dimensions;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const imagePreview = document.createElement('div');
            imagePreview.className = 'artworkImagePreview';
            imagePreview.innerHTML = `
                <div class="imageContainer">
                    <img src="${e.target.result}" alt="Artwork Preview ${index + 1}" class="previewImage">
                    <div class="imageOverlay">
                        <button type="button" class="removeImageBtn" onclick="removeImage(this)" title="Remove Image">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <div class="imageLabel">
                        Image ${uploadedImages.children.length + 1}
                    </div>
                    <div class="imageInfo">
                        ${(file.size / 1024 / 1024).toFixed(1)} MB<br>
                        ${dimensions.width}×${dimensions.height}px
                    </div>
                </div>
            `;
            uploadedImages.appendChild(imagePreview);
        };
        reader.readAsDataURL(file);
    });
}

// Function to check for duplicate artworks
async function checkForDuplicateArtwork(title, price, category, description) {
    try {
        // Get dimensions for additional comparison
        const width = document.getElementById('artworkWidth')?.value?.trim();
        const height = document.getElementById('artworkHeight')?.value?.trim();
        
        const response = await fetch('/API/checkDuplicateArtwork.php', {
            method: 'POST',
            credentials: 'include', // Include session cookies
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                title: title,
                price: parseFloat(price),
                category: category,
                description: description,
                width: width ? parseFloat(width) : null,
                height: height ? parseFloat(height) : null
            })
        });
        
        if (!response.ok) {
            return { canProceed: true, isDuplicate: false };
        }
        
        const data = await response.json();
        
        if (data.isDuplicate) {
            // Show simple duplicate detection message in SweetAlert
            const duplicateMessage = data.message || 'Artwork already exists';
            // Show confirmation dialog with simple message
            const confirmResult = await Swal.fire({
                title: 'Duplicate Artwork Detected!',
                text: duplicateMessage,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Continue Anyway',
                cancelButtonText: 'Edit Current Artwork',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6B4423',
                allowOutsideClick: false
            });
            
            if (confirmResult.isConfirmed) {
                // User wants to continue anyway - return with force flag
                return { 
                    canProceed: true, 
                    isDuplicate: true, 
                    forceDuplicate: true
                };
            } else {
                // User wants to edit the current artwork
                return {
                    canProceed: false,
                    isDuplicate: true
                };
            }
        }
        
        return { canProceed: true, isDuplicate: false };
        
    } catch (error) {
        // Show error message in SweetAlert
        await Swal.fire({
            title: 'Duplicate Check Failed',
            text: 'Unable to check for duplicates. You can still proceed with publishing your artwork.',
            icon: 'warning',
            confirmButtonText: 'Continue',
            confirmButtonColor: '#6B4423'
        });
        
        // If duplicate check fails, allow submission to proceed
        return { canProceed: true, isDuplicate: false };
    }
}

// Function to continue publishing after duplicate confirmation
function continuePublishingArtwork() {
    // Show loading Swal
    Swal.fire({
        title: 'Publishing Artwork...',
        text: 'Please wait while we save your masterpiece',
        icon: 'info',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Collect form data again (since we're calling this separately)
    const formData = new FormData();
    
    // Collect all form data
    const title = document.getElementById('artworkName')?.value?.trim();
    const price = document.getElementById('artworkPrice')?.value?.trim();
    const category = document.getElementById('artworkCategory')?.value?.trim();
    const description = document.getElementById('artworkDescription')?.value?.trim();
    const style = document.getElementById('artworkStyle')?.value?.trim();
    const medium = document.getElementById('artworkMedium')?.value?.trim();
    const width = document.getElementById('artworkWidth')?.value?.trim();
    const height = document.getElementById('artworkHeight')?.value?.trim();
    const depth = document.getElementById('artworkDepth')?.value?.trim();
    const year = document.getElementById('artworkYear')?.value?.trim();
    
    // Add data to FormData
    formData.append('title', title);
    formData.append('price', price);
    formData.append('category', category);
    formData.append('description', description);
    formData.append('force_duplicate', 'true'); // Flag to bypass duplicate check on server
    
    if (style) formData.append('style', style);
    if (medium) formData.append('material', medium);
    if (width) formData.append('width', width);
    if (height) formData.append('height', height);
    if (depth) formData.append('depth', depth);
    if (year) formData.append('year', year);
    
    // Add artwork images if uploaded
    const imageInput = document.getElementById('artworkImages');
    if (imageInput && imageInput.files && imageInput.files.length > 0) {
        // Add the first image as primary artwork image
        formData.append('artwork_image', imageInput.files[0]);
        
        // Add all images (including the first one) as additional images for artwork_photos table
        for (let i = 0; i < imageInput.files.length; i++) {
            formData.append('artwork_images[]', imageInput.files[i]);
        }
    }
    
    // Submit to API
    submitArtworkToAPI(formData);
}

// Separate function to handle the actual API submission
function submitArtworkToAPI(formData) {
    fetch('/API/addArtwork.php', {
        method: 'POST',
        credentials: 'include', // Include session cookies
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        // Handle duplicate detection (409 status) specifically
        if (response.status === 409) {
            return response.json().then(data => {
                // Close any loading dialogs
                Swal.close();
                
                // Show duplicate artwork message
                Swal.fire({
                    title: 'Artwork Already Exists!',
                    text: data.message || 'An artwork with similar details already exists in your collection.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6B4423'
                });
                return null; // Signal that we handled this case
            });
        }
        
        return response.text().then(text => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}, response: ${text}`);
            }
            
            try {
                return JSON.parse(text);
            } catch (jsonError) {
                throw new Error(`Response is not valid JSON: ${text}`);
            }
        });
    })
    .then(data => {
        if (data === null) {
            // This was a duplicate case, already handled above
            return;
        }
        
        if (data.success) {
            Swal.fire({
                title: 'Artwork Published Successfully!',
                text: 'Your artwork has been added to the gallery successfully!',
                icon: 'success',
                confirmButtonColor: '#6B4423',
                showCancelButton: true,
                cancelButtonText: 'Add Another Artwork',
                cancelButtonColor: '#8B7355',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    resetArtworkForm();
                }
            });
        } else {
            Swal.fire({
                title: 'Publication Failed!',
                html: `
                    <p>${data.message || 'Failed to publish artwork.'}</p>
                    ${data.errors ? `<br><strong>Errors:</strong><br>${data.errors.join('<br>')}` : ''}
                `,
                icon: 'error',
                confirmButtonText: 'Try Again',
                confirmButtonColor: '#6B4423'
            });
        }
    })
    .catch(error => {
        
        let errorMessage = 'An unexpected error occurred. Please try again.';
        
        if (error.message.includes('HTTP error! status: 400')) {
            errorMessage = 'Please check that all required fields are filled correctly.';
        } else if (error.message.includes('HTTP error')) {
            errorMessage = 'Server error occurred. Please check your connection and try again.';
        } else if (error.message.includes('JSON')) {
            errorMessage = 'Server returned invalid response. Please contact support if this persists.';
        } else if (error.message.includes('Failed to fetch')) {
            errorMessage = 'Network connection failed. Please check your internet connection.';
        }
        
        Swal.fire({
            title: 'Connection Error!',
            text: errorMessage,
            icon: 'error',
            confirmButtonText: 'Retry',
            confirmButtonColor: '#6B4423'
        });
    });
}

function validateImageFile(file) {
    // File type validation
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type.toLowerCase())) {
        return {
            isValid: false,
            error: 'Only JPEG, PNG, and WebP formats are allowed'
        };
    }
    
    // Keep only file type validation - removed file size restrictions
    return {
        isValid: true,
        error: null
    };
}

function validateImageDimensions(file) {
    return new Promise((resolve) => {
        const img = new Image();
        const reader = new FileReader();
        
        reader.onload = function(e) {
            img.onload = function() {
                const width = img.naturalWidth;
                const height = img.naturalHeight;
                
                // Validate minimum dimensions for auction images (300x300)
                const minWidth = 300;
                const minHeight = 300;
                
                if (width < minWidth || height < minHeight) {
                    resolve({
                        isValid: false,
                        error: `Image dimensions too small. Minimum required: ${minWidth}x${minHeight} pixels. Current: ${width}x${height} pixels.`,
                        width: width,
                        height: height
                    });
                    return;
                }
                
                resolve({
                    isValid: true,
                    error: null,
                    width: width,
                    height: height
                });
            };
            
            img.onerror = function() {
                resolve({
                    isValid: false,
                    error: 'Invalid or corrupted image file'
                });
            };
            
            img.src = e.target.result;
        };
        
        reader.onerror = function() {
            resolve({
                isValid: false,
                error: 'Error reading image file'
            });
        };
        
        reader.readAsDataURL(file);
    });
}

function removeImage(button) {
    const imagePreview = button.closest('.artworkImagePreview');
    if (imagePreview) {
        imagePreview.remove();
        
        // Update labels for remaining images
        const uploadedImages = document.getElementById('uploadedImages');
        if (uploadedImages) {
            const remainingImages = uploadedImages.querySelectorAll('.artworkImagePreview');
            remainingImages.forEach((preview, index) => {
                const label = preview.querySelector('.imageLabel');
                if (label) {
                    label.textContent = `Image ${index + 1}`;
                }
            });
        }
    }
}

function removeArtworkImage(button) {
    const imagePreview = button.closest('.artworkImagePreview');
    const index = Array.from(imagePreview.parentNode.children).indexOf(imagePreview);
    
    // Remove from uploaded files array
    artworkUploadedFiles.splice(index, 1);
    
    // Remove preview element
    imagePreview.remove();
    
    // Update labels for remaining images
    const uploadedImages = document.getElementById('uploadedImages');
    if (uploadedImages) {
        const remainingImages = uploadedImages.querySelectorAll('.artworkImagePreview');
        remainingImages.forEach((preview, index) => {
            const label = preview.querySelector('.imageLabel');
            if (label) {
                label.textContent = `Additional Image ${index + 1}`;
            }
        });
    }
}

function generateArtworkPreview() {
    const previewContainer = document.getElementById('artworkPreview');
    if (!previewContainer) return;
    
    const title = document.getElementById('artworkName').value;
    const price = document.getElementById('artworkPrice').value;
    const category = document.getElementById('artworkCategory').value;
    const description = document.getElementById('artworkDescription').value;
    
    previewContainer.innerHTML = `
        <div class="artworkPreviewCard">
            <h3>Artwork Preview</h3>
            <div class="previewContent">
                <h4>${title || 'Untitled Artwork'}</h4>
                <p class="previewPrice">EGP ${price || '0.00'}</p>
                <p class="previewCategory">${category || 'Category not selected'}</p>
                <p class="previewDescription">${description || 'No description provided'}</p>
            </div>
        </div>
    `;
}

async function publishArtwork() {
    // Collect form data first for validation
    const title = document.getElementById('artworkName')?.value?.trim();
    const price = document.getElementById('artworkPrice')?.value?.trim();
    const category = document.getElementById('artworkCategory')?.value?.trim();
    const description = document.getElementById('artworkDescription')?.value?.trim();
    
    // Validate required fields BEFORE showing loading
    if (!title || !price || !category || !description) {
        // Find which fields are missing
        const missingFields = [];
        if (!title) missingFields.push('Artwork Title');
        if (!price) missingFields.push('Price');
        if (!category) missingFields.push('Category');
        if (!description) missingFields.push('Description');
        
        Swal.fire({
            title: 'Missing Information!',
            html: `Please fill in the following required fields:<br><br><strong>${missingFields.join('<br>')}</strong>`,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423'
        });
        return;
    }
    
    // Validate that at least a primary image is uploaded
    if (!artworkPrimaryImage && artworkUploadedFiles.length === 0) {
        Swal.fire({
            title: 'No Images Uploaded!',
            text: 'Please upload at least a primary image of your artwork.',
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423'
        });
        return;
    }
    
    // Check for duplicate artwork before proceeding
    const duplicateCheck = await checkForDuplicateArtwork(title, price, category, description);
    if (!duplicateCheck.canProceed) {
        // Duplicate detected and user chose to edit - exit function
        return;
    }
    
    // Additional validation for price
    const numPrice = parseFloat(price);
    if (isNaN(numPrice)) {
        Swal.fire({
            title: 'Invalid Price!',
            text: 'Please enter a valid numeric price.',
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423'
        });
        return;
    }
    
    // If no duplicate detected, proceed with normal submission
    // Show loading Swal only after validation passes
    Swal.fire({
        title: 'Publishing Artwork...',
        text: 'Please wait while we save your masterpiece',
        icon: 'info',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Collect form data
    const formData = new FormData();
    
    // Basic information
    const style = document.getElementById('artworkStyle')?.value?.trim();
    const medium = document.getElementById('artworkMedium')?.value?.trim();
    
    // Detailed information
    const width = document.getElementById('artworkWidth')?.value?.trim();
    const height = document.getElementById('artworkHeight')?.value?.trim();
    const depth = document.getElementById('artworkDepth')?.value?.trim();
    const year = document.getElementById('artworkYear')?.value?.trim();
    
    // Add data to FormData
    formData.append('title', title);
    formData.append('price', price);
    formData.append('category', category);
    formData.append('description', description);
    
    // Add force_duplicate flag if user chose to continue with duplicate
    if (duplicateCheck.forceDuplicate) {
        formData.append('force_duplicate', 'true');
    }
    
    if (style) formData.append('style', style);
    if (medium) formData.append('material', medium);
    if (width) formData.append('width', width);
    if (height) formData.append('height', height);
    if (depth) formData.append('depth', depth);
    if (year) formData.append('year', year);
    
    // Add primary artwork image if uploaded
    if (artworkPrimaryImage) {
        formData.append('primary_image', artworkPrimaryImage, `primary_${artworkPrimaryImage.name}`);
    }
    
    // Add additional artwork images
    artworkUploadedFiles.forEach((file, index) => {
        formData.append('artwork_images[]', file, `artwork_image_${index + 1}.${file.name.split('.').pop()}`);
    });
    
    // Submit using the shared function
    submitArtworkToAPI(formData);
}

// Helper function to reset the artwork form
function resetArtworkForm() {
    // Temporarily disable validation by removing event listeners
    const fields = [
        'artworkName', 'artworkPrice', 'artworkCategory', 'artworkStyle',
        'artworkWidth', 'artworkHeight', 'artworkDepth', 'artworkYear', 'artworkDescription'
    ];
    
    // Store the current event listeners and remove them temporarily
    const tempEventListeners = [];
    fields.forEach(fieldId => {
        const fieldElement = document.getElementById(fieldId);
        if (fieldElement) {
            // Clone the element to remove all event listeners
            const newElement = fieldElement.cloneNode(true);
            fieldElement.parentNode.replaceChild(newElement, fieldElement);
            tempEventListeners.push({ oldElement: fieldElement, newElement: newElement });
        }
    });
    
    // Clear all input indicators and validation states FIRST
    document.querySelectorAll('.inputIndicator').forEach(indicator => {
        indicator.classList.remove('valid', 'error');
        indicator.innerHTML = '';
    });
    
    // Clear all error messages
    document.querySelectorAll('.errorMessage').forEach(errorElement => {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    });
    
    // Reset all input field classes
    document.querySelectorAll('#addArtworkForm input, #addArtworkForm select, #addArtworkForm textarea').forEach(field => {
        field.classList.remove('valid', 'error');
    });
    
    // Reset the form values
    document.getElementById('addArtworkForm').reset();
    
    // Reset navigation
    currentStep = 1;
    updateStepNavigation();
    
    // Clear uploaded images
    const uploadedImages = document.getElementById('uploadedImages');
    if (uploadedImages) {
        uploadedImages.innerHTML = '';
    }
    
    // Clear primary image preview
    const primaryImagePreview = document.getElementById('artworkPrimaryImagePreview');
    if (primaryImagePreview) {
        primaryImagePreview.innerHTML = '';
    }
    
    // Reset artwork image arrays
    artworkUploadedFiles = [];
    artworkPrimaryImage = null;
    
    // Show first step
    document.querySelectorAll('.formStep').forEach(step => {
        step.classList.remove('active');
    });
    document.querySelector('.formStep[data-step="1"]').classList.add('active');
    
    // Reset progress steps
    document.querySelectorAll('.progressStep').forEach(step => {
        step.classList.remove('active', 'completed');
    });
    document.querySelector('.progressStep[data-step="1"]').classList.add('active');
    
    // Re-add the real-time validation after a short delay
    setTimeout(() => {
        addRealTimeValidation();
    }, 100);
}

// ==========================================
// AUCTION FUNCTIONALITY
// ==========================================

// Flag to track if auction navigation is in progress
let auctionNavigationInProgress = false;

function initializeAuction() {
    const auctionForm = document.getElementById('addAuctionForm');
    if (auctionForm) {
        auctionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            startAuction();
        });
    }
    
    // Add launch button click handler
    const launchBtn = document.getElementById('launchAuctionBtn');
    if (launchBtn) {
        launchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            startAuction();
        });
    }
    
    // Add real-time validation for auction fields
    addAuctionRealTimeValidation();
    
    // Initialize auction image upload functionality
    initializeAuctionImageUpload();
    
    // Character counter for auction description
    const auctionDescription = document.getElementById('auctionDescription');
    const auctionDescCharCount = document.getElementById('auctionDescCharCount');
    
    if (auctionDescription && auctionDescCharCount) {
        auctionDescription.addEventListener('input', function() {
            const currentLength = this.value.length;
            auctionDescCharCount.textContent = currentLength;
            
            // Change color based on character count
            if (currentLength < 10) {
                auctionDescCharCount.style.color = '#ef4444'; // Red for too few
            } else if (currentLength > 900) {
                auctionDescCharCount.style.color = '#f59e0b'; // Orange for approaching limit
            } else {
                auctionDescCharCount.style.color = '#22c55e'; // Green for good
            }
        });
    }
    
    // Initialize auction step navigation
    initializeAuctionStepNavigation();
    
    // Only initialize to step 1 if no step is currently active and we're not in the middle of navigation
    setTimeout(() => {
        // Don't interfere if navigation is in progress
        if (auctionNavigationInProgress) {
            console.log('Auction navigation in progress, skipping initialization');
            return;
        }
        
        const currentStep = getCurrentAuctionStep();
        console.log('InitializeAuction setTimeout - current step:', currentStep);
        
        // Only reset to step 1 if truly no active step exists, and we're not navigating
        const hasActiveStep = document.querySelector('#addAuctionForm .formStep.active[data-step]');
        if (!hasActiveStep) {
            console.log('No active step found, initializing to step 1');
            showAuctionStep(1);
            updateAuctionStepNavigation();
        } else {
            console.log('Active step found, updating navigation for current step:', currentStep);
            updateAuctionStepNavigation();
        }
    }, 100);
}

function addAuctionRealTimeValidation() {
    // Get all auction form fields with indicators
    const auctionFields = [
        { field: 'auctionArtworkName', indicator: 'auctionArtworkNameIndicator', error: 'auctionArtworkNameError' },
        { field: 'initialBid', indicator: 'initialBidIndicator', error: 'initialBidError' },
        { field: 'auctionStyle', indicator: 'auctionStyleIndicator', error: 'auctionStyleError' },
        { field: 'auctionWidth', indicator: 'auctionWidthIndicator', error: 'auctionWidthError' },
        { field: 'auctionHeight', indicator: 'auctionHeightIndicator', error: 'auctionHeightError' },
        { field: 'auctionDepth', indicator: 'auctionDepthIndicator', error: 'auctionDepthError' },
        { field: 'auctionYear', indicator: 'auctionYearIndicator', error: 'auctionYearError' },
        { field: 'auctionStartDate', indicator: 'auctionStartDateIndicator', error: 'auctionStartDateError' },
        { field: 'auctionEndDate', indicator: 'auctionEndDateIndicator', error: 'auctionEndDateError' },
        { field: 'auctionDescription', indicator: 'auctionDescriptionIndicator', error: 'auctionDescriptionError' }
    ];

    auctionFields.forEach(({ field, indicator, error }) => {
        const fieldElement = document.getElementById(field);
        const indicatorElement = document.getElementById(indicator);
        const errorElement = document.getElementById(error);

        if (fieldElement) {
            // Add event listeners for real-time validation
            fieldElement.addEventListener('input', function() {
                validateAuctionField(fieldElement, indicatorElement, errorElement);
            });
            
            fieldElement.addEventListener('blur', function() {
                validateAuctionField(fieldElement, indicatorElement, errorElement);
            });
            
            fieldElement.addEventListener('change', function() {
                validateAuctionField(fieldElement, indicatorElement, errorElement);
            });
        }
    });
}

function validateAuctionField(field, indicator, errorElement) {
    const fieldId = field.id;
    
    switch (fieldId) {
        case 'auctionArtworkName':
            const title = field.value.trim();
            if (!title) {
                clearFieldState(field, indicator, errorElement);
            } else if (/^\d+$/.test(title)) {
                setFieldError(field, indicator, errorElement, 'Title should contain words, not just numbers');
            } else if (title.length < 3) {
                setFieldError(field, indicator, errorElement, 'Title should be at least 3 characters long');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'initialBid':
            const bid = parseFloat(field.value);
            if (!field.value.trim()) {
                clearFieldState(field, indicator, errorElement);
            } else if (isNaN(bid) || bid <= 0) {
                setFieldError(field, indicator, errorElement, 'Starting bid must be a positive number');
            } else if (bid > 5000000) {
                setFieldError(field, indicator, errorElement, 'Starting bid cannot exceed 5,000,000 EGP');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'auctionStyle':
            if (!field.value) {
                clearFieldState(field, indicator, errorElement);
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'auctionWidth':
            const width = parseFloat(field.value);
            if (!field.value.trim()) {
                clearFieldState(field, indicator, errorElement);
            } else if (isNaN(width) || width <= 0) {
                setFieldError(field, indicator, errorElement, 'Width must be a positive number');
            } else if (width < 1 || width > 1000) {
                setFieldError(field, indicator, errorElement, 'Width should be between 1cm and 1000cm');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'auctionHeight':
            const height = parseFloat(field.value);
            if (!field.value.trim()) {
                clearFieldState(field, indicator, errorElement);
            } else if (isNaN(height) || height <= 0) {
                setFieldError(field, indicator, errorElement, 'Height must be a positive number');
            } else if (height < 1 || height > 1000) {
                setFieldError(field, indicator, errorElement, 'Height should be between 1cm and 1000cm');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'auctionDepth':
            if (!field.value.trim()) {
                clearFieldState(field, indicator, errorElement);
                return; // Optional field
            }
            const depth = parseFloat(field.value);
            if (isNaN(depth) || depth < 0) {
                setFieldError(field, indicator, errorElement, 'Depth must be a positive number or empty');
            } else if (depth > 500) {
                setFieldError(field, indicator, errorElement, 'Depth should not exceed 500cm');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'auctionYear':
            if (!field.value.trim()) {
                clearFieldState(field, indicator, errorElement);
                return; // Optional field
            }
            const year = parseInt(field.value);
            const currentYear = new Date().getFullYear();
            if (isNaN(year)) {
                setFieldError(field, indicator, errorElement, 'Year must be a valid number');
            } else if (year < 1800) {
                setFieldError(field, indicator, errorElement, 'Year cannot be before 1800');
            } else if (year > currentYear) {
                setFieldError(field, indicator, errorElement, `Year cannot be in the future (current year: ${currentYear})`);
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'auctionStartDate':
            if (!field.value) {
                clearFieldState(field, indicator, errorElement);
            } else {
                const startDate = new Date(field.value);
                const now = new Date();
                if (startDate <= now) {
                    setFieldError(field, indicator, errorElement, 'Start date must be in the future');
                } else {
                    setFieldValid(field, indicator, errorElement);
                    // Also validate end date if it exists
                    const endDateField = document.getElementById('auctionEndDate');
                    if (endDateField && endDateField.value) {
                        validateAuctionField(endDateField, document.getElementById('auctionEndDateIndicator'), document.getElementById('auctionEndDateError'));
                    }
                }
            }
            break;
            
        case 'auctionEndDate':
            if (!field.value) {
                clearFieldState(field, indicator, errorElement);
            } else {
                const endDate = new Date(field.value);
                const now = new Date();
                const startDateField = document.getElementById('auctionStartDate');
                
                if (endDate <= now) {
                    setFieldError(field, indicator, errorElement, 'End date must be in the future');
                } else if (startDateField && startDateField.value) {
                    const startDate = new Date(startDateField.value);
                    if (endDate <= startDate) {
                        setFieldError(field, indicator, errorElement, 'End date must be after start date');
                    } else {
                        setFieldValid(field, indicator, errorElement);
                    }
                } else {
                    setFieldValid(field, indicator, errorElement);
                }
            }
            break;
            
        case 'auctionDescription':
            const description = field.value.trim();
            if (!description) {
                clearFieldState(field, indicator, errorElement);
            } else if (description.length < 10) {
                setFieldError(field, indicator, errorElement, 'Description should be at least 10 characters long');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
    }
}

function initializeAuctionStepNavigation() {
    const nextBtn = document.getElementById('auctionNextStep');
    const prevBtn = document.getElementById('auctionPrevStep');
    const launchBtn = document.getElementById('launchAuctionBtn');
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            // Add validation logic here for auction steps
            nextAuctionStep();
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            prevAuctionStep();
        });
    }
}

function nextAuctionStep() {
    // Validate current step before proceeding
    if (!validateCurrentAuctionStep()) {
        return; // Don't proceed if validation fails
    }
    
    // Set navigation flag
    auctionNavigationInProgress = true;
    
    // Get current auction step
    const currentAuctionStep = getCurrentAuctionStep();
    console.log('Next auction step: from step', currentAuctionStep);
    
    if (currentAuctionStep === 1) {
        // Move from step 1 (Auction Info) to step 2 (Images)
        showAuctionStep(2);
        updateAuctionStepNavigation();
        console.log('Moved to step 2');
    } else if (currentAuctionStep === 2) {
        // Move from step 2 (Images) to step 3 (Preview)
        showAuctionStep(3);
        // Generate preview when entering step 3
        setTimeout(generateAuctionPreview, 100);
        updateAuctionStepNavigation();
        console.log('Moved to step 3 (Preview)');
    }
    
    // Clear navigation flag after a short delay
    setTimeout(() => {
        auctionNavigationInProgress = false;
    }, 300);
}

function validateCurrentAuctionStep() {
    const currentStep = getCurrentAuctionStep();
    let isValid = true;
    let errorMessages = [];
    
    if (currentStep === 1) {
        // Step 1: Auction Information validation
        // 1. Artwork Title validation
        const titleField = document.getElementById('auctionArtworkName');
        const titleIndicator = document.getElementById('auctionArtworkNameIndicator');
        const titleError = document.getElementById('auctionArtworkNameError');
        
        if (titleField) {
            const title = titleField.value.trim();
            if (!title) {
                setFieldError(titleField, titleIndicator, titleError, 'Artwork title is required');
                errorMessages.push('Artwork title is required');
                isValid = false;
            } else if (/^\d+$/.test(title)) {
                setFieldError(titleField, titleIndicator, titleError, 'Artwork title should contain words, not just numbers');
                errorMessages.push('Artwork title should contain words, not just numbers');
                isValid = false;
            } else if (title.length < 3) {
                setFieldError(titleField, titleIndicator, titleError, 'Artwork title should be at least 3 characters long');
                errorMessages.push('Artwork title should be at least 3 characters long');
                isValid = false;
            } else {
                setFieldValid(titleField, titleIndicator, titleError);
            }
        }
        
        // 2. Starting Bid validation
        const bidField = document.getElementById('initialBid');
        const bidIndicator = document.getElementById('initialBidIndicator');
        const bidError = document.getElementById('initialBidError');
        
        if (bidField) {
            const bid = parseFloat(bidField.value);
            if (!bidField.value.trim()) {
                setFieldError(bidField, bidIndicator, bidError, 'Starting bid is required');
                errorMessages.push('Starting bid is required');
                isValid = false;
            } else if (isNaN(bid) || bid <= 0) {
                setFieldError(bidField, bidIndicator, bidError, 'Starting bid must be a positive number');
                errorMessages.push('Starting bid must be a positive number');
                isValid = false;
            } else if (bid > 5000000) {
                setFieldError(bidField, bidIndicator, bidError, 'Starting bid cannot exceed 5,000,000 EGP');
                errorMessages.push('Starting bid cannot exceed 5,000,000 EGP');
                isValid = false;
            } else {
                setFieldValid(bidField, bidIndicator, bidError);
            }
        }
        
        // 3. Art Style validation
        const styleField = document.getElementById('auctionStyle');
        const styleIndicator = document.getElementById('auctionStyleIndicator');
        const styleError = document.getElementById('auctionStyleError');
        
        if (styleField) {
            if (!styleField.value) {
                setFieldError(styleField, styleIndicator, styleError, 'Please select an art style');
                errorMessages.push('Please select an art style');
                isValid = false;
            } else {
                setFieldValid(styleField, styleIndicator, styleError);
            }
        }
        
        // 4. Width validation
        const widthField = document.getElementById('auctionWidth');
        const widthIndicator = document.getElementById('auctionWidthIndicator');
        const widthError = document.getElementById('auctionWidthError');
        
        if (widthField) {
            const width = parseFloat(widthField.value);
            if (!widthField.value.trim()) {
                setFieldError(widthField, widthIndicator, widthError, 'Width is required');
                errorMessages.push('Width is required');
                isValid = false;
            } else if (isNaN(width) || width <= 0) {
                setFieldError(widthField, widthIndicator, widthError, 'Width must be a positive number');
                errorMessages.push('Width must be a positive number');
                isValid = false;
            } else if (width < 1 || width > 1000) {
                setFieldError(widthField, widthIndicator, widthError, 'Width should be between 1cm and 1000cm');
                errorMessages.push('Width should be between 1cm and 1000cm');
                isValid = false;
            } else {
                setFieldValid(widthField, widthIndicator, widthError);
            }
        }
        
        // 5. Height validation
        const heightField = document.getElementById('auctionHeight');
        const heightIndicator = document.getElementById('auctionHeightIndicator');
        const heightError = document.getElementById('auctionHeightError');
        
        if (heightField) {
            const height = parseFloat(heightField.value);
            if (!heightField.value.trim()) {
                setFieldError(heightField, heightIndicator, heightError, 'Height is required');
                errorMessages.push('Height is required');
                isValid = false;
            } else if (isNaN(height) || height <= 0) {
                setFieldError(heightField, heightIndicator, heightError, 'Height must be a positive number');
                errorMessages.push('Height must be a positive number');
                isValid = false;
            } else if (height < 1 || height > 1000) {
                setFieldError(heightField, heightIndicator, heightError, 'Height should be between 1cm and 1000cm');
                errorMessages.push('Height should be between 1cm and 1000cm');
                isValid = false;
            } else {
                setFieldValid(heightField, heightIndicator, heightError);
            }
        }
        
        // 6. Start Date validation
        const startDateField = document.getElementById('auctionStartDate');
        const startDateIndicator = document.getElementById('auctionStartDateIndicator');
        const startDateError = document.getElementById('auctionStartDateError');
        
        if (startDateField) {
            if (!startDateField.value) {
                setFieldError(startDateField, startDateIndicator, startDateError, 'Auction start date is required');
                errorMessages.push('Auction start date is required');
                isValid = false;
            } else {
                const startDate = new Date(startDateField.value);
                const now = new Date();
                if (startDate <= now) {
                    setFieldError(startDateField, startDateIndicator, startDateError, 'Start date must be in the future');
                    errorMessages.push('Start date must be in the future');
                    isValid = false;
                } else {
                    setFieldValid(startDateField, startDateIndicator, startDateError);
                }
            }
        }
        
        // 7. End Date validation
        const endDateField = document.getElementById('auctionEndDate');
        const endDateIndicator = document.getElementById('auctionEndDateIndicator');
        const endDateError = document.getElementById('auctionEndDateError');
        
        if (endDateField) {
            if (!endDateField.value) {
                setFieldError(endDateField, endDateIndicator, endDateError, 'Auction end date is required');
                errorMessages.push('Auction end date is required');
                isValid = false;
            } else {
                const endDate = new Date(endDateField.value);
                const startDate = new Date(startDateField.value);
                if (endDate <= startDate) {
                    setFieldError(endDateField, endDateIndicator, endDateError, 'End date must be after start date');
                    errorMessages.push('End date must be after start date');
                    isValid = false;
                } else {
                    setFieldValid(endDateField, endDateIndicator, endDateError);
                }
            }
        }
        
        // 8. Description validation
        const descriptionField = document.getElementById('auctionDescription');
        const descriptionIndicator = document.getElementById('auctionDescriptionIndicator');
        const descriptionError = document.getElementById('auctionDescriptionError');
        
        if (descriptionField) {
            const description = descriptionField.value.trim();
            if (!description) {
                setFieldError(descriptionField, descriptionIndicator, descriptionError, 'Description is required');
                errorMessages.push('Description is required');
                isValid = false;
            } else if (description.length < 10) {
                setFieldError(descriptionField, descriptionIndicator, descriptionError, 'Description should be at least 10 characters long');
                errorMessages.push('Description should be at least 10 characters long');
                isValid = false;
            } else {
                setFieldValid(descriptionField, descriptionIndicator, descriptionError);
            }
        }
        
    } else if (currentStep === 2) {
        // Step 2: Image validation
        const uploadedImages = document.getElementById('auctionUploadedImages');
        const imageCount = uploadedImages ? uploadedImages.querySelectorAll('.auctionImagePreview').length : 0;
        
        if (imageCount === 0) {
            errorMessages.push('At least one image is required for auction');
            isValid = false;
        } else if (imageCount < 3) {
            errorMessages.push('At least 3 images are recommended for better auction results');
            // Note: This is a warning, not blocking validation
            // You can decide whether to make this blocking or not
        }
    }
    
    // Show SweetAlert if there are validation errors
    if (!isValid) {
        Swal.fire({
            title: 'Validation Error!',
            html: `Please fix the following issues:<br><br><ul style="text-align: left; padding-left: 20px;">${errorMessages.map(msg => `<li>${msg}</li>`).join('')}</ul>`,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423',
            customClass: {
                popup: 'validation-popup',
                htmlContainer: 'validation-html'
            }
        });
    }
    
    return isValid;
}

// Debugging functions (can be called from browser console)
window.testAuctionNavigation = function() {
    console.log('Testing auction navigation...');
    
    const currentStep = getCurrentAuctionStep();
    const nextBtn = document.getElementById('auctionNextStep');
    const launchBtn = document.getElementById('launchAuctionBtn');
    
    console.log('Current step:', currentStep);
    console.log('Next button:', nextBtn);
    console.log('Launch button:', launchBtn);
    
    if (nextBtn) console.log('Next button display:', nextBtn.style.display);
    if (launchBtn) console.log('Launch button display:', launchBtn.style.display);
    
    // Force navigation to step 3
    console.log('Forcing navigation to step 3...');
    showAuctionStep(3);
    updateAuctionStepNavigation();
    
    console.log('Forced to step 3, checking again...');
    setTimeout(() => {
        const newStep = getCurrentAuctionStep();
        console.log('New step after timeout:', newStep);
        if (nextBtn) console.log('Next button display after update:', nextBtn.style.display);
        if (launchBtn) console.log('Launch button display after update:', launchBtn.style.display);
    }, 200);
};

// Add function to manually go to any step
window.goToAuctionStep = function(step) {
    console.log('Manually going to auction step:', step);
    showAuctionStep(step);
    updateAuctionStepNavigation();
};

// Add function to check auction form state
window.debugAuctionForm = function() {
    console.log('=== Auction Form Debug ===');
    console.log('Current step:', getCurrentAuctionStep());
    
    // Check all form steps
    const allSteps = document.querySelectorAll('#addAuctionForm .formStep');
    console.log('All form steps found:', allSteps.length);
    allSteps.forEach((step, index) => {
        const stepNum = step.getAttribute('data-step');
        const isActive = step.classList.contains('active');
        const isVisible = getComputedStyle(step).display !== 'none';
        console.log(`Step ${index}: data-step="${stepNum}", active=${isActive}, visible=${isVisible}, element:`, step);
    });
    
    // Check if auction form exists
    const auctionForm = document.getElementById('addAuctionForm');
    console.log('Auction form exists:', !!auctionForm);
    if (auctionForm) {
        console.log('Auction form innerHTML length:', auctionForm.innerHTML.length);
    }
    
    // Try to find step 3 with different selectors
    const step3_v1 = document.querySelector('#addAuctionForm .formStep[data-step="3"]');
    const step3_v2 = document.querySelector('#addAuctionForm [data-step="3"]');
    const step3_v3 = document.querySelector('.formStep[data-step="3"]');
    const step3_v4 = document.querySelector('[data-step="3"]');
    
    console.log('Step 3 selectors:');
    console.log('  #addAuctionForm .formStep[data-step="3"]:', step3_v1);
    console.log('  #addAuctionForm [data-step="3"]:', step3_v2);
    console.log('  .formStep[data-step="3"]:', step3_v3);
    console.log('  [data-step="3"]:', step3_v4);
    
    const nextBtn = document.getElementById('auctionNextStep');
    const launchBtn = document.getElementById('launchAuctionBtn');
    console.log('Next button exists:', !!nextBtn);
    console.log('Launch button exists:', !!launchBtn);
    if (nextBtn) console.log('Next button display:', getComputedStyle(nextBtn).display);
    if (launchBtn) console.log('Launch button display:', getComputedStyle(launchBtn).display);
};

function prevAuctionStep() {
    // Set navigation flag
    auctionNavigationInProgress = true;
    
    const currentAuctionStep = getCurrentAuctionStep();
    
    if (currentAuctionStep === 2) {
        // Move from step 2 (Images) back to step 1 (Auction Info)
        showAuctionStep(1);
        updateAuctionStepNavigation();
    } else if (currentAuctionStep === 3) {
        // Move from step 3 (Preview) back to step 2 (Images)
        showAuctionStep(2);
        updateAuctionStepNavigation();
    }
    
    // Clear navigation flag after a short delay
    setTimeout(() => {
        auctionNavigationInProgress = false;
    }, 300);
}

function getCurrentAuctionStep() {
    // First try to find step within the form
    let activeStep = document.querySelector('#addAuctionForm .formStep.active[data-step]');
    
    // If not found in form, look in the broader auction section
    if (!activeStep) {
        activeStep = document.querySelector('#auction-section .formStep.active[data-step]');
    }
    
    // If still not found, try any active formStep with data-step
    if (!activeStep) {
        activeStep = document.querySelector('.formStep.active[data-step]');
    }
    
    const stepNumber = activeStep ? parseInt(activeStep.getAttribute('data-step')) : 1;
    console.log('getCurrentAuctionStep - activeStep element:', activeStep);
    console.log('getCurrentAuctionStep - returning step:', stepNumber);
    return stepNumber;
}

function showAuctionStep(stepNumber) {
    console.log('showAuctionStep called with step:', stepNumber);
    
    // Debug: Check what elements exist in different scopes
    const formSteps = document.querySelectorAll('#addAuctionForm .formStep');
    const sectionSteps = document.querySelectorAll('#auction-section .formStep');
    const allSteps = document.querySelectorAll('.formStep');
    
    console.log('Form steps found:', formSteps.length);
    console.log('Section steps found:', sectionSteps.length);
    console.log('All steps found:', allSteps.length);
    
    // Use the broadest scope that contains all steps
    const stepsToManage = sectionSteps.length >= 3 ? sectionSteps : (formSteps.length >= 2 ? formSteps : allSteps);
    
    stepsToManage.forEach((step, index) => {
        const dataStep = step.getAttribute('data-step');
        console.log(`Step ${index}: data-step="${dataStep}", classes:`, step.className);
    });
    
    // If we don't find all 3 steps, there might be a timing issue
    if (stepsToManage.length < 3) {
        console.warn('Expected 3 steps but only found', stepsToManage.length);
        // Try to find the form and log its contents
        const auctionForm = document.getElementById('addAuctionForm');
        if (auctionForm) {
            console.log('Form HTML snippet:', auctionForm.innerHTML.substring(0, 500));
        }
    }
    
    // Hide all auction form steps (use broadest scope)
    document.querySelectorAll('.formStep').forEach(step => {
        step.classList.remove('active');
    });
    
    // Try multiple selectors to find the target step
    let targetStep = document.querySelector(`#addAuctionForm .formStep[data-step="${stepNumber}"]`);
    
    if (!targetStep) {
        // Try in auction section
        targetStep = document.querySelector(`#auction-section .formStep[data-step="${stepNumber}"]`);
        console.log('Auction section selector result:', targetStep);
    }
    
    if (!targetStep) {
        // Try without container restrictions
        targetStep = document.querySelector(`.formStep[data-step="${stepNumber}"]`);
        console.log('Broad selector result:', targetStep);
    }
    
    console.log('Looking for step:', stepNumber);
    console.log('Target step found:', targetStep);
    
    if (targetStep) {
        targetStep.classList.add('active');
        console.log('Activated step', stepNumber);
    } else {
        console.error('Could not find step element for step', stepNumber);
        
        // Last resort: try to create step 3 if it's missing
        if (stepNumber === 3) {
            console.log('Attempting to find/create step 3...');
            const auctionForm = document.getElementById('addAuctionForm');
            if (auctionForm) {
                // Check if step 3 content exists somewhere
                const step3Content = auctionForm.querySelector('[id*="auctionPreview"]');
                if (step3Content) {
                    console.log('Found step 3 content, but missing wrapper. Creating wrapper...');
                    const step3Wrapper = document.createElement('div');
                    step3Wrapper.className = 'formStep active';
                    step3Wrapper.setAttribute('data-step', '3');
                    
                    // If the preview is not wrapped properly, wrap it
                    if (!step3Content.closest('.formStep[data-step="3"]')) {
                        const stepHeader = document.createElement('div');
                        stepHeader.className = 'stepHeader';
                        stepHeader.innerHTML = `
                            <h2><i class="fas fa-eye"></i> Preview & Launch</h2>
                            <p>Review your auction listing before launching</p>
                        `;
                        
                        step3Wrapper.appendChild(stepHeader);
                        step3Content.parentNode.insertBefore(step3Wrapper, step3Content);
                        step3Wrapper.appendChild(step3Content);
                        
                        console.log('Created step 3 wrapper successfully');
                        targetStep = step3Wrapper;
                    }
                }
            }
        }
    }
    
    // Update progress indicators
    const allProgressSteps = document.querySelectorAll('#auction-section .progressStep');
    allProgressSteps.forEach(progressStep => {
        progressStep.classList.remove('active');
        const stepNum = parseInt(progressStep.getAttribute('data-step'));
        if (stepNum <= stepNumber) {
            progressStep.classList.add('completed');
        } else {
            progressStep.classList.remove('completed');
        }
    });
    
    // Set current step as active
    const currentProgressStep = document.querySelector(`#auction-section .progressStep[data-step="${stepNumber}"]`);
    if (currentProgressStep) {
        currentProgressStep.classList.add('active');
    }
    
    // Generate preview if we're showing step 3
    if (stepNumber === 3) {
        generateAuctionPreview();
        console.log('Generated preview for step 3');
    }
}

function updateAuctionStepNavigation() {
    const currentStep = getCurrentAuctionStep();
    const prevBtn = document.getElementById('auctionPrevStep');
    const nextBtn = document.getElementById('auctionNextStep');
    const launchBtn = document.getElementById('launchAuctionBtn');
    const stepNumDisplay = document.getElementById('auctionCurrentStepNum');
    
    console.log('Auction step navigation update:', {
        currentStep,
        prevBtn: !!prevBtn,
        nextBtn: !!nextBtn,
        launchBtn: !!launchBtn
    });
    
    // Update step number display
    if (stepNumDisplay) {
        stepNumDisplay.textContent = currentStep;
    }
    
    // Show/hide previous button
    if (prevBtn) {
        prevBtn.style.display = currentStep > 1 ? 'inline-flex' : 'none';
    }
    
    // Show/hide next/publish buttons
    if (nextBtn && launchBtn) {
        if (currentStep === 3) {
            // On final step (Preview), show publish button instead of next
            nextBtn.style.display = 'none';
            launchBtn.style.display = 'inline-flex';
            launchBtn.innerHTML = '<i class="fas fa-rocket"></i> Publish Auction';
            console.log('Showing publish button on step 3');
        } else {
            // On other steps, show next button
            nextBtn.style.display = 'inline-flex';
            launchBtn.style.display = 'none';
            console.log('Showing next button on step', currentStep);
        }
    } else {
        console.error('Missing buttons:', { nextBtn: !!nextBtn, launchBtn: !!launchBtn });
    }
}

function initializeAuctionImageUpload() {
    // Initialize primary auction image upload
    const auctionPrimaryUploadZone = document.getElementById('auctionPrimaryUploadZone');
    const auctionPrimaryFileInput = document.getElementById('auctionPrimaryImage');
    
    if (auctionPrimaryUploadZone && auctionPrimaryFileInput) {
        auctionPrimaryUploadZone.addEventListener('click', function() {
            auctionPrimaryFileInput.click();
        });
        
        auctionPrimaryUploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        auctionPrimaryUploadZone.addEventListener('dragleave', function() {
            this.classList.remove('dragover');
        });
        
        auctionPrimaryUploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            handleAuctionPrimaryImage(e.dataTransfer.files);
        });
        
        auctionPrimaryFileInput.addEventListener('change', function() {
            handleAuctionPrimaryImage(this.files);
        });
    }
    
    // Initialize additional auction images upload
    const uploadZone = document.getElementById('auctionUploadZone');
    const fileInput = document.getElementById('auctionImages');
    
    if (uploadZone && fileInput) {
        uploadZone.addEventListener('click', function() {
            fileInput.click();
        });
        
        uploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        uploadZone.addEventListener('dragleave', function() {
            this.classList.remove('dragover');
        });
        
        uploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            handleAuctionFiles(e.dataTransfer.files);
        });
        
        fileInput.addEventListener('change', function() {
            handleAuctionFiles(this.files);
        });
    }
}

async function handleAuctionPrimaryImage(files) {
    if (files.length === 0) return;
    
    // Only take the first file for primary image
    const file = files[0];
    
    // Validate file type only
    const validation = validateImageFile(file);
    if (!validation.isValid) {
        Swal.fire({
            title: 'Invalid File',
            text: validation.message,
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423'
        });
        return;
    }
    
    // Store the primary image
    auctionPrimaryImage = file;
    
    // Get the preview container
    const previewContainer = document.getElementById('auctionPrimaryImagePreview');
    
    // Clear previous preview
    previewContainer.innerHTML = '';
    
    // Create preview
    const reader = new FileReader();
    reader.onload = function(e) {
        const imagePreview = document.createElement('div');
        imagePreview.classList.add('artworkImagePreview');
        
        imagePreview.innerHTML = `
            <div class="imageContainer">
                <img src="${e.target.result}" alt="Primary Auction Image" class="previewImage">
                <div class="imageOverlay">
                    <button type="button" class="removeImageBtn" onclick="removeAuctionPrimaryImage()" title="Remove Image">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                <div class="imageInfo">
                    ${Math.round(file.size / 1024)}KB
                </div>
                <div class="imageLabel">Primary Image</div>
            </div>
        `;
        
        previewContainer.appendChild(imagePreview);
    };
    reader.readAsDataURL(file);
}

function removeAuctionPrimaryImage() {
    // Clear the primary image
    auctionPrimaryImage = null;
    
    // Get the preview container and clear it
    const previewContainer = document.getElementById('auctionPrimaryImagePreview');
    previewContainer.innerHTML = '';
    
    // Reset the file input
    const fileInput = document.getElementById('auctionPrimaryImage');
    if (fileInput) {
        fileInput.value = '';
    }
}

// Store uploaded auction files for later submission
let auctionUploadedFiles = [];
let auctionPrimaryImage = null; // Store primary auction image

// Store uploaded artwork files for later submission
let artworkUploadedFiles = [];
let artworkPrimaryImage = null; // Store primary artwork image

function handleAuctionFiles(files) {
    const uploadedImages = document.getElementById('auctionUploadedImages');
    if (!uploadedImages) return;
    
    // Check total number of images (limit to 10 for auctions)
    const currentImageCount = uploadedImages.querySelectorAll('.auctionImagePreview').length;
    const totalAfterUpload = currentImageCount + files.length;
    
    if (totalAfterUpload > 10) {
        Swal.fire({
            title: 'Too Many Images',
            text: `You can upload maximum 10 images for auctions. Currently you have ${currentImageCount} images, trying to add ${files.length} more.`,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423'
        });
        return;
    }
    
    // Show loading state for validation
    if (files.length > 0) {
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'uploadingMessage';
        loadingDiv.innerHTML = `
            <div class="uploadingContent">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Validating ${files.length} image${files.length > 1 ? 's' : ''}...</span>
            </div>
        `;
        uploadedImages.appendChild(loadingDiv);
    }
    
    // Process files with validation
    processAuctionFilesWithValidation(Array.from(files), uploadedImages);
}

async function processAuctionFilesWithValidation(files, uploadedImages) {
    const validFiles = [];
    const errors = [];
    
    for (const file of files) {
        // Basic validation only (file type)
        const basicValidation = validateImageFile(file);
        if (!basicValidation.isValid) {
            errors.push(`${file.name}: ${basicValidation.error}`);
            continue;
        }
        
        validFiles.push({
            file: file
        });
    }
    
    // Remove loading message
    const loadingMessage = uploadedImages.querySelector('.uploadingMessage');
    if (loadingMessage) {
        loadingMessage.remove();
    }
    
    // Show validation errors if any
    if (errors.length > 0) {
        Swal.fire({
            title: 'Image Validation Error',
            html: `The following files could not be uploaded:<br><br>• ${errors.join('<br>• ')}`,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423'
        });
        
        // If no valid files, return early
        if (validFiles.length === 0) return;
    }
    
    // Process valid files
    validFiles.forEach((fileData, index) => {
        const file = fileData.file;
        
        // Store the file object for later submission
        auctionUploadedFiles.push(file);
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const imagePreview = document.createElement('div');
            imagePreview.className = 'auctionImagePreview';
            imagePreview.setAttribute('data-file-index', auctionUploadedFiles.length - 1);
            imagePreview.innerHTML = `
                <div class="imageContainer">
                    <img src="${e.target.result}" alt="Auction Preview ${index + 1}" class="previewImage">
                    <div class="imageOverlay">
                        <button type="button" class="removeImageBtn" onclick="removeAuctionImage(this)" title="Remove Image">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <div class="imageLabel">
                        Image ${uploadedImages.querySelectorAll('.auctionImagePreview').length + 1}
                    </div>
                    <div class="imageInfo">
                        ${(file.size / 1024 / 1024).toFixed(1)} MB
                    </div>
                </div>
            `;
            uploadedImages.appendChild(imagePreview);
        };
        reader.readAsDataURL(file);
    });
}

async function validateAuctionImageDimensions(file) {
    return new Promise((resolve) => {
        const img = new Image();
        const reader = new FileReader();
        
        reader.onload = function(e) {
            img.onload = function() {
                const width = this.naturalWidth;
                const height = this.naturalHeight;
                
                // Removed dimension restrictions for auction images - accept any valid image
                resolve({
                    isValid: true,
                    width: width,
                    height: height
                });
            };
            
            img.onerror = function() {
                resolve({
                    isValid: false,
                    error: 'Unable to read image dimensions'
                });
            };
            
            img.src = e.target.result;
        };
        
        reader.onerror = function() {
            resolve({
                isValid: false,
                error: 'Error reading image file'
            });
        };
        
        reader.readAsDataURL(file);
    });
}

function removeAuctionImage(button) {
    const imagePreview = button.closest('.auctionImagePreview');
    if (imagePreview) {
        // Get the file index and remove from stored files
        const fileIndex = parseInt(imagePreview.getAttribute('data-file-index'));
        if (!isNaN(fileIndex) && fileIndex >= 0 && fileIndex < auctionUploadedFiles.length) {
            auctionUploadedFiles.splice(fileIndex, 1);
        }
        
        imagePreview.remove();
        
        // Update labels and data-file-index for remaining images
        const uploadedImages = document.getElementById('auctionUploadedImages');
        if (uploadedImages) {
            const remainingImages = uploadedImages.querySelectorAll('.auctionImagePreview');
            remainingImages.forEach((preview, index) => {
                const label = preview.querySelector('.imageLabel');
                if (label) {
                    label.textContent = `Image ${index + 1}`;
                }
                preview.setAttribute('data-file-index', index);
            });
        }
        
        // Update auction preview if we're on step 3
        if (getCurrentAuctionStep() === 3) {
            generateAuctionPreview();
        }
    }
}

function generateAuctionPreview() {
    const previewContainer = document.getElementById('auctionPreview');
    if (!previewContainer) return;
    
    // Get form values
    const title = document.getElementById('auctionArtworkName')?.value || 'Untitled Artwork';
    const startingBid = document.getElementById('initialBid')?.value || '0';
    const style = document.getElementById('auctionStyle')?.value || 'Not specified';
    const width = document.getElementById('auctionWidth')?.value || '';
    const height = document.getElementById('auctionHeight')?.value || '';
    const depth = document.getElementById('auctionDepth')?.value || '';
    const year = document.getElementById('auctionYear')?.value || '';
    const startDate = document.getElementById('auctionStartDate')?.value || '';
    const endDate = document.getElementById('auctionEndDate')?.value || '';
    const description = document.getElementById('auctionDescription')?.value || 'No description provided';
    
    // Format dimensions
    let dimensionsText = '';
    if (width && height) {
        dimensionsText = `${width} × ${height}`;
        if (depth) dimensionsText += ` × ${depth}`;
        dimensionsText += ' cm';
    }
    
    // Format dates
    const formatDate = (dateString) => {
        if (!dateString) return 'Not set';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };
    
    // Get uploaded images
    const uploadedImages = document.getElementById('auctionUploadedImages');
    const images = uploadedImages ? uploadedImages.querySelectorAll('.auctionImagePreview img') : [];
    
    // Generate preview HTML
    let imagesHtml = '';
    if (images.length > 0) {
        imagesHtml = `
            <div class="previewImages">
                <h4>Artwork Images (${images.length})</h4>
                <div class="previewImageGrid">
                    ${Array.from(images).map((img, index) => `
                        <div class="previewImageThumb">
                            <img src="${img.src}" alt="Preview ${index + 1}">
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    } else {
        imagesHtml = `
            <div class="previewImages">
                <h4>Artwork Images</h4>
                <p class="noImages">No images uploaded</p>
            </div>
        `;
    }
    
    previewContainer.innerHTML = `
        <div class="auctionPreviewCard">
            <div class="previewHeader">
                <h3>Auction Preview</h3>
                <span class="previewLabel">Review your auction details before publishing</span>
            </div>
            
            <div class="previewContent">
                <div class="previewMainInfo">
                    <h2 class="previewTitle">${title}</h2>
                    <div class="previewBidding">
                        <span class="bidLabel">Starting Bid</span>
                        <span class="bidAmount">EGP ${parseFloat(startingBid).toLocaleString()}</span>
                    </div>
                </div>
                
                ${imagesHtml}
                
                <div class="previewDetails">
                    <div class="detailRow">
                        <span class="detailLabel">Art Style:</span>
                        <span class="detailValue">${style}</span>
                    </div>
                    ${dimensionsText ? `
                        <div class="detailRow">
                            <span class="detailLabel">Dimensions:</span>
                            <span class="detailValue">${dimensionsText}</span>
                        </div>
                    ` : ''}
                    ${year ? `
                        <div class="detailRow">
                            <span class="detailLabel">Year Created:</span>
                            <span class="detailValue">${year}</span>
                        </div>
                    ` : ''}
                    <div class="detailRow">
                        <span class="detailLabel">Auction Start:</span>
                        <span class="detailValue">${formatDate(startDate)}</span>
                    </div>
                    <div class="detailRow">
                        <span class="detailLabel">Auction End:</span>
                        <span class="detailValue">${formatDate(endDate)}</span>
                    </div>
                </div>
                
                <div class="previewDescription">
                    <h4>Description</h4>
                    <p>${description}</p>
                </div>
            </div>
        </div>
    `;
}

function handleAuctionImages(files) {
    // Redirect to the new comprehensive function
    handleAuctionFiles(files);
}

function startAuction() {
    // First validate all steps before submission
    if (!validateCurrentAuctionStep()) {
        return;
    }
    
    // Validate that we have at least a primary image
    if (!auctionPrimaryImage && auctionUploadedFiles.length === 0) {
        Swal.fire({
            title: 'Images Required!',
            text: 'Please upload at least a primary image before launching the auction.',
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423'
        });
        return;
    }
    
    // Collect form data
    const formData = new FormData();
    
    // Basic auction information
    formData.append('artwork_title', document.getElementById('auctionArtworkName')?.value || '');
    formData.append('starting_bid', document.getElementById('initialBid')?.value || '');
    formData.append('art_style', document.getElementById('auctionStyle')?.value || '');
    formData.append('width', document.getElementById('auctionWidth')?.value || '');
    formData.append('height', document.getElementById('auctionHeight')?.value || '');
    formData.append('depth', document.getElementById('auctionDepth')?.value || '');
    formData.append('year', document.getElementById('auctionYear')?.value || '');
    formData.append('start_date', document.getElementById('auctionStartDate')?.value || '');
    formData.append('end_date', document.getElementById('auctionEndDate')?.value || '');
    formData.append('description', document.getElementById('auctionDescription')?.value || '');
    
    // Add primary auction image if uploaded
    if (auctionPrimaryImage) {
        formData.append('primary_image', auctionPrimaryImage, `primary_${auctionPrimaryImage.name}`);
    }
    
    // Add additional auction images
    auctionUploadedFiles.forEach((file, index) => {
        formData.append('auction_images[]', file, `auction_image_${index + 1}.${file.name.split('.').pop()}`);
    });
    
    // Show loading state
    Swal.fire({
        title: 'Creating Auction...',
        text: 'Please wait while we process your auction listing',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Submit to API
    fetch('/API/addAuction.php', {
        method: 'POST',
        credentials: 'include', // Include session cookies
        body: formData
    })
        .then(response => {
            console.log('Auction API Response received:', response);
            console.log('Response status:', response.status);
            
            return response.text().then(text => {
                console.log('Raw response text:', text);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}, response: ${text}`);
                }
                
                try {
                    return JSON.parse(text);
                } catch (jsonError) {
                    console.error('JSON parse error:', jsonError);
                    throw new Error(`Response is not valid JSON: ${text}`);
                }
            });
        })
        .then(data => {
            console.log('Auction API Response data:', data);
            
            if (data.success) {
                // Success response
                let message = data.message;
                if (data.warnings && data.warnings.length > 0) {
                    message += `\n\nNote: ${data.warnings.join(', ')}`;
                }
                
                Swal.fire({
                    title: 'Auction Created Successfully!',
                    html: `
                        <div style="text-align: left;">
                            <p><strong>Artwork:</strong> ${data.data.artwork_title}</p>
                            <p><strong>Starting Bid:</strong> EGP ${parseFloat(data.data.starting_bid).toLocaleString()}</p>
                            <p><strong>Images Uploaded:</strong> ${data.data.images_uploaded}</p>
                            <p><strong>Auction Start:</strong> ${new Date(data.data.start_date).toLocaleString()}</p>
                            <p><strong>Auction End:</strong> ${new Date(data.data.end_date).toLocaleString()}</p>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'View My Auctions',
                    confirmButtonColor: '#6B4423',
                    showCancelButton: true,
                    cancelButtonText: 'Create Another',
                    cancelButtonColor: '#8B7355'
                }).then((result) => {
                    // Always reset the form and clear data after successful creation
                    resetAuctionForm();
                    
                    if (result.isConfirmed) {
                        // Switch to dashboard or auctions view
                        switchSection('dashboard');
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // Stay on auction form for creating another auction
                        // Form is already reset above
                    } else {
                        // If user closes the dialog or clicks outside, refresh the page
                        window.location.reload();
                    }
                });
                
            } else {
                // Error from API
                let errorMessage = data.message || 'Failed to create auction.';
                if (data.errors && data.errors.length > 0) {
                    errorMessage += '\n\nDetails:\n• ' + data.errors.join('\n• ');
                }
                
                Swal.fire({
                    title: 'Auction Creation Failed!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'Try Again',
                    confirmButtonColor: '#6B4423'
                });
                console.error('Auction API Error:', data);
            }
        })
        .catch(error => {
            console.error('Auction Network/Parse Error:', error);
            
            let errorMessage = 'An unexpected error occurred. Please try again.';
            
            if (error.message.includes('HTTP error! status: 400')) {
                errorMessage = 'Please check that all required fields are filled correctly.';
            } else if (error.message.includes('HTTP error! status: 500')) {
                errorMessage = 'Server error occurred. Please try again later.';
            } else if (error.message.includes('JSON')) {
                errorMessage = 'Server returned invalid response. Please contact support if this persists.';
            } else if (error.message.includes('Failed to fetch')) {
                errorMessage = 'Network connection failed. Please check your internet connection.';
            }
            
            Swal.fire({
                title: 'Connection Error!',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'Retry',
                confirmButtonColor: '#6B4423'
            });
        });
}

function resetAuctionForm() {
    // Reset form fields
    document.getElementById('addAuctionForm')?.reset();
    
    // Clear uploaded images and stored files
    const uploadedImages = document.getElementById('auctionUploadedImages');
    if (uploadedImages) {
        uploadedImages.innerHTML = '';
    }
    
    // Clear stored files array
    auctionUploadedFiles = [];
    
    // Reset to step 1
    showAuctionStep(1);
    updateAuctionStepNavigation();
    
    // Clear any validation states
    const allFields = document.querySelectorAll('#addAuctionForm input, #addAuctionForm select, #addAuctionForm textarea');
    allFields.forEach(field => {
        field.classList.remove('error', 'valid');
        const indicator = document.getElementById(field.id + 'Indicator');
        const errorElement = document.getElementById(field.id + 'Error');
        if (indicator) {
            indicator.className = 'inputIndicator';
        }
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    });
    
    // Clear any custom validation messages
    const errorElements = document.querySelectorAll('.errorText, .error-message');
    errorElements.forEach(element => {
        element.style.display = 'none';
        element.textContent = '';
    });
}

// Function to completely refresh the auction section
function refreshAuctionSection() {
    resetAuctionForm();
    // Optional: reload the page to ensure everything is clean
    // window.location.reload();
}

// ==========================================
// GALLERY EVENTS FUNCTIONALITY
// ==========================================

let currentGalleryStep = 1;
let selectedEventType = null;
let virtualTags = [];
let galleryUploadedFiles = []; // Store uploaded gallery files
let galleryPrimaryImage = null; // Store primary gallery image

function initializeGalleryEvents() {
    // Event type selection
    initializeEventTypeSelection();
    
    // Form navigation
    const nextBtnGallery = document.getElementById('nextStepGallery');
    const prevBtnGallery = document.getElementById('prevStepGallery');
    const publishEventBtn = document.getElementById('publishGalleryEventBtn');
    
    if (nextBtnGallery) {
        nextBtnGallery.addEventListener('click', function() {
            if (validateCurrentGalleryStep()) {
                nextGalleryStep();
            }
        });
    }
    
    if (prevBtnGallery) {
        prevBtnGallery.addEventListener('click', function() {
            previousGalleryStep();
        });
    }
    
    if (publishEventBtn) {
        publishEventBtn.addEventListener('click', function(e) {
            e.preventDefault();
            publishGalleryEvent();
        });
    }
    
    // Character counters
    initializeGalleryCharCounters();
    
    // Virtual tags functionality
    initializeVirtualTags();
    
    // Gallery image uploads
    initializeGalleryImageUpload();
    
    // Initialize gallery form validation
    initializeGalleryValidation();
    
    // Form submission
    const galleryForm = document.getElementById('addGalleryEventForm');
    if (galleryForm) {
        galleryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            publishGalleryEvent();
        });
    }
    
    // My Events toggle
    const viewMyEventsBtn = document.getElementById('viewMyEventsBtn');
    const backToCreateBtn = document.getElementById('backToCreateEvent');
    
    if (viewMyEventsBtn) {
        viewMyEventsBtn.addEventListener('click', function() {
            toggleMyEvents(true);
        });
    }
    
    if (backToCreateBtn) {
        backToCreateBtn.addEventListener('click', function() {
            toggleMyEvents(false);
        });
    }
}

function initializeEventTypeSelection() {
    const typeOptions = document.querySelectorAll('input[name="eventType"]');
    
    typeOptions.forEach(option => {
        option.addEventListener('change', function() {
            if (this.checked) {
                selectedEventType = this.value;
                showEventDetailsForm(this.value);
                updateEventTypeSelection(this.value);
            }
        });
    });
}

function showEventDetailsForm(eventType) {
    const eventDetailsSection = document.getElementById('eventDetailsSection');
    const virtualDetails = document.getElementById('virtualEventDetails');
    const physicalDetails = document.getElementById('physicalEventDetails');
    const formNavigation = document.getElementById('formNavigation');
    
    // Show the details section
    if (eventDetailsSection) {
        eventDetailsSection.style.display = 'block';
    }
    
    // Show navigation
    if (formNavigation) {
        formNavigation.style.display = 'block';
    }
    
    // Hide both detail forms first
    if (virtualDetails) virtualDetails.style.display = 'none';
    if (physicalDetails) physicalDetails.style.display = 'none';
    
    // Show the appropriate form based on event type
    if (eventType === 'virtual' && virtualDetails) {
        virtualDetails.style.display = 'block';
    } else if (eventType === 'physical' && physicalDetails) {
        physicalDetails.style.display = 'block';
    }
    
    // Update navigation state
    currentGalleryStep = 2; // We're now on the details step
    updateGalleryStepNavigation();
}

function updateEventTypeSelection(eventType) {
    const stepText = document.getElementById('currentStepText');
    
    // Update step text
    if (stepText) {
        stepText.textContent = eventType === 'virtual' ? 'Complete virtual event details' : 'Complete local gallery details';
    }
    
    // Add visual feedback
    const typeOptions = document.querySelectorAll('.typeOption');
    typeOptions.forEach(option => {
        const input = option.querySelector('input[type="radio"]');
        if (input.value === eventType) {
            option.classList.add('selected');
        } else {
            option.classList.remove('selected');
        }
    });
}

function validateCurrentGalleryStep() {
    if (currentGalleryStep === 1) {
        // Step 1: Event type selection
        if (!selectedEventType) {
            showNotification('Please select an event type', 'error');
            return false;
        }
        return true;
    }
    
    // For steps 2+ validate based on event type
    const stepSelector = selectedEventType === 'virtual' ? '[data-step="2-virtual"]' : '[data-step="2-physical"]';
    const currentStepElement = document.querySelector(`.formStep${stepSelector}`);
    
    if (!currentStepElement) return false;
    
    const requiredFields = currentStepElement.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('error');
            isValid = false;
        } else {
            field.classList.remove('error');
        }
    });
    
    if (!isValid) {
        showNotification('Please fill in all required fields', 'error');
    }
    
    return isValid;
}

function nextGalleryStep() {
    if (currentGalleryStep === 2) {
        // From step 2 (details) to step 3 (preview)
        const eventDetailsSection = document.getElementById('eventDetailsSection');
        const virtualDetails = document.getElementById('virtualEventDetails');
        const physicalDetails = document.getElementById('physicalEventDetails');
        const previewSection = document.getElementById('eventPreviewSection');
        
        // Hide the current details form
        if (selectedEventType === 'virtual' && virtualDetails) {
            virtualDetails.style.display = 'none';
        } else if (selectedEventType === 'physical' && physicalDetails) {
            physicalDetails.style.display = 'none';
        }
        
        // Show preview section
        if (previewSection) {
            previewSection.style.display = 'block';
        }
        
        currentGalleryStep = 3;
        generateEventPreview();
    }
    
    updateGalleryStepNavigation();
}

function previousGalleryStep() {
    if (currentGalleryStep === 3) {
        // From step 3 (preview) back to step 2 (details)
        const previewSection = document.getElementById('eventPreviewSection');
        const virtualDetails = document.getElementById('virtualEventDetails');
        const physicalDetails = document.getElementById('physicalEventDetails');
        
        // Hide preview section
        if (previewSection) {
            previewSection.style.display = 'none';
        }
        
        // Show the appropriate details form
        if (selectedEventType === 'virtual' && virtualDetails) {
            virtualDetails.style.display = 'block';
        } else if (selectedEventType === 'physical' && physicalDetails) {
            physicalDetails.style.display = 'block';
        }
        
        currentGalleryStep = 2;
    }
    
    updateGalleryStepNavigation();
}

function updateGalleryStepNavigation() {
    const prevBtn = document.getElementById('prevStepGallery');
    const nextBtn = document.getElementById('nextStepGallery');
    const publishBtn = document.getElementById('publishGalleryEventBtn');
    const stepText = document.getElementById('currentStepText');
    
    // Show/hide navigation buttons
    if (prevBtn) {
        prevBtn.style.display = currentGalleryStep > 1 ? 'inline-flex' : 'none';
    }
    
    if (nextBtn && publishBtn) {
        if (currentGalleryStep === 3) {
            nextBtn.style.display = 'none';
            publishBtn.style.display = 'inline-flex';
        } else {
            nextBtn.style.display = 'inline-flex';
            publishBtn.style.display = 'none';
        }
    }
    
    // Update step text
    if (stepText) {
        let text = '';
        switch (currentGalleryStep) {
            case 1:
                text = 'Choose event type';
                break;
            case 2:
                text = selectedEventType === 'virtual' ? 'Virtual event details' : 'Local gallery details';
                break;
            case 3:
                text = 'Preview & publish';
                break;
        }
        stepText.textContent = text;
    }
}

function initializeGalleryCharCounters() {
    // Virtual event description counter
    const virtualDescInput = document.getElementById('virtualEventDescription');
    const virtualDescCounter = document.getElementById('virtualDescCharCount');
    
    if (virtualDescInput && virtualDescCounter) {
        virtualDescInput.addEventListener('input', function() {
            virtualDescCounter.textContent = this.value.length;
        });
    }
    
    // Physical event description counter
    const physicalDescInput = document.getElementById('physicalEventDescription');
    const physicalDescCounter = document.getElementById('physicalDescCharCount');
    
    if (physicalDescInput && physicalDescCounter) {
        physicalDescInput.addEventListener('input', function() {
            physicalDescCounter.textContent = this.value.length;
        });
    }
}

function initializeVirtualTags() {
    const virtualTagsInput = document.getElementById('virtualEventTags');
    const virtualTagsList = document.getElementById('virtualTagsList');
    
    if (virtualTagsInput && virtualTagsList) {
        // Handle Enter key and comma separator
        virtualTagsInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                const value = this.value.trim().replace(',', '');
                if (value) {
                    addVirtualTag(value);
                    this.value = '';
                }
            }
        });
        
        // Handle space key for quick tag addition
        virtualTagsInput.addEventListener('keydown', function(e) {
            if (e.key === ' ' && this.value.trim()) {
                // Only if the current word seems complete (no trailing space)
                const words = this.value.trim().split(' ');
                if (words.length > 1) {
                    e.preventDefault();
                    const lastWord = words.pop();
                    const tagsToAdd = words.join(' ');
                    if (tagsToAdd) {
                        addVirtualTag(tagsToAdd);
                        this.value = lastWord + ' ';
                    }
                }
            }
        });
        
        // Real-time validation feedback
        virtualTagsInput.addEventListener('input', function() {
            const value = this.value.trim();
            
            // Hide previous errors
            const errorDiv = document.getElementById('virtualTagsError');
            if (errorDiv) {
                errorDiv.style.display = 'none';
            }
            
            // Show character count for long inputs
            if (value.length > 15) {
                this.style.borderColor = value.length > 20 ? 'var(--danger-red)' : 'var(--warning-orange)';
            } else {
                this.style.borderColor = '';
            }
        });
        
        // Clear input styling on blur
        virtualTagsInput.addEventListener('blur', function() {
            this.style.borderColor = '';
        });
    }
}

function addVirtualTag(tagText) {
    // Clean and validate tag text
    tagText = tagText.trim().toLowerCase();
    
    // Validate tag
    if (!tagText) return;
    if (tagText.length < 2) {
        showTagError('Tag must be at least 2 characters long');
        return;
    }
    if (tagText.length > 20) {
        showTagError('Tag cannot exceed 20 characters');
        return;
    }
    if (virtualTags.includes(tagText)) {
        showTagError('This tag has already been added');
        return;
    }
    if (virtualTags.length >= 10) {
        showTagError('Maximum 10 tags allowed');
        return;
    }
    
    virtualTags.push(tagText);
    
    const tagsList = document.getElementById('virtualTagsList');
    if (!tagsList) return;
    
    const tag = document.createElement('span');
    tag.className = 'virtualTag';
    tag.innerHTML = `
        <span class="tagText">${tagText}</span>
        <button type="button" class="virtualTagRemove" onclick="removeVirtualTag('${tagText}')" title="Remove tag">×</button>
    `;
    
    tagsList.appendChild(tag);
    
    // Add a subtle success indication
    tag.style.opacity = '0';
    tag.style.transform = 'scale(0.8)';
    setTimeout(() => {
        tag.style.transition = 'all 0.3s ease';
        tag.style.opacity = '1';
        tag.style.transform = 'scale(1)';
    }, 10);
    
    // Update tag counter
    updateTagCounter();
    
    // Update suggestions
    updateTagSuggestions();
}

function showTagError(message) {
    // Create or update error display
    let errorDiv = document.getElementById('virtualTagsError');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = 'virtualTagsError';
        errorDiv.style.cssText = `
            color: var(--danger-red);
            font-size: 0.85rem;
            margin-top: 5px;
            padding: 5px 10px;
            background: rgba(197, 83, 74, 0.1);
            border-radius: 4px;
            border-left: 3px solid var(--danger-red);
        `;
        
        const tagsInput = document.getElementById('virtualTagsInput');
        if (tagsInput) {
            tagsInput.appendChild(errorDiv);
        }
    }
    
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    
    // Auto-hide after 3 seconds
    setTimeout(() => {
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }, 3000);
}

function removeVirtualTag(tagText) {
    // Remove from array
    virtualTags = virtualTags.filter(tag => tag !== tagText);
    
    // Remove from DOM with animation
    const tagsList = document.getElementById('virtualTagsList');
    if (tagsList) {
        const tags = tagsList.querySelectorAll('.virtualTag');
        tags.forEach(tag => {
            const tagTextElement = tag.querySelector('.tagText');
            if (tagTextElement && tagTextElement.textContent.trim() === tagText) {
                // Add removal animation
                tag.style.transition = 'all 0.3s ease';
                tag.style.transform = 'scale(0.8)';
                tag.style.opacity = '0';
                
                setTimeout(() => {
                    tag.remove();
                }, 300);
            }
        });
    }
    
    // Update tag counter
    updateTagCounter();
    
    // Update suggestions
    updateTagSuggestions();
}

function updateTagCounter() {
    const counter = document.getElementById('virtualTagCounter');
    if (counter) {
        const count = virtualTags.length;
        counter.textContent = `(${count}/10)`;
        
        // Update counter styling based on count
        counter.classList.remove('warning', 'full');
        if (count >= 10) {
            counter.classList.add('full');
        } else if (count >= 8) {
            counter.classList.add('warning');
        }
    }
}

function updateTagSuggestions() {
    const suggestions = document.querySelectorAll('.suggestedTag');
    suggestions.forEach(suggestion => {
        const tagText = suggestion.textContent.toLowerCase();
        if (virtualTags.includes(tagText)) {
            suggestion.classList.add('used');
        } else {
            suggestion.classList.remove('used');
        }
    });
}

function generateEventPreview() {
    const previewTitle = document.getElementById('previewTitle');
    const previewType = document.getElementById('previewType');
    const previewPrice = document.getElementById('previewPrice');
    const previewDescription = document.getElementById('previewDescription');
    const previewLocationSection = document.getElementById('previewLocationSection');
    const previewTagsSection = document.getElementById('previewTagsSection');
    const previewTags = document.getElementById('previewTags');
    const previewStart = document.getElementById('previewStart');
    const previewDays = document.getElementById('previewDays');
    const previewDurationRow = document.getElementById('previewDurationRow');
    
    let title, price, description, startDate, duration;
    
    if (selectedEventType === 'virtual') {
        title = document.getElementById('virtualEventTitle')?.value || 'Untitled Event';
        price = document.getElementById('virtualEventPrice')?.value || '0';
        description = document.getElementById('virtualEventDescription')?.value || 'No description provided';
        startDate = document.getElementById('virtualEventStartDate')?.value;
        duration = document.getElementById('virtualEventDuration')?.value;
        
        // Show tags section for virtual events
        if (previewTagsSection) {
            previewTagsSection.style.display = 'block';
            if (previewTags) {
                previewTags.innerHTML = virtualTags.map(tag => 
                    `<span class="previewTag">${tag}</span>`
                ).join('');
            }
        }
        
        // Hide location section
        if (previewLocationSection) {
            previewLocationSection.style.display = 'none';
        }
        
        // Show duration for virtual events
        if (previewDurationRow) {
            previewDurationRow.style.display = 'block';
        }
        
    } else {
        title = document.getElementById('physicalEventTitle')?.value || 'Untitled Event';
        price = document.getElementById('physicalEventPrice')?.value || '0';
        description = document.getElementById('physicalEventDescription')?.value || 'No description provided';
        startDate = document.getElementById('physicalEventStartDate')?.value;
        
        // Hide tags section for physical events
        if (previewTagsSection) {
            previewTagsSection.style.display = 'none';
        }
        
        // Show location section
        if (previewLocationSection) {
            previewLocationSection.style.display = 'block';
            
            const address = document.getElementById('physicalEventAddress')?.value || 'Address not provided';
            const city = document.getElementById('physicalEventCity')?.value || 'City not provided';
            const phone = document.getElementById('physicalEventPhone')?.value || 'Phone not provided';
            
            document.getElementById('previewAddress').textContent = address;
            document.getElementById('previewCity').textContent = city;
            document.getElementById('previewPhone').textContent = phone;
        }
        
        // Hide duration for physical events
        if (previewDurationRow) {
            previewDurationRow.style.display = 'none';
        }
    }
    
    // Update preview elements
    if (previewTitle) previewTitle.textContent = title;
    if (previewType) previewType.textContent = selectedEventType === 'virtual' ? 'Virtual Exhibition' : 'Local Gallery';
    if (previewPrice) previewPrice.textContent = price === '0' || price === '' ? 'Free' : `EGP ${price}`;
    if (previewDescription) previewDescription.textContent = description;
    if (previewStart && startDate) {
        const date = new Date(startDate);
        previewStart.textContent = date.toLocaleString();
    }
    if (previewDays && duration) {
        previewDays.textContent = `${duration} days`;
    }
}

function publishGalleryEvent() {
    console.log('Publishing gallery event...');
    
    // Validate that an event type is selected
    if (!selectedEventType) {
        Swal.fire({
            title: 'Event Type Required!',
            text: 'Please select whether this is a virtual or physical gallery event.',
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423'
        });
        return;
    }
    
    // Collect form data based on event type
    const formData = new FormData();
    
    let title, description, price, startDate;
    let missingFields = [];
    
    if (selectedEventType === 'virtual') {
        title = document.getElementById('virtualEventTitle')?.value?.trim();
        description = document.getElementById('virtualEventDescription')?.value?.trim();
        price = document.getElementById('virtualEventPrice')?.value?.trim() || '0';
        startDate = document.getElementById('virtualEventStartDate')?.value;
        const duration = document.getElementById('virtualEventDuration')?.value?.trim();
        
        // Validation for virtual events
        if (!title) missingFields.push('Event Title');
        if (!description) missingFields.push('Event Description');
        if (!startDate) missingFields.push('Start Date');
        if (!duration) missingFields.push('Duration');
        
        if (missingFields.length > 0) {
            Swal.fire({
                title: 'Missing Information!',
                html: `Please fill in the following required fields:<br><br><strong>${missingFields.join('<br>')}</strong>`,
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6B4423'
            });
            return;
        }
        
        // Additional validation
        if (title.length < 3) {
            Swal.fire({
                title: 'Invalid Title!',
                text: 'Event title must be at least 3 characters long.',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6B4423'
            });
            return;
        }
        
        if (description.length < 1) {
            Swal.fire({
                title: 'Invalid Description!',
                text: 'Event description is required.',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6B4423'
            });
            return;
        }
        
        // Validate start date - must be in the future
        if (startDate) {
            const startDateObj = new Date(startDate);
            const currentDate = new Date();
            
            if (startDateObj <= currentDate) {
                Swal.fire({
                    title: 'Invalid Start Date!',
                    text: 'Start date must be in the future.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6B4423'
                });
                return;
            }
        }
        
        const numDuration = parseInt(duration);
        if (isNaN(numDuration) || numDuration < 1) {
            Swal.fire({
                title: 'Invalid Duration!',
                text: 'Duration must be a valid positive number in minutes.',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6B4423'
            });
            return;
        }
        
        if (numDuration > 120) {
            Swal.fire({
                title: 'Invalid Duration!',
                text: 'Duration cannot exceed 2 hours (120 minutes).',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6B4423'
            });
            return;
        }
        
        formData.append('title', title);
        formData.append('description', description);
        formData.append('gallery_type', 'virtual');
        formData.append('price', price);
        formData.append('duration', duration);
        formData.append('start_date', startDate);
        
    } else {
        title = document.getElementById('physicalEventTitle')?.value?.trim();
        description = document.getElementById('physicalEventDescription')?.value?.trim();
        price = document.getElementById('physicalEventPrice')?.value?.trim() || '0';
        startDate = document.getElementById('physicalEventStartDate')?.value;
        const address = document.getElementById('physicalEventAddress')?.value?.trim();
        const city = document.getElementById('physicalEventCity')?.value?.trim();
        const phone = document.getElementById('physicalEventPhone')?.value?.trim();
        
        // Validation for physical events
        if (!title) missingFields.push('Event Title');
        if (!description) missingFields.push('Event Description');
        if (!startDate) missingFields.push('Start Date');
        if (!address) missingFields.push('Gallery Address');
        if (!city) missingFields.push('City');
        if (!phone) missingFields.push('Contact Phone');
        
        if (missingFields.length > 0) {
            Swal.fire({
                title: 'Missing Information!',
                html: `Please fill in the following required fields:<br><br><strong>${missingFields.join('<br>')}</strong>`,
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6B4423'
            });
            return;
        }
        
        // Additional validation
        if (title.length < 3) {
            Swal.fire({
                title: 'Invalid Title!',
                text: 'Event title must be at least 3 characters long.',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6B4423'
            });
            return;
        }
        
        if (description.length < 1) {
            Swal.fire({
                title: 'Invalid Description!',
                text: 'Event description is required.',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6B4423'
            });
            return;
        }
        
        // Validate start date - must be in the future
        if (startDate) {
            const startDateObj = new Date(startDate);
            const currentDate = new Date();
            
            if (startDateObj <= currentDate) {
                Swal.fire({
                    title: 'Invalid Start Date!',
                    text: 'Start date must be in the future.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6B4423'
                });
                return;
            }
        }
        
        // Phone validation
        const phoneRegex = /^[0-9+\-\s()]+$/;
        if (!phoneRegex.test(phone)) {
            Swal.fire({
                title: 'Invalid Phone Number!',
                text: 'Please enter a valid phone number.',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6B4423'
            });
            return;
        }
        
        formData.append('title', title);
        formData.append('description', description);
        formData.append('gallery_type', 'physical');
        formData.append('price', price);
        formData.append('address', address);
        formData.append('city', city);
        formData.append('phone', phone);
        formData.append('start_date', startDate);
    }
    
    // Add gallery images to FormData
    galleryUploadedFiles.forEach((file, index) => {
        formData.append('gallery_images[]', file, `gallery_image_${index + 1}.${file.name.split('.').pop()}`);
    });
    
    // Add primary image if uploaded
    if (galleryPrimaryImage) {
        formData.append('primary_image', galleryPrimaryImage, `primary_${galleryPrimaryImage.name}`);
    }
    
    // Price validation (common for both types)
    if (price && price !== '0') {
        const numPrice = parseFloat(price);
        if (isNaN(numPrice) || numPrice < 0) {
            Swal.fire({
                title: 'Invalid Price!',
                text: 'Please enter a valid price.',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6B4423'
            });
            return;
        }
    }
    
    // Show loading
    Swal.fire({
        title: 'Publishing Gallery Event...',
        text: 'Please wait while we create your gallery event',
        icon: 'info',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Submit to API
    fetch('/API/addGallery.php', {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Gallery API Response received:', response);
        console.log('Response status:', response.status);
        
        return response.text().then(text => {
            console.log('Raw response text:', text);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}, response: ${text}`);
            }
            
            try {
                return JSON.parse(text);
            } catch (jsonError) {
                console.error('JSON parse error:', jsonError);
                throw new Error(`Response is not valid JSON: ${text}`);
            }
        });
    })
    .then(data => {
        console.log('Gallery API Response data:', data);
        
        if (data.success) {
            // Different messages based on gallery type
            const isVirtual = selectedEventType === 'virtual';
            
            Swal.fire({
                title: isVirtual ? 'Gallery Published Successfully!' : 'Gallery Submitted Successfully!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'Continue',
                confirmButtonColor: '#6B4423',
                showCancelButton: true,
                cancelButtonText: 'Create Another',
                cancelButtonColor: '#8B7355',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Reset form and switch to dashboard or my events
                    resetGalleryForm();
                    switchSection('dashboard');
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    resetGalleryForm();
                }
            });
            
        } else {
            // Error from API
            Swal.fire({
                title: 'Publication Failed!',
                text: data.message || 'Failed to publish gallery event.',
                icon: 'error',
                confirmButtonText: 'Try Again',
                confirmButtonColor: '#6B4423'
            });
            console.error('Gallery API Error:', data);
        }
    })
    .catch(error => {
        console.error('Gallery Network/Parse Error:', error);
        
        let errorMessage = 'An unexpected error occurred. Please try again.';
        
        if (error.message.includes('HTTP error! status: 400')) {
            errorMessage = 'Please check that all required fields are filled correctly.';
        } else if (error.message.includes('HTTP error')) {
            errorMessage = 'Server error occurred. Please check your connection and try again.';
        } else if (error.message.includes('JSON')) {
            errorMessage = 'Server returned invalid response. Please contact support if this persists.';
        } else if (error.message.includes('Failed to fetch')) {
            errorMessage = 'Network connection failed. Please check your internet connection.';
        }
        
        Swal.fire({
            title: 'Connection Error!',
            text: errorMessage,
            icon: 'error',
            confirmButtonText: 'Retry',
            confirmButtonColor: '#6B4423'
        });
    });
}

function toggleMyEvents(show) {
    const formContainer = document.querySelector('.galleryFormContainer');
    const eventsCard = document.getElementById('myEventsCard');
    
    if (formContainer && eventsCard) {
        if (show) {
            formContainer.style.display = 'none';
            eventsCard.style.display = 'block';
        } else {
            formContainer.style.display = 'block';
            eventsCard.style.display = 'none';
        }
    }
}

// ==========================================
// STATISTICS FUNCTIONALITY
// ==========================================

let artworksSwiper, virtualGalleriesSwiper, localGalleriesSwiper, auctionsSwiper;

function initializeStatistics() {
    // Initialize swipers when statistics section loads
    if (currentSection === 'statistics') {
        initializeSwipers();
    }
}

function initializeSwipers() {
    // Initialize Artworks Swiper
    artworksSwiper = new Swiper('.artworks-swiper', {
        slidesPerView: 'auto',
        spaceBetween: 20,
        freeMode: true,
        pagination: {
            el: '.artworks-swiper .swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.artworks-swiper .swiper-button-next',
            prevEl: '.artworks-swiper .swiper-button-prev',
        },
        breakpoints: {
            640: {
                slidesPerView: 'auto',
                spaceBetween: 20,
            },
            1024: {
                slidesPerView: 'auto',
                spaceBetween: 25,
            },
            1440: {
                slidesPerView: 'auto',
                spaceBetween: 30,
            }
        }
    });

    // Initialize Virtual Galleries Swiper
    virtualGalleriesSwiper = new Swiper('.virtual-galleries-swiper', {
        slidesPerView: 'auto',
        spaceBetween: 20,
        freeMode: true,
        pagination: {
            el: '.virtual-galleries-swiper .swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.virtual-galleries-swiper .swiper-button-next',
            prevEl: '.virtual-galleries-swiper .swiper-button-prev',
        },
        breakpoints: {
            640: {
                slidesPerView: 'auto',
                spaceBetween: 20,
            },
            1024: {
                slidesPerView: 'auto',
                spaceBetween: 25,
            },
            1440: {
                slidesPerView: 'auto',
                spaceBetween: 30,
            }
        }
    });

    // Initialize Local Galleries Swiper
    localGalleriesSwiper = new Swiper('.local-galleries-swiper', {
        slidesPerView: 'auto',
        spaceBetween: 20,
        freeMode: true,
        pagination: {
            el: '.local-galleries-swiper .swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.local-galleries-swiper .swiper-button-next',
            prevEl: '.local-galleries-swiper .swiper-button-prev',
        },
        breakpoints: {
            640: {
                slidesPerView: 'auto',
                spaceBetween: 20,
            },
            1024: {
                slidesPerView: 'auto',
                spaceBetween: 25,
            },
            1440: {
                slidesPerView: 'auto',
                spaceBetween: 30,
            }
        }
    });

    // Initialize Auctions Swiper
    auctionsSwiper = new Swiper('.auctions-swiper', {
        slidesPerView: 'auto',
        spaceBetween: 20,
        freeMode: true,
        pagination: {
            el: '.auctions-swiper .swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.auctions-swiper .swiper-button-next',
            prevEl: '.auctions-swiper .swiper-button-prev',
        },
        breakpoints: {
            640: {
                slidesPerView: 'auto',
                spaceBetween: 20,
            },
            1024: {
                slidesPerView: 'auto',
                spaceBetween: 25,
            },
            1440: {
                slidesPerView: 'auto',
                spaceBetween: 30,
            }
        }
    });
}

async function loadArtistStatistics() {
    try {
        // First check credentials using the credential check endpoint
        console.log('Checking credentials...');
        const credentialResponse = await fetch('./API/checkCredentials.php', {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        if (!credentialResponse.ok) {
            throw new Error(`Credential check failed! status: ${credentialResponse.status}`);
        }
        
        const credentialData = await credentialResponse.json();
        console.log('Credential check result:', credentialData);
        
        if (!credentialData.success) {
            console.error('Authentication failed:', credentialData.message);
            showEmptyStatistics();
            return;
        }
        
        const userId = credentialData.user_id;
        console.log('Authenticated user ID:', userId);
        
        // COMPLETE cache clearing - destroy all cached data
        window.currentArtistData = null;
        
        // Clear localStorage if any cached data exists
        if (typeof(Storage) !== "undefined") {
            localStorage.removeItem('artistData');
            localStorage.removeItem('statisticsData');
            localStorage.clear(); // Clear all localStorage
        }
        
        // Clear sessionStorage
        if (typeof(Storage) !== "undefined") {
            sessionStorage.clear();
        }
        
        // Clear all browser caches
        if ('caches' in window) {
            caches.keys().then(names => {
                names.forEach(name => {
                    caches.delete(name);
                });
            });
        }
        
        // Clear any potential cached DOM data
        const containers = ['artworks-container', 'virtual-galleries-container', 'local-galleries-container', 'auctions-container'];
        containers.forEach(containerId => {
            const container = document.getElementById(containerId);
            if (container) {
                container.innerHTML = '';
            }
        });
        
        // Show loading state
        const loadingElement = document.getElementById('statisticsLoading');
        const contentElement = document.getElementById('statisticsContent');
        
        if (loadingElement) loadingElement.style.display = 'block';
        if (contentElement) contentElement.style.display = 'none';

        // Fetch real data from API using authenticated request with user_id
        console.log('Fetching artist statistics for user ID:', userId);
        const timestamp = new Date().getTime(); // Cache buster
        const randomParam = Math.random().toString(36).substring(7); // Additional randomness
        const response = await fetch(`./API/getArtistStatistics.php?user_id=${userId}&t=${timestamp}&r=${randomParam}&clearCache=1`, {
            method: 'GET',
            credentials: 'include', // Ensure cookies are sent
            headers: {
                'Content-Type': 'application/json',
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0',
                'X-Clear-Session': '1' // Custom header to indicate session should be cleared
            }
        });
        
        console.log('API response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Artist statistics data:', data);
        console.log('Authenticated artist_id:', data.data?.artist_id);
        console.log('Number of artworks:', data.data?.products?.length);
        if (data.data?.products?.length > 0) {
            console.log('First artwork:', data.data.products[0]);
            console.log('All artwork IDs and titles:', data.data.products.map(p => ({ id: p.id, title: p.title, artist_id: data.data?.artist_id })));
        }
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to fetch artist statistics');
        }

        // Store artist data globally for use in other functions
        window.currentArtistData = {
            artist_name: data.data.artist_info ? `${data.data.artist_info.first_name || ''} ${data.data.artist_info.last_name || ''}`.trim() : 'Artist'
        };

        // Update dashboard cards with real data
        updateDashboardStats(data.data.summary);

        // Transform and render each section with real data
        const artworks = transformArtworksData(data.data.products || []);
        const virtualGalleries = transformVirtualGalleriesData(data.data.virtual_galleries || []);
        const localGalleries = transformLocalGalleriesData(data.data.local_galleries || []);
        const auctions = transformAuctionsData(data.data.products || []); // Filter auctions from products

        // Render each section
        renderArtworksSection(artworks);
        renderVirtualGalleriesSection(virtualGalleries);
        renderLocalGalleriesSection(localGalleries);
        renderAuctionsSection(auctions);

        // Hide loading and show content
        if (loadingElement) loadingElement.style.display = 'none';
        if (contentElement) contentElement.style.display = 'block';

        // Initialize swipers after content is loaded
        setTimeout(() => {
            initializeSwipers();
        }, 100);

        // Update swipers after content is loaded
        setTimeout(() => {
            if (artworksSwiper) artworksSwiper.update();
            if (virtualGalleriesSwiper) virtualGalleriesSwiper.update();
            if (localGalleriesSwiper) localGalleriesSwiper.update();
            if (auctionsSwiper) auctionsSwiper.update();
        }, 200);

    } catch (error) {
        console.error('Error loading artist statistics:', error);
        // Remove notification, just log the error
        
        // Hide loading state and show empty states
        const loadingElement = document.getElementById('statisticsLoading');
        const contentElement = document.getElementById('statisticsContent');
        
        if (loadingElement) loadingElement.style.display = 'none';
        if (contentElement) contentElement.style.display = 'block';
        
        // Show empty states for all sections
        renderArtworksSection([]);
        renderVirtualGalleriesSection([]);
        renderLocalGalleriesSection([]);
        renderAuctionsSection([]);
    }
}

// Function to show empty statistics when authentication fails
function showEmptyStatistics() {
    // Hide loading state
    const loadingElement = document.getElementById('statisticsLoading');
    const contentElement = document.getElementById('statisticsContent');
    
    if (loadingElement) loadingElement.style.display = 'none';
    if (contentElement) contentElement.style.display = 'block';
    
    // Show empty states for all sections
    renderArtworksSection([]);
    renderVirtualGalleriesSection([]);
    renderLocalGalleriesSection([]);
    renderAuctionsSection([]);
    
    // Set default dashboard stats
    updateDashboardStats({});
}

// Function to update dashboard statistics cards
function updateDashboardStats(summaryData) {
    try {
        // Provide default values if summaryData is missing or incomplete
        const summary = summaryData || {};
        
        // Update total revenue
        const totalRevenueElement = document.getElementById('total-revenue');
        if (totalRevenueElement) {
            const revenue = parseFloat(summary.total_revenue) || 0;
            totalRevenueElement.textContent = `EGP ${revenue.toLocaleString()}`;
        }

        // Update artwork count (only artworks, not auctions)
        const artworkCountElement = document.getElementById('artwork-count');
        if (artworkCountElement) {
            artworkCountElement.textContent = summary.total_artworks || 0;
        }

        // Update galleries count
        const galleriesCountElement = document.getElementById('galleries-count');
        if (galleriesCountElement) {
            galleriesCountElement.textContent = summary.total_galleries || 0;
        }

        // Update auctions count
        const auctionsCountElement = document.getElementById('auctions-count');
        if (auctionsCountElement) {
            auctionsCountElement.textContent = summary.total_auctions || 0;
        }

        // Update orders count
        const ordersCountElement = document.getElementById('orders-count');
        if (ordersCountElement) {
            ordersCountElement.textContent = summary.total_sales || 0;
        }
    } catch (error) {
        console.error('Error updating dashboard stats:', error);
        
        // Set default values on error
        const elements = [
            { id: 'total-revenue', value: 'EGP 0' },
            { id: 'artwork-count', value: '0' },
            { id: 'galleries-count', value: '0' },
            { id: 'auctions-count', value: '0' },
            { id: 'orders-count', value: '0' }
        ];
        
        elements.forEach(({ id, value }) => {
            const element = document.getElementById(id);
            if (element) element.textContent = value;
        });
    }
}

// Transform API artworks data to match UI format
function transformArtworksData(artworks) {
    return artworks.map(artwork => ({
        id: artwork.id,
        title: artwork.title,
        artist: getCurrentArtistName(),
        price: parseFloat(artwork.price) || 0,
        dimensions: artwork.dimensions || 'N/A',
        description: artwork.description || 'No description available',
        image: artwork.image || '/image/default-artwork.jpg',
        category: artwork.type || 'Uncategorized',
        status: (artwork.status || 'active').toLowerCase()
    }));
}

// Transform API virtual galleries data
function transformVirtualGalleriesData(galleries) {
    return galleries.map(gallery => ({
        id: gallery.id,
        title: gallery.title || 'Untitled Gallery',
        description: gallery.description || 'No description available',
        price: parseFloat(gallery.price) || 0,
        duration: gallery.duration || 0,
        artworks_count: gallery.artwork_count || 0,
        status: (gallery.status || 'active').toLowerCase().replace(' ', '_'),
        image: gallery.image || '/image/default-gallery.jpg'
    }));
}

// Transform API local galleries data
function transformLocalGalleriesData(galleries) {
    return galleries.map(gallery => ({
        id: gallery.id,
        title: gallery.title || 'Untitled Gallery',
        description: gallery.description || 'No description available',
        address: gallery.address || 'Address not specified',
        city: gallery.city || 'City not specified',
        phone: gallery.phone || 'Phone not specified',
        price: parseFloat(gallery.price) || 0,
        artworks_count: gallery.artwork_count || 0,
        status: (gallery.status || 'active').toLowerCase().replace(' ', '_'),
        image: gallery.image || './image/default-gallery.jpg'
    }));
}

// Transform API auctions data to match UI format
function transformAuctionsData(products) {
    // Filter only products that are on auction
    const auctions = products.filter(product => product.on_auction);
    
    // Update auctions count in dashboard
    const auctionsCountElement = document.getElementById('auctions-count');
    if (auctionsCountElement) {
        auctionsCountElement.textContent = auctions.length;
    }

    return auctions.map(auction => ({
        id: auction.auction_id || auction.id, // Use auction_id if available, fallback to artwork_id
        artwork_id: auction.id, // Keep artwork_id for reference
        title: auction.title || 'Untitled Auction',
        description: auction.description || 'No description available',
        starting_bid: parseFloat(auction.price) || 0,
        current_bid: parseFloat(auction.price) || 0, // You might want to get actual current bid from auction table
        dimensions: auction.dimensions || 'N/A',
        status: 'active', // All fetched auctions should be active
        end_date: auction.end_date || 'N/A',
        image: auction.image || '/image/default-auction.jpg'
    }));
}

// Helper function to get current artist name
function getCurrentArtistName() {
    // Try to get artist name from API response data
    if (window.currentArtistData && window.currentArtistData.artist_name) {
        return window.currentArtistData.artist_name;
    }
    
    // Try to get from meta tag or other sources
    const artistNameMeta = document.querySelector('meta[name="artist-name"]');
    if (artistNameMeta) {
        return artistNameMeta.getAttribute('content');
    }
    
    // Fallback to a placeholder
    return 'Artist';
}

// Render functions for statistics sections
function renderArtworksSection(artworks) {
    const container = document.getElementById('artworks-container');
    const countElement = document.getElementById('artworks-count');
    
    countElement.textContent = artworks.length;
    
    if (artworks.length === 0) {
        container.innerHTML = `
            <div class="swiper-slide">
                <div class="stats-empty-state">
                    <div class="stats-empty-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <h3>No Artworks Yet</h3>
                    <p>Start creating your first artwork to see it here</p>
                    <button class="stats-empty-action" onclick="switchSection('artwork')">
                        <i class="fas fa-plus"></i> Add Artwork
                    </button>
                </div>
            </div>
        `;
        return;
    }

    container.innerHTML = artworks.map(artwork => `
        <div class="swiper-slide">
            <div class="stats-card">
                <div class="stats-card-image-container">
                    <img src="${artwork.image}" alt="${artwork.title}" class="stats-card-image">
                    <div class="stats-card-overlay">
                        <div class="stats-card-actions">
                            <button class="stats-action-btn edit-btn" onclick="openEditModal(${artwork.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="stats-action-btn delete-btn" onclick="deleteArtwork(${artwork.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="stats-card-info">
                    <div class="stats-card-category">${artwork.category}</div>
                    <h3 class="stats-card-title">${artwork.title}</h3>
                    <p class="stats-card-artist">By ${artwork.artist}</p>
                    <p class="stats-card-price">EGP ${artwork.price.toLocaleString()}</p>
                    <p class="stats-card-dimensions">${artwork.dimensions}</p>
                    <p class="stats-card-description">${artwork.description}</p>
                    <div class="stats-card-status">
                        <span class="status-badge ${artwork.status}">${artwork.status.charAt(0).toUpperCase() + artwork.status.slice(1)}</span>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function renderVirtualGalleriesSection(galleries) {
    const container = document.getElementById('virtual-galleries-container');
    const countElement = document.getElementById('virtual-galleries-count');
    
    countElement.textContent = galleries.length;
    
    if (galleries.length === 0) {
        container.innerHTML = `
            <div class="swiper-slide">
                <div class="stats-empty-state">
                    <div class="stats-empty-icon">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <h3>No Virtual Galleries</h3>
                    <p>Create your first virtual gallery to showcase your work</p>
                    <button class="stats-empty-action" onclick="switchSection('gallery')">
                        <i class="fas fa-plus"></i> Create Gallery
                    </button>
                </div>
            </div>
        `;
        return;
    }

    container.innerHTML = galleries.map(gallery => `
        <div class="swiper-slide">
            <div class="stats-card">
                <div class="stats-card-image-container">
                    <img src="${gallery.image}" alt="${gallery.title}" class="stats-card-image">
                    <div class="stats-card-overlay">
                        <div class="stats-card-actions">
                            <button class="stats-action-btn edit-btn" onclick="editVirtualGallery(${gallery.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="stats-action-btn delete-btn" onclick="deleteVirtualGallery(${gallery.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="stats-card-info">
                    <div class="stats-card-category">Virtual Gallery</div>
                    <h3 class="stats-card-title">${gallery.title}</h3>
                    <p class="stats-card-price">${gallery.price > 0 ? `EGP ${gallery.price.toLocaleString()}` : 'Free'}</p>
                    <p class="stats-card-dimensions">${gallery.duration} minutes • ${gallery.artworks_count} artworks</p>
                    <p class="stats-card-description">${gallery.description}</p>
                    <div class="stats-card-status">
                        <span class="status-badge ${gallery.status}">${gallery.status.charAt(0).toUpperCase() + gallery.status.slice(1)}</span>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function renderLocalGalleriesSection(galleries) {
    const container = document.getElementById('local-galleries-container');
    const countElement = document.getElementById('local-galleries-count');
    
    countElement.textContent = galleries.length;
    
    if (galleries.length === 0) {
        container.innerHTML = `
            <div class="swiper-slide">
                <div class="stats-empty-state">
                    <div class="stats-empty-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3>No Local Galleries</h3>
                    <p>Create your first local gallery exhibition</p>
                    <button class="stats-empty-action" onclick="switchSection('gallery')">
                        <i class="fas fa-plus"></i> Create Gallery
                    </button>
                </div>
            </div>
        `;
        return;
    }

    container.innerHTML = galleries.map(gallery => `
        <div class="swiper-slide">
            <div class="stats-card">
                <div class="stats-card-image-container">
                    <img src="${gallery.image}" alt="${gallery.title}" class="stats-card-image">
                    <div class="stats-card-overlay">
                        <div class="stats-card-actions">
                            <button class="stats-action-btn edit-btn" onclick="editLocalGallery(${gallery.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="stats-action-btn delete-btn" onclick="deleteLocalGallery(${gallery.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="stats-card-info">
                    <div class="stats-card-category">Local Gallery</div>
                    <h3 class="stats-card-title">${gallery.title}</h3>
                    <p class="stats-card-artist">${gallery.city}</p>
                    <p class="stats-card-price">${gallery.price > 0 ? `EGP ${gallery.price.toLocaleString()}` : 'Free Entry'}</p>
                    <p class="stats-card-dimensions">${gallery.artworks_count} artworks</p>
                    <p class="stats-card-description">${gallery.description}</p>
                    <div class="stats-card-status">
                        <span class="status-badge ${gallery.status}">${gallery.status.charAt(0).toUpperCase() + gallery.status.slice(1)}</span>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function renderAuctionsSection(auctions) {
    const container = document.getElementById('auctions-container');
    const countElement = document.getElementById('auctions-count');
    
    countElement.textContent = auctions.length;
    
    if (auctions.length === 0) {
        container.innerHTML = `
            <div class="swiper-slide">
                <div class="stats-empty-state">
                    <div class="stats-empty-icon">
                        <i class="fas fa-gavel"></i>
                    </div>
                    <h3>No Auctions</h3>
                    <p>Create your first auction to start bidding</p>
                    <button class="stats-empty-action" onclick="switchSection('auction')">
                        <i class="fas fa-plus"></i> Create Auction
                    </button>
                </div>
            </div>
        `;
        return;
    }

    container.innerHTML = auctions.map(auction => `
        <div class="swiper-slide">
            <div class="stats-card">
                <div class="stats-card-image-container">
                    <img src="${auction.image}" alt="${auction.title}" class="stats-card-image">
                    <div class="stats-card-overlay">
                        <div class="stats-card-actions">
                            <button class="stats-action-btn edit-btn" onclick="editAuction(${auction.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="stats-action-btn delete-btn" onclick="deleteAuction(${auction.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="stats-card-info">
                    <div class="stats-card-category">Auction</div>
                    <h3 class="stats-card-title">${auction.title}</h3>
                    <p class="stats-card-price">Current: EGP ${auction.current_bid.toLocaleString()}</p>
                    <p class="stats-card-dimensions">${auction.dimensions}</p>
                    <p class="stats-card-description">${auction.description}</p>
                    <div class="stats-card-status">
                        <span class="status-badge ${auction.status}">${auction.status.charAt(0).toUpperCase() + auction.status.slice(1)}</span>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Action functions for edit and delete buttons
// Global function to open edit modal - can be called from anywhere
window.openEditModal = function(artworkId) {
    console.log('Opening edit modal for artwork:', artworkId);
    
    const modal = document.getElementById('editArtworkModal');
    if (!modal) {
        alert('Edit modal not found');
        return;
    }
    
    // Always ensure modal is closed first
    modal.classList.remove('active');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    
    // Reset form completely
    const form = document.getElementById('editArtworkForm');
    if (form) {
        form.reset();
    }
    
    // Reset image preview
    const img = document.getElementById('editArtworkImg');
    const text = document.querySelector('#editArtworkImagePreview .file-input-text');
    if (img) {
        img.style.display = 'none';
        img.src = '';
    }
    if (text) {
        text.textContent = 'Choose Image';
    }
    
    console.log('Making API call for artwork:', artworkId);
    
    // Make API call
    fetch(`./API/getArtworkInfo.php?id=${artworkId}&timestamp=${Date.now()}&rand=${Math.random()}`, {
        method: 'GET',
        credentials: 'include',
        headers: {
            'Cache-Control': 'no-cache, no-store, must-revalidate',
            'Pragma': 'no-cache',
            'Expires': '0'
        }
    })
    .then(response => {
        console.log('API response status:', response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('API response data:', data);
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to load artwork');
        }
        
        const artwork = data.data;
        
        // Populate form fields
        const artwork_id = artwork.artwork_id;
        const title = artwork.title || '';
        const category = artwork.type || artwork.category || '';
        const style = artwork.art_style || artwork.style || '';
        const medium = artwork.medium || '';
        const price = artwork.price || '';
        const width = artwork.width || '';
        const height = artwork.height || '';
        const depth = artwork.depth || '';
        const year = artwork.year || '';
        const description = artwork.description || '';
        const is_available = artwork.is_available ? '1' : '0';
        const on_auction = artwork.on_auction ? '1' : '0';
        
        // Set form values
        document.getElementById('editArtworkId').value = artwork_id;
        document.getElementById('editArtworkTitle').value = title;
        document.getElementById('editArtworkCategory').value = category;
        document.getElementById('editArtworkStyle').value = style;
        document.getElementById('editArtworkMedium').value = medium;
        document.getElementById('editArtworkPrice').value = price;
        document.getElementById('editArtworkWidth').value = width;
        document.getElementById('editArtworkHeight').value = height;
        document.getElementById('editArtworkDepth').value = depth;
        document.getElementById('editArtworkYear').value = year;
        document.getElementById('editArtworkDescription').value = description;
        document.getElementById('editArtworkAvailable').value = is_available;
        document.getElementById('editArtworkAuction').value = on_auction;
        
        // Update character counter for description
        const charCountElement = document.getElementById('editDescCharCount');
        if (charCountElement) {
            charCountElement.textContent = description.length;
        }
        
        // Handle image preview and photo gallery
        if (artwork.artwork_image_url && img && text) {
            img.src = artwork.artwork_image_url;
            img.style.display = 'block';
            text.textContent = 'Change Image';
        }
        
        // Display artwork photos from artwork_photos table
        displayArtworkPhotos(artwork.photos || [], artwork.artwork_image);
        
        // Show modal
        modal.style.display = 'flex';
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Initialize validation for edit form
        setTimeout(() => {
            initializeEditArtworkValidation();
        }, 100);
        
        console.log('Modal opened successfully');
        
    })
    .catch(error => {
        console.error('Error loading artwork:', error);
        alert('Failed to load artwork details: ' + error.message);
    });
};

// Function to display artwork photos in the edit modal
function displayArtworkPhotos(photos, primaryImageName) {
    const photosContainer = document.getElementById('artworkPhotosContainer');
    if (!photosContainer) {
        console.warn('Photos container not found in modal');
        return;
    }
    
    // Clear existing photos and create grid
    photosContainer.innerHTML = '<div class="artwork-photos-grid"></div>';
    const photosGrid = photosContainer.querySelector('.artwork-photos-grid');
    
    if (!photos || photos.length === 0) {
        photosGrid.innerHTML = '<p class="no-photos-message">No additional photos uploaded</p>';
        return;
    }
    
    // Display photos directly in the existing grid
    photos.forEach(photo => {
        const photoItem = document.createElement('div');
        photoItem.className = 'artwork-photo-item';
        
        // Check if this is the primary image
        const isPrimary = photo.is_primary || photo.image_path === primaryImageName;
        photoItem.className = `artwork-photo-item ${isPrimary ? 'primary-photo' : ''}`;
        
        photoItem.innerHTML = `
            <img src="${photo.photo_url}" alt="${photo.alt_text || 'Artwork photo'}" class="artwork-photo-thumb">
            ${isPrimary ? '<div class="primary-badge"><i class="fas fa-star"></i> Primary</div>' : ''}
            <div class="photo-actions">
                ${!isPrimary ? `<button type="button" class="photo-action-btn set-primary-btn" onclick="setPrimaryPhoto(${photo.photo_id})" title="Set as primary">
                    <i class="fas fa-star"></i>
                </button>` : ''}
                <button type="button" class="photo-action-btn delete-photo-btn" onclick="deleteArtworkPhoto(${photo.photo_id})" title="Delete photo">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        photosGrid.appendChild(photoItem);
    });
}

// Function to delete artwork photo
window.deleteArtworkPhoto = function(photoId) {
    if (!confirm('Are you sure you want to delete this photo?')) {
        return;
    }
    
    fetch('./API/deleteArtworkPhoto.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        credentials: 'include',
        body: JSON.stringify({ photo_id: photoId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the photos display
            const artworkId = document.getElementById('editArtworkId').value;
            if (artworkId) {
                refreshArtworkPhotos(artworkId);
            }
            showNotification('Photo deleted successfully!', 'success');
        } else {
            throw new Error(data.message || 'Failed to delete photo');
        }
    })
    .catch(error => {
        console.error('Error deleting photo:', error);
        showNotification('Failed to delete photo: ' + error.message, 'error');
    });
};

// Function to set primary photo
window.setPrimaryPhoto = function(photoId) {
    const artworkId = document.getElementById('editArtworkId').value;
    if (!artworkId) {
        showNotification('Artwork ID not found', 'error');
        return;
    }
    
    fetch('./API/setPrimaryPhoto.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        credentials: 'include',
        body: JSON.stringify({ 
            photo_id: photoId,
            artwork_id: artworkId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the photos display
            refreshArtworkPhotos(artworkId);
            showNotification('Primary photo updated successfully!', 'success');
        } else {
            throw new Error(data.message || 'Failed to set primary photo');
        }
    })
    .catch(error => {
        console.error('Error setting primary photo:', error);
        showNotification('Failed to set primary photo: ' + error.message, 'error');
    });
};

// Function to refresh artwork photos display
function refreshArtworkPhotos(artworkId) {
    fetch(`./API/getArtworkInfo.php?id=${artworkId}&t=${Date.now()}`, {
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayArtworkPhotos(data.data.photos || [], data.data.artwork_image);
        }
    })
    .catch(error => {
        console.error('Error refreshing photos:', error);
    });
}

// Function to display artwork photos with delete functionality
function displayArtworkPhotos(photos, primaryImageName) {
    console.log('Displaying artwork photos:', photos);
    console.log('Primary image name:', primaryImageName);
    
    // Use the existing photos container
    const photosContainer = document.getElementById('artworkPhotosContainer');
    if (!photosContainer) {
        console.error('Photos container not found');
        return;
    }
    
    // Clear existing content and create the grid
    photosContainer.innerHTML = '<div class="artwork-photos-grid" id="artworkPhotosGrid"></div>';
    
    const photosGrid = document.getElementById('artworkPhotosGrid');
    if (!photosGrid) return;
    
    // Clear existing photos
    photosGrid.innerHTML = '';
    
    if (!photos || photos.length === 0) {
        photosGrid.innerHTML = '<p class="no-photos-message">No additional photos uploaded for this artwork.</p>';
        return;
    }
    
    // Display each photo
    photos.forEach(photo => {
        const photoElement = document.createElement('div');
        photoElement.className = 'artwork-photo-item';
        
        // Check if this is the primary image by comparing with artwork table image
        const isPrimaryFromArtworkTable = primaryImageName && photo.photo_path === primaryImageName;
        const isPrimary = photo.is_primary || isPrimaryFromArtworkTable;
        
        if (isPrimary) {
            photoElement.className = 'artwork-photo-item primary-photo';
        }
        
        photoElement.innerHTML = `
            <img src="${photo.photo_url}" alt="${photo.alt_text || 'Artwork photo'}" class="artwork-photo-thumb">
            ${isPrimary ? '<div class="primary-badge"><i class="fas fa-star"></i> Primary</div>' : ''}
            <div class="photo-actions">
                <button type="button" class="photo-action-btn delete-photo-btn" onclick="deleteArtworkPhoto(${photo.photo_id})" title="Delete Photo">
                    <i class="fas fa-trash"></i>
                </button>
                ${!isPrimary ? `<button type="button" class="photo-action-btn set-primary-btn" onclick="setAsPrimaryPhoto(${photo.photo_id})" title="Set as Primary">
                    <i class="fas fa-star"></i>
                </button>` : ''}
            </div>
        `;
        
        photosGrid.appendChild(photoElement);
    });
}

// Function to delete an artwork photo
window.deleteArtworkPhoto = function(photoId) {
    if (!confirm('Are you sure you want to delete this photo? This action cannot be undone.')) {
        return;
    }
    
    console.log('Deleting photo ID:', photoId);
    
    fetch('./API/deleteArtworkPhoto.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        credentials: 'include',
        body: JSON.stringify({ photo_id: photoId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the photo element from the grid
            const photoElement = document.querySelector(`[onclick="deleteArtworkPhoto(${photoId})"]`).closest('.artwork-photo-item');
            if (photoElement) {
                photoElement.remove();
            }
            
            // Show success message
            alert('Photo deleted successfully!');
        } else {
            alert('Failed to delete photo: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error deleting photo:', error);
        alert('Failed to delete photo: ' + error.message);
    });
};

// Function to set a photo as primary
window.setAsPrimaryPhoto = function(photoId) {
    console.log('Setting photo as primary, ID:', photoId);
    
    fetch('./API/setArtworkPrimaryPhoto.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        credentials: 'include',
        body: JSON.stringify({ photo_id: photoId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the photos display
            const artworkId = document.getElementById('editArtworkId').value;
            if (artworkId) {
                // Re-fetch artwork data to refresh the photos
                openEditModal(artworkId);
            }
            
            alert('Primary photo updated successfully!');
        } else {
            alert('Failed to set primary photo: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error setting primary photo:', error);
        alert('Failed to set primary photo: ' + error.message);
    });
};

// Simple initialization - no complex logic
function initializeEditArtworkForm() {
    console.log('Initializing edit artwork with simple approach');
    
    // Remove any existing listeners first to prevent duplicates
    document.removeEventListener('click', handleEditArtworkClick);
    document.removeEventListener('submit', handleEditFormSubmit);
    
    // Add fresh event listeners
    document.addEventListener('click', handleEditArtworkClick);
    document.addEventListener('submit', handleEditFormSubmit);
    
    // Initialize edit form validation
    initializeEditArtworkValidation();
}

// Initialize validation for edit artwork form
function initializeEditArtworkValidation() {
    const editFields = [
        { id: 'editArtworkTitle', indicator: 'editArtworkTitleIndicator', error: 'editArtworkTitleError' },
        { id: 'editArtworkPrice', indicator: 'editArtworkPriceIndicator', error: 'editArtworkPriceError' },
        { id: 'editArtworkCategory', indicator: 'editArtworkCategoryIndicator', error: 'editArtworkCategoryError' },
        { id: 'editArtworkStyle', indicator: 'editArtworkStyleIndicator', error: 'editArtworkStyleError' },
        { id: 'editArtworkMedium', indicator: 'editArtworkMediumIndicator', error: 'editArtworkMediumError' },
        { id: 'editArtworkWidth', indicator: 'editArtworkWidthIndicator', error: 'editArtworkWidthError' },
        { id: 'editArtworkHeight', indicator: 'editArtworkHeightIndicator', error: 'editArtworkHeightError' },
        { id: 'editArtworkDepth', indicator: 'editArtworkDepthIndicator', error: 'editArtworkDepthError' },
        { id: 'editArtworkYear', indicator: 'editArtworkYearIndicator', error: 'editArtworkYearError' },
        { id: 'editArtworkDescription', indicator: 'editArtworkDescriptionIndicator', error: 'editArtworkDescriptionError' },
        { id: 'editArtworkAvailable', indicator: 'editArtworkAvailableIndicator', error: 'editArtworkAvailableError' },
        { id: 'editArtworkAuction', indicator: 'editArtworkAuctionIndicator', error: 'editArtworkAuctionError' }
    ];

    editFields.forEach(({ id, indicator, error }) => {
        const fieldElement = document.getElementById(id);
        const indicatorElement = document.getElementById(indicator);
        const errorElement = document.getElementById(error);

        if (fieldElement) {
            // Remove existing event listeners by cloning the element
            const newFieldElement = fieldElement.cloneNode(true);
            fieldElement.parentNode.replaceChild(newFieldElement, fieldElement);
            
            newFieldElement.addEventListener('input', function() {
                validateEditArtworkField(newFieldElement, indicatorElement, errorElement);
            });
            
            newFieldElement.addEventListener('blur', function() {
                validateEditArtworkField(newFieldElement, indicatorElement, errorElement);
            });
            
            newFieldElement.addEventListener('change', function() {
                validateEditArtworkField(newFieldElement, indicatorElement, errorElement);
            });
        }
    });
    
    // Add character counter for description
    const descriptionField = document.getElementById('editArtworkDescription');
    const charCountElement = document.getElementById('editDescCharCount');
    
    if (descriptionField && charCountElement) {
        descriptionField.addEventListener('input', () => {
            const charCount = descriptionField.value.length;
            charCountElement.textContent = charCount;
            
            if (charCount > 1000) {
                charCountElement.style.color = '#e74c3c';
            } else {
                charCountElement.style.color = '#666';
            }
        });
    }
}

// Edit artwork field validation function
function validateEditArtworkField(field, indicator, errorElement) {
    const fieldId = field.id;
    
    switch (fieldId) {
        case 'editArtworkTitle':
            const title = field.value.trim();
            if (!title) {
                clearFieldState(field, indicator, errorElement);
            } else if (/^\d+$/.test(title)) {
                setFieldError(field, indicator, errorElement, 'Title should contain words, not just numbers');
            } else if (title.length < 3) {
                setFieldError(field, indicator, errorElement, 'Title should be at least 3 characters long');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'editArtworkPrice':
            const price = parseFloat(field.value);
            if (!field.value.trim()) {
                clearFieldState(field, indicator, errorElement);
            } else if (isNaN(price) || price <= 0) {
                setFieldError(field, indicator, errorElement, 'Price must be a positive number');
            } else if (price > 3000000) {
                setFieldError(field, indicator, errorElement, 'Price cannot exceed 3,000,000 EGP');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'editArtworkCategory':
        case 'editArtworkStyle':
            if (!field.value) {
                clearFieldState(field, indicator, errorElement);
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'editArtworkMedium':
            // Medium is optional, so no validation required
            clearFieldState(field, indicator, errorElement);
            break;
            
        case 'editArtworkWidth':
        case 'editArtworkHeight':
            const dimension = parseFloat(field.value);
            if (!field.value.trim()) {
                clearFieldState(field, indicator, errorElement);
            } else if (isNaN(dimension) || dimension <= 0) {
                setFieldError(field, indicator, errorElement, 'Must be a positive number');
            } else if (dimension > 1000) {
                setFieldError(field, indicator, errorElement, 'Maximum size is 1000 cm');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'editArtworkDepth':
            const depth = parseFloat(field.value);
            if (!field.value.trim()) {
                clearFieldState(field, indicator, errorElement);
            } else if (isNaN(depth) || depth < 0) {
                setFieldError(field, indicator, errorElement, 'Must be a positive number or empty');
            } else if (depth > 1000) {
                setFieldError(field, indicator, errorElement, 'Maximum depth is 1000 cm');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'editArtworkYear':
            const year = parseInt(field.value);
            const currentYear = new Date().getFullYear();
            if (!field.value.trim()) {
                clearFieldState(field, indicator, errorElement);
            } else if (isNaN(year) || year < 1800 || year > currentYear) {
                setFieldError(field, indicator, errorElement, `Year must be between 1800 and ${currentYear}`);
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'editArtworkDescription':
            const description = field.value.trim();
            if (!description) {
                clearFieldState(field, indicator, errorElement);
            } else if (description.length < 10) {
                setFieldError(field, indicator, errorElement, 'Description should be at least 10 characters long');
            } else if (description.length > 1000) {
                setFieldError(field, indicator, errorElement, 'Description cannot exceed 1000 characters');
            } else {
                setFieldValid(field, indicator, errorElement);
            }
            break;
            
        case 'editArtworkAvailable':
        case 'editArtworkAuction':
            // These are select fields with predefined options, always valid
            setFieldValid(field, indicator, errorElement);
            break;
            
        default:
            clearFieldState(field, indicator, errorElement);
    }
}

// Separate handler functions to ensure they can be properly removed/added
function handleEditArtworkClick(e) {
    // Handle edit button clicks
    if (e.target.closest('.edit-btn')) {
        e.preventDefault();
        const btn = e.target.closest('.edit-btn');
        const artworkId = btn.getAttribute('data-artwork-id');
        if (artworkId) {
            console.log('Edit clicked for artwork:', artworkId);
            editArtwork(artworkId);
        }
        return;
    }
    
    // Handle modal close
    if (e.target.classList.contains('modalClose') || e.target.closest('.modalClose')) {
        const modal = e.target.closest('.modal');
        if (modal && modal.id === 'editArtworkModal') {
            closeModal('editArtworkModal');
        }
        return;
    }
}

function handleEditFormSubmit(e) {
    console.log('handleEditFormSubmit called', e.target.id);
    if (e.target.id === 'editArtworkForm') {
        e.preventDefault();
        console.log('Calling handleEditArtworkSubmit');
        handleEditArtworkSubmit(e);
    }
}

// Basic edit artwork function
function editArtwork(id) {
    console.log('editArtwork called with ID:', id);
    
    const modal = document.getElementById('editArtworkModal');
    if (!modal) {
        alert('Edit modal not found');
        return;
    }
    
    // Always reset modal state first
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
    
    // Reset form
    const form = document.getElementById('editArtworkForm');
    if (form) {
        form.reset();
    }
    
    // Simple API call with cache busting
    fetch(`./API/getArtworkInfo.php?id=${id}&t=${Date.now()}`, {
        credentials: 'include',
        headers: {
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache'
        }
    })
    .then(response => {
        console.log('API Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('API Response data:', data);
        if (data.success) {
            // Populate form
            const artwork = data.data;
            document.getElementById('editArtworkId').value = artwork.artwork_id || '';
            document.getElementById('editArtworkTitle').value = artwork.title || '';
            document.getElementById('editArtworkType').value = artwork.type || '';
            document.getElementById('editArtworkPrice').value = artwork.price || '';
            document.getElementById('editArtworkYear').value = artwork.year || '';
            document.getElementById('editArtworkDimensions').value = artwork.dimensions || '';
            document.getElementById('editArtworkMaterial').value = artwork.material || '';
            document.getElementById('editArtworkDescription').value = artwork.description || '';
            document.getElementById('editArtworkAvailable').value = artwork.is_available ? '1' : '0';
            document.getElementById('editArtworkAuction').value = artwork.on_auction ? '1' : '0';
            
            // Handle image preview if exists
            const img = document.getElementById('editArtworkImg');
            const text = document.querySelector('#editArtworkImagePreview .file-input-text');
            if (img && text && artwork.artwork_image_url) {
                img.src = artwork.artwork_image_url;
                img.style.display = 'block';
                text.textContent = 'Change Image';
            }
            
            // Display artwork photos gallery
            displayArtworkPhotos(artwork.photos || [], artwork.artwork_image);
            
            // Small delay to ensure form is populated before opening
            setTimeout(() => {
                // Open modal
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                console.log('Modal opened successfully for artwork:', id);
            }, 100);
            
        } else {
            alert('Failed to load artwork: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load artwork: ' + error.message);
    });
}

// Direct form population function
function populateEditFormDirect(artwork) {
    console.log('Populating form with artwork:', artwork);
    
    // Helper function to safely set field values
    const setField = (id, value) => {
        const element = document.getElementById(id);
        if (element) {
            element.value = value || '';
        } else {
            console.warn(`Field ${id} not found`);
        }
    };
    
    // Populate all fields
    setField('editArtworkId', artwork.artwork_id);
    setField('editArtworkTitle', artwork.title);
    setField('editArtworkType', artwork.type);
    setField('editArtworkPrice', artwork.price);
    setField('editArtworkYear', artwork.year);
    setField('editArtworkDimensions', artwork.dimensions);
    setField('editArtworkMaterial', artwork.material);
    setField('editArtworkDescription', artwork.description);
    setField('editArtworkAvailable', artwork.is_available ? '1' : '0');
    setField('editArtworkAuction', artwork.on_auction ? '1' : '0');
    
    // Handle image preview
    const img = document.getElementById('editArtworkImg');
    const text = document.querySelector('#editArtworkImagePreview .file-input-text');
    
    if (img && text) {
        if (artwork.artwork_image_url) {
            img.src = artwork.artwork_image_url;
            img.style.display = 'block';
            text.textContent = 'Change Image';
        } else {
            img.style.display = 'none';
            text.textContent = 'Choose Image';
        }
    }
}

// Function to reset edit artwork modal
function resetEditArtworkModal() {
    console.log('Resetting edit artwork modal');
    
    // Reset the form
    const form = document.getElementById('editArtworkForm');
    if (form) {
        form.reset();
    }
    
    // Reset image preview
    const img = document.getElementById('editArtworkImg');
    const text = document.querySelector('#editArtworkImagePreview .file-input-text');
    if (img) {
        img.style.display = 'none';
        img.src = '';
    }
    if (text) {
        text.textContent = 'Choose Image';
    }
    
    // Ensure modal is closed
    const modal = document.getElementById('editArtworkModal');
    if (modal) {
        modal.classList.remove('active');
    }
    
    // Reset body overflow
    document.body.style.overflow = 'auto';
    
    console.log('Edit artwork modal reset completed');
}

// Function to open a modal
function openModal(modalId) {
    console.log('Opening modal:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        // Ensure modal is properly reset first
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
        
        // Force a reflow to ensure the removal took effect
        modal.offsetHeight;
        
        // Add active class and set body overflow
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        console.log('Modal opened successfully:', modalId);
        
        // Verify modal is actually visible
        setTimeout(() => {
            const isVisible = modal.classList.contains('active');
            console.log(`Modal ${modalId} visibility check:`, isVisible);
            if (!isVisible) {
                console.error('Modal failed to open properly, retrying...');
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }, 100);
    } else {
        console.error('Modal not found:', modalId);
    }
}

// Function to close a modal
function closeModal(modalId) {
    console.log('Closing modal:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
        console.log('Modal closed successfully:', modalId);
        
        // Reset specific modal states
        if (modalId === 'editArtworkModal') {
            // Reset form immediately
            const form = document.getElementById('editArtworkForm');
            if (form) {
                form.reset();
            }
            
            // Reset image preview
            const img = document.getElementById('editArtworkImg');
            const text = document.querySelector('#editArtworkImagePreview .file-input-text');
            if (img) {
                img.style.display = 'none';
                img.src = '';
            }
            if (text) {
                text.textContent = 'Choose Image';
            }
            
            console.log('Edit artwork modal reset completed');
        }
    } else {
        console.error('Modal not found:', modalId);
    }
}

// Function to load artwork data for editing
async function loadArtworkForEdit(artworkId) {
    try {
        console.log('Loading artwork for edit, ID:', artworkId);
        
        // Ensure modal exists before proceeding
        const modal = document.getElementById('editArtworkModal');
        if (!modal) {
            throw new Error('Edit modal not found in DOM');
        }
        
        const response = await fetch(`./API/getArtworkInfo.php?id=${artworkId}&t=${Date.now()}`, {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0'
            }
        });
        
        console.log('API response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('API response data:', data);
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to load artwork details');
        }
        
        const artwork = data.data;
        
        // Populate form first
        populateEditForm(artwork);
        
        // Then open modal
        openModal('editArtworkModal');
        
        console.log('Artwork loaded and modal opened successfully');
        
    } catch (error) {
        console.error('Error loading artwork for edit:', error);
        showNotification('Failed to load artwork details. Please try again.', 'error');
        
        // Ensure modal is closed on error
        closeModal('editArtworkModal');
    }
}

// Function to populate the edit form with artwork data
function populateEditForm(artwork) {
    console.log('Populating edit form with artwork data:', artwork);
    
    try {
        // Basic artwork information
        const setFieldValue = (id, value) => {
            const element = document.getElementById(id);
            if (element) {
                element.value = value || '';
            } else {
                console.warn(`Element with ID '${id}' not found`);
            }
        };
        
        setFieldValue('editArtworkId', artwork.artwork_id);
        setFieldValue('editArtworkTitle', artwork.title);
        setFieldValue('editArtworkType', artwork.type);
        setFieldValue('editArtworkPrice', artwork.price);
        setFieldValue('editArtworkYear', artwork.year);
        setFieldValue('editArtworkDimensions', artwork.dimensions);
        setFieldValue('editArtworkMaterial', artwork.material);
        setFieldValue('editArtworkDescription', artwork.description);
        setFieldValue('editArtworkAvailable', artwork.is_available ? '1' : '0');
        setFieldValue('editArtworkAuction', artwork.on_auction ? '1' : '0');
        
        // Handle artwork image preview
        const artworkImg = document.getElementById('editArtworkImg');
        const fileText = document.querySelector('#editArtworkImagePreview .file-input-text');
        
        if (artworkImg && fileText) {
            if (artwork.artwork_image_url) {
                artworkImg.src = artwork.artwork_image_url;
                artworkImg.style.display = 'block';
                fileText.textContent = 'Change Image';
            } else {
                artworkImg.style.display = 'none';
                artworkImg.src = '';
                fileText.textContent = 'Choose Image';
            }
        }
        
        console.log('Form populated successfully');
        
    } catch (error) {
        console.error('Error populating edit form:', error);
        throw error;
    }
}

// Function to handle edit form submission
async function handleEditArtworkSubmit(event) {
    console.log('handleEditArtworkSubmit called');
    
    // Check if SweetAlert is available
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 (Swal) is not loaded!');
        alert('Please wait for the page to fully load and try again.');
        return;
    }
    
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Debug: Log form data
    console.log('Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ':', value);
    }
    
    try {
        // Show loading state with SweetAlert
        console.log('Showing SweetAlert loading...');
        
        Swal.fire({
            title: '🎨 Saving Your Changes...',
            text: 'We\'re updating your artwork with the new information. This will just take a moment!',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            background: '#fff',
            color: '#333',
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        const response = await fetch('./API/updateArtwork.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to update artwork');
        }
        
        // Get artwork title for success message
        const artworkTitle = data.data && data.data.artwork && data.data.artwork.title 
            ? data.data.artwork.title 
            : form.querySelector('[name="title"]')?.value || 'artwork';
        
        // Show success message with SweetAlert
        Swal.fire({
            title: 'Artwork Updated Successfully',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423',
            timer: 3000,
            timerProgressBar: true,
            showClass: {
                popup: 'animate__animated animate__bounceIn'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOut'
            }
        }).then(() => {
            // Close modal after success
            closeModal('editArtworkModal');
            
            // Reload statistics to show updated data
            if (currentSection === 'statistics') {
                loadArtistStatistics();
            }
        });
        
    } catch (error) {
        console.error('Error updating artwork:', error);
        
        // Show error message with SweetAlert
        Swal.fire({
            title: '😔 Oops! Something Went Wrong',
            text: `Don't worry! ${error.message || 'We couldn\'t save your changes right now, but you can try again.'} Your artwork information is still safe.`,
            icon: 'error',
            confirmButtonText: 'I\'ll Try Again',
            confirmButtonColor: '#6B4423',
            showCancelButton: true,
            cancelButtonText: 'Maybe Later',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
            footer: '<i class="fas fa-lightbulb"></i> Tip: Make sure all required fields are filled out correctly!'
        }).then((result) => {
            if (result.isConfirmed) {
                // User wants to try again, keep the modal open
                console.log('User chose to try again');
            } else {
                // User chose to cancel, maybe close the modal
                console.log('User chose to try later');
            }
        });
    }
}

function deleteArtwork(id) {
    Swal.fire({
        title: 'Delete Artwork?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#c5534a',
        cancelButtonColor: '#6b4423',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            try {
                // Call the delete API directly with artwork_id
                const deleteResponse = await fetch('./API/deleteArtwork.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    credentials: 'include',
                    body: JSON.stringify({ artwork_id: id })
                });
                
                const deleteText = await deleteResponse.text();
                console.log('Delete response text:', deleteText);
                
                if (!deleteText || deleteText.trim() === '') {
                    throw new Error('Empty response from server. Please check server logs.');
                }
                
                if (!deleteResponse.ok) {
                    try {
                        const errorData = JSON.parse(deleteText);
                        throw new Error(errorData.message || `HTTP error! status: ${deleteResponse.status}`);
                    } catch (parseError) {
                        throw new Error(`HTTP error! status: ${deleteResponse.status}. Response: ${deleteText}`);
                    }
                }
                
                const deleteData = JSON.parse(deleteText);
                console.log('Delete response data:', deleteData);
                
                if (!deleteData.success) {
                    throw new Error(deleteData.message || 'Failed to delete artwork');
                }
                
                return deleteData;
                
            } catch (error) {
                console.error('Delete artwork error:', error);
                Swal.showValidationMessage(`Request failed: ${error.message}`);
                return false;
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            // Success - show success message
            Swal.fire({
                title: 'Deleted!',
                text: 'Your artwork has been deleted successfully.',
                icon: 'success',
                confirmButtonColor: '#6b4423'
            });
            
            // Refresh the statistics section to update the artwork list
            if (currentSection === 'statistics') {
                loadArtistStatistics();
            }
            
            return Swal.fire({
                title: 'Success!',
                text: 'Artwork deleted successfully',
                icon: 'success',
                confirmButtonColor: '#6b4423'
            });
        }
    });
}

function editVirtualGallery(id) {
    showNotification(`Editing virtual gallery ${id}`, 'info');
}

function deleteVirtualGallery(id) {
    deleteGallery(id, 'Virtual Gallery');
}

function editLocalGallery(id) {
    showNotification(`Editing local gallery ${id}`, 'info');
}

function deleteLocalGallery(id) {
    deleteGallery(id, 'Local Gallery');
}

// Generic gallery deletion function
function deleteGallery(id, galleryType) {
    Swal.fire({
        title: `Delete ${galleryType}?`,
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#c5534a',
        cancelButtonColor: '#6b4423',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            try {
                // Call the delete API directly with gallery_id
                const deleteResponse = await fetch('./API/deleteGallery.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    credentials: 'include',
                    body: `gallery_id=${id}`
                });
                
                const deleteText = await deleteResponse.text();
                console.log('Delete gallery response text:', deleteText);
                
                if (!deleteText || deleteText.trim() === '') {
                    throw new Error('Empty response from server. Please check server logs.');
                }
                
                if (!deleteResponse.ok) {
                    try {
                        const errorData = JSON.parse(deleteText);
                        throw new Error(errorData.message || `HTTP error! status: ${deleteResponse.status}`);
                    } catch (parseError) {
                        throw new Error(`HTTP error! status: ${deleteResponse.status}. Response: ${deleteText}`);
                    }
                }
                
                const deleteData = JSON.parse(deleteText);
                console.log('Delete gallery response data:', deleteData);
                
                if (!deleteData.success) {
                    throw new Error(deleteData.message || 'Failed to delete gallery');
                }
                
                return deleteData;
                
            } catch (error) {
                console.error('Delete gallery error:', error);
                Swal.showValidationMessage(`Request failed: ${error.message}`);
                return false;
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            // Success - show success message
            Swal.fire({
                title: 'Deleted!',
                text: `Your ${galleryType.toLowerCase()} has been deleted successfully.`,
                icon: 'success',
                confirmButtonColor: '#6b4423'
            });
            
            // Refresh the statistics section to update the gallery list
            if (currentSection === 'statistics') {
                loadArtistStatistics();
            }
            
            return Swal.fire({
                title: 'Success!',
                text: `${galleryType} deleted successfully`,
                icon: 'success',
                confirmButtonColor: '#6b4423'
            });
        }
    });
}

function editAuction(id) {
    showNotification(`Editing auction ${id}`, 'info');
}

function deleteAuction(id) {
    Swal.fire({
        title: 'Delete Auction?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#c5534a',
        cancelButtonColor: '#6b4423',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            try {
                // Call the delete API directly with auction_id
                const deleteResponse = await fetch('./API/deleteAuction.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    credentials: 'include',
                    body: `auction_id=${id}`
                });
                
                const deleteText = await deleteResponse.text();
                console.log('Delete auction response text:', deleteText);
                
                if (!deleteText || deleteText.trim() === '') {
                    throw new Error('Empty response from server. Please check server logs.');
                }
                
                if (!deleteResponse.ok) {
                    try {
                        const errorData = JSON.parse(deleteText);
                        throw new Error(errorData.message || `HTTP error! status: ${deleteResponse.status}`);
                    } catch (parseError) {
                        throw new Error(`HTTP error! status: ${deleteResponse.status}. Response: ${deleteText}`);
                    }
                }
                
                const deleteData = JSON.parse(deleteText);
                console.log('Delete auction response data:', deleteData);
                
                if (!deleteData.success) {
                    throw new Error(deleteData.message || 'Failed to delete auction');
                }
                
                return deleteData;
                
            } catch (error) {
                console.error('Delete auction error:', error);
                Swal.showValidationMessage(`Request failed: ${error.message}`);
                return false;
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            // Success - show success message
            Swal.fire({
                title: 'Deleted!',
                text: 'Your auction has been deleted successfully.',
                icon: 'success',
                confirmButtonColor: '#6b4423'
            });
            
            // Refresh the statistics section to update the auction list
            if (currentSection === 'statistics') {
                loadArtistStatistics();
            }
            
            return Swal.fire({
                title: 'Success!',
                text: 'Auction deleted successfully',
                icon: 'success',
                confirmButtonColor: '#6b4423'
            });
        }
    });
}

// ==========================================
// UTILITY FUNCTIONS
// ==========================================

// =====================================
// ARTIST PROFILE MANAGEMENT
// =====================================

// =====================================
// PROFILE UPDATE FUNCTIONS
// =====================================

// Function to update artist profile
async function updateArtistProfile(profileData) {
    console.log('🚀 Starting profile update with data:', profileData);
    console.log('🔍 Bio data specifically:', {
        bio: profileData.bio,
        bio_length: profileData.bio ? profileData.bio.length : 0,
        bio_type: typeof profileData.bio
    });
    
    try {
        console.log('📡 Making fetch request to API...');
        
        // Ensure we have the correct API path
        const apiUrl = './API/updateArtistProfile.php';
        console.log('🌐 API URL:', apiUrl);
        
        const response = await fetch(apiUrl, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(profileData)
        });

        console.log('📥 Response received:', {
            status: response.status,
            statusText: response.statusText,
            ok: response.ok,
            url: response.url
        });

        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        console.log('📋 Content-Type:', contentType);
        
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('❌ Non-JSON response received:', text);
            throw new Error('Server returned non-JSON response. Check server logs for PHP errors.');
        }

        const result = await response.json();
        console.log('📋 Parsed result:', result);

        if (result.success) {
            // Show success message with SweetAlert
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Profile Saved Successfully!',
                    text: 'Your artist profile has been updated with the latest information.',
                    confirmButtonText: 'Perfect!',
                    confirmButtonColor: '#8B5A3C',
                    timer: 3500,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'swal-success-popup',
                        title: 'swal-success-title',
                        confirmButton: 'swal-confirm-btn'
                    }
                });
            } else {
                alert('Profile updated successfully!');
            }
            
            // Reload profile data to reflect changes
            if (typeof loadArtistProfile === 'function') {
                loadArtistProfile();
            }
            
            return true;
        } else {
            throw new Error(result.message || 'Update failed');
        }

    } catch (error) {
        console.error('❌ Error updating profile:', error);
        console.error('📊 Error details:', {
            message: error.message,
            stack: error.stack,
            name: error.name
        });
        
        // Show error message with SweetAlert
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Unable to Save Profile',
                text: 'We encountered an issue while saving your profile changes. Please check your connection and try again.',
                confirmButtonText: 'Try Again',
                confirmButtonColor: '#8B5A3C',
                customClass: {
                    popup: 'swal-error-popup',
                    title: 'swal-error-title',
                    confirmButton: 'swal-confirm-btn'
                }
            });
        } else {
            alert('Error: ' + (error.message || 'Unable to update profile'));
        }
        
        return false;
    }
}

// Function to save profile form data
async function saveProfileChanges() {
    console.log('💾 Save profile changes triggered');
    
    try {
        // Check if we're in the right context
        const profileSection = document.getElementById('profile-section');
        if (!profileSection) {
            console.error('❌ Profile section not found');
            return;
        }
        
        // Collect form data - using the correct IDs from the HTML
        const profileData = {};
        
        // Safely get form values
        const artistBio = document.getElementById('artistBio');
        if (artistBio) {
            profileData.bio = artistBio.value || '';
            console.log('📝 Bio field value:', JSON.stringify(artistBio.value));
            console.log('📝 Bio field length:', artistBio.value ? artistBio.value.length : 0);
            console.log('📝 Bio field raw value:', artistBio.value);
            console.log('📝 profileData.bio set to:', JSON.stringify(profileData.bio));
        } else {
            console.warn('❌ artistBio element not found');
            console.log('📋 All textarea elements found:', document.querySelectorAll('textarea').length);
            console.log('📋 Elements with artistBio:', document.querySelectorAll('#artistBio').length);
        }
        
        const artistPhone = document.getElementById('artistPhone');
        if (artistPhone) {
            profileData.phone_number = artistPhone.value || '';
            console.log('📱 Phone field value:', artistPhone.value);
        } else {
            console.warn('❌ artistPhone element not found');
        }
        
        const artistEmail = document.getElementById('artistEmail');
        if (artistEmail) {
            profileData.email = artistEmail.value || '';
            console.log('📧 Email field value:', artistEmail.value);
        } else {
            console.warn('❌ artistEmail element not found');
        }
        
        const artistSpecialty = document.getElementById('artistSpecialty');
        if (artistSpecialty) profileData.art_specialty = artistSpecialty.value || '';
        
        const artistExperience = document.getElementById('artistExperience');
        if (artistExperience) {
            profileData.years_of_experience = mapExperienceValueToNumber(artistExperience.value || '');
        }
        
        const location = document.getElementById('location');
        if (location) profileData.location = location.value || '';
        
        const education = document.getElementById('education');
        if (education) profileData.education = education.value || '';

        console.log('📝 Collected form data:', profileData);
        console.log('🔍 Detailed data analysis:');
        console.log('  - bio:', JSON.stringify(profileData.bio));
        console.log('  - email:', JSON.stringify(profileData.email));
        console.log('  - phone_number:', JSON.stringify(profileData.phone_number));
        console.log('  - art_specialty:', JSON.stringify(profileData.art_specialty));
        console.log('📊 Data keys:', Object.keys(profileData));
        console.log('🔍 Form elements found:', {
            artistBio: !!artistBio,
            artistPhone: !!artistPhone,
            artistEmail: !!artistEmail,
            artistSpecialty: !!artistSpecialty,
            artistExperience: !!artistExperience,
            location: !!location,
            education: !!education
        });

        // Validate that we have some data to save
        if (Object.keys(profileData).length === 0) {
            console.warn('⚠️ No form data found to save');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Profile Form Not Found',
                    text: 'We couldn\'t locate your profile information form. Please make sure you\'re on the profile management page.',
                    confirmButtonText: 'Got It',
                    confirmButtonColor: '#8B5A3C',
                    customClass: {
                        popup: 'swal-info-popup',
                        title: 'swal-info-title',
                        confirmButton: 'swal-confirm-btn'
                    }
                });
            }
            return;
        }

        // Validate required fields
        if (!profileData.email) {
            console.warn('⚠️ Email validation failed');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Email Required',
                    text: 'Please provide your email address to save your profile. This helps potential clients contact you.',
                    confirmButtonText: 'Add Email',
                    confirmButtonColor: '#8B5A3C',
                    customClass: {
                        popup: 'swal-warning-popup',
                        title: 'swal-warning-title',
                        confirmButton: 'swal-confirm-btn'
                    }
                });
            } else {
                alert('Email address is required.');
            }
            return;
        }

        console.log('✅ Validation passed, calling updateArtistProfile...');
        // Update profile
        await updateArtistProfile(profileData);
        
    } catch (error) {
        console.error('❌ Error in saveProfileChanges:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Profile Save Error',
                text: 'Something unexpected happened while saving your profile. Please refresh the page and try again.',
                confirmButtonText: 'Refresh Page',
                confirmButtonColor: '#8B5A3C',
                customClass: {
                    popup: 'swal-error-popup',
                    title: 'swal-error-title',
                    confirmButton: 'swal-confirm-btn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                }
            });
        } else {
            alert('Error saving profile: ' + error.message);
        }
    }
}

// Function to map experience dropdown value to numeric value
function mapExperienceValueToNumber(experienceValue) {
    switch (experienceValue) {
        case '1-2': return 2;
        case '3-5': return 5;
        case '6-10': return 10;
        case '10+': return 15;
        default: return 0;
    }
}

// Function to add new achievement
async function addNewAchievement() {
    const { value: achievementText } = await Swal.fire({
        title: 'Share Your Achievement',
        input: 'textarea',
        inputPlaceholder: 'Describe your accomplishment or milestone...',
        inputAttributes: {
            'aria-label': 'Enter your achievement'
        },
        showCancelButton: true,
        confirmButtonText: 'Add Achievement',
        cancelButtonText: 'Maybe Later',
        confirmButtonColor: '#8B5A3C',
        cancelButtonColor: '#6C757D',
        customClass: {
            popup: 'swal-custom-popup',
            title: 'swal-custom-title',
            confirmButton: 'swal-confirm-btn',
            cancelButton: 'swal-cancel-btn'
        },
        inputValidator: (value) => {
            if (!value || value.trim() === '') {
                return 'Please describe your achievement to add it to your profile!'
            }
        }
    });

    if (achievementText) {
        const profileData = {
            achievements: {
                operation: 'add',
                achievement: achievementText.trim()
            }
        };

        const success = await updateArtistProfile(profileData);
        if (success) {
            // Add to UI immediately
            createAchievementItem(achievementText.trim());
        }
    }
}

// Function to delete achievement
async function deleteAchievement(achievementText, achievementElement) {
    const result = await Swal.fire({
        title: 'Remove Achievement',
        text: 'This achievement will be permanently removed from your profile. Are you sure you want to continue?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#8B5A3C',
        cancelButtonColor: '#6C757D',
        confirmButtonText: 'Remove Achievement',
        cancelButtonText: 'Keep It',
        customClass: {
            popup: 'swal-custom-popup',
            title: 'swal-custom-title',
            confirmButton: 'swal-confirm-btn',
            cancelButton: 'swal-cancel-btn'
        }
    });

    if (result.isConfirmed) {
        const profileData = {
            achievements: {
                operation: 'delete',
                achievement: achievementText
            }
        };

        const success = await updateArtistProfile(profileData);
        if (success && achievementElement) {
            achievementElement.remove();
        }
    }
}

// Enhanced createAchievementItem function with delete functionality
function createAchievementItem(achievementData) {
    const achievementsList = document.getElementById('achievementsList');
    if (!achievementsList) return;
    
    // Handle both old format (string) and new format (object)
    let achievementText, achievementId;
    
    if (typeof achievementData === 'string') {
        achievementText = achievementData;
        achievementId = null; // For legacy achievements without ID
    } else if (typeof achievementData === 'object' && achievementData !== null) {
        achievementText = achievementData.achievement_name || achievementData.text;
        achievementId = achievementData.achievement_id || achievementData.id || null;
        
        // If we couldn't extract text from the object, skip this item
        if (!achievementText) {
            console.error('❌ Object achievement missing text property:', achievementData);
            return;
        }
    } else {
        console.error('❌ Invalid achievement data format:', achievementData);
        return;
    }
    
    const achievementDiv = document.createElement('div');
    achievementDiv.className = 'achievementItem';
    if (achievementId) {
        achievementDiv.setAttribute('data-achievement-id', achievementId);
    }
    achievementDiv.innerHTML = `
        <span>${achievementText}</span>
        <button type="button" class="removeAchievement" title="Remove achievement">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add remove functionality
    const removeBtn = achievementDiv.querySelector('.removeAchievement');
    removeBtn.addEventListener('click', function() {
        removeAchievement(achievementDiv, achievementId, achievementText);
    });
    
    achievementsList.appendChild(achievementDiv);
}

// Function to handle the Add Achievement button functionality
async function handleAddAchievementClick() {
    const newAchievementInput = document.getElementById('newAchievement');
    if (!newAchievementInput) return;
    
    const achievementText = newAchievementInput.value.trim();
    if (!achievementText) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Achievement Required',
                text: 'Please describe your achievement before adding it to your profile.',
                confirmButtonText: 'Got It',
                confirmButtonColor: '#8B5A3C',
                customClass: {
                    popup: 'swal-info-popup',
                    title: 'swal-info-title',
                    confirmButton: 'swal-confirm-btn'
                }
            });
        }
        return;
    }
    
    // Add achievement via API
    const profileData = {
        achievements: {
            operation: 'add',
            achievement: achievementText
        }
    };
    
    try {
        const success = await updateArtistProfile(profileData);
        if (success) {
            // Clear the input
            newAchievementInput.value = '';
            // Add to UI immediately
            createAchievementItem(achievementText);
        }
    } catch (error) {
        console.error('Error adding achievement:', error);
    }
}

// Function to initialize save button functionality
function initializeProfileSaveButton() {
    console.log('🔧 Initializing profile save button...');
    
    try {
        // Look for the artist info form specifically
        const artistForm = document.getElementById('artistInfoForm');
        console.log('🔍 Artist info form found:', !!artistForm);
        
        if (artistForm) {
            console.log('✅ Artist form found, attaching submit event listener');
            // Remove existing listeners to prevent duplicates
            artistForm.removeEventListener('submit', handleFormSubmit);
            artistForm.addEventListener('submit', handleFormSubmit);
        } else {
            console.warn('⚠️ Artist info form not found!');
        }
        
        // Also look for any save buttons specifically
        const saveButtons = [
            document.querySelector('#artistInfoForm button[type="submit"]'),
            // Find button by text content - more flexible search
            ...Array.from(document.querySelectorAll('button')).filter(btn => 
                btn.textContent && (
                    btn.textContent.includes('Save Profile') || 
                    btn.textContent.includes('Save') ||
                    btn.textContent.trim() === 'Save Profile'
                )
            ),
            document.querySelector('[data-action="save"]')
        ].filter(Boolean); // Remove null/undefined entries
        
        saveButtons.forEach((btn, index) => {
            if (btn) {
                console.log(`✅ Save button ${index + 1} found, attaching click event`);
                // Remove existing listeners to prevent duplicates
                btn.removeEventListener('click', handleButtonClick);
                btn.addEventListener('click', handleButtonClick);
            }
        });

        // Look for add achievement button
        const addAchievementBtn = document.querySelector('#addAchievementBtn');
        console.log('🏆 Add achievement button found:', !!addAchievementBtn);
        
        if (addAchievementBtn) {
            addAchievementBtn.removeEventListener('click', handleAchievementClick);
            addAchievementBtn.addEventListener('click', handleAchievementClick);
        }
        
        // Also handle existing remove achievement buttons
        const removeButtons = document.querySelectorAll('.removeAchievement');
        removeButtons.forEach(btn => {
            // Remove existing listener first
            btn.removeEventListener('click', btn._handleRemove);
            // Create new handler with proper context binding
            btn._handleRemove = function(e) {
                const achievementText = this.parentElement.querySelector('span').textContent;
                deleteAchievement(achievementText, this.parentElement);
            }.bind(btn);
            btn.addEventListener('click', btn._handleRemove);
        });
        
    } catch (error) {
        console.error('❌ Error initializing profile save button:', error);
    }
}

// Separate event handlers to prevent duplicate listeners
function handleFormSubmit(e) {
    console.log('🖱️ Form submit triggered!');
    e.preventDefault();
    saveProfileChanges();
}

function handleButtonClick(e) {
    console.log('🖱️ Save button clicked!');
    e.preventDefault();
    saveProfileChanges();
}

function handleAchievementClick(e) {
    console.log('🖱️ Add achievement button clicked!');
    e.preventDefault();
    handleAddAchievementClick();
}

// Initialize profile update functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Profile update initialization starting...');
    
    // Initialize all profile functionality
    setTimeout(() => {
        initializeProfileSaveButton();
        
        // Load profile data immediately if we're on the profile section
        const profileSection = document.getElementById('profile-section');
        if (profileSection && profileSection.classList.contains('active')) {
            console.log('📍 Profile section is active on page load, loading profile data...');
            loadArtistProfile();
        }
        
        // Re-initialize when profile section becomes active
        const profileLink = document.querySelector('[data-section="profile"]');
        if (profileLink) {
            profileLink.addEventListener('click', function() {
                console.log('📍 Profile section clicked, re-initializing...');
                setTimeout(() => {
                    initializeProfileSaveButton();
                    loadArtistProfile();
                }, 200);
            });
        }
    }, 100);
});

// Function to load artist profile information
async function loadArtistProfile() {
    try {
        console.log('Loading artist profile from cookie...');
        
        // Fetch artist information (API will read user ID from cookie)
        const profileResponse = await fetch(`./API/getArtistInfo.php`, {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!profileResponse.ok) {
            throw new Error('Failed to load profile data');
        }

        const profileData = await profileResponse.json();

        if (profileData.success && profileData.data) {
            console.log('Profile loaded successfully:', profileData.data);
            populateProfileForm(profileData.data);
        } else {
            console.error('Failed to load profile:', profileData.message);
            throw new Error(profileData.message || 'Failed to load profile');
        }

    } catch (error) {
        console.error('Error loading artist profile:', error);
        // Show user-friendly error message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error Loading Profile',
                text: 'Unable to load your profile information. Please refresh the page and try again.',
                confirmButtonText: 'OK'
            });
        }
    }
}

// Function to populate the profile form with data
function populateProfileForm(artistData) {
    console.log('Populating form with data:', artistData);
    
    // Update profile picture
    const profilePreview = document.getElementById('profilePreview');
    if (profilePreview && artistData.profile_picture) {
        profilePreview.src = artistData.profile_picture;
    }
    
    // Update artist name in profile info section using the new ID
    const profileNameDisplay = document.getElementById('profileNameDisplay');
    if (profileNameDisplay && artistData.full_name) {
        profileNameDisplay.textContent = artistData.full_name;
    }
    
    // Update specialty in profile info section using the new ID
    const profileSpecialtyDisplay = document.getElementById('profileSpecialtyDisplay');
    if (profileSpecialtyDisplay) {
        if (artistData.art_specialty && artistData.art_specialty.trim() !== '') {
            profileSpecialtyDisplay.textContent = artistData.art_specialty.charAt(0).toUpperCase() + artistData.art_specialty.slice(1) + ' Artist';
        } else {
            profileSpecialtyDisplay.textContent = 'General Artist';
        }
    }
    
    // Update profile stats with real data
    const ratingBadge = document.querySelector('.profileStats .statBadge:first-child');
    if (ratingBadge && artistData.average_rating !== undefined) {
        const rating = artistData.average_rating > 0 ? artistData.average_rating : 'New';
        ratingBadge.innerHTML = `<i class="fas fa-star"></i> ${rating} Rating`;
    }
    
    const artworkBadge = document.querySelector('.profileStats .statBadge:last-child');
    if (artworkBadge && artistData.artwork_count !== undefined) {
        artworkBadge.innerHTML = `<i class="fas fa-palette"></i> ${artistData.artwork_count} Artworks`;
    }
    
    // Update artist name in form (disabled field)
    const artistNameField = document.getElementById('artistName');
    if (artistNameField && artistData.full_name) {
        artistNameField.value = artistData.full_name;
    }
    
    // Update bio/about me
    const artistBio = document.getElementById('artistBio');
    if (artistBio) {
        const bioValue = artistData.bio || '';
        artistBio.value = bioValue;
        console.log('📝 Bio populated with:', bioValue);
        console.log('📝 Bio length:', bioValue.length);
        
        // Update character count
        const charCount = document.getElementById('bioCharCount');
        if (charCount) {
            charCount.textContent = bioValue.length;
            console.log('📝 Bio character count updated to:', bioValue.length);
        }
    } else {
        console.warn('❌ Bio field not found:', {
            bioElement: !!artistBio
        });
    }
    
    // Update email field
    const artistEmail = document.getElementById('artistEmail');
    if (artistEmail && artistData.email) {
        artistEmail.value = artistData.email;
    }
    
    // Update phone field (if phone data is available)
    const artistPhone = document.getElementById('artistPhone');
    if (artistPhone && artistData.phone) {
        artistPhone.value = artistData.phone;
    } else if (artistPhone && artistData.phone_number) {
        artistPhone.value = artistData.phone_number;
    }
    
    // Update art specialty
    const artistSpecialty = document.getElementById('artistSpecialty');
    if (artistSpecialty) {
        console.log('Art specialty value:', artistData.art_specialty, 'Type:', typeof artistData.art_specialty);
        if (artistData.art_specialty && artistData.art_specialty.trim() !== '' && artistData.art_specialty !== 'null') {
            artistSpecialty.value = artistData.art_specialty;
        } else {
            // Reset to placeholder option when art specialty is null or empty
            artistSpecialty.selectedIndex = 0; // Select first option (placeholder)
            console.log('Setting to placeholder, selectedIndex:', artistSpecialty.selectedIndex);
        }
    }
    
    // Update years of experience
    const artistExperience = document.getElementById('artistExperience');
    if (artistExperience && artistData.years_of_experience) {
        // Map numeric value to option value
        const experienceValue = mapExperienceToValue(artistData.years_of_experience);
        artistExperience.value = experienceValue;
    }
    
    // Load achievements from API instead of using the legacy field
    loadAndPopulateAchievements();
    
    console.log('Profile form populated successfully');
}

// Function to map numeric experience to dropdown value
function mapExperienceToValue(years) {
    if (years <= 2) return '1-2';
    if (years <= 5) return '3-5';
    if (years <= 10) return '6-10';
    return '10+';
}

// Function to load and populate achievements from API
async function loadAndPopulateAchievements() {
    try {
        console.log('🏆 Loading achievements from API...');
        
        const response = await fetch('./API/getAchievements.php', {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        if (result.success) {
            console.log('✅ Achievements loaded successfully:', result.data);
            populateAchievements(result.data);
        } else {
            console.warn('⚠️ No achievements found or error:', result.message);
            // Clear the list if no achievements
            const achievementsList = document.getElementById('achievementsList');
            if (achievementsList) {
                achievementsList.innerHTML = '';
            }
        }
        
    } catch (error) {
        console.error('❌ Failed to load achievements:', error);
        // Don't show error to user for achievements loading failure
    }
}

// Function to populate achievements list from API data
function populateAchievements(achievementsData) {
    const achievementsList = document.getElementById('achievementsList');
    if (!achievementsList) return;
    
    // Clear existing achievements
    achievementsList.innerHTML = '';
    
    if (!achievementsData || achievementsData.length === 0) {
        return;
    }
    
    // Handle new API format (array of objects with achievement_id and achievement_name)
    achievementsData.forEach(achievement => {
        if (typeof achievement === 'object' && achievement !== null) {
            createAchievementItem(achievement);
        } else if (typeof achievement === 'string') {
            // Handle legacy string format by converting to object format
            createAchievementItem({
                achievement_id: null,
                achievement_name: achievement
            });
        }
    });
}

// Initialize profile management when DOM is loaded
function initializeProfileManagement() {
    console.log('🔧 Initializing profile management...');
    
    // Load profile data when user navigates to profile section
    const profileLink = document.querySelector('[data-section="profile"]');
    if (profileLink) {
        profileLink.addEventListener('click', function() {
            console.log('📍 Profile section clicked, loading profile data...');
            // Small delay to ensure section is visible
            setTimeout(loadArtistProfile, 200);
        });
    }
    
    // Also load if we're already on the profile section
    if (window.location.hash === '#profile-section' || 
        document.getElementById('profile-section')?.classList.contains('active')) {
        console.log('📍 Already on profile section, loading profile data...');
        loadArtistProfile();
    }
    
    // Load profile data immediately if profile section is visible
    const profileSection = document.getElementById('profile-section');
    if (profileSection && (profileSection.style.display !== 'none' && !profileSection.hidden)) {
        console.log('📍 Profile section is visible, loading profile data...');
        setTimeout(loadArtistProfile, 100);
    }
    
    // Bio character counter functionality
    const artistBio = document.getElementById('artistBio');
    const bioCharCount = document.getElementById('bioCharCount');
    
    if (artistBio && bioCharCount) {
        artistBio.addEventListener('input', function() {
            bioCharCount.textContent = this.value.length;
        });
    }
}

// Add profile management initialization to the main DOMContentLoaded event
document.addEventListener('DOMContentLoaded', function() {
    // Initialize profile management
    initializeProfileManagement();
});

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="closeNotification(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add to page
    let container = document.querySelector('.notification-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'notification-container';
        document.body.appendChild(container);
    }
    
    container.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}

function getNotificationIcon(type) {
    switch (type) {
        case 'success': return 'fa-check-circle';
        case 'error': return 'fa-exclamation-circle';
        case 'warning': return 'fa-exclamation-triangle';
        default: return 'fa-info-circle';
    }
}

function closeNotification(button) {
    const notification = button.closest('.notification');
    if (notification && notification.parentNode) {
        notification.parentNode.removeChild(notification);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

function formatType(type) {
    return type.replace(/_/g, ' ').replace(/\w\S*/g, (txt) => {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}

// Notification system
function showNotification(message, type = 'info') {
    // Simple notification using SweetAlert for consistency
    const iconType = type === 'error' ? 'error' : type === 'success' ? 'success' : 'info';
    
    Swal.fire({
        title: message,
        icon: iconType,
        confirmButtonText: 'OK',
        confirmButtonColor: '#6B4423',
        timer: 3000,
        timerProgressBar: true,
        toast: true,
        position: 'top-end',
        showConfirmButton: false
    });
}

// ==========================================
// INITIALIZATION
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeMobileMenu();
    initializeSidebarNavigation();
    initializeDashboard();
    initializeReviews();
    initializeOrders();
    initializeProfile();
    initializeArtworkForm();
    initializeAuction();
    initializeGalleryEvents();
    initializeStatistics();
    initializeEditArtworkForm();
    
});

// Handle page visibility changes
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        // Page is hidden
        closeSidebar();
    }
});

// ==========================================
// GALLERY IMAGE UPLOAD FUNCTIONALITY
// ==========================================

function initializeGalleryImageUpload() {
    // Initialize virtual primary image upload
    const virtualPrimaryUploadZone = document.getElementById('virtualPrimaryUploadZone');
    const virtualPrimaryFileInput = document.getElementById('virtualPrimaryImage');
    
    if (virtualPrimaryUploadZone && virtualPrimaryFileInput) {
        virtualPrimaryUploadZone.addEventListener('click', function() {
            virtualPrimaryFileInput.click();
        });
        
        virtualPrimaryUploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        virtualPrimaryUploadZone.addEventListener('dragleave', function() {
            this.classList.remove('dragover');
        });
        
        virtualPrimaryUploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            handlePrimaryImage(e.dataTransfer.files, 'virtual');
        });
        
        virtualPrimaryFileInput.addEventListener('change', function() {
            handlePrimaryImage(this.files, 'virtual');
        });
    }
    
    // Initialize physical primary image upload
    const physicalPrimaryUploadZone = document.getElementById('physicalPrimaryUploadZone');
    const physicalPrimaryFileInput = document.getElementById('physicalPrimaryImage');
    
    if (physicalPrimaryUploadZone && physicalPrimaryFileInput) {
        physicalPrimaryUploadZone.addEventListener('click', function() {
            physicalPrimaryFileInput.click();
        });
        
        physicalPrimaryUploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        physicalPrimaryUploadZone.addEventListener('dragleave', function() {
            this.classList.remove('dragover');
        });
        
        physicalPrimaryUploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            handlePrimaryImage(e.dataTransfer.files, 'physical');
        });
        
        physicalPrimaryFileInput.addEventListener('change', function() {
            handlePrimaryImage(this.files, 'physical');
        });
    }

    // Initialize virtual gallery image upload
    const virtualUploadZone = document.getElementById('virtualGalleryUploadZone');
    const virtualFileInput = document.getElementById('virtualGalleryImages');
    
    if (virtualUploadZone && virtualFileInput) {
        virtualUploadZone.addEventListener('click', function() {
            virtualFileInput.click();
        });
        
        virtualUploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        virtualUploadZone.addEventListener('dragleave', function() {
            this.classList.remove('dragover');
        });
        
        virtualUploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            handleGalleryFiles(e.dataTransfer.files, 'virtual');
        });
        
        virtualFileInput.addEventListener('change', function() {
            handleGalleryFiles(this.files, 'virtual');
        });
    }
    
    // Initialize physical gallery image upload
    const physicalUploadZone = document.getElementById('physicalGalleryUploadZone');
    const physicalFileInput = document.getElementById('physicalGalleryImages');
    
    if (physicalUploadZone && physicalFileInput) {
        physicalUploadZone.addEventListener('click', function() {
            physicalFileInput.click();
        });
        
        physicalUploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        physicalUploadZone.addEventListener('dragleave', function() {
            this.classList.remove('dragover');
        });
        
        physicalUploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            handleGalleryFiles(e.dataTransfer.files, 'physical');
        });
        
        physicalFileInput.addEventListener('change', function() {
            handleGalleryFiles(this.files, 'physical');
        });
    }
}

function handleGalleryFiles(files, type) {
    const maxFiles = 15;
    const currentImages = galleryUploadedFiles.length;
    
    if (currentImages + files.length > maxFiles) {
        Swal.fire({
            title: 'Too Many Files',
            text: `You can upload a maximum of ${maxFiles} images. You currently have ${currentImages} images.`,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423'
        });
        return;
    }
    
    const uploadedImages = type === 'virtual' ? 
        document.getElementById('virtualUploadedImages') : 
        document.getElementById('physicalUploadedImages');
    
    processGalleryFilesWithValidation(files, uploadedImages, type);
}

async function processGalleryFilesWithValidation(files, uploadedImages, type) {
    for (let file of files) {
        // Validate file type only
        const validation = validateGalleryImageFile(file);
        if (!validation.valid) {
            Swal.fire({
                title: 'Invalid File',
                text: validation.message,
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6B4423'
            });
            continue;
        }
        
        // Add to uploaded files array
        galleryUploadedFiles.push(file);
        
        // Create preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const imagePreview = document.createElement('div');
            imagePreview.classList.add('artworkImagePreview');
            
            imagePreview.innerHTML = `
                <div class="imageContainer">
                    <img src="${e.target.result}" alt="Gallery Image" class="previewImage">
                    <div class="imageOverlay">
                        <button type="button" class="removeImageBtn" onclick="removeGalleryImage(this, '${type}')" title="Remove Image">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <div class="imageInfo">
                        ${Math.round(file.size / 1024)}KB
                    </div>
                    <div class="imageLabel">Gallery Image</div>
                </div>
            `;
            
            uploadedImages.appendChild(imagePreview);
        };
        reader.readAsDataURL(file);
    }
}

function handlePrimaryImage(files, type) {
    if (files.length === 0) return;
    
    // Only take the first file for primary image
    const file = files[0];
    
    // Validate file type
    const validation = validateGalleryImageFile(file);
    if (!validation.valid) {
        Swal.fire({
            title: 'Invalid File',
            text: validation.message,
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6B4423'
        });
        return;
    }
    
    // Store the primary image
    galleryPrimaryImage = file;
    
    // Get the preview container
    const previewContainer = type === 'virtual' ? 
        document.getElementById('virtualPrimaryImagePreview') : 
        document.getElementById('physicalPrimaryImagePreview');
    
    // Clear previous preview
    previewContainer.innerHTML = '';
    
    // Create preview
    const reader = new FileReader();
    reader.onload = function(e) {
        const imagePreview = document.createElement('div');
        imagePreview.classList.add('artworkImagePreview');
        
        imagePreview.innerHTML = `
            <div class="imageContainer">
                <img src="${e.target.result}" alt="Primary Gallery Image" class="previewImage">
                <div class="imageOverlay">
                    <button type="button" class="removeImageBtn" onclick="removePrimaryImage('${type}')" title="Remove Image">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                <div class="imageInfo">
                    ${Math.round(file.size / 1024)}KB
                </div>
                <div class="imageLabel">Primary Image</div>
            </div>
        `;
        
        previewContainer.appendChild(imagePreview);
    };
    reader.readAsDataURL(file);
}

function validateGalleryImageFile(file) {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!allowedTypes.includes(file.type)) {
        return {
            valid: false,
            message: 'Invalid file type. Please upload JPG, PNG, GIF, or WebP images only.'
        };
    }
    
    return { valid: true };
}

function removeGalleryImage(button, type) {
    const imagePreview = button.closest('.artworkImagePreview');
    const index = Array.from(imagePreview.parentNode.children).indexOf(imagePreview);
    
    // Remove from uploaded files array
    galleryUploadedFiles.splice(index, 1);
    
    // Remove preview element
    imagePreview.remove();
}

function removePrimaryImage(type) {
    // Clear the primary image
    galleryPrimaryImage = null;
    
    // Get the preview container and clear it
    const previewContainer = type === 'virtual' ? 
        document.getElementById('virtualPrimaryImagePreview') : 
        document.getElementById('physicalPrimaryImagePreview');
    
    previewContainer.innerHTML = '';
    
    // Reset the file input
    const fileInput = type === 'virtual' ? 
        document.getElementById('virtualPrimaryImage') : 
        document.getElementById('physicalPrimaryImage');
    
    if (fileInput) {
        fileInput.value = '';
    }
}

function resetGalleryForm() {
    // Reset form
    document.getElementById('addGalleryEventForm').reset();
    
    // Clear uploaded images
    galleryUploadedFiles = [];
    galleryPrimaryImage = null; // Clear primary image
    
    const virtualUploadedImages = document.getElementById('virtualUploadedImages');
    const physicalUploadedImages = document.getElementById('physicalUploadedImages');
    const virtualPrimaryImagePreview = document.getElementById('virtualPrimaryImagePreview');
    const physicalPrimaryImagePreview = document.getElementById('physicalPrimaryImagePreview');
    
    if (virtualUploadedImages) {
        virtualUploadedImages.innerHTML = '';
    }
    if (physicalUploadedImages) {
        physicalUploadedImages.innerHTML = '';
    }
    if (virtualPrimaryImagePreview) {
        virtualPrimaryImagePreview.innerHTML = '';
    }
    if (physicalPrimaryImagePreview) {
        physicalPrimaryImagePreview.innerHTML = '';
    }
    
    // Reset variables
    currentGalleryStep = 1;
    selectedEventType = null;
    virtualTags = [];
    
    // Clear virtual tags display
    const virtualTagsList = document.getElementById('virtualTagsList');
    if (virtualTagsList) {
        virtualTagsList.innerHTML = '';
    }
    
    // Reset character counters
    const virtualDescCounter = document.getElementById('virtualDescCharCount');
    const physicalDescCounter = document.getElementById('physicalDescCharCount');
    if (virtualDescCounter) virtualDescCounter.textContent = '0';
    if (physicalDescCounter) physicalDescCounter.textContent = '0';
    
    // Reset type selection
    document.querySelectorAll('.typeOption').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Hide all sections except the type selection
    const eventDetailsSection = document.getElementById('eventDetailsSection');
    const virtualDetails = document.getElementById('virtualEventDetails');
    const physicalDetails = document.getElementById('physicalEventDetails');
    const previewSection = document.getElementById('eventPreviewSection');
    
    if (eventDetailsSection) eventDetailsSection.style.display = 'none';
    if (virtualDetails) virtualDetails.style.display = 'none';
    if (physicalDetails) physicalDetails.style.display = 'none';
    if (previewSection) previewSection.style.display = 'none';

    // Clear uploaded gallery images
    galleryUploadedFiles = [];
    const virtualUploadedImagesReset = document.getElementById('virtualUploadedImages');
    const physicalUploadedImagesReset = document.getElementById('physicalUploadedImages');
    if (virtualUploadedImagesReset) virtualUploadedImagesReset.innerHTML = '';
    if (physicalUploadedImagesReset) physicalUploadedImagesReset.innerHTML = '';

    // Reset all validation indicators and error messages
    const allGalleryFieldIds = [
        'virtualEventTitle', 'virtualEventDescription', 'virtualEventPrice', 
        'virtualEventDuration', 'virtualEventStartDate',
        'physicalEventTitle', 'physicalEventDescription', 'physicalEventPrice',
        'physicalEventStartDate', 'physicalEventPhone', 'physicalEventCity', 'physicalEventAddress'
    ];
    
    allGalleryFieldIds.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        const indicator = document.getElementById(fieldId + 'Indicator');
        const errorMessage = document.getElementById(fieldId + 'Error');
        
        if (field) {
            field.classList.remove('error', 'valid');
        }
        if (indicator) {
            indicator.className = 'inputIndicator';
        }
        if (errorMessage) {
            errorMessage.textContent = '';
        }
    });

    updateGalleryStepNavigation();
}

// ==========================================
// GALLERY FORM VALIDATION
// ==========================================

function initializeGalleryValidation() {
    // Virtual Event Validation
    const virtualEventTitle = document.getElementById('virtualEventTitle');
    const virtualEventDescription = document.getElementById('virtualEventDescription');
    const virtualEventPrice = document.getElementById('virtualEventPrice');
    const virtualEventDuration = document.getElementById('virtualEventDuration');
    const virtualEventStartDate = document.getElementById('virtualEventStartDate');
    
    // Physical Event Validation
    const physicalEventTitle = document.getElementById('physicalEventTitle');
    const physicalEventDescription = document.getElementById('physicalEventDescription');
    const physicalEventPrice = document.getElementById('physicalEventPrice');
    const physicalEventStartDate = document.getElementById('physicalEventStartDate');
    const physicalEventPhone = document.getElementById('physicalEventPhone');
    const physicalEventCity = document.getElementById('physicalEventCity');
    const physicalEventAddress = document.getElementById('physicalEventAddress');
    
    // Virtual Event Listeners
    if (virtualEventTitle) {
        virtualEventTitle.addEventListener('input', () => validateGalleryField('virtualEventTitle'));
        virtualEventTitle.addEventListener('blur', () => validateGalleryField('virtualEventTitle'));
    }
    
    if (virtualEventDescription) {
        virtualEventDescription.addEventListener('input', () => validateGalleryField('virtualEventDescription'));
        virtualEventDescription.addEventListener('blur', () => validateGalleryField('virtualEventDescription'));
    }
    
    if (virtualEventPrice) {
        virtualEventPrice.addEventListener('input', () => validateGalleryField('virtualEventPrice'));
        virtualEventPrice.addEventListener('blur', () => validateGalleryField('virtualEventPrice'));
    }
    
    if (virtualEventDuration) {
        virtualEventDuration.addEventListener('input', () => validateGalleryField('virtualEventDuration'));
        virtualEventDuration.addEventListener('blur', () => validateGalleryField('virtualEventDuration'));
    }
    
    if (virtualEventStartDate) {
        virtualEventStartDate.addEventListener('input', () => validateGalleryField('virtualEventStartDate'));
        virtualEventStartDate.addEventListener('blur', () => validateGalleryField('virtualEventStartDate'));
    }
    
    // Physical Event Listeners
    if (physicalEventTitle) {
        physicalEventTitle.addEventListener('input', () => validateGalleryField('physicalEventTitle'));
        physicalEventTitle.addEventListener('blur', () => validateGalleryField('physicalEventTitle'));
    }
    
    if (physicalEventDescription) {
        physicalEventDescription.addEventListener('input', () => validateGalleryField('physicalEventDescription'));
        physicalEventDescription.addEventListener('blur', () => validateGalleryField('physicalEventDescription'));
    }
    
    if (physicalEventPrice) {
        physicalEventPrice.addEventListener('input', () => validateGalleryField('physicalEventPrice'));
        physicalEventPrice.addEventListener('blur', () => validateGalleryField('physicalEventPrice'));
    }
    
    if (physicalEventStartDate) {
        physicalEventStartDate.addEventListener('input', () => validateGalleryField('physicalEventStartDate'));
        physicalEventStartDate.addEventListener('blur', () => validateGalleryField('physicalEventStartDate'));
    }
    
    if (physicalEventPhone) {
        physicalEventPhone.addEventListener('input', () => validateGalleryField('physicalEventPhone'));
        physicalEventPhone.addEventListener('blur', () => validateGalleryField('physicalEventPhone'));
    }
    
    if (physicalEventCity) {
        physicalEventCity.addEventListener('input', () => validateGalleryField('physicalEventCity'));
        physicalEventCity.addEventListener('blur', () => validateGalleryField('physicalEventCity'));
    }
    
    if (physicalEventAddress) {
        physicalEventAddress.addEventListener('input', () => validateGalleryField('physicalEventAddress'));
        physicalEventAddress.addEventListener('blur', () => validateGalleryField('physicalEventAddress'));
    }
}

function validateGalleryField(fieldId) {
    const field = document.getElementById(fieldId);
    const indicator = document.getElementById(fieldId + 'Indicator');
    const errorMessage = document.getElementById(fieldId + 'Error');
    
    if (!field || !indicator || !errorMessage) return true;
    
    const value = field.value.trim();
    let isValid = true;
    let errorText = '';
    
    // Clear previous states
    indicator.className = 'inputIndicator';
    errorMessage.textContent = '';
    field.classList.remove('error', 'valid');
    
    switch (fieldId) {
        case 'virtualEventTitle':
        case 'physicalEventTitle':
            if (!value) {
                isValid = false;
                errorText = 'Event title is required';
            } else if (value.length < 3) {
                isValid = false;
                errorText = 'Title must be at least 3 characters long';
            } else if (value.length > 100) {
                isValid = false;
                errorText = 'Title must be less than 100 characters';
            }
            break;
            
        case 'virtualEventDescription':
        case 'physicalEventDescription':
            if (!value) {
                isValid = false;
                errorText = 'Event description is required';
            } else if (value.length < 10) {
                isValid = false;
                errorText = 'Description must be at least 10 characters long';
            } else if (value.length > 1000) {
                isValid = false;
                errorText = 'Description must be less than 1000 characters';
            }
            break;
            
        case 'virtualEventPrice':
        case 'physicalEventPrice':
            if (value && (isNaN(value) || parseFloat(value) < 0)) {
                isValid = false;
                errorText = 'Price must be a valid positive number';
            }
            break;
            
        case 'virtualEventDuration':
            if (!value) {
                isValid = false;
                errorText = 'Duration is required';
            } else if (isNaN(value) || parseInt(value) < 1) {
                isValid = false;
                errorText = 'Duration must be at least 1 minute';
            } else if (parseInt(value) > 120) {
                isValid = false;
                errorText = 'Duration cannot exceed 120 minutes';
            }
            break;
            
        case 'virtualEventStartDate':
        case 'physicalEventStartDate':
            if (!value) {
                isValid = false;
                errorText = 'Start date and time is required';
            } else {
                const selectedDate = new Date(value);
                const now = new Date();
                if (selectedDate <= now) {
                    isValid = false;
                    errorText = 'Start date must be in the future';
                }
            }
            break;
            
        case 'physicalEventPhone':
            if (!value) {
                isValid = false;
                errorText = 'Contact phone is required';
            } else if (!/^[\+]?[0-9\s\-\(\)]{10,}$/.test(value)) {
                isValid = false;
                errorText = 'Please enter a valid phone number';
            }
            break;
            
        case 'physicalEventCity':
            if (!value) {
                isValid = false;
                errorText = 'City is required';
            } else if (value.length < 2) {
                isValid = false;
                errorText = 'City name must be at least 2 characters';
            }
            break;
            
        case 'physicalEventAddress':
            if (!value) {
                isValid = false;
                errorText = 'Gallery address is required';
            } else if (value.length < 10) {
                isValid = false;
                errorText = 'Please provide a detailed address (at least 10 characters)';
            }
            break;
    }
    
    // Update UI based on validation result
    if (isValid) {
        indicator.className = 'inputIndicator valid';
        field.classList.add('valid');
    } else {
        indicator.className = 'inputIndicator error';
        field.classList.add('error');
        errorMessage.textContent = errorText;
    }
    
    return isValid;
}

function validateAllGalleryFields() {
    const eventType = selectedEventType;
    if (!eventType) return false;
    
    let isValid = true;
    
    if (eventType === 'virtual') {
        const virtualFields = [
            'virtualEventTitle',
            'virtualEventDescription', 
            'virtualEventDuration',
            'virtualEventStartDate'
        ];
        
        virtualFields.forEach(fieldId => {
            if (!validateGalleryField(fieldId)) {
                isValid = false;
            }
        });
        
        // Validate price if provided
        const priceField = document.getElementById('virtualEventPrice');
        if (priceField && priceField.value.trim()) {
            if (!validateGalleryField('virtualEventPrice')) {
                isValid = false;
            }
        }
        
    } else if (eventType === 'physical') {
        const physicalFields = [
            'physicalEventTitle',
            'physicalEventDescription',
            'physicalEventStartDate',
            'physicalEventPhone',
            'physicalEventCity',
            'physicalEventAddress'
        ];
        
        physicalFields.forEach(fieldId => {
            if (!validateGalleryField(fieldId)) {
                isValid = false;
            }
        });
        
        // Validate price if provided
        const priceField = document.getElementById('physicalEventPrice');
        if (priceField && priceField.value.trim()) {
            if (!validateGalleryField('physicalEventPrice')) {
                isValid = false;
            }
        }
    }
    
    return isValid;
}