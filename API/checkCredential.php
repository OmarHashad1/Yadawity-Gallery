<?php
// Only set headers if this file is being accessed directly as an API endpoint
if (basename($_SERVER['PHP_SELF']) === 'checkCredential.php') {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    // Handle preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

include_once 'db.php';

/**
 * Function to check if user is authenticated based on session cookie
 * Returns user information if authenticated, false otherwise
 */
function checkUserAuthentication() {
    global $db;
    
    // Check if user_login cookie exists (this is what the login system actually sets)
    if (!isset($_COOKIE['user_login'])) {
        return false;
    }
    
    $cookie_value = $_COOKIE['user_login'];
    
    // Extract user_id from cookie (format: user_id_hash)
    $cookie_parts = explode('_', $cookie_value, 2);
    if (count($cookie_parts) !== 2) {
        return false;
    }
    
    $user_id = (int)$cookie_parts[0];
    $cookie_hash = $cookie_parts[1];
    
    // Get the most recent active session for this user
    $query = "SELECT 
                s.session_id,
                s.user_id, 
                s.login_time,
                s.expires_at,
                u.user_type,
                u.first_name,
                u.last_name,
                u.email
              FROM user_login_sessions s 
              JOIN users u ON s.user_id = u.user_id 
              WHERE s.user_id = ? 
              AND s.is_active = 1 
              AND s.expires_at > NOW()
              AND u.is_active = 1
              ORDER BY s.login_time DESC
              LIMIT 1";
    
    $stmt = $db->prepare($query);
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $session_data = $result->fetch_assoc();
        
        // Verify the cookie hash matches what we expect
        $expected_hash = hash('sha256', $session_data['email'] . $session_data['login_time'] . 'yadawity_salt');
        
        if ($cookie_hash === $expected_hash) {
            // Additional token validation - check if session_id token is valid
            $token_query = "SELECT session_id FROM user_login_sessions 
                           WHERE session_id = ? AND user_id = ? AND is_active = 1 AND expires_at > NOW()";
            $token_stmt = $db->prepare($token_query);
            
            if ($token_stmt) {
                $token_stmt->bind_param("si", $session_data['session_id'], $user_id);
                $token_stmt->execute();
                $token_result = $token_stmt->get_result();
                
                if ($token_result->num_rows === 1) {
                    $token_stmt->close();
                    $stmt->close();
                    return $session_data;
                }
                $token_stmt->close();
            }
        }
    }
    
    $stmt->close();
    return false;
}

// Handle the authentication check endpoint ONLY when called directly
if (basename($_SERVER['PHP_SELF']) === 'checkCredential.php' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $auth_result = checkUserAuthentication();
        
        if ($auth_result) {
            // User is authenticated
            echo json_encode([
                'success' => true,
                'authenticated' => true,
                'user_id' => (int)$auth_result['user_id'],
                'user_type' => $auth_result['user_type'],
                'email' => $auth_result['email']
            ]);
        } else {
            // User is not authenticated
            // Check if this is an AJAX request
            $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            
            // Check for fetch API request (common in modern JS)
            $is_fetch = isset($_SERVER['HTTP_ACCEPT']) && 
                       strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
            
            if ($is_ajax || $is_fetch || isset($_GET['json'])) {
                // Return JSON response for AJAX/API calls
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'authenticated' => false,
                    'message' => 'User not authenticated or session expired',
                    'redirect' => '../login.php'
                ]);
            } else {
                // Redirect for direct browser access
                $current_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
                $redirect_url = '../login.php';
                
                // Add current URL as redirect parameter if available
                if (!empty($current_url) && !strpos($current_url, 'login.php')) {
                    $redirect_url .= '?redirect=' . urlencode($current_url);
                }
                
                header('Location: ' . $redirect_url);
                exit();
            }
        }
    } catch (Exception $e) {
        // Check if this is an AJAX request for error handling too
        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        $is_fetch = isset($_SERVER['HTTP_ACCEPT']) && 
                   strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
        
        if ($is_ajax || $is_fetch || isset($_GET['json'])) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'authenticated' => false,
                'error' => 'Authentication check failed',
                'message' => $e->getMessage(),
                'redirect' => '../login.php'
            ]);
        } else {
            // Redirect to login with error for direct browser access
            header('Location: ../login.php?error=auth_failed');
            exit();
        }
    }
} elseif (basename($_SERVER['PHP_SELF']) === 'checkCredential.php') {
    // Method not allowed - only when called directly as API endpoint
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
}

/*
DATABASE SCHEMA REFERENCE:

-- Users table (combined with artist information)
CREATE TABLE users (
user_id INT AUTO_INCREMENT PRIMARY KEY,
email VARCHAR(255) UNIQUE NOT NULL,
password VARCHAR(255) NOT NULL,
first_name VARCHAR(100) NOT NULL,
last_name VARCHAR(100) NOT NULL,
phone VARCHAR(20),
user_type ENUM('artist', 'buyer', 'admin') DEFAULT 'buyer',
profile_picture VARCHAR(500),
bio TEXT,
is_active TINYINT(1) DEFAULT 1,
-- Artist-specific fields (nullable)
art_specialty VARCHAR(255) NULL,
years_of_experience INT NULL,
achievements TEXT NULL,
artist_bio TEXT NULL,
location VARCHAR(255) NULL,
education TEXT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_login_sessions (
session_id VARCHAR(128) PRIMARY KEY, -- Unique session token
user_id INT NOT NULL, -- Reference to logged-in user
login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- When user logged in
expires_at TIMESTAMP NOT NULL, -- Session expiration time
is_active TINYINT(1) DEFAULT 1, -- Session active status
logout_time TIMESTAMP NULL, -- When user logged out
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- [Rest of schema tables...]
*/
?>
