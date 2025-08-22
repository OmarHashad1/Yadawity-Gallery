<?php
require_once 'db.php';
require_once 'getCart.php'; // reuse getCart helper functions

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

function sendResponseSimple($success, $message, $data = null) {
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

try {
    // Read JSON body
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['artwork_id'])) {
        sendResponseSimple(false, 'Missing artwork_id');
    }

    $artwork_id = (int)$input['artwork_id'];
    $quantity = isset($input['quantity']) ? max(1, (int)$input['quantity']) : 1;

    // Authenticate user using functions from getCart.php (validateUserAuthentication)
    if (!function_exists('validateUserAuthentication')) {
        sendResponseSimple(false, 'Server misconfiguration: auth helper missing');
    }

    $user_id = validateUserAuthentication($db);

    // Check artwork exists and is available
    $stmt = $db->prepare("SELECT artwork_id, is_available, on_auction FROM artworks WHERE artwork_id = ? LIMIT 1");
    $stmt->bind_param('i', $artwork_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $art = $res->fetch_assoc();
    $stmt->close();

    if (!$art) {
        sendResponseSimple(false, 'Artwork not found');
    }

    // If artwork is on auction, disallow adding to cart
    if ((int)$art['on_auction'] === 1) {
        sendResponseSimple(false, 'Artwork currently on auction and cannot be added to cart');
    }

    // Insert or update cart (respect UNIQUE user_id + artwork_id)
    $stmt = $db->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND artwork_id = ? AND is_active = 1 LIMIT 1");
    $stmt->bind_param('ii', $user_id, $artwork_id);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existing) {
        $newQty = $existing['quantity'] + $quantity;
        $stmt = $db->prepare("UPDATE cart SET quantity = ?, added_date = NOW() WHERE id = ?");
        $stmt->bind_param('ii', $newQty, $existing['id']);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $db->prepare("INSERT INTO cart (user_id, artwork_id, quantity, added_date, is_active) VALUES (?, ?, ?, NOW(), 1)");
        $stmt->bind_param('iii', $user_id, $artwork_id, $quantity);
        $stmt->execute();
        $stmt->close();
    }

    // Return updated cart using getCartItems function from getCart.php
    if (!function_exists('getCartItems')) {
        sendResponseSimple(true, 'Added to cart', null);
    }

    $cart_data = getCartItems($db, $user_id);
    sendResponseSimple(true, 'Artwork added to cart', $cart_data);

} catch (Exception $e) {
    sendResponseSimple(false, 'Error adding to cart: ' . $e->getMessage());
} finally {
    if (isset($db) && !$db->connect_error) {
        $db->close();
    }
}

?>
