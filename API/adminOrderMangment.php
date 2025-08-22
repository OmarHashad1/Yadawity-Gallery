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
    $order_id = $_GET['order_id'] ?? '';
    
    // Route to appropriate function based on method and action
    switch ($method) {
        case 'GET':
            handleGetRequest($action, $order_id);
            break;
        case 'POST':
            handlePostRequest($action);
            break;
        case 'PUT':
            handlePutRequest($action, $order_id);
            break;
        case 'DELETE':
            handleDeleteRequest($action, $order_id);
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
function handleGetRequest($action, $order_id) {
    global $db;
    
    // If no action specified, default to list
    if (empty($action)) {
        $action = 'list';
    }
    
    switch ($action) {
        case 'list':
        case '': // Handle empty action as list
            getAllOrdersAdmin();
            break;
        case 'details':
            if (!$order_id) {
                sendErrorResponse("Order ID is required", 400);
                return;
            }
            getOrderDetails($order_id);
            break;
        case 'items':
            if (!$order_id) {
                sendErrorResponse("Order ID is required", 400);
                return;
            }
            getOrderItems($order_id);
            break;
        case 'stats':
            getOrderStats();
            break;
        case 'revenue':
            getRevenueAnalytics();
            break;
        case 'customers':
            getTopCustomers();
            break;
        case 'artists':
            getTopArtists();
            break;
        case 'export':
            exportOrders();
            break;
        case 'test':
            // Debug endpoint to see what parameters we're receiving
            sendSuccessResponse([
                'method' => $_SERVER['REQUEST_METHOD'],
                'get_params' => $_GET,
                'post_params' => $_POST,
                'action' => $action,
                'order_id' => $order_id,
                'query_string' => $_SERVER['QUERY_STRING'] ?? 'none'
            ], "Debug information");
            break;
        default:
            sendErrorResponse("Invalid action: '{$action}'. Available actions: list, details, items, stats, revenue, customers, artists, export", 400);
    }
}

/**
 * Handle POST requests
 */
function handlePostRequest($action) {
    switch ($action) {
        case 'create':
            createOrder();
            break;
        case 'bulk-update':
            bulkUpdateOrders();
            break;
        case 'refund':
            processRefund();
            break;
        default:
            sendErrorResponse("Invalid action", 400);
    }
}

/**
 * Handle PUT requests
 */
function handlePutRequest($action, $order_id) {
    if (!$order_id) {
        sendErrorResponse("Order ID is required", 400);
        return;
    }
    
    switch ($action) {
        case 'status':
            updateOrderStatus($order_id);
            break;
        case 'shipping':
            updateShippingAddress($order_id);
            break;
        case 'amount':
            updateOrderAmount($order_id);
            break;
        default:
            sendErrorResponse("Invalid action", 400);
    }
}

/**
 * Handle DELETE requests
 */
function handleDeleteRequest($action, $order_id) {
    if (!$order_id) {
        sendErrorResponse("Order ID is required", 400);
        return;
    }
    
    switch ($action) {
        case 'cancel':
            cancelOrder($order_id);
            break;
        case 'delete':
            deleteOrder($order_id);
            break;
        default:
            sendErrorResponse("Invalid action", 400);
    }
}

/**
 * Get all orders with admin details
 */
function getAllOrdersAdmin() {
    global $db;
    
    try {
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $buyer_id = $_GET['buyer_id'] ?? '';
        $date_from = $_GET['date_from'] ?? '';
        $date_to = $_GET['date_to'] ?? '';
        $min_amount = $_GET['min_amount'] ?? '';
        $max_amount = $_GET['max_amount'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = min(100, max(10, (int)($_GET['limit'] ?? 20)));
        $offset = ($page - 1) * $limit;
        
        // Build WHERE conditions
        $conditions = ["1=1"];
        $params = [];
        
        if (!empty($search)) {
            $conditions[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR o.id LIKE ?)";
            $search_param = "%{$search}%";
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
        }
        
        if (!empty($status)) {
            $conditions[] = "o.status = ?";
            $params[] = $status;
        }
        
        if (!empty($buyer_id)) {
            $conditions[] = "o.buyer_id = ?";
            $params[] = $buyer_id;
        }
        
        if (!empty($date_from)) {
            $conditions[] = "DATE(o.created_at) >= ?";
            $params[] = $date_from;
        }
        
        if (!empty($date_to)) {
            $conditions[] = "DATE(o.created_at) <= ?";
            $params[] = $date_to;
        }
        
        if (!empty($min_amount)) {
            $conditions[] = "o.total_amount >= ?";
            $params[] = $min_amount;
        }
        
        if (!empty($max_amount)) {
            $conditions[] = "o.total_amount <= ?";
            $params[] = $max_amount;
        }
        
        $where_clause = "WHERE " . implode(" AND ", $conditions);
        
        // Main query
        $sql = "SELECT 
                    o.id as order_id,
                    o.total_amount,
                    o.status,
                    o.shipping_address,
                    o.created_at,
                    
                    -- Buyer details
                    u.user_id as buyer_id,
                    u.first_name as buyer_first_name,
                    u.last_name as buyer_last_name,
                    u.email as buyer_email,
                    u.phone as buyer_phone,
                    u.profile_picture as buyer_profile_picture,
                    
                    -- Order statistics
                    (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as item_count,
                    (SELECT COUNT(DISTINCT aw.artist_id) FROM order_items oi INNER JOIN artworks aw ON oi.artwork_id = aw.artwork_id WHERE oi.order_id = o.id) as artist_count
                    
                FROM orders o
                INNER JOIN users u ON o.buyer_id = u.user_id
                $where_clause
                ORDER BY o.created_at DESC
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $types = str_repeat('s', count($params) - 2) . 'ii';
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = formatOrderAdminData($row);
        }
        
        // Get total count for pagination
        $count_sql = "SELECT COUNT(*) as total 
                      FROM orders o
                      INNER JOIN users u ON o.buyer_id = u.user_id
                      $where_clause";
        
        $count_params = array_slice($params, 0, -2); // Remove limit and offset
        $count_stmt = $db->prepare($count_sql);
        if (!empty($count_params)) {
            $count_types = str_repeat('s', count($count_params));
            $count_stmt->bind_param($count_types, ...$count_params);
        }
        $count_stmt->execute();
        $total_count = $count_stmt->get_result()->fetch_assoc()['total'];
        
        sendSuccessResponse([
            'orders' => $orders,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total_count' => (int)$total_count,
                'total_pages' => ceil($total_count / $limit)
            ]
        ], "Orders retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching orders: " . $e->getMessage());
    }
}

/**
 * Get detailed order information
 */
function getOrderDetails($order_id) {
    global $db;
    
    try {
        $sql = "SELECT 
                    o.id as order_id,
                    o.total_amount,
                    o.status,
                    o.shipping_address,
                    o.created_at,
                    
                    -- Buyer details
                    u.user_id as buyer_id,
                    u.first_name as buyer_first_name,
                    u.last_name as buyer_last_name,
                    u.email as buyer_email,
                    u.phone as buyer_phone,
                    u.profile_picture as buyer_profile_picture,
                    u.user_type as buyer_type
                    
                FROM orders o
                INNER JOIN users u ON o.buyer_id = u.user_id
                WHERE o.id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $order_data = formatOrderAdminData($row);
            
            // Get order items with artwork and artist details
            $items_sql = "SELECT 
                              oi.id as item_id,
                              oi.price as item_price,
                              oi.quantity,
                              oi.created_at as item_created_at,
                              
                              -- Artwork details
                              aw.artwork_id,
                              aw.title as artwork_title,
                              aw.description as artwork_description,
                              aw.price as artwork_original_price,
                              aw.dimensions,
                              aw.year,
                              aw.material,
                              aw.artwork_image,
                              aw.type as artwork_type,
                              
                              -- Artist details
                              artist.user_id as artist_id,
                              artist.first_name as artist_first_name,
                              artist.last_name as artist_last_name,
                              artist.email as artist_email,
                              artist.profile_picture as artist_profile_picture,
                              artist.art_specialty
                              
                          FROM order_items oi
                          INNER JOIN artworks aw ON oi.artwork_id = aw.artwork_id
                          INNER JOIN users artist ON aw.artist_id = artist.user_id
                          WHERE oi.order_id = ?
                          ORDER BY oi.created_at ASC";
            
            $items_stmt = $db->prepare($items_sql);
            $items_stmt->bind_param("i", $order_id);
            $items_stmt->execute();
            $items_result = $items_stmt->get_result();
            
            $items = [];
            $total_items = 0;
            $unique_artists = [];
            
            while ($item_row = $items_result->fetch_assoc()) {
                $items[] = [
                    'item_id' => (int)$item_row['item_id'],
                    'quantity' => (int)$item_row['quantity'],
                    'price' => (float)$item_row['item_price'],
                    'subtotal' => (float)$item_row['item_price'] * (int)$item_row['quantity'],
                    'item_created_at' => $item_row['item_created_at'],
                    'artwork' => [
                        'artwork_id' => (int)$item_row['artwork_id'],
                        'title' => $item_row['artwork_title'],
                        'description' => $item_row['artwork_description'],
                        'original_price' => (float)$item_row['artwork_original_price'],
                        'dimensions' => $item_row['dimensions'],
                        'year' => $item_row['year'],
                        'material' => $item_row['material'],
                        'image' => $item_row['artwork_image'],
                        'type' => $item_row['artwork_type']
                    ],
                    'artist' => [
                        'artist_id' => (int)$item_row['artist_id'],
                        'name' => $item_row['artist_first_name'] . ' ' . $item_row['artist_last_name'],
                        'email' => $item_row['artist_email'],
                        'profile_picture' => $item_row['artist_profile_picture'],
                        'specialty' => $item_row['art_specialty']
                    ]
                ];
                
                $total_items += (int)$item_row['quantity'];
                $unique_artists[$item_row['artist_id']] = $item_row['artist_first_name'] . ' ' . $item_row['artist_last_name'];
            }
            
            $order_data['items'] = $items;
            $order_data['order_summary'] = [
                'total_items' => $total_items,
                'unique_artworks' => count($items),
                'unique_artists' => count($unique_artists),
                'artists_involved' => array_values($unique_artists)
            ];
            
            sendSuccessResponse($order_data, "Order details retrieved successfully");
        } else {
            sendErrorResponse("Order not found", 404);
        }
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching order details: " . $e->getMessage());
    }
}

