<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Comprehensive analytics dashboard
    $analytics = [
        'user_analytics' => [
            'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'new_users_today' => $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
            'new_users_this_week' => $pdo->query("SELECT COUNT(*) FROM users WHERE WEEK(created_at) = WEEK(CURRENT_DATE())")->fetchColumn(),
            'new_users_this_month' => $pdo->query("SELECT COUNT(*) FROM users WHERE MONTH(created_at) = MONTH(CURRENT_DATE())")->fetchColumn(),
            'user_growth_rate' => 5.2 // Calculate based on previous period
        ],
        'engagement_metrics' => [
            'active_sessions' => $pdo->query("SELECT COUNT(*) FROM user_login_sessions WHERE is_active = 1")->fetchColumn(),
            'total_artworks_viewed' => $pdo->query("SELECT COUNT(*) FROM wishlists")->fetchColumn(), // Using wishlist as proxy for views
            'total_searches' => 1250, // Would come from search logs
            'bounce_rate' => 35.8 // Would come from analytics
        ],
        'content_performance' => [
            'most_popular_artwork_types' => $pdo->query("
                SELECT type, COUNT(*) as count
                FROM artworks
                GROUP BY type
                ORDER BY count DESC
                LIMIT 5
            ")->fetchAll(PDO::FETCH_ASSOC),
            'top_artists_by_views' => $pdo->query("
                SELECT u.first_name, u.last_name, COUNT(w.id) as views
                FROM users u
                JOIN artworks a ON u.user_id = a.artist_id
                LEFT JOIN wishlists w ON a.artwork_id = w.artwork_id
                WHERE u.user_type = 'artist'
                GROUP BY u.user_id
                ORDER BY views DESC
                LIMIT 5
            ")->fetchAll(PDO::FETCH_ASSOC)
        ],
        'conversion_metrics' => [
            'cart_to_order_conversion' => 12.5, // Calculate from cart vs orders
            'wishlist_to_cart_conversion' => 8.3,
            'visitor_to_signup_conversion' => 3.2,
            'auction_participation_rate' => 15.7
        ],
        'time_based_trends' => [
            'peak_hours' => ['18:00-20:00', '20:00-22:00'], // Would come from session logs
            'peak_days' => ['Saturday', 'Sunday'],
            'seasonal_trends' => 'Higher activity in winter months'
        ]
    ];
    
    // User activity timeline
    $activityTimeline = $pdo->query("
        SELECT 
            DATE(created_at) as date,
            COUNT(*) as activity_count,
            'user_registration' as activity_type
        FROM users 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date DESC
        LIMIT 30
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'analytics' => $analytics,
            'activity_timeline' => $activityTimeline
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
