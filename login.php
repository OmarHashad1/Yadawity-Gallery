

<?php
// Include database connection
require_once 'API/db.php';

/**
 * Check if user has a valid login cookie
 * @return array - Returns user info if cookie is valid, false otherwise
 */
function checkUserCookie() {
    global $db;
    
    // Check cookie-based authentication only
    if (isset($_COOKIE['user_login'])) {
        $cookieValue = $_COOKIE['user_login'];
        $parts = explode('_', $cookieValue, 2);
        
        if (count($parts) === 2) {
            $userId = $parts[0];
            $cookieHash = $parts[1];
            
            // Get user info to verify cookie
            $stmt = $db->prepare("SELECT user_id, email, first_name, last_name, user_type FROM users WHERE user_id = ? AND is_active = 1");
            if ($stmt) {
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    // Verify cookie hash
                    $expectedHash = hash('sha256', $user['email'] . 'yadawity_salt');
                    
                    if ($cookieHash === $expectedHash) {
                        $stmt->close();
                        return [
                            'active' => true,
                            'user_id' => $user['user_id'],
                            'user_name' => $user['first_name'] . ' ' . $user['last_name'],
                            'user_type' => $user['user_type']
                        ];
                    }
                }
                $stmt->close();
            }
        }
    }
    
    return ['active' => false];
}

// Check if user has a valid login cookie
$cookieCheck = checkUserCookie();
if ($cookieCheck['active']) {
    // User has valid cookie, redirect them
    $redirectUrl = ($cookieCheck['user_type'] === 'admin') ? 'admin-dashboard.php' : 'index.php';
    header("Location: $redirectUrl");
    exit;
}

// Close database connection
if (isset($db)) {
    $db->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Yadawity - Welcome Back</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css" />
    <link rel="stylesheet" href="./public/homePage.css" />
    <link rel="stylesheet" href="./public/login.css" />
 
 
</head>
<body>
    <nav class="navbar navbarYadawity" id="yadawityNavbar">
      <!-- Navbar content -->
    </nav>

    <div class="login-container">
      <div class="logo-section">
        <div class="logo">
          <svg width="40" height="40" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 50 Q15 30 25 25 Q35 20 45 35 Q40 45 35 50 Q40 55 45 65 Q35 80 25 75 Q15 70 20 50 Z" fill="currentColor" opacity="0.8"/>
            <path d="M80 50 Q85 30 75 25 Q65 20 55 35 Q60 45 65 50 Q60 55 55 65 Q65 80 75 75 Q85 70 80 50 Z" fill="currentColor" opacity="0.8"/>
            <line x1="50" y1="20" x2="50" y2="80" stroke="currentColor" stroke-width="3"/>
            <path d="M50 20 Q45 15 42 12 M50 20 Q55 15 58 12" stroke="currentColor" stroke-width="2" fill="none"/>
          </svg>
        </div>
        <div class="brand-info">
          <div class="brand-name">Yadawity</div>
          <div class="brand-tagline">EST. 2025</div>
        </div>
      </div>

      <p class="welcome-subtitle">
        Sign in to your account to continue exploring authentic artworks and handcrafted creations
      </p>

      <form id="loginForm">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" placeholder="johndoe@example.com" required />
        </div>

        <div class="form-group password-section">
          <label for="password">Password</label>
          <a href="#" class="forgot-password">Forgot password?</a>
          <div style="clear: both"></div>
          <input type="password" id="password" name="password" required />
        </div>

        <button type="submit" class="sign-in-btn" id="loginBtn">Login</button>
      </form>

      <!-- Rest of your form content -->
      <div class="divider"><span>or continue with</span></div>
      
      <div class="social-buttons">
        <a href="#" class="social-btn">
          <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
          </svg>
          Continue with Google
        </a>
        <a href="#" class="social-btn">
          <svg class="icon" viewBox="0 0 24 24" fill="#1877F2">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
          </svg>
          Continue with Facebook
        </a>
      </div>

      <div class="signup-link">
        Don't have an account? <a href="API/signup.php">Join Yadawity</a>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsencrypt/3.3.2/jsencrypt.min.js"></script>
    <script src="./app.js"></script>
    <script src="./components/BurgerMenu/burger-menu.js"></script>
<script src="./public/login.js"></script>    
</body>
</html>