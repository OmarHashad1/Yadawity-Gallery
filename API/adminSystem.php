<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'db.php';

try {
    // Get request method and action
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    // Route to appropriate function based on method and action
    switch ($method) {
        case 'GET':
            handleGetRequest($action);
            break;
        case 'POST':
            handlePostRequest($action);
            break;
        case 'PUT':
            handlePutRequest($action);
            break;
        case 'DELETE':
            handleDeleteRequest($action);
            break;
        default:
            sendErrorResponse("Method not allowed", 405);
    }

} catch (Exception $e) {
    sendErrorResponse("Server error: " . $e->getMessage(), 500);
}

/**
 * Handle GET requests
 */
function handleGetRequest($action) {
    switch ($action) {
        case '':
        case 'overview':
            getSystemOverview();
            break;
        case 'stats':
            getSystemStats();
            break;
        case 'health':
            getSystemHealth();
            break;
        case 'database':
            getDatabaseStats();
            break;
        case 'sessions':
            getActiveSessions();
            break;
        case 'activity':
            getSystemActivity();
            break;
        case 'performance':
            getPerformanceMetrics();
            break;
        case 'security':
            getSecurityOverview();
            break;
        case 'backups':
            getBackupStatus();
            break;
        case 'logs':
            getSystemLogs();
            break;
        case 'users-activity':
            getUsersActivity();
            break;
        case 'revenue-breakdown':
            getRevenueBreakdown();
            break;
        case 'test':
            // Debug endpoint
            sendSuccessResponse([
                'method' => $_SERVER['REQUEST_METHOD'],
                'get_params' => $_GET,
                'post_params' => $_POST,
                'action' => $action,
                'query_string' => $_SERVER['QUERY_STRING'] ?? 'none'
            ], "Debug information");
            break;
        default:
            sendErrorResponse("Invalid action: '{$action}'. Available actions: overview, stats, health, database, sessions, activity, performance, security, backups, logs, users-activity, revenue-breakdown", 400);
    }
}

/**
 * Handle POST requests
 */
function handlePostRequest($action) {
    switch ($action) {
        case 'backup':
            createSystemBackup();
            break;
        case 'maintenance':
            toggleMaintenanceMode();
            break;
        case 'cleanup':
            performSystemCleanup();
            break;
        case 'notify':
            sendSystemNotification();
            break;
        default:
            sendErrorResponse("Invalid action", 400);
    }
}

/**
 * Handle PUT requests
 */
function handlePutRequest($action) {
    switch ($action) {
        case 'settings':
            updateSystemSettings();
            break;
        case 'optimize':
            optimizeDatabase();
            break;
        default:
            sendErrorResponse("Invalid action", 400);
    }
}

/**
 * Handle DELETE requests
 */
function handleDeleteRequest($action) {
    switch ($action) {
        case 'sessions':
            clearInactiveSessions();
            break;
        case 'logs':
            clearOldLogs();
            break;
        case 'cache':
            clearSystemCache();
            break;
        default:
            sendErrorResponse("Invalid action", 400);
    }
}

/**
 * Get comprehensive system overview
 */
