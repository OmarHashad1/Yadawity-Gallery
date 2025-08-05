<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Hub - Book Your Perfect Gallery Experience</title>
    
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
    <link rel="stylesheet" href="./public/homePage.css" />
    <link rel="stylesheet" href="./public/gallery.css">
    <link rel="stylesheet" href="./components/Navbar/navbar.css" />
    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css" />
    
</head>
<body>
    <?php include './components/includes/navbar.php'; ?>

    <?php include './components/includes/burger-menu.php'; ?>
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
                  <h1 class="fade-in-up delay-100">Our professional Artist</h1>
                </div>              
                 <div class="galleryOptions">
                    <!-- Local Gallery Card -->
                    <div class="optionCard" id="localOption">
                        <div class="optionIcon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <h2>Local Galleries</h2>
                        <p>Experience art in person - visit physical galleries, meet artists, and immerse yourself in local art culture</p>
                        <div class="tags">
                            <span class="tag">In-Person Tours</span>
                            <span class="tag">Live Events</span>
                        </div>
                        <a href="localGallery.php" class="optionBtn">
                            Explore  <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Virtual Gallery Card -->
                    <div class="optionCard" id="virtualOption">
                        <div class="optionIcon">
                            <i class="fas fa-cube"></i>
                        </div>
                        <h2>Virtual Galleries</h2>
                        <p>Discover digital art experiences - immersive virtual tours, global exhibitions, and interactive installations</p>
                        <div class="tags">
                            <span class="tag">VR Tours</span>
                            <span class="tag">Global Access</span>
                        </div>
                        <a href="virtualGallery.php" class="optionBtn">
                            Explore  <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
      </section>

      <?php include './components/includes/footer.php'; ?>

    
    <script src="./components/Navbar/navbar.js"></script>
    <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./public/gallery.js"></script>
    <script src="./app.js"></script>
</body>
</html>