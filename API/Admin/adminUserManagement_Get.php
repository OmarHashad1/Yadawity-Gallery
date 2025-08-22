<?php
include_once 'db.php';

try {
    // Get all users with optional filters
    $userType = $_GET['user_type'] ?? '';
    $isActive = $_GET['is_active'] ?? '';
    $search = $_GET['search'] ?? '';
    $limit = (int)($_GET['limit'] ?? 50);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $sql = "SELECT user_id, email, first_name, last_name, phone, user_type, profile_picture, 
                   is_active, location, created_at FROM users WHERE 1=1";
    $params = [];
    
    if ($userType) {
        $sql .= " AND user_type = ?";
        $params[] = $userType;
    }
    
    if ($isActive !== '') {
        $sql .= " AND is_active = ?";
        $params[] = $isActive;
    }
    
    if ($search) {
        $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll();
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM users WHERE 1=1";
    $countParams = [];
    $paramIndex = 0;
    
    if ($userType) {
        $countSql .= " AND user_type = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($isActive !== '') {
        $countSql .= " AND is_active = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($search) {
        $countSql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
        $countParams[] = $params[$paramIndex++];
        $countParams[] = $params[$paramIndex++];
        $countParams[] = $params[$paramIndex++];
    }
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $total = $countStmt->fetch()['total'];
    
    sendResponse(true, 'Users retrieved successfully', [
        'users' => $users,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);

} catch (Exception $e) {
    sendResponse(false, 'Error retrieving users: ' . $e->getMessage(), null, 500);
}
?>