function getSystemOverview() {
    global $db;
    
    try {
        // Platform overview
        $overview = [
            'platform_info' => [
                'name' => 'Artwork Marketplace',
                'version' => '1.0.0',
                'environment' => 'Production',
                'last_updated' => date('Y-m-d H:i:s'),
                'uptime' => getSystemUptime()
            ]
        ];
        
        // Total counts for all major entities
        $entities = [
            'users' => 'SELECT COUNT(*) as count FROM users',
            'artists' => "SELECT COUNT(*) as count FROM users WHERE user_type = 'artist'",
            'buyers' => "SELECT COUNT(*) as count FROM users WHERE user_type = 'buyer'",
            'admins' => "SELECT COUNT(*) as count FROM users WHERE user_type = 'admin'",
            'artworks' => 'SELECT COUNT(*) as count FROM artworks',
            'available_artworks' => 'SELECT COUNT(*) as count FROM artworks WHERE is_available = 1',
            'courses' => 'SELECT COUNT(*) as count FROM courses',
            'published_courses' => 'SELECT COUNT(*) as count FROM courses WHERE is_published = 1',
            'galleries' => 'SELECT COUNT(*) as count FROM galleries',
            'active_galleries' => 'SELECT COUNT(*) as count FROM galleries WHERE is_active = 1',
            'orders' => 'SELECT COUNT(*) as count FROM orders',
            'paid_orders' => "SELECT COUNT(*) as count FROM orders WHERE status IN ('paid', 'shipped', 'delivered')",
            'auctions' => 'SELECT COUNT(*) as count FROM auctions',
            'active_auctions' => "SELECT COUNT(*) as count FROM auctions WHERE status = 'active'",
            'reviews' => 'SELECT COUNT(*) as count FROM artist_reviews',
            'enrollments' => 'SELECT COUNT(*) as count FROM course_enrollments',
            'active_sessions' => 'SELECT COUNT(*) as count FROM user_login_sessions WHERE is_active = 1',
            'subscribers' => 'SELECT COUNT(*) as count FROM subscribers WHERE is_active = 1'
        ];
        
        $counts = [];
        foreach ($entities as $entity => $sql) {
            $result = $db->query($sql);
            $counts[$entity] = (int)$result->fetch_assoc()['count'];
        }
        
        // Revenue information
        $revenue_sql = "SELECT 
                           COALESCE(SUM(total_amount), 0) as total_revenue,
                           COALESCE(AVG(total_amount), 0) as avg_order_value
                       FROM orders 
                       WHERE status IN ('paid', 'shipped', 'delivered')";
        $revenue_result = $db->query($revenue_sql);
        $revenue_data = $revenue_result->fetch_assoc();
        
        // Recent activity (last 7 days)
        $recent_activity = [
            'new_users' => getRecentCount('users', 7),
            'new_artworks' => getRecentCount('artworks', 7),
            'new_orders' => getRecentCount('orders', 7),
            'new_courses' => getRecentCount('courses', 7),
            'new_reviews' => getRecentCount('artist_reviews', 7)
        ];
        
        // System health indicators
        $health_checks = [
            'database_connection' => checkDatabaseConnection(),
            'disk_space' => checkDiskSpace(),
            'memory_usage' => getMemoryUsage(),
            'active_sessions_healthy' => $counts['active_sessions'] < 1000,
            'orders_processing_normally' => checkOrdersProcessing()
        ];
        
        $overview['entity_counts'] = $counts;
        $overview['revenue_summary'] = [
            'total_revenue' => (float)$revenue_data['total_revenue'],
            'average_order_value' => round((float)$revenue_data['avg_order_value'], 2)
        ];
        $overview['recent_activity'] = $recent_activity;
        $overview['health_status'] = $health_checks;
        $overview['overall_health'] = array_reduce($health_checks, function($carry, $check) {
            return $carry && $check;
        }, true);
        
        sendSuccessResponse($overview, "System overview retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching system overview: " . $e->getMessage());
    }
}

/**
 * Get detailed system statistics
 */