/**
 * Update order status
 */
function updateOrderStatus($order_id) {
    global $db;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['status'])) {
            sendErrorResponse("Status is required", 400);
            return;
        }
        
        $valid_statuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($input['status'], $valid_statuses)) {
            sendErrorResponse("Invalid status. Valid options: " . implode(', ', $valid_statuses), 400);
            return;
        }
        
        // Check if order exists
        $check_sql = "SELECT id, status, total_amount FROM orders WHERE id = ?";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("i", $order_id);
        $check_stmt->execute();
        $order = $check_stmt->get_result()->fetch_assoc();
        
        if (!$order) {
            sendErrorResponse("Order not found", 404);
            return;
        }
        
        $old_status = $order['status'];
        $new_status = $input['status'];
        
        // Business logic for status transitions
        if ($old_status === 'delivered' && $new_status !== 'delivered') {
            sendErrorResponse("Cannot change status of delivered order", 400);
            return;
        }
        
        if ($old_status === 'cancelled' && $new_status !== 'cancelled') {
            sendErrorResponse("Cannot change status of cancelled order", 400);
            return;
        }
        
        $db->begin_transaction();
        
        try {
            // Update order status
            $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
            $update_stmt = $db->prepare($update_sql);
            $update_stmt->bind_param("si", $new_status, $order_id);
            $update_stmt->execute();
            
            // Handle artwork availability based on status change
            if ($new_status === 'cancelled' && $old_status !== 'cancelled') {
                // Make artworks available again
                $artwork_sql = "UPDATE artworks aw 
                               INNER JOIN order_items oi ON aw.artwork_id = oi.artwork_id 
                               SET aw.is_available = 1 
                               WHERE oi.order_id = ?";
                $artwork_stmt = $db->prepare($artwork_sql);
                $artwork_stmt->bind_param("i", $order_id);
                $artwork_stmt->execute();
            } elseif ($new_status === 'paid' && $old_status === 'pending') {
                // Mark artworks as sold
                $artwork_sql = "UPDATE artworks aw 
                               INNER JOIN order_items oi ON aw.artwork_id = oi.artwork_id 
                               SET aw.is_available = 0 
                               WHERE oi.order_id = ?";
                $artwork_stmt = $db->prepare($artwork_sql);
                $artwork_stmt->bind_param("i", $order_id);
                $artwork_stmt->execute();
            }
            
            $db->commit();
            
            sendSuccessResponse([
                'order_id' => $order_id,
                'old_status' => $old_status,
                'new_status' => $new_status,
                'total_amount' => (float)$order['total_amount']
            ], "Order status updated successfully");
            
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        sendErrorResponse("Error updating order status: " . $e->getMessage());
    }
}

