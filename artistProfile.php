<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lady Catherine Pemberton - Artist Profile | Yadawity Gallery</title>

    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
      integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="./components/Navbar/navbar.css" />`n    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css" />
    <link rel="stylesheet" href="./public/homePage.css" />
    <link rel="stylesheet" href="./public/artistProfile.css" />
  </head>

  <body>
    <!-- Navigation -->
    <?php include './components/includes/navbar.php'; ?>

    <?php include './components/includes/burger-menu.php'; ?>
    

    <!-- Artist Hero Section -->
    <section class="artist-hero">
      <div class="artist-hero-content">
        <div class="artist-profile-image">
          <img
            src="./image/artist-sitting-on-the-floor.jpg"
            alt="Lady Catherine Pemberton"
            class="artist-main-image"
          />
        </div>

        <div class="artist-info">
          <h1 class="artist-name">Lady Catherine Pemberton</h1>
          <p class="artist-specialty">Classical Portraiture Master</p>

          <div class="artist-badges">
            <div class="badge badge-yadawity">
              <i class="fas fa-crown"></i>
              Yadawity Partner
            </div>
            <div class="badge badge-verified">
              <i class="fas fa-check-circle"></i>
              Verified Artist
            </div>
            <div class="badge badge-exclusive">
              <i class="fas fa-star"></i>
              Exclusive Collection
            </div>
            <div class="badge badge-featured">
              <i class="fas fa-fire"></i>
              Featured Master
            </div>
          </div>

          <div class="artist-stats">
            <div class="stat-item">
              <span class="stat-number">127</span>
              <span class="stat-label">Masterpieces</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">15</span>
              <span class="stat-label">Years Experience</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">89</span>
              <span class="stat-label">Happy Clients</span>
            </div>
          </div>

          <div class="artist-actions">
            <a href="#products" class="btn-primary">
              <i class="fas fa-palette"></i>
              View Artworks
            </a>
            <a href="#about" class="btn-secondary">
              <i class="fas fa-info-circle"></i>
              About Artist
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section class="about-section" id="about">
      <div class="about-content">
        <div class="about-sidebar">
          <h3 style="color: var(--primary-brown); margin-bottom: 20px">
            Quick Info
          </h3>

          <div style="margin-bottom: 20px">
            <strong style="display: block; margin-bottom: 5px"
              >Location:</strong
            >
            <span style="color: var(--text-light)">London, United Kingdom</span>
          </div>

          <div style="margin-bottom: 20px">
            <strong style="display: block; margin-bottom: 5px"
              >Education:</strong
            >
            <span style="color: var(--text-light)">Royal Academy of Arts</span>
          </div>

          <div style="margin-bottom: 20px">
            <strong style="display: block; margin-bottom: 5px">Style:</strong>
            <span style="color: var(--text-light)">Classical Realism</span>
          </div>
        </div>

        <div class="about-main">
          <h2>About Lady Catherine</h2>

          <p class="about-text">
            Lady Catherine Pemberton stands as one of the most distinguished
            classical portrait artists of our time. With over fifteen years of
            dedicated practice, she has mastered the intricate techniques passed
            down through generations of Royal Academy masters.
          </p>

          <p class="about-text">
            Her work is characterized by an extraordinary attention to detail,
            capturing not just the physical likeness but the very essence and
            character of her subjects. Each portrait tells a story, revealing
            layers of personality through masterful use of light, shadow, and
            color.
          </p>

          <p class="about-text">
            Catherine's journey began at the prestigious Royal Academy of Arts,
            where she studied under renowned masters and developed her
            distinctive style that bridges classical techniques with
            contemporary sensibilities. Her portraits have graced the walls of
            private collections, galleries, and institutions across Europe.
          </p>

          <div class="achievements">
            <h3>Achievements & Recognition</h3>

            <div class="achievement-item">
              <div class="achievement-icon">
                <i class="fas fa-trophy"></i>
              </div>
              <div>
                <strong>Royal Academy Summer Exhibition</strong><br />
                <span style="color: var(--text-light)"
                  >Featured Artist (2019-2024)</span
                >
              </div>
            </div>

            <div class="achievement-item">
              <div class="achievement-icon">
                <i class="fas fa-medal"></i>
              </div>
              <div>
                <strong>Portrait Society of America</strong><br />
                <span style="color: var(--text-light)"
                  >Gold Medal Winner (2022)</span
                >
              </div>
            </div>

            <div class="achievement-item">
              <div class="achievement-icon">
                <i class="fas fa-star"></i>
              </div>
              <div>
                <strong>International Artist Magazine</strong><br />
                <span style="color: var(--text-light)"
                  >Artist of the Year (2023)</span
                >
              </div>
            </div>

            <div class="achievement-item">
              <div class="achievement-icon">
                <i class="fas fa-graduation-cap"></i>
              </div>
              <div>
                <strong>Master Class Instructor</strong><br />
                <span style="color: var(--text-light)"
                  >Yadawity Gallery & Royal Academy</span
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Artist Products/Artworks Section -->
    <section class="products-section" id="products">
      <div class="products-header">
        <h2>Featured Artworks</h2>
        <p style="color: var(--text-light); font-size: 1.2rem">
          Discover Catherine's most celebrated masterpieces
        </p>
      </div>

      <div class="mansoryLayoutProductCard">
        <div class="galleryContainer">
          <div class="enhanced-artwork-card fade-in-left delay-100" data-category="portraits" data-price="15000">
            <div class="artwork-image-container">
              <img
                class="enhanced-artwork-image"
                src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=280&fit=crop"
                alt="Portrait of Elegance"
              />
              
              <div class="artwork-overlay">
                <div class="quick-actions">
                  <button class="quick-action-btn" title="Quick View" data-action="view" data-id="1">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="enhanced-artwork-info">
              <div class="artwork-category">PORTRAITS</div>
              <h3 class="enhanced-artwork-title">Portrait of Elegance</h3>
              <p class="enhanced-artwork-artist">By Lady Catherine Pemberton</p>
              <p class="enhanced-artwork-price">£15,000</p>
              <p class="artwork-dimensions">18" × 24"</p>
              <p class="enhanced-artwork-description">
                A captivating oil painting that showcases the subject's grace and
                nobility through masterful classical techniques and refined brushwork.
              </p>
              
              <div class="artwork-actions">
                <button class="enhanced-add-to-cart" data-id="1">
                  ADD TO CART
                </button>
                <button class="wishlist-btn" data-id="1" title="Add to Wishlist">
                  <i class="fas fa-heart"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="enhanced-artwork-card fade-in-left delay-200" data-category="portraits" data-price="22000">
            <div class="artwork-image-container">
              <img
                class="enhanced-artwork-image"
                src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=400&h=320&fit=crop"
                alt="The Scholar's Gaze"
              />
              
              <div class="artwork-overlay">
                <div class="quick-actions">
                  <button class="quick-action-btn" title="Quick View" data-action="view" data-id="2">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="enhanced-artwork-info">
              <div class="artwork-category">PORTRAITS</div>
              <h3 class="enhanced-artwork-title">The Scholar's Gaze</h3>
              <p class="enhanced-artwork-artist">By Lady Catherine Pemberton</p>
              <p class="enhanced-artwork-price">£22,000</p>
              <p class="artwork-dimensions">20" × 30"</p>
              <p class="enhanced-artwork-description">
                An intimate portrayal capturing the intellectual depth and
                contemplative nature of a distinguished academic through luminous technique.
              </p>
              
              <div class="artwork-actions">
                <button class="enhanced-add-to-cart" data-id="2">
                  ADD TO CART
                </button>
                <button class="wishlist-btn" data-id="2" title="Add to Wishlist">
                  <i class="fas fa-heart"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="enhanced-artwork-card fade-in-left delay-300" data-category="portraits" data-price="35000">
            <div class="artwork-image-container">
              <img
                class="enhanced-artwork-image"
                src="https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=400&h=400&fit=crop"
                alt="Aristocratic Heritage"
              />
              
              <div class="artwork-overlay">
                <div class="quick-actions">
                  <button class="quick-action-btn" title="Quick View" data-action="view" data-id="3">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="enhanced-artwork-info">
              <div class="artwork-category">PORTRAITS</div>
              <h3 class="enhanced-artwork-title">Aristocratic Heritage</h3>
              <p class="enhanced-artwork-artist">By Lady Catherine Pemberton</p>
              <p class="enhanced-artwork-price">£35,000</p>
              <p class="artwork-dimensions">24" × 36"</p>
              <p class="enhanced-artwork-description">
                A grand commissioned portrait reflecting centuries of tradition
                and refinement captured in every masterful brushstroke.
              </p>
              
              <div class="artwork-actions">
                <button class="enhanced-add-to-cart" data-id="3">
                  ADD TO CART
                </button>
                <button class="wishlist-btn" data-id="3" title="Add to Wishlist">
                  <i class="fas fa-heart"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="enhanced-artwork-card fade-in-left delay-200" data-category="portraits" data-price="18000">
            <div class="artwork-image-container">
              <img
                class="enhanced-artwork-image"
                src="https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=400&h=200&fit=crop"
                alt="Young Virtuoso"
              />
              
              <div class="artwork-overlay">
                <div class="quick-actions">
                  <button class="quick-action-btn" title="Quick View" data-action="view" data-id="4">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="enhanced-artwork-info">
              <div class="artwork-category">PORTRAITS</div>
              <h3 class="enhanced-artwork-title">Young Virtuoso</h3>
              <p class="enhanced-artwork-artist">By Lady Catherine Pemberton</p>
              <p class="enhanced-artwork-price">£18,000</p>
              <p class="artwork-dimensions">16" × 20"</p>
              <p class="enhanced-artwork-description">
                A delicate portrait capturing the promise and passion of youth
                through luminous classical painting techniques and soft lighting.
              </p>
              
              <div class="artwork-actions">
                <button class="enhanced-add-to-cart" data-id="4">
                  ADD TO CART
                </button>
                <button class="wishlist-btn" data-id="4" title="Add to Wishlist">
                  <i class="fas fa-heart"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="enhanced-artwork-card fade-in-left delay-300" data-category="portraits" data-price="28000">
            <div class="artwork-image-container">
              <img
                class="enhanced-artwork-image"
                src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=240&fit=crop"
                alt="The Philanthropist"
              />
              
              <div class="artwork-overlay">
                <div class="quick-actions">
                  <button class="quick-action-btn" title="Quick View" data-action="view" data-id="5">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="enhanced-artwork-info">
              <div class="artwork-category">PORTRAITS</div>
              <h3 class="enhanced-artwork-title">The Philanthropist</h3>
              <p class="enhanced-artwork-artist">By Lady Catherine Pemberton</p>
              <p class="enhanced-artwork-price">£28,000</p>
              <p class="artwork-dimensions">22" × 30"</p>
              <p class="enhanced-artwork-description">
                A commanding portrait that reveals the subject's benevolent spirit
                and distinguished character through expert composition.
              </p>
              
              <div class="artwork-actions">
                <button class="enhanced-add-to-cart" data-id="5">
                  ADD TO CART
                </button>
                <button class="wishlist-btn" data-id="5" title="Add to Wishlist">
                  <i class="fas fa-heart"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="enhanced-artwork-card fade-in-left delay-400" data-category="portraits" data-price="32000">
            <div class="artwork-image-container">
              <img
                class="enhanced-artwork-image"
                src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=400&h=360&fit=crop"
                alt="Evening Reflection"
              />
              
              <div class="artwork-overlay">
                <div class="quick-actions">
                  <button class="quick-action-btn" title="Quick View" data-action="view" data-id="6">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="enhanced-artwork-info">
              <div class="artwork-category">PORTRAITS</div>
              <h3 class="enhanced-artwork-title">Evening Reflection</h3>
              <p class="enhanced-artwork-artist">By Lady Catherine Pemberton</p>
              <p class="enhanced-artwork-price">£32,000</p>
              <p class="artwork-dimensions">24" × 36"</p>
              <p class="enhanced-artwork-description">
                A masterful study in light and mood, capturing a moment of quiet
                contemplation with extraordinary depth and atmospheric beauty.
              </p>
              
              <div class="artwork-actions">
                <button class="enhanced-add-to-cart" data-id="6">
                  ADD TO CART
                </button>
                <button class="wishlist-btn" data-id="6" title="Add to Wishlist">
                  <i class="fas fa-heart"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="enhanced-artwork-card fade-in-left delay-300" data-category="portraits" data-price="45000">
            <div class="artwork-image-container">
              <img
                class="enhanced-artwork-image"
                src="https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=400&h=300&fit=crop"
                alt="Royal Commission"
                style="height: 300px; object-fit: cover"
              />
              
              <div class="artwork-overlay">
                <div class="quick-actions">
                  <button class="quick-action-btn" title="Quick View" data-action="view" data-id="7">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="enhanced-artwork-info">
              <div class="artwork-category">portraits</div>
              <h3 class="enhanced-artwork-title">Royal Commission</h3>
              <p class="enhanced-artwork-artist">by Lady Catherine Pemberton</p>
              <p class="enhanced-artwork-price">£45,000</p>
              <p class="artwork-dimensions">30" × 40"</p>
              <p class="enhanced-artwork-description">
                An official portrait commissioned for the Royal Collection,
                demonstrating the pinnacle of classical portraiture excellence.
              </p>
              
              <div class="artwork-actions">
                <button class="enhanced-add-to-cart" data-id="7">
                  Add to Cart
                </button>
                <button class="wishlist-btn" data-id="7" title="Add to Wishlist">
                  <i class="fas fa-heart"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="enhanced-artwork-card fade-in-left delay-400" data-category="portraits" data-price="38000">
            <div class="artwork-image-container">
              <img
                class="enhanced-artwork-image"
                src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=220&fit=crop"
                alt="Master's Study"
                style="height: 220px; object-fit: cover"
              />
              
              <div class="artwork-overlay">
                <div class="quick-actions">
                  <button class="quick-action-btn" title="Quick View" data-action="view" data-id="8">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="enhanced-artwork-info">
              <div class="artwork-category">portraits</div>
              <h3 class="enhanced-artwork-title">Master's Study</h3>
              <p class="enhanced-artwork-artist">by Lady Catherine Pemberton</p>
              <p class="enhanced-artwork-price">£38,000</p>
              <p class="artwork-dimensions">36" × 48"</p>
              <p class="enhanced-artwork-description">
                A preparatory study showcasing Catherine's technical mastery
                and her deep understanding of classical portraiture traditions.
              </p>
              
              <div class="artwork-actions">
                <button class="enhanced-add-to-cart" data-id="8">
                  Add to Cart
                </button>
                <button class="wishlist-btn" data-id="8" title="Add to Wishlist">
                  <i class="fas fa-heart"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Reviews & Feedback Section -->
    <section class="reviews-section" id="reviews">
      <div class="reviews-container">
        <div class="reviews-header">
          <h2>Client Reviews & Feedback</h2>
        </div>

        <div class="reviews-stats">
          <div class="rating-overview">
            <span class="rating-score">4.9</span>
            <div class="rating-stars">
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
            </div>
            <span class="rating-count">(127 reviews)</span>
          </div>
        </div>

        <div class="reviews-grid">
          <div class="review-card">
            <div class="review-header">
              <img
                src="https://randomuser.me/api/portraits/men/1.jpg"
                alt="Client Photo"
                class="review-avatar"
              />
              <div class="review-info">
                <span class="review-name">John Doe</span>
                <span class="review-date">March 15, 2024</span>
                <div class="review-rating">
                  <span class="star">★</span>
                  <span class="star">★</span>
                  <span class="star">★</span>
                  <span class="star">★</span>
                  <span class="star">★</span>
                </div>
              </div>
            </div>

            <p class="review-text">
              "I am absolutely thrilled with my portrait by Lady Catherine! She
              captured my likeness and personality perfectly. The attention to
              detail is astonishing, and the painting has received countless
              compliments. Highly recommend!"
            </p>

            <p class="review-artwork">
              Artwork: <strong>A Regal Presence</strong>
            </p>
          </div>

          <div class="review-card">
            <div class="review-header">
              <img
                src="https://randomuser.me/api/portraits/women/2.jpg"
                alt="Client Photo"
                class="review-avatar"
              />
              <div class="review-info">
                <span class="review-name">Jane Smith</span>
                <span class="review-date">March 10, 2024</span>
                <div class="review-rating">
                  <span class="star">★</span>
                  <span class="star">★</span>
                  <span class="star">★</span>
                  <span class="star">★</span>
                  <span class="star">★</span>
                </div>
              </div>
            </div>

            <p class="review-text">
              "Lady Catherine is a true master of her craft. The portrait she
              created for me is beyond what I could have imagined. Every time I
              look at it, I am transported back to the moment it was painted. An
              incredible experience!"
            </p>

            <p class="review-artwork">
              Artwork: <strong>Timeless Beauty</strong>
            </p>
          </div>

          <div class="review-card">
            <div class="review-header">
              <img
                src="https://randomuser.me/api/portraits/men/3.jpg"
                alt="Client Photo"
                class="review-avatar"
              />
              <div class="review-info">
                <span class="review-name">Michael Johnson</span>
                <span class="review-date">February 25, 2024</span>
                <div class="review-rating">
                  <span class="star">★</span>
                  <span class="star">★</span>
                  <span class="star">★</span>
                  <span class="star">★</span>
                  <span class="star">★</span>
                </div>
              </div>
            </div>

            <p class="review-text">
              "The experience of having my portrait painted by Lady Catherine
              was nothing short of extraordinary. She is not only a talented
              artist but also a wonderful person who makes you feel at ease. I
              can't recommend her highly enough!"
            </p>

            <p class="review-artwork">
              Artwork: <strong>Majestic Encounter</strong>
            </p>
          </div>
        </div>

        <!-- Simple Review Input -->
        <div class="simple-review-form">
          <h3>Leave a Quick Review</h3>
          <p class="form-subtitle">
            We value your feedback! Please share your experience.
          </p>

          <div class="review-form-container">
            <div class="form-section">
              <div class="form-section-title">
                <i class="fas fa-paint-brush"></i>
                Artwork Details
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label for="purchasedArtwork"
                    >Artwork Purchased
                    <span class="required-asterisk">*</span></label
                  >
                  <select id="purchasedArtwork" class="artwork-select">
                    <option value="">
                      Select the artwork you purchased...
                    </option>
                    <option value="Portrait of Elegance">
                      Portrait of Elegance - £15,000
                    </option>
                    <option value="The Scholar's Gaze">
                      The Scholar's Gaze - £22,000
                    </option>
                    <option value="Aristocratic Heritage">
                      Aristocratic Heritage - £35,000
                    </option>
                    <option value="Young Virtuoso">
                      Young Virtuoso - £18,000
                    </option>
                    <option value="The Philanthropist">
                      The Philanthropist - £28,000
                    </option>
                    <option value="Evening Reflection">
                      Evening Reflection - £32,000
                    </option>
                    <option value="Custom Commission">Custom Commission</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="form-section">
              <div class="form-section-title">
                <i class="fas fa-star"></i>
                Your Rating
              </div>

              <div class="rating-section">
                <h4>Overall Experience</h4>
                <div class="star-rating-container">
                  <div class="star-rating" data-type="overall">
                    <span class="star" data-value="1">★</span>
                    <span class="star" data-value="2">★</span>
                    <span class="star" data-value="3">★</span>
                    <span class="star" data-value="4">★</span>
                    <span class="star" data-value="5">★</span>
                  </div>
                </div>
                <p class="rating-description">
                  Click the stars to rate your overall experience with Lady
                  Catherine's artwork and service.
                </p>
              </div>
            </div>

            <div class="form-section">
              <div class="form-section-title">
                <i class="fas fa-comment-dots"></i>
                Your Review
              </div>

              <div class="review-text-section">
                <textarea
                  id="quickReview"
                  class="quick-review-input"
                  maxlength="500"
                  placeholder="Share your experience with Lady Catherine's artwork, the quality, your satisfaction, and any other thoughts..."
                ></textarea>
                <div class="character-counter" id="characterCounter">0/500</div>
              </div>
            </div>

            <div class="submit-section">
              <button class="submit-quick-review" id="submitQuickReview">
                <i class="fas fa-paper-plane"></i>
                Submit Review
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <?php include './components/includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./app.js"></script>
  </body>
</html>
