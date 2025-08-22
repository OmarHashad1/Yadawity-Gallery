<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Auction statistics
    $totalAuctions = $pdo->query("SELECT COUNT(*) FROM auctions")->fetchColumn();
    $activeAuctions = $pdo->query("SELECT COUNT(*) FROM auctions WHERE status = 'active'")->fetchColumn();
    $endedAuctions = $pdo->query("SELECT COUNT(*) FROM auctions WHERE status = 'ended'")->fetchColumn();
    $cancelledAuctions = $pdo->query("SELECT COUNT(*) FROM auctions WHERE status = 'cancelled'")->fetchColumn();
    
    // Auction performance
    $auctionPerformance = $pdo->query("
        SELECT 
            AVG(current_bid - starting_bid) as avg_bid_increase,
            MAX(current_bid) as highest_bid,
            AVG(current_bid) as avg_final_bid,
            COUNT(CASE WHEN current_bid > starting_bid THEN 1 END) as successful_auctions
        FROM auctions
        WHERE status = 'ended'
    ")->fetch(PDO::FETCH_ASSOC);
    
    // Recent auctions
    $recentAuctions = $pdo->query("
        SELECT au.id, au.starting_bid, au.current_bid, au.status, au.end_time,
               a.title, u.first_name, u.last_name
        FROM auctions au
        JOIN artworks a ON au.product_id = a.artwork_id
        JOIN users u ON au.artist_id = u.user_id
        ORDER BY au.created_at DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Auction status distribution
    $statusDistribution = $pdo->query("
        SELECT status, COUNT(*) as count
        FROM auctions
        GROUP BY status
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'summary' => [
                'total_auctions' => $totalAuctions,
                'active_auctions' => $activeAuctions,
                'ended_auctions' => $endedAuctions,
                'cancelled_auctions' => $cancelledAuctions
            ],
            'performance' => $auctionPerformance,
            'recent_auctions' => $recentAuctions,
            'status_distribution' => $statusDistribution
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
