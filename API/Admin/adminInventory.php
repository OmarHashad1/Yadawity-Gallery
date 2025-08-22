<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Get inventory status
    $totalArtworks = $pdo->query("SELECT COUNT(*) FROM artworks")->fetchColumn();
    $availableArtworks = $pdo->query("SELECT COUNT(*) FROM artworks WHERE is_available = 1")->fetchColumn();
    $unavailableArtworks = $pdo->query("SELECT COUNT(*) FROM artworks WHERE is_available = 0")->fetchColumn();
    $auctionArtworks = $pdo->query("SELECT COUNT(*) FROM artworks WHERE on_auction = 1")->fetchColumn();
    
    // Get artworks by type
    $artworkTypes = $pdo->query("
        SELECT type, COUNT(*) as count 
        FROM artworks 
        GROUP BY type
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent additions
    $recentArtworks = $pdo->query("
        SELECT a.title, a.price, a.type, a.created_at, u.first_name, u.last_name
        FROM artworks a
        JOIN users u ON a.artist_id = u.user_id
        ORDER BY a.created_at DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'summary' => [
                'total_artworks' => $totalArtworks,
                'available_artworks' => $availableArtworks,
                'unavailable_artworks' => $unavailableArtworks,
                'auction_artworks' => $auctionArtworks
            ],
            'artwork_types' => $artworkTypes,
            'recent_artworks' => $recentArtworks
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
