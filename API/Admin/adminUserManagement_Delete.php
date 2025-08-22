<?php
include_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, 'Invalid JSON input', null, 400);
    }
    
    // Validate required fields
    $error = validateRequired($input, ['user_id']);
    if ($error) {
        sendResponse(false, $error, null, 400);
    }
    
    $userId = $input['user_id'];
    $hardDelete = $input['hard_delete'] ?? false;
    
    // Check if user exists
    $checkStmt = $pdo->prepare("SELECT user_id, first_name, last_name, email FROM users WHERE user_id = ?");
    $checkStmt->execute([$userId]);
    $user = $checkStmt->fetch();
    
    if (!$user) {
        sendResponse(false, 'User not found', null, 404);
    }
    
    if ($hardDelete) {
        // Hard delete - completely remove user and related data
        $pdo->beginTransaction();
        
        try {
            // Delete related data first (due to foreign key constraints)
            $pdo->prepare("DELETE FROM user_login_sessions WHERE user_id = ?")->execute([$userId]);
            $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$userId]);
            $pdo->prepare("DELETE FROM wishlists WHERE user_id = ?")->execute([$userId]);
            $pdo->prepare("DELETE FROM course_enrollments WHERE user_id = ?")->execute([$userId]);
            $pdo->prepare("DELETE FROM auction_bids WHERE user_id = ?")->execute([$userId]);
            $pdo->prepare("DELETE FROM orders WHERE buyer_id = ?")->execute([$userId]);
            $pdo->prepare("DELETE FROM exams WHERE user_id = ?")->execute([$userId]);
            $pdo->prepare("DELETE FROM artist_reviews WHERE user_id = ? OR artist_id = ?")->execute([$userId, $userId]);
            
            // Delete artworks and related data for artists
            $artworkStmt = $pdo->prepare("SELECT artwork_id FROM artworks WHERE artist_id = ?");
            $artworkStmt->execute([$userId]);
            $artworks = $artworkStmt->fetchAll();
            
            foreach ($artworks as $artwork) {
                $pdo->prepare("DELETE FROM artwork_photos WHERE artwork_id = ?")->execute([$artwork['artwork_id']]);
                $pdo->prepare("DELETE FROM order_items WHERE artwork_id = ?")->execute([$artwork['artwork_id']]);
                $pdo->prepare("DELETE FROM auctions WHERE product_id = ?")->execute([$artwork['artwork_id']]);
            }
            
            $pdo->prepare("DELETE FROM artworks WHERE artist_id = ?")->execute([$userId]);
            $pdo->prepare("DELETE FROM courses WHERE artist_id = ?")->execute([$userId]);
            $pdo->prepare("DELETE FROM galleries WHERE artist_id = ?")->execute([$userId]);
            $pdo->prepare("DELETE FROM subscribers WHERE artist_id = ?")->execute([$userId]);
            
            // Finally delete the user
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
            $result = $stmt->execute([$userId]);
            
            $pdo->commit();
            
            sendResponse(true, "User '{$user['first_name']} {$user['last_name']}' permanently deleted");
            
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
        
    } else {
        // Soft delete - deactivate user
        $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE user_id = ?");
        $result = $stmt->execute([$userId]);
        
        if ($result) {
            sendResponse(true, "User '{$user['first_name']} {$user['last_name']}' deactivated successfully");
        } else {
            sendResponse(false, 'Failed to deactivate user', null, 500);
        }
    }

} catch (Exception $e) {
    sendResponse(false, 'Error deleting user: ' . $e->getMessage(), null, 500);
}
?>
