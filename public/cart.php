<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping Cart - Yadawity Gallery</title>

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
    <link rel="stylesheet" href="./public/cart.css" />

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
          <i class="fas fa-shopping-bag"></i>
          <span>SHOPPING CART</span>
        </div>
        <h1 class="pageTitle">Your Cart</h1>
        <p class="pageDescription">
          Review your selected artworks and proceed to secure checkout.
          Each piece is carefully prepared for safe delivery to your home.
        </p>
      </div>
    </div>

    <!-- Cart Content -->
    <div class="cartContainer">
      <div class="cartLayout">
        <!-- Cart Items Section -->
        <div class="cartItems">
          <div class="cartHeader">
            <h2>Cart Items (3)</h2>
            <button class="clearCartBtn" id="clearCartBtn">
              <i class="fas fa-trash-alt"></i>
              Clear Cart
            </button>
          </div>

          <!-- Cart Item 1 -->
          <div class="cartItem" data-price="75000" data-id="item-1">
            <div class="itemImage">
              <img 
                src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=400&h=300&fit=crop" 
                alt="Abstract Harmony"
              />
            </div>
            
            <div class="itemDetails">
              <h3 class="itemTitle">Abstract Harmony</h3>
              <p class="itemArtist">by Marina Kovač</p>
              <p class="itemSpecs">Oil on canvas, 80x100cm</p>
              
              <div class="itemActions">
                <button class="saveForLaterBtn">
                  <i class="fas fa-heart"></i>
                  Save for Later
                </button>
                <button class="removeItemBtn">
                  <i class="fas fa-trash"></i>
                  Remove
                </button>
              </div>
            </div>
            
            <div class="itemPricing">
              <div class="quantity">
                <label>Qty:</label>
                <div class="quantityControls">
                  <button class="qtyBtn minus">-</button>
                  <input type="number" value="1" min="1" max="1" class="qtyInput" readonly>
                  <button class="qtyBtn plus">+</button>
                </div>
              </div>
              <div class="price">EGP 75,000</div>
            </div>
          </div>

          <!-- Cart Item 2 -->
          <div class="cartItem" data-price="35000" data-id="item-2">
            <div class="itemImage">
              <img 
                src="https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=400&h=300&fit=crop" 
                alt="Urban Reflections"
              />
            </div>
            
            <div class="itemDetails">
              <h3 class="itemTitle">Urban Reflections</h3>
              <p class="itemArtist">by Sarah Chen</p>
              <p class="itemSpecs">Photography print, 70x50cm</p>
              
              <div class="itemActions">
                <button class="saveForLaterBtn">
                  <i class="fas fa-heart"></i>
                  Save for Later
                </button>
                <button class="removeItemBtn">
                  <i class="fas fa-trash"></i>
                  Remove
                </button>
              </div>
            </div>
            
            <div class="itemPricing">
              <div class="quantity">
                <label>Qty:</label>
                <div class="quantityControls">
                  <button class="qtyBtn minus">-</button>
                  <input type="number" value="1" min="1" max="1" class="qtyInput" readonly>
                  <button class="qtyBtn plus">+</button>
                </div>
              </div>
              <div class="price">EGP 35,000</div>
            </div>
          </div>

          <!-- Cart Item 3 -->
          <div class="cartItem" data-price="45000" data-id="item-3">
            <div class="itemImage">
              <img 
                src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=400&h=300&fit=crop" 
                alt="Sunset Serenity"
              />
            </div>
            
            <div class="itemDetails">
              <h3 class="itemTitle">Sunset Serenity</h3>
              <p class="itemArtist">by Omar Farouk</p>
              <p class="itemSpecs">Acrylic on canvas, 60x80cm</p>
              
              <div class="itemActions">
                <button class="saveForLaterBtn">
                  <i class="fas fa-heart"></i>
                  Save for Later
                </button>
                <button class="removeItemBtn">
                  <i class="fas fa-trash"></i>
                  Remove
                </button>
              </div>
            </div>
            
            <div class="itemPricing">
              <div class="quantity">
                <label>Qty:</label>
                <div class="quantityControls">
                  <button class="qtyBtn minus">-</button>
                  <input type="number" value="1" min="1" max="1" class="qtyInput" readonly>
                  <button class="qtyBtn plus">+</button>
                </div>
              </div>
              <div class="price">EGP 45,000</div>
            </div>
          </div>
        </div>

        <!-- Order Summary Section -->
        <div class="orderSummary">
          <div class="summaryCard">
            <h3>Order Summary</h3>
            
            <div class="summaryLine">
              <span>Subtotal (3 items):</span>
              <span id="subtotal">EGP 155,000</span>
            </div>
            
            <div class="summaryLine">
              <span>Shipping:</span>
              <span id="shipping">Free</span>
            </div>
            
            <div class="summaryLine">
              <span>Insurance:</span>
              <span id="insurance">EGP 1,550</span>
            </div>
            
            <div class="summaryLine">
              <span>Tax:</span>
              <span id="tax">EGP 15,500</span>
            </div>
            
            <hr class="summaryDivider">
            
            <div class="summaryTotal">
              <span>Total:</span>
              <span id="total">EGP 172,050</span>
            </div>
            
            <button class="checkoutBtn" id="checkoutBtn">
              <i class="fas fa-lock"></i>
              Proceed to Checkout
            </button>
            
            <div class="securityNote">
              <i class="fas fa-shield-alt"></i>
              <span>Secure checkout with SSL encryption</span>
            </div>
          </div>

          <!-- Promo Code -->
          <div class="promoCard">
            <h4>Have a promo code?</h4>
            <div class="promoInput">
              <input type="text" placeholder="Enter code" id="promoCode">
              <button id="applyPromoBtn">Apply</button>
            </div>
          </div>

          <!-- Payment Methods -->
          <div class="paymentMethods">
            <h4>We Accept</h4>
            <div class="paymentIcons">
              <i class="fab fa-cc-visa"></i>
              <i class="fab fa-cc-mastercard"></i>
              <i class="fab fa-cc-amex"></i>
              <i class="fab fa-paypal"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty Cart State (initially hidden) -->
      <div class="emptyCart" style="display: none;">
        <div class="emptyIcon">
          <i class="fas fa-shopping-bag"></i>
        </div>
        <h3>Your cart is empty</h3>
        <p>Browse our gallery to discover amazing artworks and add them to your cart.</p>
        <a href="gallery.php" class="browseBtn">
          <i class="fas fa-palette"></i>
          Browse Gallery
        </a>
      </div>

      <!-- Continue Shopping -->
      <div class="continueShoppingSection">
        <a href="gallery.php" class="continueShoppingBtn">
          <i class="fas fa-arrow-left"></i>
          Continue Shopping
        </a>
      </div>
    </div>

    <?php include './components/includes/footer.php'; ?>

    <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./components/Navbar/navbar.js"></script>
    <script src="./public/cart.js"></script>
  </body>
</html>
