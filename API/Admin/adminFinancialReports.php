<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Financial analytics
    $financialData = [
        'revenue' => [
            'total_revenue' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'paid'")->fetchColumn(),
            'monthly_revenue' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'paid' AND MONTH(created_at) = MONTH(CURRENT_DATE())")->fetchColumn(),
            'yearly_revenue' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'paid' AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetchColumn(),
            'average_order_value' => $pdo->query("SELECT AVG(total_amount) FROM orders WHERE status = 'paid'")->fetchColumn()
        ],
        'auction_revenue' => [
            'total_auction_value' => $pdo->query("SELECT COALESCE(SUM(current_bid), 0) FROM auctions WHERE status = 'ended'")->fetchColumn(),
            'active_auction_value' => $pdo->query("SELECT COALESCE(SUM(current_bid), 0) FROM auctions WHERE status = 'active'")->fetchColumn()
        ],
        'course_revenue' => [
            'total_course_revenue' => $pdo->query("SELECT COALESCE(SUM(c.price), 0) FROM course_enrollments ce JOIN courses c ON ce.course_id = c.course_id WHERE ce.is_payed = 1")->fetchColumn(),
            'monthly_course_revenue' => $pdo->query("SELECT COALESCE(SUM(c.price), 0) FROM course_enrollments ce JOIN courses c ON ce.course_id = c.course_id WHERE ce.is_payed = 1 AND MONTH(ce.enrollment_date) = MONTH(CURRENT_DATE())")->fetchColumn()
        ],
        'gallery_revenue' => [
            'virtual_gallery_revenue' => $pdo->query("SELECT COALESCE(SUM(price), 0) FROM galleries WHERE gallery_type = 'virtual' AND is_active = 1")->fetchColumn()
        ]
    ];
    
    // Monthly revenue breakdown
    $monthlyBreakdown = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COALESCE(SUM(total_amount), 0) as revenue,
            COUNT(*) as order_count
        FROM orders 
        WHERE status = 'paid' AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Top revenue generators
    $topArtists = $pdo->query("
        SELECT u.first_name, u.last_name, u.email,
               COALESCE(SUM(oi.price * oi.quantity), 0) as total_revenue,
               COUNT(DISTINCT o.id) as orders_count
        FROM users u
        JOIN artworks a ON u.user_id = a.artist_id
        JOIN order_items oi ON a.artwork_id = oi.artwork_id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.status = 'paid'
        GROUP BY u.user_id
        ORDER BY total_revenue DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'financial_overview' => $financialData,
            'monthly_breakdown' => $monthlyBreakdown,
            'top_artists' => $topArtists
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
