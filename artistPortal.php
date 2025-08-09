<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Portal - Yadawity Gallery</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
                    <i class="fas fa-analytics"></i>
                    <span>My Statistics</span>
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
            <div class="pageHeader">
                <div class="headerContent">
                    <h1>My Statistics</h1>
                    <p>Comprehensive overview of your artworks and galleries performance.</p>
                </div>
                <div class="headerActions">
                    <button class="btn btn-secondary btn-sm" id="refreshStatsBtn">
                        <i class="fas fa-sync-alt"></i>
                        Refresh
                    </button>
                    <button class="btn btn-outline btn-sm" id="exportStatsBtn">
                        <i class="fas fa-download"></i>
                        Export Report
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div class="loadingState" id="statisticsLoading">
                <div class="loadingSpinner">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <p>Loading your statistics...</p>
            </div>

            <!-- Statistics Content -->
            <div class="statisticsContent" id="statisticsContent" style="display: none;">
                <!-- Summary Stats Cards -->
                <div class="summaryStatsGrid">
                    <div class="statCard">
                        <div class="statIcon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <div class="statContent">
                            <h3 id="totalProductsCount">0</h3>
                            <p>Total Artworks</p>
                        </div>
                    </div>
                    <div class="statCard">
                        <div class="statIcon">
                            <i class="fas fa-images"></i>
                        </div>
                        <div class="statContent">
                            <h3 id="totalGalleriesCount">0</h3>
                            <p>Total Galleries</p>
                        </div>
                    </div>
                    <div class="statCard">
                        <div class="statIcon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="statContent">
                            <h3 id="totalSalesCount">0</h3>
                            <p>Total Sales</p>
                        </div>
                    </div>
                    <div class="statCard">
                        <div class="statIcon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="statContent">
                            <h3 id="totalRevenueAmount">EGP 0</h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                    <div class="statCard">
                        <div class="statIcon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="statContent">
                            <h3 id="totalWishlistCount">0</h3>
                            <p>Total Wishlist</p>
                        </div>
                    </div>
                    <div class="statCard">
                        <div class="statIcon">
                            <i class="fas fa-cart-plus"></i>
                        </div>
                        <div class="statContent">
                            <h3 id="totalCartCount">0</h3>
                            <p>Total in Cart</p>
                        </div>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="contentCard">
                    <div class="cardHeader">
                        <h2><i class="fas fa-palette"></i> My Artworks Statistics</h2>
                        <div class="cardActions">
                            <div class="filterGroup">
                                <select class="filterSelect" id="productStatusFilter">
                                    <option value="">All Status</option>
                                    <option value="Available">Available</option>
                                    <option value="Sold">Sold</option>
                                    <option value="On Auction">On Auction</option>
                                    <option value="Draft">Draft</option>
                                </select>
                            </div>
                            <div class="searchGroup">
                                <input type="text" class="searchInput" placeholder="Search artworks..." id="productSearch">
                            </div>
                        </div>
                    </div>
                    
                    <div class="statsTableContainer">
                        <div class="statsTable" id="productsStatsTable">
                            <!-- Products will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Virtual Galleries Section -->
                <div class="contentCard">
                    <div class="cardHeader">
                        <h2><i class="fas fa-desktop"></i> Virtual Galleries Statistics</h2>
                        <div class="cardActions">
                            <div class="filterGroup">
                                <select class="filterSelect" id="virtualGalleryStatusFilter">
                                    <option value="">All Status</option>
                                    <option value="Published">Published</option>
                                    <option value="Draft">Draft</option>
                                </select>
                            </div>
                            <div class="searchGroup">
                                <input type="text" class="searchInput" placeholder="Search virtual galleries..." id="virtualGallerySearch">
                            </div>
                        </div>
                    </div>
                    
                    <div class="statsTableContainer">
                        <div class="statsTable" id="virtualGalleriesStatsTable">
                            <!-- Virtual galleries will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Local Galleries Section -->
                <div class="contentCard">
                    <div class="cardHeader">
                        <h2><i class="fas fa-building"></i> Local Galleries Statistics</h2>
                        <div class="cardActions">
                            <div class="filterGroup">
                                <select class="filterSelect" id="localGalleryStatusFilter">
                                    <option value="">All Status</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Pending Approval">Pending Approval</option>
                                </select>
                            </div>
                            <div class="searchGroup">
                                <input type="text" class="searchInput" placeholder="Search local galleries..." id="localGallerySearch">
                            </div>
                        </div>
                    </div>
                    
                    <div class="statsTableContainer">
                        <div class="statsTable" id="localGalleriesStatsTable">
                            <!-- Local galleries will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div class="emptyStatsState" id="emptyStatsState" style="display: none;">
                    <div class="emptyStateIcon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>No Statistics Available</h3>
                    <p>Start creating artworks and galleries to see your statistics here.</p>
                    <div class="emptyStateActions">
                        <button class="btn btn-primary" onclick="switchSection('artwork')">
                            <i class="fas fa-plus"></i>
                            Add Your First Artwork
                        </button>
                        <button class="btn btn-outline" onclick="switchSection('gallery')">
                            <i class="fas fa-images"></i>
                            Create Gallery Event
                        </button>
                    </div>
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
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                    </select>
                </div>
                <div class="searchGroup">
                    <label>Search Orders</label>
                    <input type="text" class="searchInput" placeholder="Search by order ID or customer..." id="orderSearch">
                </div>
            </div>

            <div class="contentCard">
                <div class="tableContainer">
                    <table class="dataTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Artwork</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#ORD-001</td>
                                <td>Sarah Ahmed</td>
                                <td>Abstract Composition</td>
                                <td>EGP 15,500</td>
                                <td><span class="statusBadge status-processing">Processing</span></td>
                                <td>Jan 15, 2025</td>
                                <td>
                                    <button class="actionBtn btn-view" onclick="viewOrder('ORD-001')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="actionBtn btn-track" onclick="trackOrder('ORD-001')">
                                        <i class="fas fa-truck"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>#ORD-002</td>
                                <td>Mohamed Hassan</td>
                                <td>Modern Landscape</td>
                                <td>EGP 12,000</td>
                                <td><span class="statusBadge status-shipped">Shipped</span></td>
                                <td>Jan 14, 2025</td>
                                <td>
                                    <button class="actionBtn btn-view" onclick="viewOrder('ORD-002')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="actionBtn btn-track" onclick="trackOrder('ORD-002')">
                                        <i class="fas fa-truck"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>#ORD-003</td>
                                <td>Layla Mansour</td>
                                <td>Color Symphony</td>
                                <td>EGP 16,800</td>
                                <td><span class="statusBadge status-pending">Pending</span></td>
                                <td>Jan 13, 2025</td>
                                <td>
                                    <button class="actionBtn btn-view" onclick="viewOrder('ORD-003')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="actionBtn btn-track" onclick="trackOrder('ORD-003')">
                                        <i class="fas fa-truck"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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

            <div class="profileGrid">
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
                                <h4>Elena Rosetti</h4>
                                <p>Contemporary Artist</p>
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
                                <input type="text" id="artistName" value="Elena Rosetti" disabled>
                                <div class="inputHelp">
                                    <i class="fas fa-info-circle"></i>
                                    Name cannot be changed. Contact support if needed.
                                </div>
                            </div>
                        </div>

                        <div class="formRow">
                            <div class="inputGroup">
                                <label for="artistBio"><i class="fas fa-quote-left"></i> About Me</label>
                                <textarea id="artistBio" rows="4" placeholder="Tell the world about your artistic journey..." maxlength="500">Contemporary artist specializing in abstract expressionism with over 10 years of experience in oil painting and mixed media. My work explores the intersection of emotion and color, creating pieces that evoke deep contemplation.</textarea>
                                <div class="charCounter">
                                    <span id="bioCharCount">156</span>/500 characters
                                </div>
                            </div>
                        </div>

                        <div class="formRow">
                            <div class="inputGroup half">
                                <label for="artistPhone"><i class="fas fa-phone"></i> Phone Number</label>
                                <input type="tel" id="artistPhone" value="+20 1099359953" placeholder="+20 XXX XXX XXXX">
                                <div class="validationIcon"></div>
                            </div>
                            <div class="inputGroup half">
                                <label for="artistEmail"><i class="fas fa-envelope"></i> Email Address</label>
                                <input type="email" id="artistEmail" value="elena.rosetti@email.com" placeholder="your@email.com">
                                <div class="validationIcon"></div>
                            </div>
                        </div>

                        <div class="formRow">
                            <div class="inputGroup half">
                                <label for="artistSpecialty"><i class="fas fa-palette"></i> Art Specialty</label>
                                <select id="artistSpecialty">
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
                </div>

                <div class="contentCard">
                    <div class="cardHeader">
                        <h2><i class="fas fa-shield-alt"></i> Security & Privacy</h2>
                        <div class="securityLevel">
                            <span class="securityBadge high">
                                <i class="fas fa-lock"></i>
                                High Security
                            </span>
                        </div>
                    </div>
                    <form class="modernForm" id="securityForm">
                        <div class="securityOverview">
                            <div class="securityItem">
                                <div class="securityIcon">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div class="securityInfo">
                                    <h4>Password</h4>
                                    <p>Last changed 3 months ago</p>
                                </div>
                                <div class="securityStatus">
                                    <span class="statusGood">Strong</span>
                                </div>
                            </div>
                            <div class="securityItem">
                                <div class="securityIcon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="securityInfo">
                                    <h4>Two-Factor Authentication</h4>
                                    <p>SMS to +20 ••• ••• •953</p>
                                </div>
                                <div class="securityStatus">
                                    <span class="statusGood">Active</span>
                                </div>
                            </div>
                        </div>

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

                            <div class="imageUploadArea">
                                <div class="uploadZone" id="uploadZone">
                                    <div class="uploadContent">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <h3>Drag & Drop Images Here</h3>
                                        <p>or <span class="uploadLink">browse files</span></p>
                                        <div class="uploadReqs">
                                            <span>• JPG, PNG, WEBP up to 10MB each</span>
                                            <span>• Minimum 1200x1200 pixels</span>
                                            <span>• Maximum 10 images</span>
                                        </div>
                                    </div>
                                    <input type="file" id="artworkImages" multiple accept="image/*" hidden>
                                </div>

                                <div class="uploadedImages" id="uploadedImages">
                                    <!-- Uploaded images will appear here -->
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
                                        <div class="inputHelp">This will be the main title displayed to online visitors</div>
                                    </div>
                                </div>

                                <div class="formRow">
                                    <div class="inputGroup">
                                        <label for="virtualEventDescription"><i class="fas fa-align-left"></i> Event Description *</label>
                                        <textarea id="virtualEventDescription" rows="5" placeholder="Describe your virtual exhibition, featured artworks, theme, and what visitors can expect..." required maxlength="1000"></textarea>
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
                                        <div class="inputHelp">Leave empty or 0 for free virtual events</div>
                                    </div>
                                    <div class="inputGroup half">
                                        <label for="virtualEventDuration"><i class="fas fa-clock"></i> Duration (Minutes) *</label>
                                        <input type="number" id="virtualEventDuration" placeholder="120" min="1" max="120" required>
                                        <div class="inputHelp">How many minutes will this virtual event run? (Max: 2 hours)</div>
                                    </div>
                                </div>

                                <div class="formRow">
                                    <div class="inputGroup">
                                        <label for="virtualEventStartDate"><i class="fas fa-calendar-plus"></i> Start Date & Time *</label>
                                        <input type="datetime-local" id="virtualEventStartDate" required>
                                    </div>
                                </div>

                                <div class="formRow">
                                    <div class="inputGroup">
                                        <label for="virtualEventTags"><i class="fas fa-tags"></i> Event Tags</label>
                                        <div class="tagsInput" id="virtualTagsInput">
                                            <div class="tagsList" id="virtualTagsList"></div>
                                            <input type="text" id="virtualEventTags" placeholder="Add tags (press Enter after each tag)">
                                        </div>
                                        <div class="inputHelp">Add relevant tags to help visitors find your virtual exhibition</div>
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
                                        <div class="inputHelp">This will be the main title displayed to visitors</div>
                                    </div>
                                </div>

                                <div class="formRow">
                                    <div class="inputGroup">
                                        <label for="physicalEventDescription"><i class="fas fa-align-left"></i> Event Description *</label>
                                        <textarea id="physicalEventDescription" rows="5" placeholder="Describe your gallery exhibition, featured artworks, theme, and what visitors can expect..." required maxlength="1000"></textarea>
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
                                        <div class="inputHelp">Leave empty or 0 for free events</div>
                                    </div>
                                    <div class="inputGroup half">
                                        <label for="physicalEventStartDate"><i class="fas fa-calendar-plus"></i> Start Date & Time *</label>
                                        <input type="datetime-local" id="physicalEventStartDate" required>
                                    </div>
                                </div>

                                <div class="formRow">
                                    <div class="inputGroup half">
                                        <label for="physicalEventPhone"><i class="fas fa-phone"></i> Contact Phone *</label>
                                        <input type="tel" id="physicalEventPhone" placeholder="+20 XXX XXX XXXX" required>
                                    </div>
                                    <div class="inputGroup half">
                                        <label for="physicalEventCity"><i class="fas fa-city"></i> City *</label>
                                        <input type="text" id="physicalEventCity" placeholder="e.g., Cairo, Alexandria..." required>
                                    </div>
                                </div>

                                <div class="formRow">
                                    <div class="inputGroup">
                                        <label for="physicalEventAddress"><i class="fas fa-map-marker-alt"></i> Gallery Address *</label>
                                        <textarea id="physicalEventAddress" rows="3" placeholder="Enter the full address of your gallery or event venue..." required></textarea>
                                        <div class="inputHelp">Include street address, district, and landmarks if applicable</div>
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

            <div class="contentCard">
                <form class="auctionForm" id="addAuctionForm">
                    <div class="formGrid">
                        <div class="formGroup">
                            <label for="auctionArtworkName">Artwork Name *</label>
                            <input type="text" id="auctionArtworkName" placeholder="Enter artwork title" required>
                        </div>
                        <div class="formGroup">
                            <label for="initialBid">Initial Bid (EGP) *</label>
                            <input type="number" id="initialBid" placeholder="0.00" min="0" step="0.01" required>
                        </div>
                        <div class="formGroup">
                            <label for="auctionDimensions">Dimensions *</label>
                            <input type="text" id="auctionDimensions" placeholder="e.g., 60cm x 80cm" required>
                        </div>
                        <div class="formGroup">
                            <label for="auctionStyle">Style *</label>
                            <select id="auctionStyle" required>
                                <option value="">Select Style</option>
                                <option value="abstract">Abstract</option>
                                <option value="impressionism">Impressionism</option>
                                <option value="realism">Realism</option>
                                <option value="expressionism">Expressionism</option>
                                <option value="contemporary">Contemporary</option>
                                <option value="traditional">Traditional</option>
                            </select>
                        </div>
                        <div class="formGroup">
                            <label for="auctionStartDate">Auction Start Date *</label>
                            <input type="datetime-local" id="auctionStartDate" required>
                        </div>
                        <div class="formGroup">
                            <label for="auctionEndDate">Auction End Date *</label>
                            <input type="datetime-local" id="auctionEndDate" required>
                        </div>
                    </div>
                    
                    <div class="formGroup">
                        <label for="auctionDescription">Description *</label>
                        <textarea id="auctionDescription" rows="4" placeholder="Describe your artwork, inspiration, technique used..." required></textarea>
                    </div>

                    <div class="formGroup">
                        <label for="auctionImages">Artwork Images *</label>
                        <div class="multiFileUpload">
                            <input type="file" id="auctionImages" multiple accept="image/*" required>
                            <label for="auctionImages" class="fileUploadLabel">
                                <i class="fas fa-images"></i>
                                Choose Multiple Images
                            </label>
                            <div class="uploadPreview" id="auctionImagePreview"></div>
                        </div>
                    </div>

                    <div class="formActions">
                        <button type="button" class="btn btn-secondary">Save as Draft</button>
                        <button type="submit" class="btn btn-primary">Start Auction</button>
                    </div>
                </form>
            </div>

            <div class="contentCard">
                <div class="cardHeader">
                    <h2>Current Auctions</h2>
                </div>
                <div class="auctionList">
                    <div class="auctionItem active">
                        <img src="./image/slide1.jpg" alt="Modern Abstract" class="auctionImage">
                        <div class="auctionInfo">
                            <h4>Modern Abstract #3</h4>
                            <p class="auctionStatus"><span class="statusBadge status-active">Active</span></p>
                            <p class="auctionBid">Current Bid: <strong>EGP 22,500</strong></p>
                            <p class="auctionTime"><i class="fas fa-clock"></i> Ends in 2 days, 5 hours</p>
                            <p class="auctionBidders"><i class="fas fa-users"></i> 12 bidders</p>
                        </div>
                        <div class="auctionActions">
                            <button class="btn btn-outline btn-sm">View Details</button>
                            <button class="btn btn-primary btn-sm">Track Bids</button>
                        </div>
                    </div>
                    <div class="auctionItem ended">
                        <img src="./image/photo-1554907984-15263bfd63bd.jpeg" alt="Landscape Dreams" class="auctionImage">
                        <div class="auctionInfo">
                            <h4>Landscape Dreams</h4>
                            <p class="auctionStatus"><span class="statusBadge status-ended">Ended</span></p>
                            <p class="auctionBid">Final Bid: <strong>EGP 18,750</strong></p>
                            <p class="auctionTime"><i class="fas fa-check"></i> Ended 3 days ago</p>
                            <p class="auctionBidders"><i class="fas fa-users"></i> 8 bidders</p>
                        </div>
                        <div class="auctionActions">
                            <button class="btn btn-outline btn-sm">View Details</button>
                            <button class="btn btn-secondary btn-sm">See Results</button>
                        </div>
                    </div>
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="./public/artist-portal.js"></script>
</body>
</html>