/**
 * Get order statistics
 */
function getOrderStats() {
    global $db;
    
    try {
        $time_period = $_GET['period'] ?? 'month';
        $time_condition = getTimeCondition($time_period);
        
        // Total orders
        $total_sql = "SELECT COUNT(*) as total FROM orders $time_condition";
        $total_result = $db->query($total_sql);
        $total_orders = $total_result->fetch_assoc()['total'];
        
        // Orders by status
        $status_sql = "SELECT status, COUNT(*) as count FROM orders $time_condition GROUP BY status";
        $status_result = $db->query($status_sql);
        $order_status_stats = [];
        while ($row = $status_result->fetch_assoc()) {
            $order_status_stats[$row['status']] = (int)$row['count'];
        }
        
        // Total revenue (paid and delivered orders)
        $revenue_sql = "SELECT COALESCE(SUM(total_amount), 0) as revenue 
                       FROM orders 
                       WHERE status IN ('paid', 'shipped', 'delivered') $time_condition";
        $revenue_result = $db->query($revenue_sql);
        $total_revenue = $revenue_result->fetch_assoc()['revenue'];
        
        // Average order value
        $avg_order_sql = "SELECT AVG(total_amount) as avg_amount 
                         FROM orders 
                         WHERE status IN ('paid', 'shipped', 'delivered') $time_condition";
        $avg_order_result = $db->query($avg_order_sql);
        $avg_order_value = $avg_order_result->fetch_assoc()['avg_amount'] ?: 0;
        
        // Total items sold
        $items_sql = "SELECT COALESCE(SUM(oi.quantity), 0) as total_items 
                     FROM order_items oi 
                     INNER JOIN orders o ON oi.order_id = o.id 
                     WHERE o.status IN ('paid', 'shipped', 'delivered') $time_condition";
        $items_result = $db->query($items_sql);
        $total_items_sold = $items_result->fetch_assoc()['total_items'];
        
        // Top selling artwork types
        $artwork_types_sql = "SELECT aw.type, COUNT(*) as sold_count, SUM(oi.price * oi.quantity) as revenue
                             FROM order_items oi
                             INNER JOIN orders o ON oi.order_id = o.id
                             INNER JOIN artworks aw ON oi.artwork_id = aw.artwork_id
                             WHERE o.status IN ('paid', 'shipped', 'delivered') $time_condition
                             GROUP BY aw.type
                             ORDER BY sold_count DESC
                             LIMIT 5";
        $artwork_types_result = $db->query($artwork_types_sql);
        $top_artwork_types = [];
        while ($row = $artwork_types_result->fetch_assoc()) {
            $top_artwork_types[] = [
                'type' => ucfirst(str_replace('_', ' ', $row['type'])),
                'sold_count' => (int)$row['sold_count'],
                'revenue' => (float)$row['revenue']
            ];
        }
        
        // Monthly revenue trend (last 6 months)
        $revenue_trend_sql = "SELECT 
                                DATE_FORMAT(created_at, '%Y-%m') as month,
                                SUM(total_amount) as revenue,
                                COUNT(*) as order_count
                             FROM orders 
                             WHERE status IN ('paid', 'shipped', 'delivered')
                             AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                             ORDER BY month DESC";
        $revenue_trend_result = $db->query($revenue_trend_sql);
        $revenue_trend = [];
        while ($row = $revenue_trend_result->fetch_assoc()) {
            $revenue_trend[] = [
                'month' => $row['month'],
                'revenue' => (float)$row['revenue'],
                'order_count' => (int)$row['order_count']
            ];
        }
        
        sendSuccessResponse([
            'overview' => [
                'total_orders' => (int)$total_orders,
                'total_revenue' => (float)$total_revenue,
                'average_order_value' => round((float)$avg_order_value, 2),
                'total_items_sold' => (int)$total_items_sold
            ],
            'order_status_distribution' => $order_status_stats,
            'top_artwork_types' => $top_artwork_types,
            'revenue_trend' => array_reverse($revenue_trend), // Show oldest to newest
            'period' => $time_period
        ], "Order statistics retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching order statistics: " . $e->getMessage());
    }
}

