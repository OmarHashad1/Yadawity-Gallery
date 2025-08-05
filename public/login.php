<?php
require_once "API/db.php"; // Include database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Check if user has a valid session (either from PHP session or login cookie)
 * This is the main authentication verification function
 * 
 * @param object $db Database connection
 * @return bool True if user has valid session, false otherwise
 * 
 * Function flow:
 * 1. First checks if PHP session variables exist and are valid
 * 2. If session is valid, returns true
 * 3. If no valid session, checks for login cookie as fallback
 * 4. Cleans up expired sessions automatically
 */
function checkUserSession($db) {
    try {
        // First priority: Check if session variables exist
        if (isset($_SESSION['user_id']) && isset($_SESSION['session_token'])) {
            $user_id = $_SESSION['user_id'];
            $session_token = $_SESSION['session_token'];
            
            // Verify session in database
            $stmt = $db->prepare("SELECT user_id, expires_at FROM user_login_sessions WHERE session_id = ? AND user_id = ? AND is_active = 1");
            
            if (!$stmt) {
                throw new Exception("Failed to prepare session verification query: " . $db->error);
            }
            
            $stmt->bind_param("si", $session_token, $user_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to execute session verification query: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $session = $result->fetch_assoc();
                
                // Check if session hasn't expired
                if (strtotime($session['expires_at']) > time()) {
                    $stmt->close();
                    return true; // Valid session found
                } else {
                    // Session expired, clean it up
                    cleanupExpiredSession($db, $session_token);
                    session_unset();
                }
            }
            $stmt->close();
        }
        
        // Second priority: Check for active login cookie
        return validateLoginCookie($db);
        
    } catch (Exception $e) {
        error_log("checkUserSession function error: " . $e->getMessage());
        return false;
    }
}

/**
 * Clean up expired user session from database
 * Marks session as inactive and sets logout time
 * 
 * @param object $db Database connection
 * @param string $session_token Session token to cleanup
 * @return bool True if cleanup successful, false otherwise
 */
function cleanupExpiredSession($db, $session_token) {
    try {
        $cleanup_stmt = $db->prepare("UPDATE user_login_sessions SET is_active = 0, logout_time = NOW() WHERE session_id = ?");
        
        if (!$cleanup_stmt) {
            throw new Exception("Failed to prepare cleanup query: " . $db->error);
        }
        
        $cleanup_stmt->bind_param("s", $session_token);
        
        if (!$cleanup_stmt->execute()) {
            throw new Exception("Failed to execute cleanup query: " . $cleanup_stmt->error);
        }
        
        $cleanup_stmt->close();
        return true;
        
    } catch (Exception $e) {
        error_log("cleanupExpiredSession function error: " . $e->getMessage());
        return false;
    }
}


function validateLoginCookie($db) {
    try {
        if (!isset($_COOKIE['user_login'])) {
            return false;
        }
        
        $cookie_parts = explode('_', $_COOKIE['user_login'], 2);
        if (count($cookie_parts) !== 2) {
            // Malformed cookie, clear it and session
            setcookie('user_login', '', time() - 3600, '/', '', false, true);
            session_unset();
            return false;
        }
        
        $user_id = intval($cookie_parts[0]);
        $cookie_hash = $cookie_parts[1];
        
        // Validate user_id is positive integer
        if ($user_id <= 0) {
            // Invalid cookie format, clear it
            setcookie('user_login', '', time() - 3600, '/', '', false, true);
            return false;
        }
        
        // Verify user exists and is active
        $stmt = $db->prepare("SELECT user_id, email FROM users WHERE user_id = ? AND is_active = 1");
        
        if (!$stmt) {
            throw new Exception("Failed to prepare user verification query: " . $db->error);
        }
        
        $stmt->bind_param("i", $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute user verification query: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Check if there's an active session for this user
            if (validateCookieSession($db, $user_id, $user['email'], $cookie_hash)) {
                $stmt->close();
                return true;
            }
        } else {
            // User doesn't exist or is inactive - invalid cookie, clear it and session
            setcookie('user_login', '', time() - 3600, '/', '', false, true);
            session_unset();
        }
        $stmt->close();
        return false;
        
    } catch (Exception $e) {
        error_log("validateLoginCookie function error: " . $e->getMessage());
        return false;
    }
}

