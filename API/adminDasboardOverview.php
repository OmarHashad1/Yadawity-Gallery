<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'db.php';

try {
    // Get request parameters
    $action = $_GET['action'] ?? 'overview';
    $time_period = $_GET['time_period'] ?? 'week';
    $category = $_GET['category'] ?? 'all';

    $response = [];

    switch ($action) {
        case 'stats':
            // Get only statistics
            $stats = getDashboardStats($db, $time_period);
            $response = $stats;
            break;
            
        case 'charts':
            // Get chart data
            $charts = getChartData($db, $time_period);
            $response = $charts;
            break;
            
        case 'activity':
            // Get recent activity
            $activity = getRecentActivity($db);
            $response = $activity;
            break;
            
        case 'overview':
        default:
            // Get complete dashboard overview
            $stats = getDashboardStats($db, $time_period);
            $charts = getChartData($db, $time_period);
            $activity = getRecentActivity($db);
            $analytics = getAnalyticsData($db, $time_period);
            
            $response = [
                'success' => true,
                'stats' => $stats['success'] ? $stats['data'] : null,
                'charts' => $charts['success'] ? $charts['data'] : null,
                'activity' => $activity['success'] ? $activity['data'] : [],
                'analytics' => $analytics['success'] ? $analytics['data'] : null,
                'last_updated' => date('Y-m-d H:i:s'),
                'errors' => []
            ];
            
            // Collect any errors
            if (!$stats['success']) {
                $response['errors'][] = 'Stats: ' . $stats['message'];
            }
            if (!$charts['success']) {
                $response['errors'][] = 'Charts: ' . $charts['message'];
            }
            if (!$activity['success']) {
                $response['errors'][] = 'Activity: ' . $activity['message'];
            }
            if (!$analytics['success']) {
                $response['errors'][] = 'Analytics: ' . $analytics['message'];
            }
            break;
    }

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

function getDashboardStats($db, $time_period) {
    try {
        // Get time range based on period
        $time_condition = getTimeCondition($time_period);
        $previous_time_condition = getPreviousTimeCondition($time_period);

        // Total Revenue
        $revenue_query = "SELECT COALESCE(SUM(total_amount), 0) as current_revenue FROM orders WHERE status IN ('paid', 'delivered') $time_condition";
        $prev_revenue_query = "SELECT COALESCE(SUM(total_amount), 0) as previous_revenue FROM orders WHERE status IN ('paid', 'delivered') $previous_time_condition";
        
        $current_revenue_result = $db->query($revenue_query);
        $prev_revenue_result = $db->query($prev_revenue_query);
        
        if (!$current_revenue_result || !$prev_revenue_result) {
            throw new Exception("Revenue query failed: " . $db->error);
        }
        
        $current_revenue = $current_revenue_result->fetch_assoc()['current_revenue'];
        $previous_revenue = $prev_revenue_result->fetch_assoc()['previous_revenue'];
        $revenue_change = calculatePercentageChange($previous_revenue, $current_revenue);

        // Active Users (users with recent activity)
        $users_query = "SELECT COUNT(DISTINCT u.user_id) as active_users 
                       FROM users u 
                       LEFT JOIN user_login_sessions uls ON u.user_id = uls.user_id 
                       WHERE u.is_active = 1 AND (uls.login_time IS NULL OR uls.login_time >= DATE_SUB(NOW(), INTERVAL 30 DAY))";
        
        $prev_users_query = "SELECT COUNT(DISTINCT u.user_id) as prev_active_users 
                            FROM users u 
                            LEFT JOIN user_login_sessions uls ON u.user_id = uls.user_id 
                            WHERE u.is_active = 1 AND uls.login_time BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND DATE_SUB(NOW(), INTERVAL 30 DAY)";
        
        $active_users_result = $db->query($users_query);
        $prev_users_result = $db->query($prev_users_query);
        
        if (!$active_users_result || !$prev_users_result) {
            throw new Exception("Users query failed: " . $db->error);
        }
        
        $active_users = $active_users_result->fetch_assoc()['active_users'];
        $prev_active_users = $prev_users_result->fetch_assoc()['prev_active_users'];
        $users_change = calculatePercentageChange($prev_active_users, $active_users);

        // Orders Today/Period
        $orders_query = "SELECT COUNT(*) as current_orders FROM orders $time_condition";
        $prev_orders_query = "SELECT COUNT(*) as previous_orders FROM orders $previous_time_condition";
        
        $current_orders_result = $db->query($orders_query);
        $previous_orders_result = $db->query($prev_orders_query);
        
        if (!$current_orders_result || !$previous_orders_result) {
            throw new Exception("Orders query failed: " . $db->error);
        }
        
        $current_orders = $current_orders_result->fetch_assoc()['current_orders'];
        $previous_orders = $previous_orders_result->fetch_assoc()['previous_orders'];
        $orders_change = calculatePercentageChange($previous_orders, $current_orders);

        // Conversion Rate (orders/active users ratio)
        $total_users_period_query = "SELECT COUNT(DISTINCT user_id) as total FROM user_login_sessions $time_condition";
        $total_users_result = $db->query($total_users_period_query);
        
        if (!$total_users_result) {
            throw new Exception("Total users query failed: " . $db->error);
        }
        
        $total_users_period = $total_users_result->fetch_assoc()['total'];
        $conversion_rate = $total_users_period > 0 ? ($current_orders / $total_users_period) * 100 : 0;
        
        $prev_total_users_query = "SELECT COUNT(DISTINCT user_id) as total FROM user_login_sessions $previous_time_condition";
        $prev_total_users_result = $db->query($prev_total_users_query);
        
        if (!$prev_total_users_result) {
            // If no previous data, set to 0
            $prev_total_users = 0;
        } else {
            $prev_total_users = $prev_total_users_result->fetch_assoc()['total'];
        }
        
        $prev_conversion_rate = $prev_total_users > 0 ? ($previous_orders / $prev_total_users) * 100 : 0;
        $conversion_change = calculatePercentageChange($prev_conversion_rate, $conversion_rate);

        // Additional platform-specific stats
        $platform_stats = getPlatformStats($db, $time_condition);

        return [
            'success' => true,
            'data' => [
                'total_revenue' => [
                    'value' => (float)$current_revenue,
                    'formatted' => '$' . number_format($current_revenue, 2),
                    'change' => $revenue_change,
                    'trend' => $revenue_change >= 0 ? 'positive' : 'negative'
                ],
                'active_users' => [
                    'value' => (int)$active_users,
                    'formatted' => number_format($active_users),
                    'change' => $users_change,
                    'trend' => $users_change >= 0 ? 'positive' : 'negative'
                ],
                'total_orders' => [
                    'value' => (int)$current_orders,
                    'formatted' => number_format($current_orders),
                    'change' => $orders_change,
                    'trend' => $orders_change >= 0 ? 'positive' : 'negative'
                ],
                'conversion_rate' => [
                    'value' => round($conversion_rate, 2),
                    'formatted' => round($conversion_rate, 2) . '%',
                    'change' => $conversion_change,
                    'trend' => $conversion_change >= 0 ? 'positive' : 'negative'
                ],
                'platform_stats' => $platform_stats
            ]
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error fetching dashboard stats: ' . $e->getMessage(),
            'data' => null
        ];
    }
}

function getPlatformStats($db, $time_condition) {
    // Get artwork marketplace specific stats
    $artworks_count = $db->query("SELECT COUNT(*) as count FROM artworks WHERE is_available = 1 $time_condition")->fetch_assoc()['count'];
    $auctions_active = $db->query("SELECT COUNT(*) as count FROM auctions WHERE status = 'active' AND end_time > NOW()")->fetch_assoc()['count'];
    $courses_published = $db->query("SELECT COUNT(*) as count FROM courses WHERE is_published = 1 $time_condition")->fetch_assoc()['count'];
    $galleries_active = $db->query("SELECT COUNT(*) as count FROM galleries WHERE is_active = 1 $time_condition")->fetch_assoc()['count'];
    $artists_count = $db->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'artist' AND is_active = 1")->fetch_assoc()['count'];

    return [
        'available_artworks' => (int)$artworks_count,
        'active_auctions' => (int)$auctions_active,
        'published_courses' => (int)$courses_published,
        'active_galleries' => (int)$galleries_active,
        'active_artists' => (int)$artists_count
    ];
}

function getChartData($db, $time_period) {
    try {
        // Revenue chart data (last 7 days/weeks/months based on period)
        $revenue_data = getRevenueChartData($db, $time_period);
        
        // Traffic sources (user types)
        $traffic_data = getTrafficSourcesData($db);
        
        // Sales performance by category
        $sales_performance = getSalesPerformanceData($db, $time_period);

        return [
            'success' => true,
            'data' => [
                'revenue_chart' => $revenue_data,
                'traffic_sources' => $traffic_data,
                'sales_performance' => $sales_performance
            ]
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error fetching chart data: ' . $e->getMessage(),
            'data' => null
        ];
    }
}

function getRevenueChartData($db, $time_period) {
    $labels = [];
    $data = [];
    
    try {
        switch ($time_period) {
            case 'today':
                // Hourly data for today
                for ($i = 23; $i >= 0; $i--) {
                    $hour = date('H:00', strtotime("-$i hours"));
                    $labels[] = $hour;
                    
                    $query = "SELECT COALESCE(SUM(total_amount), 0) as revenue 
                             FROM orders 
                             WHERE status IN ('paid', 'delivered') 
                             AND DATE(created_at) = CURDATE() 
                             AND HOUR(created_at) = " . (24 - $i - 1);
                    $result = $db->query($query);
                    if ($result) {
                        $data[] = (float)$result->fetch_assoc()['revenue'];
                    } else {
                        $data[] = 0;
                    }
                }
                break;
                
            case 'week':
                // Daily data for last 7 days
                for ($i = 6; $i >= 0; $i--) {
                    $date = date('M j', strtotime("-$i days"));
                    $labels[] = $date;
                    
                    $query = "SELECT COALESCE(SUM(total_amount), 0) as revenue 
                             FROM orders 
                             WHERE status IN ('paid', 'delivered') 
                             AND DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL $i DAY)";
                    $result = $db->query($query);
                    if ($result) {
                        $data[] = (float)$result->fetch_assoc()['revenue'];
                    } else {
                        $data[] = 0;
                    }
                }
                break;
                
            case 'month':
                // Weekly data for last 4 weeks
                for ($i = 3; $i >= 0; $i--) {
                    $start_date = date('M j', strtotime("-" . (($i + 1) * 7) . " days"));
                    $labels[] = "Week of $start_date";
                    
                    $query = "SELECT COALESCE(SUM(total_amount), 0) as revenue 
                             FROM orders 
                             WHERE status IN ('paid', 'delivered') 
                             AND created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL " . (($i + 1) * 7) . " DAY) 
                             AND DATE_SUB(CURDATE(), INTERVAL " . ($i * 7) . " DAY)";
                    $result = $db->query($query);
                    if ($result) {
                        $data[] = (float)$result->fetch_assoc()['revenue'];
                    } else {
                        $data[] = 0;
                    }
                }
                break;
                
            default:
                // Monthly data for last 6 months
                for ($i = 5; $i >= 0; $i--) {
                    $month = date('M Y', strtotime("-$i months"));
                    $labels[] = $month;
                    
                    $query = "SELECT COALESCE(SUM(total_amount), 0) as revenue 
                             FROM orders 
                             WHERE status IN ('paid', 'delivered') 
                             AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL $i MONTH))
                             AND MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL $i MONTH))";
                    $result = $db->query($query);
                    if ($result) {
                        $data[] = (float)$result->fetch_assoc()['revenue'];
                    } else {
                        $data[] = 0;
                    }
                }
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'total' => array_sum($data)
        ];
        
    } catch (Exception $e) {
        // Return empty data if there's an error
        return [
            'labels' => ['No Data'],
            'data' => [0],
            'total' => 0,
            'error' => $e->getMessage()
        ];
    }
}

