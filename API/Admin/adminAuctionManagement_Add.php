<?php
include_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, 'Invalid JSON input', null, 400);
    }
    
    // Validate required fields
    $required = ['product_id', 'starting_bid', 'start_time', 'end_time'];
    $error = validateRequired($input, $required);
    if ($error) {
        sendResponse(false, $error, null, 400);
    }
    
    $productId = $input['product_id'];
    $startingBid = $input['starting_bid'];
    $startTime = $input['start_time'];
    $endTime = $input['end_time'];
    
    // Validate artwork exists and get artist_id
    $artworkCheck = $pdo->prepare("
        SELECT a.artwork_id, a.artist_id, a.title, a.is_available, a.on_auction,
               u.first_name, u.last_name 
        FROM artworks a 
        JOIN users u ON a.artist_id = u.user_id 
        WHERE a.artwork_id = ?
    ");
    $artworkCheck->execute([$productId]);
    $artwork = $artworkCheck->fetch();
    
    if (!$artwork) {
        sendResponse(false, 'Artwork not found', null, 404);
    }
    
    if (!$artwork['is_available']) {
        sendResponse(false, 'Artwork is not available for auction', null, 400);
    }
    
    if ($artwork['on_auction']) {
        sendResponse(false, 'Artwork is already on auction', null, 400);
    }
    
    // Validate dates
    $startTimestamp = strtotime($startTime);
    $endTimestamp = strtotime($endTime);
    
    if (!$startTimestamp || !$endTimestamp) {
        sendResponse(false, 'Invalid date format', null, 400);
    }
    
    if ($endTimestamp <= $startTimestamp) {
        sendResponse(false, 'End time must be after start time', null, 400);
    }
    
    if ($startTimestamp < time() - 3600) { // Allow 1 hour buffer for immediate auctions
        sendResponse(false, 'Start time cannot be in the past', null, 400);
    }
    
    $pdo->beginTransaction();
    
    try {
        // Create auction
        $stmt = $pdo->prepare("
            INSERT INTO auctions (product_id, artist_id, starting_bid, start_time, end_time, status) 
            VALUES (?, ?, ?, ?, ?, 'active')
        ");
        
        $result = $stmt->execute([
            $productId, 
            $artwork['artist_id'], 
            $startingBid, 
            date('Y-m-d H:i:s', $startTimestamp),
            date('Y-m-d H:i:s', $endTimestamp)
        ]);
        
        if (!$result) {
            throw new Exception('Failed to create auction');
        }
        
        $auctionId = $pdo->lastInsertId();
        
        // Mark artwork as on auction
        $updateArtwork = $pdo->prepare("UPDATE artworks SET on_auction = 1 WHERE artwork_id = ?");
        $updateArtwork->execute([$productId]);
        
        $pdo->commit();
        
        // Get the created auction with full details
        $getAuctionStmt = $pdo->prepare("
            SELECT au.id, au.starting_bid, au.current_bid, au.start_time, au.end_time, 
                   au.status, au.created_at,
                   a.artwork_id, a.title as artwork_title, a.artwork_image, a.type,
                   u.user_id as artist_id, u.first_name, u.last_name, u.email
            FROM auctions au 
            JOIN artworks a ON au.product_id = a.artwork_id
            JOIN users u ON au.artist_id = u.user_id 
            WHERE au.id = ?
        ");
        $getAuctionStmt->execute([$auctionId]);
        $auction = $getAuctionStmt->fetch();
        
        sendResponse(true, 'Auction created successfully', $auction, 201);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    sendResponse(false, 'Error creating auction: ' . $e->getMessage(), null, 500);
}
?>
