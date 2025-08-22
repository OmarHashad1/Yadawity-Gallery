<?php

/**
 * Send API response using cURL
 * @param string $url - The API endpoint URL
 * @param array $data - Data to send in the request
 * @param string $method - HTTP method (GET, POST, PUT, DELETE)
 * @param array $headers - Additional headers
 * @return array - Response from the API
 */
function sendApiResponse($url, $data = [], $method = 'POST', $headers = []) {
    $curl = curl_init();
    
    // Default headers
    $defaultHeaders = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    // Merge with custom headers
    $headers = array_merge($defaultHeaders, $headers);
    
    // Basic cURL options
    $curlOptions = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3
    ];
    
    // Set method-specific options
    switch (strtoupper($method)) {
        case 'POST':
            $curlOptions[CURLOPT_POST] = true;
            if (!empty($data)) {
                $curlOptions[CURLOPT_POSTFIELDS] = json_encode($data);
            }
            break;
            
        case 'PUT':
            $curlOptions[CURLOPT_CUSTOMREQUEST] = 'PUT';
            if (!empty($data)) {
                $curlOptions[CURLOPT_POSTFIELDS] = json_encode($data);
            }
            break;
            
        case 'DELETE':
            $curlOptions[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            break;
            
        case 'GET':
        default:
            if (!empty($data)) {
                $url .= '?' . http_build_query($data);
                $curlOptions[CURLOPT_URL] = $url;
            }
            break;
    }
    
    curl_setopt_array($curl, $curlOptions);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    
    curl_close($curl);
    
    // Handle cURL errors
    if ($error) {
        return [
            'success' => false,
            'message' => 'cURL Error: ' . $error,
            'http_code' => 0,
            'data' => null
        ];
    }
    
    // Decode JSON response
    $decodedResponse = json_decode($response, true);
    
    return [
        'success' => $httpCode >= 200 && $httpCode < 300,
        'message' => $httpCode >= 200 && $httpCode < 300 ? 'Request successful' : 'Request failed',
        'http_code' => $httpCode,
        'data' => $decodedResponse,
        'raw_response' => $response
    ];
}

/**
 * Send JSON response to client
 * @param bool $success - Success status
 * @param string $message - Response message
 * @param array $data - Additional data to include
 * @param int $httpCode - HTTP status code
 */
