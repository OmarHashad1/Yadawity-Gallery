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
    <div class="auctionGrid" id="auctionGrid">
        <!-- Dynamic auction cards will be loaded here via JavaScript -->
        <div class="loading-container" id="loadingContainer">
            <div class="loading-spinner"></div>
            <p>Loading auctions...</p>
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
