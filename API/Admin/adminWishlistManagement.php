<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Get all wishlist items with user and artwork info
    $wishlistItems = $pdo->query("
        SELECT w.id, w.price_alert, w.is_active, w.created_at,
               u.first_name, u.last_name, u.email,
               a.title, a.price, a.type, a.is_available
        FROM wishlists w
        JOIN users u ON w.user_id = u.user_id
        JOIN artworks a ON w.artwork_id = a.artwork_id
        ORDER BY w.created_at DESC
        LIMIT 100
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Wishlist statistics
    $stats = [
        'total_wishlist_items' => count($wishlistItems),
        'active_wishlists' => count(array_filter($wishlistItems, function($w) { return $w['is_active']; })),
        'with_price_alerts' => count(array_filter($wishlistItems, function($w) { return $w['price_alert'] > 0; })),
        'most_wishlisted_artworks' => $pdo->query("
            SELECT a.title, COUNT(w.id) as wishlist_count, u.first_name, u.last_name
            FROM wishlists w
            JOIN artworks a ON w.artwork_id = a.artwork_id
            JOIN users u ON a.artist_id = u.user_id
            GROUP BY a.artwork_id
            ORDER BY wishlist_count DESC
            LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC)
    ];

    echo json_encode([
        'success' => true,
        'data' => [
            'wishlist_items' => $wishlistItems,
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
