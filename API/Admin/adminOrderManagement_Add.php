<?php
include_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, 'Invalid JSON input', null, 400);
    }
    
    // Validate required fields
    $required = ['buyer_id', 'items', 'total_amount'];
    $error = validateRequired($input, $required);
    if ($error) {
        sendResponse(false, $error, null, 400);
    }
    
    $buyerId = $input['buyer_id'];
    $items = $input['items']; // Array of {artwork_id, price, quantity}
    $totalAmount = $input['total_amount'];
    $shippingAddress = $input['shipping_address'] ?? '';
    $status = $input['status'] ?? 'pending';
    
    // Validate buyer exists
    $buyerCheck = $pdo->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $buyerCheck->execute([$buyerId]);
    if (!$buyerCheck->fetch()) {
        sendResponse(false, 'Buyer not found', null, 404);
    }
    
    // Validate items array
    if (!is_array($items) || empty($items)) {
        sendResponse(false, 'Items array is required and cannot be empty', null, 400);
    }
    
    // Validate status
    $validStatuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
    if (!in_array($status, $validStatuses)) {
        sendResponse(false, 'Invalid order status', null, 400);
    }
    
    $pdo->beginTransaction();
    
    try {
        // Create order
        $stmt = $pdo->prepare("
            INSERT INTO orders (buyer_id, total_amount, status, shipping_address) 
            VALUES (?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([$buyerId, $totalAmount, $status, $shippingAddress]);
        
        if (!$result) {
            throw new Exception('Failed to create order');
        }
        
        $orderId = $pdo->lastInsertId();
        
        // Add order items
        $itemStmt = $pdo->prepare("
            INSERT INTO order_items (order_id, artwork_id, price, quantity) 
            VALUES (?, ?, ?, ?)
        ");
        
        $calculatedTotal = 0;
        foreach ($items as $item) {
            if (!isset($item['artwork_id'], $item['price'], $item['quantity'])) {
                throw new Exception('Each item must have artwork_id, price, and quantity');
            }
            
            // Validate artwork exists and is available
            $artworkCheck = $pdo->prepare("
                SELECT artwork_id, title, is_available 
                FROM artworks 
                WHERE artwork_id = ?
            ");
            $artworkCheck->execute([$item['artwork_id']]);
            $artwork = $artworkCheck->fetch();
            
            if (!$artwork) {
                throw new Exception("Artwork with ID {$item['artwork_id']} not found");
            }
            
            if (!$artwork['is_available']) {
                throw new Exception("Artwork '{$artwork['title']}' is not available");
            }
            
            $itemStmt->execute([
                $orderId, 
                $item['artwork_id'], 
                $item['price'], 
                $item['quantity']
            ]);
            
            $calculatedTotal += $item['price'] * $item['quantity'];
            
            // Mark artwork as unavailable if quantity = 1 (single item)
            if ($item['quantity'] == 1) {
                $updateArtwork = $pdo->prepare("UPDATE artworks SET is_available = 0 WHERE artwork_id = ?");
                $updateArtwork->execute([$item['artwork_id']]);
            }
        }
        
        // Validate total amount
        if (abs($calculatedTotal - $totalAmount) > 0.01) {
            throw new Exception('Total amount mismatch');
        }
        
        $pdo->commit();
        
        // Get the created order with full details
        $getOrderStmt = $pdo->prepare("
            SELECT o.id as order_id, o.total_amount, o.status, o.shipping_address, o.created_at,
                   u.user_id as buyer_id, u.first_name, u.last_name, u.email, u.phone,
                   COUNT(oi.id) as item_count
            FROM orders o 
            JOIN users u ON o.buyer_id = u.user_id 
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.id = ?
            GROUP BY o.id
        ");
        $getOrderStmt->execute([$orderId]);
        $order = $getOrderStmt->fetch();
        
        sendResponse(true, 'Order created successfully', $order, 201);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    sendResponse(false, 'Error creating order: ' . $e->getMessage(), null, 500);
}
?>
