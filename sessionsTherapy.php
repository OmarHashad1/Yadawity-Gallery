<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yadawity - Sessions</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="./components/Navbar/navbar.css" />`n    <link
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
<link rel="stylesheet" href="./public/sessionsTherapy.css" />
</head>
<body>
    
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


      <div class="container">
        <!-- Header -->
        <header class="page-header">
            <div class="course-header-container">
                <h1 class="page-title">SESSIONS</h1>
              </div>
        </header>

        <!-- Search Section -->
        <div class="search-section">
            <!-- Hero Section -->
            <div class="search-hero">
                <h2>Discover Your Journey</h2>
                <p class="search-subtitle">Browse through our curated collection of art courses</p>
            </div>

            <!-- Main Search Bar -->
            <div class="main-search">
                <div class="search-wrapper">
                    <input 
                        type="text"
                        class="search-input"
                        id="searchInput"
                        placeholder="Search courses by name, instructor, or keywords..."
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
                    <h3>Filter Sessions</h3>
                    <button class="clear-filters-btn" onclick="clearAllFilters()">
                        <i class="fas fa-times"></i> Clear All
                    </button>
                </div>

                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-user-md"></i>
                            Doctor
                        </label>
                        <select class="filter-select" id="doctorFilter">
                            <option value="">All Doctors</option>
                            <option value="Dr. Ahmed Hassan">Dr. Ahmed Hassan</option>
                            <option value="Dr. Sara Youssef">Dr. Sara Youssef</option>
                            <option value="Dr. Mona Khaled">Dr. Mona Khaled</option>
                            <option value="Dr. Tarek Nabil">Dr. Tarek Nabil</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-palette"></i>
                            Category
                        </label>
                        <select class="filter-select" id="categoryFilter">
                            <option value="">All Categories</option>
                            <option value="Cognitive Art Therapy">Cognitive Art Therapy</option>
                            <option value="Dialectical Behavior Therapy (DBT) with Art">Dialectical Behavior Therapy (DBT) with Art</option>
                            <option value="Trauma-Informed Art Therapy">Trauma-Informed Art Therapy</option>
                            <option value="Behavioral Art Therapy">Behavioral Art Therapy</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-map-marker-alt"></i>
                            City
                        </label>
                        <select class="filter-select" id="cityFilter">
                            <option value="">All Cities</option>
                            <option value="Cairo">Cairo</option>
                            <option value="Alexandria">Alexandria</option>
                            <option value="Giza">Giza</option>
                            <option value="Mansoura">Mansoura</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-road"></i>
                            Street
                        </label>
                        <select class="filter-select" id="streetFilter">
                            <option value="">All Streets</option>
                            <option value="Tahrir Street">Tahrir Street</option>
                            <option value="El Merghany Street">El Merghany Street</option>
                            <option value="El Haram Street">El Haram Street</option>
                            <option value="Port Said Street">Port Said Street</option>
                            <option value="El Nasr Road">El Nasr Road</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-tag"></i>
                            Price Range
                        </label>
                        <div class="price-range-container">
                            <div class="price-input-wrapper">
                                <span class="currency-symbol">$</span>
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
                                <span class="currency-symbol">$</span>
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

                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-calendar-alt"></i>
                            Date
                        </label>
                        <select class="filter-select" id="dateFilter">
                            <option value="">All Dates</option>
                            <option value="2025-07-30">Today (July 30, 2025)</option>
                            <option value="2025-07-31">Tomorrow (July 31, 2025)</option>
                            <option value="2025-08-01">Friday (August 1, 2025)</option>
                            <option value="2025-08-02">Saturday (August 2, 2025)</option>
                            <option value="2025-08-03">Sunday (August 3, 2025)</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-clock"></i>
                            Time
                        </label>
                        <input type="time" class="filter-input" id="timeFilter">
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


        <!-- Courses Grid -->
        <div class="courses-grid" id="coursesGrid">
            <!-- Course cards will be dynamically added here -->
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

        <!-- Course Count -->
        <div class="course-count" id="courseCount">
            Showing all courses
        </div>

        <!-- No Results -->
        <div class="no-results" id="noResults" style="display: none;">
            <div class="no-results-icon">🎨</div>
            <h3>No courses found</h3>
            <p>Try adjusting your search terms or filters</p>
            <button class="clear-search-btn" onclick="clearAllFilters()">Clear All Filters</button>
        </div>
    </div>

    <?php include './components/includes/footer.php'; ?>
     <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./public/sesstionsTherapy.js"></script>
    </script>
</body>
</html>