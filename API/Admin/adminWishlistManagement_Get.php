<?php
include_once 'db.php';

try {
    // Get all wishlist items with user and artwork details
    $userId = $_GET['user_id'] ?? '';
    $isActive = $_GET['is_active'] ?? '';
    $hasPriceAlert = $_GET['has_price_alert'] ?? '';
    $search = $_GET['search'] ?? '';
    $limit = (int)($_GET['limit'] ?? 50);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $sql = "SELECT w.id as wishlist_id, w.price_alert, w.is_active, w.created_at,
                   u.user_id, u.first_name, u.last_name, u.email,
                   a.artwork_id, a.title as artwork_title, a.price, a.type, a.artwork_image, a.is_available,
                   artist.first_name as artist_first_name, artist.last_name as artist_last_name,
                   CASE 
                       WHEN w.price_alert IS NOT NULL AND a.price <= w.price_alert THEN 'triggered'
                       WHEN w.price_alert IS NOT NULL THEN 'active'
                       ELSE 'no_alert'
                   END as alert_status
            FROM wishlists w
            JOIN users u ON w.user_id = u.user_id
            JOIN artworks a ON w.artwork_id = a.artwork_id
            JOIN users artist ON a.artist_id = artist.user_id
            WHERE 1=1";
    $params = [];
    
    if ($userId) {
        $sql .= " AND w.user_id = ?";
        $params[] = $userId;
    }
    
    if ($isActive !== '') {
        $sql .= " AND w.is_active = ?";
        $params[] = $isActive;
    }
    
    if ($hasPriceAlert !== '') {
        if ($hasPriceAlert == '1') {
            $sql .= " AND w.price_alert IS NOT NULL";
        } else {
            $sql .= " AND w.price_alert IS NULL";
        }
    }
    
    if ($search) {
        $sql .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR a.title LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql .= " ORDER BY w.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $wishlistItems = $stmt->fetchAll();
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM wishlists w
                 JOIN users u ON w.user_id = u.user_id
                 JOIN artworks a ON w.artwork_id = a.artwork_id
                 WHERE 1=1";
    $countParams = [];
    $paramIndex = 0;
    
    if ($userId) {
        $countSql .= " AND w.user_id = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($isActive !== '') {
        $countSql .= " AND w.is_active = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($hasPriceAlert !== '') {
        if ($hasPriceAlert == '1') {
            $countSql .= " AND w.price_alert IS NOT NULL";
        } else {
            $countSql .= " AND w.price_alert IS NULL";
        }
    }
    
    if ($search) {
        $countSql .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR a.title LIKE ?)";
        $countParams[] = $params[$paramIndex++];
        $countParams[] = $params[$paramIndex++];
        $countParams[] = $params[$paramIndex++];
        $countParams[] = $params[$paramIndex++];
    }
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $total = $countStmt->fetch()['total'];
    
    // Calculate summary statistics
    $summaryStmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT w.user_id) as unique_users,
            COUNT(*) as total_items,
            COUNT(CASE WHEN w.price_alert IS NOT NULL THEN 1 END) as items_with_alerts,
            COUNT(CASE WHEN w.price_alert IS NOT NULL AND a.price <= w.price_alert THEN 1 END) as triggered_alerts,
            AVG(a.price) as avg_artwork_price
        FROM wishlists w
        JOIN artworks a ON w.artwork_id = a.artwork_id
        WHERE w.is_active = 1
    ");
    $summaryStmt->execute();
    $summary = $summaryStmt->fetch();
    
    sendResponse(true, 'Wishlist items retrieved successfully', [
        'wishlist_items' => $wishlistItems,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset,
        'summary' => [
            'unique_users' => (int)$summary['unique_users'],
            'total_items' => (int)$summary['total_items'],
            'items_with_alerts' => (int)$summary['items_with_alerts'],
            'triggered_alerts' => (int)$summary['triggered_alerts'],
            'avg_artwork_price' => (float)$summary['avg_artwork_price']
        ]
    ]);

} catch (Exception $e) {
    sendResponse(false, 'Error retrieving wishlist items: ' . $e->getMessage(), null, 500);
}
?>
