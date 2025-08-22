<?php
include_once 'db.php';

try {
    // Get content items (about page, terms, privacy policy, etc.)
    $contentType = $_GET['content_type'] ?? '';
    $isActive = $_GET['is_active'] ?? '';
    $search = $_GET['search'] ?? '';
    $limit = (int)($_GET['limit'] ?? 50);
    $offset = (int)($_GET['offset'] ?? 0);
    
    // For this implementation, we'll assume a content table structure
    // If no content table exists, we'll return default content types
    
    try {
        $sql = "SELECT * FROM content WHERE 1=1";
        $params = [];
        
        if ($contentType) {
            $sql .= " AND content_type = ?";
            $params[] = $contentType;
        }
        
        if ($isActive !== '') {
            $sql .= " AND is_active = ?";
            $params[] = $isActive;
        }
        
        if ($search) {
            $sql .= " AND (title LIKE ? OR content LIKE ? OR content_type LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY content_type, created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $contentItems = $stmt->fetchAll();
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM content WHERE 1=1";
        $countParams = array_slice($params, 0, -2); // Remove limit and offset
        
        if ($contentType) {
            $countSql .= " AND content_type = ?";
        }
        if ($isActive !== '') {
            $countSql .= " AND is_active = ?";
        }
        if ($search) {
            $countSql .= " AND (title LIKE ? OR content LIKE ? OR content_type LIKE ?)";
        }
        
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($countParams);
        $total = $countStmt->fetch()['total'];
        
    } catch (Exception $e) {
        // If content table doesn't exist, return default content structure
        $contentItems = [
            [
                'id' => 1,
                'content_type' => 'about',
                'title' => 'About Us',
                'content' => 'Default about content',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'id' => 2,
                'content_type' => 'terms',
                'title' => 'Terms of Service',
                'content' => 'Default terms content',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'id' => 3,
                'content_type' => 'privacy',
                'title' => 'Privacy Policy',
                'content' => 'Default privacy content',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ],
            [
                'id' => 4,
                'content_type' => 'cookie',
                'title' => 'Cookie Policy',
                'content' => 'Default cookie policy content',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ]
        ];
        $total = count($contentItems);
    }
    
    // Get content statistics
    $contentTypes = ['about', 'terms', 'privacy', 'cookie', 'help', 'faq'];
    $summary = [
        'total_content_items' => $total,
        'content_types' => count($contentTypes),
        'active_items' => count(array_filter($contentItems, function($item) {
            return $item['is_active'] == 1;
        })),
        'inactive_items' => count(array_filter($contentItems, function($item) {
            return $item['is_active'] == 0;
        }))
    ];
    
    sendResponse(true, 'Content items retrieved successfully', [
        'content_items' => $contentItems,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset,
        'summary' => $summary,
        'available_types' => $contentTypes
    ]);

} catch (Exception $e) {
    sendResponse(false, 'Error retrieving content items: ' . $e->getMessage(), null, 500);
}
?>
