<?php
include_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, 'Invalid JSON input', null, 400);
    }
    
    // Validate required fields
    $error = validateRequired($input, ['cart_id']);
    if ($error) {
        sendResponse(false, $error, null, 400);
    }
    
    $cartId = $input['cart_id'];
    $hardDelete = $input['hard_delete'] ?? false;
    
    // Check if cart item exists
    $checkStmt = $pdo->prepare("
        SELECT c.id, u.first_name, u.last_name, a.title as artwork_title
        FROM cart c
        JOIN users u ON c.user_id = u.user_id
        JOIN artworks a ON c.artwork_id = a.artwork_id
        WHERE c.id = ?
    ");
    $checkStmt->execute([$cartId]);
    $cartItem = $checkStmt->fetch();
    
    if (!$cartItem) {
        sendResponse(false, 'Cart item not found', null, 404);
    }
    
    if ($hardDelete) {
        // Hard delete - completely remove cart item
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
        $result = $stmt->execute([$cartId]);
        
        if ($result) {
            sendResponse(true, "Cart item '{$cartItem['artwork_title']}' for {$cartItem['first_name']} {$cartItem['last_name']} permanently deleted");
        } else {
            sendResponse(false, 'Failed to delete cart item', null, 500);
        }
        
    } else {
        // Soft delete - mark as inactive
        $stmt = $pdo->prepare("UPDATE cart SET is_active = 0 WHERE id = ?");
        $result = $stmt->execute([$cartId]);
        
        if ($result) {
            sendResponse(true, "Cart item '{$cartItem['artwork_title']}' marked as inactive");
        } else {
            sendResponse(false, 'Failed to deactivate cart item', null, 500);
        }
    }

} catch (Exception $e) {
    sendResponse(false, 'Error deleting cart item: ' . $e->getMessage(), null, 500);
}
?>
