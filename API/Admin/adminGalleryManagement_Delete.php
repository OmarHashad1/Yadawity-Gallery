<?php
include_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, 'Invalid JSON input', null, 400);
    }
    
    // Validate required fields
    $error = validateRequired($input, ['gallery_id']);
    if ($error) {
        sendResponse(false, $error, null, 400);
    }
    
    $galleryId = $input['gallery_id'];
    $hardDelete = $input['hard_delete'] ?? false;
    
    // Check if gallery exists
    $checkStmt = $pdo->prepare("
        SELECT g.gallery_id, g.title, g.gallery_type, g.is_active,
               u.first_name, u.last_name 
        FROM galleries g 
        JOIN users u ON g.artist_id = u.user_id 
        WHERE g.gallery_id = ?
    ");
    $checkStmt->execute([$galleryId]);
    $gallery = $checkStmt->fetch();
    
    if (!$gallery) {
        sendResponse(false, 'Gallery not found', null, 404);
    }
    
    if ($hardDelete) {
        // Hard delete - completely remove gallery
        $stmt = $pdo->prepare("DELETE FROM galleries WHERE gallery_id = ?");
        $result = $stmt->execute([$galleryId]);
        
        if ($result) {
            sendResponse(true, "Gallery '{$gallery['title']}' by {$gallery['first_name']} {$gallery['last_name']} permanently deleted");
        } else {
            sendResponse(false, 'Failed to delete gallery', null, 500);
        }
        
    } else {
        // Soft delete - deactivate gallery
        $stmt = $pdo->prepare("UPDATE galleries SET is_active = 0 WHERE gallery_id = ?");
        $result = $stmt->execute([$galleryId]);
        
        if ($result) {
            sendResponse(true, "Gallery '{$gallery['title']}' deactivated successfully");
        } else {
            sendResponse(false, 'Failed to deactivate gallery', null, 500);
        }
    }

} catch (Exception $e) {
    sendResponse(false, 'Error deleting gallery: ' . $e->getMessage(), null, 500);
}
?>