function getTrafficSourcesData($db) {
    $query = "SELECT user_type, COUNT(*) as count FROM users WHERE is_active = 1 GROUP BY user_type";
    $result = $db->query($query);
    
    $labels = [];
    $data = [];
    $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'];
    $i = 0;
    
    while ($row = $result->fetch_assoc()) {
        $labels[] = ucfirst($row['user_type']);
        $data[] = (int)$row['count'];
        $i++;
    }
    
    return [
        'labels' => $labels,
        'data' => $data,
        'colors' => array_slice($colors, 0, count($labels))
    ];
}

function getSalesPerformanceData($db, $time_period) {
    $time_condition = getTimeCondition($time_period);
    
    $query = "SELECT 
                a.type as category,
                COUNT(oi.id) as order_count,
                COALESCE(SUM(oi.price * oi.quantity), 0) as revenue
              FROM artworks a
              LEFT JOIN order_items oi ON a.artwork_id = oi.artwork_id
              LEFT JOIN orders o ON oi.order_id = o.id
              WHERE o.status IN ('paid', 'delivered') $time_condition
              GROUP BY a.type
              ORDER BY revenue DESC";
    
    $result = $db->query($query);
    
    $categories = [];
    $orders = [];
    $revenues = [];
    
    while ($row = $result->fetch_assoc()) {
        $categories[] = ucfirst(str_replace('_', ' ', $row['category']));
        $orders[] = (int)$row['order_count'];
        $revenues[] = (float)$row['revenue'];
    }
    
    return [
        'categories' => $categories,
        'orders' => $orders,
        'revenues' => $revenues
    ];
}

