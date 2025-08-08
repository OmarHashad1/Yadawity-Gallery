<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Yadawity Gallery: Discover classical art, healing, and heritage. Explore master artists, curated artworks, and testimonials from art lovers and collectors.">
    <title>Yadawity Gallery</title>

    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
      integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="./components/Navbar/navbar.css" />
    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css" />
    <link rel="stylesheet" href="./public/homePage.css" />

  </head>
  <body>
    <?php include './components/includes/navbar.php'; ?>

    <?php include './components/includes/burger-menu.php'; ?>

    <section class="hero">
      <h1>
        <span>WHERE CLASSICAL ART</span>
        <span>MEETS</span>
        <span class="highlight1">HEALING</span>
        <span class="highlight2">& HERITAGE</span>
      </h1>
      <p>
        A distinguished establishment preserving the finest traditions of art,
        fostering therapeutic healing, and connecting connoisseurs with
        masterful creations.
      </p>
    </section>

    <div class="sectionHeaderContainer">
      <div class="artisanDecorativeDivider fade-in-up">
        <div class="artisanOrnamentalIcon">
          <i class="fa-solid fa-palette"></i>
        </div>
      </div>

      <div class="sectionHeader">
        <h2 class="fade-in-up delay-100">Our professional Artist</h2>
        <p class="fade-in-up delay-200">
          Discover exceptional talent from our curated fellowship of master
          artists and distinguished craftspeople
        </p>
      </div>
      <div class="artistCardSection">
        <!--Artist Card 1-->
        <div class="profileCard fade-in-up delay-200">
          <div class="profileHeader">
            <img
              src="./image/images.jpeg"
              alt="Artist Photo"
              class="profileImage"
            />
            <div class="ratingBadge">
              <div class="starsContainer">
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
              </div>
              <span class="ratingText">4.9</span>
            </div>
            <div class="academyBadge">Royal Academy Member</div>
          </div>

          <div class="profileContent">
            <h3 class="profileName">Lady Catherine<br />Pemberton</h3>
            <p class="profileSpecialty">Classical Portraiture</p>

            <div class="profileStats">
              <span class="masterpiecesCount">127 masterpieces</span>
            </div>

            <button class="viewPortfolioBtn">View Portfolio</button>
          </div>
        </div>
        <!--Artist Card 2-->
        <div class="profileCard fade-in-up delay-300">
          <div class="profileHeader">
            <img
              src="./image/Artist-PainterLookingAtCamera.webp"
              alt="Artist Photo"
              class="profileImage"
            />
            <div class="ratingBadge">
              <div class="starsContainer">
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
              </div>
              <span class="ratingText">4.9</span>
            </div>
            <div class="academyBadge">Royal Academy Member</div>
          </div>

          <div class="profileContent">
            <h3 class="profileName">Lady Catherine<br />Pemberton</h3>
            <p class="profileSpecialty">Classical Portraiture</p>

            <div class="profileStats">
              <span class="masterpiecesCount">127 masterpieces</span>
            </div>

            <button class="viewPortfolioBtn">View Portfolio</button>
          </div>
        </div>
        <!--Artist Card 3-->
        <div class="profileCard fade-in-up delay-400">
          <div class="profileHeader">
            <img
              src="./image/artist-sitting-on-the-floor.jpg"
              alt="Artist Photo"
              class="profileImage"
            />
            <div class="ratingBadge">
              <div class="starsContainer">
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
              </div>
              <span class="ratingText">4.9</span>
            </div>
            <div class="academyBadge">Yadawity Partner</div>
          </div>

          <div class="profileContent">
            <h3 class="profileName">Lady Catherine<br />Pemberton</h3>
            <p class="profileSpecialty">Classical Portraiture</p>

            <div class="profileStats">
              <span class="masterpiecesCount">127 masterpieces</span>
            </div>

            <button class="viewPortfolioBtn">View Portfolio</button>
          </div>
        </div>

        <!--Artist Card 4-->
        <div class="profileCard fade-in-up delay-200">
          <div class="profileHeader">
            <img
              src="./image/images.jpeg"
              alt="Artist Photo"
              class="profileImage"
            />
            <div class="ratingBadge">
              <div class="starsContainer">
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
              </div>
              <span class="ratingText">4.9</span>
            </div>
            <div class="academyBadge">Yadawity Partner</div>
          </div>

          <div class="profileContent">
            <h3 class="profileName">Lady Catherine<br />Pemberton</h3>
            <p class="profileSpecialty">Classical Portraiture</p>

            <div class="profileStats">
              <span class="masterpiecesCount">127 masterpieces</span>
            </div>

            <button class="viewPortfolioBtn">View Portfolio</button>
          </div>
        </div>
        <!--Artist Card 5-->
        <div class="profileCard fade-in-up delay-300">
          <div class="profileHeader">
            <img
              src="./image/Artist-PainterLookingAtCamera.webp"
              alt="Artist Photo"
              class="profileImage"
            />
            <div class="ratingBadge">
              <div class="starsContainer">
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
              </div>
              <span class="ratingText">4.9</span>
            </div>
            <div class="academyBadge">Yadawity Partner</div>
          </div>

          <div class="profileContent">
            <h3 class="profileName">Lady Catherine<br />Pemberton</h3>
            <p class="profileSpecialty">Classical Portraiture</p>

            <div class="profileStats">
              <span class="masterpiecesCount">127 masterpieces</span>
            </div>

            <button class="viewPortfolioBtn">View Portfolio</button>
          </div>
        </div>
        <!--Artist Card 6-->
        <div class="profileCard fade-in-up delay-400">
          <div class="profileHeader">
            <img
              src="./image/artist-sitting-on-the-floor.jpg"
              alt="Artist Photo"
              class="profileImage"
            />
            <div class="ratingBadge">
              <div class="starsContainer">
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
              </div>
              <span class="ratingText">4.9</span>
            </div>
            <div class="academyBadge">Yadawity Partner</div>
          </div>

          <div class="profileContent">
            <h3 class="profileName">Lady Catherine<br />Pemberton</h3>
            <p class="profileSpecialty">Classical Portraiture</p>

            <div class="profileStats">
              <span class="masterpiecesCount">127 masterpieces</span>
            </div>

            <button class="viewPortfolioBtn">View Portfolio</button>
          </div>
        </div>
      </div>
      <button class="discoverArtistsBtn fade-in-up delay-500">
        Discover Artists
      </button>
    </div>

    <div class="sectionHeaderContainer">
      <div class="artisanDecorativeDivider fade-in-up">
        <div class="artisanOrnamentalIcon">
          <i class="fa-solid fa-brush"></i>
        </div>
      </div>
      <div class="sectionHeader">
        <h2 class="fade-in-up delay-100">featured Art</h2>
        <p class="fade-in-up delay-200">
          Explore curated works by master artists and renowned craftspeople.
        </p>
      </div>
      <!-- Artwork Section -->
      <section class="galleryContainer">
        <!-- Artwork Card -->
        <div class="artworkCard" data-category="painting" data-price="1200">
          <div class="artwork-image-container">
            <img src="image/photo.jpeg" alt="Abstract Art" class="artworkImage">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="artworkInfo">
            <h3 class="artworkTitle">Abstract Harmony</h3>
            <p class="artworkArtist">By Sarah Johnson</p>
            <p class="artworkPrice">$1,200</p>
            <p class="artworkDescription">A vibrant exploration of color and movement.</p>
            <div class="artworkActions">
              <button class="addToCart">Add to Cart</button>
              <button class="wishlistBtn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>

        <!-- Artwork Card -->
        <div class="artworkCard" data-category="sculpture" data-price="2500">
          <div class="artwork-image-container">
            <img src="image/photo-1554907984-15263bfd63bd.jpeg" alt="Bronze Sculpture" class="artworkImage">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="artworkInfo">
            <h3 class="artworkTitle">Bronze Elegance</h3>
            <p class="artworkArtist">By Michael Chen</p>
            <p class="artworkPrice">$2,500</p>
            <p class="artworkDescription">A masterful bronze sculpture that embodies grace and motion.</p>
            <div class="artworkActions">
              <button class="addToCart">Add to Cart</button>
              <button class="wishlistBtn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>

            <!-- Artwork Card -->
            <div class="artworkCard" data-category="sculpture" data-price="2500">
              <div class="artwork-image-container">
                <img src="image/photo-1554907984-15263bfd63bd.jpeg" alt="Bronze Sculpture" class="artworkImage">
                <div class="artwork-overlay">
                  <div class="quick-actions">
                    <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
                  </div>
                </div>
              </div>
              <div class="artworkInfo">
                <h3 class="artworkTitle">Bronze Elegance</h3>
                <p class="artworkArtist">By Michael Chen</p>
                <p class="artworkPrice">$2,500</p>
                <p class="artworkDescription">A masterful bronze sculpture that embodies grace and motion.</p>
                <div class="artworkActions">
                  <button class="addToCart">Add to Cart</button>
                  <button class="wishlistBtn"><i class="far fa-heart"></i></button>
                </div>
              </div>
            </div>

                <!-- Artwork Card -->
        <div class="artworkCard" data-category="sculpture" data-price="2500">
          <div class="artwork-image-container">
            <img src="image/photo-1554907984-15263bfd63bd.jpeg" alt="Bronze Sculpture" class="artworkImage">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="artworkInfo">
            <h3 class="artworkTitle">Bronze Elegance</h3>
            <p class="artworkArtist">By Michael Chen</p>
            <p class="artworkPrice">$2,500</p>
            <p class="artworkDescription">A masterful bronze sculpture that embodies grace and motion.</p>
            <div class="artworkActions">
              <button class="addToCart">Add to Cart</button>
              <button class="wishlistBtn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>

            <!-- Artwork Card -->
            <div class="artworkCard" data-category="sculpture" data-price="2500">
              <div class="artwork-image-container">
                <img src="image/photo-1554907984-15263bfd63bd.jpeg" alt="Bronze Sculpture" class="artworkImage">
                <div class="artwork-overlay">
                  <div class="quick-actions">
                    <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
                  </div>
                </div>
              </div>
              <div class="artworkInfo">
                <h3 class="artworkTitle">Bronze Elegance</h3>
                <p class="artworkArtist">By Michael Chen</p>
                <p class="artworkPrice">$2,500</p>
                <p class="artworkDescription">A masterful bronze sculpture that embodies grace and motion.</p>
                <div class="artworkActions">
                  <button class="addToCart">Add to Cart</button>
                  <button class="wishlistBtn"><i class="far fa-heart"></i></button>
                </div>
              </div>
            </div>

                <!-- Artwork Card -->
        <div class="artworkCard" data-category="sculpture" data-price="2500">
          <div class="artwork-image-container">
            <img src="image/photo-1554907984-15263bfd63bd.jpeg" alt="Bronze Sculpture" class="artworkImage">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="artworkInfo">
            <h3 class="artworkTitle">Bronze Elegance</h3>
            <p class="artworkArtist">By Michael Chen</p>
            <p class="artworkPrice">$2,500</p>
            <p class="artworkDescription">A masterful bronze sculpture that embodies grace and motion.</p>
            <div class="artworkActions">
              <button class="addToCart">Add to Cart</button>
              <button class="wishlistBtn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>

            <!-- Artwork Card -->
            <div class="artworkCard" data-category="sculpture" data-price="2500">
              <div class="artwork-image-container">
                <img src="image/photo-1554907984-15263bfd63bd.jpeg" alt="Bronze Sculpture" class="artworkImage">
                <div class="artwork-overlay">
                  <div class="quick-actions">
                    <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
                  </div>
                </div>
              </div>
              <div class="artworkInfo">
                <h3 class="artworkTitle">Bronze Elegance</h3>
                <p class="artworkArtist">By Michael Chen</p>
                <p class="artworkPrice">$2,500</p>
                <p class="artworkDescription">A masterful bronze sculpture that embodies grace and motion.</p>
                <div class="artworkActions">
                  <button class="addToCart">Add to Cart</button>
                  <button class="wishlistBtn"><i class="far fa-heart"></i></button>
                </div>
              </div>
            </div>

                <!-- Artwork Card -->
        <div class="artworkCard" data-category="sculpture" data-price="2500">
          <div class="artwork-image-container">
            <img src="image/photo-1554907984-15263bfd63bd.jpeg" alt="Bronze Sculpture" class="artworkImage">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="artworkInfo">
            <h3 class="artworkTitle">Bronze Elegance</h3>
            <p class="artworkArtist">By Michael Chen</p>
            <p class="artworkPrice">$2,500</p>
            <p class="artworkDescription">A masterful bronze sculpture that embodies grace and motion.</p>
            <div class="artworkActions">
              <button class="addToCart">Add to Cart</button>
              <button class="wishlistBtn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>

        <!-- Artwork Card -->
        <div class="artworkCard" data-category="photography" data-price="800">
          <div class="artwork-image-container">
            <img src="image/darker_image.webp" alt="Urban Photography" class="artworkImage">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="artworkInfo">
            <h3 class="artworkTitle">Urban Reflections</h3>
            <p class="artworkArtist">By Emma Davis</p>
            <p class="artworkPrice">$800</p>
            <p class="artworkDescription">A striking black and white photograph capturing city life.</p>
            <div class="artworkActions">
              <button class="addToCart">Add to Cart</button>
              <button class="wishlistBtn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>
      </section>
    </div>

    <div class="sectionHeaderContainer">
      <div class="artisanDecorativeDivider fade-in-up">
        <div class="artisanOrnamentalIcon">
          <i class="fa-solid fa-award"></i>
        </div>
      </div>
      <div class="sectionHeader">
        <h2 class="fade-in-up delay-100">Users testimonials</h2>
        <p class="fade-in-up delay-200">
          Testimonials from those touched by our artists' and makers'
          excellence.
        </p>
      </div>
      <div class="testimonialsCardSection zoom-in delay-300">
        <div class="testimonialsCarousel">
          <div class="testimonialsCarouselContainer">
            <div class="testimonialsCarouselTrack" id="carouselTrack">
              <!-- testimonials cards will be generated here -->
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php include './components/includes/footer.php'; ?>

    <script src="./components/Navbar/navbar.js"></script>
    <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./app.js"></script>
    
    <!-- Demo Script for Artist/Buyer Role Testing -->
    <script>
        // Add demo buttons to test role functionality (remove in production)
        document.addEventListener('DOMContentLoaded', function() {
            // Create demo panel
            const demoPanel = document.createElement('div');
            demoPanel.style.cssText = `
                position: fixed;
                bottom: 20px;
                left: 20px;
                background: var(--white);
                padding: 1rem;
                border-radius: 12px;
                box-shadow: 0 8px 25px var(--shadow-medium);
                z-index: 10000;
                display: flex;
                gap: 0.5rem;
                flex-direction: column;
                min-width: 200px;
            `;
            
            demoPanel.innerHTML = `
                <h4 style="margin: 0; color: var(--primary-brown); font-size: 0.9rem;">Demo Panel</h4>
                <button id="demo-buyer" style="padding: 0.5rem; border: none; background: var(--primary-brown); color: white; border-radius: 6px; cursor: pointer; font-size: 0.8rem;">Login as Buyer</button>
                <button id="demo-artist" style="padding: 0.5rem; border: none; background: var(--artist-accent); color: white; border-radius: 6px; cursor: pointer; font-size: 0.8rem;">Login as Artist</button>
                <button id="demo-logout" style="padding: 0.5rem; border: none; background: var(--red-accent); color: white; border-radius: 6px; cursor: pointer; font-size: 0.8rem;">Logout</button>
            `;
            
            document.body.appendChild(demoPanel);
            
            // Add event listeners
            document.getElementById('demo-buyer').addEventListener('click', () => {
                window.NavbarController.simulateLogin('buyer');
            });
            
            document.getElementById('demo-artist').addEventListener('click', () => {
                window.NavbarController.simulateLogin('artist');
            });
            
            document.getElementById('demo-logout').addEventListener('click', () => {
                window.NavbarController.logout();
            });
        });
    </script>
  </body>
</html>
