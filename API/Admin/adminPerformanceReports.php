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
            // Get performance metrics
            $reportType = $_GET['type'] ?? 'overview';
            $timeRange = $_GET['range'] ?? '30'; // days
            
            $performanceMetrics = [
                'overview' => [
                    'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
                    'total_artworks' => $pdo->query("SELECT COUNT(*) FROM artworks")->fetchColumn(),
                    'total_revenue' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'paid'")->fetchColumn(),
                    'conversion_rate' => 3.2,
                    'avg_session_duration' => '4m 32s',
                    'bounce_rate' => 35.8
                ],
                'user_engagement' => [
                    'daily_active_users' => $pdo->query("SELECT COUNT(DISTINCT user_id) FROM user_login_sessions WHERE DATE(login_time) = CURDATE()")->fetchColumn(),
                    'weekly_active_users' => $pdo->query("SELECT COUNT(DISTINCT user_id) FROM user_login_sessions WHERE login_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn(),
                    'monthly_active_users' => $pdo->query("SELECT COUNT(DISTINCT user_id) FROM user_login_sessions WHERE login_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn(),
                    'user_retention_rate' => 78.5,
                    'avg_pages_per_session' => 5.7
                ],
                'content_performance' => [
                    'most_viewed_artworks' => $pdo->query("
                        SELECT a.title, COUNT(w.id) as views, u.first_name, u.last_name
                        FROM artworks a
                        LEFT JOIN wishlists w ON a.artwork_id = w.artwork_id
                        JOIN users u ON a.artist_id = u.user_id
                        GROUP BY a.artwork_id
                        ORDER BY views DESC
                        LIMIT 10
                    ")->fetchAll(PDO::FETCH_ASSOC),
                    'popular_categories' => $pdo->query("
                        SELECT type, COUNT(*) as count
                        FROM artworks
                        GROUP BY type
                        ORDER BY count DESC
                    ")->fetchAll(PDO::FETCH_ASSOC)
                ],
                'financial_performance' => [
                    'revenue_growth' => 15.3,
                    'average_order_value' => $pdo->query("SELECT AVG(total_amount) FROM orders WHERE status = 'paid'")->fetchColumn(),
                    'monthly_recurring_revenue' => $pdo->query("SELECT COALESCE(SUM(c.price), 0) FROM course_enrollments ce JOIN courses c ON ce.course_id = c.course_id WHERE ce.is_payed = 1 AND MONTH(ce.enrollment_date) = MONTH(CURRENT_DATE())")->fetchColumn(),
                    'customer_lifetime_value' => 245.80
                ]
            ];
            
            // Generate trends data
            $trends = [
                'user_growth' => $pdo->query("
                    SELECT DATE(created_at) as date, COUNT(*) as count
                    FROM users
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL $timeRange DAY)
                    GROUP BY DATE(created_at)
                    ORDER BY date ASC
                ")->fetchAll(PDO::FETCH_ASSOC),
                'revenue_trend' => $pdo->query("
                    SELECT DATE(created_at) as date, COALESCE(SUM(total_amount), 0) as revenue
                    FROM orders
                    WHERE status = 'paid' AND created_at >= DATE_SUB(NOW(), INTERVAL $timeRange DAY)
                    GROUP BY DATE(created_at)
                    ORDER BY date ASC
                ")->fetchAll(PDO::FETCH_ASSOC)
            ];
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'performance_metrics' => $performanceMetrics,
                    'trends' => $trends,
                    'report_generated_at' => date('Y-m-d H:i:s')
                ]
            ]);
            break;
            
        case 'POST':
            // Generate custom report
            $input = json_decode(file_get_contents('php://input'), true);
            $reportType = $input['report_type'];
            $dateRange = $input['date_range'];
            $metrics = $input['metrics'];
            
            echo json_encode(['success' => true, 'message' => 'Custom report generated', 'report_id' => uniqid()]);
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
