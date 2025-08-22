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
            // Get payment method statistics
            $paymentStats = $pdo->query("
                SELECT 
                    payment_method,
                    COUNT(*) as transaction_count,
                    SUM(amount) as total_amount,
                    AVG(amount) as avg_amount
                FROM transactions 
                WHERE status = 'completed'
                GROUP BY payment_method
                ORDER BY total_amount DESC
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Get payment failures
            $failureStats = $pdo->query("
                SELECT 
                    payment_method,
                    COUNT(*) as failure_count,
                    failure_reason
                FROM transactions 
                WHERE status = 'failed'
                GROUP BY payment_method, failure_reason
                ORDER BY failure_count DESC
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Recent transactions
            $recentTransactions = $pdo->query("
                SELECT t.*, u.email as user_email
                FROM transactions t
                JOIN users u ON t.user_id = u.user_id
                ORDER BY t.created_at DESC
                LIMIT 20
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'payment_stats' => $paymentStats,
                    'failure_stats' => $failureStats,
                    'recent_transactions' => $recentTransactions
                ]
            ]);
            break;
            
        case 'POST':
            // Process refund
            $input = json_decode(file_get_contents('php://input'), true);
            $transactionId = $input['transaction_id'];
            $refundAmount = $input['refund_amount'];
            $reason = $input['reason'];
            
            // Update transaction status
            $stmt = $pdo->prepare("
                UPDATE transactions 
                SET status = 'refunded', 
                    refund_amount = ?,
                    refund_reason = ?,
                    refunded_at = NOW()
                WHERE transaction_id = ?
            ");
            $result = $stmt->execute([$refundAmount, $reason, $transactionId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Refund processed successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to process refund']);
            }
            break;
            
        case 'PUT':
            // Update payment method settings
            $input = json_decode(file_get_contents('php://input'), true);
            $paymentMethod = $input['payment_method'];
            $isEnabled = $input['is_enabled'];
            
            // In a real system, this would update payment gateway settings
            echo json_encode([
                'success' => true,
                'message' => 'Payment method ' . $paymentMethod . ' ' . ($isEnabled ? 'enabled' : 'disabled')
            ]);
            break;
            
        case 'DELETE':
            // Mark transaction as disputed
            $input = json_decode(file_get_contents('php://input'), true);
            $transactionId = $input['transaction_id'];
            
            $stmt = $pdo->prepare("
                UPDATE transactions 
                SET status = 'disputed',
                    disputed_at = NOW()
                WHERE transaction_id = ?
            ");
            $result = $stmt->execute([$transactionId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Transaction marked as disputed']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update transaction']);
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
