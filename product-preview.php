<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Preview - Yadawity Gallery</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <link rel="stylesheet" href="./components/Navbar/navbar.css" />`n    <!-- Component Styles -->
    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css">
    <link rel="stylesheet" href="./public/homePage.css">
    <link rel="stylesheet" href="./public/product-preview.css">
</head>
<body>
    <!-- Navigation -->
    <?php include './components/includes/navbar.php'; ?>

    <?php include './components/includes/burger-menu.php'; ?>
        
      </div>
    </div>

    <!-- Breadcrumb -->
    <div class="breadcrumbContainer">
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="breadcrumbSeparator">/</span>
            <a href="gallery.php">Gallery</a>
            <span class="breadcrumbSeparator">/</span>
            <a href="artwork.php">Artworks</a>
            <span class="breadcrumbSeparator">/</span>
            <span class="breadcrumbCurrent">Abstract Composition</span>
        </div>
    </div>

    <!-- Main Product Section -->
    <main class="productMain">
        <div class="productContainer">
            <!-- Product Gallery -->
            <div class="productGallery">
                <div class="thumbnailList">
                    <img src="./image/slide1.jpg" alt="Thumbnail 1" class="thumbnail active" data-main="./image/slide1.jpg">
                    <img src="./image/photo-1554907984-15263bfd63bd.jpeg" alt="Thumbnail 2" class="thumbnail" data-main="./image/photo-1554907984-15263bfd63bd.jpeg">
                    <img src="./image/artist-sitting-on-the-floor.jpg" alt="Thumbnail 3" class="thumbnail" data-main="./image/artist-sitting-on-the-floor.jpg">
                    <img src="./image/images.jpeg" alt="Thumbnail 4" class="thumbnail" data-main="./image/images.jpeg">
                </div>
                <div class="mainImageContainer">
                    <img src="./image/slide1.jpg" alt="Abstract Composition" class="mainImage" id="mainImage">
                    <button class="zoomBtn" id="zoomBtn">
                        <i class="fas fa-search-plus"></i>
                    </button>
                </div>
            </div>

            <!-- Product Information -->
            <div class="productInfo">
                <div class="productHeader">
                    <h1 class="productTitle">Abstract Composition</h1>
                    <div class="productMeta">
                        <span class="productType">Oil on Canvas</span>
                        <span class="productDimensions">60cm x 80cm</span>
                    </div>
                </div>

                <div class="artistInfo">
                    <span class="byText">by</span>
                    <a href="artist-profile.html" class="artistLink" id="artistLink">
                        <span class="artistName">Elena Rosetti</span>
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>

                <div class="priceSection">
                    <div class="currentPrice">
                        <span class="currency">EGP</span>
                        <span class="amount">15,500</span>
                    </div>
                    <div class="originalPrice">
                        <span class="currency">EGP</span>
                        <span class="amount">18,000</span>
                    </div>
                    <div class="discount">Save 14%</div>
                </div>

                <div class="productDescription">
                    <h3>Description</h3>
                    <p>This captivating abstract composition showcases the artist's mastery of color and form. The dynamic interplay of warm and cool tones creates a sense of movement and energy that draws the viewer into a contemplative journey. Created using traditional oil painting techniques on premium canvas, this piece represents the intersection of classical methods with contemporary artistic vision.</p>
                    
                    <div class="artworkDetails">
                        <div class="detailItem">
                            <span class="detailLabel">Type:</span>
                            <span class="detailValue">Oil on Canvas</span>
                        </div>
                        <div class="detailItem">
                            <span class="detailLabel">Dimensions:</span>
                            <span class="detailValue">60cm x 80cm</span>
                        </div>
                        <div class="detailItem">
                            <span class="detailLabel">Year:</span>
                            <span class="detailValue">2024</span>
                        </div>
                        <div class="detailItem">
                            <span class="detailLabel">Style:</span>
                            <span class="detailValue">Abstract Expressionism</span>
                        </div>
                        
                    </div>
                </div>

                <div class="deliveryInfo">
                    <div class="deliveryOption">
                        <i class="fas fa-truck"></i>
                        <div class="deliveryText">
                            <strong>Delivery</strong>
                            <span>Available - Check options at checkout</span>
                        </div>
                    </div>
                    <div class="deliveryOption">
                        <i class="fas fa-store"></i>
                        <div class="deliveryText">
                            <strong>Gallery Pickup</strong>
                            <span>Available at our Cairo location</span>
                        </div>
                    </div>
                </div>

                <div class="purchaseActions">
                    <button class="addToCartBtn" id="addToCartBtn">
                        <i class="fas fa-shopping-bag"></i>
                        Add to Cart
                    </button>
                    
                    <button class="wishlistBtn" id="wishlistBtn">
                        <i class="far fa-heart"></i>
                    </button>
                </div>

                <div class="socialShare">
                    <span class="shareLabel">Share:</span>
                    <div class="shareButtons">
                        <button class="shareBtn facebook" data-platform="facebook">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                        <button class="shareBtn twitter" data-platform="twitter">
                            <i class="fab fa-twitter"></i>
                        </button>
                        <button class="shareBtn pinterest" data-platform="pinterest">
                            <i class="fab fa-pinterest"></i>
                        </button>
                        <button class="shareBtn whatsapp" data-platform="whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Similar Products Section -->
    <section class="similarProducts">
        <div class="sectionContainer">
            <div class="sectionHeader">
                <h2>Similar Products</h2>
                <p>Discover more artworks that complement your style</p>
            </div>
            
            <div class="productsGrid">
                <div class="productCard">
                    <div class="productImageContainer">
                        <img src="./image/photo-1554907984-15263bfd63bd.jpeg" alt="Modern Landscape" class="productImage">
                        <div class="productOverlay">
                            <button class="quickViewBtn">Quick View</button>
                            <button class="addToWishlistBtn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="productCardInfo">
                        <h3 class="productCardTitle">Modern Landscape</h3>
                        <p class="productCardArtist">by Ahmed Hassan</p>
                        <div class="productCardPrice">
                            <span class="currentPrice">EGP 12,000</span>
                        </div>
                    </div>
                </div>

                <div class="productCard">
                    <div class="productImageContainer">
                        <img src="./image/artist-sitting-on-the-floor.jpg" alt="Portrait Study" class="productImage">
                        <div class="productOverlay">
                            <button class="quickViewBtn">Quick View</button>
                            <button class="addToWishlistBtn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="productCardInfo">
                        <h3 class="productCardTitle">Portrait Study</h3>
                        <p class="productCardArtist">by Sara Mohamed</p>
                        <div class="productCardPrice">
                            <span class="currentPrice">EGP 8,500</span>
                            <span class="originalPrice">EGP 10,000</span>
                        </div>
                    </div>
                </div>

                <div class="productCard">
                    <div class="productImageContainer">
                        <img src="./image/images.jpeg" alt="Geometric Forms" class="productImage">
                        <div class="productOverlay">
                            <button class="quickViewBtn">Quick View</button>
                            <button class="addToWishlistBtn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="productCardInfo">
                        <h3 class="productCardTitle">Geometric Forms</h3>
                        <p class="productCardArtist">by Omar Farouk</p>
                        <div class="productCardPrice">
                            <span class="currentPrice">EGP 22,000</span>
                        </div>
                    </div>
                </div>

                <div class="productCard">
                    <div class="productImageContainer">
                        <img src="./image/_grj4724.jpg" alt="Color Symphony" class="productImage">
                        <div class="productOverlay">
                            <button class="quickViewBtn">Quick View</button>
                            <button class="addToWishlistBtn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="productCardInfo">
                        <h3 class="productCardTitle">Color Symphony</h3>
                        <p class="productCardArtist">by Layla Mansour</p>
                        <div class="productCardPrice">
                            <span class="currentPrice">EGP 16,800</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="viewAllContainer">
                <a href="gallery.php" class="viewAllBtn">
                    View All Artworks
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include './components/includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="./components/Navbar/navbar.js"></script>
    <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./public/product-preview.js"></script>
</body>
</html>
