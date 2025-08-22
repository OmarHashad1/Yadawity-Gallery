<?php
require_once 'db.php';

// Set content type to JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

function sendResponse($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}


function validateUserAuthentication($db) {
    $user_id = null;
    
    // First check for user_login cookie (primary method from login.php)
    if (isset($_COOKIE['user_login'])) {
        $user_id = validateUserLoginCookie($db);
    }
    // Fallback: Check for session_id cookie (if still used elsewhere)
    elseif (isset($_COOKIE['session_id'])) {
        $user_id = validateSessionCookie($db);
    }
    // Fallback: Check for simple user_id cookie (legacy support)
    elseif (isset($_COOKIE['user_id'])) {
        $user_id = validateUserIdCookie($db);
    }
    // No valid authentication found
    else {
        throw new Exception('No session found. Please log in.');
    }
    
    return $user_id;
}


function validateUserLoginCookie($db) {
    $cookie_parts = explode('_', $_COOKIE['user_login'], 2);
    if (count($cookie_parts) !== 2) {
        throw new Exception('Invalid cookie format. Please log in again.');
    }
    
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
    
    if (!$stmt->execute()) {
        throw new Exception("Database query failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        $stmt->close();
        throw new Exception('User session not found or expired. Please log in again.');
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Verify cookie hash matches expected pattern
    $expected_hash = hash('sha256', $user['email'] . $user['login_time'] . 'yadawity_salt');
    
    if (!hash_equals($expected_hash, $cookie_hash)) {
        throw new Exception('Invalid session. Please log in again.');
    }
    
    return $user_id;
}

function validateSessionCookie($db) {
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
    
    if (!$stmt->execute()) {
        throw new Exception("Database query failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $session = $result->fetch_assoc();
    $stmt->close();
    
    if (!$session) {
        throw new Exception('Invalid or expired session. Please log in again.');
    }
    
    return $session['user_id'];
}


function validateUserIdCookie($db) {
    $user_id = intval($_COOKIE['user_id']);
    
    if ($user_id <= 0) {
        throw new Exception('Invalid user ID in cookie.');
    }
    
    // Verify user exists and is active
    $stmt = $db->prepare("SELECT user_id FROM users WHERE user_id = ? AND is_active = 1");
    $stmt->bind_param("i", $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Database query failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        $stmt->close();
        throw new Exception('User not found or inactive. Please log in again.');
    }
    
    $stmt->close();
    return $user_id;
}

function getCartQuery() {
    return "SELECT 
                c.id as cart_id,
                c.quantity,
                c.added_date,
                a.artwork_id,
                a.title,
                a.description,
                a.price,
                a.artwork_image,
                ap.image_path as artwork_photo_filename,
                a.dimensions,
                a.year,
                a.material,
                a.type,
                a.is_available,
                a.on_auction,
                u.user_id as artist_id,
                u.first_name as artist_first_name,
                u.last_name as artist_last_name,
                u.profile_picture as artist_profile_picture,
                (c.quantity * a.price) as item_total
            FROM cart c
            INNER JOIN artworks a ON c.artwork_id = a.artwork_id
            LEFT JOIN artwork_photos ap ON a.artwork_id = ap.artwork_id AND (ap.is_primary = 1 OR ap.is_primary IS NULL)
            INNER JOIN users u ON a.artist_id = u.user_id
            WHERE c.user_id = ? 
            AND c.is_active = 1
            ORDER BY c.added_date DESC";
}

function formatCartItem($row) {
    return [
        'cart_id' => (int)$row['cart_id'],
        'quantity' => (int)$row['quantity'],
        'added_date' => $row['added_date'],
        'artwork' => [
            'artwork_id' => (int)$row['artwork_id'],
            'title' => $row['title'],
            'description' => $row['description'],
            'price' => (float)$row['price'],
            'artwork_image' => $row['artwork_image'],
            'artwork_photo_filename' => isset($row['artwork_photo_filename']) ? $row['artwork_photo_filename'] : null,
            // Prefer the artwork_photos filename when available, otherwise fallback to artworks.artwork_image
            'artwork_image_url' => (!empty($row['artwork_photo_filename']) ? './uploads/artworks/' . $row['artwork_photo_filename'] : (!empty($row['artwork_image']) ? './uploads/artworks/' . $row['artwork_image'] : null)),
            'dimensions' => $row['dimensions'],
            'year' => $row['year'] ? (int)$row['year'] : null,
            'material' => $row['material'],
            'type' => $row['type'],
            'is_available' => (bool)$row['is_available'],
            'on_auction' => (bool)$row['on_auction']
        ],
        'artist' => [
            'artist_id' => (int)$row['artist_id'],
            'name' => $row['artist_first_name'] . ' ' . $row['artist_last_name'],
            'first_name' => $row['artist_first_name'],
            'last_name' => $row['artist_last_name'],
            'profile_picture' => $row['artist_profile_picture'],
            'profile_picture_url' => $row['artist_profile_picture'] ? '../uploads/profiles/' . $row['artist_profile_picture'] : null
        ],
        'item_total' => (float)$row['item_total'],
        'status' => [
            'can_purchase' => (bool)$row['is_available'] && !(bool)$row['on_auction'],
            'availability_status' => (bool)$row['is_available'] ? 'available' : 'sold',
            'auction_status' => (bool)$row['on_auction'] ? 'on_auction' : 'not_on_auction'
        ]
    ];
}


function getCartItems($db, $user_id) {
    try {
        $sql = getCartQuery();
        $stmt = $db->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $db->error);
        }

        $stmt->bind_param("i", $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();

        $cart_items = [];
        $total_amount = 0;
        $total_items = 0;

        while ($row = $result->fetch_assoc()) {
            $cart_item = formatCartItem($row);
            $cart_items[] = $cart_item;
            
            $total_amount += $cart_item['item_total'];
            $total_items += $cart_item['quantity'];
        }

        $stmt->close();

        return [
            'user_id' => $user_id,
            'total_items' => $total_items,
            'total_amount' => round($total_amount, 2),
            'currency' => 'USD',
            'cart_items' => $cart_items,
            'cart_summary' => [
                'items_count' => count($cart_items),
                'total_quantity' => $total_items,
                'subtotal' => round($total_amount, 2),
                'estimated_tax' => round($total_amount * 0.08, 2), // 8% estimated tax
                'estimated_total' => round($total_amount * 1.08, 2)
            ]
        ];
        
    } catch (Exception $e) {
        throw new Exception("Error fetching cart items: " . $e->getMessage());
    }
}


function handleGetCart() {
    global $db;
    
    try {
        // Validate database connection
        if (!isset($db) || $db->connect_error) {
            throw new Exception("Database connection failed: " . ($db->connect_error ?? "Connection not established"));
        }
        
        // Authenticate user and get user ID
        $user_id = validateUserAuthentication($db);
        
        // Get cart items
        $cart_data = getCartItems($db, $user_id);
        
        // Send success response
        sendResponse(true, 'Cart retrieved successfully', $cart_data);
        
    } catch (Exception $e) {
        // Send error response
        sendResponse(false, 'An error occurred while retrieving cart: ' . $e->getMessage());
    } finally {
        // Close database connection if it exists
        if (isset($db) && !$db->connect_error) {
            $db->close();
        }
    }
}

handleGetCart();
?>