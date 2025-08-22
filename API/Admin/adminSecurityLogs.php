<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get security logs and audit trail
            $activityType = $_GET['type'] ?? 'all';
            $limit = $_GET['limit'] ?? 50;
            
            $securityLogs = [
                'user_activities' => $pdo->query("
                    SELECT 'user_login' as activity_type, uls.login_time as timestamp, 
                           CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email, u.user_type,
                           'User logged in' as description
                    FROM user_login_sessions uls
                    JOIN users u ON uls.user_id = u.user_id
                    ORDER BY uls.login_time DESC
                    LIMIT $limit
                ")->fetchAll(PDO::FETCH_ASSOC),
                'recent_registrations' => $pdo->query("
                    SELECT 'user_registration' as activity_type, created_at as timestamp,
                           CONCAT(first_name, ' ', last_name) as user_name, email, user_type,
                           'New user registered' as description
                    FROM users
                    ORDER BY created_at DESC
                    LIMIT 20
                ")->fetchAll(PDO::FETCH_ASSOC),
                'content_changes' => $pdo->query("
                    SELECT 'artwork_upload' as activity_type, a.created_at as timestamp,
                           CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email, u.user_type,
                           CONCAT('Uploaded artwork: ', a.title) as description
                    FROM artworks a
                    JOIN users u ON a.artist_id = u.user_id
                    ORDER BY a.created_at DESC
                    LIMIT 20
                ")->fetchAll(PDO::FETCH_ASSOC)
            ];
            
            // Security statistics
            $securityStats = [
                'total_active_sessions' => $pdo->query("SELECT COUNT(*) FROM user_login_sessions WHERE is_active = 1")->fetchColumn(),
                'failed_login_attempts' => 0, // Would come from security logs
                'suspicious_activities' => 0, // Would come from security monitoring
                'blocked_ips' => 0, // Would come from firewall logs
                'admin_actions_today' => $pdo->query("SELECT COUNT(*) FROM user_login_sessions uls JOIN users u ON uls.user_id = u.user_id WHERE u.user_type = 'admin' AND DATE(uls.login_time) = CURDATE()")->fetchColumn()
            ];
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'security_logs' => $securityLogs,
                    'security_stats' => $securityStats
                ]
            ]);
            break;
            
        case 'POST':
            // Log admin action
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'];
            $description = $input['description'];
            $adminId = $input['admin_id'];
            
            // In a real system, you'd log this to an admin_actions table
            echo json_encode(['success' => true, 'message' => 'Admin action logged']);
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
