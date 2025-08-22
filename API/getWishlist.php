<?php

require_once "db.php";

// Set content type to JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Response function
function sendResponse($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Function to authenticate user and get user ID
function authenticateUser($db) {
    $user_id = null;
    
    // First check for user_login cookie (primary method from login.php)
    if (isset($_COOKIE['user_login'])) {
        $cookie_parts = explode('_', $_COOKIE['user_login'], 2);
        if (count($cookie_parts) === 2) {
            $user_id = intval($cookie_parts[0]);
            $cookie_hash = $cookie_parts[1];
            
            // Validate user_id is positive integer
            if ($user_id <= 0) {
                throw new Exception('Invalid user session. Please log in again.');
            }
            
            // Verify user exists and is active, and validate cookie hash
            $stmt = $db->prepare("SELECT u.user_id, u.email, s.login_time FROM users u 
                                 LEFT JOIN user_login_sessions s ON u.user_id = s.user_id 
                                 WHERE u.user_id = ? AND u.is_active = 1 
                                 AND s.is_active = 1 AND s.expires_at > NOW() 
                                 ORDER BY s.login_time DESC LIMIT 1");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Verify cookie hash matches expected pattern
                $expected_hash = hash('sha256', $user['email'] . $user['login_time'] . 'yadawity_salt');
                
                if (!hash_equals($expected_hash, $cookie_hash)) {
                    throw new Exception('Invalid session. Please log in again.');
                }
            } else {
                throw new Exception('User session not found or expired. Please log in again.');
            }
            $stmt->close();
        } else {
            throw new Exception('Invalid cookie format. Please log in again.');
        }
    }
    // Fallback: Check for session_id cookie (if still used elsewhere)
    elseif (isset($_COOKIE['session_id'])) {
        $session_id = $_COOKIE['session_id'];
        
        // Verify session in database
        $stmt = $db->prepare("
            SELECT user_id, expires_at, is_active
            FROM user_login_sessions 
            WHERE session_id = ? 
            AND is_active = 1 
            AND expires_at > NOW()
        ");
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $session = $result->fetch_assoc();
        
        if (!$session) {
            throw new Exception('Invalid or expired session. Please log in again.');
        }
        
        $user_id = $session['user_id'];
        $stmt->close();
    }
    // Fallback: Check for simple user_id cookie (legacy support)
    elseif (isset($_COOKIE['user_id'])) {
        $user_id = intval($_COOKIE['user_id']);
        
        if ($user_id <= 0) {
            throw new Exception('Invalid user ID in cookie.');
        }
        
        // Verify user exists and is active
        $stmt = $db->prepare("SELECT user_id FROM users WHERE user_id = ? AND is_active = 1");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows !== 1) {
            throw new Exception('User not found or inactive. Please log in again.');
        }
        $stmt->close();
    }
    // No valid authentication found
    else {
        throw new Exception('No session found. Please log in.');
    }
    
    return $user_id;
}

// Function to get wishlist items
function getWishlistItems($db, $user_id) {
    // Also left join artwork_photos to obtain a primary photo filename when available.
    $sql = "SELECT 
                w.id as wishlist_id,
                w.price_alert,
                w.created_at as added_to_wishlist,
                a.artwork_id,
                a.title,
                a.description,
                a.price,
                a.dimensions,
                a.year,
                a.material,
                a.artwork_image,
                ap.image_path as artwork_photo_filename,
                a.type,
                a.is_available,
                a.on_auction,
                u.user_id as artist_id,
                u.first_name as artist_first_name,
                u.last_name as artist_last_name,
                u.profile_picture as artist_profile_picture
            FROM wishlists w
            INNER JOIN artworks a ON w.artwork_id = a.artwork_id
            INNER JOIN users u ON a.artist_id = u.user_id
            LEFT JOIN artwork_photos ap ON a.artwork_id = ap.artwork_id AND (ap.is_primary = 1 OR ap.is_primary IS NULL)
            WHERE w.user_id = ? AND w.is_active = 1
            ORDER BY w.created_at DESC";

    $stmt = $db->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $db->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $wishlist_items = [];
    
    while ($row = $result->fetch_assoc()) {
        // compute a usable artwork image URL; prefer artwork_photos filename, fallback to artworks.artwork_image
        $image_url = null;
        if (!empty($row['artwork_photo_filename'])) {
            $image_url = './uploads/artworks/' . $row['artwork_photo_filename'];
        } else if (!empty($row['artwork_image'])) {
            $image_url = './uploads/artworks/' . $row['artwork_image'];
        } else {
            $image_url = null;
        }

        $wishlist_items[] = [
            'wishlist_id' => (int)$row['wishlist_id'],
            'price_alert' => $row['price_alert'] ? (float)$row['price_alert'] : null,
            'added_to_wishlist' => $row['added_to_wishlist'],
            'artwork' => [
                'artwork_id' => (int)$row['artwork_id'],
                'title' => $row['title'],
                'description' => $row['description'],
                'price' => (float)$row['price'],
                'dimensions' => $row['dimensions'],
                'year' => $row['year'],
                'material' => $row['material'],
                'artwork_image' => $row['artwork_image'],
                'artwork_image_url' => $image_url,
                'artwork_photo_filename' => $row['artwork_photo_filename'],
                'type' => $row['type'],
                'is_available' => (bool)$row['is_available'],
                'on_auction' => (bool)$row['on_auction']
            ],
            'artist' => [
                'artist_id' => (int)$row['artist_id'],
                'name' => $row['artist_first_name'] . ' ' . $row['artist_last_name'],
                'first_name' => $row['artist_first_name'],
                'last_name' => $row['artist_last_name'],
                'profile_picture' => $row['artist_profile_picture']
            ]
        ];
    }

    $stmt->close();
    return $wishlist_items;
}

// Main execution
try {
    // Authenticate user
    $user_id = authenticateUser($db);
    
    // Get wishlist items
    $wishlist_items = getWishlistItems($db, $user_id);
    
    // Return successful response
    sendResponse(true, 'Wishlist retrieved successfully', [
        'user_id' => $user_id,
        'total_items' => count($wishlist_items),
        'wishlist_items' => $wishlist_items
    ]);

} catch (Exception $e) {
    sendResponse(false, 'Error retrieving wishlist: ' . $e->getMessage());
} finally {
    if (isset($db)) {
        $db->close();
    }
}

?>