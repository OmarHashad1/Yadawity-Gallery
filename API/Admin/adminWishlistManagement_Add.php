<?php
include_once 'db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Only POST method allowed', null, 405);
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    $userId = $input['user_id'] ?? '';
    $artworkId = $input['artwork_id'] ?? '';
    $priceAlert = $input['price_alert'] ?? null;
    $isActive = $input['is_active'] ?? 1;
    
    // Validate required fields
    if (empty($userId) || empty($artworkId)) {
        sendResponse(false, 'user_id and artwork_id are required', null, 400);
    }
    
    // Validate user exists
    $userStmt = $pdo->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $userStmt->execute([$userId]);
    if (!$userStmt->fetch()) {
        sendResponse(false, 'User not found', null, 404);
    }
    
    // Validate artwork exists and is available
    $artworkStmt = $pdo->prepare("SELECT artwork_id, price, is_available FROM artworks WHERE artwork_id = ?");
    $artworkStmt->execute([$artworkId]);
    $artwork = $artworkStmt->fetch();
    if (!$artwork) {
        sendResponse(false, 'Artwork not found', null, 404);
    }
    
    if (!$artwork['is_available']) {
        sendResponse(false, 'Artwork is not available for wishlist', null, 400);
    }
    
    // Check if already in wishlist
    $existingStmt = $pdo->prepare("SELECT id FROM wishlists WHERE user_id = ? AND artwork_id = ?");
    $existingStmt->execute([$userId, $artworkId]);
    if ($existingStmt->fetch()) {
        sendResponse(false, 'Artwork already in wishlist', null, 409);
    }
    
    // Validate price alert if provided
    if ($priceAlert !== null) {
        $priceAlert = (float)$priceAlert;
        if ($priceAlert <= 0) {
            sendResponse(false, 'Price alert must be greater than 0', null, 400);
        }
        if ($priceAlert > $artwork['price']) {
            sendResponse(false, 'Price alert cannot be higher than current artwork price', null, 400);
        }
    }
    
    // Add to wishlist
    $insertStmt = $pdo->prepare("
        INSERT INTO wishlists (user_id, artwork_id, price_alert, is_active, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    $insertStmt->execute([$userId, $artworkId, $priceAlert, $isActive]);
    $wishlistId = $pdo->lastInsertId();
    
    // Get the created wishlist item with details
    $selectStmt = $pdo->prepare("
        SELECT w.id as wishlist_id, w.price_alert, w.is_active, w.created_at,
               u.first_name, u.last_name, u.email,
               a.artwork_id, a.title as artwork_title, a.price, a.type, a.artwork_image,
               artist.first_name as artist_first_name, artist.last_name as artist_last_name
        FROM wishlists w
        JOIN users u ON w.user_id = u.user_id
        JOIN artworks a ON w.artwork_id = a.artwork_id
        JOIN users artist ON a.artist_id = artist.user_id
        WHERE w.id = ?
    ");
    $selectStmt->execute([$wishlistId]);
    $wishlistItem = $selectStmt->fetch();
    
    sendResponse(true, 'Wishlist item added successfully', [
        'wishlist_item' => $wishlistItem,
        'wishlist_id' => $wishlistId
    ], 201);

} catch (Exception $e) {
    sendResponse(false, 'Error adding wishlist item: ' . $e->getMessage(), null, 500);
}
?>
