<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Yadawity Gallery</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
      integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="./components/Navbar/navbar.css" />`n    <link rel="stylesheet" href="./public/homePage.css" />
    <link rel="stylesheet" href="./public/privacyPolicy.css" />
    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css" />
</head>
<body>
    <nav class="navbar navbarYadawity" id="yadawityNavbar">
      <div class="navContainer">
        <div class="navLogo">
          <a href="index.php" class="navLogoLink">
            <div class="logoIcon">
              <svg
                width="40"
                height="40"
                viewBox="0 0 100 100"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M20 50 Q15 30 25 25 Q35 20 45 35 Q40 45 35 50 Q40 55 45 65 Q35 80 25 75 Q15 70 20 50 Z"
                  fill="currentColor"
                  opacity="0.8"
                />
                <path
                  d="M80 50 Q85 30 75 25 Q65 20 55 35 Q60 45 65 50 Q60 55 55 65 Q65 80 75 75 Q85 70 80 50 Z"
                  fill="currentColor"
                  opacity="0.8"
                />
                <line
                  x1="50"
                  y1="20"
                  x2="50"
                  y2="80"
                  stroke="currentColor"
                  stroke-width="3"
                />
                <path
                  d="M50 20 Q45 15 42 12 M50 20 Q55 15 58 12"
                  stroke="currentColor"
                  stroke-width="2"
                  fill="none"
                />
              </svg>
            </div>
            <div class="logoText">
              <span class="logoName">Yadawity</span>
              <span class="logoEst">EST. 2025</span>
            </div>
          </a>
        </div>

        <div class="navMenu" id="navMenu">
          <a href="index.php" class="navLink" data-page="home">HOME</a>
          <a href="gallery.php" class="navLink" data-page="gallery">GALLERY</a>
          <a href="courses.php" class="navLink" data-page="courses">COURSES</a>
          <a href="artwork.php" class="navLink" data-page="atelier">ARTWORKS</a>
          <a href="auction.php" class="navLink" data-page="auction">AUCTION HOUSE</a>
          <a href="artTherapy.php" class="navLink therapyNav" data-page="therapy">THERAPY</a>

          <div class="navActions">
            <div class="searchContainer">
              <input
                type="text"
                placeholder="Search artists, artworks..."
                class="searchInput"
                id="navbarSearch"
              />
              <button class="searchBtn" id="searchButton">
                <i class="fas fa-search"></i>
              </button>
            </div>

            <a
              href="wishlist.php"
              class="navIconLink"
              title="Wishlist"
              id="wishlistLink"
            >
              <i class="fas fa-heart"></i>
              <span
                class="wishlistCount"
                id="wishlistCount"
                style="display: none"
                >0</span
              >
            </a>

            <a
              href="cart.php"
              class="navIconLink cartLink"
              title="Cart"
              id="cartLink"
            >
              <i class="fas fa-shopping-bag"></i>
              <span class="cartCount" id="cartCount">0</span>
            </a>

            <div class="userDropdown">
              <a href="#" class="navIconLink" title="Account" id="userAccount">
                <i class="fas fa-user"></i>
              </a>
              <div class="userDropdownMenu" id="userMenu">
                <a href="profile.php" class="dropdownItem">
                  <i class="fas fa-cog"></i>
                  <span>Settings</span>
                </a>
                <div class="dropdownDivider"></div>
                <a href="./userLogin.html" class="dropdownItem" id="loginLogout" rel="noopener">
                  <i class="fas fa-sign-in-alt"></i>
                  <span>Login</span>
                </a>
              </div>
            </div>
          </div>
        </div>

        <div class="navToggle" id="navToggle">
          <span class="bar"></span>
          <span class="bar"></span>
          <span class="bar"></span>
        </div>
      </div>
    </nav>

    <!-- Burger Menu Component -->
    <div class="burgerMenuOverlay" id="burgerMenuOverlay">
      <div class="burgerMenuContainer">
        <!-- Header -->
        <div class="burgerMenuHeader">
          <a href="index.php" class="burgerMenuLogo">Yadawity</a>
          <button class="burgerMenuClose" id="burgerMenuClose">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- Navigation Links -->
        <div class="burgerNavLinks">
          <a href="index.php" class="burgerNavLink" data-page="home">
            <span>Home</span>
          </a>
          <a href="gallery.php" class="burgerNavLink" data-page="gallery">
            <span>Gallery</span>
          </a>
          <a href="artwork.php" class="burgerNavLink" data-page="artworks">
            <span>Artworks</span>
          </a>
          <a
            href="courses.php"
            class="burgerNavLink therapyNav"
            data-page="courses"
          >
            <span>Art Therapy</span>
          </a>
          <a href="about.php" class="burgerNavLink" data-page="about">
            <span>About</span>
          </a>
          <a href="support.php" class="burgerNavLink" data-page="contact">
            <span>Contact</span>
          </a>
        </div>

        <!-- Quick Actions Section -->
        <div class="burgerQuickActions">
          <div class="burgerQuickActionsLabel">QUICK ACTIONS</div>

          <!-- Search -->
          <div class="burgerSearchContainer">
            <input
              type="text"
              class="burgerSearchInput"
              id="burgerSearchInput"
              placeholder="Search artworks, artists..."
              autocomplete="off"
            />
            <button class="burgerSearchBtn" id="burgerSearchButton">
              <i class="fas fa-search"></i>
            </button>
          </div>

          <!-- Action Links -->
          <div class="burgerActionRow">
            <a href="cart.php" class="burgerActionLink" id="burgerCartLink">
              <i class="fas fa-shopping-cart"></i>
              <span class="burgerActionText">Cart</span>
              <span class="burgerActionCounter" id="burgerCartCount">3</span>
            </a>

            <a
              href="wishlist.php"
              class="burgerActionLink"
              id="burgerWishlistLink"
            >
              <i class="fas fa-heart"></i>
              <span class="burgerActionText">Wishlist</span>
              <span
                class="burgerActionCounter burgerWishlistCount"
                id="burgerWishlistCount"
                >7</span
              >
            </a>

            <a href="favorites.html" class="burgerActionLink">
              <i class="fas fa-star"></i>
              <span class="burgerActionText">Favorites</span>
            </a>
          </div>

          <!-- User Account Dropdown -->
          <div class="burgerUserDropdown" id="burgerUserDropdown">
            <a href="#" class="burgerActionLink" id="burgerUserAccount">
              <i class="fas fa-user"></i>
              <span class="burgerActionText">Account</span>
              <i
                class="fas fa-chevron-down"
                style="margin-left: auto; font-size: 0.7rem"
              ></i>
            </a>

            <!-- Dropdown Menu -->
            <div class="burgerUserDropdownMenu" id="burgerUserMenu">
              <a href="settings.html" class="burgerDropdownItem">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
              </a>

              <hr class="burgerDropdownDivider" />

              <a
                href="./login.html"
                class="burgerDropdownItem"
                id="burgerLoginLogout"
              >
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Hero Section -->
    <section class="privacyHero">
        <div class="container">
            <div class="heroContent">
                <h1 class="heroTitle">Privacy Policy</h1>
                <p class="heroSubtitle">Your privacy is important to us. This policy explains how we collect, use, and protect your personal information when you use our platform.</p>
                <div class="lastUpdated">
                    <i class="fas fa-shield-alt"></i>
                    <span>Last updated: January 1, 2025</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Table of Contents -->
    <section class="tableOfContents">
        <div class="container">
            <div class="tocContainer">
                <h2>Table of Contents</h2>
                <ul class="tocList">
                    <li><a href="#information-we-collect" class="tocLink">1. Information We Collect</a></li>
                    <li><a href="#how-we-use-information" class="tocLink">2. How We Use Your Information</a></li>
                    <li><a href="#information-sharing" class="tocLink">3. How We Share Your Information</a></li>
                    <li><a href="#data-security" class="tocLink">4. Data Security</a></li>
                    <li><a href="#your-rights" class="tocLink">5. Your Rights and Choices</a></li>
                    <li><a href="#contact-us" class="tocLink">6. Contact Us</a></li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Privacy Content -->
    <section class="privacyContent">
        <div class="container">
            <div class="contentWrapper">
                
                <div class="contentSection" id="information-we-collect">
                    <h2>1. Information We Collect</h2>
                    <p>We collect information you provide directly to us, information we obtain automatically when you use our services, and information from third-party sources.</p>
                    
                    <h3>Information You Provide to Us</h3>
                    <ul>
                        <li><strong>Account Information:</strong> Name, email address, password, profile picture, and billing information</li>
                        <li><strong>Profile Information:</strong> Artist bio, portfolio details, social media links, and professional background</li>
                        <li><strong>Transaction Information:</strong> Purchase history, payment methods, shipping addresses, and commission details</li>
                        <li><strong>Communication Data:</strong> Messages, reviews, comments, and customer support interactions</li>
                        <li><strong>Content:</strong> Artwork uploads, descriptions, pricing, and other content you create on our platform</li>
                    </ul>
                </div>

                <div class="contentSection" id="how-we-use-information">
                    <h2>2. How We Use Your Information</h2>
                    <p>We use your information to provide, maintain, and improve our services:</p>
                    
                    <ul>
                        <li><strong>Service Provision:</strong> Create and manage accounts, process transactions, and deliver purchased items</li>
                        <li><strong>Communication:</strong> Send order confirmations, updates, newsletters, and respond to inquiries</li>
                        <li><strong>Personalization:</strong> Recommend artworks, customize your experience, and show relevant content</li>
                        <li><strong>Analytics:</strong> Understand user behavior, measure performance, and identify areas for improvement</li>
                        <li><strong>Security:</strong> Detect fraud, prevent abuse, and protect user accounts and data</li>
                        <li><strong>Legal Compliance:</strong> Comply with applicable laws, regulations, and legal processes</li>
                    </ul>
                </div>

                <div class="contentSection" id="information-sharing">
                    <h2>3. How We Share Your Information</h2>
                    <p>We share your information only in specific circumstances:</p>
                    
                    <h3>With Service Providers</h3>
                    <ul>
                        <li><strong>Payment Processors:</strong> Stripe, PayPal for transaction processing</li>
                        <li><strong>Shipping Partners:</strong> Delivery companies for order fulfillment</li>
                        <li><strong>Cloud Services:</strong> AWS, Google Cloud for data storage and hosting</li>
                        <li><strong>Analytics:</strong> Google Analytics, Hotjar for website analysis</li>
                    </ul>
                </div>

                <div class="contentSection" id="data-security">
                    <h2>4. Data Security</h2>
                    <p>We implement comprehensive security measures to protect your personal information:</p>
                    
                    <ul>
                        <li><strong>Encryption:</strong> All data is encrypted in transit and at rest</li>
                        <li><strong>Access Controls:</strong> Role-based access with multi-factor authentication</li>
                        <li><strong>Network Security:</strong> Firewalls, intrusion detection, and regular security monitoring</li>
                        <li><strong>Regular Updates:</strong> Security patches and software updates are applied promptly</li>
                    </ul>
                </div>

                <div class="contentSection" id="your-rights">
                    <h2>5. Your Rights and Choices</h2>
                    <p>You have several rights regarding your personal information:</p>
                    
                    <ul>
                        <li><strong>Access:</strong> Request a copy of your personal data</li>
                        <li><strong>Rectification:</strong> Correct inaccurate or incomplete information</li>
                        <li><strong>Erasure:</strong> Request deletion of your personal data</li>
                        <li><strong>Portability:</strong> Receive your data in a structured format</li>
                        <li><strong>Withdraw Consent:</strong> Revoke consent for data processing</li>
                    </ul>
                </div>

                <div class="contentSection" id="contact-us">
                    <h2>6. Contact Us</h2>
                    <p>If you have questions about this Privacy Policy, please contact us:</p>
                    
                    <div class="contactInfo">
                        <div class="contactMethod">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <strong>Email:</strong>
                                <a href="mailto:privacy@yadawity.com" class="inlineLink">privacy@yadawity.com</a>
                            </div>
                        </div>
                        
                        <div class="contactMethod">
                            <i class="fas fa-phone"></i>
                            <div>
                                <strong>Phone:</strong>
                                <span>+20 1099359953</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-content">
        <div class="footer-section">
          <h2>Yadawity</h2>
          <p>
            Discover exceptional talent from our curated fellowship of artists
            and distinguished craftspeople. Every piece tells a unique story.
          </p>
          <div class="social-links">
            <a href="#" aria-label="Facebook">
              <svg viewBox="0 0 24 24">
                <path
                  d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                />
              </svg>
            </a>
            <a href="#" aria-label="Instagram">
              <svg viewBox="0 0 24 24">
                <path
                  d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.40s-.644-1.44-1.439-1.40z"
                />
              </svg>
            </a>
            <a href="#" aria-label="Twitter">
              <svg viewBox="0 0 24 24">
                <path
                  d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"
                />
              </svg>
            </a>
          </div>
        </div>

        <div class="footer-section">
          <h3>Explore</h3>
          <ul>
            <li><a href="#">Featured Artists</a></li>
            <li><a href="#">Art Categories</a></li>
            <li><a href="#">Handcrafts</a></li>
            <li><a href="#">Custom Commissions</a></li>
            <li><a href="#">Art Courses</a></li>
          </ul>
        </div>

        <div class="footer-section">
          <h3>Support</h3>
          <ul>
            <li><a href="support.php">Help Center</a></li>
            <li><a href="#">Shipping Info</a></li>
            <li><a href="#">Returns & Exchanges</a></li>
            <li><a href="#">Size Guide</a></li>
            <li><a href="#">Care Instructions</a></li>
          </ul>
        </div>

        <div class="footer-section">
          <h3>Contact Us</h3>
          <div class="contact-info">
            <div class="contact-item">
              <span class="contact-icon">
                <svg viewBox="0 0 24 24">
                  <path
                    d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"
                  />
                </svg>
              </span>
              <span>+20 1099359953</span>
            </div>
            <div class="contact-item">
              <span class="contact-icon">
                <svg viewBox="0 0 24 24">
                  <path
                    d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"
                  />
                </svg>
              </span>
              <span>yadawity@gmail.com</span>
            </div>
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <p>Made with 🤎 by Yadawity Team © All rights reserved.</p>
        <div class="footer-links">
          <a href="aboutUs.html">About Us</a>
          <a href="privacyPolicy.php">Privacy Policy</a>
          <a href="termsOfService.php">Terms of Service</a>
          <a href="cookiePolicy.php">Cookie Policy</a>
        </div>
      </div>
    </footer>

    <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./app.js"></script>
    <script>
        // Smooth scrolling for table of contents links
        document.addEventListener('DOMContentLoaded', function() {
            const tocLinks = document.querySelectorAll('.tocLink');
            
            tocLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // Highlight current section in table of contents
            const sections = document.querySelectorAll('.contentSection');
            const tocLinksList = document.querySelectorAll('.tocLink');
            
            const observerOptions = {
                threshold: 0.3,
                rootMargin: '-100px 0px -50% 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const id = entry.target.getAttribute('id');
                        tocLinksList.forEach(link => {
                            link.classList.remove('active');
                            if (link.getAttribute('href') === '#' + id) {
                                link.classList.add('active');
                            }
                        });
                    }
                });
            }, observerOptions);
            
            sections.forEach(section => {
                observer.observe(section);
            });
        });
    </script>
</body>
</html>