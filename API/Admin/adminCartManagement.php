<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Get all cart items with user and artwork info
    $cartItems = $pdo->query("
        SELECT c.id, c.quantity, c.added_date, c.is_active,
               u.first_name, u.last_name, u.email,
               a.title, a.price, a.type, a.is_available
        FROM cart c
        JOIN users u ON c.user_id = u.user_id
        JOIN artworks a ON c.artwork_id = a.artwork_id
        ORDER BY c.added_date DESC
        LIMIT 100
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Cart statistics
    $stats = [
        'total_cart_items' => count($cartItems),
        'active_carts' => count(array_filter($cartItems, function($c) { return $c['is_active']; })),
        'inactive_carts' => count(array_filter($cartItems, function($c) { return !$c['is_active']; })),
        'total_cart_value' => array_sum(array_map(function($c) { 
            return $c['is_active'] ? $c['price'] * $c['quantity'] : 0; 
        }, $cartItems))
    ];

    echo json_encode([
        'success' => true,
        'data' => [
            'cart_items' => $cartItems,
            'statistics' => $stats
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
