<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Profile - Yadawity Gallery</title>

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
    <link rel="stylesheet" href="./public/profile.css" />

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
          <i class="fas fa-user-circle"></i>
          <span>MY ACCOUNT</span>
        </div>
        <h1 class="pageTitle">Profile</h1>
        <p class="pageDescription">
          Manage your account information, preferences, and view your activity within the Yadawity community.
        </p>
      </div>
    </div>

    <!-- Profile Content -->
    <div class="profileContainer">
      <!-- Profile Sidebar -->
      <div class="profileSidebar">
        <div class="profileCard">
          <div class="profileImageContainer">
            <img 
              src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face" 
              alt="Profile Picture"
              class="profileImage"
              id="profileImage"
            />
            <button class="changeImageBtn" id="changeImageBtn">
              <i class="fas fa-camera"></i>
            </button>
          </div>
          
          <div class="profileInfo">
            <h3 class="profileName">Ahmed Hassan</h3>
            <p class="profileEmail">ahmed.hassan@email.com</p>
            <p class="profileMemberSince">Member since March 2024</p>
          </div>
          
          <div class="profileStats">
            <div class="statItem">
              <span class="statNumber">12</span>
              <span class="statLabel">Purchases</span>
            </div>
            <div class="statItem">
              <span class="statNumber">7</span>
              <span class="statLabel">Wishlist</span>
            </div>
            <div class="statItem">
              <span class="statNumber">5</span>
              <span class="statLabel">Reviews</span>
            </div>
          </div>
        </div>
        
        <!-- Navigation Menu -->
        <div class="profileMenu">
          <a href="#personalInfo" class="menuItem active" data-tab="personalInfo">
            <i class="fas fa-user"></i>
            <span>Personal Information</span>
          </a>
          <a href="#orderHistory" class="menuItem" data-tab="orderHistory">
            <i class="fas fa-shopping-bag"></i>
            <span>Order History</span>
          </a>
          <a href="#addresses" class="menuItem" data-tab="addresses">
            <i class="fas fa-map-marker-alt"></i>
            <span>Addresses</span>
          </a>
          <a href="#paymentMethods" class="menuItem" data-tab="paymentMethods">
            <i class="fas fa-credit-card"></i>
            <span>Payment Methods</span>
          </a>
          <a href="#notifications" class="menuItem" data-tab="notifications">
            <i class="fas fa-bell"></i>
            <span>Notifications</span>
          </a>
          <a href="#security" class="menuItem" data-tab="security">
            <i class="fas fa-shield-alt"></i>
            <span>Security</span>
          </a>
        </div>
      </div>

      <!-- Profile Main Content -->
      <div class="profileMainContent">
        <!-- Personal Information Tab -->
        <div class="tabContent active" id="personalInfo">
          <div class="sectionHeader">
            <h2>Personal Information</h2>
            <button class="editBtn" id="editPersonalInfo">
              <i class="fas fa-edit"></i>
              Edit
            </button>
          </div>
          
          <form class="profileForm" id="personalInfoForm">
            <div class="formGrid">
              <div class="formGroup">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName" value="Ahmed" readonly />
              </div>
              
              <div class="formGroup">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName" value="Hassan" readonly />
              </div>
              
              <div class="formGroup">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="ahmed.hassan@email.com" readonly />
              </div>
              
              <div class="formGroup">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="+20 1099359953" readonly />
              </div>
              
          </form>
        </div>

        <!-- Order History Tab -->
        <div class="tabContent" id="orderHistory">
          <div class="sectionHeader">
            <h2>Order History</h2>
            <div class="orderFilters">
              <select class="filterSelect" id="orderStatusFilter">
                <option value="">All Orders</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
              </select>
              <select class="filterSelect" id="orderDateFilter">
                <option value="">All Time</option>
                <option value="30">Last 30 Days</option>
                <option value="90">Last 3 Months</option>
                <option value="365">Last Year</option>
              </select>
            </div>
          </div>
          
          <div class="ordersList">
            <!-- Order Item 1 -->
            <div class="orderItem">
              <div class="orderHeader">
                <div class="orderInfo">
                  <h4>Order #ORD-2024-001</h4>
                  <span class="orderDate">January 15, 2024</span>
                </div>
                <div class="orderStatus">
                  <span class="statusBadge delivered">Delivered</span>
                  <span class="orderTotal">EGP 75,000</span>
                </div>
              </div>
              
              <div class="orderItems">
                <div class="orderItemDetail">
                  <img 
                    src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=80&h=80&fit=crop" 
                    alt="Abstract Harmony"
                    class="orderItemImage"
                  />
                  <div class="orderItemInfo">
                    <h5>Abstract Harmony</h5>
                    <p>by Marina Kovač</p>
                    <span class="itemPrice">EGP 75,000</span>
                  </div>
                </div>
              </div>
              
              <div class="orderActions">
                <button class="orderActionBtn">View Details</button>
                <button class="orderActionBtn">Leave Review</button>
                <button class="orderActionBtn">Reorder</button>
              </div>
            </div>

            <!-- Order Item 2 -->
            <div class="orderItem">
              <div class="orderHeader">
                <div class="orderInfo">
                  <h4>Order #ORD-2024-002</h4>
                  <span class="orderDate">February 3, 2024</span>
                </div>
                <div class="orderStatus">
                  <span class="statusBadge shipped">Shipped</span>
                  <span class="orderTotal">EGP 45,500</span>
                </div>
              </div>
              
              <div class="orderItems">
                <div class="orderItemDetail">
                  <img 
                    src="https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=80&h=80&fit=crop" 
                    alt="Urban Reflections"
                    class="orderItemImage"
                  />
                  <div class="orderItemInfo">
                    <h5>Urban Reflections</h5>
                    <p>by Sarah Chen</p>
                    <span class="itemPrice">EGP 35,000</span>
                  </div>
                </div>
                <div class="orderItemDetail">
                  <img 
                    src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=80&h=80&fit=crop" 
                    alt="Ceramic Vase"
                    class="orderItemImage"
                  />
                  <div class="orderItemInfo">
                    <h5>Handcrafted Ceramic Vase</h5>
                    <p>by Local Artisan</p>
                    <span class="itemPrice">EGP 10,500</span>
                  </div>
                </div>
              </div>
              
              <div class="orderActions">
                <button class="orderActionBtn">Track Package</button>
                <button class="orderActionBtn">View Details</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Addresses Tab -->
        <div class="tabContent" id="addresses">
          <div class="sectionHeader">
            <h2>Saved Addresses</h2>
            <button class="addBtn" id="addAddressBtn">
              <i class="fas fa-plus"></i>
              Add New Address
            </button>
          </div>
          
          <div class="addressesList">
            <!-- Address 1 -->
            <div class="addressCard">
              <div class="addressHeader">
                <h4>Home Address</h4>
                <span class="defaultBadge">Default</span>
              </div>
              <div class="addressDetails">
                <p><strong>Ahmed Hassan</strong></p>
                <p>123 Tahrir Square, Downtown</p>
                <p>Cairo, Egypt 11511</p>
                <p>Phone: +20 1099359953</p>
              </div>
              <div class="addressActions">
                <button class="editAddressBtn">Edit</button>
                <button class="deleteAddressBtn">Delete</button>
              </div>
            </div>

            <!-- Address 2 -->
            <div class="addressCard">
              <div class="addressHeader">
                <h4>Work Address</h4>
              </div>
              <div class="addressDetails">
                <p><strong>Ahmed Hassan</strong></p>
                <p>456 Zamalek Street, Zamalek</p>
                <p>Cairo, Egypt 11211</p>
                <p>Phone: +20 1099359953</p>
              </div>
              <div class="addressActions">
                <button class="editAddressBtn">Edit</button>
                <button class="setDefaultBtn">Set as Default</button>
                <button class="deleteAddressBtn">Delete</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Methods Tab -->
        <div class="tabContent" id="paymentMethods">
          <div class="sectionHeader">
            <h2>Payment Methods</h2>
            <button class="addBtn" id="addPaymentBtn">
              <i class="fas fa-plus"></i>
              Add Payment Method
            </button>
          </div>
          
          <div class="paymentMethodsList">
            <!-- Payment Method 1 -->
            <div class="paymentCard">
              <div class="paymentHeader">
                <div class="cardInfo">
                  <i class="fab fa-cc-visa"></i>
                  <span class="cardNumber">**** **** **** 1234</span>
                </div>
                <span class="defaultBadge">Default</span>
              </div>
              <div class="paymentDetails">
                <p>Expires: 12/26</p>
                <p>Ahmed Hassan</p>
              </div>
              <div class="paymentActions">
                <button class="editPaymentBtn">Edit</button>
                <button class="deletePaymentBtn">Delete</button>
              </div>
            </div>

            <!-- Payment Method 2 -->
            <div class="paymentCard">
              <div class="paymentHeader">
                <div class="cardInfo">
                  <i class="fab fa-cc-mastercard"></i>
                  <span class="cardNumber">**** **** **** 5678</span>
                </div>
              </div>
              <div class="paymentDetails">
                <p>Expires: 08/27</p>
                <p>Ahmed Hassan</p>
              </div>
              <div class="paymentActions">
                <button class="editPaymentBtn">Edit</button>
                <button class="setDefaultPaymentBtn">Set as Default</button>
                <button class="deletePaymentBtn">Delete</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Notifications Tab -->
        <div class="tabContent" id="notifications">
          <div class="sectionHeader">
            <h2>Notification Preferences</h2>
          </div>
          
          <div class="notificationSettings">
            <div class="settingGroup">
              <h4>Email Notifications</h4>
              <div class="settingItem">
                <label class="settingLabel">
                  <input type="checkbox" class="settingCheckbox" checked />
                  <span class="checkmark"></span>
                  Order updates and shipping notifications
                </label>
              </div>
              <div class="settingItem">
                <label class="settingLabel">
                  <input type="checkbox" class="settingCheckbox" checked />
                  <span class="checkmark"></span>
                  New artwork arrivals and featured artists
                </label>
              </div>
              <div class="settingItem">
                <label class="settingLabel">
                  <input type="checkbox" class="settingCheckbox" />
                  <span class="checkmark"></span>
                  Promotional offers and discounts
                </label>
              </div>
            </div>
            
            <div class="settingGroup">
              <h4>Push Notifications</h4>
              <div class="settingItem">
                <label class="settingLabel">
                  <input type="checkbox" class="settingCheckbox" checked />
                  <span class="checkmark"></span>
                  Auction alerts and bidding updates
                </label>
              </div>
              <div class="settingItem">
                <label class="settingLabel">
                  <input type="checkbox" class="settingCheckbox" />
                  <span class="checkmark"></span>
                  Wishlist item price drops
                </label>
              </div>
            </div>
            
            <div class="settingGroup">
              <h4>SMS Notifications</h4>
              <div class="settingItem">
                <label class="settingLabel">
                  <input type="checkbox" class="settingCheckbox" />
                  <span class="checkmark"></span>
                  Order delivery confirmations
                </label>
              </div>
            </div>
            
            <button class="saveNotificationsBtn">Save Preferences</button>
          </div>
        </div>

        <!-- Security Tab -->
        <div class="tabContent" id="security">
          <div class="sectionHeader">
            <h2>Security Settings</h2>
          </div>
          
          <div class="securitySettings">
            <div class="securityGroup">
              <h4>Password</h4>
              <p>Keep your account secure with a strong password.</p>
              <button class="changePasswordBtn">Change Password</button>
            </div>
            
            <div class="securityGroup">
              <h4>Two-Factor Authentication</h4>
              <p>Add an extra layer of security to your account.</p>
              <div class="twoFactorStatus">
                <span class="statusIndicator disabled"></span>
                <span>Two-factor authentication is disabled</span>
                <button class="enableTwoFactorBtn">Enable</button>
              </div>
            </div>
            
            <div class="securityGroup">
              <h4>Login Activity</h4>
              <p>Review recent login activity on your account.</p>
              <div class="loginActivity">
                <div class="activityItem">
                  <div class="activityInfo">
                    <p><strong>Current session</strong></p>
                    <p>Cairo, Egypt • Chrome on Windows</p>
                  </div>
                  <span class="activityTime">Active now</span>
                </div>
                <div class="activityItem">
                  <div class="activityInfo">
                    <p>Mobile app</p>
                    <p>Cairo, Egypt • iOS</p>
                  </div>
                  <span class="activityTime">2 hours ago</span>
                </div>
              </div>
            </div>
            
            <div class="securityGroup dangerous">
              <h4>Account Deletion</h4>
              <p>Permanently delete your account and all associated data.</p>
              <button class="deleteAccountBtn">Delete Account</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php include './components/includes/footer.php'; ?>

    <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./components/Navbar/navbar.js"></script>
    <script src="./public/profile.js"></script>
  </body>
</html>
