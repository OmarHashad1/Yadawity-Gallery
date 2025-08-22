<?php
include_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, 'Invalid JSON input', null, 400);
    }
    
    // Validate required fields
    $error = validateRequired($input, ['auction_id']);
    if ($error) {
        sendResponse(false, $error, null, 400);
    }
    
    $auctionId = $input['auction_id'];
    
    // Check if auction exists
    $checkStmt = $pdo->prepare("
        SELECT au.id, au.product_id, au.status, au.start_time, au.end_time,
               a.title as artwork_title
        FROM auctions au 
        JOIN artworks a ON au.product_id = a.artwork_id
        WHERE au.id = ?
    ");
    $checkStmt->execute([$auctionId]);
    $auction = $checkStmt->fetch();
    
    if (!$auction) {
        sendResponse(false, 'Auction not found', null, 404);
    }
    
    $updateFields = [];
    $params = [];
    
    // Build dynamic update query
    $allowedFields = ['starting_bid', 'start_time', 'end_time', 'status'];
    
    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updateFields[] = "$field = ?";
            $params[] = $input[$field];
        }
    }
    
    if (empty($updateFields)) {
        sendResponse(false, 'No valid fields to update', null, 400);
    }
    
    // Validate status if being updated
    if (isset($input['status'])) {
        $validStatuses = ['active', 'ended', 'cancelled'];
        if (!in_array($input['status'], $validStatuses)) {
            sendResponse(false, 'Invalid auction status', null, 400);
        }
    }
    
    // Validate dates if being updated
    if (isset($input['start_time']) || isset($input['end_time'])) {
        $startTime = isset($input['start_time']) ? $input['start_time'] : $auction['start_time'];
        $endTime = isset($input['end_time']) ? $input['end_time'] : $auction['end_time'];
        
        $startTimestamp = strtotime($startTime);
        $endTimestamp = strtotime($endTime);
        
        if (!$startTimestamp || !$endTimestamp) {
            sendResponse(false, 'Invalid date format', null, 400);
        }
        
        if ($endTimestamp <= $startTimestamp) {
            sendResponse(false, 'End time must be after start time', null, 400);
        }
    }
    
    $pdo->beginTransaction();
    
    try {
        $params[] = $auctionId;
        $sql = "UPDATE auctions SET " . implode(', ', $updateFields) . " WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);
        
        if (!$result) {
            throw new Exception('Failed to update auction');
        }
        
        // If auction is being cancelled or ended, update artwork status
        if (isset($input['status']) && in_array($input['status'], ['cancelled', 'ended'])) {
            $updateArtwork = $pdo->prepare("UPDATE artworks SET on_auction = 0 WHERE artwork_id = ?");
            $updateArtwork->execute([$auction['product_id']]);
        }
        
        $pdo->commit();
        
        // Get updated auction data
        $getAuctionStmt = $pdo->prepare("
            SELECT au.id, au.starting_bid, au.current_bid, au.start_time, au.end_time, 
                   au.status, au.created_at,
                   a.artwork_id, a.title as artwork_title, a.artwork_image, a.type,
                   u.user_id as artist_id, u.first_name, u.last_name, u.email,
                   COUNT(ab.id) as bid_count
            FROM auctions au 
            JOIN artworks a ON au.product_id = a.artwork_id
            JOIN users u ON au.artist_id = u.user_id 
            LEFT JOIN auction_bids ab ON au.id = ab.auction_id
            WHERE au.id = ?
            GROUP BY au.id
        ");
        $getAuctionStmt->execute([$auctionId]);
        $updatedAuction = $getAuctionStmt->fetch();
        
        sendResponse(true, 'Auction updated successfully', $updatedAuction);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    sendResponse(false, 'Error updating auction: ' . $e->getMessage(), null, 500);
}
?>
