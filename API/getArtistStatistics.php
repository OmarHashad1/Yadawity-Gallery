<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('ETag: ' . md5(microtime()));

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

// Use correct database connection variable
$conn = $db;

// Function to validate user cookie and get user ID
function validateUserCookie($conn) {
    if (!isset($_COOKIE['user_login'])) {
        return null;
    }
    
    $cookieValue = $_COOKIE['user_login'];
    
    // Extract user ID from cookie (format: user_id_hash)
    $parts = explode('_', $cookieValue, 2);
    if (count($parts) !== 2) {
        return null;
    }
    
    $user_id = (int)$parts[0];
    $provided_hash = $parts[1];
    
    if ($user_id <= 0) {
        return null;
    }
    
    // Get user data
    $stmt = $conn->prepare("SELECT email, is_active FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return null;
    }
    
    $user = $result->fetch_assoc();
    
    // Check if user is active
    if (!$user['is_active']) {
        return null;
    }
    
    // Get all active login sessions for this user
    $session_stmt = $conn->prepare("
        SELECT login_time 
        FROM user_login_sessions 
        WHERE user_id = ? AND is_active = 1 
        ORDER BY login_time DESC
    ");
    $session_stmt->bind_param("i", $user_id);
    $session_stmt->execute();
    $session_result = $session_stmt->get_result();
    
    // Try to validate the hash against any active session
    while ($session = $session_result->fetch_assoc()) {
        $expected_hash = hash('sha256', $user['email'] . $session['login_time'] . 'yadawity_salt');
        if ($provided_hash === $expected_hash) {
            return $user_id; // Hash matches one of the active sessions
        }
    }
    
    // No matching hash found
    return null;
}

// Function to get authenticated user ID
function getAuthenticatedUserId($conn) {
    // COMPLETELY destroy any existing session data to prevent contamination
    $_SESSION = array();
    
    // Destroy the session cookie if it exists
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy session completely
    session_destroy();
    
    // Start fresh session to ensure no contamination
    session_start();
    
    // ONLY use cookie authentication - no session fallbacks
    $cookie_user_id = validateUserCookie($conn);
    if ($cookie_user_id) {
        error_log("getAuthenticatedUserId: Using cookie authentication for user_id = " . $cookie_user_id);
        return $cookie_user_id;
    }
    
    error_log("getAuthenticatedUserId: No valid authentication found");
    return null;
}

function getArtistProducts($conn, $artist_id) {
    try {
        // Debug logging
        error_log("getArtistProducts: Using artist_id = " . $artist_id);
        
        $query = "
            SELECT 
                a.artwork_id,
                a.title,
                a.description,
                a.price,
                a.dimensions,
                a.artwork_image,
                a.type,
                a.is_available,
                a.on_auction,
                a.created_at,
                auc.id as auction_id,
                COUNT(DISTINCT oi.id) as cart_count,
                COUNT(DISTINCT w.id) as wishlist_count,
                COUNT(DISTINCT CASE WHEN o.status IN ('paid', 'shipped', 'delivered') THEN o.id END) as sales_count,
                COALESCE(SUM(CASE WHEN o.status IN ('paid', 'shipped', 'delivered') THEN oi.price * oi.quantity END), 0) as total_earnings
            FROM artworks a
            LEFT JOIN auctions auc ON a.artwork_id = auc.product_id AND a.artist_id = auc.artist_id
            LEFT JOIN cart c ON a.artwork_id = c.artwork_id
            LEFT JOIN wishlists w ON a.artwork_id = w.artwork_id
            LEFT JOIN order_items oi ON a.artwork_id = oi.artwork_id
            LEFT JOIN orders o ON oi.order_id = o.id
            WHERE a.artist_id = ?
            GROUP BY a.artwork_id
            ORDER BY a.created_at DESC
        ";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $artist_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $status = 'Draft';
            if ($row['on_auction']) {
                $status = 'On Auction';
            } elseif ($row['is_available']) {
                $status = 'Available';
            } else {
                $status = 'Sold';
            }
            
            $products[] = [
                'id' => $row['artwork_id'],
                'title' => $row['title'],
                'description' => $row['description'],
                'price' => (float)$row['price'],
                'dimensions' => $row['dimensions'],
                'image' => $row['artwork_image'] ? '/uploads/artworks/' . $row['artwork_image'] : '/image/placeholder-artwork.jpg',
                'type' => $row['type'],
                'status' => $status,
                'auction_id' => $row['auction_id'], // Include auction_id for items that are on auction
                'on_auction' => (bool)$row['on_auction'], // Include on_auction flag
                'cart_count' => (int)$row['cart_count'],
                'wishlist_count' => (int)$row['wishlist_count'],
                'sales_count' => (int)$row['sales_count'],
                'total_earnings' => (float)$row['total_earnings'],
                'created_at' => $row['created_at']
            ];
        }
        
        $stmt->close();
        
        // Debug logging
        error_log("getArtistProducts: Found " . count($products) . " products for artist_id " . $artist_id);
        
        return $products;
        
    } catch (Exception $e) {
        throw new Exception("Error fetching artist products: " . $e->getMessage());
    }
}

