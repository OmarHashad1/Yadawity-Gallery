<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    $auctionId = $_GET['auction_id'] ?? '';
    
    if (!$auctionId) {
        echo json_encode(['success' => false, 'message' => 'Auction ID required']);
        exit;
    }
    
    // Get auction details
    $stmt = $pdo->prepare("
        SELECT au.*, a.title, a.description, a.price as base_price, a.artwork_image,
               u.first_name, u.last_name, u.email
        FROM auctions au
        JOIN artworks a ON au.product_id = a.artwork_id
        JOIN users u ON au.artist_id = u.user_id
        WHERE au.id = ?
    ");
    $stmt->execute([$auctionId]);
    $auction = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$auction) {
        echo json_encode(['success' => false, 'message' => 'Auction not found']);
        exit;
    }
    
    // Get all bids for this auction
    $stmt = $pdo->prepare("
        SELECT ab.bid_amount, ab.bid_time, ab.is_winning_bid,
               u.first_name, u.last_name, u.email
        FROM auction_bids ab
        JOIN users u ON ab.user_id = u.user_id
        WHERE ab.auction_id = ?
        ORDER BY ab.bid_time DESC
    ");
    $stmt->execute([$auctionId]);
    $bids = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'auction' => $auction,
            'bids' => $bids,
            'total_bids' => count($bids)
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
