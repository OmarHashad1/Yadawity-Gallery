<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Advanced inventory analytics
    $inventoryData = [
        'overview' => [
            'total_artworks' => $pdo->query("SELECT COUNT(*) FROM artworks")->fetchColumn(),
            'available_artworks' => $pdo->query("SELECT COUNT(*) FROM artworks WHERE is_available = 1")->fetchColumn(),
            'sold_artworks' => $pdo->query("SELECT COUNT(*) FROM artworks WHERE is_available = 0")->fetchColumn(),
            'auction_artworks' => $pdo->query("SELECT COUNT(*) FROM artworks WHERE on_auction = 1")->fetchColumn(),
            'total_value' => $pdo->query("SELECT COALESCE(SUM(price), 0) FROM artworks WHERE is_available = 1")->fetchColumn()
        ],
        'by_artist' => $pdo->query("
            SELECT u.first_name, u.last_name, u.email,
                   COUNT(a.artwork_id) as artwork_count,
                   COUNT(CASE WHEN a.is_available = 1 THEN 1 END) as available_count,
                   COALESCE(SUM(CASE WHEN a.is_available = 1 THEN a.price END), 0) as total_value
            FROM users u
            JOIN artworks a ON u.user_id = a.artist_id
            WHERE u.user_type = 'artist'
            GROUP BY u.user_id
            ORDER BY artwork_count DESC
            LIMIT 15
        ")->fetchAll(PDO::FETCH_ASSOC),
        'by_type' => $pdo->query("
            SELECT type,
                   COUNT(*) as total_count,
                   COUNT(CASE WHEN is_available = 1 THEN 1 END) as available_count,
                   AVG(price) as avg_price,
                   MIN(price) as min_price,
                   MAX(price) as max_price
            FROM artworks
            GROUP BY type
            ORDER BY total_count DESC
        ")->fetchAll(PDO::FETCH_ASSOC),
        'price_analysis' => $pdo->query("
            SELECT 
                CASE 
                    WHEN price < 100 THEN 'Under $100'
                    WHEN price BETWEEN 100 AND 500 THEN '$100 - $500'
                    WHEN price BETWEEN 501 AND 1000 THEN '$501 - $1,000'
                    WHEN price BETWEEN 1001 AND 5000 THEN '$1,001 - $5,000'
                    WHEN price BETWEEN 5001 AND 10000 THEN '$5,001 - $10,000'
                    ELSE 'Over $10,000'
                END as price_range,
                COUNT(*) as count,
                COUNT(CASE WHEN is_available = 1 THEN 1 END) as available_count
            FROM artworks
            GROUP BY price_range
            ORDER BY MIN(price)
        ")->fetchAll(PDO::FETCH_ASSOC),
        'recent_additions' => $pdo->query("
            SELECT a.artwork_id, a.title, a.price, a.type, a.created_at,
                   u.first_name, u.last_name
            FROM artworks a
            JOIN users u ON a.artist_id = u.user_id
            ORDER BY a.created_at DESC
            LIMIT 20
        ")->fetchAll(PDO::FETCH_ASSOC)
    ];

    echo json_encode([
        'success' => true,
        'data' => $inventoryData
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
