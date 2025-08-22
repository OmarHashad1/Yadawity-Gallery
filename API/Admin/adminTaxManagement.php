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
            // Get tax settings and reports
            $taxSettings = [
                'default_tax_rate' => 8.5,
                'tax_by_region' => [
                    'CA' => 7.5,
                    'NY' => 8.0,
                    'TX' => 6.25,
                    'FL' => 6.0
                ],
                'tax_exempt_items' => ['digital_art', 'educational_courses']
            ];
            
            // Tax collection summary
            $taxSummary = $pdo->query("
                SELECT 
                    DATE(created_at) as date,
                    SUM(tax_amount) as total_tax_collected,
                    COUNT(*) as taxable_transactions
                FROM transactions 
                WHERE tax_amount > 0
                GROUP BY DATE(created_at)
                ORDER BY date DESC
                LIMIT 30
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Tax by region
            $taxByRegion = $pdo->query("
                SELECT 
                    shipping_state,
                    SUM(tax_amount) as total_tax,
                    COUNT(*) as transaction_count
                FROM transactions t
                WHERE tax_amount > 0
                GROUP BY shipping_state
                ORDER BY total_tax DESC
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'tax_settings' => $taxSettings,
                    'tax_summary' => $taxSummary,
                    'tax_by_region' => $taxByRegion
                ]
            ]);
            break;
            
        case 'POST':
            // Update tax settings
            $input = json_decode(file_get_contents('php://input'), true);
            $defaultRate = $input['default_rate'];
            $regionRates = $input['region_rates'] ?? [];
            
            // In a real system, this would update tax configuration
            echo json_encode([
                'success' => true,
                'message' => 'Tax settings updated successfully',
                'default_rate' => $defaultRate,
                'region_rates' => $regionRates
            ]);
            break;
            
        case 'PUT':
            // Generate tax report
            $input = json_decode(file_get_contents('php://input'), true);
            $startDate = $input['start_date'];
            $endDate = $input['end_date'];
            
            $taxReport = $pdo->prepare("
                SELECT 
                    DATE(created_at) as transaction_date,
                    transaction_id,
                    amount,
                    tax_amount,
                    shipping_state,
                    user_id
                FROM transactions 
                WHERE created_at BETWEEN ? AND ?
                AND tax_amount > 0
                ORDER BY created_at DESC
            ");
            $taxReport->execute([$startDate, $endDate]);
            $reportData = $taxReport->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'report_period' => ['start' => $startDate, 'end' => $endDate],
                    'transactions' => $reportData,
                    'total_tax_collected' => array_sum(array_column($reportData, 'tax_amount'))
                ]
            ]);
            break;
            
        case 'DELETE':
            // Remove tax exemption
            $input = json_decode(file_get_contents('php://input'), true);
            $exemptionType = $input['exemption_type'];
            
            echo json_encode([
                'success' => true,
                'message' => 'Tax exemption removed: ' . $exemptionType
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
