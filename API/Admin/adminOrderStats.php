<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Order statistics
    $totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $totalRevenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'paid'")->fetchColumn();
    $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
    
    // Orders by status
    $ordersByStatus = $pdo->query("
        SELECT status, COUNT(*) as count, COALESCE(SUM(total_amount), 0) as revenue
        FROM orders 
        GROUP BY status
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Monthly sales
    $monthlySales = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as order_count,
            COALESCE(SUM(total_amount), 0) as revenue
        FROM orders 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Top buyers
    $topBuyers = $pdo->query("
        SELECT u.first_name, u.last_name, u.email, 
               COUNT(o.id) as order_count, COALESCE(SUM(o.total_amount), 0) as total_spent
        FROM users u
        JOIN orders o ON u.user_id = o.buyer_id
        GROUP BY u.user_id
        ORDER BY total_spent DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent orders
    $recentOrders = $pdo->query("
        SELECT o.id, o.total_amount, o.status, o.created_at,
               u.first_name, u.last_name, u.email
        FROM orders o
        JOIN users u ON o.buyer_id = u.user_id
        ORDER BY o.created_at DESC
        LIMIT 15
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'summary' => [
                'total_orders' => $totalOrders,
                'total_revenue' => round($totalRevenue, 2),
                'average_order_value' => round($averageOrderValue, 2)
            ],
            'orders_by_status' => $ordersByStatus,
            'monthly_sales' => $monthlySales,
            'top_buyers' => $topBuyers,
            'recent_orders' => $recentOrders
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
