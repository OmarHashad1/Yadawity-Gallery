<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Platform overview metrics
    $platformMetrics = [
        'user_metrics' => [
            'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'artists' => $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'artist'")->fetchColumn(),
            'buyers' => $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'buyer'")->fetchColumn(),
            'admins' => $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'admin'")->fetchColumn(),
            'active_users' => $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn()
        ],
        'content_metrics' => [
            'total_artworks' => $pdo->query("SELECT COUNT(*) FROM artworks")->fetchColumn(),
            'available_artworks' => $pdo->query("SELECT COUNT(*) FROM artworks WHERE is_available = 1")->fetchColumn(),
            'total_courses' => $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn(),
            'published_courses' => $pdo->query("SELECT COUNT(*) FROM courses WHERE is_published = 1")->fetchColumn(),
            'total_galleries' => $pdo->query("SELECT COUNT(*) FROM galleries")->fetchColumn(),
            'active_galleries' => $pdo->query("SELECT COUNT(*) FROM galleries WHERE is_active = 1")->fetchColumn()
        ],
        'activity_metrics' => [
            'total_orders' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
            'completed_orders' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status IN ('delivered', 'paid')")->fetchColumn(),
            'active_auctions' => $pdo->query("SELECT COUNT(*) FROM auctions WHERE status = 'active'")->fetchColumn(),
            'total_reviews' => $pdo->query("SELECT COUNT(*) FROM artist_reviews")->fetchColumn(),
            'course_enrollments' => $pdo->query("SELECT COUNT(*) FROM course_enrollments")->fetchColumn()
        ],
        'financial_metrics' => [
            'total_revenue' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'paid'")->fetchColumn(),
            'pending_payments' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'pending'")->fetchColumn(),
            'auction_value' => $pdo->query("SELECT COALESCE(SUM(current_bid), 0) FROM auctions WHERE status = 'active'")->fetchColumn(),
            'course_revenue' => $pdo->query("SELECT COALESCE(SUM(c.price), 0) FROM course_enrollments ce JOIN courses c ON ce.course_id = c.course_id WHERE ce.is_payed = 1")->fetchColumn()
        ]
    ];
    
    // Growth trends (last 6 months)
    $growthTrends = [
        'user_growth' => $pdo->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count
            FROM users 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ")->fetchAll(PDO::FETCH_ASSOC),
        'artwork_growth' => $pdo->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count
            FROM artworks 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ")->fetchAll(PDO::FETCH_ASSOC),
        'revenue_growth' => $pdo->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COALESCE(SUM(total_amount), 0) as revenue
            FROM orders 
            WHERE status = 'paid' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ")->fetchAll(PDO::FETCH_ASSOC)
    ];

    echo json_encode([
        'success' => true,
        'data' => [
            'platform_metrics' => $platformMetrics,
            'growth_trends' => $growthTrends
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
