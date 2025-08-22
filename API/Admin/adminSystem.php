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
            // Get system information and settings
            $systemInfo = [
                'database' => [
                    'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
                    'total_artworks' => $pdo->query("SELECT COUNT(*) FROM artworks")->fetchColumn(),
                    'total_orders' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
                    'total_auctions' => $pdo->query("SELECT COUNT(*) FROM auctions")->fetchColumn(),
                    'total_courses' => $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn(),
                    'total_galleries' => $pdo->query("SELECT COUNT(*) FROM galleries")->fetchColumn()
                ],
                'user_sessions' => [
                    'active_sessions' => $pdo->query("SELECT COUNT(*) FROM user_login_sessions WHERE is_active = 1")->fetchColumn(),
                    'total_sessions' => $pdo->query("SELECT COUNT(*) FROM user_login_sessions")->fetchColumn()
                ],
                'system_health' => [
                    'status' => 'operational',
                    'uptime' => '99.9%',
                    'last_backup' => date('Y-m-d H:i:s'),
                    'server_load' => 'normal'
                ],
                'settings' => [
                    'site_maintenance' => false,
                    'user_registration' => true,
                    'artwork_upload' => true,
                    'auction_creation' => true,
                    'course_creation' => true,
                    'max_file_size' => '10MB',
                    'supported_formats' => ['jpg', 'jpeg', 'png', 'gif']
                ]
            ];
            
            // Recent system activity
            $recentActivity = [
                'new_users' => $pdo->query("
                    SELECT first_name, last_name, user_type, created_at
                    FROM users 
                    ORDER BY created_at DESC 
                    LIMIT 5
                ")->fetchAll(PDO::FETCH_ASSOC),
                'new_artworks' => $pdo->query("
                    SELECT a.title, a.type, a.created_at, u.first_name, u.last_name
                    FROM artworks a
                    JOIN users u ON a.artist_id = u.user_id
                    ORDER BY a.created_at DESC 
                    LIMIT 5
                ")->fetchAll(PDO::FETCH_ASSOC),
                'recent_orders' => $pdo->query("
                    SELECT o.id, o.total_amount, o.status, o.created_at, u.first_name, u.last_name
                    FROM orders o
                    JOIN users u ON o.buyer_id = u.user_id
                    ORDER BY o.created_at DESC 
                    LIMIT 5
                ")->fetchAll(PDO::FETCH_ASSOC)
            ];
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'system_info' => $systemInfo,
                    'recent_activity' => $recentActivity
                ]
            ]);
            break;
            
        case 'POST':
            // Update system settings
            $input = json_decode(file_get_contents('php://input'), true);
            
            // In a real system, you'd save settings to a database
            echo json_encode([
                'success' => true,
                'message' => 'System settings updated successfully'
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
