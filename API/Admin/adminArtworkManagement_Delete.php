<?php
include_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, 'Invalid JSON input', null, 400);
    }
    
    // Validate required fields
    $error = validateRequired($input, ['artwork_id']);
    if ($error) {
        sendResponse(false, $error, null, 400);
    }
    
    $artworkId = $input['artwork_id'];
    $hardDelete = $input['hard_delete'] ?? false;
    
    // Check if artwork exists
    $checkStmt = $pdo->prepare("
        SELECT a.artwork_id, a.title, u.first_name, u.last_name 
        FROM artworks a 
        JOIN users u ON a.artist_id = u.user_id 
        WHERE a.artwork_id = ?
    ");
    $checkStmt->execute([$artworkId]);
    $artwork = $checkStmt->fetch();
    
    if (!$artwork) {
        sendResponse(false, 'Artwork not found', null, 404);
    }
    
    if ($hardDelete) {
        // Hard delete - completely remove artwork and related data
        $pdo->beginTransaction();
        
        try {
            // Delete related data first (due to foreign key constraints)
            $pdo->prepare("DELETE FROM artwork_photos WHERE artwork_id = ?")->execute([$artworkId]);
            $pdo->prepare("DELETE FROM cart WHERE artwork_id = ?")->execute([$artworkId]);
            $pdo->prepare("DELETE FROM wishlists WHERE artwork_id = ?")->execute([$artworkId]);
            $pdo->prepare("DELETE FROM order_items WHERE artwork_id = ?")->execute([$artworkId]);
            $pdo->prepare("DELETE FROM artist_reviews WHERE artwork_id = ?")->execute([$artworkId]);
            
            // Delete auctions and related bids
            $auctionStmt = $pdo->prepare("SELECT id FROM auctions WHERE product_id = ?");
            $auctionStmt->execute([$artworkId]);
            $auctions = $auctionStmt->fetchAll();
            
            foreach ($auctions as $auction) {
                $pdo->prepare("DELETE FROM auction_bids WHERE auction_id = ?")->execute([$auction['id']]);
            }
            
            $pdo->prepare("DELETE FROM auctions WHERE product_id = ?")->execute([$artworkId]);
            
            // Finally delete the artwork
            $stmt = $pdo->prepare("DELETE FROM artworks WHERE artwork_id = ?");
            $result = $stmt->execute([$artworkId]);
            
            $pdo->commit();
            
            sendResponse(true, "Artwork '{$artwork['title']}' by {$artwork['first_name']} {$artwork['last_name']} permanently deleted");
            
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
        
    } else {
        // Soft delete - mark as unavailable
        $stmt = $pdo->prepare("UPDATE artworks SET is_available = 0 WHERE artwork_id = ?");
        $result = $stmt->execute([$artworkId]);
        
        if ($result) {
            sendResponse(true, "Artwork '{$artwork['title']}' marked as unavailable");
        } else {
            sendResponse(false, 'Failed to mark artwork as unavailable', null, 500);
        }
    }

} catch (Exception $e) {
    sendResponse(false, 'Error deleting artwork: ' . $e->getMessage(), null, 500);
}
?>
