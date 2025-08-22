<?php
include_once 'db.php';

try {
    // Get all artworks with artist info and optional filters
    $artistId = $_GET['artist_id'] ?? '';
    $type = $_GET['type'] ?? '';
    $isAvailable = $_GET['is_available'] ?? '';
    $onAuction = $_GET['on_auction'] ?? '';
    $search = $_GET['search'] ?? '';
    $limit = (int)($_GET['limit'] ?? 50);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $sql = "SELECT a.artwork_id, a.title, a.description, a.price, a.dimensions, a.year, 
                   a.material, a.artwork_image, a.type, a.is_available, a.on_auction, a.created_at,
                   u.first_name, u.last_name, u.email, u.user_id as artist_id,
                   COUNT(ap.photo_id) as photo_count
            FROM artworks a 
            JOIN users u ON a.artist_id = u.user_id 
            LEFT JOIN artwork_photos ap ON a.artwork_id = ap.artwork_id
            WHERE 1=1";
    $params = [];
    
    if ($artistId) {
        $sql .= " AND a.artist_id = ?";
        $params[] = $artistId;
    }
    
    if ($type) {
        $sql .= " AND a.type = ?";
        $params[] = $type;
    }
    
    if ($isAvailable !== '') {
        $sql .= " AND a.is_available = ?";
        $params[] = $isAvailable;
    }
    
    if ($onAuction !== '') {
        $sql .= " AND a.on_auction = ?";
        $params[] = $onAuction;
    }
    
    if ($search) {
        $sql .= " AND (a.title LIKE ? OR a.description LIKE ? OR a.material LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql .= " GROUP BY a.artwork_id ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $artworks = $stmt->fetchAll();
    
    // Get total count
    $countSql = "SELECT COUNT(DISTINCT a.artwork_id) as total FROM artworks a 
                 JOIN users u ON a.artist_id = u.user_id WHERE 1=1";
    $countParams = [];
    $paramIndex = 0;
    
    if ($artistId) {
        $countSql .= " AND a.artist_id = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($type) {
        $countSql .= " AND a.type = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($isAvailable !== '') {
        $countSql .= " AND a.is_available = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($onAuction !== '') {
        $countSql .= " AND a.on_auction = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($search) {
        $countSql .= " AND (a.title LIKE ? OR a.description LIKE ? OR a.material LIKE ?)";
        $countParams[] = $params[$paramIndex++];
        $countParams[] = $params[$paramIndex++];
        $countParams[] = $params[$paramIndex++];
    }
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $total = $countStmt->fetch()['total'];
    
    sendResponse(true, 'Artworks retrieved successfully', [
        'artworks' => $artworks,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);

} catch (Exception $e) {
    sendResponse(false, 'Error retrieving artworks: ' . $e->getMessage(), null, 500);
}
?>
