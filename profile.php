<?php
session_start();
require_once "API/db.php";
$user_id = $_SESSION['user_id'];
// Load user info for display
$stmt = $db->prepare("SELECT first_name, last_name, email, profile_picture, created_at, bio, phone FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $profile_picture, $created_at, $bio, $phone);
$stmt->fetch();
$stmt->close();
$profile_photo_stmt = $db->prepare("SELECT photo_filename FROM user_profile_photo WHERE user_id = ?");
$profile_photo_stmt->bind_param("i", $user_id);
$profile_photo_stmt->execute();
$profile_photo_stmt->bind_result($photo_filename);
$profile_photo_stmt->fetch();
$profile_photo_stmt->close();
$profile_picture_final = $photo_filename ? ('uploads/' . $photo_filename) : $profile_picture;
$user = [
  'first_name' => $first_name,
  'last_name' => $last_name,
  'email' => $email,
  'profile_picture' => $profile_picture_final,
  'created_at' => $created_at,
  'bio' => $bio,
  'phone' => $phone
];
// Get stats for Purchases, Wishlist, Reviews
$stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE buyer_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($purchases_count);
$stmt->fetch();
$stmt->close();
$stmt = $db->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($wishlist_count);
$stmt->fetch();
$stmt->close();
$stmt = $db->prepare("SELECT COUNT(*) FROM artist_reviews WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($reviews_count);
$stmt->fetch();
$stmt->close();
// Handle profile update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'updatePersonInfo') {
  $first_name = trim($_POST['first_name']);
  $last_name = trim($_POST['last_name']);
  $email = trim($_POST['email']);
  $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
  $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
  $profile_picture = null;
  $upload_dir = 'uploads/';
  if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
  }
  $photo_filename = null;
  if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['profile_picture']['tmp_name'];
    $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
    $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
    $target = $upload_dir . $filename;
    if (move_uploaded_file($tmp_name, $target)) {
      $profile_picture = $target;
      $photo_filename = $filename;
    }
  } else if (!empty($_POST['current_profile_picture'])) {
    // Use the current profile picture if no new one is uploaded
    $profile_picture = $_POST['current_profile_picture'];
    // Extract filename for user_profile_photo
    $photo_filename = basename($profile_picture);
  }
  // Update user info in DB
  if ($profile_picture) {
    $stmt = $db->prepare("UPDATE users SET first_name=?, last_name=?, email=?, bio=?, phone=?, profile_picture=? WHERE user_id=?");
    $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $bio, $phone, $profile_picture, $user_id);
  } else {
    $stmt = $db->prepare("UPDATE users SET first_name=?, last_name=?, email=?, bio=?, phone=? WHERE user_id=?");
    $stmt->bind_param("sssssi", $first_name, $last_name, $email, $bio, $phone, $user_id);
  }
  $success = $stmt->execute();
  $stmt->close();

  // Update or insert into user_profile_photo
  if ($photo_filename) {
    $check_stmt = $db->prepare("SELECT user_id FROM user_profile_photo WHERE user_id = ?");
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
      $update_stmt = $db->prepare("UPDATE user_profile_photo SET photo_filename=? WHERE user_id=?");
      $update_stmt->bind_param("si", $photo_filename, $user_id);
      $update_stmt->execute();
      $update_stmt->close();
    } else {
      $insert_stmt = $db->prepare("INSERT INTO user_profile_photo (user_id, photo_filename) VALUES (?, ?)");
      $insert_stmt->bind_param("is", $user_id, $photo_filename);
      $insert_stmt->execute();
      $insert_stmt->close();
    }
    $check_stmt->close();
  }

  // Get updated user info and photo
  $stmt = $db->prepare("SELECT first_name, last_name, email, profile_picture, bio, phone FROM users WHERE user_id=?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($fn, $ln, $em, $pp, $bioVal, $phoneVal);
  $stmt->fetch();
  $stmt->close();
  // Get updated photo from user_profile_photo
  $profile_photo_stmt = $db->prepare("SELECT photo_filename FROM user_profile_photo WHERE user_id = ?");
  $profile_photo_stmt->bind_param("i", $user_id);
  $profile_photo_stmt->execute();
  $profile_photo_stmt->bind_result($photo_filename_new);
  $profile_photo_stmt->fetch();
  $profile_photo_stmt->close();
  $profile_picture_final = $photo_filename_new ? ('uploads/' . $photo_filename_new) : $pp;
  echo json_encode([
    'status' => $success ? 'success' : 'fail',
    'first_name' => $fn,
    'last_name' => $ln,
    'email' => $em,
    'profile_picture' => $profile_picture_final,
    'bio' => $bioVal,
    'phone' => $phoneVal
  ]);
  exit;
}
// AJAX endpoint to get latest stats
if (isset($_GET['action']) && $_GET['action'] === 'getProfileStats') {
  // ...existing code...
}
?>
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
              src="<?php echo isset($user['profile_picture']) && $user['profile_picture'] ? htmlspecialchars($user['profile_picture']) : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face'; ?>" 
              alt="Profile Picture"
              class="profileImage"
              id="profileImage"
            />
            <button class="changeImageBtn" id="changeImageBtn">
              <i class="fas fa-camera"></i>
            </button>
          </div>
          
          <div class="profileInfo">
            <h3 class="profileName"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
            <p class="profileEmail"><?php echo htmlspecialchars($user['email']); ?></p>
            <p class="profileMemberSince">Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
          </div>
          
          <div class="profileStats">
            <div class="statItem">
              <span class="statNumber" id="purchasesCount"><?php echo isset($purchases_count) ? $purchases_count : 0; ?></span>
              <span class="statLabel">Purchases</span>
            </div>
            <div class="statItem">
              <span class="statNumber" id="wishlistCount"><?php echo isset($wishlist_count) ? $wishlist_count : 0; ?></span>
              <span class="statLabel">Wishlist</span>
            </div>
            <div class="statItem">
              <span class="statNumber" id="reviewsCount"><?php echo isset($reviews_count) ? $reviews_count : 0; ?></span>
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
              <!-- Profile Edit Modal -->
              <div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.32); z-index:9999; align-items:center; justify-content:center;">
                <form id="modalEditForm" enctype="multipart/form-data" style="background:#fff; padding:48px 60px 40px 60px; border-radius:26px; max-width:420px; width:90vw; margin:auto; box-shadow:0 8px 32px rgba(44,36,18,0.12); font-family:'Playfair Display',serif; display:flex; flex-direction:column; gap:22px; align-items:center;">
                  <div style="display:flex; flex-direction:column; align-items:center; gap:12px; position:relative;">
                    <div style="position:relative; display:inline-block;">
                      <img id="modalProfilePreview" src="<?php echo isset($user['profile_picture']) && $user['profile_picture'] ? htmlspecialchars($user['profile_picture']) : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face'; ?>" alt="Profile Picture" style="width:110px; height:110px; border-radius:50%; object-fit:cover; border:2px solid #e5e5e5; cursor:pointer;">
                      <span id="profilePinIcon" style="position:absolute; bottom:6px; right:6px; background:#fff; border-radius:50%; box-shadow:0 2px 8px rgba(0,0,0,0.08); padding:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; border:1px solid #e5e5e5;">
                        <svg width="20" height="20" fill="#6d4c1b" viewBox="0 0 24 24"><path d="M17.707 6.293l-3.999-3.999c-.391-.391-1.023-.391-1.414 0l-8 8c-.391.391-.391 1.023 0 1.414l3.999 3.999c.391.391 1.023.391 1.414 0l8-8c.391-.391.391-1.023 0-1.414zm-9.414 7.414l-2.585-2.585 7.293-7.293 2.585 2.585-7.293 7.293zm13.707 5.293c0 .553-.447 1-1 1h-16c-.553 0-1-.447-1-1v-2c0-.553.447-1 1-1h16c.553 0 1 .447 1 1v2z"/></svg>
                      </span>
                      <input type="file" name="profile_picture" id="modalProfileInput" accept="image/*" style="display:none;">
                    </div>
                    <label style="font-size:1.08rem; color:#6d4c1b; font-weight:600; cursor:pointer;" for="modalProfileInput">Change Photo</label>
                  </div>
                  <input type="text" name="first_name" id="modalFirstName" placeholder="First Name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required style="padding:16px 16px; border:1px solid #e5e5e5; border-radius:10px; font-size:1.15rem; background:#faf8f6; width:100%;">
                  <input type="text" name="last_name" id="modalLastName" placeholder="Last Name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required style="padding:16px 16px; border:1px solid #e5e5e5; border-radius:10px; font-size:1.15rem; background:#faf8f6; width:100%;">
                  <input type="email" name="email" id="modalEmail" placeholder="Email" value="<?php echo htmlspecialchars($user['email']); ?>" required style="padding:16px 16px; border:1px solid #e5e5e5; border-radius:10px; font-size:1.15rem; background:#faf8f6; width:100%;">
                  <input type="tel" name="phone" id="modalPhone" placeholder="Phone Number" value="<?php echo htmlspecialchars($user['phone']); ?>" style="padding:16px 16px; border:1px solid #e5e5e5; border-radius:10px; font-size:1.15rem; background:#faf8f6; width:100%;">
                  <textarea name="bio" id="modalBio" placeholder="Bio" rows="3" style="padding:16px 16px; border:1px solid #e5e5e5; border-radius:10px; font-size:1.15rem; background:#faf8f6; width:100%; resize:vertical;"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                  <input type="hidden" name="action" value="updatePersonInfo">
                  <input type="hidden" name="current_profile_picture" id="currentProfilePicture" value="<?php echo htmlspecialchars($user['profile_picture']); ?>">
                  <div style="display:flex; gap:18px; justify-content:center; margin-top:10px;">
                    <button type="submit" id="modalSaveBtn" style="background:#6d4c1b; color:#fff; border:none; border-radius:10px; padding:14px 38px; font-size:1.15rem; font-family:'Playfair Display',serif; font-weight:600; cursor:pointer; transition:background 0.2s;">Save</button>
                    <button type="button" id="closeModalBtn" style="background:#fff; color:#6d4c1b; border:1px solid #6d4c1b; border-radius:10px; padding:14px 38px; font-size:1.15rem; font-family:'Playfair Display',serif; font-weight:600; cursor:pointer; transition:background 0.2s;">Cancel</button>
                  </div>
                </form>
              </div>