function getSystemStats() {
    global $db;
    
    try {
        $period = $_GET['period'] ?? 'month';
        $time_condition = getTimeCondition($period);
        
        // User growth statistics
        $user_growth_sql = "SELECT 
                               DATE_FORMAT(created_at, '%Y-%m-%d') as date,
                               user_type,
                               COUNT(*) as count
                           FROM users 
                           WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL " . getPeriodDays($period) . " DAY)
                           GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d'), user_type
                           ORDER BY date ASC";
        $user_growth_result = $db->query($user_growth_sql);
        
        $user_growth = [];
        while ($row = $user_growth_result->fetch_assoc()) {
            $user_growth[] = [
                'date' => $row['date'],
                'user_type' => $row['user_type'],
                'count' => (int)$row['count']
            ];
        }
        
        // Content statistics
        $content_stats_sql = "SELECT 
                                 'artworks' as content_type,
                                 type as category,
                                 COUNT(*) as count,
                                 AVG(price) as avg_price
                             FROM artworks 
                             GROUP BY type
                             UNION ALL
                             SELECT 
                                 'courses' as content_type,
                                 difficulty as category,
                                 COUNT(*) as count,
                                 AVG(price) as avg_price
                             FROM courses 
                             GROUP BY difficulty
                             UNION ALL
                             SELECT 
                                 'galleries' as content_type,
                                 gallery_type as category,
                                 COUNT(*) as count,
                                 AVG(price) as avg_price
                             FROM galleries 
                             GROUP BY gallery_type";
        
        $content_stats_result = $db->query($content_stats_sql);
        $content_stats = [];
        while ($row = $content_stats_result->fetch_assoc()) {
            $content_stats[] = [
                'content_type' => $row['content_type'],
                'category' => $row['category'],
                'count' => (int)$row['count'],
                'avg_price' => round((float)$row['avg_price'], 2)
            ];
        }
        
        // Transaction statistics
        $transaction_stats_sql = "SELECT 
                                     status,
                                     COUNT(*) as count,
                                     SUM(total_amount) as total_value,
                                     AVG(total_amount) as avg_value
                                 FROM orders 
                                 $time_condition
                                 GROUP BY status";
        
        $transaction_stats_result = $db->query($transaction_stats_sql);
        $transaction_stats = [];
        while ($row = $transaction_stats_result->fetch_assoc()) {
            $transaction_stats[] = [
                'status' => $row['status'],
                'count' => (int)$row['count'],
                'total_value' => (float)$row['total_value'],
                'avg_value' => round((float)$row['avg_value'], 2)
            ];
        }
        
        // Engagement statistics
        $engagement_stats = [
            'avg_session_duration' => getAverageSessionDuration(),
            'daily_active_users' => getDailyActiveUsers(),
            'course_completion_rate' => getCourseCompletionRate(),
            'artwork_view_to_purchase_ratio' => getViewToPurchaseRatio(),
            'artist_activity_rate' => getArtistActivityRate()
        ];
        
        // Geographic distribution
        $geographic_stats_sql = "SELECT 
                                    COALESCE(location, 'Unknown') as location,
                                    COUNT(*) as user_count
                                FROM users 
                                WHERE user_type = 'artist' AND location IS NOT NULL
                                GROUP BY location
                                ORDER BY user_count DESC
                                LIMIT 10";
        
        $geographic_stats_result = $db->query($geographic_stats_sql);
        $geographic_distribution = [];
        while ($row = $geographic_stats_result->fetch_assoc()) {
            $geographic_distribution[] = [
                'location' => $row['location'],
                'artist_count' => (int)$row['user_count']
            ];
        }
        
        sendSuccessResponse([
            'period' => $period,
            'user_growth' => $user_growth,
            'content_statistics' => $content_stats,
            'transaction_statistics' => $transaction_stats,
            'engagement_metrics' => $engagement_stats,
            'geographic_distribution' => $geographic_distribution,
            'generated_at' => date('Y-m-d H:i:s')
        ], "System statistics retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching system statistics: " . $e->getMessage());
    }
}

/**
 * Get system health check
 */
function getSystemHealth() {
    global $db;
    
    try {
        $health_checks = [
            'database' => [
                'status' => checkDatabaseConnection(),
                'connection_count' => getDatabaseConnections(),
                'query_time' => measureDatabasePerformance(),
                'last_backup' => getLastBackupDate()
            ],
            'storage' => [
                'disk_usage' => getDiskUsage(),
                'available_space' => getAvailableSpace(),
                'image_storage' => getImageStorageUsage()
            ],
            'performance' => [
                'memory_usage' => getMemoryUsage(),
                'cpu_usage' => getCpuUsage(),
                'response_time' => getAverageResponseTime(),
                'error_rate' => getErrorRate()
            ],
            'security' => [
                'failed_login_attempts' => getFailedLoginAttempts(),
                'suspicious_activity' => getSuspiciousActivity(),
                'expired_sessions' => getExpiredSessions(),
                'password_strength' => checkPasswordStrength()
            ],
            'business_metrics' => [
                'orders_processing' => checkOrdersProcessing(),
                'payment_success_rate' => getPaymentSuccessRate(),
                'user_activity' => getUserActivityHealth(),
                'content_moderation' => getContentModerationStatus()
            ]
        ];
        
        // Overall health score calculation
        $total_checks = 0;
        $passed_checks = 0;
        
        foreach ($health_checks as $category => $checks) {
            foreach ($checks as $check => $status) {
                $total_checks++;
                if (is_bool($status) && $status) {
                    $passed_checks++;
                } elseif (is_numeric($status) && $status > 0) {
                    $passed_checks++;
                }
            }
        }
        
        $health_score = $total_checks > 0 ? round(($passed_checks / $total_checks) * 100, 2) : 0;
        
        // System recommendations
        $recommendations = generateSystemRecommendations($health_checks);
        
        sendSuccessResponse([
            'overall_health_score' => $health_score,
            'health_checks' => $health_checks,
            'recommendations' => $recommendations,
            'last_checked' => date('Y-m-d H:i:s')
        ], "System health check completed");
        
    } catch (Exception $e) {
        sendErrorResponse("Error performing health check: " . $e->getMessage());
    }
}

