<nav class="navbar navbar-yadawity" id="yadawity-navbar">
    <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php" class="nav-logo-link">
                    <div class="logo-container">
                        <img src="./image/Logo.png" alt="Yadawity Gallery" class="logo-image">
                    </div>
                    <div class="logo-text">
                        <span class="logo-name">Yadawity</span>
                        <span class="logo-est">EST. 2025</span>
                    </div>
                </a>
            </div>        <div class="nav-menu" id="nav-menu">
            <a href="index.php" class="nav-link" data-page="home">
                <i class="fas fa-home nav-link-icon"></i>
                <span>HOME</span>
            </a>
            <a href="gallery.php" class="nav-link" data-page="gallery">
                <i class="fas fa-images nav-link-icon"></i>
                <span>GALLERY</span>
            </a>
            <a href="artwork.php" class="nav-link" data-page="artwork">
                <i class="fas fa-palette nav-link-icon"></i>
                <span>ARTWORKS</span>
            </a>
            <a href="courses.php" class="nav-link" data-page="courses">
                <i class="fas fa-graduation-cap nav-link-icon"></i>
                <span>COURSES</span>
            </a>
            <a href="auction.php" class="nav-link" data-page="auction">
                <i class="fas fa-gavel nav-link-icon"></i>
                <span>AUCTION</span>
            </a>
            <a href="artTherapy.php" class="nav-link therapy-nav" data-page="therapy">
                <i class="fas fa-heart nav-link-icon"></i>
                <span>THERAPY</span>
            </a>
        </div>

        <div class="nav-actions">

                <a href="wishlist.php" class="nav-icon-link" title="Wishlist" id="wishlist-link">
                    <i class="fas fa-heart"></i>
                    <span class="wishlist-count" id="wishlist-count" style="display: none;">0</span>
                </a>

                <a href="cart.php" class="nav-icon-link cart-link" title="Cart" id="cart-link">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-count" id="cart-count">0</span>
                </a>            <div class="user-dropdown">
                <a href="#" class="nav-icon-link user-account-btn" title="Account" id="user-account">
                    <i class="fas fa-user"></i>
                </a>
                <div class="user-dropdown-menu" id="user-menu">
                    <div class="dropdown-header">
                        <div class="user-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="user-info">
                            <span class="user-name" id="user-name">Guest User</span>
                            <span class="user-role" id="user-role">Visitor</span>
                        </div>
                    </div>
                    
                        <div class="dropdown-section">
                            <a href="profile.php" class="dropdown-item">
                                <i class="fas fa-user-circle"></i>
                                <span>My Profile</span>
                            </a>
                            <a href="orders.php" class="dropdown-item">
                                <i class="fas fa-box"></i>
                                <span>My Orders</span>
                            </a>
                            <a href="support.php" class="dropdown-item">
                                <i class="fas fa-headset"></i>
                                <span>Support</span>
                            </a>
                        </div>                    <!-- Artist Portal Section - Only visible for artists -->
                    <div class="dropdown-section artist-section" id="artist-section" style="display: none;">
                        <div class="dropdown-section-title">
                            <i class="fas fa-palette"></i>
                            <span>Artist Portal</span>
                        </div>
                        <a href="artist-portal.php" class="dropdown-item artist-item">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Artist Dashboard</span>
                        </a>
                        <a href="artistProfile.php" class="dropdown-item artist-item">
                            <i class="fas fa-images"></i>
                            <span>My Portfolio</span>
                        </a>
                        <a href="artist-sales.php" class="dropdown-item artist-item">
                            <i class="fas fa-chart-line"></i>
                            <span>Sales Analytics</span>
                        </a>
                        <a href="artist-commissions.php" class="dropdown-item artist-item">
                            <i class="fas fa-handshake"></i>
                            <span>Commissions</span>
                        </a>
                    </div>
                    
                    <div class="dropdown-divider"></div>
                    <a href="login.php" class="dropdown-item logout-item" id="login-logout">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="nav-toggle" id="nav-toggle">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
    </div>
 </nav>
