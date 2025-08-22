<?php
include_once 'db.php';

try {
    // Since the database doesn't have a support_tickets table, we'll create a simple communication system
    // using the existing tables or create a basic notification system
    
    $type = $_GET['type'] ?? 'messages'; // messages, notifications, announcements
    $status = $_GET['status'] ?? '';
    $search = $_GET['search'] ?? '';
    $limit = (int)($_GET['limit'] ?? 50);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $communications = [];
    
    if ($type === 'messages') {
        // Get user reviews as communication messages
        $sql = "SELECT ar.id, ar.rating, ar.feedback as message, ar.created_at,
                       u1.user_id as from_user_id, u1.first_name as from_first_name, 
                       u1.last_name as from_last_name, u1.email as from_email,
                       u2.user_id as to_user_id, u2.first_name as to_first_name, 
                       u2.last_name as to_last_name, u2.email as to_email,
                       a.title as artwork_title,
                       'review' as message_type
                FROM artist_reviews ar
                JOIN users u1 ON ar.user_id = u1.user_id
                JOIN users u2 ON ar.artist_id = u2.user_id
                LEFT JOIN artworks a ON ar.artwork_id = a.artwork_id
                WHERE 1=1";
        $params = [];
        
        if ($search) {
            $sql .= " AND (ar.feedback LIKE ? OR u1.first_name LIKE ? OR u1.last_name LIKE ? OR u2.first_name LIKE ? OR u2.last_name LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY ar.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $communications = $stmt->fetchAll();
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM artist_reviews ar
                     JOIN users u1 ON ar.user_id = u1.user_id
                     JOIN users u2 ON ar.artist_id = u2.user_id
                     WHERE 1=1";
        $countParams = [];
        
        if ($search) {
            $countSql .= " AND (ar.feedback LIKE ? OR u1.first_name LIKE ? OR u1.last_name LIKE ? OR u2.first_name LIKE ? OR u2.last_name LIKE ?)";
            $countParams = array_slice($params, 0, 5);
        }
        
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($countParams);
        $total = $countStmt->fetch()['total'];
        
    } elseif ($type === 'notifications') {
        // Create system notifications based on recent activities
        $notifications = [];
        
        // New user registrations (last 7 days)
        $newUsersStmt = $pdo->prepare("
            SELECT user_id, CONCAT(first_name, ' ', last_name) as name, email, user_type, created_at,
                   'new_user' as type, 'New user registration' as title
            FROM users 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ORDER BY created_at DESC
            LIMIT 20
        ");
        $newUsersStmt->execute();
        $newUsers = $newUsersStmt->fetchAll();
        
        // New artworks (last 7 days)
        $newArtworksStmt = $pdo->prepare("
            SELECT a.artwork_id, a.title, a.price, a.created_at,
                   CONCAT(u.first_name, ' ', u.last_name) as artist_name,
                   'new_artwork' as type, 'New artwork added' as title
            FROM artworks a
            JOIN users u ON a.artist_id = u.user_id
            WHERE a.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ORDER BY a.created_at DESC
            LIMIT 20
        ");
        $newArtworksStmt->execute();
        $newArtworks = $newArtworksStmt->fetchAll();
        
        // Recent orders (last 7 days)
        $recentOrdersStmt = $pdo->prepare("
            SELECT o.id, o.total_amount, o.status, o.created_at,
                   CONCAT(u.first_name, ' ', u.last_name) as buyer_name,
                   'new_order' as type, 'New order placed' as title
            FROM orders o
            JOIN users u ON o.buyer_id = u.user_id
            WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ORDER BY o.created_at DESC
            LIMIT 20
        ");
        $recentOrdersStmt->execute();
        $recentOrders = $recentOrdersStmt->fetchAll();
        
        // Combine all notifications
        $communications = array_merge($newUsers, $newArtworks, $recentOrders);
        
        // Sort by created_at
        usort($communications, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        // Apply limit and offset
        $total = count($communications);
        $communications = array_slice($communications, $offset, $limit);
        
    } elseif ($type === 'announcements') {
        // System announcements (using gallery data as example announcements)
        $sql = "SELECT g.gallery_id as id, g.title, g.description as content, 
                       g.start_date as created_at, g.gallery_type as category,
                       CONCAT(u.first_name, ' ', u.last_name) as created_by,
                       g.is_active as status,
                       'gallery_announcement' as type
                FROM galleries g
                JOIN users u ON g.artist_id = u.user_id
                WHERE 1=1";
        $params = [];
        
        if ($status !== '') {
            $sql .= " AND g.is_active = ?";
            $params[] = $status;
        }
        
        if ($search) {
            $sql .= " AND (g.title LIKE ? OR g.description LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY g.start_date DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $communications = $stmt->fetchAll();
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM galleries g WHERE 1=1";
        $countParams = [];
        $paramIndex = 0;
        
        if ($status !== '') {
            $countSql .= " AND g.is_active = ?";
            $countParams[] = $params[$paramIndex++];
        }
        
        if ($search) {
            $countSql .= " AND (g.title LIKE ? OR g.description LIKE ?)";
            $countParams[] = $params[$paramIndex++];
            $countParams[] = $params[$paramIndex++];
        }
        
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($countParams);
        $total = $countStmt->fetch()['total'];
    }
    
    sendResponse(true, 'Communication data retrieved successfully', [
        'communications' => $communications,
        'type' => $type,
        'total' => $total ?? count($communications),
        'limit' => $limit,
        'offset' => $offset
    ]);

} catch (Exception $e) {
    sendResponse(false, 'Error retrieving communication data: ' . $e->getMessage(), null, 500);
}
?>
