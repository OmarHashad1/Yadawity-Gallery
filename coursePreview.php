<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mastering Oil Painting Techniques - Yadawity Gallery</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="./components/Navbar/navbar.css" />`n    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css">
    <link rel="stylesheet" href="./public/homePage.css">
    <link rel="stylesheet" href="./public/course-preview.css">
</head>
<body>
    <!-- Navigation -->
    <?php include './components/includes/navbar.php'; ?>

    <?php include './components/includes/burger-menu.php'; ?>
        
      </div>
    </div>

    <div class="mobileSearchOverlay" id="mobileSearchOverlay">
      <div class="mobileSearchContainer">
        <input
          type="text"
          placeholder="Search artists, artworks..."
          class="mobileSearchInput"
          id="mobileSearchInput"
        />
        <button class="mobileSearchClose" id="mobileSearchClose">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="searchSuggestions" id="mobileSearchSuggestions"></div>
    </div>

    <!-- Main Container -->
    <div class="course-preview-container">
        <!-- Hero Section -->
        <div class="course-hero">
            <div class="hero-content">
                <div class="hero-main">
                    <div class="hero-info">
                        <div class="course-category">
                            <i class="fas fa-palette"></i>
                            <span>Oil Painting</span>
                        </div>
                        
                        <h1 class="course-title">Mastering Oil Painting Techniques: From Beginner to Advanced</h1>
                        
                        <p class="course-description">
                            Learn the fundamentals and advanced techniques of oil painting from master artist Leonardo Rosetti. 
                            This comprehensive course covers everything from color theory to professional finishing techniques.
                        </p>

                        <div class="course-stats">
                            <div class="rating-section">
                                <div class="rating-stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <span class="rating-score">4.8</span>
                                <span class="rating-count">(2,847 ratings)</span>
                            </div>
                            
                            <div class="students-enrolled">
                                <i class="fas fa-users"></i>
                                <span>12,543 students enrolled</span>
                            </div>
                        </div>

                        <div class="instructor-info">
                            <img src="./image/Artist-PainterLookingAtCamera.webp" alt="Leonardo Rosetti" class="instructor-avatar">
                            <div class="instructor-details">
                                <span class="created-by">Created by</span>
                                <a href="artistProfile.php" class="instructor-name">Leonardo Rosetti</a>
                                <div class="instructor-credentials">
                                    <span>Master Artist</span>
                                    <span class="separator">•</span>
                                    <span>15+ years experience</span>
                                </div>
                            </div>
                        </div>

                        <div class="course-updated">
                            <i class="fas fa-info-circle"></i>
                            <span>Last updated: December 2024</span>
                            <span class="language">
                                <i class="fas fa-globe"></i>
                                English, Arabic
                            </span>
                        </div>
                    </div>

                    <div class="course-preview-card">
                        <div class="preview-video">
                            <img src="./image/slide1.jpg" alt="Course Preview" class="preview-image">
                            <button class="play-button">
                                <i class="fas fa-play"></i>
                            </button>
                            <span class="preview-label">Preview this course</span>
                        </div>

                        <div class="pricing-section">
                            <div class="price-info">
                                <span class="current-price">$89.99</span>
                                <span class="original-price">$149.99</span>
                                <span class="discount">40% off</span>
                            </div>
                            <div class="sale-timer">
                                <i class="fas fa-clock"></i>
                                <span class="timer-text">4 days left at this price!</span>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button class="btn-primary buy-now">
                                Buy now
                            </button>
                        </div>

                        <div class="course-includes">
                            <h4>This course includes:</h4>
                            <div class="includes-list">
                                <div class="include-item">
                                    <i class="fas fa-play-circle"></i>
                                    <span>24.5 hours on-demand video</span>
                                </div>
                                <div class="include-item">
                                    <i class="fas fa-file-download"></i>
                                    <span>15 downloadable resources</span>
                                </div>
                                <div class="include-item">
                                    <i class="fas fa-infinity"></i>
                                    <span>Full lifetime access</span>
                                </div>
                                <div class="include-item">
                                    <i class="fas fa-mobile-alt"></i>
                                    <span>Access on mobile and TV</span>
                                </div>
                                <div class="include-item">
                                    <i class="fas fa-certificate"></i>
                                    <span>Certificate of completion</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Content Sections -->
        <div class="course-content-wrapper">
            <div class="main-content">
                <!-- What You'll Learn Section -->
                <section class="course-section what-learn">
                    <h2>What you'll learn</h2>
                    <div class="learning-grid">
                        <div class="learning-item">
                            <i class="fas fa-check"></i>
                            <span>Master fundamental oil painting techniques and color theory</span>
                        </div>
                        <div class="learning-item">
                            <i class="fas fa-check"></i>
                            <span>Create realistic portraits with proper proportions and shading</span>
                        </div>
                        <div class="learning-item">
                            <i class="fas fa-check"></i>
                            <span>Understand brush techniques for different textures and effects</span>
                        </div>
                        <div class="learning-item">
                            <i class="fas fa-check"></i>
                            <span>Learn professional mixing and blending techniques</span>
                        </div>
                        <div class="learning-item">
                            <i class="fas fa-check"></i>
                            <span>Create stunning landscape paintings with depth and atmosphere</span>
                        </div>
                        <div class="learning-item">
                            <i class="fas fa-check"></i>
                            <span>Develop your own artistic style and creative voice</span>
                        </div>
                        <div class="learning-item">
                            <i class="fas fa-check"></i>
                            <span>Handle oil painting tools and materials like a professional</span>
                        </div>
                        <div class="learning-item">
                            <i class="fas fa-check"></i>
                            <span>Complete 10+ guided painting projects from start to finish</span>
                        </div>
                    </div>
                </section>

                <!-- Course Content Section -->
                <section class="course-section course-curriculum">
                    <div class="curriculum-header">
                        <h2>Course content</h2>
                        <div class="curriculum-stats">
                            <span>8 sections</span>
                            <span class="separator">•</span>
                            <span>64 lectures</span>
                            <span class="separator">•</span>
                            <span>24h 32m total length</span>
                        </div>
                    </div>

                    <div class="curriculum-sections">
                        <div class="curriculum-section">
                            <div class="section-header">
                                <button class="section-toggle">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                                <h3>Introduction to Oil Painting</h3>
                                <div class="section-info">
                                    <span>8 lectures</span>
                                    <span class="separator">•</span>
                                    <span>2h 15m</span>
                                </div>
                            </div>
                            <div class="section-content">
                                <div class="lecture-item">
                                    <div class="lecture-info">
                                        <i class="fas fa-play-circle"></i>
                                        <span class="lecture-title">Welcome & Course Overview</span>
                                    </div>
                                    <div class="lecture-meta">
                                        <span class="lecture-duration">15:30</span>
                                        <button class="preview-btn">Preview</button>
                                    </div>
                                </div>
                                <div class="lecture-item">
                                    <div class="lecture-info">
                                        <i class="fas fa-play-circle"></i>
                                        <span class="lecture-title">Essential Tools and Materials</span>
                                    </div>
                                    <div class="lecture-meta">
                                        <span class="lecture-duration">22:45</span>
                                    </div>
                                </div>
                                <div class="lecture-item">
                                    <div class="lecture-info">
                                        <i class="fas fa-file-pdf"></i>
                                        <span class="lecture-title">Materials Shopping List (PDF)</span>
                                    </div>
                                    <div class="lecture-meta">
                                        <span class="lecture-duration">Download</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="curriculum-section">
                            <div class="section-header">
                                <button class="section-toggle">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                <h3>Color Theory and Mixing</h3>
                                <div class="section-info">
                                    <span>10 lectures</span>
                                    <span class="separator">•</span>
                                    <span>3h 20m</span>
                                </div>
                            </div>
                        </div>

                        <div class="curriculum-section">
                            <div class="section-header">
                                <button class="section-toggle">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                <h3>Basic Brush Techniques</h3>
                                <div class="section-info">
                                    <span>8 lectures</span>
                                    <span class="separator">•</span>
                                    <span>2h 45m</span>
                                </div>
                            </div>
                        </div>

                        <div class="curriculum-section">
                            <div class="section-header">
                                <button class="section-toggle">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                <h3>Portrait Painting Fundamentals</h3>
                                <div class="section-info">
                                    <span>12 lectures</span>
                                    <span class="separator">•</span>
                                    <span>4h 10m</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button class="show-all-sections">
                        <span>Show all sections</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </section>

                <!-- Requirements Section -->
                <section class="course-section requirements">
                    <h2>Requirements</h2>
                    <ul class="requirements-list">
                        <li>No prior painting experience required - this course starts from the basics</li>
                        <li>Basic art supplies (brushes, paints, canvas) - full shopping list provided</li>
                        <li>Enthusiasm to learn and practice regularly</li>
                        <li>Access to a well-lit workspace for painting</li>
                        <li>Willingness to experiment and make mistakes as part of the learning process</li>
                    </ul>
                </section>

                <!-- Description Section -->
                <section class="course-section description">
                    <h2>Description</h2>
                    <div class="description-content">
                        <p>Welcome to the most comprehensive oil painting course available online! Whether you're a complete beginner or looking to refine your existing skills, this course will take you on a journey from basic techniques to advanced mastery.</p>
                        
                        <p><strong>What makes this course special?</strong></p>
                        <p>This isn't just another art course. It's a complete transformation of your artistic abilities, taught by Leonardo Rosetti, a master artist with over 15 years of professional experience and thousands of satisfied students worldwide.</p>

                        <h3>Course Highlights:</h3>
                        <ul>
                            <li><strong>Comprehensive Curriculum:</strong> 8 detailed sections covering every aspect of oil painting</li>
                            <li><strong>Hands-on Projects:</strong> 10+ complete painting projects you'll create during the course</li>
                            <li><strong>Professional Techniques:</strong> Learn the same methods used by master artists throughout history</li>
                            <li><strong>Personal Feedback:</strong> Get constructive feedback on your work from the instructor</li>
                            <li><strong>Lifetime Access:</strong> Learn at your own pace with permanent access to all materials</li>
                        </ul>

                        <p>By the end of this course, you'll have the confidence and skills to create stunning oil paintings that you'll be proud to display. Join thousands of students who have already transformed their artistic abilities!</p>
                        
                        <button class="show-more-btn">Show more <i class="fas fa-chevron-down"></i></button>
                    </div>
                </section>

                <!-- Student Reviews Section -->
                <section class="course-section reviews">
                    <div class="reviews-header">
                        <h2>Instructor Reviews</h2>
                        <div class="rating-overview">
                            <div class="overall-rating">
                                <span class="rating-number">4.8</span>
                                <div class="rating-stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <span class="rating-text">Course Rating</span>
                            </div>
                        </div>
                    </div>

                    <div class="reviews-list">
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">S</div>
                                    <div class="reviewer-details">
                                        <span class="reviewer-name">Sarah Mitchell</span>
                                        <div class="review-rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                </div>
                                <span class="review-date">2 weeks ago</span>
                            </div>
                            <div class="review-content">
                                <p>This course exceeded all my expectations! Leonardo's teaching style is incredible - he breaks down complex techniques into easy-to-follow steps. I went from never touching a paintbrush to creating my first portrait in just 6 weeks. Highly recommended!</p>
                            </div>
                            <div class="review-actions">
                                <button class="report-btn">
                                    <i class="fas fa-flag"></i>
                                    Report
                                </button>
                            </div>
                        </div>

                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">M</div>
                                    <div class="reviewer-details">
                                        <span class="reviewer-name">Michael Rodriguez</span>
                                        <div class="review-rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                </div>
                                <span class="review-date">1 month ago</span>
                            </div>
                            <div class="review-content">
                                <p>The course structure is perfect for beginners. Each lesson builds on the previous one, and the projects are engaging and challenging. The materials list was very helpful too. Worth every penny!</p>
                            </div>
                            <div class="review-actions">
                                <button class="report-btn">
                                    <i class="fas fa-flag"></i>
                                    Report
                                </button>
                            </div>
                        </div>
                    </div>

                    <button class="show-more-reviews">Show all reviews</button>
                </section>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include './components/includes/footer.php'; ?>

    <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./public/course-preview.js"></script>
    <script src="./app.js"></script>
</body>
</html>