<script>
// Function to update stats after profile change
function updateProfileStats() {
  fetch('profile.php?action=getProfileStats')
    .then(r => r.json())
    .then(stats => {
      document.getElementById('purchasesCount').textContent = stats.purchases;
      document.getElementById('wishlistCount').textContent = stats.wishlist;
      document.getElementById('reviewsCount').textContent = stats.reviews;
    });
}
// Show modal and fill with current values
document.getElementById('editPersonalInfo').onclick = function() {
  document.getElementById('editModal').style.display = 'flex';
  document.getElementById('modalFirstName').value = document.querySelector('.profileName').textContent.split(' ')[0];
  document.getElementById('modalLastName').value = document.querySelector('.profileName').textContent.split(' ').slice(1).join(' ');
  document.getElementById('modalEmail').value = document.querySelector('.profileEmail').textContent;
};
document.getElementById('closeModalBtn').onclick = function() {
  document.getElementById('editModal').style.display = 'none';
};
// Preview profile image
document.getElementById('modalProfileInput').onchange = function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(ev) {
      document.getElementById('modalProfilePreview').src = ev.target.result;
    };
    reader.readAsDataURL(file);
  document.getElementById('modalSaveBtn').disabled = false;
// Always enable the Save button in the modal
document.getElementById('modalSaveBtn').disabled = false;
  }
};
document.getElementById('modalProfilePreview').onclick = function() {
  document.getElementById('modalProfileInput').click();
};
document.getElementById('profilePinIcon').onclick = function() {
  document.getElementById('modalProfileInput').click();
};
// AJAX submit for modal
document.getElementById('modalEditForm').onsubmit = function(e) {
  e.preventDefault();
  var formData = new FormData(this);
  fetch('profile.php', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    if (data.status === 'success') {
      // Update profile photo in sidebar and modal preview if changed
      if (data.profile_picture) {
        var profileImg = document.getElementById('profileImage');
        if (profileImg) profileImg.src = data.profile_picture + '?t=' + new Date().getTime();
        var modalPreview = document.getElementById('modalProfilePreview');
        if (modalPreview) modalPreview.src = data.profile_picture + '?t=' + new Date().getTime();
      }
      // Update name and email in sidebar
      if (data.first_name && data.last_name) {
        var nameElem = document.querySelector('.profileName');
        if (nameElem) nameElem.textContent = data.first_name + ' ' + data.last_name;
        // Also update personal info box fields
        var firstNameInput = document.getElementById('firstName');
        var lastNameInput = document.getElementById('lastName');
        if (firstNameInput) firstNameInput.value = data.first_name;
        if (lastNameInput) lastNameInput.value = data.last_name;
      }
      if (data.email) {
        var emailElem = document.querySelector('.profileEmail');
        if (emailElem) emailElem.textContent = data.email;
        var emailInput = document.getElementById('email');
        if (emailInput) emailInput.value = data.email;
      }
      if (typeof data.bio !== 'undefined') {
        var bioInput = document.getElementById('bio');
        if (bioInput) bioInput.value = data.bio;
        var modalBio = document.getElementById('modalBio');
        if (modalBio) modalBio.value = data.bio;
      }
      if (typeof data.phone !== 'undefined') {
        var phoneInput = document.getElementById('phone');
        if (phoneInput) phoneInput.value = data.phone;
        var modalPhone = document.getElementById('modalPhone');
        if (modalPhone) modalPhone.value = data.phone;
      }
      updateProfileStats();
      document.getElementById('editModal').style.display = 'none';
    } else {
      alert('Update failed: ' + (data.message || ''));
    }
  });
};
</script>
          </div>
          
          <form class="profileForm" id="personalInfoForm">
            <div class="formGrid">
              <div class="formGroup">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($user['first_name']); ?>" readonly />
              </div>
              
              <div class="formGroup">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($user['last_name']); ?>" readonly />
              </div>
              
              <div class="formRow" style="display:flex; gap:16px;">
                <div class="formGroup" style="flex:1; min-width:180px;">
                  <label for="email">Email Address</label>
                  <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly />
                </div>
                <div class="formGroup" style="flex:1; min-width:180px;">
                  <label for="phone">Phone Number</label>
                  <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly />
                </div>
              </div>
              <div class="formGroup">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" rows="3" readonly><?php echo htmlspecialchars($user['bio']); ?></textarea>
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
          
          <div class="ordersList" id="ordersList">
            <!-- Orders will be loaded here by JavaScript -->
          </div>
