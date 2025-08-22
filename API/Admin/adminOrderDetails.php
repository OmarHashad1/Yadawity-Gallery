<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    $orderId = $_GET['order_id'] ?? '';
    
    if (!$orderId) {
        echo json_encode(['success' => false, 'message' => 'Order ID required']);
        exit;
    }
    
    // Get order details
    $stmt = $pdo->prepare("
        SELECT o.*, u.first_name, u.last_name, u.email, u.phone
        FROM orders o
        JOIN users u ON o.buyer_id = u.user_id
        WHERE o.id = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }
    
    // Get order items
    $stmt = $pdo->prepare("
        SELECT oi.price, oi.quantity, a.title, a.artwork_image, a.type,
               u.first_name as artist_first_name, u.last_name as artist_last_name
        FROM order_items oi
        JOIN artworks a ON oi.artwork_id = a.artwork_id
        JOIN users u ON a.artist_id = u.user_id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'order' => $order,
            'items' => $orderItems,
            'total_items' => count($orderItems)
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
