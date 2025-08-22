<?php
include_once 'db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Only DELETE/POST method allowed', null, 405);
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    $wishlistId = $input['wishlist_id'] ?? $_GET['wishlist_id'] ?? '';
    $userId = $input['user_id'] ?? $_GET['user_id'] ?? '';
    $artworkId = $input['artwork_id'] ?? $_GET['artwork_id'] ?? '';
    $deleteType = $input['delete_type'] ?? 'single'; // single, user_all, artwork_all
    
    if ($deleteType === 'single') {
        // Delete single wishlist item
        if (empty($wishlistId)) {
            sendResponse(false, 'wishlist_id is required for single delete', null, 400);
        }
        
        // Get wishlist item details before deletion
        $selectStmt = $pdo->prepare("
            SELECT w.id, w.user_id, w.artwork_id,
                   u.first_name, u.last_name, u.email,
                   a.title as artwork_title
            FROM wishlists w
            JOIN users u ON w.user_id = u.user_id
            JOIN artworks a ON w.artwork_id = a.artwork_id
            WHERE w.id = ?
        ");
        $selectStmt->execute([$wishlistId]);
        $wishlistItem = $selectStmt->fetch();
        
        if (!$wishlistItem) {
            sendResponse(false, 'Wishlist item not found', null, 404);
        }
        
        $deleteStmt = $pdo->prepare("DELETE FROM wishlists WHERE id = ?");
        $deleteStmt->execute([$wishlistId]);
        
        if ($deleteStmt->rowCount() === 0) {
            sendResponse(false, 'Wishlist item not found or already deleted', null, 404);
        }
        
        sendResponse(true, 'Wishlist item deleted successfully', [
            'deleted_item' => $wishlistItem,
            'delete_type' => 'single'
        ]);
        
    } elseif ($deleteType === 'user_all') {
        // Delete all wishlist items for a specific user
        if (empty($userId)) {
            sendResponse(false, 'user_id is required for user_all delete', null, 400);
        }
        
        // Validate user exists
        $userStmt = $pdo->prepare("SELECT user_id, first_name, last_name FROM users WHERE user_id = ?");
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch();
        if (!$user) {
            sendResponse(false, 'User not found', null, 404);
        }
        
        // Get count before deletion
        $countStmt = $pdo->prepare("SELECT COUNT(*) as count FROM wishlists WHERE user_id = ?");
        $countStmt->execute([$userId]);
        $count = $countStmt->fetch()['count'];
        
        $deleteStmt = $pdo->prepare("DELETE FROM wishlists WHERE user_id = ?");
        $deleteStmt->execute([$userId]);
        
        sendResponse(true, "All wishlist items deleted for user {$user['first_name']} {$user['last_name']}", [
            'deleted_count' => $count,
            'user' => $user,
            'delete_type' => 'user_all'
        ]);
        
    } elseif ($deleteType === 'artwork_all') {
        // Delete all wishlist items for a specific artwork
        if (empty($artworkId)) {
            sendResponse(false, 'artwork_id is required for artwork_all delete', null, 400);
        }
        
        // Validate artwork exists
        $artworkStmt = $pdo->prepare("SELECT artwork_id, title FROM artworks WHERE artwork_id = ?");
        $artworkStmt->execute([$artworkId]);
        $artwork = $artworkStmt->fetch();
        if (!$artwork) {
            sendResponse(false, 'Artwork not found', null, 404);
        }
        
        // Get count before deletion
        $countStmt = $pdo->prepare("SELECT COUNT(*) as count FROM wishlists WHERE artwork_id = ?");
        $countStmt->execute([$artworkId]);
        $count = $countStmt->fetch()['count'];
        
        $deleteStmt = $pdo->prepare("DELETE FROM wishlists WHERE artwork_id = ?");
        $deleteStmt->execute([$artworkId]);
        
        sendResponse(true, "All wishlist items deleted for artwork '{$artwork['title']}'", [
            'deleted_count' => $count,
            'artwork' => $artwork,
            'delete_type' => 'artwork_all'
        ]);
        
    } else {
        sendResponse(false, 'Invalid delete_type. Use: single, user_all, or artwork_all', null, 400);
    }

} catch (Exception $e) {
    sendResponse(false, 'Error deleting wishlist item(s): ' . $e->getMessage(), null, 500);
}
?>