/**
 * Validate cookie session by checking hash and restoring session variables
 * Verifies that cookie hash matches expected pattern for active session
 * 
 * @param object $db Database connection
 * @param int $user_id User ID from cookie
 * @param string $email User email
 * @param string $cookie_hash Hash from cookie to validate
 * @return bool True if cookie session is valid, false otherwise
 * 
 * Function flow:
 * 1. Finds most recent active session for user
 * 2. Generates expected hash using email + login_time + salt
 * 3. Compares hashes securely
 * 4. If valid, restores PHP session variables
 */
function validateCookieSession($db, $user_id, $email, $cookie_hash) {
    try {
        $session_stmt = $db->prepare("SELECT session_id, login_time FROM user_login_sessions WHERE user_id = ? AND is_active = 1 AND expires_at > NOW() ORDER BY login_time DESC LIMIT 1");
        
        if (!$session_stmt) {
            throw new Exception("Failed to prepare session validation query: " . $db->error);
        }
        
        $session_stmt->bind_param("i", $user_id);
        
        if (!$session_stmt->execute()) {
            throw new Exception("Failed to execute session validation query: " . $session_stmt->error);
        }
        
        $session_result = $session_stmt->get_result();
        
        if ($session_result->num_rows === 1) {
            $session_data = $session_result->fetch_assoc();
            
            // Verify cookie hash matches expected pattern for this session
            $expected_hash = hash('sha256', $email . $session_data['login_time'] . 'yadawity_salt');
            
            if (hash_equals($expected_hash, $cookie_hash)) {
                // Cookie is valid and matches active session - restore session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['session_token'] = $session_data['session_id'];
                $_SESSION['email'] = $email;
                
                $session_stmt->close();
                return true;
            } else {
                // Cookie hash doesn't match - invalid cookie, clear it and session
                setcookie('user_login', '', time() - 3600, '/', '', false, true);
                session_unset();
            }
        } else {
            // No active session found for this user - cookie is stale, clear it and session
            setcookie('user_login', '', time() - 3600, '/', '', false, true);
            session_unset();
        }
        $session_stmt->close();
        return false;
        
    } catch (Exception $e) {
        error_log("validateCookieSession function error: " . $e->getMessage());
        return false;
    }
}

/**
 * Validate user input (email and password)
 * Performs client-side style validation on server
 * 
 * @param string $email Email to validate
 * @param string $password Password to validate
 * @return array Array with validation results and error messages
 * 
 * Validation rules:
 * - Email: Required, must be valid email format
 * - Password: Required, minimum 6 characters
 */
function validateUserInput($email, $password) {
    try {
        $errors = [
            'email' => '',
            'password' => '',
            'isValid' => true
        ];
        
        // Validate email
        if (empty($email)) {
            $errors['email'] = "Email is required";
            $errors['isValid'] = false;
        } else {
            $email = trim($email);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Invalid email format";
                $errors['isValid'] = false;
            }
        }
        
        // Validate password
        if (empty($password)) {
            $errors['password'] = "Password is required";
            $errors['isValid'] = false;
        } else {
            $password = trim($password);
            if (strlen($password) < 6) {
                $errors['password'] = "Password must be at least 6 characters";
                $errors['isValid'] = false;
            }
        }
        
        return $errors;
        
    } catch (Exception $e) {
        error_log("validateUserInput function error: " . $e->getMessage());
        return [
            'email' => 'Validation error occurred',
            'password' => 'Validation error occurred',
            'isValid' => false
        ];
    }
}

/**
 * Check if user has any active sessions in database
 * Used to prevent multiple simultaneous logins
 * 
 * @param object $db Database connection
 * @param int $user_id User ID to check
 * @return bool True if user has active sessions, false otherwise
 */
function checkActiveUserSessions($db, $user_id) {
    try {
        $active_session_check = $db->prepare("SELECT COUNT(*) as active_count FROM user_login_sessions WHERE user_id = ? AND is_active = 1 AND expires_at > NOW()");
        
        if (!$active_session_check) {
            throw new Exception("Failed to prepare active session check query: " . $db->error);
        }
        
        $active_session_check->bind_param("i", $user_id);
        
        if (!$active_session_check->execute()) {
            throw new Exception("Failed to execute active session check query: " . $active_session_check->error);
        }
        
        $active_result = $active_session_check->get_result();
        $active_data = $active_result->fetch_assoc();
        $active_session_check->close();
        
        return $active_data['active_count'] > 0;
        
    } catch (Exception $e) {
        error_log("checkActiveUserSessions function error: " . $e->getMessage());
        return false;
    }
}

