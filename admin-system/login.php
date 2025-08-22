
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Yadawity</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <!-- Header & Navigation -->
    <header class="main-header">
        <nav class="navbar">
            <div class="navbar-logo">
                <a href="/index.php" class="logo-text">Yadawity</a>
            </div>
            <ul class="navbar-links">
                <li><a href="/index.php">Home</a></li>
                <li><a href="/gallery.php">Gallery</a></li>
                <li><a href="/courses.php">Courses</a></li>
                <li><a href="/about.php">About</a></li>
                <li><a href="/contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="login-main">
        <section class="login-section">
            <div class="login-card">
                <div class="login-header">
                                        <div class="login-logo">
                                                <!-- SVG logo placeholder, replace with your logo if available -->
                                                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="4" y="4" width="40" height="40" rx="10" fill="#d4a574"/>
                                                    <rect x="10" y="10" width="28" height="28" rx="6" fill="#faf8f3"/>
                                                    <path d="M14 34L22 22L30 34H14Z" fill="#9abe78"/>
                                                    <circle cx="18" cy="18" r="3" fill="#6b4423"/>
                                                </svg>
                                        </div>
                    <h1 class="login-title">Admin Login</h1>
                    <p class="login-subtitle">Sign in to manage Yadawity</p>
                </div>
                <form id="loginForm" class="login-form">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                    </div>
                    <div class="remember-me">
                        <input type="checkbox" id="remember" class="remember-checkbox" name="remember">
                        <label for="remember" class="remember-label">Remember Me</label>
                    </div>
                    <button type="submit" class="login-btn">Login</button>
                </form>
                <div id="errorMessage" class="alert alert-danger" style="display: none;"></div>
                <div class="forgot-password">
                    <a href="#">Forgot Password?</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="login-footer">
        <p>&copy; <?php echo date('Y'); ?> Yadawity. All rights reserved. | <a href="/privacyPolicy.php">Privacy Policy</a> | <a href="/termsOfService.php">Terms of Service</a></p>
    </footer>

    <!-- Custom JS -->
    <script src="login.js"></script>
</body>
</html>
