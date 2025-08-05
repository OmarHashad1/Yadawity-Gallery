<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookie Policy - Yadawity Gallery</title>
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
    <link rel="stylesheet" href="./public/cookiePolicy.css" />
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
    <section class="cookieHero">
        <div class="container">
            <div class="heroContent">
                <h1 class="heroTitle">Cookie Policy</h1>
                <p class="heroSubtitle">Learn how we use cookies to enhance your experience on our platform and how you can manage your preferences.</p>
                <div class="lastUpdated">
                    <i class="fas fa-cookie-bite"></i>
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
                    <li><a href="#what-are-cookies" class="tocLink">1. What Are Cookies?</a></li>
                    <li><a href="#how-we-use-cookies" class="tocLink">2. How We Use Cookies</a></li>
                    <li><a href="#types-of-cookies" class="tocLink">3. Types of Cookies We Use</a></li>
                    <li><a href="#essential-cookies" class="tocLink">4. Essential Cookies</a></li>
                    <li><a href="#performance-cookies" class="tocLink">5. Performance Cookies</a></li>
                    <li><a href="#functionality-cookies" class="tocLink">6. Functionality Cookies</a></li>
                    <li><a href="#advertising-cookies" class="tocLink">7. Advertising Cookies</a></li>
                    <li><a href="#third-party-cookies" class="tocLink">8. Third-Party Cookies</a></li>
                    <li><a href="#managing-cookies" class="tocLink">9. Managing Your Cookie Preferences</a></li>
                    <li><a href="#browser-settings" class="tocLink">10. Browser Settings</a></li>
                    <li><a href="#mobile-settings" class="tocLink">11. Mobile Device Settings</a></li>
                    <li><a href="#consent-withdrawal" class="tocLink">12. Withdrawing Consent</a></li>
                    <li><a href="#updates-policy" class="tocLink">13. Updates to This Policy</a></li>
                    <li><a href="#contact-us" class="tocLink">14. Contact Us</a></li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Cookie Content -->
    <section class="cookieContent">
        <div class="container">
            <div class="contentWrapper">
                
                <div class="contentSection" id="what-are-cookies">
                    <h2>1. What Are Cookies?</h2>
                    <p>Cookies are small text files that are stored on your device (computer, tablet, or mobile phone) when you visit our website. They contain information about your browsing activities and preferences, helping us provide you with a better, more personalized experience.</p>
                    
                    <p>Cookies are widely used by website owners to make their websites work more efficiently and to provide analytical information. They do not typically contain any information that personally identifies a user, but personal information that we store about you may be linked to the information stored in and obtained from cookies.</p>
                    
                    <div class="highlightBox">
                        <i class="fas fa-info-circle"></i>
                        <p><strong>Important:</strong> Cookies cannot access, read, or modify any other data on your computer and cannot be used to deliver viruses or malware to your device.</p>
                    </div>
                </div>

                <div class="contentSection" id="how-we-use-cookies">
                    <h2>2. How We Use Cookies</h2>
                    <p>At Yadawity Gallery, we use cookies for several important purposes:</p>
                    
                    <ul>
                        <li><strong>Essential Functionality:</strong> To enable core features like user authentication, shopping cart functionality, and security</li>
                        <li><strong>User Experience:</strong> To remember your preferences, language settings, and customize your browsing experience</li>
                        <li><strong>Performance Analysis:</strong> To understand how visitors interact with our website and identify areas for improvement</li>
                        <li><strong>Content Personalization:</strong> To show you relevant artworks and recommendations based on your interests</li>
                        <li><strong>Marketing:</strong> To deliver targeted advertisements and measure the effectiveness of our marketing campaigns</li>
                        <li><strong>Security:</strong> To protect against fraud and ensure the security of your account and transactions</li>
                    </ul>
                </div>

                <div class="contentSection" id="types-of-cookies">
                    <h2>3. Types of Cookies We Use</h2>
                    <p>We categorize cookies based on their duration and origin:</p>
                    
                    <h3>By Duration:</h3>
                    <ul>
                        <li><strong>Session Cookies:</strong> Temporary cookies that are deleted when you close your browser</li>
                        <li><strong>Persistent Cookies:</strong> Cookies that remain on your device for a specified period or until manually deleted</li>
                    </ul>
                    
                    <h3>By Origin:</h3>
                    <ul>
                        <li><strong>First-Party Cookies:</strong> Set directly by Yadawity Gallery</li>
                        <li><strong>Third-Party Cookies:</strong> Set by external services we use (analytics, advertising, social media)</li>
                    </ul>
                </div>

                <div class="contentSection" id="essential-cookies">
                    <h2>4. Essential Cookies</h2>
                    <p>These cookies are necessary for the website to function properly and cannot be switched off in our systems. They are usually only set in response to actions made by you, such as logging in, making purchases, or setting privacy preferences.</p>
                    
                    <div class="cookieTable">
                        <table>
                            <thead>
                                <tr>
                                    <th>Cookie Name</th>
                                    <th>Purpose</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>session_id</td>
                                    <td>Maintains your session while browsing</td>
                                    <td>Session</td>
                                </tr>
                                <tr>
                                    <td>auth_token</td>
                                    <td>Keeps you logged in securely</td>
                                    <td>30 days</td>
                                </tr>
                                <tr>
                                    <td>cart_contents</td>
                                    <td>Remembers items in your shopping cart</td>
                                    <td>7 days</td>
                                </tr>
                                <tr>
                                    <td>csrf_token</td>
                                    <td>Protects against security attacks</td>
                                    <td>Session</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="contentSection" id="performance-cookies">
                    <h2>5. Performance Cookies</h2>
                    <p>These cookies allow us to count visits and traffic sources so we can measure and improve the performance of our site. They help us know which pages are most popular and see how visitors move around the site.</p>
                    
                    <div class="cookieTable">
                        <table>
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Purpose</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Google Analytics</td>
                                    <td>Website traffic analysis and user behavior tracking</td>
                                    <td>2 years</td>
                                </tr>
                                <tr>
                                    <td>Hotjar</td>
                                    <td>User experience analysis and heatmap generation</td>
                                    <td>1 year</td>
                                </tr>
                                <tr>
                                    <td>Internal Analytics</td>
                                    <td>Platform-specific performance metrics</td>
                                    <td>1 year</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="contentSection" id="functionality-cookies">
                    <h2>6. Functionality Cookies</h2>
                    <p>These cookies enable the website to provide enhanced functionality and personalization. They may be set by us or by third-party providers whose services we have added to our pages.</p>
                    
                    <div class="cookieTable">
                        <table>
                            <thead>
                                <tr>
                                    <th>Function</th>
                                    <th>Purpose</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Language Preference</td>
                                    <td>Remembers your preferred language</td>
                                    <td>1 year</td>
                                </tr>
                                <tr>
                                    <td>Theme Settings</td>
                                    <td>Saves your display preferences</td>
                                    <td>6 months</td>
                                </tr>
                                <tr>
                                    <td>Wishlist</td>
                                    <td>Stores your saved artworks</td>
                                    <td>1 year</td>
                                </tr>
                                <tr>
                                    <td>Recently Viewed</td>
                                    <td>Shows recently browsed artworks</td>
                                    <td>30 days</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="contentSection" id="advertising-cookies">
                    <h2>7. Advertising Cookies</h2>
                    <p>These cookies are used to deliver advertisements that are more relevant to you and your interests. They are also used to limit the number of times you see an advertisement and measure the effectiveness of advertising campaigns.</p>
                    
                    <div class="cookieTable">
                        <table>
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Purpose</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Google Ads</td>
                                    <td>Targeted advertising and remarketing</td>
                                    <td>30 days</td>
                                </tr>
                                <tr>
                                    <td>Facebook Pixel</td>
                                    <td>Social media advertising optimization</td>
                                    <td>90 days</td>
                                </tr>
                                <tr>
                                    <td>Instagram Ads</td>
                                    <td>Visual platform advertising targeting</td>
                                    <td>30 days</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="contentSection" id="third-party-cookies">
                    <h2>8. Third-Party Cookies</h2>
                    <p>Some cookies on our site are set by third-party services that appear on our pages. We use various third-party services to enhance your experience:</p>
                    
                    <ul>
                        <li><strong>Social Media:</strong> Facebook, Instagram, Twitter widgets and sharing buttons</li>
                        <li><strong>Payment Processing:</strong> Stripe, PayPal for secure payment handling</li>
                        <li><strong>Customer Support:</strong> Live chat and help desk services</li>
                        <li><strong>Content Delivery:</strong> CDNs for faster loading of images and assets</li>
                        <li><strong>Marketing Tools:</strong> Email marketing and automation platforms</li>
                        <li><strong>Security Services:</strong> Fraud detection and prevention tools</li>
                    </ul>
                    
                    <p>These third parties may use cookies to collect information about your online activities across different websites. Please refer to their respective privacy policies for more information.</p>
                </div>

                <div class="contentSection" id="managing-cookies">
                    <h2>9. Managing Your Cookie Preferences</h2>
                    <p>You have several options for managing cookies on our website:</p>
                    
                    <div class="cookieControls">
                        <div class="controlMethod">
                            <h3><i class="fas fa-cogs"></i> Cookie Consent Manager</h3>
                            <p>Use our cookie consent banner to accept or reject different categories of cookies when you first visit our site.</p>
                            <button class="cookieBtn" onclick="showCookiePreferences()">
                                <i class="fas fa-cookie-bite"></i>
                                Manage Cookie Preferences
                            </button>
                        </div>
                        
                        <div class="controlMethod">
                            <h3><i class="fas fa-user-cog"></i> Account Settings</h3>
                            <p>Logged-in users can manage cookie preferences from their account settings page.</p>
                            <a href="profile.php" class="cookieBtn">
                                <i class="fas fa-user"></i>
                                Go to Account Settings
                            </a>
                        </div>
                    </div>
                </div>

                <div class="contentSection" id="browser-settings">
                    <h2>10. Browser Settings</h2>
                    <p>You can also control cookies through your browser settings. Here's how to manage cookies in popular browsers:</p>
                    
                    <div class="browserGuides">
                        <div class="browserGuide">
                            <h3><i class="fab fa-chrome"></i> Google Chrome</h3>
                            <ol>
                                <li>Click the three dots menu → Settings</li>
                                <li>Navigate to Privacy and Security → Cookies and other site data</li>
                                <li>Choose your preferred cookie settings</li>
                            </ol>
                        </div>
                        
                        <div class="browserGuide">
                            <h3><i class="fab fa-firefox"></i> Mozilla Firefox</h3>
                            <ol>
                                <li>Click the menu button → Options</li>
                                <li>Select Privacy & Security panel</li>
                                <li>In the Cookies and Site Data section, adjust your settings</li>
                            </ol>
                        </div>
                        
                        <div class="browserGuide">
                            <h3><i class="fab fa-safari"></i> Safari</h3>
                            <ol>
                                <li>Safari menu → Preferences</li>
                                <li>Click the Privacy tab</li>
                                <li>Choose your cookie preferences</li>
                            </ol>
                        </div>
                        
                        <div class="browserGuide">
                            <h3><i class="fab fa-edge"></i> Microsoft Edge</h3>
                            <ol>
                                <li>Click the three dots menu → Settings</li>
                                <li>Select Cookies and site permissions</li>
                                <li>Configure your cookie settings</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="contentSection" id="mobile-settings">
                    <h2>11. Mobile Device Settings</h2>
                    <p>On mobile devices, you can manage cookies through your device's browser settings or our mobile app settings:</p>
                    
                    <h3>iOS (iPhone/iPad)</h3>
                    <ul>
                        <li>Settings → Safari → Privacy & Security</li>
                        <li>Toggle "Block All Cookies" or "Prevent Cross-Site Tracking"</li>
                    </ul>
                    
                    <h3>Android</h3>
                    <ul>
                        <li>Open your browser → Menu → Settings → Privacy</li>
                        <li>Adjust cookie settings according to your preferences</li>
                    </ul>
                    
                    <h3>Mobile App</h3>
                    <ul>
                        <li>App Settings → Privacy → Cookie Preferences</li>
                        <li>Customize your cookie settings within the app</li>
                    </ul>
                </div>

                <div class="contentSection" id="consent-withdrawal">
                    <h2>12. Withdrawing Consent</h2>
                    <p>You can withdraw your consent for non-essential cookies at any time. Here's how:</p>
                    
                    <ul>
                        <li>Use the "Manage Cookie Preferences" button at any time</li>
                        <li>Clear your browser's cookies and browsing data</li>
                        <li>Contact us directly to request cookie preference reset</li>
                        <li>Adjust settings in your user account dashboard</li>
                    </ul>
                    
                    <div class="warningBox">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p><strong>Note:</strong> Disabling certain cookies may affect the functionality of our website and limit your user experience.</p>
                    </div>
                </div>

                <div class="contentSection" id="updates-policy">
                    <h2>13. Updates to This Policy</h2>
                    <p>We may update this Cookie Policy from time to time to reflect changes in technology, legislation, or our business practices. When we make significant changes:</p>
                    
                    <ul>
                        <li>We will update the "Last updated" date at the top of this policy</li>
                        <li>We will notify users via email or website notification</li>
                        <li>We may request renewed consent for new cookie uses</li>
                        <li>Changes will be effective immediately upon posting</li>
                    </ul>
                    
                    <p>We encourage you to review this policy periodically to stay informed about how we use cookies.</p>
                </div>

                <div class="contentSection" id="contact-us">
                    <h2>14. Contact Us</h2>
                    <p>If you have any questions about our use of cookies or this policy, please contact us:</p>
                    
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
                        
                        <div class="contactMethod">
                            <i class="fas fa-comment-dots"></i>
                            <div>
                                <strong>Live Chat:</strong>
                                <span>Available on our website during business hours</span>
                            </div>
                        </div>
                        
                        <div class="contactMethod">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong>Address:</strong>
                                <span>Yadawity Gallery<br>Cairo, Egypt</span>
                            </div>
                        </div>
                    </div>
                    
                    <p>We are committed to addressing your privacy concerns and will respond to your inquiry within 30 days.</p>
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
                  d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.40z"
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
        // Cookie management functions
        function showCookiePreferences() {
            alert('Cookie preferences manager would open here. This is a demo implementation.');
        }
        
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
