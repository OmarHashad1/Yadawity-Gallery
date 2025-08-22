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
    $hardDelete = $input['hard_delete'] ?? false;
    
    // Check if auction exists
    $checkStmt = $pdo->prepare("
        SELECT au.id, au.product_id, au.status, au.current_bid,
               a.title as artwork_title, u.first_name, u.last_name
        FROM auctions au 
        JOIN artworks a ON au.product_id = a.artwork_id
        JOIN users u ON au.artist_id = u.user_id
        WHERE au.id = ?
    ");
    $checkStmt->execute([$auctionId]);
    $auction = $checkStmt->fetch();
    
    if (!$auction) {
        sendResponse(false, 'Auction not found', null, 404);
    }
    
    // Check if auction has bids
    $bidCheck = $pdo->prepare("SELECT COUNT(*) as bid_count FROM auction_bids WHERE auction_id = ?");
    $bidCheck->execute([$auctionId]);
    $bidCount = $bidCheck->fetch()['bid_count'];
    
    if ($bidCount > 0 && !$hardDelete) {
        sendResponse(false, 'Cannot delete auction with bids. Use hard delete to force removal.', null, 400);
    }
    
    if ($auction['status'] === 'active' && !$hardDelete) {
        sendResponse(false, 'Cannot delete active auction. Cancel it first or use hard delete.', null, 400);
    }
    
    $pdo->beginTransaction();
    
    try {
        if ($hardDelete) {
            // Hard delete - completely remove auction and related data
            $pdo->prepare("DELETE FROM auction_bids WHERE auction_id = ?")->execute([$auctionId]);
            $stmt = $pdo->prepare("DELETE FROM auctions WHERE id = ?");
            $result = $stmt->execute([$auctionId]);
            
            $action = "permanently deleted";
            
        } else {
            // Soft delete - cancel auction
            $stmt = $pdo->prepare("UPDATE auctions SET status = 'cancelled' WHERE id = ?");
            $result = $stmt->execute([$auctionId]);
            
            $action = "cancelled";
        }
        
        if (!$result) {
            throw new Exception('Failed to delete auction');
        }
        
        // Update artwork status - remove from auction
        $updateArtwork = $pdo->prepare("UPDATE artworks SET on_auction = 0 WHERE artwork_id = ?");
        $updateArtwork->execute([$auction['product_id']]);
        
        $pdo->commit();
        
        sendResponse(true, "Auction for '{$auction['artwork_title']}' by {$auction['first_name']} {$auction['last_name']} $action successfully");
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    sendResponse(false, 'Error deleting auction: ' . $e->getMessage(), null, 500);
}
?>