<script>
// Fetch and render order history
function renderOrders(orders) {
  const ordersList = document.getElementById('ordersList');
  if (!ordersList) return;
  if (!orders || orders.length === 0) {
    ordersList.innerHTML = '<div style="padding:32px;text-align:center;color:#888;">No orders found.</div>';
    return;
  }
  ordersList.innerHTML = orders.map(order => `
    <div class="orderItem">
      <div class="orderHeader">
        <div class="orderInfo">
          <h4>Order #${order.order_id}</h4>
          <span class="orderDate">${new Date(order.created_at).toLocaleDateString()}</span>
        </div>
        <div class="orderStatus">
          <span class="statusBadge ${order.status}">${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span>
          <span class="orderTotal">EGP ${order.total_amount}</span>
        </div>
      </div>
      <div class="orderItems">
        ${order.items.map(item => `
          <div class="orderItemDetail">
            <img src="${item.artwork_image}" alt="${item.artwork_title}" class="orderItemImage" />
            <div class="orderItemInfo">
              <h5>${item.artwork_title}</h5>
              <span class="itemPrice">EGP ${item.price}</span>
              <span class="itemQty">Qty: ${item.quantity}</span>
            </div>
          </div>
        `).join('')}
      </div>
      <div class="orderActions">
        <button class="orderActionBtn">View Details</button>
        <button class="orderActionBtn">Leave Review</button>
        <button class="orderActionBtn">Reorder</button>
      </div>
    </div>
  `).join('');
}

