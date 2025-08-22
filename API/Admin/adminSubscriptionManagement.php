<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT');
header('Access-Control-Allow-Headers: Content-Type');

include_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get all subscribers
            $subscribers = $pdo->query("
                SELECT s.id, s.plan, s.duration, s.start_date, s.end_date, s.is_active,
                       u.first_name, u.last_name, u.email, u.user_type
                FROM subscribers s
                JOIN users u ON s.artist_id = u.user_id
                ORDER BY s.start_date DESC
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Subscription statistics
            $stats = [
                'total_subscribers' => count($subscribers),
                'active_subscribers' => count(array_filter($subscribers, function($s) { return $s['is_active']; })),
                'basic_plans' => count(array_filter($subscribers, function($s) { return $s['plan'] === 'basic' && $s['is_active']; })),
                'premium_plans' => count(array_filter($subscribers, function($s) { return $s['plan'] === 'premium' && $s['is_active']; })),
                'pro_plans' => count(array_filter($subscribers, function($s) { return $s['plan'] === 'pro' && $s['is_active']; }))
            ];
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'subscribers' => $subscribers,
                    'statistics' => $stats
                ]
            ]);
            break;
            
        case 'PUT':
            // Update subscription status
            $input = json_decode(file_get_contents('php://input'), true);
            $subscriptionId = $input['subscription_id'];
            $isActive = $input['is_active'];
            
            $stmt = $pdo->prepare("UPDATE subscribers SET is_active = ? WHERE id = ?");
            $result = $stmt->execute([$isActive, $subscriptionId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Subscription updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update subscription']);
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
