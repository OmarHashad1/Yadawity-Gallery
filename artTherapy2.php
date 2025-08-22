<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Hub - Book Your Perfect Gallery Experience</title>
    
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
    <link rel="stylesheet" href="./public/artTherapy2.css">
    <link rel="stylesheet" href="./public/homePage.css" />
    <link rel="stylesheet" href="./components/Navbar/navbar.css" />
    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css" />
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
    <!-- Home Page -->

<!-- Hero Section -->
    <section class="supportHero">
        <div class="heroBackground">
            <img src="/image/darker_image_25_percent.jpeg" alt="Art Gallery" class="heroBackgroundImg">
            <div class="heroOverlay"></div>
        </div>
        <div class="heroContent">
            <div class="heroText">
              <div class="sectionHeaderContainer">
                <div class="artisanDecorativeDivider fade-in-up">
                  <div class="artisanOrnamentalIcon">
                    <i class="fa-solid fa-palette"></i>
                  </div>
                </div>
          
                <div class="sectionHeader">
                  <h1 class="fade-in-up delay-100">Explor one of them</h1>
                </div>              
                 <div class="galleryOptions">
                    <!-- Local Gallery Card -->
                    <div class="optionCard" id="localOption">
                        <div class="optionIcon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <h2>Sessions</h2>
                    
                        <a href="sessionsTherapy.php" class="optionBtn">
                            Explore <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Virtual Gallery Card -->
                    <div class="optionCard" id="virtualOption">
                        <div class="optionIcon">
                            <i class="fas fa-cube"></i>
                        </div>
                        <h2>Workshops</h2>
                    
                        <a href="workshops.php" class="optionBtn">
                            Explore <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
      </section>

      <!-- Footer -->
      <?php include './components/includes/footer.php'; ?>

    <script src="./public/artTherapy2.js"></script>
    <script src="./components/Navbar/navbar.js"></script>
    <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./app.js"></script>
</body>
</html>