/**
 * Clean up all existing sessions for a user before creating new one
 * Ensures single session per user policy
 * 
 * @param object $db Database connection
 * @param int $user_id User ID to cleanup sessions for
 * @return bool True if cleanup successful, false otherwise
 * 
 * Function flow:
 * 1. Marks all user sessions as inactive
 * 2. Clears existing login cookies
 * 3. Clears PHP session data
 */
function cleanupUserSessions($db, $user_id) {
    try {
        // Clean up any existing sessions for this user before creating new one
        $cleanup_stmt = $db->prepare("UPDATE user_login_sessions SET is_active = 0, logout_time = NOW() WHERE user_id = ?");
        
        if (!$cleanup_stmt) {
            throw new Exception("Failed to prepare cleanup query: " . $db->error);
        }
        
        $cleanup_stmt->bind_param("i", $user_id);
        
        if (!$cleanup_stmt->execute()) {
            throw new Exception("Failed to execute cleanup query: " . $cleanup_stmt->error);
        }
        
        $cleanup_stmt->close();
        
        // Clear any existing login cookies
        if (isset($_COOKIE['user_login'])) {
            setcookie('user_login', '', time() - 3600, '/', '', false, true);
        }
        
        // Clear any existing PHP session data but keep the session active
        session_unset();
        
        return true;
        
    } catch (Exception $e) {
        error_log("cleanupUserSessions function error: " . $e->getMessage());
        return false;
    }
}

/**
 * Generate cryptographically secure unique session token
 * Ensures no token collisions in database
 * 
 * @param object $db Database connection
 * @return string Unique 64-character hexadecimal session token
 * 
 * Function flow:
 * 1. Generates random 32-byte token
 * 2. Checks database for uniqueness
 * 3. Regenerates if duplicate found (rare)
 */
function generateUniqueSessionToken($db) {
    try {
        do {
            $session_token = bin2hex(random_bytes(32));
            $token_check_stmt = $db->prepare("SELECT COUNT(*) as token_count FROM user_login_sessions WHERE session_id = ?");
            
            if (!$token_check_stmt) {
                throw new Exception("Failed to prepare token check query: " . $db->error);
            }
            
            $token_check_stmt->bind_param("s", $session_token);
            
            if (!$token_check_stmt->execute()) {
                throw new Exception("Failed to execute token check query: " . $token_check_stmt->error);
            }
            
            $token_result = $token_check_stmt->get_result();
            $token_data = $token_result->fetch_assoc();
            $token_check_stmt->close();
        } while ($token_data['token_count'] > 0); // Regenerate if token already exists
        
        return $session_token;
        
    } catch (Exception $e) {
        error_log("generateUniqueSessionToken function error: " . $e->getMessage());
        return bin2hex(random_bytes(32)); // Fallback token
    }
}

/**
 * Create new user session and set authentication cookies
 * Main function for establishing user login state
 * 
 * @param object $db Database connection
 * @param array $user User data from database
 * @return array Result array with success status and redirect info
 * 
 * Function flow:
 * 1. Sets PHP session variables
 * 2. Generates unique session token
 * 3. Stores session in database (30-day expiry)
 * 4. Creates secure login cookie
 * 5. Determines redirect URL based on user type
 */
