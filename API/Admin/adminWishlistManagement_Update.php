<?php
include_once 'db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Only PUT/POST method allowed', null, 405);
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    $wishlistId = $input['wishlist_id'] ?? '';
    $priceAlert = $input['price_alert'] ?? null;
    $isActive = $input['is_active'] ?? null;
    
    // Validate required fields
    if (empty($wishlistId)) {
        sendResponse(false, 'wishlist_id is required', null, 400);
    }
    
    // Check if wishlist item exists
    $existingStmt = $pdo->prepare("
        SELECT w.*, a.price as artwork_price 
        FROM wishlists w 
        JOIN artworks a ON w.artwork_id = a.artwork_id 
        WHERE w.id = ?
    ");
    $existingStmt->execute([$wishlistId]);
    $existing = $existingStmt->fetch();
    
    if (!$existing) {
        sendResponse(false, 'Wishlist item not found', null, 404);
    }
    
    // Build update query dynamically
    $updateFields = [];
    $updateParams = [];
    
    if ($priceAlert !== null) {
        if ($priceAlert == '') {
            // Remove price alert
            $updateFields[] = "price_alert = NULL";
        } else {
            $priceAlert = (float)$priceAlert;
            if ($priceAlert <= 0) {
                sendResponse(false, 'Price alert must be greater than 0', null, 400);
            }
            if ($priceAlert > $existing['artwork_price']) {
                sendResponse(false, 'Price alert cannot be higher than current artwork price', null, 400);
            }
            $updateFields[] = "price_alert = ?";
            $updateParams[] = $priceAlert;
        }
    }
    
    if ($isActive !== null) {
        $updateFields[] = "is_active = ?";
        $updateParams[] = $isActive;
    }
    
    if (empty($updateFields)) {
        sendResponse(false, 'No fields to update', null, 400);
    }
    
    $updateFields[] = "updated_at = NOW()";
    $updateParams[] = $wishlistId;
    
    $updateSql = "UPDATE wishlists SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute($updateParams);
    
    if ($updateStmt->rowCount() === 0) {
        sendResponse(false, 'No changes were made', null, 400);
    }
    
    // Get the updated wishlist item with details
    $selectStmt = $pdo->prepare("
        SELECT w.id as wishlist_id, w.price_alert, w.is_active, w.created_at, w.updated_at,
               u.first_name, u.last_name, u.email,
               a.artwork_id, a.title as artwork_title, a.price, a.type, a.artwork_image,
               artist.first_name as artist_first_name, artist.last_name as artist_last_name,
               CASE 
                   WHEN w.price_alert IS NOT NULL AND a.price <= w.price_alert THEN 'triggered'
                   WHEN w.price_alert IS NOT NULL THEN 'active'
                   ELSE 'no_alert'
               END as alert_status
        FROM wishlists w
        JOIN users u ON w.user_id = u.user_id
        JOIN artworks a ON w.artwork_id = a.artwork_id
        JOIN users artist ON a.artist_id = artist.user_id
        WHERE w.id = ?
    ");
    $selectStmt->execute([$wishlistId]);
    $updatedWishlistItem = $selectStmt->fetch();
    
    sendResponse(true, 'Wishlist item updated successfully', [
        'wishlist_item' => $updatedWishlistItem
    ]);

} catch (Exception $e) {
    sendResponse(false, 'Error updating wishlist item: ' . $e->getMessage(), null, 500);
}
?>
