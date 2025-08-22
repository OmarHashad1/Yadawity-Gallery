<?php
include_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, 'Invalid JSON input', null, 400);
    }
    
    // Validate required fields
    $error = validateRequired($input, ['order_id']);
    if ($error) {
        sendResponse(false, $error, null, 400);
    }
    
    $orderId = $input['order_id'];
    $hardDelete = $input['hard_delete'] ?? false;
    
    // Check if order exists
    $checkStmt = $pdo->prepare("
        SELECT o.id, o.status, o.total_amount, u.first_name, u.last_name 
        FROM orders o 
        JOIN users u ON o.buyer_id = u.user_id 
        WHERE o.id = ?
    ");
    $checkStmt->execute([$orderId]);
    $order = $checkStmt->fetch();
    
    if (!$order) {
        sendResponse(false, 'Order not found', null, 404);
    }
    
    // Business rules for deletion
    if ($order['status'] === 'delivered' && !$hardDelete) {
        sendResponse(false, 'Cannot delete delivered order. Use hard delete to force removal.', null, 400);
    }
    
    if ($order['status'] === 'shipped' && !$hardDelete) {
        sendResponse(false, 'Cannot delete shipped order. Use hard delete to force removal.', null, 400);
    }
    
    $pdo->beginTransaction();
    
    try {
        // Get order items before deletion to restore artwork availability
        $itemsStmt = $pdo->prepare("
            SELECT oi.artwork_id, oi.quantity, a.title 
            FROM order_items oi 
            JOIN artworks a ON oi.artwork_id = a.artwork_id 
            WHERE oi.order_id = ?
        ");
        $itemsStmt->execute([$orderId]);
        $orderItems = $itemsStmt->fetchAll();
        
        if ($hardDelete) {
            // Hard delete - completely remove order and related data
            $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$orderId]);
            $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
            $result = $stmt->execute([$orderId]);
            
            $action = "permanently deleted";
            
        } else {
            // Soft delete - cancel order
            $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
            $result = $stmt->execute([$orderId]);
            
            $action = "cancelled";
        }
        
        if (!$result) {
            throw new Exception('Failed to delete order');
        }
        
        // Make artworks available again
        foreach ($orderItems as $item) {
            if ($item['quantity'] == 1) { // Only for single items
                $updateArtwork = $pdo->prepare("UPDATE artworks SET is_available = 1 WHERE artwork_id = ?");
                $updateArtwork->execute([$item['artwork_id']]);
            }
        }
        
        $pdo->commit();
        
        sendResponse(true, "Order #{$orderId} for {$order['first_name']} {$order['last_name']} (${$order['total_amount']}) $action successfully");
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    sendResponse(false, 'Error deleting order: ' . $e->getMessage(), null, 500);
}
?>
