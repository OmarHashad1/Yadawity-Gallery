<?php
include_once 'db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Only PUT/POST method allowed', null, 405);
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    $contentId = $input['content_id'] ?? '';
    $contentType = $input['content_type'] ?? '';
    $title = $input['title'] ?? '';
    $content = $input['content'] ?? '';
    $isActive = $input['is_active'] ?? null;
    $metaDescription = $input['meta_description'] ?? '';
    $metaKeywords = $input['meta_keywords'] ?? '';
    
    // Validate required fields
    if (empty($contentId)) {
        sendResponse(false, 'content_id is required', null, 400);
    }
    
    // Check if content item exists
    $existingStmt = $pdo->prepare("SELECT * FROM content WHERE id = ?");
    $existingStmt->execute([$contentId]);
    $existing = $existingStmt->fetch();
    
    if (!$existing) {
        sendResponse(false, 'Content item not found', null, 404);
    }
    
    // Validate content type if provided
    if ($contentType) {
        $allowedTypes = ['about', 'terms', 'privacy', 'cookie', 'help', 'faq', 'contact', 'news', 'announcement'];
        if (!in_array($contentType, $allowedTypes)) {
            sendResponse(false, 'Invalid content_type. Allowed types: ' . implode(', ', $allowedTypes), null, 400);
        }
        
        // Check for conflicts with unique content types
        $uniqueTypes = ['about', 'terms', 'privacy', 'cookie'];
        if (in_array($contentType, $uniqueTypes) && $contentType !== $existing['content_type']) {
            $conflictStmt = $pdo->prepare("SELECT id FROM content WHERE content_type = ? AND id != ?");
            $conflictStmt->execute([$contentType, $contentId]);
            if ($conflictStmt->fetch()) {
                sendResponse(false, "Content type '$contentType' already exists", null, 409);
            }
        }
    }
    
    // Build update query dynamically
    $updateFields = [];
    $updateParams = [];
    
    if ($contentType && $contentType !== $existing['content_type']) {
        $updateFields[] = "content_type = ?";
        $updateParams[] = $contentType;
    }
    
    if ($title && $title !== $existing['title']) {
        $updateFields[] = "title = ?";
        $updateParams[] = $title;
    }
    
    if ($content && $content !== $existing['content']) {
        $updateFields[] = "content = ?";
        $updateParams[] = $content;
    }
    
    if ($isActive !== null && $isActive != $existing['is_active']) {
        $updateFields[] = "is_active = ?";
        $updateParams[] = $isActive;
    }
    
    if ($metaDescription !== '' && $metaDescription !== $existing['meta_description']) {
        $updateFields[] = "meta_description = ?";
        $updateParams[] = $metaDescription;
    }
    
    if ($metaKeywords !== '' && $metaKeywords !== $existing['meta_keywords']) {
        $updateFields[] = "meta_keywords = ?";
        $updateParams[] = $metaKeywords;
    }
    
    if (empty($updateFields)) {
        sendResponse(false, 'No changes detected', null, 400);
    }
    
    $updateFields[] = "updated_at = NOW()";
    $updateParams[] = $contentId;
    
    $updateSql = "UPDATE content SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute($updateParams);
    
    // Get the updated content item
    $selectStmt = $pdo->prepare("SELECT * FROM content WHERE id = ?");
    $selectStmt->execute([$contentId]);
    $updatedContent = $selectStmt->fetch();
    
    sendResponse(true, 'Content item updated successfully', [
        'content_item' => $updatedContent
    ]);

} catch (Exception $e) {
    sendResponse(false, 'Error updating content item: ' . $e->getMessage(), null, 500);
}
?>
