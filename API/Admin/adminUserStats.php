<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // User statistics
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $activeUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn();
    $inactiveUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 0")->fetchColumn();
    
    // User type breakdown
    $userTypes = $pdo->query("
        SELECT user_type, COUNT(*) as count 
        FROM users 
        GROUP BY user_type
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent registrations
    $recentUsers = $pdo->query("
        SELECT first_name, last_name, email, user_type, created_at
        FROM users 
        ORDER BY created_at DESC 
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Monthly user growth
    $monthlyGrowth = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as new_users
        FROM users 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'summary' => [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'inactive_users' => $inactiveUsers
            ],
            'user_types' => $userTypes,
            'recent_users' => $recentUsers,
            'monthly_growth' => $monthlyGrowth
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
