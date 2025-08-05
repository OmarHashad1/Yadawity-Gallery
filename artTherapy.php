<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yadawity - Courses</title>
    
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
<link rel="stylesheet" href="./public/courses.css" />
    <link rel="stylesheet" href="./public/artTherapy.css" />
    
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
                <h1 class="page-title">ART THERAPY</h1>
              </div>
        </header>

        <!-- Search Section -->
        <div class="contentWrapper">
    <div class="contentSection">
      <h2>What is Art Therapy?</h2>
      <p>Art therapy is a form of psychotherapy that uses visual art as a primary method of communication and healing. It helps people express emotions, process trauma, and improve mental well-being through creative expression.</p>
    </div>

    <div class="contentSection">
      <h2>1. Behavioral Art Therapy</h2>
      <p>This approach helps a person change unwanted or harmful behaviors using guided artistic activities.</p>
      <ul>
        <li>Example: A child who acts out might draw instead of hitting.</li>
        <li>Based on: Behavior can be unlearned or replaced.</li>
      </ul>
    </div>

    <div class="contentSection">
      <h2>2. Cognitive Art Therapy</h2>
      <p>Combines Cognitive Behavioral Therapy (CBT) with art-making to help change negative thought patterns.</p>
      <ul>
        <li>Example: A person who sees themselves as a failure may draw themselves succeeding.</li>
        <li>Goal: Transform negative beliefs through creative expression.</li>
      </ul>
    </div>

    <div class="contentSection">
      <h2>3. Dialectical Behavior Therapy (DBT) with Art</h2>
      <p>Helps individuals with intense emotional difficulties by teaching emotional regulation through creative techniques.</p>
      <ul>
        <li>Balances acceptance and change.</li>
        <li>Helps with stress, relationships, and self-expression.</li>
      </ul>
    </div>

    <div class="contentSection">
      <h2>4. Trauma-Informed Art Therapy</h2>
      <p>Designed for people who experienced trauma, this therapy uses art to express deep emotions without direct verbalization.</p>
      <ul>
        <li>Focuses on safety, healing, and emotional release.</li>
        <li>Examples include body-mapping and abstract emotional art.</li>
      </ul>
    </div>

    <a href="artTherapy2.php" class="optionBtn">Now we can start!!</a>
    <style>
      
    </style>
   </div>
      </div>
    <?php include './components/includes/footer.php'; ?>
     <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./public/course.js"></script>
    </script>
</body>
</html>