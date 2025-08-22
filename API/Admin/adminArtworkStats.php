<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Artwork statistics
    $totalArtworks = $pdo->query("SELECT COUNT(*) FROM artworks")->fetchColumn();
    $availableArtworks = $pdo->query("SELECT COUNT(*) FROM artworks WHERE is_available = 1")->fetchColumn();
    $soldArtworks = $pdo->query("SELECT COUNT(*) FROM artworks WHERE is_available = 0")->fetchColumn();
    $auctionArtworks = $pdo->query("SELECT COUNT(*) FROM artworks WHERE on_auction = 1")->fetchColumn();
    
    // Artwork by type
    $artworksByType = $pdo->query("
        SELECT type, COUNT(*) as count, AVG(price) as avg_price
        FROM artworks 
        GROUP BY type
        ORDER BY count DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Price range distribution
    $priceRanges = $pdo->query("
        SELECT 
            CASE 
                WHEN price < 100 THEN 'Under $100'
                WHEN price BETWEEN 100 AND 500 THEN '$100 - $500'
                WHEN price BETWEEN 501 AND 1000 THEN '$501 - $1000'
                WHEN price BETWEEN 1001 AND 5000 THEN '$1001 - $5000'
                ELSE 'Over $5000'
            END as price_range,
            COUNT(*) as count
        FROM artworks
        GROUP BY price_range
        ORDER BY MIN(price)
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Top artists by artwork count
    $topArtists = $pdo->query("
        SELECT u.first_name, u.last_name, u.email, COUNT(a.artwork_id) as artwork_count,
               AVG(a.price) as avg_price
        FROM users u
        JOIN artworks a ON u.user_id = a.artist_id
        GROUP BY u.user_id
        ORDER BY artwork_count DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'summary' => [
                'total_artworks' => $totalArtworks,
                'available_artworks' => $availableArtworks,
                'sold_artworks' => $soldArtworks,
                'auction_artworks' => $auctionArtworks
            ],
            'artworks_by_type' => $artworksByType,
            'price_ranges' => $priceRanges,
            'top_artists' => $topArtists
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
