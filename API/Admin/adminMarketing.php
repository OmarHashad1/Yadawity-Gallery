<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Sales analytics
    $totalRevenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'paid'")->fetchColumn();
    $totalOrdersCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $averageOrderValue = $totalOrdersCount > 0 ? $totalRevenue / $totalOrdersCount : 0;
    
    // User growth
    $usersThisMonth = $pdo->query("SELECT COUNT(*) FROM users WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetchColumn();
    $usersLastMonth = $pdo->query("SELECT COUNT(*) FROM users WHERE MONTH(created_at) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)")->fetchColumn();
    
    // Top artists by artwork count
    $topArtists = $pdo->query("
        SELECT u.first_name, u.last_name, COUNT(a.artwork_id) as artwork_count
        FROM users u
        JOIN artworks a ON u.user_id = a.artist_id
        WHERE u.user_type = 'artist'
        GROUP BY u.user_id
        ORDER BY artwork_count DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Popular artwork types
    $popularTypes = $pdo->query("
        SELECT type, COUNT(*) as count
        FROM artworks
        GROUP BY type
        ORDER BY count DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'revenue' => [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrdersCount,
                'average_order_value' => round($averageOrderValue, 2)
            ],
            'user_growth' => [
                'users_this_month' => $usersThisMonth,
                'users_last_month' => $usersLastMonth,
                'growth_rate' => $usersLastMonth > 0 ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100, 2) : 0
            ],
            'top_artists' => $topArtists,
            'popular_types' => $popularTypes
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
