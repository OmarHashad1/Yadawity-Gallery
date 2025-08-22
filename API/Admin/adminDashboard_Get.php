<?php
include_once 'db.php';

try {
    $timeframe = $_GET['timeframe'] ?? 'week'; // today, week, month, year
    $type = $_GET['type'] ?? 'overview'; // overview, detailed, charts
    
    // Set date range based on timeframe
    switch ($timeframe) {
        case 'today':
            $startDate = date('Y-m-d 00:00:00');
            $endDate = date('Y-m-d 23:59:59');
            break;
        case 'week':
            $startDate = date('Y-m-d 00:00:00', strtotime('-7 days'));
            $endDate = date('Y-m-d 23:59:59');
            break;
        case 'month':
            $startDate = date('Y-m-01 00:00:00');
            $endDate = date('Y-m-t 23:59:59');
            break;
        case 'year':
            $startDate = date('Y-01-01 00:00:00');
            $endDate = date('Y-12-31 23:59:59');
            break;
        default:
            $startDate = date('Y-m-d 00:00:00', strtotime('-7 days'));
            $endDate = date('Y-m-d 23:59:59');
    }
    
    $analytics = [];
    
    // Overview Statistics
    if ($type === 'overview' || $type === 'detailed') {
        // Total Users
        $totalUsersStmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
        $totalUsers = $totalUsersStmt->fetch()['total'];
        
        // New Users in timeframe
        $newUsersStmt = $pdo->prepare("SELECT COUNT(*) as new_users FROM users WHERE created_at BETWEEN ? AND ?");
        $newUsersStmt->execute([$startDate, $endDate]);
        $newUsers = $newUsersStmt->fetch()['new_users'];
        
        // Active Artists
        $activeArtistsStmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'artist' AND is_active = 1");
        $activeArtists = $activeArtistsStmt->fetch()['total'];
        
        // Total Artworks
        $totalArtworksStmt = $pdo->query("SELECT COUNT(*) as total FROM artworks WHERE is_available = 1");
        $totalArtworks = $totalArtworksStmt->fetch()['total'];
        
        // New Artworks in timeframe
        $newArtworksStmt = $pdo->prepare("SELECT COUNT(*) as new_artworks FROM artworks WHERE created_at BETWEEN ? AND ?");
        $newArtworksStmt->execute([$startDate, $endDate]);
        $newArtworks = $newArtworksStmt->fetch()['new_artworks'];
        
        // Total Orders
        $totalOrdersStmt = $pdo->query("SELECT COUNT(*) as total, SUM(total_amount) as revenue FROM orders WHERE status != 'cancelled'");
        $ordersData = $totalOrdersStmt->fetch();
        $totalOrders = $ordersData['total'];
        $totalRevenue = $ordersData['revenue'] ?? 0;
        
        // Orders in timeframe
        $periodOrdersStmt = $pdo->prepare("SELECT COUNT(*) as orders, SUM(total_amount) as revenue FROM orders WHERE created_at BETWEEN ? AND ? AND status != 'cancelled'");
        $periodOrdersStmt->execute([$startDate, $endDate]);
        $periodOrders = $periodOrdersStmt->fetch();
        $periodOrderCount = $periodOrders['orders'];
        $periodRevenue = $periodOrders['revenue'] ?? 0;
        
        // Active Auctions
        $activeAuctionsStmt = $pdo->query("SELECT COUNT(*) as total FROM auctions WHERE status = 'active' AND end_time > NOW()");
        $activeAuctions = $activeAuctionsStmt->fetch()['total'];
        
        // Total Courses
        $totalCoursesStmt = $pdo->query("SELECT COUNT(*) as total FROM courses WHERE is_published = 1");
        $totalCourses = $totalCoursesStmt->fetch()['total'];
        
        // Course Enrollments
        $enrollmentsStmt = $pdo->query("SELECT COUNT(*) as total FROM course_enrollments WHERE is_active = 1");
        $totalEnrollments = $enrollmentsStmt->fetch()['total'];
        
        // Active Galleries
        $activeGalleriesStmt = $pdo->query("SELECT COUNT(*) as total FROM galleries WHERE is_active = 1");
        $activeGalleries = $activeGalleriesStmt->fetch()['total'];
        
        $analytics['overview'] = [
            'users' => [
                'total' => (int)$totalUsers,
                'new_this_period' => (int)$newUsers,
                'artists' => (int)$activeArtists
            ],
            'artworks' => [
                'total' => (int)$totalArtworks,
                'new_this_period' => (int)$newArtworks
            ],
            'orders' => [
                'total' => (int)$totalOrders,
                'this_period' => (int)$periodOrderCount,
                'total_revenue' => (float)$totalRevenue,
                'period_revenue' => (float)$periodRevenue
            ],
            'auctions' => [
                'active' => (int)$activeAuctions
            ],
            'courses' => [
                'total' => (int)$totalCourses,
                'enrollments' => (int)$totalEnrollments
            ],
            'galleries' => [
                'active' => (int)$activeGalleries
            ]
        ];
    }
    
    // Detailed Analytics
    if ($type === 'detailed') {
        // Top Selling Artworks
        $topArtworksStmt = $pdo->prepare("
            SELECT a.artwork_id, a.title, a.price, a.type,
                   u.first_name, u.last_name,
                   COUNT(oi.id) as sales_count,
                   SUM(oi.price * oi.quantity) as total_sales
            FROM artworks a
            JOIN users u ON a.artist_id = u.user_id
            LEFT JOIN order_items oi ON a.artwork_id = oi.artwork_id
            LEFT JOIN orders o ON oi.order_id = o.id
            WHERE o.created_at BETWEEN ? AND ? AND o.status != 'cancelled'
            GROUP BY a.artwork_id
            ORDER BY sales_count DESC, total_sales DESC
            LIMIT 10
        ");
        $topArtworksStmt->execute([$startDate, $endDate]);
        $topArtworks = $topArtworksStmt->fetchAll();
        
        // Top Artists by Revenue
        $topArtistsStmt = $pdo->prepare("
            SELECT u.user_id, u.first_name, u.last_name, u.email,
                   COUNT(DISTINCT a.artwork_id) as artwork_count,
                   COUNT(DISTINCT oi.id) as sales_count,
                   SUM(oi.price * oi.quantity) as total_revenue
            FROM users u
            JOIN artworks a ON u.user_id = a.artist_id
            LEFT JOIN order_items oi ON a.artwork_id = oi.artwork_id
            LEFT JOIN orders o ON oi.order_id = o.id
            WHERE u.user_type = 'artist' AND o.created_at BETWEEN ? AND ? AND o.status != 'cancelled'
            GROUP BY u.user_id
            ORDER BY total_revenue DESC
            LIMIT 10
        ");
        $topArtistsStmt->execute([$startDate, $endDate]);
        $topArtists = $topArtistsStmt->fetchAll();
        
        // Recent Activity
        $recentActivityStmt = $pdo->prepare("
            (SELECT 'user_registration' as type, CONCAT(first_name, ' ', last_name) as description, 
                    created_at as activity_time, user_id as reference_id
             FROM users WHERE created_at BETWEEN ? AND ? ORDER BY created_at DESC LIMIT 5)
            UNION ALL
            (SELECT 'artwork_added' as type, CONCAT('New artwork: ', title) as description,
                    created_at as activity_time, artwork_id as reference_id
             FROM artworks WHERE created_at BETWEEN ? AND ? ORDER BY created_at DESC LIMIT 5)
            UNION ALL
            (SELECT 'order_placed' as type, CONCAT('Order #', id, ' - $', total_amount) as description,
                    created_at as activity_time, id as reference_id
             FROM orders WHERE created_at BETWEEN ? AND ? ORDER BY created_at DESC LIMIT 5)
            ORDER BY activity_time DESC LIMIT 15
        ");
        $recentActivityStmt->execute([$startDate, $endDate, $startDate, $endDate, $startDate, $endDate]);
        $recentActivity = $recentActivityStmt->fetchAll();
        
        $analytics['detailed'] = [
            'top_artworks' => $topArtworks,
            'top_artists' => $topArtists,
            'recent_activity' => $recentActivity
        ];
    }
    
    // Chart Data
    if ($type === 'charts') {
        // Daily Sales Chart (last 30 days)
        $dailySalesStmt = $pdo->prepare("
            SELECT DATE(created_at) as date, 
                   COUNT(*) as orders,
                   SUM(total_amount) as revenue
            FROM orders 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            AND status != 'cancelled'
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $dailySalesStmt->execute();
        $dailySales = $dailySalesStmt->fetchAll();
        
        // Artwork Types Distribution
        $artworkTypesStmt = $pdo->query("
            SELECT type, COUNT(*) as count
            FROM artworks 
            WHERE is_available = 1
            GROUP BY type
            ORDER BY count DESC
        ");
        $artworkTypes = $artworkTypesStmt->fetchAll();
        
        // User Growth Chart (last 12 months)
        $userGrowthStmt = $pdo->prepare("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as month,
                   COUNT(*) as new_users
            FROM users 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ");
        $userGrowthStmt->execute();
        $userGrowth = $userGrowthStmt->fetchAll();
        
        $analytics['charts'] = [
            'daily_sales' => $dailySales,
            'artwork_types' => $artworkTypes,
            'user_growth' => $userGrowth
        ];
    }
    
    $analytics['timeframe'] = $timeframe;
    $analytics['date_range'] = [
        'start' => $startDate,
        'end' => $endDate
    ];
    
    sendResponse(true, 'Analytics data retrieved successfully', $analytics);

} catch (Exception $e) {
    sendResponse(false, 'Error retrieving analytics: ' . $e->getMessage(), null, 500);
}
?>
