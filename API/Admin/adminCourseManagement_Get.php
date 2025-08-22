<?php
include_once 'db.php';

try {
    // Get all courses with instructor info
    $artistId = $_GET['artist_id'] ?? '';
    $difficulty = $_GET['difficulty'] ?? '';
    $courseType = $_GET['course_type'] ?? '';
    $isPublished = $_GET['is_published'] ?? '';
    $search = $_GET['search'] ?? '';
    $limit = (int)($_GET['limit'] ?? 50);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $sql = "SELECT c.course_id, c.title, c.rate, c.duration_date, c.description, c.requirement,
                   c.difficulty, c.course_type, c.price, c.thumbnail, c.is_published, c.created_at,
                   u.user_id as artist_id, u.first_name, u.last_name, u.email,
                   COUNT(ce.id) as enrollment_count
            FROM courses c 
            JOIN users u ON c.artist_id = u.user_id 
            LEFT JOIN course_enrollments ce ON c.course_id = ce.course_id AND ce.is_active = 1
            WHERE 1=1";
    $params = [];
    
    if ($artistId) {
        $sql .= " AND c.artist_id = ?";
        $params[] = $artistId;
    }
    
    if ($difficulty) {
        $sql .= " AND c.difficulty = ?";
        $params[] = $difficulty;
    }
    
    if ($courseType) {
        $sql .= " AND c.course_type = ?";
        $params[] = $courseType;
    }
    
    if ($isPublished !== '') {
        $sql .= " AND c.is_published = ?";
        $params[] = $isPublished;
    }
    
    if ($search) {
        $sql .= " AND (c.title LIKE ? OR c.description LIKE ? OR c.requirement LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql .= " GROUP BY c.course_id ORDER BY c.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $courses = $stmt->fetchAll();
    
    // Get total count
    $countSql = "SELECT COUNT(DISTINCT c.course_id) as total FROM courses c 
                 JOIN users u ON c.artist_id = u.user_id WHERE 1=1";
    $countParams = [];
    $paramIndex = 0;
    
    if ($artistId) {
        $countSql .= " AND c.artist_id = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($difficulty) {
        $countSql .= " AND c.difficulty = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($courseType) {
        $countSql .= " AND c.course_type = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($isPublished !== '') {
        $countSql .= " AND c.is_published = ?";
        $countParams[] = $params[$paramIndex++];
    }
    
    if ($search) {
        $countSql .= " AND (c.title LIKE ? OR c.description LIKE ? OR c.requirement LIKE ?)";
        $countParams[] = $params[$paramIndex++];
        $countParams[] = $params[$paramIndex++];
        $countParams[] = $params[$paramIndex++];
    }
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $total = $countStmt->fetch()['total'];
    
    sendResponse(true, 'Courses retrieved successfully', [
        'courses' => $courses,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);

} catch (Exception $e) {
    sendResponse(false, 'Error retrieving courses: ' . $e->getMessage(), null, 500);
}
?>
