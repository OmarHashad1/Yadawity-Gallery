<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Yadawity Gallery</title>
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
    <link rel="stylesheet" href="./public/termsOfService.css" />
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
    <section class="termsHero">
        <div class="container">
            <div class="heroContent">
                <h1 class="heroTitle">Terms of Service</h1>
                <p class="heroSubtitle">Please read these terms carefully before using our platform. By using Yadawity Gallery, you agree to these terms.</p>
                <div class="lastUpdated">
                    <i class="fas fa-calendar-alt"></i>
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
                    <li><a href="#acceptance" class="tocLink">1. Acceptance of Terms</a></li>
                    <li><a href="#platform-description" class="tocLink">2. Platform Description</a></li>
                    <li><a href="#user-accounts" class="tocLink">3. User Accounts</a></li>
                    <li><a href="#user-conduct" class="tocLink">4. User Conduct</a></li>
                    <li><a href="#intellectual-property" class="tocLink">5. Intellectual Property</a></li>
                    <li><a href="#transactions" class="tocLink">6. Transactions and Payments</a></li>
                    <li><a href="#artist-terms" class="tocLink">7. Artist Terms</a></li>
                    <li><a href="#content-policies" class="tocLink">8. Content Policies</a></li>
                    <li><a href="#privacy" class="tocLink">9. Privacy</a></li>
                    <li><a href="#disclaimers" class="tocLink">10. Disclaimers</a></li>
                    <li><a href="#limitation-liability" class="tocLink">11. Limitation of Liability</a></li>
                    <li><a href="#termination" class="tocLink">12. Termination</a></li>
                    <li><a href="#governing-law" class="tocLink">13. Governing Law</a></li>
                    <li><a href="#changes-terms" class="tocLink">14. Changes to Terms</a></li>
                    <li><a href="#contact-information" class="tocLink">15. Contact Information</a></li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Terms Content -->
    <section class="termsContent">
        <div class="container">
            <div class="contentWrapper">
                
                <div class="contentSection" id="acceptance">
                    <h2>1. Acceptance of Terms</h2>
                    <p>Welcome to Yadawity Gallery ("Platform," "Service," "we," "us," or "our"). These Terms of Service ("Terms") govern your use of our online art gallery platform, including our website, mobile applications, and related services.</p>
                    
                    <p>By accessing or using our Platform, you agree to be bound by these Terms and all applicable laws and regulations. If you do not agree with any of these Terms, you are prohibited from using or accessing this Platform.</p>
                    
                    <p>These Terms constitute a legally binding agreement between you and Yadawity Gallery. We may modify these Terms at any time, and such modifications will be effective immediately upon posting.</p>
                </div>

                <div class="contentSection" id="platform-description">
                    <h2>2. Platform Description</h2>
                    <p>Yadawity Gallery is an online platform that connects artists with art enthusiasts, collectors, and buyers. Our services include:</p>
                    
                    <ul>
                        <li><strong>Art Marketplace:</strong> A platform for buying and selling original artworks, prints, and crafts</li>
                        <li><strong>Commission Services:</strong> Facilitating custom artwork commissions between artists and clients</li>
                        <li><strong>Art Courses:</strong> Educational content and courses taught by professional artists</li>
                        <li><strong>Gallery Exhibitions:</strong> Virtual and physical art exhibitions and showcases</li>
                        <li><strong>Artist Portfolios:</strong> Professional portfolio hosting and management tools</li>
                        <li><strong>Community Features:</strong> Forums, reviews, and social networking for art enthusiasts</li>
                    </ul>
                    
                    <p>We reserve the right to modify, suspend, or discontinue any aspect of our Platform at any time without prior notice.</p>
                </div>

                <div class="contentSection" id="user-accounts">
                    <h2>3. User Accounts</h2>
                    <h3>Account Registration</h3>
                    <p>To access certain features of our Platform, you must create an account. You agree to:</p>
                    
                    <ul>
                        <li>Provide accurate, current, and complete information during registration</li>
                        <li>Maintain and promptly update your account information</li>
                        <li>Maintain the security of your password and accept responsibility for all activities under your account</li>
                        <li>Notify us immediately of any unauthorized use of your account</li>
                    </ul>
                    
                    <h3>Account Types</h3>
                    <ul>
                        <li><strong>Buyer Accounts:</strong> For purchasing artworks and commissioning artists</li>
                        <li><strong>Artist Accounts:</strong> For selling artworks, accepting commissions, and teaching courses</li>
                        <li><strong>Gallery Accounts:</strong> For art galleries and institutions</li>
                    </ul>
                    
                    <p>You are responsible for all activities that occur under your account, whether or not you authorized such activities.</p>
                </div>

                <div class="contentSection" id="user-conduct">
                    <h2>4. User Conduct</h2>
                    <p>You agree not to use the Platform to:</p>
                    
                    <ul>
                        <li>Violate any applicable laws or regulations</li>
                        <li>Infringe upon the rights of others, including intellectual property rights</li>
                        <li>Upload, post, or transmit any content that is unlawful, harmful, threatening, abusive, harassing, defamatory, vulgar, obscene, or otherwise objectionable</li>
                        <li>Impersonate any person or entity or falsely state or misrepresent your affiliation with any person or entity</li>
                        <li>Engage in any fraudulent activity or misrepresent the authenticity or provenance of artworks</li>
                        <li>Spam, solicit, or contact other users for commercial purposes outside of legitimate Platform transactions</li>
                        <li>Attempt to gain unauthorized access to other users' accounts or our systems</li>
                        <li>Use automated systems, bots, or scripts to access or interact with the Platform</li>
                        <li>Interfere with or disrupt the Platform's operation or servers</li>
                    </ul>
                    
                    <p>We reserve the right to suspend or terminate accounts that violate these conduct rules.</p>
                </div>

                <div class="contentSection" id="intellectual-property">
                    <h2>5. Intellectual Property</h2>
                    <h3>Platform Content</h3>
                    <p>The Platform and its original content, features, and functionality are owned by Yadawity Gallery and are protected by international copyright, trademark, patent, trade secret, and other intellectual property laws.</p>
                    
                    <h3>User Content</h3>
                    <p>By uploading content to the Platform, you represent and warrant that:</p>
                    
                    <ul>
                        <li>You own or have the necessary rights to the content</li>
                        <li>Your content does not infringe upon the rights of any third party</li>
                        <li>You grant us a non-exclusive, worldwide, royalty-free license to use, display, and promote your content on the Platform</li>
                    </ul>
                    
                    <h3>Artist Works</h3>
                    <p>Artists retain full ownership of their original artworks. By listing works on our Platform, artists grant us limited rights to display and promote their works for Platform purposes only.</p>
                </div>

                <div class="contentSection" id="transactions">
                    <h2>6. Transactions and Payments</h2>
                    <h3>Purchase Process</h3>
                    <p>When you purchase artwork through our Platform:</p>
                    
                    <ul>
                        <li>You enter into a direct contract with the artist or seller</li>
                        <li>We facilitate the transaction but are not a party to the sale</li>
                        <li>All sales are final unless otherwise specified in our return policy</li>
                        <li>Prices are set by artists and may change without notice</li>
                    </ul>
                    
                    <h3>Payment Processing</h3>
                    <ul>
                        <li>We use third-party payment processors for secure transactions</li>
                        <li>We collect a service fee from each transaction as outlined in our fee schedule</li>
                        <li>Artists receive payment minus our service fee after successful delivery</li>
                        <li>Disputed transactions may result in payment holds pending resolution</li>
                    </ul>
                    
                    <h3>Refunds and Returns</h3>
                    <p>Our return policy varies by item type. Custom commissions are generally non-refundable. See our detailed return policy for specific terms.</p>
                </div>

                <div class="contentSection" id="artist-terms">
                    <h2>7. Artist Terms</h2>
                    <h3>Artist Obligations</h3>
                    <p>Artists using our Platform agree to:</p>
                    
                    <ul>
                        <li>Provide accurate descriptions and images of their artworks</li>
                        <li>Honor all sales and commission agreements</li>
                        <li>Ship items promptly and securely as described</li>
                        <li>Maintain professional standards in all communications</li>
                        <li>Comply with all applicable tax obligations</li>
                    </ul>
                    
                    <h3>Commission Guidelines</h3>
                    <ul>
                        <li>Clearly define commission terms, timelines, and pricing</li>
                        <li>Provide regular updates to commissioners</li>
                        <li>Deliver work that meets agreed-upon specifications</li>
                        <li>Handle revisions professionally within agreed parameters</li>
                    </ul>
                    
                    <h3>Artist Verification</h3>
                    <p>We may require verification of artist identity and credentials. Featured artists undergo additional vetting processes to ensure quality and authenticity.</p>
                </div>

                <div class="contentSection" id="content-policies">
                    <h2>8. Content Policies</h2>
                    <h3>Acceptable Content</h3>
                    <p>All content on our Platform must be:</p>
                    
                    <ul>
                        <li>Original or properly licensed</li>
                        <li>Appropriate for a general audience (or properly age-restricted)</li>
                        <li>Accurately described and categorized</li>
                        <li>Free from harmful or illegal material</li>
                    </ul>
                    
                    <h3>Prohibited Content</h3>
                    <ul>
                        <li>Copyrighted material without proper authorization</li>
                        <li>Hate speech, discrimination, or violent content</li>
                        <li>Adult content without proper age restrictions</li>
                        <li>False or misleading information about artworks</li>
                        <li>Content that violates any applicable laws</li>
                    </ul>
                    
                    <p>We reserve the right to remove any content that violates these policies without prior notice.</p>
                </div>

                <div class="contentSection" id="privacy">
                    <h2>9. Privacy</h2>
                    <p>Your privacy is important to us. Our collection, use, and protection of your personal information is governed by our <a href="privacyPolicy.php" class="inlineLink">Privacy Policy</a>, which is incorporated into these Terms by reference.</p>
                    
                    <p>By using our Platform, you consent to the collection and use of your information as described in our Privacy Policy.</p>
                </div>

                <div class="contentSection" id="disclaimers">
                    <h2>10. Disclaimers</h2>
                    <p>THE PLATFORM IS PROVIDED ON AN "AS IS" AND "AS AVAILABLE" BASIS. WE MAKE NO WARRANTIES, EXPRESS OR IMPLIED, INCLUDING:</p>
                    
                    <ul>
                        <li>The Platform will be uninterrupted, secure, or error-free</li>
                        <li>The accuracy, completeness, or reliability of content</li>
                        <li>The authenticity or quality of artworks listed on the Platform</li>
                        <li>The reliability or performance of artists or buyers</li>
                        <li>The security of transactions or communications</li>
                    </ul>
                    
                    <p>WE DISCLAIM ALL WARRANTIES, EXPRESS OR IMPLIED, INCLUDING WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT.</p>
                </div>

                <div class="contentSection" id="limitation-liability">
                    <h2>11. Limitation of Liability</h2>
                    <p>TO THE MAXIMUM EXTENT PERMITTED BY LAW, YADAWITY GALLERY SHALL NOT BE LIABLE FOR:</p>
                    
                    <ul>
                        <li>Any indirect, incidental, special, consequential, or punitive damages</li>
                        <li>Loss of profits, revenue, data, or use</li>
                        <li>Damages arising from transactions between users</li>
                        <li>Damages caused by third-party services or content</li>
                        <li>Any damages exceeding the amount you paid to us in the twelve months preceding the claim</li>
                    </ul>
                    
                    <p>Some jurisdictions do not allow the exclusion of certain warranties or limitations of liability, so these limitations may not apply to you.</p>
                </div>

                <div class="contentSection" id="termination">
                    <h2>12. Termination</h2>
                    <h3>Termination by You</h3>
                    <p>You may terminate your account at any time by contacting us or using the account deletion feature in your settings.</p>
                    
                    <h3>Termination by Us</h3>
                    <p>We may suspend or terminate your account if:</p>
                    
                    <ul>
                        <li>You violate these Terms or our policies</li>
                        <li>You engage in fraudulent or illegal activity</li>
                        <li>Your account remains inactive for an extended period</li>
                        <li>We discontinue the Platform or specific features</li>
                    </ul>
                    
                    <h3>Effect of Termination</h3>
                    <p>Upon termination, your right to use the Platform ceases immediately. We may retain certain information as required by law or for legitimate business purposes.</p>
                </div>

                <div class="contentSection" id="governing-law">
                    <h2>13. Governing Law</h2>
                    <p>These Terms are governed by and construed in accordance with the laws of Egypt, without regard to conflict of law principles.</p>
                    
                    <p>Any disputes arising from these Terms or your use of the Platform will be subject to the exclusive jurisdiction of the courts of Cairo, Egypt.</p>
                </div>

                <div class="contentSection" id="changes-terms">
                    <h2>14. Changes to Terms</h2>
                    <p>We reserve the right to modify these Terms at any time. When we make changes:</p>
                    
                    <ul>
                        <li>We will post the updated Terms on this page</li>
                        <li>We will update the "Last updated" date</li>
                        <li>For significant changes, we will notify users via email or platform notification</li>
                        <li>Continued use of the Platform after changes constitutes acceptance of the new Terms</li>
                    </ul>
                    
                    <p>We encourage you to review these Terms periodically for any changes.</p>
                </div>

                <div class="contentSection" id="contact-information">
                    <h2>15. Contact Information</h2>
                    <p>If you have any questions about these Terms of Service, please contact us:</p>
                    
                    <div class="contactInfo">
                        <div class="contactMethod">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <strong>Email:</strong>
                                <a href="mailto:legal@yadawity.com" class="inlineLink">legal@yadawity.com</a>
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
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong>Address:</strong>
                                <span>Yadawity Gallery<br>Cairo, Egypt</span>
                            </div>
                        </div>
                    </div>
                    
                    <p>We will respond to your inquiry within 30 days of receipt.</p>
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
