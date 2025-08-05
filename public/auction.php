<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Auction House - Yadawity Gallery</title>

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
    <link rel="stylesheet" href="./components/Navbar/navbar.css" />`n    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css" />
    <link rel="stylesheet" href="./public/homePage.css" />
    <link rel="stylesheet" href="./public/auction.css" />

  </head>
  <body>
    <?php include './components/includes/navbar.php'; ?>

    <?php include './components/includes/burger-menu.php'; ?>
        
      </div>
    </div>
    <div class="container">
      <!-- Page Header -->
    <header class="page-header">
        <div class="course-header-container">
            <h1 class="page-title">AUCTION HOUSE</h1>
        </div>
    </header>

    <!-- Search Section -->
    <div class="search-section">
        <!-- Hero Section -->
        <div class="search-hero">
            <h2>Discover Unique Artworks</h2>
            <p class="search-subtitle">Browse through our curated collection of art auctions</p>
        </div>

        <!-- Main Search Bar -->
        <div class="main-search">
            <div class="search-wrapper">
                <input 
                    type="text"
                    class="search-input"
                    id="searchInput"
                    placeholder="Search auctions by artwork, artist, or medium..."
                    autocomplete="off"
                >
                <button class="search-btn" onclick="applyFilters()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Enhanced Filters Container -->
        <div class="filters-container">
            <div class="filters-header">
                <h3>Filter Auctions</h3>
                <button class="clear-filters-btn" onclick="clearAllFilters()">
                    <i class="fas fa-times"></i> Clear All
                </button>
            </div>

            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-palette"></i>
                        Category
                    </label>
                    <select class="filter-select" id="categoryFilter">
                        <option value="">All Categories</option>
                        <option value="Paintings">Paintings</option>
                        <option value="Sculptures">Sculptures</option>
                        <option value="Photography">Photography</option>
                        <option value="Digital Art">Digital Art</option>
                        <option value="Mixed Media">Mixed Media</option>
                        <option value="Drawings">Drawings</option>
                        <option value="Prints">Prints</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-clock"></i>
                        Status
                    </label>
                    <select class="filter-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="live">Live Now</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="ended">Ended</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-tag"></i>
                        Price Range
                    </label>
                    <div class="price-range">
                        <div class="price-input-wrapper">
                            <span class="currency-symbol">EGP</span>
                            <input
                                type="number"
                                class="filter-input price-input"
                                id="minPrice"
                                placeholder="Min"
                                min="0"
                            >
                        </div>
                        <span class="price-separator">-</span>
                        <div class="price-input-wrapper">
                            <span class="currency-symbol">EGP</span>
                            <input
                                type="number"
                                class="filter-input price-input"
                                id="maxPrice"
                                placeholder="Max"
                                min="0"
                            >
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

        <!-- Active Filters Display -->
        <div class="active-filters" id="activeFilters"></div>

        <!-- Search Results -->
        <div class="search-results" id="searchResults"></div>
    </div>
      </div>

    

    <!-- Auction Grid -->
    <div class="auctionGrid">
      <!-- Live Auction Item 1 -->
      <div class="auctionCard live" data-category="paintings" data-price="75000">
        <div class="auctionImageContainer">
          <img 
            src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=400&h=300&fit=crop" 
            alt="Abstract Harmony"
            class="auctionImage"
          />
          <div class="auctionStatus live">
            <i class="fas fa-circle"></i>
            <span>LIVE</span>
          </div>
          <div class="auctionTimer" data-end-time="2025-01-25T18:30:00">
            <i class="fas fa-clock"></i>
            <span class="timeRemaining">2h 45m</span>
          </div>
        </div>
        
        <div class="auctionInfo">
          <h3 class="auctionTitle">Abstract Harmony</h3>
          <p class="auctionArtist">by Marina Kovač</p>
          <p class="auctionDescription">
            Oil on canvas, 80x100cm. A stunning piece exploring the balance between chaos and order...
          </p>
          
          <div class="auctionPricing">
            <div class="currentBid">
              <span class="bidLabel">Current Bid</span>
              <span class="bidAmount">EGP 75,000</span>
            </div>
            <div class="bidsCount">
              <i class="fas fa-users"></i>
              <span>12 bidders</span>
            </div>
          </div>
          
          <div class="auctionActions">
            <button class="bidNowBtn" onclick="openAuctionPreview('auction-1')">
              <i class="fas fa-gavel"></i>
              Bid Now
            </button>
            <button class="watchBtn" title="Add to Watchlist">
              <i class="far fa-heart"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Upcoming Auction Item 2 -->
      <div class="auctionCard upcoming" data-category="sculptures" data-price="120000">
        <div class="auctionImageContainer">
          <img 
            src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop" 
            alt="Bronze Elegance"
            class="auctionImage"
          />
          <div class="auctionStatus upcoming">
            <i class="fas fa-calendar"></i>
            <span>UPCOMING</span>
          </div>
          <div class="auctionTimer" data-start-time="2025-01-26T15:00:00">
            <i class="fas fa-calendar-alt"></i>
            <span class="timeRemaining">Starts in 1d 8h</span>
          </div>
        </div>
        
        <div class="auctionInfo">
          <h3 class="auctionTitle">Bronze Elegance</h3>
          <p class="auctionArtist">by Ahmed Hassan</p>
          <p class="auctionDescription">
            Limited edition bronze sculpture, 45cm height. Masterful craftsmanship showcasing...
          </p>
          
          <div class="auctionPricing">
            <div class="currentBid">
              <span class="bidLabel">Starting Bid</span>
              <span class="bidAmount">EGP 120,000</span>
            </div>
            <div class="bidsCount">
              <i class="fas fa-eye"></i>
              <span>28 watching</span>
            </div>
          </div>
          
          <div class="auctionActions">
            <button class="preRegisterBtn" onclick="openAuctionPreview('auction-2')">
              <i class="fas fa-bell"></i>
              Pre-Register
            </button>
            <button class="watchBtn" title="Add to Watchlist">
              <i class="far fa-heart"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Live Auction Item 3 -->
      <div class="auctionCard live" data-category="photography" data-price="35000">
        <div class="auctionImageContainer">
          <img 
            src="https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=400&h=300&fit=crop" 
            alt="Urban Reflections"
            class="auctionImage"
          />
          <div class="auctionStatus live">
            <i class="fas fa-circle"></i>
            <span>LIVE</span>
          </div>
          <div class="auctionTimer" data-end-time="2025-01-25T20:15:00">
            <i class="fas fa-clock"></i>
            <span class="timeRemaining">4h 30m</span>
          </div>
        </div>
        
        <div class="auctionInfo">
          <h3 class="auctionTitle">Urban Reflections</h3>
          <p class="auctionArtist">by Sarah Chen</p>
          <p class="auctionDescription">
            Limited edition fine art photography print, 70x50cm. Captures the soul of modern...
          </p>
          
          <div class="auctionPricing">
            <div class="currentBid">
              <span class="bidLabel">Current Bid</span>
              <span class="bidAmount">EGP 35,000</span>
            </div>
            <div class="bidsCount">
              <i class="fas fa-users"></i>
              <span>8 bidders</span>
            </div>
          </div>
          
          <div class="auctionActions">
            <button class="bidNowBtn" onclick="openAuctionPreview('auction-3')">
              <i class="fas fa-gavel"></i>
              Bid Now
            </button>
            <button class="watchBtn" title="Add to Watchlist">
              <i class="far fa-heart"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Ended Auction Item 4 -->
      <div class="auctionCard ended" data-category="paintings" data-price="180000">
        <div class="auctionImageContainer">
          <img 
            src="https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=400&h=300&fit=crop" 
            alt="Classical Portrait"
            class="auctionImage"
          />
          <div class="auctionStatus ended">
            <i class="fas fa-check"></i>
            <span>SOLD</span>
          </div>
          <div class="soldOverlay">
            <span>SOLD</span>
          </div>
        </div>
        
        <div class="auctionInfo">
          <h3 class="auctionTitle">Classical Portrait</h3>
          <p class="auctionArtist">by Elena Popović</p>
          <p class="auctionDescription">
            Oil on canvas masterpiece, 90x70cm. Exquisite portraiture technique from renowned...
          </p>
          
          <div class="auctionPricing">
            <div class="currentBid">
              <span class="bidLabel">Final Price</span>
              <span class="bidAmount">EGP 180,000</span>
            </div>
            <div class="bidsCount">
              <i class="fas fa-trophy"></i>
              <span>Won by user_1847</span>
            </div>
          </div>
          
          <div class="auctionActions">
            <button class="viewDetailsBtn" onclick="openAuctionPreview('auction-4')">
              <i class="fas fa-eye"></i>
              View Details
            </button>
            <button class="watchBtn disabled" title="Auction Ended">
              <i class="fas fa-check"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- More auction items... -->
      <div class="auctionCard upcoming" data-category="mixed-media" data-price="95000">
        <div class="auctionImageContainer">
          <img 
            src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop" 
            alt="Digital Dreams"
            class="auctionImage"
          />
          <div class="auctionStatus upcoming">
            <i class="fas fa-calendar"></i>
            <span>UPCOMING</span>
          </div>
          <div class="auctionTimer" data-start-time="2025-01-27T19:00:00">
            <i class="fas fa-calendar-alt"></i>
            <span class="timeRemaining">Starts in 2d 3h</span>
          </div>
        </div>
        
        <div class="auctionInfo">
          <h3 class="auctionTitle">Digital Dreams</h3>
          <p class="auctionArtist">by Marcus Rodriguez</p>
          <p class="auctionDescription">
            Mixed media on canvas with digital elements, 100x80cm. A groundbreaking fusion of...
          </p>
          
          <div class="auctionPricing">
            <div class="currentBid">
              <span class="bidLabel">Starting Bid</span>
              <span class="bidAmount">EGP 95,000</span>
            </div>
            <div class="bidsCount">
              <i class="fas fa-eye"></i>
              <span>15 watching</span>
            </div>
          </div>
          
          <div class="auctionActions">
            <button class="preRegisterBtn" onclick="openAuctionPreview('auction-5')">
              <i class="fas fa-bell"></i>
              Pre-Register
            </button>
            <button class="watchBtn" title="Add to Watchlist">
              <i class="far fa-heart"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="auctionCard live" data-category="paintings" data-price="45000">
        <div class="auctionImageContainer">
          <img 
            src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=400&h=300&fit=crop" 
            alt="Sunset Serenity"
            class="auctionImage"
          />
          <div class="auctionStatus live">
            <i class="fas fa-circle"></i>
            <span>LIVE</span>
          </div>
          <div class="auctionTimer" data-end-time="2025-01-25T22:00:00">
            <i class="fas fa-clock"></i>
            <span class="timeRemaining">6h 15m</span>
          </div>
        </div>
        
        <div class="auctionInfo">
          <h3 class="auctionTitle">Sunset Serenity</h3>
          <p class="auctionArtist">by Omar Farouk</p>
          <p class="auctionDescription">
            Acrylic on canvas landscape, 60x80cm. Breathtaking color palette capturing the magic...
          </p>
          
          <div class="auctionPricing">
            <div class="currentBid">
              <span class="bidLabel">Current Bid</span>
              <span class="bidAmount">EGP 45,000</span>
            </div>
            <div class="bidsCount">
              <i class="fas fa-users"></i>
              <span>6 bidders</span>
            </div>
          </div>
          
          <div class="auctionActions">
            <button class="bidNowBtn" onclick="openAuctionPreview('auction-6')">
              <i class="fas fa-gavel"></i>
              Bid Now
            </button>
            <button class="watchBtn" title="Add to Watchlist">
              <i class="far fa-heart"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Third Row - Additional Auction Items -->
      <div class="auctionCard upcoming" data-category="sculptures" data-price="85000">
        <div class="auctionImageContainer">
          <img 
            src="https://images.unsplash.com/photo-1594736797933-d0ac6a4d5d0e?w=400&h=300&fit=crop" 
            alt="Modern Forms"
            class="auctionImage"
          />
          <div class="auctionStatus upcoming">
            <i class="fas fa-calendar"></i>
            <span>UPCOMING</span>
          </div>
          <div class="auctionTimer" data-start-time="2025-01-28T16:00:00">
            <i class="fas fa-calendar-alt"></i>
            <span class="timeRemaining">Starts in 3d 12h</span>
          </div>
        </div>
        
        <div class="auctionInfo">
          <h3 class="auctionTitle">Modern Forms</h3>
          <p class="auctionArtist">by Layla Mahmoud</p>
          <p class="auctionDescription">
            Contemporary ceramic sculpture, 60cm height. Bold geometric forms that challenge traditional boundaries...
          </p>
          
          <div class="auctionPricing">
            <div class="currentBid">
              <span class="bidLabel">Starting Bid</span>
              <span class="bidAmount">EGP 85,000</span>
            </div>
            <div class="bidsCount">
              <i class="fas fa-eye"></i>
              <span>22 watching</span>
            </div>
          </div>
          
          <div class="auctionActions">
            <button class="preRegisterBtn" onclick="openAuctionPreview('auction-7')">
              <i class="fas fa-bell"></i>
              Pre-Register
            </button>
            <button class="watchBtn" title="Add to Watchlist">
              <i class="far fa-heart"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="auctionCard live" data-category="mixed-media" data-price="62000">
        <div class="auctionImageContainer">
          <img 
            src="https://images.unsplash.com/photo-1549490349-8643362247b5?w=400&h=300&fit=crop" 
            alt="Collage Dreams"
            class="auctionImage"
          />
          <div class="auctionStatus live">
            <i class="fas fa-circle"></i>
            <span>LIVE</span>
          </div>
          <div class="auctionTimer" data-end-time="2025-01-26T14:20:00">
            <i class="fas fa-clock"></i>
            <span class="timeRemaining">1d 2h</span>
          </div>
        </div>
        
        <div class="auctionInfo">
          <h3 class="auctionTitle">Collage Dreams</h3>
          <p class="auctionArtist">by Nadia Rostom</p>
          <p class="auctionDescription">
            Mixed media collage, 75x95cm. Innovative layering techniques creating depth and narrative...
          </p>
          
          <div class="auctionPricing">
            <div class="currentBid">
              <span class="bidLabel">Current Bid</span>
              <span class="bidAmount">EGP 62,000</span>
            </div>
            <div class="bidsCount">
              <i class="fas fa-users"></i>
              <span>9 bidders</span>
            </div>
          </div>
          
          <div class="auctionActions">
            <button class="bidNowBtn" onclick="openAuctionPreview('auction-8')">
              <i class="fas fa-gavel"></i>
              Bid Now
            </button>
            <button class="watchBtn" title="Add to Watchlist">
              <i class="far fa-heart"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="auctionCard ended" data-category="photography" data-price="28000">
        <div class="auctionImageContainer">
          <img 
            src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop" 
            alt="City Lights"
            class="auctionImage"
          />
          <div class="auctionStatus ended">
            <i class="fas fa-check"></i>
            <span>SOLD</span>
          </div>
          <div class="soldOverlay">
            <span>SOLD</span>
          </div>
        </div>
        
        <div class="auctionInfo">
          <h3 class="auctionTitle">City Lights</h3>
          <p class="auctionArtist">by Karim El-Sharif</p>
          <p class="auctionDescription">
            Night photography series, 50x70cm print. Stunning urban landscapes captured during golden hour...
          </p>
          
          <div class="auctionPricing">
            <div class="currentBid">
              <span class="bidLabel">Final Price</span>
              <span class="bidAmount">EGP 28,000</span>
            </div>
            <div class="bidsCount">
              <i class="fas fa-trophy"></i>
              <span>Won by art_collector_92</span>
            </div>
          </div>
          
          <div class="auctionActions">
            <button class="viewDetailsBtn" onclick="openAuctionPreview('auction-9')">
              <i class="fas fa-eye"></i>
              View Details
            </button>
            <button class="watchBtn disabled" title="Auction Ended">
              <i class="fas fa-check"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

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

    <!-- No Results -->
    <div class="no-results" id="noResults" style="display: none;">
        <div class="no-results-icon">🎨</div>
        <h3>No auctions found</h3>
        <p>Try adjusting your search terms or filters</p>
        <button class="clear-search-btn" onclick="clearAllFilters()">Clear All Filters</button>
    </div>

    <?php include './components/includes/footer.php'; ?>

    <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./components/Navbar/navbar.js"></script>
    <script src="./public/auction.js"></script>
  </body>
</html>