/**
 * Get revenue analytics
 */
function getRevenueAnalytics() {
    global $db;
    
    try {
        $period = $_GET['period'] ?? 'month';
        
        // Revenue by period
        switch ($period) {
            case 'week':
                $group_by = "DATE(created_at)";
                $date_format = "%Y-%m-%d";
                $interval = "7 DAY";
                break;
            case 'month':
                $group_by = "DATE_FORMAT(created_at, '%Y-%m-%d')";
                $date_format = "%Y-%m-%d";
                $interval = "30 DAY";
                break;
            case 'year':
                $group_by = "DATE_FORMAT(created_at, '%Y-%m')";
                $date_format = "%Y-%m";
                $interval = "12 MONTH";
                break;
            default:
                $group_by = "DATE_FORMAT(created_at, '%Y-%m-%d')";
                $date_format = "%Y-%m-%d";
                $interval = "30 DAY";
        }
        
        $revenue_sql = "SELECT 
                           DATE_FORMAT(created_at, '$date_format') as period,
                           SUM(total_amount) as revenue,
                           COUNT(*) as order_count,
                           AVG(total_amount) as avg_order_value
                       FROM orders 
                       WHERE status IN ('paid', 'shipped', 'delivered')
                       AND created_at >= DATE_SUB(CURDATE(), INTERVAL $interval)
                       GROUP BY $group_by
                       ORDER BY period ASC";
        
        $revenue_result = $db->query($revenue_sql);
        $revenue_data = [];
        while ($row = $revenue_result->fetch_assoc()) {
            $revenue_data[] = [
                'period' => $row['period'],
                'revenue' => (float)$row['revenue'],
                'order_count' => (int)$row['order_count'],
                'avg_order_value' => round((float)$row['avg_order_value'], 2)
            ];
        }
        
        // Revenue by artist
        $artist_revenue_sql = "SELECT 
                                  u.user_id,
                                  u.first_name,
                                  u.last_name,
                                  SUM(oi.price * oi.quantity) as revenue,
                                  COUNT(DISTINCT o.id) as order_count,
                                  COUNT(oi.id) as items_sold
                              FROM order_items oi
                              INNER JOIN orders o ON oi.order_id = o.id
                              INNER JOIN artworks aw ON oi.artwork_id = aw.artwork_id
                              INNER JOIN users u ON aw.artist_id = u.user_id
                              WHERE o.status IN ('paid', 'shipped', 'delivered')
                              AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL $interval)
                              GROUP BY u.user_id
                              ORDER BY revenue DESC
                              LIMIT 10";
        
        $artist_revenue_result = $db->query($artist_revenue_sql);
        $artist_revenue = [];
        while ($row = $artist_revenue_result->fetch_assoc()) {
            $artist_revenue[] = [
                'artist_id' => (int)$row['user_id'],
                'name' => $row['first_name'] . ' ' . $row['last_name'],
                'revenue' => (float)$row['revenue'],
                'order_count' => (int)$row['order_count'],
                'items_sold' => (int)$row['items_sold']
            ];
        }
        
        sendSuccessResponse([
            'period' => $period,
            'revenue_timeline' => $revenue_data,
            'top_artists_by_revenue' => $artist_revenue,
            'summary' => [
                'total_revenue' => array_sum(array_column($revenue_data, 'revenue')),
                'total_orders' => array_sum(array_column($revenue_data, 'order_count')),
                'periods_tracked' => count($revenue_data)
            ]
        ], "Revenue analytics retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching revenue analytics: " . $e->getMessage());
    }
}

