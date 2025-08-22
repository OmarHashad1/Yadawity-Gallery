<?php
include_once 'db.php';

try {
    // Get all auctions with artwork and artist info
    $status = $_GET['status'] ?? '';
    $artistId = $_GET['artist_id'] ?? '';
    $search = $_GET['search'] ?? '';
    $limit = (int)($_GET['limit'] ?? 50);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $sql = "SELECT au.id, au.starting_bid, au.current_bid, au.start_time, au.end_time, 
                   au.status, au.created_at,
                   a.artwork_id, a.title as artwork_title, a.artwork_image, a.type,
                   u.user_id as artist_id, u.first_name, u.last_name, u.email,
                   COUNT(ab.id) as bid_count,
                   CASE 
                       WHEN au.end_time > NOW() AND au.status = 'active' THEN 'active'
                       WHEN au.end_time <= NOW() AND au.status = 'active' THEN 'ended'
                       ELSE au.status
                   END as current_status
            FROM auctions au 
            JOIN artworks a ON au.product_id = a.artwork_id
            JOIN users u ON au.artist_id = u.user_id 
            LEFT JOIN auction_bids ab ON au.id = ab.auction_id
            WHERE 1=1";
    $params = [];
    
    if ($status) {
        if ($status === 'active') {
            $sql .= " AND au.status = 'active' AND au.end_time > NOW()";
        } elseif ($status === 'ended') {
            $sql .= " AND (au.status = 'ended' OR (au.status = 'active' AND au.end_time <= NOW()))";
        } else {
            $sql .= " AND au.status = ?";
            $params[] = $status;
        }
    }
    
    if ($artistId) {
        $sql .= " AND au.artist_id = ?";
        $params[] = $artistId;
    }
    
    if ($search) {
        $sql .= " AND (a.title LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql .= " GROUP BY au.id ORDER BY au.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $auctions = $stmt->fetchAll();
    
    // Get total count
    $countSql = "SELECT COUNT(DISTINCT au.id) as total FROM auctions au 
                 JOIN artworks a ON au.product_id = a.artwork_id
                 JOIN users u ON au.artist_id = u.user_id WHERE 1=1";
    $countParams = [];
    $paramIndex = 0;
    
    if ($status) {
        if ($status === 'active') {
            $countSql .= " AND au.status = 'active' AND au.end_time > NOW()";
        } elseif ($status === 'ended') {
            $countSql .= " AND (au.status = 'ended' OR (au.status = 'active' AND au.end_time <= NOW()))";
        } else {
            $countSql .= " AND au.status = ?";
            $countParams[] = $params[$paramIndex++];
        }
    }
    
    if ($artistId) {
        $countSql .= " AND au.artist_id = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($search) {
        $countSql .= " AND (a.title LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
        $countParams[] = $params[$paramIndex++];
        $countParams[] = $params[$paramIndex++];
        $countParams[] = $params[$paramIndex++];
    }
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $total = $countStmt->fetch()['total'];
    
    sendResponse(true, 'Auctions retrieved successfully', [
        'auctions' => $auctions,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);

} catch (Exception $e) {
    sendResponse(false, 'Error retrieving auctions: ' . $e->getMessage(), null, 500);
}
?>