function createUserSession($db, $user) {
    try {
        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['login_time'] = time();
        
        // Generate unique session token
        $session_token = generateUniqueSessionToken($db);
        
        $expires_at = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 days
        $login_time = date('Y-m-d H:i:s');

        // Insert new session into database
        $session_stmt = $db->prepare("INSERT INTO user_login_sessions (session_id, user_id, login_time, expires_at, is_active) VALUES (?, ?, ?, ?, 1)");
        
        if (!$session_stmt) {
            throw new Exception("Failed to prepare session insert query: " . $db->error);
        }
        
        $session_stmt->bind_param("siss", $session_token, $user['user_id'], $login_time, $expires_at);
        
        if (!$session_stmt->execute()) {
            throw new Exception("Failed to execute session insert query: " . $session_stmt->error);
        }
        
        // Store session token in PHP session for tracking
        $_SESSION['session_token'] = $session_token;
        
        // Set secure login cookie that matches our validation logic
        $cookie_hash = hash('sha256', $user['email'] . $login_time . 'yadawity_salt');
        $cookie_value = $user['user_id'] . '_' . $cookie_hash;
        setcookie('user_login', $cookie_value, time() + (30 * 24 * 60 * 60), '/', '', false, true);
        
        $session_stmt->close();
        
        return [
            'success' => true,
            'message' => 'Session created successfully',
            'user_name' => $user['first_name'] . ' ' . $user['last_name'],
            'redirect_url' => ($user['user_type'] === 'admin') ? 'admin-dashboard.html' : 'index.php'
        ];
        
    } catch (Exception $e) {
        error_log("createUserSession function error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Login failed. Please try again.'
        ];
    }
}

/**
 * Authenticate user credentials against database
 * Main authentication function that verifies email/password
 * 
 * @param object $db Database connection
 * @param string $email User email
 * @param string $password User password
 * @return array Authentication result with success status and user data
 * 
 * Function flow:
 * 1. Looks up user by email
 * 2. Verifies password (supports both password_hash and MD5 legacy)
 * 3. Checks for existing active sessions
 * 4. Cleans up old sessions if needed
 * 5. Creates new session if authentication successful
 */
function authenticateUser($db, $email, $password) {
    try {
        // Prepare and execute query to find user by email
        $stmt = $db->prepare("SELECT user_id, email, password, first_name, last_name, user_type FROM users WHERE email = ? AND is_active = 1");
        
        if (!$stmt) {
            throw new Exception("Failed to prepare user authentication query: " . $db->error);
        }
        
        $stmt->bind_param("s", $email);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute user authentication query: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password - check if it's MD5 hashed or password_hash
            if (password_verify($password, $user['password']) || md5($password) === $user['password']) {
                
                // Check if user already has any active session
                if (checkActiveUserSessions($db, $user['user_id'])) {
                    $stmt->close();
                    return [
                        'success' => false,
                        'message' => 'User already logged in',
                        'already_logged_in' => true
                    ];
                }
                
                // Clean up existing sessions
                if (!cleanupUserSessions($db, $user['user_id'])) {
                    $stmt->close();
                    return [
                        'success' => false,
                        'message' => 'Login failed. Please try again.'
                    ];
                }
                
                // Create new session
                $session_result = createUserSession($db, $user);
                $stmt->close();
                return $session_result;
                
            } else {
                $stmt->close();
                return [
                    'success' => false,
                    'message' => 'Invalid email or password'
                ];
            }
        } else {
            $stmt->close();
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }
        
    } catch (Exception $e) {
        error_log("authenticateUser function error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Authentication failed. Please try again.'
        ];
    }
}

/**
 * Process login form submission
 * Main controller function for handling POST requests
 * 
 * @param object $db Database connection
 * @return array Process result with status, errors, and redirect info
 * 
 * Function flow:
 * 1. Re-checks if user is already authenticated (security check)
 * 2. Validates form input
 * 3. Calls authenticateUser if validation passes
 * 4. Returns appropriate response for success/failure
 */
function processLoginForm($db) {
    try {
        // CRITICAL: Re-check authentication status during POST to prevent bypass
        if (checkUserSession($db)) {
            // User is already authenticated, redirect immediately
            return [
                'success' => true,
                'redirect' => true,
                'redirect_url' => 'index.php'
            ];
        }
        
        $email = $_POST["email"] ?? '';
        $password = $_POST["password"] ?? '';
        
        // Validate input
        $validation = validateUserInput($email, $password);
        
        if (!$validation['isValid']) {
            return [
                'success' => false,
                'errors' => $validation,
                'email' => trim($email),
                'password' => ''
            ];
        }
        
        // Authenticate user
        $auth_result = authenticateUser($db, trim($email), trim($password));
        
        if ($auth_result['success']) {
            return [
                'success' => true,
                'login_success' => true,
                'user_name' => $auth_result['user_name'],
                'redirect_url' => $auth_result['redirect_url']
            ];
        } else {
            return [
                'success' => false,
                'login_error' => $auth_result['message'],
                'already_logged_in' => $auth_result['already_logged_in'] ?? false,
                'email' => trim($email),
                'password' => ''
            ];
        }
        
    } catch (Exception $e) {
        error_log("processLoginForm function error: " . $e->getMessage());
        return [
            'success' => false,
            'login_error' => 'Login failed. Please try again.',
            'email' => trim($_POST["email"] ?? ''),
            'password' => ''
        ];
    }
}