/**
 * Get top customers
 */
function getTopCustomers() {
    global $db;
    
    try {
        $limit = min(50, max(5, (int)($_GET['limit'] ?? 10)));
        $period = $_GET['period'] ?? 'all';
        
        $time_condition = $period === 'all' ? '' : getTimeCondition($period);
        
        $sql = "SELECT 
                    u.user_id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.profile_picture,
                    COUNT(o.id) as total_orders,
                    SUM(o.total_amount) as total_spent,
                    AVG(o.total_amount) as avg_order_value,
                    MAX(o.created_at) as last_order_date,
                    (SELECT COUNT(*) FROM order_items oi INNER JOIN orders o2 ON oi.order_id = o2.id WHERE o2.buyer_id = u.user_id AND o2.status IN ('paid', 'shipped', 'delivered')) as total_items_purchased
                FROM users u
                INNER JOIN orders o ON u.user_id = o.buyer_id
                WHERE o.status IN ('paid', 'shipped', 'delivered')
                $time_condition
                GROUP BY u.user_id
                ORDER BY total_spent DESC
                LIMIT ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $customers = [];
        while ($row = $result->fetch_assoc()) {
            $customers[] = [
                'customer_id' => (int)$row['user_id'],
                'name' => $row['first_name'] . ' ' . $row['last_name'],
                'email' => $row['email'],
                'profile_picture' => $row['profile_picture'],
                'total_orders' => (int)$row['total_orders'],
                'total_spent' => (float)$row['total_spent'],
                'avg_order_value' => round((float)$row['avg_order_value'], 2),
                'total_items_purchased' => (int)$row['total_items_purchased'],
                'last_order_date' => $row['last_order_date']
            ];
        }
        
        sendSuccessResponse([
            'customers' => $customers,
            'period' => $period,
            'limit' => $limit
        ], "Top customers retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching top customers: " . $e->getMessage());
    }
}

