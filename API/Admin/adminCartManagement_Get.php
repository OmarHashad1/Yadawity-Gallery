<?php
include_once 'db.php';

try {
    // Get all cart items with user and artwork details
    $userId = $_GET['user_id'] ?? '';
    $isActive = $_GET['is_active'] ?? '';
    $search = $_GET['search'] ?? '';
    $limit = (int)($_GET['limit'] ?? 50);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $sql = "SELECT c.id as cart_id, c.quantity, c.added_date, c.is_active,
                   u.user_id, u.first_name, u.last_name, u.email,
                   a.artwork_id, a.title as artwork_title, a.price, a.type, a.artwork_image,
                   artist.first_name as artist_first_name, artist.last_name as artist_last_name,
                   (a.price * c.quantity) as total_price
            FROM cart c
            JOIN users u ON c.user_id = u.user_id
            JOIN artworks a ON c.artwork_id = a.artwork_id
            JOIN users artist ON a.artist_id = artist.user_id
            WHERE 1=1";
    $params = [];
    
    if ($userId) {
        $sql .= " AND c.user_id = ?";
        $params[] = $userId;
    }
    
    if ($isActive !== '') {
        $sql .= " AND c.is_active = ?";
        $params[] = $isActive;
    }
    
    if ($search) {
        $sql .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR a.title LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql .= " ORDER BY c.added_date DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $cartItems = $stmt->fetchAll();
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM cart c
                 JOIN users u ON c.user_id = u.user_id
                 JOIN artworks a ON c.artwork_id = a.artwork_id
                 WHERE 1=1";
    $countParams = [];
    $paramIndex = 0;
    
    if ($userId) {
        $countSql .= " AND c.user_id = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($isActive !== '') {
        $countSql .= " AND c.is_active = ?";
        $countParams[] = $params[$paramIndex++];
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
            COUNT(DISTINCT c.user_id) as unique_users,
            COUNT(*) as total_items,
            SUM(a.price * c.quantity) as total_value,
            AVG(a.price * c.quantity) as avg_value
        FROM cart c
        JOIN artworks a ON c.artwork_id = a.artwork_id
        WHERE c.is_active = 1
    ");
    $summaryStmt->execute();
    $summary = $summaryStmt->fetch();
    
    sendResponse(true, 'Cart items retrieved successfully', [
        'cart_items' => $cartItems,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset,
        'summary' => [
            'unique_users' => (int)$summary['unique_users'],
            'total_items' => (int)$summary['total_items'],
            'total_value' => (float)$summary['total_value'],
            'average_value' => (float)$summary['avg_value']
        ]
    ]);

} catch (Exception $e) {
    sendResponse(false, 'Error retrieving cart items: ' . $e->getMessage(), null, 500);
}
?>
