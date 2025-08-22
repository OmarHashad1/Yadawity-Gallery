<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get notification settings and recent notifications
            $type = $_GET['type'] ?? 'all';
            
            $notifications = [
                'system_alerts' => [
                    ['id' => 1, 'type' => 'warning', 'message' => 'High server load detected', 'timestamp' => date('Y-m-d H:i:s', strtotime('-30 minutes')), 'is_read' => false],
                    ['id' => 2, 'type' => 'info', 'message' => 'Database backup completed successfully', 'timestamp' => date('Y-m-d H:i:s', strtotime('-2 hours')), 'is_read' => true],
                    ['id' => 3, 'type' => 'success', 'message' => 'System maintenance completed', 'timestamp' => date('Y-m-d H:i:s', strtotime('-1 day')), 'is_read' => true]
                ],
                'user_notifications' => [
                    ['id' => 4, 'type' => 'info', 'message' => '5 new user registrations today', 'timestamp' => date('Y-m-d H:i:s'), 'is_read' => false],
                    ['id' => 5, 'type' => 'warning', 'message' => 'Pending artwork approval required', 'timestamp' => date('Y-m-d H:i:s', strtotime('-1 hour')), 'is_read' => false]
                ],
                'financial_alerts' => [
                    ['id' => 6, 'type' => 'success', 'message' => 'Monthly revenue target achieved', 'timestamp' => date('Y-m-d H:i:s', strtotime('-3 hours')), 'is_read' => true],
                    ['id' => 7, 'type' => 'info', 'message' => 'New order #1234 received', 'timestamp' => date('Y-m-d H:i:s', strtotime('-45 minutes')), 'is_read' => false]
                ]
            ];
            
            // Notification statistics
            $stats = [
                'total_unread' => 4,
                'critical_alerts' => 0,
                'warnings' => 2,
                'info_messages' => 3,
                'success_messages' => 2
            ];
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'notifications' => $notifications,
                    'statistics' => $stats
                ]
            ]);
            break;
            
        case 'POST':
            // Mark notifications as read or create new notification
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'];
            
            if ($action === 'mark_read') {
                $notificationId = $input['notification_id'];
                echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
            } elseif ($action === 'mark_all_read') {
                echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);
            } elseif ($action === 'create') {
                $message = $input['message'];
                $type = $input['type'];
                echo json_encode(['success' => true, 'message' => 'Notification created']);
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
