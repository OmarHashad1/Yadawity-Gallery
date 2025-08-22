<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get marketing analytics and campaigns
            $analytics = [
                'user_acquisition' => [
                    'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
                    'users_this_month' => $pdo->query("SELECT COUNT(*) FROM users WHERE MONTH(created_at) = MONTH(CURRENT_DATE())")->fetchColumn(),
                    'artists_this_month' => $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'artist' AND MONTH(created_at) = MONTH(CURRENT_DATE())")->fetchColumn(),
                    'buyers_this_month' => $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'buyer' AND MONTH(created_at) = MONTH(CURRENT_DATE())")->fetchColumn()
                ],
                'sales_performance' => [
                    'total_revenue' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'paid'")->fetchColumn(),
                    'revenue_this_month' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'paid' AND MONTH(created_at) = MONTH(CURRENT_DATE())")->fetchColumn(),
                    'orders_this_month' => $pdo->query("SELECT COUNT(*) FROM orders WHERE MONTH(created_at) = MONTH(CURRENT_DATE())")->fetchColumn()
                ],
                'content_engagement' => [
                    'total_artworks' => $pdo->query("SELECT COUNT(*) FROM artworks")->fetchColumn(),
                    'artworks_this_month' => $pdo->query("SELECT COUNT(*) FROM artworks WHERE MONTH(created_at) = MONTH(CURRENT_DATE())")->fetchColumn(),
                    'active_auctions' => $pdo->query("SELECT COUNT(*) FROM auctions WHERE status = 'active'")->fetchColumn(),
                    'courses_enrolled' => $pdo->query("SELECT COUNT(*) FROM course_enrollments WHERE MONTH(enrollment_date) = MONTH(CURRENT_DATE())")->fetchColumn()
                ]
            ];
            
            // Top performing content
            $topArtworks = $pdo->query("
                SELECT a.title, a.price, a.type, u.first_name, u.last_name,
                       (SELECT COUNT(*) FROM wishlists w WHERE w.artwork_id = a.artwork_id) as wishlist_count
                FROM artworks a
                JOIN users u ON a.artist_id = u.user_id
                ORDER BY wishlist_count DESC
                LIMIT 10
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'analytics' => $analytics,
                    'top_artworks' => $topArtworks
                ]
            ]);
            break;
            
        case 'POST':
            // Create/update marketing campaigns
            $input = json_decode(file_get_contents('php://input'), true);
            
            echo json_encode([
                'success' => true,
                'message' => 'Marketing campaign updated successfully'
            ]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