/**
 * Cancel order
 */
function cancelOrder($order_id) {
    global $db;
    
    try {
        $db->begin_transaction();
        
        // Check if order exists and can be cancelled
        $check_sql = "SELECT id, status, total_amount FROM orders WHERE id = ?";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("i", $order_id);
        $check_stmt->execute();
        $order = $check_stmt->get_result()->fetch_assoc();
        
        if (!$order) {
            sendErrorResponse("Order not found", 404);
            return;
        }
        
        if (in_array($order['status'], ['delivered', 'cancelled'])) {
            sendErrorResponse("Cannot cancel order with status: " . $order['status'], 400);
            return;
        }
        
        // Cancel the order
        $cancel_sql = "UPDATE orders SET status = 'cancelled' WHERE id = ?";
        $cancel_stmt = $db->prepare($cancel_sql);
        $cancel_stmt->bind_param("i", $order_id);
        $cancel_stmt->execute();
        
        // Make artworks available again
        $artwork_sql = "UPDATE artworks aw 
                       INNER JOIN order_items oi ON aw.artwork_id = oi.artwork_id 
                       SET aw.is_available = 1 
                       WHERE oi.order_id = ?";
        $artwork_stmt = $db->prepare($artwork_sql);
        $artwork_stmt->bind_param("i", $order_id);
        $artwork_stmt->execute();
        
        $db->commit();
        
        sendSuccessResponse([
            'order_id' => $order_id,
            'previous_status' => $order['status'],
            'new_status' => 'cancelled',
            'total_amount' => (float)$order['total_amount']
        ], "Order cancelled successfully");
        
    } catch (Exception $e) {
        $db->rollback();
        sendErrorResponse("Error cancelling order: " . $e->getMessage());
    }
}