function fetchOrders() {
  fetch('API/getUserOrder.php', {
    credentials: 'include' // send cookies (PHPSESSID)
  })
    .then(r => r.json())
    .then(data => renderOrders(data))
    .catch(() => {
      const ordersList = document.getElementById('ordersList');
      if (ordersList) ordersList.innerHTML = '<div style="padding:32px;text-align:center;color:#c00;">Failed to load orders.</div>';
    });
}

// Load orders when the order history tab is shown
document.addEventListener('DOMContentLoaded', function() {
  const orderTab = document.querySelector('[data-tab="orderHistory"]');
  if (orderTab) {
    orderTab.addEventListener('click', fetchOrders);
  }
  // Optionally, load immediately if order history is default tab
  if (document.getElementById('orderHistory').classList.contains('active')) {
    fetchOrders();
  }
});
</script>

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
      <button class="editBtn" id="editAddressSectionBtn">
        <i class="fas fa-edit"></i>
        Edit
      </button>
          </div>
          
          <div class="addressesList">
            <div class="addressCard" id="addressCard">
              <div class="addressHeader">
                <h4>Home Address</h4>
                <span class="defaultBadge">Default</span>
              </div>
              <div class="addressDetails" id="addressDetails">
                <div style="display:flex; flex-direction:column; gap:12px; align-items:flex-start; padding:10px 0 10px 0;">
                  <div style="display:flex; align-items:center; gap:18px;">
                    <span style="min-width:70px; color:#6d4c1b; font-weight:600; text-align:left;">Name:</span>
                    <span id="addressName" style="color:#222; font-size:1.08rem;">Ahmed Hassan</span>
                  </div>
                  <div style="display:flex; align-items:center; gap:18px;">
                    <span style="min-width:70px; color:#6d4c1b; font-weight:600; text-align:left;">Street:</span>
                    <span id="addressStreet" style="color:#222; font-size:1.08rem;">123 Tahrir Square, Downtown</span>
                  </div>
                  <div style="display:flex; align-items:center; gap:18px;">
                    <span style="min-width:70px; color:#6d4c1b; font-weight:600; text-align:left;">City:</span>
                    <span id="addressCity" style="color:#222; font-size:1.08rem;">Cairo, Egypt 11511</span>
                  </div>
                  <div style="display:flex; align-items:center; gap:18px;">
                    <span style="min-width:70px; color:#6d4c1b; font-weight:600; text-align:left;">Phone:</span>
                    <span id="addressPhone" style="color:#222; font-size:1.08rem;">+20 1099359953</span>
                  </div>
                </div>
              </div>
                <!-- Address Edit Modal -->
                <div id="editAddressModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.32); z-index:9999; align-items:center; justify-content:center;">
                  <form id="editAddressForm" style="background:#fff; padding:48px 60px 40px 60px; border-radius:26px; max-width:420px; width:90vw; margin:auto; box-shadow:0 8px 32px rgba(44,36,18,0.12); font-family:'Playfair Display',serif; display:flex; flex-direction:column; gap:22px; align-items:center;">
                    <h3 style="font-family:'Playfair Display',serif; color:#6d4c1b; font-size:1.35rem; font-weight:700; margin-bottom:10px;">Edit Address</h3>
                    <input type="text" id="editName" name="name" placeholder="Name" required style="padding:16px 16px; border:1px solid #e5e5e5; border-radius:10px; font-size:1.15rem; background:#faf8f6; width:100%;" />
                    <input type="text" id="editStreet" name="street" placeholder="Street" required style="padding:16px 16px; border:1px solid #e5e5e5; border-radius:10px; font-size:1.15rem; background:#faf8f6; width:100%;" />
                    <input type="text" id="editCity" name="city" placeholder="City" required style="padding:16px 16px; border:1px solid #e5e5e5; border-radius:10px; font-size:1.15rem; background:#faf8f6; width:100%;" />
                    <input type="text" id="editPhone" name="phone" placeholder="Phone Number" required style="padding:16px 16px; border:1px solid #e5e5e5; border-radius:10px; font-size:1.15rem; background:#faf8f6; width:100%;" />
                    <div style="display:flex; gap:18px; width:100%; justify-content:center; margin-top:10px;">
                      <button type="submit" class="saveAddressBtn" style="background:#6d4c1b; color:#fff; border:none; border-radius:10px; padding:14px 38px; font-size:1.15rem; font-family:'Playfair Display',serif; font-weight:600; cursor:pointer; transition:background 0.2s;">Save</button>
                      <button type="button" id="closeEditAddressModalBtn" style="background:#fff; color:#6d4c1b; border:1px solid #6d4c1b; border-radius:10px; padding:14px 38px; font-size:1.15rem; font-family:'Playfair Display',serif; font-weight:600; cursor:pointer; transition:background 0.2s;">Cancel</button>
                    </div>
                  </form>
                </div>
              <div class="addressActions">
                  
              </div>
            </div>

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
              <button class="changePasswordBtn" id="showChangePasswordModal">Change Password</button>
              <!-- Change Password Modal -->
              <div id="changePasswordModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.32); z-index:9999; align-items:center; justify-content:center;">
                <form id="modalChangePasswordForm" style="background:#fff; padding:48px 60px 40px 60px; border-radius:26px; max-width:420px; width:90vw; margin:auto; box-shadow:0 8px 32px rgba(44,36,18,0.12); font-family:'Playfair Display',serif; display:flex; flex-direction:column; gap:22px; align-items:center;">
                  <h3 style="font-family:'Playfair Display',serif; color:#6d4c1b; font-size:1.35rem; font-weight:700; margin-bottom:10px;">Change Password</h3>
                  <input type="password" name="old_password" id="modalOldPassword" placeholder="Old Password" required style="padding:16px 16px; border:1px solid #e5e5e5; border-radius:10px; font-size:1.15rem; background:#faf8f6; width:100%;">
                  <input type="password" name="new_password" id="modalNewPassword" placeholder="New Password" required style="padding:16px 16px; border:1px solid #e5e5e5; border-radius:10px; font-size:1.15rem; background:#faf8f6; width:100%;">
                  <input type="password" name="confirm_password" id="modalConfirmPassword" placeholder="Confirm New Password" required style="padding:16px 16px; border:1px solid #e5e5e5; border-radius:10px; font-size:1.15rem; background:#faf8f6; width:100%;">
                  <div style="display:flex; gap:18px; width:100%; justify-content:center; margin-top:10px;">
                    <button type="submit" id="modalSavePasswordBtn" style="background:#6d4c1b; color:#fff; border:none; border-radius:10px; padding:14px 38px; font-size:1.15rem; font-family:'Playfair Display',serif; font-weight:600; cursor:pointer; transition:background 0.2s;">Save</button>
                    <button type="button" id="closeChangePasswordModalBtn" style="background:#fff; color:#6d4c1b; border:1px solid #6d4c1b; border-radius:10px; padding:14px 38px; font-size:1.15rem; font-family:'Playfair Display',serif; font-weight:600; cursor:pointer; transition:background 0.2s;">Cancel</button>
                  </div>
                </form>
              </div>
            </div>
            
        </div>
      </div>
    </div>

    <?php include './components/includes/footer.php'; ?>

    <script src="./components/BurgerMenu/burger-menu.js"></script>
    <script src="./components/Navbar/navbar.js"></script>
    <script src="./public/profile.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
      // Address Edit Logic
      document.addEventListener('DOMContentLoaded', function() {
        function fetchAddressAndFill() {
          fetch('API/getUserAddress.php')
            .then(res => res.json())
            .then(data => {
              if (data.success && data.addresses.length > 0) {
                const addr = data.addresses[0];
                document.getElementById('addressName').textContent = addr.name;
                document.getElementById('addressStreet').textContent = addr.street;
                document.getElementById('addressCity').textContent = addr.city;
                document.getElementById('addressPhone').textContent = 'Phone: ' + addr.phone;
                // Fill form fields
                document.getElementById('editName').value = addr.name;
                document.getElementById('editStreet').value = addr.street;
                document.getElementById('editCity').value = addr.city;
                document.getElementById('editPhone').value = addr.phone;
                document.getElementById('editAddressForm').dataset.id = addr.id;
              }
            });
        }
        fetchAddressAndFill();
        var editModal = document.getElementById('editAddressModal');
        var editForm = document.getElementById('editAddressForm');
        var closeEditModalBtn = document.getElementById('closeEditAddressModalBtn');
        document.addEventListener('click', function(e) {
          if (e.target && (e.target.id === 'editAddressSectionBtn' || (e.target.closest && e.target.closest('#editAddressSectionBtn')))) {
            if (editModal && editForm) {
              // Pre-fill fields with current values
              fetch('API/getUserAddress.php')
                .then(res => res.json())
                .then(data => {
                  if (data.success && data.addresses.length > 0) {
                    const addr = data.addresses[0];
                    document.getElementById('editName').value = addr.name;
                    document.getElementById('editStreet').value = addr.street;
                    document.getElementById('editCity').value = addr.city;
                    document.getElementById('editPhone').value = addr.phone;
                    editForm.dataset.id = addr.id;
                  }
                });
              editModal.style.display = 'flex';
            }
          }
        });
        if (closeEditModalBtn && editModal && editForm) {
          closeEditModalBtn.addEventListener('click', function() {
            editModal.style.display = 'none';
            editForm.reset();
          });
        }
        window.addEventListener('click', function(e) {
          if (editModal && e.target === editModal) {
            editModal.style.display = 'none';
            editForm.reset();
          }
        });
        if (editForm) {
          editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = editForm.dataset.id;
            const name = document.getElementById('editName').value;
            const street = document.getElementById('editStreet').value;
            const city = document.getElementById('editCity').value;
            const phone = document.getElementById('editPhone').value;
            fetch('API/getUserAddress.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ id, name, street, city, phone })
            })
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                Swal.fire({
                  icon: 'success',
                  title: 'Address Updated',
                  text: 'Your address has been updated!',
                  confirmButtonColor: '#6d4c1b'
                });
                editForm.style.display = 'none';
                addressDetails.style.display = 'block';
                fetchAddressAndFill();
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Update Failed',
                  text: data.error || 'Failed to update address.',
                  confirmButtonColor: '#b30000'
                });
              }
            })
            .catch(() => {
              Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: 'Failed to update address.',
                confirmButtonColor: '#b30000'
              });
            });
          });
        }
      });</script>
    <script>
      // Show/hide change password modal and handle form submit
      document.addEventListener('DOMContentLoaded', function() {
        var showBtn = document.getElementById('showChangePasswordModal');
        var modal = document.getElementById('changePasswordModal');
        var closeBtn = document.getElementById('closeChangePasswordModalBtn');
        var form = document.getElementById('modalChangePasswordForm');
        if (showBtn && modal) {
          showBtn.addEventListener('click', function() {
            modal.style.display = 'flex';
          });
        }
        if (closeBtn && modal) {
          closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
            form.reset();
            clearPasswordError();
          });
        }
        window.addEventListener('click', function(e) {
          if (modal && e.target === modal) {
            modal.style.display = 'none';
            form.reset();
            clearPasswordError();
          }
        });
        // Handle form submit
        if (form) {
          form.addEventListener('submit', function(e) {
            e.preventDefault();
            clearPasswordError();
            var old_password = document.getElementById('modalOldPassword').value;
            var new_password = document.getElementById('modalNewPassword').value;
            var confirm_password = document.getElementById('modalConfirmPassword').value;
            fetch('API/changePassword.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ old_password, new_password, confirm_password })
            })
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                form.reset();
                modal.style.display = 'none';
                Swal.fire({
                  icon: 'success',
                  title: 'Password Changed',
                  text: 'Your password has been changed successfully!',
                  confirmButtonColor: '#6d4c1b'
                });
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Change Failed',
                  text: data.error || 'Failed to change password.',
                  confirmButtonColor: '#b30000'
                });
              }
            })
            .catch(() => {
              Swal.fire({
                icon: 'error',
                title: 'Change Failed',
                text: 'Failed to change password.',
                confirmButtonColor: '#b30000'
              });
            });
          });
        }
        function showPasswordError(msg) {
          var err = document.getElementById('changePasswordError');
          if (!err) {
            err = document.createElement('div');
            err.id = 'changePasswordError';
            err.style.color = '#b30000';
            err.style.marginBottom = '8px';
            err.style.fontWeight = '600';
            form.insertBefore(err, form.firstChild.nextSibling);
          }
          err.textContent = msg;
        }
        function clearPasswordError() {
          var err = document.getElementById('changePasswordError');
          if (err) err.remove();
        }
      });
    </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </body>
</html>