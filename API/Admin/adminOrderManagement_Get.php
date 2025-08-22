<?php
include_once 'db.php';

try {
    // Get all orders with buyer and item details
    $status = $_GET['status'] ?? '';
    $buyerId = $_GET['buyer_id'] ?? '';
    $search = $_GET['search'] ?? '';
    $limit = (int)($_GET['limit'] ?? 50);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $sql = "SELECT o.id as order_id, o.total_amount, o.status, o.shipping_address, o.created_at,
                   u.user_id as buyer_id, u.first_name, u.last_name, u.email, u.phone,
                   COUNT(oi.id) as item_count,
                   GROUP_CONCAT(CONCAT(a.title, ' ($', oi.price, ')') SEPARATOR ', ') as items
            FROM orders o 
            JOIN users u ON o.buyer_id = u.user_id 
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN artworks a ON oi.artwork_id = a.artwork_id
            WHERE 1=1";
    $params = [];
    
    if ($status) {
        $sql .= " AND o.status = ?";
        $params[] = $status;
    }
    
    if ($buyerId) {
        $sql .= " AND o.buyer_id = ?";
        $params[] = $buyerId;
    }
    
    if ($search) {
        $sql .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR a.title LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql .= " GROUP BY o.id ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
    
    // Get total count
    $countSql = "SELECT COUNT(DISTINCT o.id) as total FROM orders o 
                 JOIN users u ON o.buyer_id = u.user_id 
                 LEFT JOIN order_items oi ON o.id = oi.order_id
                 LEFT JOIN artworks a ON oi.artwork_id = a.artwork_id
                 WHERE 1=1";
    $countParams = [];
    $paramIndex = 0;
    
    if ($status) {
        $countSql .= " AND o.status = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($buyerId) {
        $countSql .= " AND o.buyer_id = ?";
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
    
    sendResponse(true, 'Orders retrieved successfully', [
        'orders' => $orders,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);

} catch (Exception $e) {
    sendResponse(false, 'Error retrieving orders: ' . $e->getMessage(), null, 500);
}
?>
