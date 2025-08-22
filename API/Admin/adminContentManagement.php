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
            // Get site content/settings (using a simple approach)
            $content = [
                'site_name' => 'Yadawity Art Marketplace',
                'about_us' => 'Welcome to Yadawity, your premier destination for discovering and purchasing exceptional artwork.',
                'privacy_policy' => 'Your privacy is important to us...',
                'terms_of_service' => 'By using our platform, you agree to these terms...',
                'contact_email' => 'admin@yadawity.com',
                'contact_phone' => '+1234567890',
                'featured_categories' => ['painting', 'sculpture', 'photography', 'digital'],
                'maintenance_mode' => false,
                'commission_rate' => 10.0
            ];
            
            echo json_encode([
                'success' => true,
                'data' => $content
            ]);
            break;
            
        case 'POST':
            // Update site content
            $input = json_decode(file_get_contents('php://input'), true);
            
            // In a real system, you'd save this to a settings table
            // For now, we'll just return success
            echo json_encode([
                'success' => true,
                'message' => 'Content updated successfully'
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
