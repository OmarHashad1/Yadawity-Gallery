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
    
    // Check if order exists
    $checkStmt = $pdo->prepare("
        SELECT o.id, o.status, u.first_name, u.last_name 
        FROM orders o 
        JOIN users u ON o.buyer_id = u.user_id 
        WHERE o.id = ?
    ");
    $checkStmt->execute([$orderId]);
    $order = $checkStmt->fetch();
    
    if (!$order) {
        sendResponse(false, 'Order not found', null, 404);
    }
    
    $updateFields = [];
    $params = [];
    
    // Build dynamic update query
    $allowedFields = ['status', 'shipping_address', 'total_amount'];
    
    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updateFields[] = "$field = ?";
            $params[] = $input[$field];
        }
    }
    
    if (empty($updateFields)) {
        sendResponse(false, 'No valid fields to update', null, 400);
    }
    
    // Validate status if being updated
    if (isset($input['status'])) {
        $validStatuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($input['status'], $validStatuses)) {
            sendResponse(false, 'Invalid order status', null, 400);
        }
        
        // Business logic for status transitions
        if ($order['status'] === 'delivered' && $input['status'] !== 'delivered') {
            sendResponse(false, 'Cannot change status of delivered order', null, 400);
        }
        
        if ($order['status'] === 'cancelled' && $input['status'] !== 'cancelled') {
            sendResponse(false, 'Cannot change status of cancelled order', null, 400);
        }
    }
    
    $pdo->beginTransaction();
    
    try {
        $params[] = $orderId;
        $sql = "UPDATE orders SET " . implode(', ', $updateFields) . " WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);
        
        if (!$result) {
            throw new Exception('Failed to update order');
        }
        
        // If order is being cancelled, make artworks available again
        if (isset($input['status']) && $input['status'] === 'cancelled') {
            $makeAvailableStmt = $pdo->prepare("
                UPDATE artworks a 
                JOIN order_items oi ON a.artwork_id = oi.artwork_id 
                SET a.is_available = 1 
                WHERE oi.order_id = ?
            ");
            $makeAvailableStmt->execute([$orderId]);
        }
        
        $pdo->commit();
        
        // Get updated order data
        $getOrderStmt = $pdo->prepare("
            SELECT o.id as order_id, o.total_amount, o.status, o.shipping_address, o.created_at,
                   u.user_id as buyer_id, u.first_name, u.last_name, u.email, u.phone,
                   COUNT(oi.id) as item_count,
                   GROUP_CONCAT(CONCAT(a.title, ' ($', oi.price, ')') SEPARATOR ', ') as items
            FROM orders o 
            JOIN users u ON o.buyer_id = u.user_id 
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN artworks a ON oi.artwork_id = a.artwork_id
            WHERE o.id = ?
            GROUP BY o.id
        ");
        $getOrderStmt->execute([$orderId]);
        $updatedOrder = $getOrderStmt->fetch();
        
        sendResponse(true, 'Order updated successfully', $updatedOrder);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    sendResponse(false, 'Error updating order: ' . $e->getMessage(), null, 500);
}
?>