function getRecentActivity($db) {
    try {
        $query = "
            (SELECT 
                'order' as activity_type,
                o.created_at as activity_time,
                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                'Purchase completed' as action_description,
                o.status as status,
                CONCAT('$', FORMAT(o.total_amount, 2)) as value,
                o.id as reference_id
            FROM orders o
            JOIN users u ON o.buyer_id = u.user_id
            WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR))
            
            UNION ALL
            
            (SELECT 
                'user_registration' as activity_type,
                u.created_at as activity_time,
                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                'Account registration' as action_description,
                IF(u.is_active = 1, 'active', 'inactive') as status,
                '-' as value,
                u.user_id as reference_id
            FROM users u
            WHERE u.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR))
            
            UNION ALL
            
            (SELECT 
                'artwork' as activity_type,
                a.created_at as activity_time,
                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                'New artwork uploaded' as action_description,
                IF(a.is_available = 1, 'available', 'unavailable') as status,
                CONCAT('$', FORMAT(a.price, 2)) as value,
                a.artwork_id as reference_id
            FROM artworks a
            JOIN users u ON a.artist_id = u.user_id
            WHERE a.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR))
            
            UNION ALL
            
            (SELECT 
                'course' as activity_type,
                c.created_at as activity_time,
                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                'New course created' as action_description,
                IF(c.is_published = 1, 'published', 'draft') as status,
                CONCAT('$', FORMAT(c.price, 2)) as value,
                c.course_id as reference_id
            FROM courses c
            JOIN users u ON c.artist_id = u.user_id
            WHERE c.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR))
            
            ORDER BY activity_time DESC
            LIMIT 20";

        $result = $db->query($query);
        $activities = [];

        while ($row = $result->fetch_assoc()) {
            $time_ago = getTimeAgo($row['activity_time']);
            
            $activities[] = [
                'time' => $time_ago,
                'user' => $row['user_name'],
                'action' => $row['action_description'],
                'status' => $row['status'],
                'value' => $row['value'],
                'type' => $row['activity_type'],
                'reference_id' => $row['reference_id'],
                'timestamp' => $row['activity_time']
            ];
        }

        return [
            'success' => true,
            'data' => $activities
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error fetching recent activity: ' . $e->getMessage()
        ];
    }
}

