<?php
include_once 'db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Only POST method allowed', null, 405);
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    $contentType = $input['content_type'] ?? '';
    $title = $input['title'] ?? '';
    $content = $input['content'] ?? '';
    $isActive = $input['is_active'] ?? 1;
    $metaDescription = $input['meta_description'] ?? '';
    $metaKeywords = $input['meta_keywords'] ?? '';
    
    // Validate required fields
    if (empty($contentType) || empty($title) || empty($content)) {
        sendResponse(false, 'content_type, title, and content are required', null, 400);
    }
    
    // Validate content type
    $allowedTypes = ['about', 'terms', 'privacy', 'cookie', 'help', 'faq', 'contact', 'news', 'announcement'];
    if (!in_array($contentType, $allowedTypes)) {
        sendResponse(false, 'Invalid content_type. Allowed types: ' . implode(', ', $allowedTypes), null, 400);
    }
    
    // Create content table if it doesn't exist
    $createTableSql = "
        CREATE TABLE IF NOT EXISTS content (
            id INT AUTO_INCREMENT PRIMARY KEY,
            content_type VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            meta_description TEXT,
            meta_keywords TEXT,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_content_type (content_type),
            INDEX idx_is_active (is_active)
        )
    ";
    $pdo->exec($createTableSql);
    
    // Check if content type already exists (for unique content types)
    $uniqueTypes = ['about', 'terms', 'privacy', 'cookie'];
    if (in_array($contentType, $uniqueTypes)) {
        $existingStmt = $pdo->prepare("SELECT id FROM content WHERE content_type = ?");
        $existingStmt->execute([$contentType]);
        if ($existingStmt->fetch()) {
            sendResponse(false, "Content type '$contentType' already exists. Use update instead.", null, 409);
        }
    }
    
    // Insert content
    $insertStmt = $pdo->prepare("
        INSERT INTO content (content_type, title, content, meta_description, meta_keywords, is_active, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $insertStmt->execute([
        $contentType,
        $title,
        $content,
        $metaDescription,
        $metaKeywords,
        $isActive
    ]);
    
    $contentId = $pdo->lastInsertId();
    
    // Get the created content item
    $selectStmt = $pdo->prepare("SELECT * FROM content WHERE id = ?");
    $selectStmt->execute([$contentId]);
    $contentItem = $selectStmt->fetch();
    
    sendResponse(true, 'Content item created successfully', [
        'content_item' => $contentItem,
        'content_id' => $contentId
    ], 201);

} catch (Exception $e) {
    sendResponse(false, 'Error creating content item: ' . $e->getMessage(), null, 500);
}
?>
