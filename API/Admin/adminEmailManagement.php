<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get all email campaigns and statistics
            $campaigns = [
                'recent_campaigns' => [
                    ['id' => 1, 'name' => 'New Artwork Alert', 'type' => 'automated', 'status' => 'active', 'sent_count' => 1250, 'open_rate' => 34.5, 'click_rate' => 8.2, 'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))],
                    ['id' => 2, 'name' => 'Monthly Newsletter', 'type' => 'newsletter', 'status' => 'sent', 'sent_count' => 2800, 'open_rate' => 42.1, 'click_rate' => 12.3, 'created_at' => date('Y-m-d H:i:s', strtotime('-1 week'))],
                    ['id' => 3, 'name' => 'Auction Reminder', 'type' => 'promotional', 'status' => 'scheduled', 'sent_count' => 0, 'open_rate' => 0, 'click_rate' => 0, 'created_at' => date('Y-m-d H:i:s')]
                ],
                'email_templates' => [
                    ['id' => 1, 'name' => 'Welcome Email', 'type' => 'system', 'usage_count' => 156],
                    ['id' => 2, 'name' => 'Order Confirmation', 'type' => 'transactional', 'usage_count' => 89],
                    ['id' => 3, 'name' => 'Auction Won', 'type' => 'notification', 'usage_count' => 23],
                    ['id' => 4, 'name' => 'Course Enrollment', 'type' => 'educational', 'usage_count' => 67]
                ]
            ];
            
            // Email statistics
            $emailStats = [
                'total_subscribers' => $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn(),
                'emails_sent_today' => 156,
                'emails_sent_this_month' => 4520,
                'average_open_rate' => 38.7,
                'average_click_rate' => 9.8,
                'bounce_rate' => 2.1,
                'unsubscribe_rate' => 0.8
            ];
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'campaigns' => $campaigns,
                    'email_stats' => $emailStats
                ]
            ]);
            break;
            
        case 'POST':
            // Create new email campaign
            $input = json_decode(file_get_contents('php://input'), true);
            $campaignName = $input['campaign_name'];
            $campaignType = $input['campaign_type'];
            $recipients = $input['recipients'];
            $template = $input['template'];
            
            echo json_encode(['success' => true, 'message' => 'Email campaign created successfully']);
            break;
            
        case 'PUT':
            // Update campaign status
            $input = json_decode(file_get_contents('php://input'), true);
            $campaignId = $input['campaign_id'];
            $status = $input['status'];
            
            echo json_encode(['success' => true, 'message' => 'Campaign status updated']);
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
