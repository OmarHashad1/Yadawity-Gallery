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
            // Get admin user permissions and roles
            $admins = $pdo->query("
                SELECT user_id, email, first_name, last_name, is_active, created_at
                FROM users 
                WHERE user_type = 'admin'
                ORDER BY created_at DESC
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Admin permissions (in a real system, this would come from a permissions table)
            $permissions = [
                'user_management' => ['view', 'create', 'edit', 'delete'],
                'artwork_management' => ['view', 'approve', 'reject', 'delete'],
                'order_management' => ['view', 'update_status', 'refund'],
                'auction_management' => ['view', 'create', 'edit', 'cancel'],
                'course_management' => ['view', 'approve', 'reject'],
                'gallery_management' => ['view', 'approve', 'reject'],
                'financial_reports' => ['view', 'export'],
                'system_settings' => ['view', 'edit'],
                'user_support' => ['view', 'respond'],
                'content_management' => ['view', 'edit', 'publish']
            ];
            
            // Admin activity logs
            $adminActivity = $pdo->query("
                SELECT uls.login_time, uls.logout_time, uls.is_active,
                       u.first_name, u.last_name, u.email
                FROM user_login_sessions uls
                JOIN users u ON uls.user_id = u.user_id
                WHERE u.user_type = 'admin'
                ORDER BY uls.login_time DESC
                LIMIT 50
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'admins' => $admins,
                    'permissions' => $permissions,
                    'admin_activity' => $adminActivity
                ]
            ]);
            break;
            
        case 'POST':
            // Create new admin user
            $input = json_decode(file_get_contents('php://input'), true);
            $email = $input['email'];
            $firstName = $input['first_name'];
            $lastName = $input['last_name'];
            $password = password_hash($input['password'], PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("
                INSERT INTO users (email, password, first_name, last_name, user_type) 
                VALUES (?, ?, ?, ?, 'admin')
            ");
            $result = $stmt->execute([$email, $password, $firstName, $lastName]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Admin user created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create admin user']);
            }
            break;
            
        case 'PUT':
            // Update admin permissions or status
            $input = json_decode(file_get_contents('php://input'), true);
            $adminId = $input['admin_id'];
            $isActive = $input['is_active'];
            
            $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE user_id = ? AND user_type = 'admin'");
            $result = $stmt->execute([$isActive, $adminId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Admin status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update admin status']);
            }
            break;
            
        case 'DELETE':
            // Deactivate admin user
            $input = json_decode(file_get_contents('php://input'), true);
            $adminId = $input['admin_id'];
            
            $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE user_id = ? AND user_type = 'admin'");
            $result = $stmt->execute([$adminId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Admin user deactivated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to deactivate admin user']);
            }
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