function getAnalyticsData($db, $time_period) {
    try {
        $time_condition = getTimeCondition($time_period);
        
        // Page views simulation (could be replaced with actual analytics data)
        $page_views = rand(40000, 50000);
        $avg_session_time = '2:' . sprintf('%02d', rand(20, 45));
        $click_rate = rand(60, 75) + (rand(0, 9) / 10);
        $bounce_rate = rand(15, 30) + (rand(0, 9) / 10);

        // Real data from database
        $total_artworks = $db->query("SELECT COUNT(*) as count FROM artworks WHERE is_available = 1")->fetch_assoc()['count'];
        $total_artists = $db->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'artist' AND is_active = 1")->fetch_assoc()['count'];
        $total_courses = $db->query("SELECT COUNT(*) as count FROM courses WHERE is_published = 1")->fetch_assoc()['count'];
        $avg_rating = $db->query("SELECT AVG(rating) as avg_rating FROM artist_reviews")->fetch_assoc()['avg_rating'];

        return [
            'success' => true,
            'data' => [
                'page_views' => [
                    'value' => $page_views,
                    'formatted' => number_format($page_views),
                    'change' => '+15.3%'
                ],
                'avg_session' => [
                    'value' => $avg_session_time,
                    'formatted' => $avg_session_time,
                    'change' => '+0:23'
                ],
                'click_rate' => [
                    'value' => $click_rate,
                    'formatted' => $click_rate . '%',
                    'change' => $click_rate > 67 ? '+2.1%' : '-2.1%'
                ],
                'bounce_rate' => [
                    'value' => $bounce_rate,
                    'formatted' => $bounce_rate . '%',
                    'change' => '-5.2%'
                ],
                'marketplace_metrics' => [
                    'total_artworks' => (int)$total_artworks,
                    'total_artists' => (int)$total_artists,
                    'total_courses' => (int)$total_courses,
                    'average_rating' => $avg_rating ? round((float)$avg_rating, 2) : 0
                ]
            ]
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error fetching analytics data: ' . $e->getMessage()
        ];
    }
}

// Helper functions
function getTimeCondition($time_period) {
    switch ($time_period) {
        case 'today':
            return "AND DATE(created_at) = CURDATE()";
        case 'week':
            return "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        case 'month':
            return "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        case 'year':
            return "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
        default:
            return "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    }
}

function getPreviousTimeCondition($time_period) {
    switch ($time_period) {
        case 'today':
            return "AND DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        case 'week':
            return "AND created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL 14 DAY) AND DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        case 'month':
            return "AND created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL 60 DAY) AND DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        case 'year':
            return "AND created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL 2 YEAR) AND DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
        default:
            return "AND created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL 14 DAY) AND DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    }
}

function calculatePercentageChange($previous, $current) {
    if ($previous == 0) {
        return $current > 0 ? 100 : 0;
    }
    return round((($current - $previous) / $previous) * 100, 1);
}

function getTimeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'Just now';
    if ($time < 3600) return floor($time/60) . ' min ago';
    if ($time < 86400) return floor($time/3600) . ' hr ago';
    if ($time < 604800) return floor($time/86400) . ' day' . (floor($time/86400) > 1 ? 's' : '') . ' ago';
    
    return date('M j, Y', strtotime($datetime));
}

?>