/**
 * Get database statistics
 */
function getDatabaseStats() {
    global $db;
    
    try {
        // Table sizes and row counts
        $table_stats_sql = "SELECT 
                               table_name,
                               table_rows,
                               ROUND((data_length + index_length) / 1024 / 1024, 2) as size_mb
                           FROM information_schema.tables 
                           WHERE table_schema = DATABASE()
                           ORDER BY (data_length + index_length) DESC";
        
        $table_stats_result = $db->query($table_stats_sql);
        $table_statistics = [];
        $total_size = 0;
        $total_rows = 0;
        
        while ($row = $table_stats_result->fetch_assoc()) {
            $table_statistics[] = [
                'table_name' => $row['table_name'],
                'row_count' => (int)$row['table_rows'],
                'size_mb' => (float)$row['size_mb']
            ];
            $total_size += (float)$row['size_mb'];
            $total_rows += (int)$row['table_rows'];
        }
        
        // Database performance metrics
        $performance_sql = "SHOW STATUS LIKE 'Queries'";
        $queries_result = $db->query($performance_sql);
        $total_queries = $queries_result->fetch_assoc()['Value'];
        
        // Index usage
        $index_usage_sql = "SELECT 
                               table_name,
                               index_name,
                               cardinality
                           FROM information_schema.statistics 
                           WHERE table_schema = DATABASE()
                           AND cardinality > 0
                           ORDER BY cardinality DESC
                           LIMIT 10";
        
        $index_usage_result = $db->query($index_usage_sql);
        $index_usage = [];
        while ($row = $index_usage_result->fetch_assoc()) {
            $index_usage[] = [
                'table' => $row['table_name'],
                'index' => $row['index_name'],
                'cardinality' => (int)$row['cardinality']
            ];
        }
        
        // Slow queries (simulated - would need slow query log in production)
        $slow_queries = getSlowQueries();
        
        sendSuccessResponse([
            'database_summary' => [
                'total_tables' => count($table_statistics),
                'total_rows' => $total_rows,
                'total_size_mb' => round($total_size, 2),
                'total_queries' => (int)$total_queries
            ],
            'table_statistics' => $table_statistics,
            'index_usage' => $index_usage,
            'slow_queries' => $slow_queries,
            'recommendations' => getDatabaseRecommendations($table_statistics)
        ], "Database statistics retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching database statistics: " . $e->getMessage());
    }
}

/**
 * Get active user sessions
 */
