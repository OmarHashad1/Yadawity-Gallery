<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Artwork Collection | Yadawity Gallery</title>

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
    <link rel="stylesheet" href="./components/Navbar/navbar.css" />
    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css" />
    <link rel="stylesheet" href="./public/homePage.css" />
    <link rel="stylesheet" href="./public/artwork.css">
  </head>

  <body>
    <!-- Navigation -->
    <?php include './components/includes/navbar.php'; ?>

    <?php include './components/includes/burger-menu.php'; ?>
    
    <div class="container">
      <header class="page-header">
            <div class="course-header-container">
                <h1 class="page-title">ARTWORKS</h1>
              </div>
        </header>
    <!-- Search Section -->
<!-- Search Section -->
        <div class="search-section">
            <!-- Hero Section -->
            <div class="search-hero">
                <h2>Discover Unique Artworks</h2>
                <p class="search-subtitle">Browse through our curated collection of artworks</p>
            </div>

      <!-- Main Search Bar -->
      <div class="main-search">
        <div class="search-wrapper">
          <input 
            type="text"
            class="search-input"
            id="searchInput"
            placeholder="Search artworks by name, artist, or style..."
            autocomplete="off"
          >
          <button class="search-btn" onclick="applyFilters()">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="filters-container">
        <div class="filters-header">
            <h3>Filter Artworks</h3>
            <button class="clear-filters-btn" onclick="clearAllFilters()">
                <i class="fas fa-times"></i> Clear All
            </button>
        </div>

        <div class="filters-grid">
            <div class="filter-group">
                <label class="filter-label">
                    <i class="fas fa-th-large"></i>
                    Category
                </label>
                <select class="filter-select" id="categoryFilter">
                    <option value="all">All Categories</option>
                    <option value="portraits">Portraits</option>
                    <option value="landscapes">Landscapes</option>
                    <option value="abstract">Abstract</option>
                    <option value="photography">Photography</option>
                    <option value="mixed-media">Mixed Media</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">
                    <i class="fas fa-sort"></i>
                    Sort By
                </label>
                <select class="filter-select" id="sortBy">
                    <option value="featured">Featured</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="newest">Newest</option>
                    <option value="artist">Artist Name</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">
                    <i class="fas fa-dollar-sign"></i>
                    Price Range
                </label>
                <div class="price-range-container">
                    <div class="price-input-wrapper">
                        <span class="currency-symbol">$</span>
                        <input type="number" id="minPrice" class="filter-input price-input" placeholder="Min" min="0">
                    </div>
                    <span class="price-separator">-</span>
                    <div class="price-input-wrapper">
                        <span class="currency-symbol">$</span>
                        <input type="number" id="maxPrice" class="filter-input price-input" placeholder="Max" min="0">
                    </div>
                </div>
            </div>
        </div>

        <div class="filters-actions">
            <button class="apply-filters-btn" onclick="applyFilters()">
                <i class="fas fa-filter"></i>
                Apply Filters
            </button>
        </div>
    </div>

    </div>


     
    
    <!-- Artwork Gallery Section -->
    <section class="artwork-gallery">
      

      <div class="artworks-grid" id="artworksGrid">
        <!-- Artwork Card 1 -->
        <div class="enhanced-artwork-card" data-category="painting" data-price="1200">
          <div class="artwork-image-container">
            <img src="image/photo.jpeg" alt="Abstract Painting" class="enhanced-artwork-image">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="enhanced-artwork-info">
            <div class="artwork-category">Painting</div>
            <h3 class="enhanced-artwork-title">Abstract Harmony</h3>
            <p class="enhanced-artwork-artist">By Sarah Johnson</p>
            <p class="enhanced-artwork-price">$1,200</p>
            <p class="artwork-dimensions">36" × 48" × 1.5"</p>
            <p class="enhanced-artwork-description">A vibrant exploration of color and movement, this piece captures the essence of modern abstract expressionism.</p>
            <div class="artwork-actions">
              <button class="enhanced-add-to-cart">Add to Cart</button>
              <button class="wishlist-btn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>

        <!-- Artwork Card 2 -->
        <div class="enhanced-artwork-card" data-category="sculpture" data-price="2500">
          <div class="artwork-image-container">
            <img src="image/photo-1554907984-15263bfd63bd.jpeg" alt="Bronze Sculpture" class="enhanced-artwork-image">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="enhanced-artwork-info">
            <div class="artwork-category">Sculpture</div>
            <h3 class="enhanced-artwork-title">Bronze Elegance</h3>
            <p class="enhanced-artwork-artist">By Michael Chen</p>
            <p class="enhanced-artwork-price">$2,500</p>
            <p class="artwork-dimensions">24" × 12" × 12"</p>
            <p class="enhanced-artwork-description">A masterful bronze sculpture that embodies grace and motion in three dimensions.</p>
            <div class="artwork-actions">
              <button class="enhanced-add-to-cart">Add to Cart</button>
              <button class="wishlist-btn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>

        <!-- Artwork Card 3 -->
        <div class="enhanced-artwork-card" data-category="photography" data-price="800">
          <div class="artwork-image-container">
            <img src="image/darker_image.webp" alt="Urban Photography" class="enhanced-artwork-image">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="enhanced-artwork-info">
            <div class="artwork-category">Photography</div>
            <h3 class="enhanced-artwork-title">Urban Reflections</h3>
            <p class="enhanced-artwork-artist">By Emma Davis</p>
            <p class="enhanced-artwork-price">$800</p>
            <p class="artwork-dimensions">24" × 36"</p>
            <p class="enhanced-artwork-description">A striking black and white photograph capturing the essence of city life.</p>
            <div class="artwork-actions">
              <button class="enhanced-add-to-cart">Add to Cart</button>
              <button class="wishlist-btn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>

        <!-- Artwork Card 4 -->
        <div class="enhanced-artwork-card" data-category="painting" data-price="1800">
          <div class="artwork-image-container">
            <img src="image/STC_EDS_MINAG_R_L_2011_229-001.jpg" alt="Oil Painting" class="enhanced-artwork-image">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="enhanced-artwork-info">
            <div class="artwork-category">Painting</div>
            <h3 class="enhanced-artwork-title">Serenity Falls</h3>
            <p class="enhanced-artwork-artist">By Robert Wilson</p>
            <p class="enhanced-artwork-price">$1,800</p>
            <p class="artwork-dimensions">40" × 30" × 1.5"</p>
            <p class="enhanced-artwork-description">A serene landscape capturing the majesty of nature in oil on canvas.</p>
            <div class="artwork-actions">
              <button class="enhanced-add-to-cart">Add to Cart</button>
              <button class="wishlist-btn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>

        <!-- Artwork Card 5 -->
        <div class="enhanced-artwork-card" data-category="digital" data-price="600">
          <div class="artwork-image-container">
            <img src="image/images.jpeg" alt="Digital Art" class="enhanced-artwork-image">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="enhanced-artwork-info">
            <div class="artwork-category">Digital Art</div>
            <h3 class="enhanced-artwork-title">Digital Dreams</h3>
            <p class="enhanced-artwork-artist">By Alex Thompson</p>
            <p class="enhanced-artwork-price">$600</p>
            <p class="artwork-dimensions">24" × 24"</p>
            <p class="enhanced-artwork-description">A mesmerizing digital creation blending technology and artistic vision.</p>
            <div class="artwork-actions">
              <button class="enhanced-add-to-cart">Add to Cart</button>
              <button class="wishlist-btn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>

        <!-- Artwork Card 6 -->
        <div class="enhanced-artwork-card" data-category="mixed-media" data-price="1500">
          <div class="artwork-image-container">
            <img src="image/AllentownArtMuseum_Gallery01_DiscoverLehighValley_2450c76f-4de5-402c-a060-d0a8ff3b1d37.jpg" alt="Mixed Media" class="enhanced-artwork-image">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="enhanced-artwork-info">
            <div class="artwork-category">Mixed Media</div>
            <h3 class="enhanced-artwork-title">Textural Symphony</h3>
            <p class="enhanced-artwork-artist">By Maria Garcia</p>
            <p class="enhanced-artwork-price">$1,500</p>
            <p class="artwork-dimensions">36" × 48"</p>
            <p class="enhanced-artwork-description">An innovative mixed media piece combining various materials and techniques.</p>
            <div class="artwork-actions">
              <button class="enhanced-add-to-cart">Add to Cart</button>
              <button class="wishlist-btn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>

        <!-- Artwork Card 7 -->
        <div class="enhanced-artwork-card" data-category="painting" data-price="2200">
          <div class="artwork-image-container">
            <img src="image/artist-sitting-on-the-floor.jpg" alt="Contemporary Painting" class="enhanced-artwork-image">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="enhanced-artwork-info">
            <div class="artwork-category">Painting</div>
            <h3 class="enhanced-artwork-title">Modern Expression</h3>
            <p class="enhanced-artwork-artist">By David Martinez</p>
            <p class="enhanced-artwork-price">$2,200</p>
            <p class="artwork-dimensions">48" × 36" × 2"</p>
            <p class="enhanced-artwork-description">A bold contemporary painting that challenges traditional artistic boundaries with vibrant colors and dynamic composition.</p>
            <div class="artwork-actions">
              <button class="enhanced-add-to-cart">Add to Cart</button>
              <button class="wishlist-btn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>

        <!-- Artwork Card 8 -->
        <div class="enhanced-artwork-card" data-category="photography" data-price="950">
          <div class="artwork-image-container">
            <img src="image/Artist-PainterLookingAtCamera.webp" alt="Portrait Photography" class="enhanced-artwork-image">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="enhanced-artwork-info">
            <div class="artwork-category">Photography</div>
            <h3 class="enhanced-artwork-title">Artist's Gaze</h3>
            <p class="enhanced-artwork-artist">By Sophie Anderson</p>
            <p class="enhanced-artwork-price">$950</p>
            <p class="artwork-dimensions">20" × 30"</p>
            <p class="enhanced-artwork-description">An intimate portrait capturing the creative spirit and determination of an artist in their element.</p>
            <div class="artwork-actions">
              <button class="enhanced-add-to-cart">Add to Cart</button>
              <button class="wishlist-btn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>

        <!-- Artwork Card 9 -->
        <div class="enhanced-artwork-card" data-category="abstract" data-price="1650">
          <div class="artwork-image-container">
            <img src="image/_grj4724.jpg" alt="Abstract Art" class="enhanced-artwork-image">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="enhanced-artwork-info">
            <div class="artwork-category">Abstract</div>
            <h3 class="enhanced-artwork-title">Cosmic Fragments</h3>
            <p class="enhanced-artwork-artist">By Jennifer Lee</p>
            <p class="enhanced-artwork-price">$1,650</p>
            <p class="artwork-dimensions">42" × 54" × 1.5"</p>
            <p class="enhanced-artwork-description">An explosive abstract composition that explores the relationship between chaos and harmony through bold gestural marks.</p>
            <div class="artwork-actions">
              <button class="enhanced-add-to-cart">Add to Cart</button>
              <button class="wishlist-btn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>

        <!-- Artwork Card 10 -->
        <div class="enhanced-artwork-card" data-category="sculpture" data-price="3200">
          <div class="artwork-image-container">
            <img src="image/2d58ceedffd1ba6b3e8e2adc4371208f.jpg" alt="Modern Sculpture" class="enhanced-artwork-image">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="enhanced-artwork-info">
            <div class="artwork-category">Sculpture</div>
            <h3 class="enhanced-artwork-title">Geometric Harmony</h3>
            <p class="enhanced-artwork-artist">By Marcus Johnson</p>
            <p class="enhanced-artwork-price">$3,200</p>
            <p class="artwork-dimensions">30" × 18" × 18"</p>
            <p class="enhanced-artwork-description">A contemporary sculpture exploring geometric forms and negative space with precision-crafted metal components.</p>
            <div class="artwork-actions">
              <button class="enhanced-add-to-cart">Add to Cart</button>
              <button class="wishlist-btn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>

        <!-- Artwork Card 11 -->
        <div class="enhanced-artwork-card" data-category="digital" data-price="750">
          <div class="artwork-image-container">
            <img src="image/photoo.webp" alt="Digital Landscape" class="enhanced-artwork-image">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="enhanced-artwork-info">
            <div class="artwork-category">Digital Art</div>
            <h3 class="enhanced-artwork-title">Digital Horizon</h3>
            <p class="enhanced-artwork-artist">By Kevin Park</p>
            <p class="enhanced-artwork-price">$750</p>
            <p class="artwork-dimensions">32" × 24"</p>
            <p class="enhanced-artwork-description">A stunning digital landscape that blurs the line between reality and imagination with ethereal lighting effects.</p>
            <div class="artwork-actions">
              <button class="enhanced-add-to-cart">Add to Cart</button>
              <button class="wishlist-btn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>

        <!-- Artwork Card 12 -->
        <div class="enhanced-artwork-card" data-category="painting" data-price="1950">
          <div class="artwork-image-container">
            <img src="image/slide1.jpg" alt="Impressionist Painting" class="enhanced-artwork-image">
            <div class="artwork-overlay">
              <div class="quick-actions">
                <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
          <div class="enhanced-artwork-info">
            <div class="artwork-category">Painting</div>
            <h3 class="enhanced-artwork-title">Garden Reverie</h3>
            <p class="enhanced-artwork-artist">By Isabella Rodriguez</p>
            <p class="enhanced-artwork-price">$1,950</p>
            <p class="artwork-dimensions">40" × 32" × 1.5"</p>
            <p class="enhanced-artwork-description">An impressionist-inspired garden scene that captures the fleeting beauty of light dancing through foliage.</p>
            <div class="artwork-actions">
              <button class="enhanced-add-to-cart">Add to Cart</button>
              <button class="wishlist-btn"><i class="far fa-heart"></i></button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pagination Section -->
    <section class="pagination-section">
      <div class="pagination-container">
        <div class="pagination-controls">
          <button class="pagination-btn prev-btn" id="prevBtn" onclick="previousPage()" disabled>
            <i class="fas fa-chevron-left"></i>
            <span>Previous</span>
          </button>
          
          <div class="pagination-numbers" id="paginationNumbers">
            <button class="pagination-number active" onclick="goToPage(1)">1</button>
            <button class="pagination-number" onclick="goToPage(2)">2</button>
            <button class="pagination-number" onclick="goToPage(3)">3</button>
            <span class="pagination-dots">...</span>
            <button class="pagination-number" onclick="goToPage(4)">4</button>
          </div>
          
          <button class="pagination-btn next-btn" id="nextBtn" onclick="nextPage()">
            <span>Next</span>
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </section>

    <!-- Footer -->
   
    <?php include './components/includes/footer.php'; ?>
    <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./app.js"></script>
    <script src="./public/artwork.js"></script>
  </body>
</html>