/**
 * Bulk update orders
 */
function bulkUpdateOrders() {
    global $db;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['order_ids']) || !is_array($input['order_ids'])) {
            sendErrorResponse("Order IDs array is required", 400);
            return;
        }
        
        if (!isset($input['action'])) {
            sendErrorResponse("Bulk action is required", 400);
            return;
        }
        
        $order_ids = array_map('intval', $input['order_ids']);
        $action = $input['action'];
        
        if (empty($order_ids)) {
            sendErrorResponse("No order IDs provided", 400);
            return;
        }
        
        $placeholders = str_repeat('?,', count($order_ids) - 1) . '?';
        $updated_count = 0;
        
        $db->begin_transaction();
        
        try {
            switch ($action) {
                case 'mark_shipped':
                    $sql = "UPDATE orders SET status = 'shipped' WHERE id IN ($placeholders) AND status = 'paid'";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param(str_repeat('i', count($order_ids)), ...$order_ids);
                    $stmt->execute();
                    $updated_count = $stmt->affected_rows;
                    break;
                    
                case 'mark_delivered':
                    $sql = "UPDATE orders SET status = 'delivered' WHERE id IN ($placeholders) AND status IN ('paid', 'shipped')";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param(str_repeat('i', count($order_ids)), ...$order_ids);
                    $stmt->execute();
                    $updated_count = $stmt->affected_rows;
                    break;
                    
                case 'cancel':
                    $sql = "UPDATE orders SET status = 'cancelled' WHERE id IN ($placeholders) AND status NOT IN ('delivered', 'cancelled')";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param(str_repeat('i', count($order_ids)), ...$order_ids);
                    $stmt->execute();
                    $updated_count = $stmt->affected_rows;
                    
                    // Make artworks available again for cancelled orders
                    if ($updated_count > 0) {
                        $artwork_sql = "UPDATE artworks aw 
                                       INNER JOIN order_items oi ON aw.artwork_id = oi.artwork_id 
                                       INNER JOIN orders o ON oi.order_id = o.id
                                       SET aw.is_available = 1 
                                       WHERE o.id IN ($placeholders) AND o.status = 'cancelled'";
                        $artwork_stmt = $db->prepare($artwork_sql);
                        $artwork_stmt->bind_param(str_repeat('i', count($order_ids)), ...$order_ids);
                        $artwork_stmt->execute();
                    }
                    break;
                    
                default:
                    sendErrorResponse("Invalid bulk action. Available: mark_shipped, mark_delivered, cancel", 400);
                    return;
            }
            
            $db->commit();
            
            sendSuccessResponse([
                'updated_count' => $updated_count,
                'action' => $action,
                'order_ids' => $order_ids
            ], "Bulk update completed successfully");
            
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        sendErrorResponse("Error in bulk update: " . $e->getMessage());
    }
}

/**
 * Format order data for admin view
 */
function formatOrderAdminData($row) {
    return [
        'order_id' => (int)$row['order_id'],
        'total_amount' => (float)$row['total_amount'],
        'status' => $row['status'],
        'shipping_address' => $row['shipping_address'],
        'created_at' => $row['created_at'],
        'buyer' => [
            'buyer_id' => (int)$row['buyer_id'],
            'name' => $row['buyer_first_name'] . ' ' . $row['buyer_last_name'],
            'first_name' => $row['buyer_first_name'],
            'last_name' => $row['buyer_last_name'],
            'email' => $row['buyer_email'],
            'phone' => $row['buyer_phone'] ?? null,
            'profile_picture' => $row['buyer_profile_picture']
        ],
        'order_stats' => [
            'item_count' => isset($row['item_count']) ? (int)$row['item_count'] : 0,
            'artist_count' => isset($row['artist_count']) ? (int)$row['artist_count'] : 0
        ]
    ];
}

/**
 * Helper function to get time condition for queries
 */
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
            return "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    }
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