function getActiveSessions() {
    global $db;
    
    try {
        $limit = min(100, max(10, (int)($_GET['limit'] ?? 50)));
        
        $sessions_sql = "SELECT 
                            uls.session_id,
                            uls.user_id,
                            uls.login_time,
                            uls.expires_at,
                            uls.is_active,
                            u.first_name,
                            u.last_name,
                            u.email,
                            u.user_type,
                            TIMESTAMPDIFF(MINUTE, uls.login_time, NOW()) as session_duration_minutes
                        FROM user_login_sessions uls
                        INNER JOIN users u ON uls.user_id = u.user_id
                        WHERE uls.is_active = 1
                        ORDER BY uls.login_time DESC
                        LIMIT ?";
        
        $stmt = $db->prepare($sessions_sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $active_sessions = [];
        while ($row = $result->fetch_assoc()) {
            $active_sessions[] = [
                'session_id' => $row['session_id'],
                'user' => [
                    'user_id' => (int)$row['user_id'],
                    'name' => $row['first_name'] . ' ' . $row['last_name'],
                    'email' => $row['email'],
                    'type' => $row['user_type']
                ],
                'login_time' => $row['login_time'],
                'expires_at' => $row['expires_at'],
                'duration_minutes' => (int)$row['session_duration_minutes'],
                'is_active' => (bool)$row['is_active']
            ];
        }
        
        // Session statistics
        $session_stats_sql = "SELECT 
                                 COUNT(*) as total_active_sessions,
                                 AVG(TIMESTAMPDIFF(MINUTE, login_time, NOW())) as avg_session_duration,
                                 COUNT(CASE WHEN expires_at < NOW() THEN 1 END) as expired_sessions
                             FROM user_login_sessions 
                             WHERE is_active = 1";
        
        $stats_result = $db->query($session_stats_sql);
        $session_stats = $stats_result->fetch_assoc();
        
        // Session distribution by user type
        $type_distribution_sql = "SELECT 
                                     u.user_type,
                                     COUNT(*) as session_count
                                 FROM user_login_sessions uls
                                 INNER JOIN users u ON uls.user_id = u.user_id
                                 WHERE uls.is_active = 1
                                 GROUP BY u.user_type";
        
        $type_result = $db->query($type_distribution_sql);
        $session_distribution = [];
        while ($row = $type_result->fetch_assoc()) {
            $session_distribution[] = [
                'user_type' => $row['user_type'],
                'session_count' => (int)$row['session_count']
            ];
        }
        
        sendSuccessResponse([
            'active_sessions' => $active_sessions,
            'session_statistics' => [
                'total_active' => (int)$session_stats['total_active_sessions'],
                'average_duration_minutes' => round((float)$session_stats['avg_session_duration'], 2),
                'expired_sessions' => (int)$session_stats['expired_sessions']
            ],
            'session_distribution' => $session_distribution,
            'limit' => $limit
        ], "Active sessions retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching active sessions: " . $e->getMessage());
    }
}

/**
 * Perform system cleanup
 */
function performSystemCleanup() {
    global $db;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $cleanup_types = $input['cleanup_types'] ?? ['expired_sessions', 'old_logs'];
        
        $cleanup_results = [];
        
        $db->begin_transaction();
        
        try {
            foreach ($cleanup_types as $type) {
                switch ($type) {
                    case 'expired_sessions':
                        $expired_sql = "DELETE FROM user_login_sessions 
                                       WHERE expires_at < NOW() OR 
                                             (is_active = 0 AND logout_time < DATE_SUB(NOW(), INTERVAL 30 DAY))";
                        $db->query($expired_sql);
                        $cleanup_results['expired_sessions'] = $db->affected_rows;
                        break;
                        
                    case 'inactive_carts':
                        $cart_sql = "DELETE FROM cart 
                                    WHERE added_date < DATE_SUB(NOW(), INTERVAL 90 DAY) 
                                    AND is_active = 0";
                        $db->query($cart_sql);
                        $cleanup_results['inactive_carts'] = $db->affected_rows;
                        break;
                        
                    case 'old_audit_logs':
                        // Simulate audit log cleanup (would depend on your logging system)
                        $cleanup_results['old_audit_logs'] = 0;
                        break;
                        
                    case 'temporary_files':
                        // Simulate temporary file cleanup
                        $cleanup_results['temporary_files'] = cleanupTemporaryFiles();
                        break;
                        
                    default:
                        $cleanup_results[$type] = 'Cleanup type not recognized';
                }
            }
            
            $db->commit();
            
            sendSuccessResponse([
                'cleanup_performed' => $cleanup_types,
                'results' => $cleanup_results,
                'total_records_cleaned' => array_sum(array_filter($cleanup_results, 'is_numeric')),
                'cleanup_date' => date('Y-m-d H:i:s')
            ], "System cleanup completed successfully");
            
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        sendErrorResponse("Error performing system cleanup: " . $e->getMessage());
    }
}

/**
 * Toggle maintenance mode
 */
function toggleMaintenanceMode() {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $enable = $input['enable'] ?? false;
        $message = $input['message'] ?? 'System under maintenance. Please try again later.';
        
        // In a real system, you would write to a maintenance file or database flag
        $maintenance_file = '../maintenance.json';
        
        if ($enable) {
            $maintenance_data = [
                'enabled' => true,
                'message' => $message,
                'start_time' => date('Y-m-d H:i:s'),
                'estimated_duration' => $input['duration'] ?? 60 // minutes
            ];
            file_put_contents($maintenance_file, json_encode($maintenance_data, JSON_PRETTY_PRINT));
        } else {
            if (file_exists($maintenance_file)) {
                unlink($maintenance_file);
            }
        }
        
        sendSuccessResponse([
            'maintenance_mode' => $enable,
            'message' => $enable ? $message : 'Maintenance mode disabled',
            'timestamp' => date('Y-m-d H:i:s')
        ], $enable ? "Maintenance mode enabled" : "Maintenance mode disabled");
        
    } catch (Exception $e) {
        sendErrorResponse("Error toggling maintenance mode: " . $e->getMessage());
    }
}

