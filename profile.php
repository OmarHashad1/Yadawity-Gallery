<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="public/profile.css">
    <script src="public/profile.js" defer></script>
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
    <link rel="stylesheet" href="./components/Navbar/navbar.css" />
    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css" />
    <link rel="stylesheet" href="./public/homePage.css" />

</head>
<body>
    <!-- Navbar -->
    <?php include './components/includes/navbar.php'; ?>
    <!-- Burger Menu -->
    <?php include './components/includes/burger-menu.php'; ?>
    <div class="profile-container">
        <aside class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-avatar">
                    <img src="image/placeholder-artwork.jpg" alt="User Avatar" />
                    <button class="avatar-upload-btn"><i class="fa fa-camera"></i></button>
                </div>
                <div class="profile-member-since">Member since <span id="memberSince">1970</span></div>
                <div class="profile-stats">
                    <div><span id="purchaseCount">0</span><br>PURCHASES</div>
                    <div><span id="wishlistCount">0</span><br>WISHLIST</div>
                    <div><span id="reviewCount">0</span><br>REVIEWS</div>
                </div>
            </div>
            <nav class="profile-nav">
                <ul>
                    <li class="active" data-section="personal"> <i class="fa fa-user"></i> Personal Information</li>
                    <li data-section="orders"> <i class="fa fa-shopping-bag"></i> Order History</li>
                    <li data-section="addresses"> <i class="fa fa-map-marker"></i> Addresses</li>
                    <li data-section="reviews"> <i class="fa fa-star"></i> Reviews</li>
                    <li data-section="security"> <i class="fa fa-shield"></i> Security</li>
                </ul>
            </nav>
        </aside>
        <main class="profile-main">
            <section id="personal" class="profile-section active">
                <h2>PERSONAL INFORMATION</h2>
                <form id="personalForm">
                    <div class="form-row">
                        <input type="text" id="firstName" name="firstName" placeholder="First Name" disabled>
                        <input type="text" id="lastName" name="lastName" placeholder="Last Name" disabled>
                    </div>
                    <div class="form-row">
                        <input type="email" id="email" name="email" placeholder="Email Address" disabled>
                        <input type="text" id="phone" name="phone" placeholder="Phone Number" disabled>
                    </div>
                    <div class="form-row">
                        <textarea id="bio" name="bio" placeholder="Bio" disabled></textarea>
                    </div>
                                        <button type="button" id="editPersonal" class="edit-btn">Edit</button>
                                        <button type="submit" id="savePersonal" class="save-btn" style="display:none;">Save</button>
                                </form>
                                <!-- Edit Modal -->
                                <div id="editModal" class="modal" style="display:none;">
                                    <div class="modal-content">
                                        <span class="close" id="closeEditModal">&times;</span>
                                        <h2>Edit Profile</h2>
                                        <form id="modalEditForm" enctype="multipart/form-data">
                                            <div class="modal-row">
                                                <label for="modalProfileImage">Profile Image</label><br>
                                                <input type="file" id="modalProfileImage" name="profileImage" accept="image/*">
                                                <img id="modalProfilePreview" src="image/placeholder-artwork.jpg" alt="Profile Preview" style="width:80px;height:80px;border-radius:50%;margin-top:10px;">
                                            </div>
                                            <div class="modal-row">
                                                <input type="text" id="modalFirstName" name="firstName" placeholder="First Name" required>
                                                <input type="text" id="modalLastName" name="lastName" placeholder="Last Name" required>
                                            </div>
                                            <div class="modal-row">
                                                <input type="email" id="modalEmail" name="email" placeholder="Email Address" required>
                                                <input type="text" id="modalPhone" name="phone" placeholder="Phone Number" required>
                                            </div>
                                            <div class="modal-row">
                                                <textarea id="modalBio" name="bio" placeholder="Bio"></textarea>
                                            </div>
                                            <div class="modal-actions">
                                                <button type="submit" id="modalSaveBtn" class="save-btn">Save</button>
                                                <button type="button" id="modalCancelBtn" class="edit-btn" style="background:#fff;color:#b8860b;border:1px solid #b8860b;">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                </form>
            </section>
            <section id="orders" class="profile-section">
                <h2>ORDER HISTORY</h2>
                <div id="orderList"></div>
            </section>
            <section id="addresses" class="profile-section">
                <h2>SAVED ADDRESSES</h2>
                <div id="addressList"></div>
                <button type="button" id="addAddress" class="add-btn">Add Address</button>
            </section>
            <section id="reviews" class="profile-section">
                <h2>REVIEWS</h2>
                <div id="reviewList"></div>
            </section>
            <section id="security" class="profile-section">
                <h2>SECURITY SETTINGS</h2>
                <div>
                    <label>Password</label>
                    <p>Keep your account secure with a strong password.</p>
                    <button type="button" id="changePassword" class="change-btn">Change Password</button>
                </div>
            </section>
        </main>
    </div>
    <!-- Footer -->
    <?php include 'components/includes/footer.php'; ?>
</body>
<script src="./components/BurgerMenu/burger-menu.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var navToggle = document.getElementById('nav-toggle');
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            window.openBurgerMenu();
        });
    }
});
</script>
</body>
</html>
