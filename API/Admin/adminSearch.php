<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Search for users, artworks, orders, etc.
    $query = $_GET['query'] ?? '';
    $type = $_GET['type'] ?? 'all'; // all, users, artworks, orders, auctions, courses
    
    if (empty($query)) {
        echo json_encode(['success' => false, 'message' => 'Search query required']);
        exit;
    }
    
    $results = [];
    
    if ($type === 'all' || $type === 'users') {
        $users = $pdo->prepare("
            SELECT 'user' as type, user_id as id, 
                   CONCAT(first_name, ' ', last_name) as title,
                   email as subtitle, user_type as category
            FROM users 
            WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?
            LIMIT 10
        ");
        $searchTerm = "%$query%";
        $users->execute([$searchTerm, $searchTerm, $searchTerm]);
        $results['users'] = $users->fetchAll(PDO::FETCH_ASSOC);
    }
    
    if ($type === 'all' || $type === 'artworks') {
        $artworks = $pdo->prepare("
            SELECT 'artwork' as type, a.artwork_id as id, 
                   a.title as title,
                   CONCAT('$', a.price, ' - ', a.type) as subtitle,
                   CONCAT(u.first_name, ' ', u.last_name) as category
            FROM artworks a
            JOIN users u ON a.artist_id = u.user_id
            WHERE a.title LIKE ? OR a.description LIKE ? OR a.type LIKE ?
            LIMIT 10
        ");
        $artworks->execute([$searchTerm, $searchTerm, $searchTerm]);
        $results['artworks'] = $artworks->fetchAll(PDO::FETCH_ASSOC);
    }
    
    if ($type === 'all' || $type === 'orders') {
        $orders = $pdo->prepare("
            SELECT 'order' as type, o.id as id,
                   CONCAT('Order #', o.id) as title,
                   CONCAT('$', o.total_amount, ' - ', o.status) as subtitle,
                   CONCAT(u.first_name, ' ', u.last_name) as category
            FROM orders o
            JOIN users u ON o.buyer_id = u.user_id
            WHERE o.id LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?
            LIMIT 10
        ");
        $orders->execute(["%$query%", $searchTerm, $searchTerm]);
        $results['orders'] = $orders->fetchAll(PDO::FETCH_ASSOC);
    }
    
    if ($type === 'all' || $type === 'courses') {
        $courses = $pdo->prepare("
            SELECT 'course' as type, c.course_id as id,
                   c.title as title,
                   CONCAT('$', c.price, ' - ', c.difficulty) as subtitle,
                   CONCAT(u.first_name, ' ', u.last_name) as category
            FROM courses c
            JOIN users u ON c.artist_id = u.user_id
            WHERE c.title LIKE ? OR c.description LIKE ?
            LIMIT 10
        ");
        $courses->execute([$searchTerm, $searchTerm]);
        $results['courses'] = $courses->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        'success' => true,
        'data' => $results
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
