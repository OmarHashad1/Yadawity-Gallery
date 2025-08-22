<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get all bids with auction and user info
            $bids = $pdo->query("
                SELECT ab.id, ab.bid_amount, ab.bid_time, ab.is_winning_bid,
                       u.first_name, u.last_name, u.email,
                       au.id as auction_id, a.title as artwork_title
                FROM auction_bids ab
                JOIN users u ON ab.user_id = u.user_id
                JOIN auctions au ON ab.auction_id = au.id
                JOIN artworks a ON au.product_id = a.artwork_id
                ORDER BY ab.bid_time DESC
                LIMIT 100
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $bids
            ]);
            break;
            
        case 'DELETE':
            // Remove invalid bids (admin moderation)
            $input = json_decode(file_get_contents('php://input'), true);
            $bidId = $input['bid_id'];
            
            $stmt = $pdo->prepare("DELETE FROM auction_bids WHERE id = ?");
            $result = $stmt->execute([$bidId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Bid removed successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to remove bid']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
