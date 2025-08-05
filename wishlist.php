<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Wishlist - Yadawity Gallery</title>

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
    <link rel="stylesheet" href="./public/wishlist.css" />

  </head>
  <body>
    <?php include './components/includes/navbar.php'; ?>

    <?php include './components/includes/burger-menu.php'; ?>
        
      </div>
    </div>

    <!-- Page Header -->
    <div class="pageHeader">
      <div class="pageHeaderContent">
        <div class="pageHeaderBadge">
          <i class="fas fa-heart"></i>
          <span>MY FAVORITES</span>
        </div>
        <h1 class="pageTitle">Wishlist</h1>
        <p class="pageDescription">
          Your curated collection of favorite artworks and pieces you'd love to own.
          Keep track of what catches your eye and never miss a piece you love.
        </p>
      </div>
    </div>

    <!-- Wishlist Content -->
    <div class="wishlistContainer">
      <!-- Wishlist Actions -->
      <div class="wishlistActions">
        <div class="actionButtons">
          <button class="actionBtn primary" id="shareWishlistBtn">
            <i class="fas fa-share-alt"></i>
            Share Wishlist
          </button>
          <button class="actionBtn secondary" id="clearWishlistBtn">
            <i class="fas fa-trash-alt"></i>
            Clear All
          </button>
        </div>
        
        <div class="filterOptions">
          <select class="filterSelect" id="categoryFilter">
            <option value="">All Categories</option>
            <option value="paintings">Paintings</option>
            <option value="sculptures">Sculptures</option>
            <option value="photography">Photography</option>
            <option value="mixed-media">Mixed Media</option>
          </select>
          
          <select class="filterSelect" id="sortFilter">
            <option value="recent">Recently Added</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
            <option value="name">Alphabetical</option>
          </select>
        </div>
      </div>

      <!-- Wishlist Grid -->
      <div class="wishlistGrid">
        <!-- Wishlist Item 1 -->
        <div class="wishlistItem" data-category="paintings" data-price="75000">
          <div class="wishlistImageContainer">
            <img 
              src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=400&h=300&fit=crop" 
              alt="Abstract Harmony"
              class="wishlistImage"
            />
            <div class="wishlistBadge available">
              <i class="fas fa-check-circle"></i>
              <span>Available</span>
            </div>
            <button class="removeBtn" title="Remove from Wishlist">
              <i class="fas fa-times"></i>
            </button>
          </div>
          
          <div class="wishlistInfo">
            <h3 class="wishlistTitle">Abstract Harmony</h3>
            <p class="wishlistArtist">by Marina Kovač</p>
            <p class="wishlistDescription">
              Oil on canvas, 80x100cm. A stunning piece exploring the balance between chaos and order...
            </p>
            
            <div class="wishlistPricing">
              <span class="wishlistPrice">EGP 75,000</span>
              <span class="wishlistStatus">In Stock</span>
            </div>
            
            <div class="wishlistActions">
              <button class="addToCartBtn">
                <i class="fas fa-shopping-cart"></i>
                Add to Cart
              </button>
              <button class="viewDetailsBtn">
                <i class="fas fa-eye"></i>
                View Details
              </button>
            </div>
            
            <div class="wishlistMeta">
              <span class="addedDate">Added 3 days ago</span>
            </div>
          </div>
        </div>

        <!-- Wishlist Item 2 -->
        <div class="wishlistItem" data-category="sculptures" data-price="120000">
          <div class="wishlistImageContainer">
            <img 
              src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop" 
              alt="Bronze Elegance"
              class="wishlistImage"
            />
            <div class="wishlistBadge limited">
              <i class="fas fa-hourglass-half"></i>
              <span>Limited</span>
            </div>
            <button class="removeBtn" title="Remove from Wishlist">
              <i class="fas fa-times"></i>
            </button>
          </div>
          
          <div class="wishlistInfo">
            <h3 class="wishlistTitle">Bronze Elegance</h3>
            <p class="wishlistArtist">by Ahmed Hassan</p>
            <p class="wishlistDescription">
              Limited edition bronze sculpture, 45cm height. Masterful craftsmanship showcasing...
            </p>
            
            <div class="wishlistPricing">
              <span class="wishlistPrice">EGP 120,000</span>
              <span class="wishlistStatus limited">Only 2 left</span>
            </div>
            
            <div class="wishlistActions">
              <button class="addToCartBtn">
                <i class="fas fa-shopping-cart"></i>
                Add to Cart
              </button>
              <button class="viewDetailsBtn">
                <i class="fas fa-eye"></i>
                View Details
              </button>
            </div>
            
            <div class="wishlistMeta">
              <span class="addedDate">Added 1 week ago</span>
            </div>
          </div>
        </div>

        <!-- More wishlist items... -->
        <div class="wishlistItem" data-category="photography" data-price="35000">
          <div class="wishlistImageContainer">
            <img 
              src="https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=400&h=300&fit=crop" 
              alt="Urban Reflections"
              class="wishlistImage"
            />
            <div class="wishlistBadge available">
              <i class="fas fa-check-circle"></i>
              <span>Available</span>
            </div>
            <button class="removeBtn" title="Remove from Wishlist">
              <i class="fas fa-times"></i>
            </button>
          </div>
          
          <div class="wishlistInfo">
            <h3 class="wishlistTitle">Urban Reflections</h3>
            <p class="wishlistArtist">by Sarah Chen</p>
            <p class="wishlistDescription">
              Limited edition fine art photography print, 70x50cm. Captures the soul of modern...
            </p>
            
            <div class="wishlistPricing">
              <span class="wishlistPrice">EGP 35,000</span>
              <span class="wishlistStatus">In Stock</span>
            </div>
            
            <div class="wishlistActions">
              <button class="addToCartBtn">
                <i class="fas fa-shopping-cart"></i>
                Add to Cart
              </button>
              <button class="viewDetailsBtn">
                <i class="fas fa-eye"></i>
                View Details
              </button>
            </div>
            
            <div class="wishlistMeta">
              <span class="addedDate">Added 2 days ago</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State (initially hidden) -->
      <div class="emptyWishlist" style="display: none;">
        <div class="emptyIcon">
          <i class="far fa-heart"></i>
        </div>
        <h3>Your wishlist is empty</h3>
        <p>Start exploring our gallery to find pieces you love and add them to your wishlist.</p>
        <a href="gallery.php" class="exploreBtn">
          <i class="fas fa-palette"></i>
          Explore Gallery
        </a>
      </div>
    </div>

    <?php include './components/includes/footer.php'; ?>

    <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./components/Navbar/navbar.js"></script>
    <script src="./public/wishlist.js"></script>
  </body>
</html>