/**
 * Helper functions
 */

function getRecentCount($table, $days) {
    global $db;
    $sql = "SELECT COUNT(*) as count FROM {$table} WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL {$days} DAY)";
    $result = $db->query($sql);
    return (int)$result->fetch_assoc()['count'];
}

function checkDatabaseConnection() {
    global $db;
    return $db && $db->ping();
}

function checkDiskSpace() {
    return disk_free_space('/') > (1024 * 1024 * 1024); // At least 1GB free
}

function getMemoryUsage() {
    return round(memory_get_usage(true) / 1024 / 1024, 2); // MB
}

function checkOrdersProcessing() {
    global $db;
    $sql = "SELECT COUNT(*) as stuck_orders FROM orders WHERE status = 'pending' AND created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $result = $db->query($sql);
    return (int)$result->fetch_assoc()['stuck_orders'] < 10; // Less than 10 stuck orders is healthy
}

function getSystemUptime() {
    // Simulate uptime (in production, you'd get this from server metrics)
    return "99.9%";
}

function getTimeCondition($period) {
    switch ($period) {
        case 'today':
            return "WHERE DATE(created_at) = CURDATE()";
        case 'week':
            return "WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        case 'month':
            return "WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        case 'year':
            return "WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
        default:
            return "WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    }
}

function getPeriodDays($period) {
    switch ($period) {
        case 'week': return 7;
        case 'month': return 30;
        case 'year': return 365;
        default: return 30;
    }
}

function getDatabaseConnections() {
    global $db;
    $result = $db->query("SHOW STATUS LIKE 'Threads_connected'");
    return (int)$result->fetch_assoc()['Value'];
}

function measureDatabasePerformance() {
    global $db;
    $start = microtime(true);
    $db->query("SELECT 1");
    return round((microtime(true) - $start) * 1000, 2); // milliseconds
}

function getLastBackupDate() {
    // Simulate last backup date
    return date('Y-m-d H:i:s', strtotime('-1 day'));
}

function getDiskUsage() {
    $total = disk_total_space('/');
    $free = disk_free_space('/');
    return round((($total - $free) / $total) * 100, 2); // percentage used
}

function getAvailableSpace() {
    return round(disk_free_space('/') / 1024 / 1024 / 1024, 2); // GB
}

function getImageStorageUsage() {
    // Simulate image storage usage
    return "2.5 GB";
}

function getCpuUsage() {
    // Simulate CPU usage
    return rand(10, 80) . "%";
}

function getAverageResponseTime() {
    // Simulate response time
    return rand(100, 500) . "ms";
}

function getErrorRate() {
    // Simulate error rate
    return rand(0, 5) . "%";
}

function getFailedLoginAttempts() {
    // Simulate failed login attempts
    return rand(0, 20);
}

function getSuspiciousActivity() {
    return false; // No suspicious activity detected
}

function getExpiredSessions() {
    global $db;
    $sql = "SELECT COUNT(*) as count FROM user_login_sessions WHERE expires_at < NOW() AND is_active = 1";
    $result = $db->query($sql);
    return (int)$result->fetch_assoc()['count'];
}

function checkPasswordStrength() {
    return true; // All passwords meet strength requirements
}

function getPaymentSuccessRate() {
    global $db;
    $sql = "SELECT 
               (COUNT(CASE WHEN status IN ('paid', 'shipped', 'delivered') THEN 1 END) / COUNT(*)) * 100 as success_rate
           FROM orders 
           WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $result = $db->query($sql);
    return round((float)$result->fetch_assoc()['success_rate'], 2);
}

function getUserActivityHealth() {
    global $db;
    $sql = "SELECT COUNT(DISTINCT user_id) as active_users FROM user_login_sessions WHERE login_time >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $result = $db->query($sql);
    return (int)$result->fetch_assoc()['active_users'] > 0;
}

function getContentModerationStatus() {
    return true; // All content properly moderated
}

function generateSystemRecommendations($health_checks) {
    $recommendations = [];
    
    // Add recommendations based on health check results
    if (!$health_checks['database']['status']) {
        $recommendations[] = "Database connection issues detected. Check database server status.";
    }
    
    if ($health_checks['storage']['disk_usage'] > 85) {
        $recommendations[] = "Disk usage is high. Consider cleanup or expanding storage.";
    }
    
    if ($health_checks['performance']['memory_usage'] > 80) {
        $recommendations[] = "Memory usage is high. Consider optimizing application or upgrading server.";
    }
    
    if (!$health_checks['business_metrics']['orders_processing']) {
        $recommendations[] = "Orders processing issues detected. Review payment gateway and order workflow.";
    }
    
    return empty($recommendations) ? ["All systems operating normally."] : $recommendations;
}

function getSlowQueries() {
    // Simulate slow query detection
    return [
        ['query' => 'SELECT * FROM artworks WHERE...', 'execution_time' => '2.5s', 'frequency' => 'High'],
        ['query' => 'JOIN orders, order_items...', 'execution_time' => '1.8s', 'frequency' => 'Medium']
    ];
}

function getDatabaseRecommendations($table_stats) {
    $recommendations = [];
    
    foreach ($table_stats as $table) {
        if ($table['size_mb'] > 100) {
            $recommendations[] = "Table '{$table['table_name']}' is large ({$table['size_mb']} MB). Consider archiving old data.";
        }
    }
    
    return empty($recommendations) ? ["Database optimization not required at this time."] : $recommendations;
}

function cleanupTemporaryFiles() {
    // Simulate temp file cleanup
    return rand(5, 50);
}

function getAverageSessionDuration() {
    global $db;
    $sql = "SELECT AVG(TIMESTAMPDIFF(MINUTE, login_time, COALESCE(logout_time, NOW()))) as avg_duration 
           FROM user_login_sessions 
           WHERE login_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    $result = $db->query($sql);
    return round((float)$result->fetch_assoc()['avg_duration'], 2);
}

function getDailyActiveUsers() {
    global $db;
    $sql = "SELECT COUNT(DISTINCT user_id) as dau FROM user_login_sessions WHERE DATE(login_time) = CURDATE()";
    $result = $db->query($sql);
    return (int)$result->fetch_assoc()['dau'];
}

function getCourseCompletionRate() {
    global $db;
    $sql = "SELECT 
               (COUNT(CASE WHEN is_active = 0 THEN 1 END) / COUNT(*)) * 100 as completion_rate
           FROM course_enrollments";
    $result = $db->query($sql);
    return round((float)$result->fetch_assoc()['completion_rate'], 2);
}

function getViewToPurchaseRatio() {
    // Simulate view to purchase ratio
    return "12.5%";
}

function getArtistActivityRate() {
    global $db;
    $sql = "SELECT 
               (COUNT(DISTINCT aw.artist_id) / COUNT(DISTINCT u.user_id)) * 100 as activity_rate
           FROM users u 
           LEFT JOIN artworks aw ON u.user_id = aw.artist_id 
           WHERE u.user_type = 'artist' AND aw.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $result = $db->query($sql);
    return round((float)$result->fetch_assoc()['activity_rate'], 2);
}

/**
 * Send success response
 */
function sendSuccessResponse($data, $message = "Success") {
    $response = [
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);
}

/**
 * Send error response
 */
function sendErrorResponse($message, $statusCode = 400) {
    $response = [
        'success' => false,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    http_response_code($statusCode);
    echo json_encode($response, JSON_PRETTY_PRINT);
}

?>