function sendJsonResponse($success, $message, $data = [], $httpCode = 200) {
    http_response_code($httpCode);
    header('Content-Type: application/json');
    
    $response = [
        'success' => $success,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => $data
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

/**
 * Log API activities
 * @param string $action - The action being performed
 * @param array $data - Data related to the action
 * @param bool $success - Whether the action was successful
 */
function logApiActivity($action, $data = [], $success = true) {
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'action' => $action,
        'success' => $success,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'data' => $data
    ];
    
    error_log("API Activity: " . json_encode($logEntry));
}

// AUTHENTICATION ENDPOINT FOR AJAX REQUESTS
if (isset($_GET['action']) && $_GET['action'] === 'authenticate') {
    require_once "db.php";
    session_start();
    
    // Check if database connection is successful
    if (!isset($db) || $db->connect_error) {
        sendJsonResponse(false, 'Database connection failed. Please try again later.', [], 500);
    }
    
    logApiActivity('login_attempt', ['email' => $_POST['email'] ?? '']);

    try {
        // Get email and password from POST request
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validate input
        if (empty($email) || empty($password)) {
            logApiActivity('login_failed', ['reason' => 'missing_credentials'], false);
            sendJsonResponse(false, 'Email and password are required.', [], 400);
        }

        // Check if user exists in users table
        $stmt = $db->prepare("SELECT user_id, email, password, first_name, last_name, user_type FROM users WHERE email = ? AND is_active = 1");
        if (!$stmt) {
            logApiActivity('login_failed', ['reason' => 'database_error'], false);
            sendJsonResponse(false, 'Database error occurred.', [], 500);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password (supports both bcrypt and md5 for backward compatibility)
            if (password_verify($password, $user['password']) || md5($password) === $user['password']) {
                
                // Create session variables (2 weeks expiry)
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['login_time'] = time();

                // Generate unique session token
                $session_token = bin2hex(random_bytes(32));
                $_SESSION['session_token'] = $session_token;

                // Set cookie (2 weeks expiry = 14 days)
                $login_time = date('Y-m-d H:i:s');
                $expires_at = date('Y-m-d H:i:s', time() + (14 * 24 * 60 * 60)); // 2 weeks
                $cookie_hash = hash('sha256', $user['email'] . $login_time . 'yadawity_salt');
                $cookie_value = $user['user_id'] . '_' . $cookie_hash;
                
                setcookie('user_login', $cookie_value, time() + (14 * 24 * 60 * 60), '/', '', false, true);

                // Create session record in user_login_sessions table
                $session_stmt = $db->prepare("INSERT INTO user_login_sessions (session_id, user_id, login_time, expires_at, is_active) VALUES (?, ?, ?, ?, 1)");
                if ($session_stmt) {
                    $session_stmt->bind_param("siss", $session_token, $user['user_id'], $login_time, $expires_at);
                    
                    if ($session_stmt->execute()) {
                        // Success response for SweetAlert
                        $responseData = [
                            'user_name' => $user['first_name'] . ' ' . $user['last_name'],
                            'user_type' => $user['user_type'],
                            'redirect_url' => ($user['user_type'] === 'admin') ? 'admin-dashboard.html' : 'index.php'
                        ];
                        
                        logApiActivity('login_success', ['user_id' => $user['user_id'], 'user_type' => $user['user_type']], true);
                        sendJsonResponse(true, 'Welcome back, ' . $user['first_name'] . ' ' . $user['last_name'] . '!', $responseData, 200);
                    } else {
                        logApiActivity('login_failed', ['reason' => 'session_creation_failed', 'user_id' => $user['user_id']], false);
                        sendJsonResponse(false, 'Failed to create login session. Please try again.', [], 500);
                    }
                    $session_stmt->close();
                } else {
                    logApiActivity('login_failed', ['reason' => 'session_prepare_failed'], false);
                    sendJsonResponse(false, 'Failed to prepare session query.', [], 500);
                }
                
            } else {
                // Invalid password
                logApiActivity('login_failed', ['reason' => 'invalid_password', 'email' => $email], false);
                sendJsonResponse(false, 'Invalid email or password. Please check your credentials.', [], 401);
            }
        } else {
            // User not found
            logApiActivity('login_failed', ['reason' => 'user_not_found', 'email' => $email], false);
            sendJsonResponse(false, 'Invalid email or password. Please check your credentials.', [], 401);
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Login API Error: " . $e->getMessage());
        logApiActivity('login_failed', ['reason' => 'exception', 'error' => $e->getMessage()], false);
        sendJsonResponse(false, 'An error occurred during login. Please try again.', [], 500);
    } finally {
        if (isset($db) && $db instanceof mysqli) {
            $db->close();
        }
    }
}

/*
/*
-- Artwork Marketplace Database Schema

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

-- Course table
CREATE TABLE courses (
course_id INT AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(255) NOT NULL,
rate DECIMAL(3,2) DEFAULT 0.00 COMMENT 'Course rating out of 5',
artist_id INT NOT NULL,
duration_date INT NOT NULL COMMENT 'Duration in months',
description TEXT,
requirement TEXT,
difficulty ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
course_type ENUM('online', 'offline', 'hybrid') NOT NULL,
price DECIMAL(10,2) NOT NULL,
thumbnail VARCHAR(500),
is_published TINYINT(1) DEFAULT 0,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (artist_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Course enrollment table
CREATE TABLE course_enrollments (
id INT AUTO_INCREMENT PRIMARY KEY,
course_id INT NOT NULL,
user_id INT NOT NULL,
is_payed TINYINT(1) DEFAULT 0,
is_active TINYINT(1) DEFAULT 1,
enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE,
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
UNIQUE KEY unique_enrollment (course_id, user_id)
);

CREATE TABLE galleries (
    gallery_id INT AUTO_INCREMENT PRIMARY KEY,
    artist_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    gallery_type ENUM('virtual', 'physical') NOT NULL,
    
    -- Virtual gallery fields
    price DECIMAL(10,2) NULL,              -- Price for virtual access
    
    -- Physical gallery fields  
    address TEXT NULL,
    city VARCHAR(100) NULL,
    phone VARCHAR(20) NULL,
    
    start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    duration INT NOT NULL, --in minutes               
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Artwork table
CREATE TABLE artworks (
artwork_id INT AUTO_INCREMENT PRIMARY KEY,
artist_id INT NOT NULL,
title VARCHAR(255) NOT NULL,
description TEXT,
price DECIMAL(10,2) NOT NULL,
dimensions VARCHAR(100),
year YEAR,
material VARCHAR(255),
artwork_image VARCHAR(500),
type ENUM('painting', 'sculpture', 'photography', 'digital', 'mixed_media', 'other') NOT NULL,
is_available TINYINT(1) DEFAULT 1,
on_auction TINYINT(1) DEFAULT 0,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (artist_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Update gallery_items to reference artworks
ALTER TABLE gallery_items
ADD FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE;

-- Orders table
CREATE TABLE orders (
id INT AUTO_INCREMENT PRIMARY KEY,
buyer_id INT NOT NULL,
total_amount DECIMAL(10,2) NOT NULL,
status ENUM('pending', 'paid', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
shipping_address TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (buyer_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE order_items (
id INT AUTO_INCREMENT PRIMARY KEY,
order_id INT NOT NULL,
artwork_id INT NOT NULL,
price DECIMAL(10,2) NOT NULL,
quantity INT DEFAULT 1,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE
);

-- Artist reviews table
CREATE TABLE artist_reviews (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
artist_id INT NOT NULL,
artwork_id INT,
rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
feedback TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
FOREIGN KEY (artist_id) REFERENCES users(user_id) ON DELETE CASCADE,
FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE SET NULL
);

-- Subscribers table (for artist subscription plans)
CREATE TABLE subscribers (
id INT AUTO_INCREMENT PRIMARY KEY,
artist_id INT NOT NULL,
plan ENUM('basic', 'premium', 'pro') NOT NULL,
duration INT NOT NULL COMMENT 'Duration in months',
start_date DATE NOT NULL,
end_date DATE NOT NULL,
is_active TINYINT(1) DEFAULT 1,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (artist_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Exam table
CREATE TABLE exams (
exam_id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
need_doctor TINYINT(1) DEFAULT 0,
draw_img VARCHAR(500),
exam_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
results TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Sessions table (for user sessions, course sessions, or gallery sessions)
CREATE TABLE sessions (
session_id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
session_type ENUM('user_login', 'course', 'gallery_visit', 'exam') NOT NULL,
reference_id INT COMMENT 'ID of course, gallery, or exam depending on session_type',
start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
end_time TIMESTAMP NULL,
duration INT COMMENT 'Session duration in minutes',
ip_address VARCHAR(45),
user_agent TEXT,
is_active TINYINT(1) DEFAULT 1,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Auction table
CREATE TABLE auctions (
id INT AUTO_INCREMENT PRIMARY KEY,
product_id INT NOT NULL,
artist_id INT NOT NULL,
starting_bid DECIMAL(10,2) NOT NULL,
current_bid DECIMAL(10,2) DEFAULT 0.00,
start_time DATETIME NOT NULL,
end_time DATETIME NOT NULL,
status ENUM('active', 'ended', 'cancelled') DEFAULT 'active',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (product_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE,
FOREIGN KEY (artist_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Auction bids table (to track all bids placed on auctions)
CREATE TABLE auction_bids (
id INT AUTO_INCREMENT PRIMARY KEY,
auction_id INT NOT NULL,
user_id INT NOT NULL,
bid_amount DECIMAL(10,2) NOT NULL,
bid_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
is_winning_bid TINYINT(1) DEFAULT 0,
FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE,
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
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

CREATE TABLE cart (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL, -- Links to the user
artwork_id INT NOT NULL, -- Links to the artwork
quantity INT DEFAULT 1, -- Quantity (usually 1 for unique artworks)
added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- When added to cart
is_active TINYINT(1) DEFAULT 1, -- Active/inactive status
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE,
UNIQUE KEY unique_cart_item (user_id, artwork_id) -- Prevents duplicates
);

CREATE TABLE artwork_photos (
    photo_id INT AUTO_INCREMENT PRIMARY KEY,
    artwork_id INT NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE
);
*/



?>