function getArtistGalleries($conn, $artist_id) {
    try {
        $query = "
            SELECT 
                g.gallery_id as id,
                g.title,
                g.description,
                g.gallery_type,
                g.address,
                g.city,
                g.phone,
                g.price,
                g.duration,
                g.is_active,
                g.created_at,
                g.img,
                0 as artwork_count,
                0 as enrolled_count,
                0 as cart_count,
                0 as wishlist_count
            FROM galleries g
            WHERE g.artist_id = ?
            ORDER BY g.created_at DESC
        ";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $artist_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $virtual_galleries = [];
        $local_galleries = [];
        
        while ($row = $result->fetch_assoc()) {
            // Handle gallery image path - check if it already contains the full path
            $gallery_image = '';
            if ($row['img']) {
                if (strpos($row['img'], 'uploads/galleries/') === 0) {
                    // Already contains full path, just add leading slash
                    $gallery_image = '/' . $row['img'];
                } else {
                    // Just filename, prepend the uploads/galleries/ path
                    $gallery_image = '/uploads/galleries/' . $row['img'];
                }
            } else {
                $gallery_image = '/image/default-gallery.jpg';
            }
            
            $gallery_data = [
                'id' => $row['id'],
                'title' => $row['title'],
                'description' => $row['description'],
                'price' => (float)$row['price'],
                'duration' => (int)$row['duration'],
                'artwork_count' => (int)$row['artwork_count'],
                'enrolled_count' => (int)$row['enrolled_count'],
                'cart_count' => (int)$row['cart_count'],
                'wishlist_count' => (int)$row['wishlist_count'],
                'created_at' => $row['created_at'],
                'image' => $gallery_image
            ];
            
            if ($row['gallery_type'] === 'virtual') {
                $gallery_data['status'] = $row['is_active'] ? 'Published' : 'Draft';
                $virtual_galleries[] = $gallery_data;
            } else { // physical gallery
                $gallery_data['address'] = $row['address'];
                $gallery_data['city'] = $row['city'];
                $gallery_data['phone'] = $row['phone'];
                $gallery_data['status'] = $row['is_active'] ? 'Approved' : 'Pending Approval';
                $local_galleries[] = $gallery_data;
            }
        }
        
        $stmt->close();
        
        return [
            'virtual_galleries' => $virtual_galleries,
            'local_galleries' => $local_galleries
        ];
        
    } catch (Exception $e) {
        throw new Exception("Error fetching artist galleries: " . $e->getMessage());
    }
}

