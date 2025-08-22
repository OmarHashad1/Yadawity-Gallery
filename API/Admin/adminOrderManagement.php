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
            // Get all orders with buyer info
            $sql = "SELECT o.id, o.total_amount, o.status, o.created_at,
                           u.first_name, u.last_name, u.email
                    FROM orders o 
                    JOIN users u ON o.buyer_id = u.user_id 
                    ORDER BY o.created_at DESC";
            
            $stmt = $pdo->query($sql);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $orders
            ]);
            break;
            
        case 'PUT':
            // Update order status
            $input = json_decode(file_get_contents('php://input'), true);
            $orderId = $input['order_id'];
            $status = $input['status'];
            
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $result = $stmt->execute([$status, $orderId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update order']);
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
