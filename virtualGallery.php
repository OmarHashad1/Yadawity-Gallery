<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yadawity - Virtual Galleries</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="./components/Navbar/navbar.css" />
    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css" />
    <link rel="stylesheet" href="./public/homePage.css" />
    <link rel="stylesheet" href="./public/virtual gallery.css">
    
</head>
<body>
    <?php include './components/includes/navbar.php'; ?>

    <?php include './components/includes/burger-menu.php'; ?>
    
    <!-- Home Page -->
 <div class="container">
        <!-- Header -->
        <header class="page-header">
            <div class="course-header-container">
                <h1 class="page-title">VIRTUAL GALLERIES</h1>
               </div>
        </header>
        <!-- Search Section -->
        <div class="search-section">
            <!-- Hero Section -->
            <div class="search-hero">
                <h2>Discover Digital Art Experiences</h2>
                </div>

            <!-- Main Search Bar -->
            <div class="main-search">
                <div class="search-wrapper">
                    <input 
                        type="text"
                        class="search-input"
                        id="searchInput"
                        placeholder="Search virtual galleries, artists, or collections..."
                    >
                    <button class="search-btn" onclick="applyFilters()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Enhanced Filters Container -->
           <div class="filters-container">
            <div class="filters-header">
                <h3>Filter Galleries</h3>
                <button class="clear-filters-btn" onclick="clearAllFilters()">
                    <i class="fas fa-times"></i> Clear All
                </button>
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-user-friends"></i>
                        Artist
                    </label>
                    <select class="filter-select" id="artistSelect">
                        <option value="all">All Artists</option>
                        <option value="mohamed">Mohamed</option>
                        <option value="ahmed">Ahmed</option>
                        <option value="essraa">Essraa</option>
                        <option value="noor">Noor</option>
                        <option value="samaa">Samaa</option>
                        <option value="mariem">Mariem</option>
                        <option value="soha">Soha</option>
                        <option value="essam">Essam</option>
                        <option value="mazen">Mazen</option>
                        <option value="noraa">Noraa</option>
                        <option value="nermen">Nermen</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-dollar-sign"></i>
                        Price Range
                    </label>
                    <select class="filter-select" id="priceSelect">
                        <option value="all">All Prices</option>
                        <option value="0-20">$0 - $20</option>
                        <option value="21-40">$21 - $40</option>
                        <option value="41-60">$41 - $60</option>
                        <option value="61-80">$61 - $80</option>
                        <option value="81-100">$81 - $100</option>
                        <option value="100+">$100+</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-clock"></i>
                        Duration
                    </label>
                    <select class="filter-select" id="durationSelect">
                        <option value="all">Any Duration</option>
                        <option value="30-45">30-45 minutes</option>
                        <option value="46-60">46-60 minutes</option>
                        <option value="61-90">61-90 minutes</option>
                        <option value="91-120">91-120 minutes</option>
                        <option value="120+">120+ minutes</option>
                    </select>
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

        <!-- Galleries Grid -->
        <div class="courses-grid" id="galleriesContainer">
            <!-- Gallery cards will be dynamically added here -->
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
            <h3>No galleries found</h3>
            <p>Try adjusting your search terms or filters</p>
            <button class="clear-search-btn" onclick="clearAllFilters()">Clear All Filters</button>
        </div>
    </div>
    

<?php include './components/includes/footer.php'; ?>

    <script src="./components/Navbar/navbar.js"></script>
    <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./public/virtual gallery.js"></script>
</body>
</html>