// Main execution with try-catch
try {
    // Initialize database connection directly
    if (!isset($db)) {
        // Basic database connection - update these credentials as needed
        $servername = "localhost";
        $username = "root"; // Default XAMPP username
        $password = ""; // Default XAMPP password (empty)
        $dbname = "yadawity_db"; // Update with your actual database name
        
        // Create connection
        $db = new mysqli($servername, $username, $password, $dbname);
        
        // Check connection
        if ($db->connect_error) {
            throw new Exception("Connection failed: " . $db->connect_error);
        }
        
        // Set charset to prevent SQL injection and ensure proper character handling
        $db->set_charset("utf8");
    }

    // Start session
    session_start();

    // Handle encrypted credentials - simple approach without external functions
    if (isset($_POST['enc_email']) && isset($_POST['enc_password'])) {
        // Simple base64 decode for legacy encryption
        try {
            $decoded_email = base64_decode($_POST['enc_email']);
            $decoded_password = base64_decode($_POST['enc_password']);
            
            // Simple decryption reversal
            $email_chars = str_split($decoded_email);
            $password_chars = str_split($decoded_password);
            
            $decrypted_email = '';
            $decrypted_password = '';
            
            foreach ($email_chars as $i => $char) {
                $decrypted_email .= chr(ord($char) - ($i % 4) - 1);
            }
            
            foreach ($password_chars as $i => $char) {
                $decrypted_password .= chr(ord($char) - ($i % 4) - 1);
            }
            
            $_POST['email'] = $decrypted_email;
            $_POST['password'] = $decrypted_password;
            
            unset($_POST['enc_email'], $_POST['enc_password']);
        } catch (Exception $e) {
            error_log("Decryption error: " . $e->getMessage());
        }
    }

    // If user already has a valid session, redirect to homepage
    if (checkUserSession($db)) {
        header("Location: index.php");
        exit();
    }

    // Initialize variables
    $email = $password = "";
    $emailErr = $passwordErr = $loginErr = "";
    $isValid = true;
    $loginSuccess = false;
    $alreadyLoggedIn = false;
    $userName = "";

    // Process form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $result = processLoginForm($db);
        
        if ($result['success']) {
            if (isset($result['redirect']) && $result['redirect']) {
                header("Location: " . $result['redirect_url']);
                exit();
            } elseif (isset($result['login_success']) && $result['login_success']) {
                $loginSuccess = true;
                $userName = $result['user_name'];
                header("Location: " . $result['redirect_url']);
                exit();
            }
        } else {
            if (isset($result['errors'])) {
                $emailErr = $result['errors']['email'];
                $passwordErr = $result['errors']['password'];
                $isValid = $result['errors']['isValid'];
            }
            
            if (isset($result['login_error'])) {
                $loginErr = $result['login_error'];
            }
            
            if (isset($result['already_logged_in'])) {
                $alreadyLoggedIn = $result['already_logged_in'];
            }
            
            $email = $result['email'] ?? '';
            $password = $result['password'] ?? '';
        }
    }

} catch (Exception $e) {
    error_log("login API Error: " . $e->getMessage());
    $loginErr = 'An error occurred during login. Please try again.';
} finally {
    // Always close database connection
    if (isset($db) && $db instanceof mysqli) {
        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Yadawity - Welcome Back</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
      integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css" />
    <link rel="stylesheet" href="./public/homePage.css" />
    <link rel="stylesheet" href="./public/login.css" />
    <style>
      .error {
        color: #e74c3c;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
      }
      .form-group.has-error input {
        border-color: #e74c3c;
        box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.1);
      }
      .form-group.has-success input {
        border-color: #27ae60;
        box-shadow: 0 0 0 2px rgba(39, 174, 96, 0.1);
      }
      .sign-in-btn:disabled {
        background-color: #bdc3c7;
        cursor: not-allowed;
        opacity: 0.6;
      }
      .login-error {
        background-color: #fee;
        color: #e74c3c;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        border: 1px solid #fcc;
      }
    </style>
  </head>
  <body>
    <!-- ...existing navbar code... -->
    <nav class="navbar navbarYadawity" id="yadawityNavbar">
      <div class="navContainer">
        <div class="navLogo">
          <a href="index.php" class="navLogoLink">
            <div class="logoIcon">
              <svg
                width="40"
                height="40"
                viewBox="0 0 100 100"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M20 50 Q15 30 25 25 Q35 20 45 35 Q40 45 35 50 Q40 55 45 65 Q35 80 25 75 Q15 70 20 50 Z"
                  fill="currentColor"
                  opacity="0.8"
                />
                <path
                  d="M80 50 Q85 30 75 25 Q65 20 55 35 Q60 45 65 50 Q60 55 55 65 Q65 80 75 75 Q85 70 80 50 Z"
                  fill="currentColor"
                  opacity="0.8"
                />
                <line
                  x1="50"
                  y1="20"
                  x2="50"
                  y2="80"
                  stroke="currentColor"
                  stroke-width="3"
                />
                <path
                  d="M50 20 Q45 15 42 12 M50 20 Q55 15 58 12"
                  stroke="currentColor"
                  stroke-width="2"
                  fill="none"
                />
              </svg>
            </div>
            <div class="logoText">
              <span class="logoName">Yadawity</span>
              <span class="logoEst">EST. 2025</span>
            </div>
          </a>
        </div>

        <!-- ...existing nav menu code... -->
        <div class="navMenu" id="navMenu">
          <a href="index.php" class="navLink" data-page="home">HOME</a>
          <a href="gallery.html" class="navLink" data-page="gallery">GALLERY</a>
          <a href="courses.html" class="navLink" data-page="courses">COURSES</a>
          <a href="artwork.html" class="navLink" data-page="atelier">ARTWORKS</a>
          <a href="auction.html" class="navLink" data-page="auction">AUCTION HOUSE</a>
          <a href="art therapy.html" class="navLink therapyNav" data-page="therapy">THERAPY</a>

          <div class="navActions">
            <div class="searchContainer">
              <input
                type="text"
                placeholder="Search artists, artworks..."
                class="searchInput"
                id="navbarSearch"
              />
              <button class="searchBtn" id="searchButton">
                <i class="fas fa-search"></i>
              </button>
            </div>

            <a href="wishlist.html" class="navIconLink" title="Wishlist" id="wishlistLink">
              <i class="fas fa-heart"></i>
              <span class="wishlistCount" id="wishlistCount" style="display: none">0</span>
            </a>

            <a href="cart.html" class="navIconLink cartLink" title="Cart" id="cartLink">
              <i class="fas fa-shopping-bag"></i>
              <span class="cartCount" id="cartCount">0</span>
            </a>

            <div class="userDropdown">
              <a href="#" class="navIconLink" title="Account" id="userAccount">
                <i class="fas fa-user"></i>
              </a>
              <div class="userDropdownMenu" id="userMenu">
                <a href="profile.html" class="dropdownItem">
                  <i class="fas fa-user"></i>
                  <span>Profile</span>
                </a>
                <div class="dropdownDivider"></div>
                <a href="login.php" class="dropdownItem" id="loginLogout" rel="noopener">
                  <i class="fas fa-sign-in-alt"></i>
                  <span>Login</span>
                </a>
              </div>
            </div>
          </div>
        </div>

        <div class="navToggle" id="navToggle">
          <span class="bar"></span>
          <span class="bar"></span>
          <span class="bar"></span>
        </div>
      </div>
    </nav>

    <!-- ...existing burger menu code... -->
   

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

    <div class="login-container">
      <div class="logo-section">
        <div class="logo">
          <svg
            width="40"
            height="40"
            viewBox="0 0 100 100"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M20 50 Q15 30 25 25 Q35 20 45 35 Q40 45 35 50 Q40 55 45 65 Q35 80 25 75 Q15 70 20 50 Z"
              fill="currentColor"
              opacity="0.8"
            />
            <path
              d="M80 50 Q85 30 75 25 Q65 20 55 35 Q60 45 65 50 Q60 55 55 65 Q65 80 75 75 Q85 70 80 50 Z"
              fill="currentColor"
              opacity="0.8"
            />
            <line
              x1="50"
              y1="20"
              x2="50"
              y2="80"
              stroke="currentColor"
              stroke-width="3"
            />
            <path
              d="M50 20 Q45 15 42 12 M50 20 Q55 15 58 12"
              stroke="currentColor"
              stroke-width="2"
              fill="none"
            />
          </svg>
        </div>
        <div class="brand-info">
          <div class="brand-name">Yadawity</div>
          <div class="brand-tagline">EST. 2025</div>
        </div>
      </div>

      <p class="welcome-subtitle">
        Sign in to your account to continue exploring authentic artworks and
        handcrafted creations
      </p>

      <?php if (!empty($loginErr)): ?>
        <div class="login-error">
          <i class="fas fa-exclamation-circle"></i>
          <?php echo $loginErr; ?>
        </div>
      <?php endif; ?>

      <form id="loginForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group <?php echo !empty($emailErr) ? 'has-error' : ''; ?>">
          <label for="email">Email Address</label>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="johndoe@example.com"
            value="<?php echo htmlspecialchars($email); ?>"
            required
          />
          <?php if (!empty($emailErr)): ?>
            <span class="error"><?php echo $emailErr; ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group password-section <?php echo !empty($passwordErr) ? 'has-error' : ''; ?>">
          <label for="password">Password</label>
          <a href="#" class="forgot-password">Forgot password?</a>
          <div style="clear: both"></div>
          <input type="password" id="password" name="password" required />
          <?php if (!empty($passwordErr)): ?>
            <span class="error"><?php echo $passwordErr; ?></span>
          <?php endif; ?>
        </div>

        <button type="submit" class="sign-in-btn" id="loginBtn" disabled>Login</button>
      </form>

      <div class="divider">
        <span>or continue with</span>
      </div>

      <div class="social-buttons">
        <a href="#" class="social-btn">
          <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
              fill="#4285F4"
            />
            <path
              d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
              fill="#34A853"
            />
            <path
              d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
              fill="#FBBC05"
            />
            <path
              d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
              fill="#EA4335"
            />
          </svg>
          Continue with Google
        </a>

        <a href="#" class="social-btn">
          <svg class="icon" viewBox="0 0 24 24" fill="#1877F2">
            <path
              d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
            />
          </svg>
          Continue with Facebook
        </a>
      </div>

      <div class="signup-link">
        Don't have an account? <a href="API/signup.php">Join Yadawity</a>
      </div>
    </div>
  
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- JSEncrypt for RSA encryption -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsencrypt/3.3.2/jsencrypt.min.js"></script>
    <script src="./app.js"></script>
    <script src="./components/BurgerMenu/burger-menu.js"></script>
    
    <?php
    // Simple fallback for encryption without external functions
    $publicKey = null;
    $csrfToken = '';
    ?>
    
    <script>
      // RSA Public Key for encryption (disabled for now)
      const RSA_PUBLIC_KEY = <?php echo json_encode($publicKey); ?>;
      const CSRF_TOKEN = <?php echo json_encode($csrfToken); ?>;
      
      // Form validation and button state management
      document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const loginBtn = document.getElementById('loginBtn');
        const form = document.getElementById('loginForm');
        
        // Flag to ensure SweetAlert only shows once
        let alertShown = false;
        
        // Show SweetAlert notifications based on PHP results
        <?php if ($loginSuccess): ?>
          if (!alertShown) {
            alertShown = true;
            Swal.fire({
              icon: 'success',
              title: 'Login Successful!',
              text: 'Welcome back, <?php echo $userName; ?>!',
              confirmButtonText: 'Continue',
              confirmButtonColor: '#27ae60',
              timer: 3000,
              timerProgressBar: true
            }).then((result) => {
              window.location.href = 'index.php';
            });
          }
        <?php elseif ($alreadyLoggedIn): ?>
          if (!alertShown) {
            alertShown = true;
            Swal.fire({
              icon: 'warning',
              title: 'User Logged In Somewhere Else',
              text: 'This account is currently logged in from another location. Please try again in a moment.',
              showConfirmButton: true,
              confirmButtonText: 'OK',
              confirmButtonColor: '#f39c12',
              allowOutsideClick: false,
              allowEscapeKey: false
            }).then(() => {
              // Clear form and reset to normal state instead of reloading
              document.getElementById('email').value = '';
              document.getElementById('password').value = '';
              document.getElementById('loginBtn').disabled = true;
              alertShown = true;
              
              // Remove any error styling
              document.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('has-error', 'has-success');
              });
            });
          }
        <?php elseif (!empty($loginErr) && $_SERVER["REQUEST_METHOD"] == "POST"): ?>
          if (!alertShown) {
            alertShown = true;
            Swal.fire({
              icon: 'error',
              title: 'Login Failed',
              text: '<?php echo $loginErr; ?>',
              confirmButtonText: 'Try Again',
              confirmButtonColor: '#e74c3c'
            });
          }
        <?php endif; ?>
        
        // Validation functions
        function validateEmail(email) {
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          return emailRegex.test(email);
        }
        
        function validatePassword(password) {
          return password.length >= 6;
        }
        
        function updateFormGroup(input, isValid) {
          const formGroup = input.closest('.form-group');
          formGroup.classList.remove('has-error', 'has-success');
          
          if (input.value.trim() !== '') {
            if (isValid) {
              formGroup.classList.add('has-success');
            } else {
              formGroup.classList.add('has-error');
            }
          }
        }
        
        function checkFormValidity() {
          const email = emailInput.value.trim();
          const password = passwordInput.value.trim();
          
          const isEmailValid = email !== '' && validateEmail(email);
          const isPasswordValid = password !== '' && validatePassword(password);
          
          // Update visual feedback
          updateFormGroup(emailInput, isEmailValid);
          updateFormGroup(passwordInput, isPasswordValid);
          
          // Enable/disable login button
          if (isEmailValid && isPasswordValid) {
            loginBtn.disabled = false;
            loginBtn.style.backgroundColor = '';
            loginBtn.style.cursor = '';
            loginBtn.style.opacity = '1';
          } else {
            loginBtn.disabled = true;
            loginBtn.style.backgroundColor = '#bdc3c7';
            loginBtn.style.cursor = 'not-allowed';
            loginBtn.style.opacity = '0.6';
          }
        }
        
        // Real-time validation
        emailInput.addEventListener('input', checkFormValidity);
        emailInput.addEventListener('blur', checkFormValidity);
        passwordInput.addEventListener('input', checkFormValidity);
        passwordInput.addEventListener('blur', checkFormValidity);
        
        // Force initial check
        setTimeout(checkFormValidity, 100);
        
        // Form submission - COMPLETELY FIXED VERSION
        form.addEventListener('submit', function(e) {
          e.preventDefault(); // Always prevent default first
          
          const email = emailInput.value.trim();
          const password = passwordInput.value.trim();
          
          if (!validateEmail(email) || !validatePassword(password)) {
            Swal.fire({
              icon: 'warning',
              title: 'Validation Error',
              text: 'Please fill in all fields correctly before submitting.',
              confirmButtonText: 'OK',
              confirmButtonColor: '#f39c12'
            });
            return;
          }

          // Show a very brief loading message
          Swal.fire({
            title: 'Signing in...',
            allowOutsideClick: false,
            showConfirmButton: false,
            timer: 800,
            timerProgressBar: true,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          // Submit form after short delay
          setTimeout(() => {
            // Close any open Swal dialogs first
            Swal.close();
            
            // Create and submit form directly
            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);
            
            // Use fetch to submit
            fetch(window.location.href, {
              method: 'POST',
              body: formData
            }).then(response => {
              if (response.redirected) {
                window.location.href = response.url;
              } else {
                window.location.reload();
              }
            }).catch(error => {
              console.error('Login error:', error);
              window.location.reload();
            });
          }, 900);
        });
        
        // Helper function for legacy encryption (if needed)
        function legacyEncrypt(text) {
          return btoa(text.split('').map((c, i) => 
            String.fromCharCode(c.charCodeAt(0) + (i % 4) + 1)
          ).join(''));
        }
      });
    </script>
  </body>
</html>