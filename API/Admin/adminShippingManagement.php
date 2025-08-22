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
            // Get shipping methods and rates
            $shippingMethods = [
                'standard' => ['name' => 'Standard Shipping', 'rate' => 9.99, 'days' => '5-7'],
                'express' => ['name' => 'Express Shipping', 'rate' => 19.99, 'days' => '2-3'],
                'overnight' => ['name' => 'Overnight', 'rate' => 39.99, 'days' => '1'],
                'free' => ['name' => 'Free Shipping', 'rate' => 0, 'days' => '7-10', 'min_order' => 100]
            ];
            
            // Shipping analytics
            $shippingStats = $pdo->query("
                SELECT 
                    shipping_method,
                    COUNT(*) as order_count,
                    AVG(shipping_cost) as avg_cost,
                    SUM(shipping_cost) as total_revenue
                FROM orders 
                WHERE shipping_method IS NOT NULL
                GROUP BY shipping_method
                ORDER BY order_count DESC
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Shipping by region
            $shippingByRegion = $pdo->query("
                SELECT 
                    shipping_state,
                    shipping_country,
                    COUNT(*) as order_count,
                    AVG(shipping_cost) as avg_shipping_cost
                FROM orders 
                GROUP BY shipping_state, shipping_country
                ORDER BY order_count DESC
                LIMIT 20
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'shipping_methods' => $shippingMethods,
                    'shipping_stats' => $shippingStats,
                    'shipping_by_region' => $shippingByRegion
                ]
            ]);
            break;
            
        case 'POST':
            // Create new shipping method
            $input = json_decode(file_get_contents('php://input'), true);
            $methodName = $input['method_name'];
            $rate = $input['rate'];
            $deliveryDays = $input['delivery_days'];
            
            // In a real system, this would be stored in a shipping_methods table
            echo json_encode([
                'success' => true,
                'message' => 'Shipping method created: ' . $methodName,
                'method' => [
                    'name' => $methodName,
                    'rate' => $rate,
                    'delivery_days' => $deliveryDays
                ]
            ]);
            break;
            
        case 'PUT':
            // Update shipping rates
            $input = json_decode(file_get_contents('php://input'), true);
            $methodId = $input['method_id'];
            $newRate = $input['new_rate'];
            
            // In a real system, this would update the shipping_methods table
            echo json_encode([
                'success' => true,
                'message' => 'Shipping rate updated for method: ' . $methodId,
                'new_rate' => $newRate
            ]);
            break;
            
        case 'DELETE':
            // Disable shipping method
            $input = json_decode(file_get_contents('php://input'), true);
            $methodId = $input['method_id'];
            
            echo json_encode([
                'success' => true,
                'message' => 'Shipping method disabled: ' . $methodId
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
