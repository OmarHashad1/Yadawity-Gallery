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
    
    $contentId = $input['content_id'] ?? $_GET['content_id'] ?? '';
    $contentType = $input['content_type'] ?? $_GET['content_type'] ?? '';
    $forceDelete = $input['force_delete'] ?? false;
    
    if (empty($contentId) && empty($contentType)) {
        sendResponse(false, 'content_id or content_type is required', null, 400);
    }
    
    if ($contentId) {
        // Delete specific content item by ID
        $selectStmt = $pdo->prepare("SELECT * FROM content WHERE id = ?");
        $selectStmt->execute([$contentId]);
        $contentItem = $selectStmt->fetch();
        
        if (!$contentItem) {
            sendResponse(false, 'Content item not found', null, 404);
        }
        
        // Check if it's a protected content type
        $protectedTypes = ['about', 'terms', 'privacy'];
        if (in_array($contentItem['content_type'], $protectedTypes) && !$forceDelete) {
            sendResponse(false, "Cannot delete protected content type '{$contentItem['content_type']}'. Use force_delete=true to override.", null, 403);
        }
        
        $deleteStmt = $pdo->prepare("DELETE FROM content WHERE id = ?");
        $deleteStmt->execute([$contentId]);
        
        if ($deleteStmt->rowCount() === 0) {
            sendResponse(false, 'Content item not found or already deleted', null, 404);
        }
        
        sendResponse(true, 'Content item deleted successfully', [
            'deleted_item' => $contentItem
        ]);
        
    } elseif ($contentType) {
        // Delete all content items of a specific type
        $protectedTypes = ['about', 'terms', 'privacy'];
        if (in_array($contentType, $protectedTypes) && !$forceDelete) {
            sendResponse(false, "Cannot delete protected content type '$contentType'. Use force_delete=true to override.", null, 403);
        }
        
        // Get items before deletion
        $selectStmt = $pdo->prepare("SELECT * FROM content WHERE content_type = ?");
        $selectStmt->execute([$contentType]);
        $contentItems = $selectStmt->fetchAll();
        
        if (empty($contentItems)) {
            sendResponse(false, "No content items found for type '$contentType'", null, 404);
        }
        
        $deleteStmt = $pdo->prepare("DELETE FROM content WHERE content_type = ?");
        $deleteStmt->execute([$contentType]);
        
        $deletedCount = $deleteStmt->rowCount();
        
        sendResponse(true, "Deleted $deletedCount content item(s) of type '$contentType'", [
            'deleted_items' => $contentItems,
            'deleted_count' => $deletedCount,
            'content_type' => $contentType
        ]);
    }

} catch (Exception $e) {
    sendResponse(false, 'Error deleting content item(s): ' . $e->getMessage(), null, 500);
}
?>
