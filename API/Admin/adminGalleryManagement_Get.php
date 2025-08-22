<?php
include_once 'db.php';

try {
    // Get all galleries with artist info
    $artistId = $_GET['artist_id'] ?? '';
    $galleryType = $_GET['gallery_type'] ?? '';
    $isActive = $_GET['is_active'] ?? '';
    $search = $_GET['search'] ?? '';
    $limit = (int)($_GET['limit'] ?? 50);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $sql = "SELECT g.gallery_id, g.title, g.description, g.gallery_type, g.price, g.address, 
                   g.city, g.phone, g.start_date, g.duration, g.is_active, g.created_at,
                   u.user_id as artist_id, u.first_name, u.last_name, u.email,
                   CASE 
                       WHEN g.gallery_type = 'virtual' THEN 'Virtual Gallery'
                       WHEN g.gallery_type = 'physical' THEN 'Physical Exhibition'
                       ELSE g.gallery_type
                   END as display_type,
                   CASE 
                       WHEN g.gallery_type = 'virtual' THEN CONCAT(g.duration, ' days access')
                       WHEN g.gallery_type = 'physical' THEN CONCAT(g.duration, ' days exhibition')
                       ELSE CONCAT(g.duration, ' days')
                   END as duration_display
            FROM galleries g 
            JOIN users u ON g.artist_id = u.user_id 
            WHERE 1=1";
    $params = [];
    
    if ($artistId) {
        $sql .= " AND g.artist_id = ?";
        $params[] = $artistId;
    }
    
    if ($galleryType) {
        $sql .= " AND g.gallery_type = ?";
        $params[] = $galleryType;
    }
    
    if ($isActive !== '') {
        $sql .= " AND g.is_active = ?";
        $params[] = $isActive;
    }
    
    if ($search) {
        $sql .= " AND (g.title LIKE ? OR g.description LIKE ? OR g.city LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql .= " ORDER BY g.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $galleries = $stmt->fetchAll();
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM galleries g 
                 JOIN users u ON g.artist_id = u.user_id WHERE 1=1";
    $countParams = [];
    $paramIndex = 0;
    
    if ($artistId) {
        $countSql .= " AND g.artist_id = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($galleryType) {
        $countSql .= " AND g.gallery_type = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($isActive !== '') {
        $countSql .= " AND g.is_active = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($search) {
        $countSql .= " AND (g.title LIKE ? OR g.description LIKE ? OR g.city LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
        $countParams[] = $params[$paramIndex++];
        $countParams[] = $params[$paramIndex++];
        $countParams[] = $params[$paramIndex++];
        $countParams[] = $params[$paramIndex++];
        $countParams[] = $params[$paramIndex++];
    }
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $total = $countStmt->fetch()['total'];
    
    sendResponse(true, 'Galleries retrieved successfully', [
        'galleries' => $galleries,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);

} catch (Exception $e) {
    sendResponse(false, 'Error retrieving galleries: ' . $e->getMessage(), null, 500);
}
?>