function getArtistSummaryStats($conn, $artist_id) {
    try {
        // Get total products
        $products_query = "SELECT COUNT(*) as total_products FROM artworks WHERE artist_id = ?";
        $stmt = $conn->prepare($products_query);
        $stmt->bind_param("i", $artist_id);
        $stmt->execute();
        $total_products = $stmt->get_result()->fetch_assoc()['total_products'];
        $stmt->close();
        
        // Get total galleries
        $galleries_query = "SELECT COUNT(*) as total_galleries FROM galleries WHERE artist_id = ?";
        $stmt = $conn->prepare($galleries_query);
        $stmt->bind_param("i", $artist_id);
        $stmt->execute();
        $total_galleries = $stmt->get_result()->fetch_assoc()['total_galleries'];
        $stmt->close();
        
        // Get total sales
        $sales_query = "
            SELECT 
                COUNT(DISTINCT o.id) as total_sales,
                COALESCE(SUM(oi.price * oi.quantity), 0) as total_revenue
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN artworks a ON oi.artwork_id = a.artwork_id
            WHERE a.artist_id = ? AND o.status IN ('paid', 'shipped', 'delivered')
        ";
        $stmt = $conn->prepare($sales_query);
        $stmt->bind_param("i", $artist_id);
        $stmt->execute();
        $sales_data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        // Get total cart and wishlist counts
        $engagement_query = "
            SELECT 
                COUNT(DISTINCT w.id) as total_wishlist,
                COUNT(DISTINCT c.id) as total_cart
            FROM artworks a
            LEFT JOIN wishlists w ON a.artwork_id = w.artwork_id
            LEFT JOIN cart c ON a.artwork_id = c.artwork_id
            WHERE a.artist_id = ?
        ";
        $stmt = $conn->prepare($engagement_query);
        $stmt->bind_param("i", $artist_id);
        $stmt->execute();
        $engagement_data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        return [
            'total_products' => (int)$total_products,
            'total_galleries' => (int)$total_galleries,
            'total_sales' => (int)$sales_data['total_sales'],
            'total_revenue' => (float)$sales_data['total_revenue'],
            'total_wishlist' => (int)$engagement_data['total_wishlist'],
            'total_cart' => (int)$engagement_data['total_cart']
        ];
        
    } catch (Exception $e) {
        throw new Exception("Error fetching artist summary stats: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Check if user_id is provided directly in the URL parameter
        if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
            $artist_id = (int)$_GET['user_id'];
            error_log("Using provided user_id: " . $artist_id);
            
            // Validate that this user exists and is active
            $user_check_stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? AND is_active = 1");
            $user_check_stmt->bind_param("i", $artist_id);
            $user_check_stmt->execute();
            $user_check_result = $user_check_stmt->get_result();
            
            if ($user_check_result->num_rows === 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'User not found or inactive.',
                    'error_code' => 'USER_NOT_FOUND'
                ]);
                exit;
            }
        } else {
            // Fall back to cookie validation
            $artist_id = getAuthenticatedUserId($conn);
            
            // Debug logging
            error_log("getArtistStatistics: Authenticated artist_id = " . ($artist_id ?? 'null'));
            if (isset($_COOKIE['user_login'])) {
                error_log("getArtistStatistics: Cookie present = " . substr($_COOKIE['user_login'], 0, 10) . "...");
            }
            
            if (!$artist_id) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'User not authenticated. Please log in.',
                    'error_code' => 'AUTHENTICATION_REQUIRED'
                ]);
                exit;
            }
        }
        
        // Validate database connection
        if (!isset($conn) || $conn->connect_error) {
            throw new Exception("Database connection failed");
        }
        
        // Get artist info
        $artist_info_query = "SELECT first_name, last_name, email FROM users WHERE user_id = ? AND is_active = 1";
        $artist_stmt = $conn->prepare($artist_info_query);
        $artist_stmt->bind_param("i", $artist_id);
        $artist_stmt->execute();
        $artist_result = $artist_stmt->get_result();
        
        if ($artist_result->num_rows === 0) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Artist not found or inactive.',
                'error_code' => 'ARTIST_NOT_FOUND'
            ]);
            exit;
        }
        
        $artist_info = $artist_result->fetch_assoc();
        
        // Get all data
        $products = getArtistProducts($conn, $artist_id);
        $galleries = getArtistGalleries($conn, $artist_id);
        $summary_stats = getArtistSummaryStats($conn, $artist_id);
        
        $response = [
            'success' => true,
            'message' => 'Artist statistics retrieved successfully',
            'data' => [
                'artist_id' => $artist_id,
                'artist_info' => $artist_info,
                'summary' => $summary_stats,
                'products' => $products,
                'virtual_galleries' => $galleries['virtual_galleries'],
                'local_galleries' => $galleries['local_galleries']
            ]
        ];
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        error_log("Artist Statistics API Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error retrieving artist statistics: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

$conn->close();
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
*/

?>
