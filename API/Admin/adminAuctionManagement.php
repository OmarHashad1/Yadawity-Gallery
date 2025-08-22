<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

include_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get all auctions with artwork and artist info
            $sql = "SELECT au.id, au.starting_bid, au.current_bid, au.start_time, au.end_time, au.status,
                           a.title as artwork_title, a.price as artwork_price,
                           u.first_name, u.last_name, u.email
                    FROM auctions au
                    JOIN artworks a ON au.product_id = a.artwork_id
                    JOIN users u ON au.artist_id = u.user_id
                    ORDER BY au.created_at DESC";
            
            $stmt = $pdo->query($sql);
            $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $auctions
            ]);
            break;
            
        case 'POST':
            // Create new auction
            $input = json_decode(file_get_contents('php://input'), true);
            $productId = $input['product_id'];
            $artistId = $input['artist_id'];
            $startingBid = $input['starting_bid'];
            $endTime = $input['end_time'];
            $status = $input['status'] ?? 'active';
            
            $stmt = $pdo->prepare("INSERT INTO auctions (product_id, artist_id, starting_bid, current_bid, end_time, status) VALUES (?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([$productId, $artistId, $startingBid, $startingBid, $endTime, $status]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Auction created successfully', 'auction_id' => $pdo->lastInsertId()]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create auction']);
            }
            break;
            
        case 'PUT':
            // Update auction details
            $input = json_decode(file_get_contents('php://input'), true);
            $auctionId = $input['auction_id'];
            $updateFields = [];
            $params = [];
            
            if (isset($input['starting_bid'])) {
                $updateFields[] = "starting_bid = ?";
                $params[] = $input['starting_bid'];
            }
            if (isset($input['end_time'])) {
                $updateFields[] = "end_time = ?";
                $params[] = $input['end_time'];
            }
            if (isset($input['status'])) {
                $updateFields[] = "status = ?";
                $params[] = $input['status'];
            }
            
            $params[] = $auctionId;
            $sql = "UPDATE auctions SET " . implode(', ', $updateFields) . " WHERE id = ?";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Auction updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update auction']);
            }
            break;
            
        case 'DELETE':
            // Cancel/Delete auction
            $input = json_decode(file_get_contents('php://input'), true);
            $auctionId = $input['auction_id'];
            
            $stmt = $pdo->prepare("UPDATE auctions SET status = 'cancelled' WHERE id = ?");
            $result = $stmt->execute([$auctionId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Auction cancelled successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to cancel auction']);
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
