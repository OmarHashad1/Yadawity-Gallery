
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Portal - Yadawity Gallery</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom SweetAlert2 Z-Index Fix -->
    <style>
        /* Ensure SweetAlert appears above modals */
        .swal2-container {
            z-index: 999999 !important;
        }
        .swal2-popup {
            z-index: 999999 !important;
        }
        .swal2-backdrop-show {
            z-index: 999998 !important;
        }
        
        /* Make sure modal backdrop is lower */
        .modal {
            z-index: 1000;
        }
        .modal.active {
            z-index: 1000;
        }
    </style>
    
    <!-- Component Styles -->
    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css">
    <link rel="stylesheet" href="./public/artist-portal.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbarYadawity" id="yadawityNavbar">
        <div class="navContainer">
            <!-- Mobile Menu Icon (hidden on desktop) -->
            <button class="mobileMenuIcon" id="mobileMenuIcon">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="navLogo">
                <a href="index.html" class="navLogoLink">
                    <div class="logoIcon">
                        <svg width="40" height="40" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 50 Q15 30 25 25 Q35 20 45 35 Q40 45 35 50 Q40 55 45 65 Q35 80 25 75 Q15 70 20 50 Z" fill="currentColor" opacity="0.8"/>
                            <path d="M80 50 Q85 30 75 25 Q65 20 55 35 Q60 45 65 50 Q60 55 55 65 Q65 80 75 75 Q85 70 80 50 Z" fill="currentColor" opacity="0.8"/>
                            <line x1="50" y1="20" x2="50" y2="80" stroke="currentColor" stroke-width="3"/>
                            <path d="M50 20 Q45 15 42 12 M50 20 Q55 15 58 12" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                    </div>
                    <div class="logoText">
                        <span class="logoName">Yadawity</span>
                        <span class="logoEst">ARTIST PORTAL</span>
                    </div>
                </a>
            </div>
            <div class="navMenu" id="navMenu">
                <div class="artistBadge">
                    <i class="fas fa-palette"></i>
                    <span>ARTIST</span>
                </div>
                <a href="index.html" class="navLink">
                    <i class="fas fa-home"></i>
                    <span>BACK TO SITE</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Artist Sidebar -->
    <aside class="artistSidebar" id="artistSidebar">
        <div class="sidebarHeader">
            <h3>Artist Portal</h3>
            <button class="sidebarToggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <nav class="sidebarNav">
            <div class="navSection">
                <h4>OVERVIEW</h4>
                <a href="#dashboard-section" class="sidebarLink active" data-section="dashboard">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#statistics-section" class="sidebarLink" data-section="statistics">
                    <i class="fas fa-chart-bar"></i>
                    <span>My Statistics</span>
                </a>
                <a href="#reviews-section" class="sidebarLink" data-section="reviews">
                    <i class="fas fa-star"></i>
                    <span>User Reviews</span>
                </a>
            </div>

            <div class="navSection">
                <h4>SALES</h4>
                <a href="#orders-section" class="sidebarLink" data-section="orders">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Incoming Orders</span>
                </a>
            </div>

            <div class="navSection">
                <h4>MANAGEMENT</h4>
                <a href="#profile-section" class="sidebarLink" data-section="profile">
                    <i class="fas fa-user"></i>
                    <span>Manage Profile</span>
                </a>
                <a href="#artwork-section" class="sidebarLink" data-section="artwork">
                    <i class="fas fa-palette"></i>
                    <span>Add Artwork</span>
                </a>
                <a href="#gallery-section" class="sidebarLink" data-section="gallery">
                    <i class="fas fa-images"></i>
                    <span>Gallery Events</span>
                </a>
                <a href="#auction-section" class="sidebarLink" data-section="auction">
                    <i class="fas fa-gavel"></i>
                    <span>Auction Management</span>
                </a>
            </div>
        </nav>
    </aside>

    <main class="artistMain">
        <!-- Dashboard Section -->
        <div id="dashboard-section" class="content-section active">
            <div class="pageHeader">
                <div class="headerContent">
                    <h1>Artist Dashboard</h1>
                    <p>Welcome back! Here's your artistic journey overview.</p>
                </div>
                <div class="headerActions">
                    <button class="btn btn-secondary btn-sm" id="refreshBtn">
                        <i class="fas fa-sync-alt"></i>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="statsGrid">
                <div class="statCard">
                    <div class="statIcon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="statContent">
                        <h3 id="totalBalance">EGP 45,280</h3>
                        <p>Current Balance</p>
                    </div>
                </div>
                <div class="statCard">
                    <div class="statIcon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="statContent">
                        <h3 id="totalSales">127</h3>
                        <p>Total Sales</p>
                    </div>
                </div>
                <div class="statCard">
                    <div class="statIcon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="statContent">
                        <h3 id="avgRating">4.8</h3>
                        <p>Average Rating</p>
                    </div>
                </div>
                <div class="statCard">
                    <div class="statIcon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="statContent">
                        <h3 id="totalViews">8,451</h3>
                        <p>Total Views</p>
                    </div>
                </div>
            </div>

            <!-- Charts and Content -->
            <div class="dashboardGrid">
                <div class="contentCard chartCard">
                    <div class="cardHeader">
                        <h2>Sales Overview</h2>
                        <div class="cardActions">
                            <select class="filterSelect">
                                <option value="7">Last 7 days</option>
                                <option value="30">Last 30 days</option>
                                <option value="90">Last 3 months</option>
                            </select>
                        </div>
                    </div>
                    <div class="chartContainer">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <div class="contentCard">
                    <div class="cardHeader">
                        <h2>Recent Reviews</h2>
                    </div>
                    <div class="reviewsList">
                        <div class="reviewItem">
                            <div class="reviewHeader">
                                <div class="reviewAvatar">S</div>
                                <div class="reviewInfo">
                                    <span class="reviewerName">Sarah Ahmed</span>
                                    <div class="reviewRating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <span class="reviewTime">2 hours ago</span>
                            </div>
                            <p class="reviewText">Amazing artwork! The colors are vibrant and the technique is masterful.</p>
                        </div>
                        <div class="reviewItem">
                            <div class="reviewHeader">
                                <div class="reviewAvatar">M</div>
                                <div class="reviewInfo">
                                    <span class="reviewerName">Mohamed Hassan</span>
                                    <div class="reviewRating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                                <span class="reviewTime">1 day ago</span>
                            </div>
                            <p class="reviewText">Beautiful piece, exactly as described. Fast delivery too!</p>
                        </div>
                    </div>
                </div>

                <div class="contentCard">
                    <div class="cardHeader">
                        <h2>My Artworks Performance</h2>
                    </div>
                    <div class="artworkPerformance">
                        <div class="performanceItem">
                            <img src="./image/slide1.jpg" alt="Abstract Composition" class="performanceImage">
                            <div class="performanceInfo">
                                <h4>Abstract Composition</h4>
                                <div class="performanceStats">
                                    <span class="statItem"><i class="fas fa-eye"></i> 1,247 views</span>
                                    <span class="statItem"><i class="fas fa-heart"></i> 89 likes</span>
                                    <span class="statItem"><i class="fas fa-shopping-cart"></i> 3 sales</span>
                                </div>
                            </div>
                        </div>
                        <div class="performanceItem">
                            <img src="./image/photo-1554907984-15263bfd63bd.jpeg" alt="Modern Landscape" class="performanceImage">
                            <div class="performanceInfo">
                                <h4>Modern Landscape</h4>
                                <div class="performanceStats">
                                    <span class="statItem"><i class="fas fa-eye"></i> 892 views</span>
                                    <span class="statItem"><i class="fas fa-heart"></i> 56 likes</span>
                                    <span class="statItem"><i class="fas fa-shopping-cart"></i> 2 sales</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div id="statistics-section" class="content-section">
            <div class="statistics-container">
                <div class="statistics-header">
                    <h1><i class="fas fa-chart-bar"></i> My Statistics</h1>
                    <p>Overview of your artworks, galleries, and auctions performance</p>
                </div>

                <!-- Loading State -->
                <div id="statisticsLoading" style="display: none;">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading statistics...</p>
                    </div>
                </div>

                <!-- Content Container -->
                <div id="statisticsContent"></div>

                <!-- Statistics Dashboard Cards -->
                <div class="statsGrid">
                    <div class="statCard">
                        <div class="statIcon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="statContent">
                            <h3 id="total-revenue">EGP 0</h3>
                            <p>TOTAL REVENUE</p>
                        </div>
                    </div>
                    
                    <div class="statCard">
                        <div class="statIcon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <div class="statContent">
                            <h3 id="artwork-count">0</h3>
                            <p>ARTWORKS</p>
                        </div>
                    </div>
                    
                    <div class="statCard">
                        <div class="statIcon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="statContent">
                            <h3 id="galleries-count">0</h3>
                            <p>GALLERIES</p>
                        </div>
                    </div>
                    
                    <div class="statCard">
                        <div class="statIcon">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <div class="statContent">
                            <h3 id="auctions-count">0</h3>
                            <p>AUCTIONS</p>
                        </div>
                    </div>
                    
                    <div class="statCard">
                        <div class="statIcon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="statContent">
                            <h3 id="orders-count">0</h3>
                            <p>ORDERS</p>
                        </div>
                    </div>
                </div>

                <!-- Artworks Section -->
                <div class="stats-section">
                    <div class="stats-section-header">
                        <div class="stats-section-title">
                            <i class="fas fa-palette stats-section-icon"></i>
                            <h2>My Artworks</h2>
                            <span class="stats-count" id="artworks-count">0</span>
                        </div>
                    </div>
                    <div class="artworks-section-container">
                        <div class="stats-swiper artworks-swiper">
                            <div class="swiper-wrapper" id="artworks-container">
                                <!-- Artworks will be loaded here -->
                            </div>
                            <div class="swiper-pagination"></div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>
                </div>

                <!-- Virtual Galleries Section -->
                <div class="stats-section">
                    <div class="stats-section-header">
                        <div class="stats-section-title">
                            <i class="fas fa-desktop stats-section-icon"></i>
                            <h2>Virtual Galleries</h2>
                            <span class="stats-count" id="virtual-galleries-count">0</span>
                        </div>
                    </div>
                    <div class="artworks-section-container">
                        <div class="stats-swiper virtual-galleries-swiper">
                            <div class="swiper-wrapper" id="virtual-galleries-container">
                                <!-- Virtual galleries will be loaded here -->
                            </div>
                            <div class="swiper-pagination"></div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>
                </div>

                <!-- Local Galleries Section -->
                <div class="stats-section">
                    <div class="stats-section-header">
                        <div class="stats-section-title">
                            <i class="fas fa-building stats-section-icon"></i>
                            <h2>Local Galleries</h2>
                            <span class="stats-count" id="local-galleries-count">0</span>
                        </div>
                    </div>
                    <div class="artworks-section-container">
                        <div class="stats-swiper local-galleries-swiper">
                            <div class="swiper-wrapper" id="local-galleries-container">
                                <!-- Local galleries will be loaded here -->
                            </div>
                            <div class="swiper-pagination"></div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>
                </div>

                <!-- Auctions Section -->
                <div class="stats-section">
                    <div class="stats-section-header">
                        <div class="stats-section-title">
                            <i class="fas fa-gavel stats-section-icon"></i>
                            <h2>My Auctions</h2>
                            <span class="stats-count" id="auctions-count">0</span>
                        </div>
                    </div>
                    <div class="artworks-section-container">
                        <div class="stats-swiper auctions-swiper">
                            <div class="swiper-wrapper" id="auctions-container">
                                <!-- Auctions will be loaded here -->
                            </div>
                            <div class="swiper-pagination"></div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>
                </div>
                </div> <!-- End statisticsContent -->
            </div>
        </div>

        <!-- User Reviews Section -->
        <div id="reviews-section" class="content-section">
            <div class="pageHeader">
                <div class="headerContent">
                    <h1><i class="fas fa-star"></i> User Reviews</h1>
                    <p>See what customers are saying about your artwork and courses</p>
                </div>
                <div class="headerActions">
                    <button class="btn btn-secondary btn-sm" id="refreshReviewsBtn">
                        <i class="fas fa-sync-alt"></i>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Reviews Statistics -->
            <div class="statsGrid">
                <div class="statCard">
                    <div class="statIcon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="statContent">
                        <h3 id="averageRating">-</h3>
                        <p>Average Rating</p>
                    </div>
                </div>
                <div class="statCard">
                    <div class="statIcon">
                        <i class="fas fa-comment"></i>
                    </div>
                    <div class="statContent">
                        <h3 id="totalReviews">-</h3>
                        <p>Total Reviews</p>
                    </div>
                </div>
                <div class="statCard">
                    <div class="statIcon">
                        <i class="fas fa-thumbs-up"></i>
                    </div>
                    <div class="statContent">
                        <h3 id="positiveReviews">-</h3>
                        <p>Positive Reviews</p>
                    </div>
                </div>
                <div class="statCard">
                    <div class="statIcon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="statContent">
                        <h3 id="recentReviews">-</h3>
                        <p>This Month</p>
                    </div>
                </div>
            </div>

            <!-- Reviews Filter and Content -->
            <div class="reviewsContainer">
                <div class="reviewsFilters">
                    <div class="filterGroup">
                        <label for="reviewType">Filter by Type:</label>
                        <select id="reviewType" class="filterSelect">
                            <option value="all">All Reviews</option>
                            <option value="artwork">Artwork Reviews</option>
                            <option value="course">Course Reviews</option>
                        </select>
                    </div>
                    <div class="filterGroup">
                        <label for="ratingFilter">Filter by Rating:</label>
                        <select id="ratingFilter" class="filterSelect">
                            <option value="all">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
                    <div class="filterGroup">
                        <label for="dateFilter">Filter by Date:</label>
                        <select id="dateFilter" class="filterSelect">
                            <option value="all">All Time</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="reviewsLoading" class="loading-state" style="display: none;">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading reviews...</p>
                    </div>
                </div>

                <!-- Reviews List -->
                <div id="reviewsList" class="reviews-list">
                    <!-- Reviews will be loaded here dynamically -->
                </div>

                <!-- Pagination -->
                <div id="reviewsPagination" class="pagination-container" style="display: none;">
                    <button id="prevReviewsBtn" class="btn btn-secondary">
                        <i class="fas fa-chevron-left"></i>
                        Previous
                    </button>
                    <span id="reviewsPageInfo">Page 1 of 10</span>
                    <button id="nextReviewsBtn" class="btn btn-secondary">
                        Next
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Orders Section -->
        <div id="orders-section" class="content-section">
            <div class="pageHeader">
                <div class="headerContent">
                    <h1>Incoming Orders</h1>
                    <p>Track and manage your artwork orders.</p>
                </div>
                <div class="headerActions">
                    <button class="btn btn-primary btn-sm" id="exportOrdersBtn">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>

            <div class="controlsPanel">
                <div class="filterGroup">
                    <label>Status</label>
                    <select class="filterSelect" id="orderStatusFilter">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="paid">Paid</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="searchGroup">
                    <label>Search Orders</label>
                    <input type="text" class="searchInput" placeholder="Search by order number or customer..." id="orderSearch">
                </div>
                <div class="refreshGroup">
                    <button class="btn btn-outline btn-sm" id="refreshOrdersBtn">
                        <i class="fas fa-sync-alt"></i>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Orders Statistics Cards -->
            <div class="statsGrid" id="ordersStatsGrid">
                <div class="statCard">
                    <div class="statIcon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="statInfo">
                        <h3 id="totalOrdersCount">-</h3>
                        <p>Total Orders</p>
                    </div>
                </div>
                <div class="statCard">
                    <div class="statIcon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="statInfo">
                        <h3 id="totalRevenueAmount">-</h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                <div class="statCard">
                    <div class="statIcon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="statInfo">
                        <h3 id="pendingOrdersCount">-</h3>
                        <p>Pending Orders</p>
                    </div>
                </div>
                <div class="statCard">
                    <div class="statIcon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="statInfo">
                        <h3 id="deliveredOrdersCount">-</h3>
                        <p>Delivered</p>
                    </div>
                </div>
            </div>

            <div class="contentCard">
                <!-- Loading State -->
                <div class="loadingState" id="ordersLoadingState">
                    <div class="loadingSpinner">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <p>Loading your orders...</p>
                </div>

                <!-- Empty State -->
                <div class="emptyState" id="ordersEmptyState" style="display: none;">
                    <div class="emptyIcon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h3>No Orders Found</h3>
                    <p>You don't have any orders yet. Orders will appear here when customers purchase your artwork.</p>
                </div>

                <!-- Error State -->
                <div class="errorState" id="ordersErrorState" style="display: none;">
                    <div class="errorIcon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3>Unable to Load Orders</h3>
                    <p id="ordersErrorMessage">There was an error loading your orders. Please try again.</p>
                    <button class="btn btn-primary" onclick="loadArtistOrders()">
                        <i class="fas fa-retry"></i>
                        Try Again
                    </button>
                </div>

                <!-- Orders Table -->
                <div class="tableContainer" id="ordersTableContainer" style="display: none;">
                    <table class="dataTable">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Your Revenue</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                            <!-- Orders will be populated here -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div id="ordersPagination" class="pagination-container" style="display: none;">
                    <button id="prevOrdersBtn" class="btn btn-secondary">
                        <i class="fas fa-chevron-left"></i>
                        Previous
                    </button>
                    <span id="ordersPageInfo">Page 1 of 10</span>
                    <button id="nextOrdersBtn" class="btn btn-secondary">
                        Next
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Profile Section -->
        <div id="profile-section" class="content-section">
            <div class="pageHeader">
                <div class="headerContent">
                    <h1>Manage Profile</h1>
                    <p>Update your artist information and settings.</p>
                </div>
            </div>

            <div class="contentCard">
                <div class="cardHeader">
                    <h2><i class="fas fa-user-edit"></i> Artist Information</h2>
                    <div class="cardBadge">
                        <i class="fas fa-shield-check"></i>
                        Verified Artist
                    </div>
                </div>
                    <form class="modernForm" id="artistInfoForm">
                        <!-- Profile Picture Section -->
                        <div class="profilePictureSection">
                            <div class="currentProfilePicture">
                                <img src="./image/Artist-PainterLookingAtCamera.webp" alt="Profile Picture" id="profilePreview">
                                <div class="profileOverlay">
                                    <i class="fas fa-camera"></i>
                                    <span>Change Photo</span>
                                </div>
                            </div>
                            <div class="profilePictureInfo">
                                <h4 id="profileNameDisplay">Loading...</h4>
                                <p id="profileSpecialtyDisplay">Artist</p>
                                <div class="profileStats">
                                    <span class="statBadge"><i class="fas fa-star"></i> 4.8 Rating</span>
                                    <span class="statBadge"><i class="fas fa-palette"></i> 127 Artworks</span>
                                </div>
                            </div>
                            <div class="profilePictureUpload">
                                <input type="file" id="profilePicture" accept="image/*" hidden>
                                <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('profilePicture').click()">
                                    <i class="fas fa-upload"></i>
                                    Upload New Photo
                                </button>
                            </div>
                        </div>

                        <div class="formRow">
                            <div class="inputGroup">
                                <label for="artistName"><i class="fas fa-user"></i> Artist Name</label>
                                <input type="text" id="artistName" value="" disabled>
                                <div class="inputHelp">
                                    <i class="fas fa-info-circle"></i>
                                    Name cannot be changed. Contact support if needed.
                                </div>
                            </div>
                        </div>

                        <div class="formRow">
                            <div class="inputGroup">
                                <label for="artistBio"><i class="fas fa-quote-left"></i> About Me</label>
                                <textarea id="artistBio" rows="4" placeholder="Tell the world about your artistic journey..." maxlength="500"></textarea>
                                <div class="charCounter">
                                    <span id="bioCharCount">0</span>/500 characters
                                </div>
                            </div>
                        </div>

                        <div class="formRow">
                            <div class="inputGroup half">
                                <label for="artistPhone"><i class="fas fa-phone"></i> Phone Number</label>
                                <input type="tel" id="artistPhone" value="" placeholder="+20 XXX XXX XXXX">
                                <div class="validationIcon"></div>
                            </div>
                            <div class="inputGroup half">
                                <label for="artistEmail"><i class="fas fa-envelope"></i> Email Address</label>
                                <input type="email" id="artistEmail" value="" placeholder="your@email.com">
                                <div class="validationIcon"></div>
                            </div>
                        </div>

                        <div class="formRow">
                            <div class="inputGroup half">
                                <label for="artistSpecialty"><i class="fas fa-palette"></i> Art Specialty</label>
                                <select id="artistSpecialty">
                                    <option value="" disabled selected>Choose your art specialty</option>
                                    <option value="abstract">Abstract Art</option>
                                    <option value="realism">Realism</option>
                                    <option value="impressionism">Impressionism</option>
                                    <option value="expressionism">Expressionism</option>
                                    <option value="contemporary">Contemporary</option>
                                    <option value="traditional">Traditional</option>
                                    <option value="mixed">Mixed Media</option>
                                </select>
                            </div>
                            <div class="inputGroup half">
                                <label for="artistExperience"><i class="fas fa-clock"></i> Years of Experience</label>
                                <select id="artistExperience">
                                    <option value="1-2">1-2 years</option>
                                    <option value="3-5">3-5 years</option>
                                    <option value="6-10">6-10 years</option>
                                    <option value="10+" selected>10+ years</option>
                                </select>
                            </div>
                        </div>

                        <div class="formRow">
                            <div class="inputGroup">
                                <label for="artistAchievements"><i class="fas fa-trophy"></i> Achievements & Awards</label>
                                <div class="achievementsList" id="achievementsList">
                                    <div class="achievementItem">
                                        <span>Featured in Cairo Contemporary Art Exhibition 2024</span>
                                        <button type="button" class="removeAchievement"><i class="fas fa-times"></i></button>
                                    </div>
                                    <div class="achievementItem">
                                        <span>Winner of Best Abstract Painting Award 2023</span>
                                        <button type="button" class="removeAchievement"><i class="fas fa-times"></i></button>
                                    </div>
                                    <div class="achievementItem">
                                        <span>Solo exhibition at Alexandria Art Gallery 2022</span>
                                        <button type="button" class="removeAchievement"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                                <div class="addAchievement">
                                    <input type="text" id="newAchievement" placeholder="Add new achievement...">
                                    <button type="button" class="btn btn-outline btn-sm" id="addAchievementBtn">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="formActions">
                            <button type="button" class="btn btn-secondary">
                                <i class="fas fa-undo"></i>
                                Reset Changes
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Save Profile
                            </button>
                        </div>
                    </form>

                    <!-- Security & Privacy Section -->
                    <div class="cardHeader" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e5e5;">
                        <h2><i class="fas fa-shield-alt"></i> Security & Privacy</h2>
                        <div class="securityLevel">
                            <span class="securityBadge high">
                                <i class="fas fa-lock"></i>
                                High Security
                            </span>
                        </div>
                    </div>
                    <form class="modernForm" id="securityForm">
                     
                        <div class="formSection">
                            <h3><i class="fas fa-key"></i> Change Password</h3>
                            <div class="formRow">
                                <div class="inputGroup">
                                    <label for="currentPassword">Current Password</label>
                                    <div class="passwordInput">
                                        <input type="password" id="currentPassword" placeholder="Enter current password">
                                        <button type="button" class="passwordToggle" onclick="togglePassword('currentPassword')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="formRow">
                                <div class="inputGroup half">
                                    <label for="newPassword">New Password</label>
                                    <div class="passwordInput">
                                        <input type="password" id="newPassword" placeholder="Enter new password">
                                        <button type="button" class="passwordToggle" onclick="togglePassword('newPassword')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="passwordStrength" id="passwordStrength">
                                        <div class="strengthBar">
                                            <div class="strengthFill"></div>
                                        </div>
                                        <span class="strengthText">Password strength</span>
                                    </div>
                                </div>
                                <div class="inputGroup half">
                                    <label for="confirmPassword">Confirm New Password</label>
                                    <div class="passwordInput">
                                        <input type="password" id="confirmPassword" placeholder="Confirm new password">
                                        <button type="button" class="passwordToggle" onclick="togglePassword('confirmPassword')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="matchIndicator" id="matchIndicator"></div>
                                </div>
                            </div>
                        </div>

                        <div class="formActions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-shield-check"></i>
                                Update Security Settings
                            </button>
                        </div>
                    </form>
                </div>
        </div>

        <!-- Add Artwork Section -->
        <div id="artwork-section" class="content-section">
            <div class="pageHeader">
                <div class="headerContent">
                    <h1>Add New Artwork</h1>
                    <p>Share your latest creation with the world and start selling.</p>
                </div>
            </div>

            <div class="artworkFormContainer">
                <div class="formProgress">
                    <div class="progressStep active" data-step="1">
                        <div class="stepNumber">1</div>
                        <span>Artwork Info</span>
                    </div>
                    <div class="progressStep" data-step="2">
                        <div class="stepNumber">2</div>
                        <span>Images</span>
                    </div>
                    <div class="progressStep" data-step="3">
                        <div class="stepNumber">3</div>
                        <span>Preview</span>
                    </div>
                </div>

                <div class="contentCard">
                    <form class="modernForm artworkForm" id="addArtworkForm">
                        <!-- Step 1: Artwork Information -->
                        <div class="formStep active" data-step="1">
                            <div class="stepHeader">
                                <h2><i class="fas fa-info-circle"></i> Artwork Information</h2>
                                <p>Tell us about your artwork's details and specifications</p>
                            </div>

                            <div class="formRow">
                                <div class="inputGroup">
                                    <label for="artworkName"><i class="fas fa-signature"></i> Artwork Title *</label>
                                    <input type="text" id="artworkName" placeholder="Give your artwork a captivating title...">
                                    <div class="inputIndicator" id="artworkNameIndicator"></div>
                                    <div class="errorMessage" id="artworkNameError"></div>
                                    <div class="inputHelp">This will be the main title displayed to buyers</div>
                                </div>
                            </div>

                            <div class="formRow">
                                <div class="inputGroup half">
                                    <label for="artworkPrice"><i class="fas fa-tag"></i> Price (EGP) *</label>
                                    <div class="priceInput">
                                        <span class="currency">EGP</span>
                                        <input type="number" id="artworkPrice" placeholder="0.00" step="0.01">
                                    </div>
                                    <div class="inputIndicator" id="artworkPriceIndicator"></div>
                                    <div class="errorMessage" id="artworkPriceError"></div>
                                    <div class="priceHelp">
                                        <span class="priceNote">Yadawity fee: 15%</span>
                                        <span class="netPrice">You'll receive: EGP <span id="netPrice">0.00</span></span>
                                    </div>
                                </div>
                                <div class="inputGroup half">
                                    <label for="artworkCategory"><i class="fas fa-layer-group"></i> Category *</label>
                                    <select id="artworkCategory">
                                        <option value="">Select Category</option>
                                        <option value="paintings">Paintings</option>
                                        <option value="sculptures">Sculptures</option>
                                        <option value="textiles">Textiles</option>
                                        <option value="wooden_arts">Wooden Arts</option>
                                        <option value="ceramic_arts">Ceramic Arts</option>
                                        <option value="fiber_arts">Fiber Arts</option>
                                    </select>
                                    <div class="inputIndicator" id="artworkCategoryIndicator"></div>
                                    <div class="errorMessage" id="artworkCategoryError"></div>
                                </div>
                            </div>

                            <div class="formRow">
                                <div class="inputGroup half">
                                    <label for="artworkStyle"><i class="fas fa-palette"></i> Art Style *</label>
                                    <select id="artworkStyle">
                                        <option value="">Select Style</option>
                                        <option value="abstract">Abstract</option>
                                        <option value="impressionism">Impressionism</option>
                                        <option value="realism">Realism</option>
                                        <option value="expressionism">Expressionism</option>
                                        <option value="contemporary">Contemporary</option>
                                        <option value="traditional">Traditional</option>
                                        <option value="minimalism">Minimalism</option>
                                        <option value="surrealism">Surrealism</option>
                                    </select>
                                    <div class="inputIndicator" id="artworkStyleIndicator"></div>
                                    <div class="errorMessage" id="artworkStyleError"></div>
                                </div>
                                <div class="inputGroup half">
                                    <label for="artworkMedium"><i class="fas fa-brush"></i> Medium</label>
                                    <select id="artworkMedium">
                                        <option value="">Select Medium</option>
                                        <option value="oil">Oil Paint</option>
                                        <option value="acrylic">Acrylic Paint</option>
                                        <option value="watercolor">Watercolor</option>
                                        <option value="charcoal">Charcoal</option>
                                        <option value="pastel">Pastel</option>
                                        <option value="ink">Ink</option>
                                        <option value="pencil">Pencil</option>
                                        <option value="mixed">Mixed Media</option>
                                    </select>
                                </div>
                            </div>

                            <div class="formRow">
                                <div class="inputGroup half">
                                    <label for="artworkWidth"><i class="fas fa-ruler-horizontal"></i> Width (cm) *</label>
                                    <input type="number" id="artworkWidth" placeholder="0" step="0.1">
                                    <div class="inputIndicator" id="artworkWidthIndicator"></div>
                                    <div class="errorMessage" id="artworkWidthError"></div>
                                </div>
                                <div class="inputGroup half">
                                    <label for="artworkHeight"><i class="fas fa-ruler-vertical"></i> Height (cm) *</label>
                                    <input type="number" id="artworkHeight" placeholder="0" step="0.1">
                                    <div class="inputIndicator" id="artworkHeightIndicator"></div>
                                    <div class="errorMessage" id="artworkHeightError"></div>
                                </div>
                            </div>

                            <div class="formRow">
                                <div class="inputGroup half">
                                    <label for="artworkDepth"><i class="fas fa-cube"></i> Depth (cm)</label>
                                    <input type="number" id="artworkDepth" placeholder="0" step="0.1">
                                    <div class="inputIndicator" id="artworkDepthIndicator"></div>
                                    <div class="errorMessage" id="artworkDepthError"></div>
                                    <div class="inputHelp">Leave empty for flat artworks</div>
                                </div>
                                <div class="inputGroup half">
                                    <label for="artworkYear"><i class="fas fa-calendar"></i> Year Created</label>
                                    <input type="number" id="artworkYear" placeholder="2025" value="2025">
                                    <div class="inputIndicator" id="artworkYearIndicator"></div>
                                    <div class="errorMessage" id="artworkYearError"></div>
                                </div>
                            </div>

                            <div class="formRow">
                                <div class="inputGroup">
                                    <label for="artworkDescription"><i class="fas fa-align-left"></i> Description *</label>
                                    <textarea id="artworkDescription" rows="5" placeholder="Describe your artwork, inspiration, technique, and story behind it..." maxlength="1000"></textarea>
                                    <div class="inputIndicator" id="artworkDescriptionIndicator"></div>
                                    <div class="errorMessage" id="artworkDescriptionError"></div>
                                    <div class="charCounter">
                                        <span id="descCharCount">0</span>/1000 characters
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Image Upload -->
                        <div class="formStep" data-step="2">
                            <div class="stepHeader">
                                <h2><i class="fas fa-images"></i> Artwork Images</h2>
                                <p>Upload high-quality images of your artwork</p>
                            </div>

                            <!-- Primary Image Section -->
                            <div class="imageUploadContainer">
                                <div class="imageUploadSection">
                                    <div class="sectionLabel">
                                        <label for="artworkPrimaryImage"><i class="fas fa-star"></i> Primary Artwork Image</label>
                                        <span class="sectionDescription">This will be the main image displayed for your artwork</span>
                                    </div>
                                    <div class="uploadZone primaryUploadZone" id="artworkPrimaryUploadZone">
                                        <div class="uploadContent">
                                            <i class="fas fa-star"></i>
                                            <h3>Drag & Drop Primary Image Here</h3>
                                            <p>or <span class="uploadLink">browse files</span></p>
                                            <div class="uploadReqs">
                                                <span>• JPG, PNG, WEBP up to 10MB</span>
                                                <span>• Minimum 1200x1200 pixels recommended</span>
                                            </div>
                                        </div>
                                        <input type="file" id="artworkPrimaryImage" accept="image/*" hidden>
                                    </div>
                                    
                                    <div class="uploadedImages" id="artworkPrimaryImagePreview">
                                        <!-- Primary image preview will appear here -->
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Images Section -->
                            <div class="imageUploadContainer">
                                <div class="imageUploadSection">
                                    <div class="sectionLabel">
                                        <label for="artworkImages"><i class="fas fa-images"></i> Additional Artwork Images</label>
                                        <span class="sectionDescription">Upload additional views and details of your artwork (optional)</span>
                                    </div>
                                    <div class="uploadZone" id="uploadZone">
                                        <div class="uploadContent">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <h3>Drag & Drop Additional Images Here</h3>
                                            <p>or <span class="uploadLink">browse files</span></p>
                                            <div class="uploadReqs">
                                                <span>• JPG, PNG, WEBP up to 10MB each</span>
                                                <span>• Minimum 1200x1200 pixels</span>
                                                <span>• Maximum 9 additional images</span>
                                            </div>
                                        </div>
                                        <input type="file" id="artworkImages" multiple accept="image/*" hidden>
                                    </div>

                                    <div class="uploadedImages" id="uploadedImages">
                                        <!-- Uploaded images will appear here -->
                                    </div>
                                </div>
                            </div>

                                <div class="imageGuidelines">
                                    <h4><i class="fas fa-lightbulb"></i> Photography Tips</h4>
                                    <div class="guidelinesList">
                                        <div class="guideline">
                                            <i class="fas fa-check"></i>
                                            <span>Use natural light or professional lighting</span>
                                        </div>
                                        <div class="guideline">
                                            <i class="fas fa-check"></i>
                                            <span>Include front view, detail shots, and signature</span>
                                        </div>
                                        <div class="guideline">
                                            <i class="fas fa-check"></i>
                                            <span>Show the artwork in context (room setting)</span>
                                        </div>
                                        <div class="guideline">
                                            <i class="fas fa-check"></i>
                                            <span>Ensure colors are accurate and vibrant</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Preview -->
                        <div class="formStep" data-step="3">
                            <div class="stepHeader">
                                <h2><i class="fas fa-eye"></i> Preview & Publish</h2>
                                <p>Review your artwork listing before publishing</p>
                            </div>

                            <div class="artworkPreview" id="artworkPreview">
                                <!-- Preview will be generated here -->
                            </div>
                        </div>

                        <div class="formNavigation">
                            <button type="button" class="btn btn-secondary" id="prevStep" style="display: none;">
                                <i class="fas fa-arrow-left"></i>
                                Previous
                            </button>
                            <div class="stepInfo">
                                Step <span id="currentStepNum">1</span> of 3
                            </div>
                            <button type="button" class="btn btn-primary" id="nextStep">
                                Next
                                <i class="fas fa-arrow-right"></i>
                            </button>
                            <button type="submit" class="btn btn-success" id="publishBtn" style="display: none;">
                                <i class="fas fa-rocket"></i>
                                Publish Artwork
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Gallery Events Section -->
        <div id="gallery-section" class="content-section">
            <div class="pageHeader">
                <div class="headerContent">
                    <h1>Gallery Events</h1>
                    <p>Create and manage your gallery exhibitions and events.</p>
                </div>
                <div class="headerActions">
                    <button class="btn btn-outline btn-sm" id="viewMyEventsBtn">
                        <i class="fas fa-list"></i>
                        My Events
                    </button>
                </div>
            </div>

            <div class="galleryFormContainer">
                <div class="contentCard">
                    <form class="modernForm galleryForm" id="addGalleryEventForm">
                        <!-- Event Type Selection -->
                        <div class="stepHeader">
                            <h2><i class="fas fa-layer-group"></i> Choose Event Type</h2>
                            <p>Select the type of gallery event you want to create</p>
                        </div>

                        <div class="eventTypeSelection">
                            <div class="typeOption" data-type="virtual">
                                <input type="radio" id="typeVirtual" name="eventType" value="virtual">
                                <label for="typeVirtual">
                                    <div class="typeIcon">
                                        <i class="fas fa-desktop"></i>
                                    </div>
                                    <div class="typeContent">
                                        <h3>Virtual Exhibition</h3>
                                        <p>Host an online digital gallery experience that visitors can access from anywhere in the world</p>
                                        <ul class="typeFeatures">
                                            <li><i class="fas fa-check"></i> Global accessibility</li>
                                            <li><i class="fas fa-check"></i> Interactive digital experience</li>
                                            <li><i class="fas fa-check"></i> Easy sharing and promotion</li>
                                        </ul>
                                    </div>
                                </label>
                            </div>

                            <div class="typeOption" data-type="physical">
                                <input type="radio" id="typePhysical" name="eventType" value="physical">
                                <label for="typePhysical">
                                    <div class="typeIcon">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="typeContent">
                                        <h3>Local Gallery</h3>
                                        <p>Organize a physical exhibition at a gallery location where visitors can attend in person</p>
                                        <ul class="typeFeatures">
                                            <li><i class="fas fa-check"></i> Personal interaction</li>
                                            <li><i class="fas fa-check"></i> Physical artwork viewing</li>
                                            <li><i class="fas fa-check"></i> Local community engagement</li>
                                        </ul>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Dynamic Event Details Section -->
                        <div class="eventDetailsSection" id="eventDetailsSection" style="display: none;">
                            <!-- Virtual Event Details -->
                            <div class="eventDetailsForm" id="virtualEventDetails" style="display: none;">
                                <div class="stepHeader">
                                    <h2><i class="fas fa-desktop"></i> Virtual Exhibition Details</h2>
                                    <p>Set up your online gallery event</p>
                                </div>

                                <div class="formRow">
                                    <div class="inputGroup">
                                        <label for="virtualEventTitle"><i class="fas fa-signature"></i> Event Title *</label>
                                        <input type="text" id="virtualEventTitle" placeholder="Give your virtual exhibition a compelling title..." required>
                                        <div class="inputIndicator" id="virtualEventTitleIndicator"></div>
                                        <div class="errorMessage" id="virtualEventTitleError"></div>
                                        <div class="inputHelp">This will be the main title displayed to online visitors</div>
                                    </div>
                                </div>

                                <div class="formRow">
                                    <div class="inputGroup">
                                        <label for="virtualEventDescription"><i class="fas fa-align-left"></i> Event Description *</label>
                                        <textarea id="virtualEventDescription" rows="5" placeholder="Describe your virtual exhibition, featured artworks, theme, and what visitors can expect..." required maxlength="1000"></textarea>
                                        <div class="inputIndicator" id="virtualEventDescriptionIndicator"></div>
                                        <div class="errorMessage" id="virtualEventDescriptionError"></div>
                                        <div class="charCounter">
                                            <span id="virtualDescCharCount">0</span>/1000 characters
                                        </div>
                                    </div>
                                </div>

                                <div class="formRow">
                                    <div class="inputGroup half">
                                        <label for="virtualEventPrice"><i class="fas fa-tag"></i> Entry Price (EGP)</label>
                                        <div class="priceInput">
                                            <span class="currency">EGP</span>
                                            <input type="number" id="virtualEventPrice" placeholder="0.00" step="0.01" min="0">
                                        </div>
                                        <div class="inputIndicator" id="virtualEventPriceIndicator"></div>
                                        <div class="errorMessage" id="virtualEventPriceError"></div>
                                        <div class="inputHelp">Leave empty or 0 for free virtual events</div>
                                    </div>
                                    <div class="inputGroup half">
                                        <label for="virtualEventDuration"><i class="fas fa-clock"></i> Duration (Minutes) *</label>
                                        <input type="number" id="virtualEventDuration" placeholder="120" min="1" max="120" required>
                                        <div class="inputIndicator" id="virtualEventDurationIndicator"></div>
                                        <div class="errorMessage" id="virtualEventDurationError"></div>
                                        <div class="inputHelp">How many minutes will this virtual event run? (Max: 2 hours)</div>
                                    </div>
                                </div>

                                <div class="formRow">
                                    <div class="inputGroup">
                                        <label for="virtualEventStartDate"><i class="fas fa-calendar-plus"></i> Start Date & Time *</label>
                                        <input type="datetime-local" id="virtualEventStartDate" required>
                                        <div class="inputIndicator" id="virtualEventStartDateIndicator"></div>
                                        <div class="errorMessage" id="virtualEventStartDateError"></div>
                                    </div>
                                </div>

                                <div class="formRow">
                                    <div class="inputGroup">
                                        <label for="virtualEventTags"><i class="fas fa-tags"></i> Event Tags <span class="tagCounter" id="virtualTagCounter">(0/10)</span></label>
                                        <div class="tagsInput" id="virtualTagsInput">
                                            <div class="tagsList" id="virtualTagsList"></div>
                                            <input type="text" id="virtualEventTags" placeholder="Add tags (press Enter, comma, or space to add)">
                                        </div>
                                        <div class="inputHelp">💡 Add relevant tags to help visitors find your virtual exhibition. You can use Enter, comma, or space to add tags.</div>
                                        
                                        <!-- Tag Suggestions -->
                                        <div class="tagSuggestions" id="virtualTagSuggestions">
                                            <div class="suggestionLabel">Popular tags:</div>
                                            <div class="suggestionTags">
                                                <span class="suggestedTag" onclick="addVirtualTag('contemporary')">contemporary</span>
                                                <span class="suggestedTag" onclick="addVirtualTag('abstract')">abstract</span>
                                                <span class="suggestedTag" onclick="addVirtualTag('modern')">modern</span>
                                                <span class="suggestedTag" onclick="addVirtualTag('painting')">painting</span>
                                                <span class="suggestedTag" onclick="addVirtualTag('digital')">digital</span>
                                                <span class="suggestedTag" onclick="addVirtualTag('sculpture')">sculpture</span>
                                                <span class="suggestedTag" onclick="addVirtualTag('exhibition')">exhibition</span>
                                                <span class="suggestedTag" onclick="addVirtualTag('gallery')">gallery</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Primary Image Section for Virtual Events -->
                                <div class="formRow">
                                    <div class="inputGroup">
                                        <label for="virtualPrimaryImage"><i class="fas fa-image"></i> Primary Gallery Image</label>
                                        <div class="imageUploadArea">
                                            <div class="uploadZone" id="virtualPrimaryUploadZone">
                                                <div class="uploadContent">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <h3>Drag & Drop Primary Image Here</h3>
                                                    <p>or <span class="uploadLink">browse file</span></p>
                                                    <div class="uploadReqs">
                                                        <span>• JPG, PNG, GIF, WEBP formats supported</span>
                                                        <span>• This will be the main gallery cover image</span>
                                                    </div>
                                                </div>
                                                <input type="file" id="virtualPrimaryImage" accept="image/*" hidden>
                                            </div>

                                            <div class="uploadedImages" id="virtualPrimaryImagePreview">
                                                <!-- Primary image preview will appear here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Gallery Images Section for Virtual Events -->
                                <div class="formRow">
                                    <div class="inputGroup">
                                        <label for="virtualGalleryImages"><i class="fas fa-images"></i> Additional Gallery Images</label>
                                        <div class="imageUploadArea">
                                            <div class="uploadZone" id="virtualGalleryUploadZone">
                                                <div class="uploadContent">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <h3>Drag & Drop Gallery Images Here</h3>
                                                    <p>or <span class="uploadLink">browse files</span></p>
                                                    <div class="uploadReqs">
                                                        <span>• JPG, PNG, GIF, WEBP formats supported</span>
                                                        <span>• Maximum 15 images</span>
                                                    </div>
                                                </div>
                                                <input type="file" id="virtualGalleryImages" multiple accept="image/*" hidden>
                                            </div>

                                            <div class="uploadedImages" id="virtualUploadedImages">
                                                <!-- Uploaded images will appear here -->
                                            </div>

                                            <div class="imageGuidelines">
                                                <h4><i class="fas fa-lightbulb"></i> Gallery Photography Tips</h4>
                                                <div class="guidelinesList">
                                                    <div class="guideline">
                                                        <i class="fas fa-check"></i>
                                                        <span>Include gallery space overview shots</span>
                                                    </div>
                                                    <div class="guideline">
                                                        <i class="fas fa-check"></i>
                                                        <span>Show artworks in context within the space</span>
                                                    </div>
                                                    <div class="guideline">
                                                        <i class="fas fa-check"></i>
                                                        <span>Capture the ambiance and lighting</span>
                                                    </div>
                                                    <div class="guideline">
                                                        <i class="fas fa-check"></i>
                                                        <span>Include detailed shots of featured pieces</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="inputHelp">Upload images showcasing your virtual gallery space and featured artworks</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Physical Event Details -->
                            <div class="eventDetailsForm" id="physicalEventDetails" style="display: none;">
                                <div class="stepHeader">
                                    <h2><i class="fas fa-building"></i> Local Gallery Details</h2>
                                    <p>Set up your physical gallery event</p>
                                </div>

                                <div class="formRow">
                                    <div class="inputGroup">
                                        <label for="physicalEventTitle"><i class="fas fa-signature"></i> Event Title *</label>
                                        <input type="text" id="physicalEventTitle" placeholder="Give your gallery exhibition a compelling title..." required>
                                        <div class="inputIndicator" id="physicalEventTitleIndicator"></div>
                                        <div class="errorMessage" id="physicalEventTitleError"></div>
                                        <div class="inputHelp">This will be the main title displayed to visitors</div>
                                    </div>
                                </div>

                                <div class="formRow">
                                    <div class="inputGroup">
                                        <label for="physicalEventDescription"><i class="fas fa-align-left"></i> Event Description *</label>
                                        <textarea id="physicalEventDescription" rows="5" placeholder="Describe your gallery exhibition, featured artworks, theme, and what visitors can expect..." required maxlength="1000"></textarea>
                                        <div class="inputIndicator" id="physicalEventDescriptionIndicator"></div>
                                        <div class="errorMessage" id="physicalEventDescriptionError"></div>
                                        <div class="charCounter">
                                            <span id="physicalDescCharCount">0</span>/1000 characters
                                        </div>
                                    </div>
                                </div>

                                <div class="formRow">
                                    <div class="inputGroup half">
                                        <label for="physicalEventPrice"><i class="fas fa-tag"></i> Entry Price (EGP)</label>
                                        <div class="priceInput">
                                            <span class="currency">EGP</span>
                                            <input type="number" id="physicalEventPrice" placeholder="0.00" step="0.01" min="0">
                                        </div>
                                        <div class="inputIndicator" id="physicalEventPriceIndicator"></div>
                                        <div class="errorMessage" id="physicalEventPriceError"></div>
                                        <div class="inputHelp">Leave empty or 0 for free events</div>
                                    </div>
                                    <div class="inputGroup half">
                                        <label for="physicalEventStartDate"><i class="fas fa-calendar-plus"></i> Start Date & Time *</label>
                                        <input type="datetime-local" id="physicalEventStartDate" required>
                                        <div class="inputIndicator" id="physicalEventStartDateIndicator"></div>
                                        <div class="errorMessage" id="physicalEventStartDateError"></div>
                                    </div>
                                </div>

                                <div class="formRow">
                                    <div class="inputGroup half">
                                        <label for="physicalEventPhone"><i class="fas fa-phone"></i> Contact Phone *</label>
                                        <input type="tel" id="physicalEventPhone" placeholder="+20 XXX XXX XXXX" required>
                                        <div class="inputIndicator" id="physicalEventPhoneIndicator"></div>
                                        <div class="errorMessage" id="physicalEventPhoneError"></div>
                                    </div>
                                    <div class="inputGroup half">
                                        <label for="physicalEventCity"><i class="fas fa-city"></i> City *</label>
                                        <input type="text" id="physicalEventCity" placeholder="e.g., Cairo, Alexandria..." required>
                                        <div class="inputIndicator" id="physicalEventCityIndicator"></div>
                                        <div class="errorMessage" id="physicalEventCityError"></div>
                                    </div>
                                </div>

                                <div class="formRow">
                                    <div class="inputGroup">
                                        <label for="physicalEventAddress"><i class="fas fa-map-marker-alt"></i> Gallery Address *</label>
                                        <textarea id="physicalEventAddress" rows="3" placeholder="Enter the full address of your gallery or event venue..." required></textarea>
                                        <div class="inputIndicator" id="physicalEventAddressIndicator"></div>
                                        <div class="errorMessage" id="physicalEventAddressError"></div>
                                        <div class="inputHelp">Include street address, district, and landmarks if applicable</div>
                                    </div>
                                </div>

                                <!-- Primary Image Section for Physical Events -->
                                <div class="formRow">
                                    <div class="inputGroup">
                                        <label for="physicalPrimaryImage"><i class="fas fa-image"></i> Primary Gallery Image</label>
                                        <div class="imageUploadArea">
                                            <div class="uploadZone" id="physicalPrimaryUploadZone">
                                                <div class="uploadContent">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <h3>Drag & Drop Primary Image Here</h3>
                                                    <p>or <span class="uploadLink">browse file</span></p>
                                                    <div class="uploadReqs">
                                                        <span>• JPG, PNG, GIF, WEBP formats supported</span>
                                                        <span>• This will be the main gallery cover image</span>
                                                    </div>
                                                </div>
                                                <input type="file" id="physicalPrimaryImage" accept="image/*" hidden>
                                            </div>

                                            <div class="uploadedImages" id="physicalPrimaryImagePreview">
                                                <!-- Primary image preview will appear here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Gallery Images Section for Physical Events -->
                                <div class="formRow">
                                    <div class="inputGroup">
                                        <label for="physicalGalleryImages"><i class="fas fa-images"></i> Additional Gallery Images</label>
                                        <div class="imageUploadArea">
                                            <div class="uploadZone" id="physicalGalleryUploadZone">
                                                <div class="uploadContent">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <h3>Drag & Drop Gallery Images Here</h3>
                                                    <p>or <span class="uploadLink">browse files</span></p>
                                                    <div class="uploadReqs">
                                                        <span>• JPG, PNG, GIF, WEBP formats supported</span>
                                                        <span>• Maximum 15 images</span>
                                                    </div>
                                                </div>
                                                <input type="file" id="physicalGalleryImages" multiple accept="image/*" hidden>
                                            </div>

                                            <div class="uploadedImages" id="physicalUploadedImages">
                                                <!-- Uploaded images will appear here -->
                                            </div>

                                            <div class="imageGuidelines">
                                                <h4><i class="fas fa-lightbulb"></i> Gallery Photography Tips</h4>
                                                <div class="guidelinesList">
                                                    <div class="guideline">
                                                        <i class="fas fa-check"></i>
                                                        <span>Showcase the gallery exterior and entrance</span>
                                                    </div>
                                                    <div class="guideline">
                                                        <i class="fas fa-check"></i>
                                                        <span>Interior shots showing the exhibition space</span>
                                                    </div>
                                                    <div class="guideline">
                                                        <i class="fas fa-check"></i>
                                                        <span>Previous exhibitions or setup examples</span>
                                                    </div>
                                                    <div class="guideline">
                                                        <i class="fas fa-check"></i>
                                                        <span>Lighting and ambiance of the space</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="inputHelp">Upload images showcasing your physical gallery space and atmosphere</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Section -->
                            <div class="eventPreviewSection" id="eventPreviewSection" style="display: none;">
                                <div class="stepHeader">
                                    <h2><i class="fas fa-eye"></i> Preview & Publish</h2>
                                    <p>Review your event details before publishing</p>
                                </div>

                                <div class="eventPreview" id="eventPreview">
                                    <div class="previewContainer">
                                        <div class="previewHeader">
                                            <h3 id="previewTitle">Event Title</h3>
                                            <div class="previewMeta">
                                                <span class="previewType" id="previewType">Event Type</span>
                                                <span class="previewPrice" id="previewPrice">Free</span>
                                            </div>
                                        </div>
                                        
                                        <div class="previewContent">
                                            <div class="previewSection">
                                                <h4><i class="fas fa-align-left"></i> Description</h4>
                                                <p id="previewDescription">Event description will appear here</p>
                                            </div>
                                            
                                            <div class="previewSection" id="previewLocationSection" style="display: none;">
                                                <h4><i class="fas fa-map-marker-alt"></i> Location</h4>
                                                <div class="previewLocation">
                                                    <p id="previewAddress">Event address</p>
                                                    <p id="previewCity">City</p>
                                                    <p id="previewPhone">Contact phone</p>
                                                </div>
                                            </div>
                                            
                                            <div class="previewSection">
                                                <h4><i class="fas fa-calendar-alt"></i> Schedule</h4>
                                                <div class="previewSchedule">
                                                    <p><strong>Start:</strong> <span id="previewStart">Start date</span></p>
                                                    <p id="previewDurationRow"><strong>Duration:</strong> <span id="previewDays">Duration</span></p>
                                                </div>
                                            </div>

                                            <div class="previewSection" id="previewTagsSection" style="display: none;">
                                                <h4><i class="fas fa-tags"></i> Tags</h4>
                                                <div class="previewTags" id="previewTags">
                                                    <!-- Tags will be populated here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success" id="publishGalleryEventBtn">
                                <i class="fas fa-rocket"></i>
                                Publish Event
                            </button>                        </div>

                    </form>
                </div>
            </div>

            <!-- My Events List -->
            <div class="contentCard" id="myEventsCard" style="display: none;">
                <div class="cardHeader">
                    <h2>My Gallery Events</h2>
                    <div class="cardActions">
                        <button class="btn btn-outline btn-sm" id="backToCreateEvent">
                            <i class="fas fa-plus"></i>
                            Create New Event
                        </button>
                    </div>
                </div>
                <div class="eventsList" id="eventsList">
                    <!-- Events will be loaded here -->
                    <div class="eventItem">
                        <div class="eventInfo">
                            <h4>Contemporary Art Showcase</h4>
                            <p><i class="fas fa-map-marker-alt"></i> Downtown Gallery, Cairo</p>
                            <p><i class="fas fa-calendar-alt"></i> Dec 15, 2024 - Dec 22, 2024</p>
                            <span class="statusBadge status-active">Active</span>
                        </div>
                        <div class="eventActions">
                            <button class="btn btn-outline btn-sm">Edit</button>
                            <button class="btn btn-primary btn-sm">View</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auction Section -->
        <div id="auction-section" class="content-section">
            <div class="pageHeader">
                <div class="headerContent">
                    <h1>Auction Management</h1>
                    <p>Create auctions and track your bidding artworks.</p>
                </div>
            </div>

            <div class="artworkFormContainer">
                <div class="formProgress">
                    <div class="progressStep active" data-step="1">
                        <div class="stepNumber">1</div>
                        <span>Auction Info</span>
                    </div>
                    <div class="progressStep" data-step="2">
                        <div class="stepNumber">2</div>
                        <span>Images</span>
                    </div>
                    <div class="progressStep" data-step="3">
                        <div class="stepNumber">3</div>
                        <span>Preview</span>
                    </div>
                </div>

                <div class="contentCard">
                    <form class="modernForm auctionForm" id="addAuctionForm">
                        <!-- Step 1: Auction Information -->
                        <div class="formStep active" data-step="1">
                            <div class="stepHeader">
                                <h2><i class="fas fa-gavel"></i> Auction Information</h2>
                                <p>Set up your artwork auction details and bidding parameters</p>
                            </div>

                            <div class="formRow">
                                <div class="inputGroup">
                                    <label for="auctionArtworkName"><i class="fas fa-signature"></i> Artwork Title *</label>
                                    <input type="text" id="auctionArtworkName" placeholder="Give your artwork a captivating title...">
                                    <div class="inputIndicator" id="auctionArtworkNameIndicator"></div>
                                    <div class="errorMessage" id="auctionArtworkNameError"></div>
                                    <div class="inputHelp">This will be the main title displayed to bidders</div>
                                </div>
                            </div>

                            <div class="formRow">
                                <div class="inputGroup half">
                                    <label for="initialBid"><i class="fas fa-tag"></i> Starting Bid (EGP) *</label>
                                    <div class="priceInput">
                                        <span class="currency">EGP</span>
                                        <input type="number" id="initialBid" placeholder="0.00" step="0.01">
                                    </div>
                                    <div class="inputIndicator" id="initialBidIndicator"></div>
                                    <div class="errorMessage" id="initialBidError"></div>
                                    <div class="inputHelp">Set a competitive starting price to attract bidders</div>
                                </div>
                                <div class="inputGroup half">
                                    <label for="auctionStyle"><i class="fas fa-palette"></i> Art Style *</label>
                                    <select id="auctionStyle">
                                        <option value="">Select Style</option>
                                        <option value="abstract">Abstract</option>
                                        <option value="impressionism">Impressionism</option>
                                        <option value="realism">Realism</option>
                                        <option value="expressionism">Expressionism</option>
                                        <option value="contemporary">Contemporary</option>
                                        <option value="traditional">Traditional</option>
                                        <option value="minimalism">Minimalism</option>
                                        <option value="surrealism">Surrealism</option>
                                    </select>
                                    <div class="inputIndicator" id="auctionStyleIndicator"></div>
                                    <div class="errorMessage" id="auctionStyleError"></div>
                                </div>
                            </div>

                            <div class="formRow">
                                <div class="inputGroup half">
                                    <label for="auctionWidth"><i class="fas fa-ruler-horizontal"></i> Width (cm) *</label>
                                    <input type="number" id="auctionWidth" placeholder="0" step="0.1">
                                    <div class="inputIndicator" id="auctionWidthIndicator"></div>
                                    <div class="errorMessage" id="auctionWidthError"></div>
                                </div>
                                <div class="inputGroup half">
                                    <label for="auctionHeight"><i class="fas fa-ruler-vertical"></i> Height (cm) *</label>
                                    <input type="number" id="auctionHeight" placeholder="0" step="0.1">
                                    <div class="inputIndicator" id="auctionHeightIndicator"></div>
                                    <div class="errorMessage" id="auctionHeightError"></div>
                                </div>
                            </div>

                            <div class="formRow">
                                <div class="inputGroup half">
                                    <label for="auctionDepth"><i class="fas fa-cube"></i> Depth (cm)</label>
                                    <input type="number" id="auctionDepth" placeholder="0" step="0.1">
                                    <div class="inputIndicator" id="auctionDepthIndicator"></div>
                                    <div class="errorMessage" id="auctionDepthError"></div>
                                    <div class="inputHelp">Leave empty for flat artworks</div>
                                </div>
                                <div class="inputGroup half">
                                    <label for="auctionYear"><i class="fas fa-calendar"></i> Year Created</label>
                                    <input type="number" id="auctionYear" placeholder="2025" value="2025">
                                    <div class="inputIndicator" id="auctionYearIndicator"></div>
                                    <div class="errorMessage" id="auctionYearError"></div>
                                </div>
                            </div>

                            <div class="formRow">
                                <div class="inputGroup half">
                                    <label for="auctionStartDate"><i class="fas fa-calendar-plus fa-lg"></i> Auction Start Date *</label>
                                    <input type="datetime-local" id="auctionStartDate">
                                    <div class="inputIndicator" id="auctionStartDateIndicator"></div>
                                    <div class="errorMessage" id="auctionStartDateError"></div>
                                    <div class="inputHelp">Must be in the future</div>
                                </div>
                                <div class="inputGroup half">
                                    <label for="auctionEndDate"><i class="fas fa-calendar-times fa-lg"></i> Auction End Date *</label>
                                    <input type="datetime-local" id="auctionEndDate">
                                    <div class="inputIndicator" id="auctionEndDateIndicator"></div>
                                    <div class="errorMessage" id="auctionEndDateError"></div>
                                    <div class="inputHelp">Must be after start date</div>
                                </div>
                            </div>

                            <div class="formRow">
                                <div class="inputGroup">
                                    <label for="auctionDescription"><i class="fas fa-align-left"></i> Description *</label>
                                    <textarea id="auctionDescription" rows="5" placeholder="Describe your artwork, inspiration, technique, and story behind it..." maxlength="1000" minlength="10"></textarea>
                                    <div class="inputIndicator" id="auctionDescriptionIndicator"></div>
                                    <div class="errorMessage" id="auctionDescriptionError"></div>
                                    <div class="charCounter">
                                        <span id="auctionDescCharCount">0</span>/1000 characters (minimum 10)
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Image Upload -->
                        <div class="formStep" data-step="2">
                            <div class="stepHeader">
                                <h2><i class="fas fa-images"></i> Artwork Images</h2>
                                <p>Upload high-quality images of your artwork for the auction</p>
                            </div>

                            <!-- Primary Image Section -->
                            <div class="imageUploadContainer">
                                <div class="imageUploadSection">
                                    <div class="sectionLabel">
                                        <label for="auctionPrimaryImage"><i class="fas fa-star"></i> Primary Auction Image</label>
                                        <span class="sectionDescription">This will be the main image displayed for your auction</span>
                                    </div>
                                    <div class="uploadZone primaryUploadZone" id="auctionPrimaryUploadZone">
                                        <div class="uploadContent">
                                            <i class="fas fa-star"></i>
                                            <h3>Drag & Drop Primary Image Here</h3>
                                            <p>or <span class="uploadLink">browse files</span></p>
                                            <div class="uploadReqs">
                                                <span>• JPG, PNG, WEBP up to 50MB</span>
                                                <span>• Minimum 300x300 pixels</span>
                                            </div>
                                        </div>
                                        <input type="file" id="auctionPrimaryImage" accept="image/*" hidden>
                                    </div>
                                    
                                    <div class="uploadedImages" id="auctionPrimaryImagePreview">
                                        <!-- Primary image preview will appear here -->
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Images Section -->
                            <div class="imageUploadContainer">
                                <div class="imageUploadSection">
                                    <div class="sectionLabel">
                                        <label for="auctionImages"><i class="fas fa-images"></i> Additional Auction Images</label>
                                        <span class="sectionDescription">Upload additional views and details of your artwork</span>
                                    </div>
                                    <div class="uploadZone" id="auctionUploadZone">
                                        <div class="uploadContent">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <h3>Drag & Drop Additional Images Here</h3>
                                            <p>or <span class="uploadLink">browse files</span></p>
                                            <div class="uploadReqs">
                                                <span>• JPG, PNG, WEBP up to 50MB each</span>
                                                <span>• Minimum 300x300 pixels</span>
                                                <span>• Maximum 9 additional images</span>
                                            </div>
                                        </div>
                                        <input type="file" id="auctionImages" multiple accept="image/*" hidden>
                                    </div>

                                    <div class="uploadedImages" id="auctionUploadedImages">
                                        <!-- Uploaded images will appear here -->
                                    </div>
                                </div>
                            </div>

                                <div class="imageGuidelines">
                                    <h4><i class="fas fa-lightbulb"></i> Photography Tips for Auctions</h4>
                                    <div class="guidelinesList">
                                        <div class="guideline">
                                            <i class="fas fa-check"></i>
                                            <span>Use professional lighting to highlight details</span>
                                        </div>
                                        <div class="guideline">
                                            <i class="fas fa-check"></i>
                                            <span>Include multiple angles and close-up detail shots</span>
                                        </div>
                                        <div class="guideline">
                                            <i class="fas fa-check"></i>
                                            <span>Show the artwork signature or authentication</span>
                                        </div>
                                        <div class="guideline">
                                            <i class="fas fa-check"></i>
                                            <span>Capture true colors to avoid bidder disappointment</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Preview -->
                        <div class="formStep" data-step="3">
                            <div class="stepHeader">
                                <h2><i class="fas fa-eye"></i> Preview & Launch</h2>
                                <p>Review your auction listing before launching</p>
                            </div>

                            <div class="artworkPreview" id="auctionPreview">
                                <!-- Preview will be generated here -->
                            </div>
                        </div>

                        <div class="formNavigation">
                            <button type="button" class="btn btn-secondary" id="auctionPrevStep" style="display: none;">
                                <i class="fas fa-arrow-left"></i>
                                Previous
                            </button>
                            <div class="stepInfo">
                                Step <span id="auctionCurrentStepNum">1</span> of 3
                            </div>
                            <button type="button" class="btn btn-primary" id="auctionNextStep">
                                Next
                                <i class="fas fa-arrow-right"></i>
                            </button>
                            <button type="submit" class="btn btn-success" id="launchAuctionBtn" style="display: none;">
                                <i class="fas fa-gavel"></i>
                                Launch Auction
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Modals -->
    <div class="modal" id="orderModal">
        <div class="modalContent">
            <div class="modalHeader">
                <h2>Order Details</h2>
                <button class="modalClose" onclick="closeModal('orderModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modalBody">
                <div id="orderDetails">
                    <!-- Order details will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="trackingModal">
        <div class="modalContent">
            <div class="modalHeader">
                <h2>Order Tracking</h2>
                <button class="modalClose" onclick="closeModal('trackingModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modalBody">
                <div id="trackingDetails">
                    <!-- Tracking details will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Artwork Modal -->
    <div class="modal" id="editArtworkModal">
        <div class="modalContent artwork-edit-modal">
            <div class="modalHeader">
                <h2><i class="fas fa-edit"></i> Edit Artwork</h2>
                <button class="modalClose" onclick="closeModal('editArtworkModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modalBody">
                <form id="editArtworkForm" class="artwork-edit-form">
                    <input type="hidden" id="editArtworkId" name="artwork_id">
                    
                    <!-- Title -->
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="editArtworkTitle"><i class="fas fa-signature"></i> Artwork Title *</label>
                            <input type="text" id="editArtworkTitle" name="title" placeholder="Give your artwork a captivating title..." required>
                            <div class="inputIndicator" id="editArtworkTitleIndicator"></div>
                            <div class="errorMessage" id="editArtworkTitleError"></div>
                        </div>
                    </div>

                    <!-- Price and Category -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editArtworkPrice"><i class="fas fa-tag"></i> Price (EGP) *</label>
                            <div class="priceInput">
                                <span class="currency">EGP</span>
                                <input type="number" id="editArtworkPrice" name="price" placeholder="0.00" step="0.01" min="0" required>
                            </div>
                            <div class="inputIndicator" id="editArtworkPriceIndicator"></div>
                            <div class="errorMessage" id="editArtworkPriceError"></div>
                        </div>
                        <div class="form-group">
                            <label for="editArtworkCategory"><i class="fas fa-layer-group"></i> Category *</label>
                            <select id="editArtworkCategory" name="category" required>
                                <option value="">Select Category</option>
                                <option value="paintings">Paintings</option>
                                <option value="sculptures">Sculptures</option>
                                <option value="textiles">Textiles</option>
                                <option value="wooden_arts">Wooden Arts</option>
                                <option value="ceramic_arts">Ceramic Arts</option>
                                <option value="fiber_arts">Fiber Arts</option>
                            </select>
                            <div class="inputIndicator" id="editArtworkCategoryIndicator"></div>
                            <div class="errorMessage" id="editArtworkCategoryError"></div>
                        </div>
                    </div>

                    <!-- Style and Medium -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editArtworkStyle"><i class="fas fa-palette"></i> Art Style *</label>
                            <select id="editArtworkStyle" name="style" required>
                                <option value="">Select Style</option>
                                <option value="abstract">Abstract</option>
                                <option value="impressionism">Impressionism</option>
                                <option value="realism">Realism</option>
                                <option value="expressionism">Expressionism</option>
                                <option value="contemporary">Contemporary</option>
                                <option value="traditional">Traditional</option>
                                <option value="minimalism">Minimalism</option>
                                <option value="surrealism">Surrealism</option>
                            </select>
                            <div class="inputIndicator" id="editArtworkStyleIndicator"></div>
                            <div class="errorMessage" id="editArtworkStyleError"></div>
                        </div>
                        <div class="form-group">
                            <label for="editArtworkMedium"><i class="fas fa-brush"></i> Medium</label>
                            <select id="editArtworkMedium" name="medium">
                                <option value="">Select Medium</option>
                                <option value="oil">Oil Paint</option>
                                <option value="acrylic">Acrylic Paint</option>
                                <option value="watercolor">Watercolor</option>
                                <option value="charcoal">Charcoal</option>
                                <option value="pastel">Pastel</option>
                                <option value="ink">Ink</option>
                                <option value="pencil">Pencil</option>
                                <option value="mixed">Mixed Media</option>
                            </select>
                            <div class="inputIndicator" id="editArtworkMediumIndicator"></div>
                            <div class="errorMessage" id="editArtworkMediumError"></div>
                        </div>
                    </div>

                    <!-- Dimensions -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editArtworkWidth"><i class="fas fa-ruler-horizontal"></i> Width (cm) *</label>
                            <input type="number" id="editArtworkWidth" name="width" placeholder="0" step="0.1" min="0" required>
                            <div class="inputIndicator" id="editArtworkWidthIndicator"></div>
                            <div class="errorMessage" id="editArtworkWidthError"></div>
                        </div>
                        <div class="form-group">
                            <label for="editArtworkHeight"><i class="fas fa-ruler-vertical"></i> Height (cm) *</label>
                            <input type="number" id="editArtworkHeight" name="height" placeholder="0" step="0.1" min="0" required>
                            <div class="inputIndicator" id="editArtworkHeightIndicator"></div>
                            <div class="errorMessage" id="editArtworkHeightError"></div>
                        </div>
                    </div>

                    <!-- Depth and Year -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editArtworkDepth"><i class="fas fa-cube"></i> Depth (cm)</label>
                            <input type="number" id="editArtworkDepth" name="depth" placeholder="0" step="0.1" min="0">
                            <div class="inputIndicator" id="editArtworkDepthIndicator"></div>
                            <div class="errorMessage" id="editArtworkDepthError"></div>
                            <div class="inputHelp">Leave empty for flat artworks</div>
                        </div>
                        <div class="form-group">
                            <label for="editArtworkYear"><i class="fas fa-calendar"></i> Year Created</label>
                            <input type="number" id="editArtworkYear" name="year" placeholder="2025" min="1800" max="2025">
                            <div class="inputIndicator" id="editArtworkYearIndicator"></div>
                            <div class="errorMessage" id="editArtworkYearError"></div>
                        </div>
                    </div>

                    <!-- Availability and Auction -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editArtworkAvailable"><i class="fas fa-check-circle"></i> Availability</label>
                            <select id="editArtworkAvailable" name="is_available">
                                <option value="1">Available</option>
                                <option value="0">Not Available</option>
                            </select>
                            <div class="inputIndicator" id="editArtworkAvailableIndicator"></div>
                            <div class="errorMessage" id="editArtworkAvailableError"></div>
                        </div>
                        <div class="form-group">
                            <label for="editArtworkAuction"><i class="fas fa-gavel"></i> On Auction</label>
                            <select id="editArtworkAuction" name="on_auction">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                            <div class="inputIndicator" id="editArtworkAuctionIndicator"></div>
                            <div class="errorMessage" id="editArtworkAuctionError"></div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="editArtworkDescription"><i class="fas fa-align-left"></i> Description *</label>
                            <textarea id="editArtworkDescription" name="description" rows="5" placeholder="Describe your artwork, inspiration, technique, and story behind it..." maxlength="1000" required></textarea>
                            <div class="inputIndicator" id="editArtworkDescriptionIndicator"></div>
                            <div class="errorMessage" id="editArtworkDescriptionError"></div>
                            <div class="charCounter">
                                <span id="editDescCharCount">0</span>/1000 characters
                            </div>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <div class="imageUploadContainer">
                            <div class="imageUploadSection">
                                <div class="sectionLabel">
                                    <label><i class="fas fa-images"></i> Artwork Photos</label>
                                    <span class="sectionDescription">Manage additional photos of your artwork</span>
                                </div>
                                <div id="artworkPhotosContainer" class="artwork-photos-container">
                                    <!-- Photos will be loaded here dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('editArtworkModal')">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="./public/artist-portal.js?v=<?php echo time(); ?>"></script>
    
    <script>
        // Debug script to check profile update functionality
        console.log('🔧 Artist Portal Debug Script Loaded');
        
        // Check if our functions are available
        setTimeout(() => {
            console.log('📋 Profile Update Functions Check:', {
                updateArtistProfile: typeof updateArtistProfile,
                saveProfileChanges: typeof saveProfileChanges,
                initializeProfileSaveButton: typeof initializeProfileSaveButton
            });
            
            // Check if form elements exist
            console.log('📋 Form Elements Check:', {
                artistInfoForm: !!document.getElementById('artistInfoForm'),
                artistBio: !!document.getElementById('artistBio'),
                artistPhone: !!document.getElementById('artistPhone'),
                artistEmail: !!document.getElementById('artistEmail'),
                artistSpecialty: !!document.getElementById('artistSpecialty'),
                artistExperience: !!document.getElementById('artistExperience'),
                addAchievementBtn: !!document.getElementById('addAchievementBtn')
            });
            
            // Try to initialize manually if needed
            if (typeof initializeProfileSaveButton === 'function') {
                console.log('🚀 Manually triggering initializeProfileSaveButton...');
                initializeProfileSaveButton();
            }
            
            // Check if we're in the profile section
            const profileSection = document.getElementById('profile-section');
            if (profileSection) {
                console.log('✅ Profile section found:', profileSection.style.display !== 'none');
            }
            
        }, 1000);
        
        // Add click listener to profile nav item to re-initialize when switching sections
        document.addEventListener('DOMContentLoaded', function() {
            const profileNavItem = document.querySelector('[data-section="profile"]');
            if (profileNavItem) {
                profileNavItem.addEventListener('click', function() {
                    console.log('📍 Profile section clicked, re-initializing...');
                    setTimeout(() => {
                        if (typeof initializeProfileSaveButton === 'function') {
                            initializeProfileSaveButton();
                        }
                    }, 200);
                });
            }
        });
    </script>
</body>
</html>