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
    $auction_id = $_GET['auction_id'] ?? '';
    
    // Route to appropriate function based on method and action
    switch ($method) {
        case 'GET':
            handleGetRequest($action, $auction_id);
            break;
        case 'POST':
            handlePostRequest($action);
            break;
        case 'PUT':
            handlePutRequest($action, $auction_id);
            break;
        case 'DELETE':
            handleDeleteRequest($action, $auction_id);
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
function handleGetRequest($action, $auction_id) {
    global $db;
    
    // If no action specified, default to list
    if (empty($action)) {
        $action = 'list';
    }
    
    switch ($action) {
        case 'list':
        case '': // Handle empty action as list
            getAllAuctionsAdmin();
            break;
        case 'details':
            if (!$auction_id) {
                sendErrorResponse("Auction ID is required", 400);
                return;
            }
            getAuctionDetails($auction_id);
            break;
        case 'bids':
            if (!$auction_id) {
                sendErrorResponse("Auction ID is required", 400);
                return;
            }
            getAuctionBids($auction_id);
            break;
        case 'stats':
            getAuctionStats();
            break;
        case 'available-artworks':
            getAvailableArtworks();
            break;
        case 'end-auction':
            if (!$auction_id) {
                sendErrorResponse("Auction ID is required", 400);
                return;
            }
            endAuctionManually($auction_id);
            break;
        case 'update-expired':
            updateExpiredAuctions();
            sendSuccessResponse(['message' => 'Expired auctions updated'], "Expired auctions processed successfully");
            break;
        case 'test':
            // Debug endpoint to see what parameters we're receiving
            sendSuccessResponse([
                'method' => $_SERVER['REQUEST_METHOD'],
                'get_params' => $_GET,
                'post_params' => $_POST,
                'action' => $action,
                'auction_id' => $auction_id,
                'query_string' => $_SERVER['QUERY_STRING'] ?? 'none'
            ], "Debug information");
            break;
        default:
            sendErrorResponse("Invalid action: '{$action}'. Available actions: list, details, bids, stats, available-artworks", 400);
    }
}

/**
 * Handle POST requests
 */
function handlePostRequest($action) {
    switch ($action) {
        case 'create':
            createAuction();
            break;
        case 'bulk-update':
            bulkUpdateAuctions();
            break;
        default:
            sendErrorResponse("Invalid action", 400);
    }
}

/**
 * Handle PUT requests
 */
function handlePutRequest($action, $auction_id) {
    if (!$auction_id) {
        sendErrorResponse("Auction ID is required", 400);
        return;
    }
    
    switch ($action) {
        case 'update':
            updateAuction($auction_id);
            break;
        case 'status':
            updateAuctionStatus($auction_id);
            break;
        case 'extend':
            extendAuction($auction_id);
            break;
        default:
            sendErrorResponse("Invalid action", 400);
    }
}

/**
 * Handle DELETE requests
 */
function handleDeleteRequest($action, $auction_id) {
    if (!$auction_id) {
        sendErrorResponse("Auction ID is required", 400);
        return;
    }
    
    switch ($action) {
        case 'cancel':
            cancelAuction($auction_id);
            break;
        case 'delete':
            deleteAuction($auction_id);
            break;
        default:
            sendErrorResponse("Invalid action", 400);
    }
}

/**
 * Get all auctions with admin details
 */
function getAllAuctionsAdmin() {
    global $db;
    
    try {
        // First, update expired auctions
        updateExpiredAuctions();
        
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $artist_id = $_GET['artist_id'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = min(100, max(10, (int)($_GET['limit'] ?? 20)));
        $offset = ($page - 1) * $limit;
        
        // Build WHERE conditions
        $conditions = ["u.is_active = 1"];
        $params = [];
        
        if (!empty($search)) {
            $conditions[] = "(aw.title LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
            $search_param = "%{$search}%";
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
        }
        
        if (!empty($status)) {
            $conditions[] = "a.status = ?";
            $params[] = $status;
        }
        
        if (!empty($artist_id)) {
            $conditions[] = "a.artist_id = ?";
            $params[] = $artist_id;
        }
        
        $where_clause = "WHERE " . implode(" AND ", $conditions);
        
        // Main query
        $sql = "SELECT 
                    a.id as auction_id,
                    a.starting_bid,
                    a.current_bid,
                    a.start_time,
                    a.end_time,
                    a.status as auction_status,
                    a.created_at as auction_created_at,
                    
                    -- Artwork details
                    aw.artwork_id,
                    aw.title as artwork_title,
                    aw.description as artwork_description,
                    aw.price as artwork_price,
                    aw.dimensions,
                    aw.year,
                    aw.material,
                    aw.artwork_image,
                    aw.type as artwork_type,
                    aw.is_available,
                    aw.on_auction,
                    
                    -- Artist details
                    u.user_id as artist_id,
                    u.first_name as artist_first_name,
                    u.last_name as artist_last_name,
                    u.email as artist_email,
                    u.profile_picture as artist_profile_picture,
                    u.art_specialty,
                    u.years_of_experience,
                    u.location as artist_location,
                    
                    -- Calculate time remaining
                    CASE 
                        WHEN a.end_time > NOW() AND a.status = 'active' THEN 
                            TIMESTAMPDIFF(SECOND, NOW(), a.end_time)
                        ELSE 0 
                    END as time_remaining_seconds,
                    
                    -- Bid statistics
                    (SELECT COUNT(*) FROM auction_bids ab WHERE ab.auction_id = a.id) as total_bids,
                    (SELECT COALESCE(MAX(ab.bid_amount), a.starting_bid) FROM auction_bids ab WHERE ab.auction_id = a.id) as highest_bid,
                    (SELECT COUNT(DISTINCT ab.user_id) FROM auction_bids ab WHERE ab.auction_id = a.id) as unique_bidders,
                    
                    -- Winner info (if auction ended)
                    winner.user_id as winner_id,
                    winner.first_name as winner_first_name,
                    winner.last_name as winner_last_name,
                    winner.email as winner_email
                    
                FROM auctions a
                INNER JOIN artworks aw ON a.product_id = aw.artwork_id
                INNER JOIN users u ON a.artist_id = u.user_id
                LEFT JOIN auction_bids wb ON a.id = wb.auction_id AND wb.is_winning_bid = 1
                LEFT JOIN users winner ON wb.user_id = winner.user_id
                $where_clause
                ORDER BY a.created_at DESC
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($params) - 2) . 'ii', ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $auctions = [];
        while ($row = $result->fetch_assoc()) {
            $auctions[] = formatAuctionAdminData($row);
        }
        
        // Get total count for pagination
        $count_sql = "SELECT COUNT(*) as total 
                      FROM auctions a
                      INNER JOIN artworks aw ON a.product_id = aw.artwork_id
                      INNER JOIN users u ON a.artist_id = u.user_id
                      $where_clause";
        
        $count_params = array_slice($params, 0, -2); // Remove limit and offset
        $count_stmt = $db->prepare($count_sql);
        if (!empty($count_params)) {
            $count_stmt->bind_param(str_repeat('s', count($count_params)), ...$count_params);
        }
        $count_stmt->execute();
        $total_count = $count_stmt->get_result()->fetch_assoc()['total'];
        
        sendSuccessResponse([
            'auctions' => $auctions,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total_count' => (int)$total_count,
                'total_pages' => ceil($total_count / $limit)
            ]
        ], "Auctions retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching auctions: " . $e->getMessage());
    }
}

/**
 * Get detailed auction information
 */
function getAuctionDetails($auction_id) {
    global $db;
    
    try {
        $sql = "SELECT 
                    a.id as auction_id,
                    a.starting_bid,
                    a.current_bid,
                    a.start_time,
                    a.end_time,
                    a.status as auction_status,
                    a.created_at as auction_created_at,
                    
                    -- Artwork details
                    aw.artwork_id,
                    aw.title as artwork_title,
                    aw.description as artwork_description,
                    aw.price as artwork_price,
                    aw.dimensions,
                    aw.year,
                    aw.material,
                    aw.artwork_image,
                    aw.type as artwork_type,
                    aw.is_available,
                    aw.on_auction,
                    
                    -- Artist details
                    u.user_id as artist_id,
                    u.first_name as artist_first_name,
                    u.last_name as artist_last_name,
                    u.email as artist_email,
                    u.profile_picture as artist_profile_picture,
                    u.art_specialty,
                    u.years_of_experience,
                    u.location as artist_location,
                    
                    -- Calculate time remaining
                    CASE 
                        WHEN a.end_time > NOW() THEN 
                            TIMESTAMPDIFF(SECOND, NOW(), a.end_time)
                        ELSE 0 
                    END as time_remaining_seconds,
                    
                    -- Winner info (if auction ended)
                    winner.user_id as winner_id,
                    winner.first_name as winner_first_name,
                    winner.last_name as winner_last_name,
                    winner.email as winner_email
                    
                FROM auctions a
                INNER JOIN artworks aw ON a.product_id = aw.artwork_id
                INNER JOIN users u ON a.artist_id = u.user_id
                LEFT JOIN auction_bids wb ON a.id = wb.auction_id AND wb.is_winning_bid = 1
                LEFT JOIN users winner ON wb.user_id = winner.user_id
                WHERE a.id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $auction_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $auction_data = formatAuctionAdminData($row);
            
            // Get bid history
            $bid_sql = "SELECT 
                            ab.id as bid_id,
                            ab.bid_amount,
                            ab.bid_time,
                            ab.is_winning_bid,
                            u.user_id,
                            u.first_name,
                            u.last_name,
                            u.email
                        FROM auction_bids ab
                        INNER JOIN users u ON ab.user_id = u.user_id
                        WHERE ab.auction_id = ?
                        ORDER BY ab.bid_time DESC";
            
            $bid_stmt = $db->prepare($bid_sql);
            $bid_stmt->bind_param("i", $auction_id);
            $bid_stmt->execute();
            $bid_result = $bid_stmt->get_result();
            
            $bids = [];
            while ($bid_row = $bid_result->fetch_assoc()) {
                $bids[] = [
                    'bid_id' => (int)$bid_row['bid_id'],
                    'amount' => (float)$bid_row['bid_amount'],
                    'time' => $bid_row['bid_time'],
                    'is_winning' => (bool)$bid_row['is_winning_bid'],
                    'bidder' => [
                        'user_id' => (int)$bid_row['user_id'],
                        'name' => $bid_row['first_name'] . ' ' . $bid_row['last_name'],
                        'email' => $bid_row['email']
                    ]
                ];
            }
            
            $auction_data['bid_history'] = $bids;
            $auction_data['total_bids'] = count($bids);
            $auction_data['unique_bidders'] = count(array_unique(array_column($bids, 'bidder')));
            
            sendSuccessResponse($auction_data, "Auction details retrieved successfully");
        } else {
            sendErrorResponse("Auction not found", 404);
        }
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching auction details: " . $e->getMessage());
    }
}

/**
 * Create new auction
 */
function createAuction() {
    global $db;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required_fields = ['artwork_id', 'starting_bid', 'start_time', 'end_time'];
        foreach ($required_fields as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                sendErrorResponse("Missing required field: $field", 400);
                return;
            }
        }
        
        // Validate artwork exists and is available
        $artwork_sql = "SELECT artwork_id, artist_id, title, is_available, on_auction 
                        FROM artworks 
                        WHERE artwork_id = ? AND is_available = 1";
        $artwork_stmt = $db->prepare($artwork_sql);
        $artwork_stmt->bind_param("i", $input['artwork_id']);
        $artwork_stmt->execute();
        $artwork_result = $artwork_stmt->get_result();
        
        if (!$artwork = $artwork_result->fetch_assoc()) {
            sendErrorResponse("Artwork not found or not available", 404);
            return;
        }
        
        if ($artwork['on_auction']) {
            sendErrorResponse("Artwork is already on auction", 400);
            return;
        }
        
        // Validate dates
        $start_time = new DateTime($input['start_time']);
        $end_time = new DateTime($input['end_time']);
        $now = new DateTime();
        
        if ($end_time <= $start_time) {
            sendErrorResponse("End time must be after start time", 400);
            return;
        }
        
        if ($start_time < $now && $start_time->diff($now)->i > 5) { // Allow 5 minutes tolerance
            sendErrorResponse("Start time cannot be in the past", 400);
            return;
        }
        
        // Validate starting bid
        if ($input['starting_bid'] <= 0) {
            sendErrorResponse("Starting bid must be greater than 0", 400);
            return;
        }
        
        $db->begin_transaction();
        
        try {
            // Create auction
            $auction_sql = "INSERT INTO auctions (product_id, artist_id, starting_bid, start_time, end_time, status) 
                           VALUES (?, ?, ?, ?, ?, 'active')";
            $auction_stmt = $db->prepare($auction_sql);
            $auction_stmt->bind_param("iidss", 
                $input['artwork_id'],
                $artwork['artist_id'],
                $input['starting_bid'],
                $input['start_time'],
                $input['end_time']
            );
            $auction_stmt->execute();
            
            $auction_id = $db->insert_id;
            
            // Update artwork status
            $update_artwork_sql = "UPDATE artworks SET on_auction = 1 WHERE artwork_id = ?";
            $update_stmt = $db->prepare($update_artwork_sql);
            $update_stmt->bind_param("i", $input['artwork_id']);
            $update_stmt->execute();
            
            $db->commit();
            
            sendSuccessResponse([
                'auction_id' => $auction_id,
                'artwork_title' => $artwork['title']
            ], "Auction created successfully");
            
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        sendErrorResponse("Error creating auction: " . $e->getMessage());
    }
}

/**
 * Update auction details
 */
function updateAuction($auction_id) {
    global $db;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Check if auction exists and is modifiable
        $check_sql = "SELECT id, status, start_time, end_time, current_bid 
                      FROM auctions 
                      WHERE id = ?";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("i", $auction_id);
        $check_stmt->execute();
        $auction = $check_stmt->get_result()->fetch_assoc();
        
        if (!$auction) {
            sendErrorResponse("Auction not found", 404);
            return;
        }
        
        // Prevent updates to ended or cancelled auctions
        if ($auction['status'] !== 'active') {
            sendErrorResponse("Cannot update ended or cancelled auction", 400);
            return;
        }
        
        // Prevent updates if there are bids (except for extending time)
        if ($auction['current_bid'] > 0 && isset($input['starting_bid'])) {
            sendErrorResponse("Cannot update starting bid after bids have been placed", 400);
            return;
        }
        
        $update_fields = [];
        $params = [];
        
        // Update allowed fields
        if (isset($input['end_time'])) {
            $new_end_time = new DateTime($input['end_time']);
            $current_end_time = new DateTime($auction['end_time']);
            
            if ($new_end_time <= $current_end_time) {
                sendErrorResponse("New end time must be after current end time", 400);
                return;
            }
            
            $update_fields[] = "end_time = ?";
            $params[] = $input['end_time'];
        }
        
        if (isset($input['starting_bid']) && $auction['current_bid'] == 0) {
            if ($input['starting_bid'] <= 0) {
                sendErrorResponse("Starting bid must be greater than 0", 400);
                return;
            }
            $update_fields[] = "starting_bid = ?";
            $params[] = $input['starting_bid'];
        }
        
        if (empty($update_fields)) {
            sendErrorResponse("No valid fields to update", 400);
            return;
        }
        
        $params[] = $auction_id;
        
        $update_sql = "UPDATE auctions SET " . implode(", ", $update_fields) . " WHERE id = ?";
        $stmt = $db->prepare($update_sql);
        $types = str_repeat('s', count($params) - 1) . 'i';
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        sendSuccessResponse(['auction_id' => $auction_id], "Auction updated successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error updating auction: " . $e->getMessage());
    }
}

/**
 * Update auction status
 */
function updateAuctionStatus($auction_id) {
    global $db;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['status'])) {
            sendErrorResponse("Status is required", 400);
            return;
        }
        
        $allowed_statuses = ['active', 'ended', 'cancelled'];
        if (!in_array($input['status'], $allowed_statuses)) {
            sendErrorResponse("Invalid status", 400);
            return;
        }
        
        $db->begin_transaction();
        
        try {
            // Get auction details
            $auction_sql = "SELECT a.id, a.product_id, a.status, a.current_bid, a.end_time,
                                  ab.user_id as winner_id, ab.bid_amount as winning_bid
                           FROM auctions a
                           LEFT JOIN auction_bids ab ON a.id = ab.auction_id AND ab.is_winning_bid = 1
                           WHERE a.id = ?";
            $auction_stmt = $db->prepare($auction_sql);
            $auction_stmt->bind_param("i", $auction_id);
            $auction_stmt->execute();
            $auction = $auction_stmt->get_result()->fetch_assoc();
            
            if (!$auction) {
                sendErrorResponse("Auction not found", 404);
                return;
            }
            
            // Update auction status
            $update_sql = "UPDATE auctions SET status = ? WHERE id = ?";
            $update_stmt = $db->prepare($update_sql);
            $update_stmt->bind_param("si", $input['status'], $auction_id);
            $update_stmt->execute();
            
            // Handle artwork status based on auction status
            if ($input['status'] === 'ended') {
                if ($auction['winner_id']) {
                    // Mark artwork as sold
                    $artwork_sql = "UPDATE artworks SET is_available = 0, on_auction = 0 WHERE artwork_id = ?";
                } else {
                    // No winner, make available again
                    $artwork_sql = "UPDATE artworks SET on_auction = 0 WHERE artwork_id = ?";
                }
            } elseif ($input['status'] === 'cancelled') {
                // Make artwork available again
                $artwork_sql = "UPDATE artworks SET on_auction = 0 WHERE artwork_id = ?";
            }
            
            if (isset($artwork_sql)) {
                $artwork_stmt = $db->prepare($artwork_sql);
                $artwork_stmt->bind_param("i", $auction['product_id']);
                $artwork_stmt->execute();
            }
            
            $db->commit();
            
            sendSuccessResponse(['auction_id' => $auction_id], "Auction status updated successfully");
            
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        sendErrorResponse("Error updating auction status: " . $e->getMessage());
    }
}

/**
 * Get auction statistics
 */
function getAuctionStats() {
    global $db;
    
    try {
        $time_period = $_GET['period'] ?? 'month';
        $time_condition = getTimeCondition($time_period);
        
        // Total auctions
        $total_sql = "SELECT COUNT(*) as total FROM auctions $time_condition";
        $total_result = $db->query($total_sql);
        $total_auctions = $total_result->fetch_assoc()['total'];
        
        // Active auctions
        $active_sql = "SELECT COUNT(*) as active FROM auctions WHERE status = 'active' AND end_time > NOW()";
        $active_result = $db->query($active_sql);
        $active_auctions = $active_result->fetch_assoc()['active'];
        
        // Completed auctions
        $completed_sql = "SELECT COUNT(*) as completed FROM auctions WHERE status = 'ended' $time_condition";
        $completed_result = $db->query($completed_sql);
        $completed_auctions = $completed_result->fetch_assoc()['completed'];
        
        // Total revenue from auctions
        $revenue_sql = "SELECT COALESCE(SUM(ab.bid_amount), 0) as revenue 
                       FROM auction_bids ab 
                       INNER JOIN auctions a ON ab.auction_id = a.id 
                       WHERE ab.is_winning_bid = 1 AND a.status = 'ended' $time_condition";
        $revenue_result = $db->query($revenue_sql);
        $total_revenue = $revenue_result->fetch_assoc()['revenue'];
        
        // Average bid per auction
        $avg_bid_sql = "SELECT AVG(ab.bid_amount) as avg_bid 
                        FROM auction_bids ab 
                        INNER JOIN auctions a ON ab.auction_id = a.id 
                        WHERE ab.is_winning_bid = 1 $time_condition";
        $avg_bid_result = $db->query($avg_bid_sql);
        $avg_bid = $avg_bid_result->fetch_assoc()['avg_bid'] ?: 0;
        
        // Most popular categories
        $categories_sql = "SELECT aw.type, COUNT(*) as count 
                          FROM auctions a 
                          INNER JOIN artworks aw ON a.product_id = aw.artwork_id 
                          $time_condition 
                          GROUP BY aw.type 
                          ORDER BY count DESC 
                          LIMIT 5";
        $categories_result = $db->query($categories_sql);
        $categories = [];
        while ($row = $categories_result->fetch_assoc()) {
            $categories[] = [
                'category' => ucfirst(str_replace('_', ' ', $row['type'])),
                'count' => (int)$row['count']
            ];
        }
        
        // Top artists by auction count
        $artists_sql = "SELECT u.user_id, u.first_name, u.last_name, COUNT(*) as auction_count,
                              COALESCE(SUM(CASE WHEN ab.is_winning_bid = 1 THEN ab.bid_amount ELSE 0 END), 0) as total_revenue
                       FROM auctions a 
                       INNER JOIN users u ON a.artist_id = u.user_id 
                       LEFT JOIN auction_bids ab ON a.id = ab.auction_id AND ab.is_winning_bid = 1
                       $time_condition 
                       GROUP BY u.user_id 
                       ORDER BY auction_count DESC 
                       LIMIT 5";
        $artists_result = $db->query($artists_sql);
        $top_artists = [];
        while ($row = $artists_result->fetch_assoc()) {
            $top_artists[] = [
                'artist_id' => (int)$row['user_id'],
                'name' => $row['first_name'] . ' ' . $row['last_name'],
                'auction_count' => (int)$row['auction_count'],
                'total_revenue' => (float)$row['total_revenue']
            ];
        }
        
        sendSuccessResponse([
            'overview' => [
                'total_auctions' => (int)$total_auctions,
                'active_auctions' => (int)$active_auctions,
                'completed_auctions' => (int)$completed_auctions,
                'total_revenue' => (float)$total_revenue,
                'average_winning_bid' => (float)$avg_bid
            ],
            'popular_categories' => $categories,
            'top_artists' => $top_artists,
            'period' => $time_period
        ], "Auction statistics retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching auction statistics: " . $e->getMessage());
    }
}

/**
 * Get available artworks for auction
 */
function getAvailableArtworks() {
    global $db;
    
    try {
        $search = $_GET['search'] ?? '';
        $artist_id = $_GET['artist_id'] ?? '';
        $category = $_GET['category'] ?? '';
        
        $conditions = ["aw.is_available = 1", "aw.on_auction = 0"];
        $params = [];
        
        if (!empty($search)) {
            $conditions[] = "aw.title LIKE ?";
            $params[] = "%{$search}%";
        }
        
        if (!empty($artist_id)) {
            $conditions[] = "aw.artist_id = ?";
            $params[] = $artist_id;
        }
        
        if (!empty($category)) {
            $conditions[] = "aw.type = ?";
            $params[] = $category;
        }
        
        $where_clause = "WHERE " . implode(" AND ", $conditions);
        
        $sql = "SELECT 
                    aw.artwork_id,
                    aw.title,
                    aw.description,
                    aw.price,
                    aw.dimensions,
                    aw.year,
                    aw.material,
                    aw.artwork_image,
                    aw.type,
                    u.user_id as artist_id,
                    u.first_name as artist_first_name,
                    u.last_name as artist_last_name,
                    u.profile_picture as artist_profile_picture
                FROM artworks aw
                INNER JOIN users u ON aw.artist_id = u.user_id
                $where_clause
                ORDER BY aw.created_at DESC
                LIMIT 50";
        
        if (!empty($params)) {
            $stmt = $db->prepare($sql);
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $db->query($sql);
        }
        
        $artworks = [];
        while ($row = $result->fetch_assoc()) {
            $artworks[] = [
                'artwork_id' => (int)$row['artwork_id'],
                'title' => $row['title'],
                'description' => $row['description'],
                'price' => (float)$row['price'],
                'dimensions' => $row['dimensions'],
                'year' => $row['year'],
                'material' => $row['material'],
                'image' => $row['artwork_image'],
                'type' => $row['type'],
                'artist' => [
                    'artist_id' => (int)$row['artist_id'],
                    'name' => $row['artist_first_name'] . ' ' . $row['artist_last_name'],
                    'profile_picture' => $row['artist_profile_picture']
                ]
            ];
        }
        
        sendSuccessResponse($artworks, "Available artworks retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching available artworks: " . $e->getMessage());
    }
}

/**
 * Get auction bids
 */
function getAuctionBids($auction_id) {
    global $db;
    
    try {
        // Check if auction exists
        $check_sql = "SELECT id FROM auctions WHERE id = ?";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("i", $auction_id);
        $check_stmt->execute();
        
        if (!$check_stmt->get_result()->fetch_assoc()) {
            sendErrorResponse("Auction not found", 404);
            return;
        }
        
        // Get all bids for the auction
        $sql = "SELECT 
                    ab.id as bid_id,
                    ab.bid_amount,
                    ab.bid_time,
                    ab.is_winning_bid,
                    u.user_id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.profile_picture
                FROM auction_bids ab
                INNER JOIN users u ON ab.user_id = u.user_id
                WHERE ab.auction_id = ?
                ORDER BY ab.bid_time DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $auction_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $bids = [];
        while ($row = $result->fetch_assoc()) {
            $bids[] = [
                'bid_id' => (int)$row['bid_id'],
                'amount' => (float)$row['bid_amount'],
                'time' => $row['bid_time'],
                'is_winning' => (bool)$row['is_winning_bid'],
                'bidder' => [
                    'user_id' => (int)$row['user_id'],
                    'name' => $row['first_name'] . ' ' . $row['last_name'],
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'email' => $row['email'],
                    'profile_picture' => $row['profile_picture']
                ]
            ];
        }
        
        sendSuccessResponse([
            'auction_id' => (int)$auction_id,
            'bids' => $bids,
            'total_bids' => count($bids),
            'unique_bidders' => count(array_unique(array_column(array_column($bids, 'bidder'), 'user_id')))
        ], "Auction bids retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching auction bids: " . $e->getMessage());
    }
}

/**
 * Delete auction (only if no bids placed)
 */
function deleteAuction($auction_id) {
    global $db;
    
    try {
        $db->begin_transaction();
        
        // Check if auction exists and has no bids
        $check_sql = "SELECT a.id, a.product_id, a.status, 
                            (SELECT COUNT(*) FROM auction_bids WHERE auction_id = a.id) as bid_count
                     FROM auctions a 
                     WHERE a.id = ?";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("i", $auction_id);
        $check_stmt->execute();
        $auction = $check_stmt->get_result()->fetch_assoc();
        
        if (!$auction) {
            sendErrorResponse("Auction not found", 404);
            return;
        }
        
        if ($auction['bid_count'] > 0) {
            sendErrorResponse("Cannot delete auction with existing bids. Cancel instead.", 400);
            return;
        }
        
        // Delete auction
        $delete_sql = "DELETE FROM auctions WHERE id = ?";
        $delete_stmt = $db->prepare($delete_sql);
        $delete_stmt->bind_param("i", $auction_id);
        $delete_stmt->execute();
        
        // Make artwork available again
        $artwork_sql = "UPDATE artworks SET on_auction = 0 WHERE artwork_id = ?";
        $artwork_stmt = $db->prepare($artwork_sql);
        $artwork_stmt->bind_param("i", $auction['product_id']);
        $artwork_stmt->execute();
        
        $db->commit();
        
        sendSuccessResponse(['auction_id' => $auction_id], "Auction deleted successfully");
        
    } catch (Exception $e) {
        $db->rollback();
        sendErrorResponse("Error deleting auction: " . $e->getMessage());
    }
}

/**
 * Extend auction end time
 */
function extendAuction($auction_id) {
    global $db;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['extend_hours']) || !is_numeric($input['extend_hours'])) {
            sendErrorResponse("Extend hours is required and must be numeric", 400);
            return;
        }
        
        $extend_hours = (int)$input['extend_hours'];
        if ($extend_hours <= 0 || $extend_hours > 168) { // Max 1 week extension
            sendErrorResponse("Extend hours must be between 1 and 168 (1 week)", 400);
            return;
        }
        
        // Check if auction exists and is active
        $check_sql = "SELECT id, end_time, status FROM auctions WHERE id = ?";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("i", $auction_id);
        $check_stmt->execute();
        $auction = $check_stmt->get_result()->fetch_assoc();
        
        if (!$auction) {
            sendErrorResponse("Auction not found", 404);
            return;
        }
        
        if ($auction['status'] !== 'active') {
            sendErrorResponse("Only active auctions can be extended", 400);
            return;
        }
        
        // Extend the auction
        $extend_sql = "UPDATE auctions SET end_time = DATE_ADD(end_time, INTERVAL ? HOUR) WHERE id = ?";
        $extend_stmt = $db->prepare($extend_sql);
        $extend_stmt->bind_param("ii", $extend_hours, $auction_id);
        $extend_stmt->execute();
        
        // Get new end time
        $new_time_sql = "SELECT end_time FROM auctions WHERE id = ?";
        $time_stmt = $db->prepare($new_time_sql);
        $time_stmt->bind_param("i", $auction_id);
        $time_stmt->execute();
        $new_end_time = $time_stmt->get_result()->fetch_assoc()['end_time'];
        
        sendSuccessResponse([
            'auction_id' => $auction_id,
            'new_end_time' => $new_end_time,
            'extended_hours' => $extend_hours
        ], "Auction extended successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error extending auction: " . $e->getMessage());
    }
}

/**
 * Bulk update auctions
 */
function bulkUpdateAuctions() {
    global $db;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['auction_ids']) || !is_array($input['auction_ids'])) {
            sendErrorResponse("Auction IDs array is required", 400);
            return;
        }
        
        if (!isset($input['action'])) {
            sendErrorResponse("Bulk action is required", 400);
            return;
        }
        
        $auction_ids = array_map('intval', $input['auction_ids']);
        $action = $input['action'];
        
        if (empty($auction_ids)) {
            sendErrorResponse("No auction IDs provided", 400);
            return;
        }
        
        $db->begin_transaction();
        
        try {
            $placeholders = str_repeat('?,', count($auction_ids) - 1) . '?';
            $updated_count = 0;
            
            switch ($action) {
                case 'cancel':
                    // Cancel multiple auctions
                    $sql = "UPDATE auctions SET status = 'cancelled' WHERE id IN ($placeholders) AND status = 'active'";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param(str_repeat('i', count($auction_ids)), ...$auction_ids);
                    $stmt->execute();
                    $updated_count = $stmt->affected_rows;
                    
                    // Update artworks
                    $artwork_sql = "UPDATE artworks a 
                                   INNER JOIN auctions au ON a.artwork_id = au.product_id 
                                   SET a.on_auction = 0 
                                   WHERE au.id IN ($placeholders) AND au.status = 'cancelled'";
                    $artwork_stmt = $db->prepare($artwork_sql);
                    $artwork_stmt->bind_param(str_repeat('i', count($auction_ids)), ...$auction_ids);
                    $artwork_stmt->execute();
                    break;
                    
                case 'end':
                    // End multiple auctions
                    $sql = "UPDATE auctions SET status = 'ended' WHERE id IN ($placeholders) AND status = 'active'";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param(str_repeat('i', count($auction_ids)), ...$auction_ids);
                    $stmt->execute();
                    $updated_count = $stmt->affected_rows;
                    break;
                    
                default:
                    sendErrorResponse("Invalid bulk action. Available: cancel, end", 400);
                    return;
            }
            
            $db->commit();
            
            sendSuccessResponse([
                'updated_count' => $updated_count,
                'action' => $action,
                'auction_ids' => $auction_ids
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
 * Update expired auctions automatically
 */
function updateExpiredAuctions() {
    global $db;
    
    try {
        // Update auctions that have passed their end time
        $update_sql = "UPDATE auctions 
                      SET status = 'ended' 
                      WHERE status = 'active' 
                      AND end_time <= NOW()";
        $db->query($update_sql);
        
        // Update winning bids for ended auctions
        $winning_bid_sql = "UPDATE auction_bids ab1 
                           INNER JOIN auctions a ON ab1.auction_id = a.id
                           SET ab1.is_winning_bid = 1 
                           WHERE a.status = 'ended' 
                           AND ab1.bid_amount = (
                               SELECT MAX(ab2.bid_amount) 
                               FROM auction_bids ab2 
                               WHERE ab2.auction_id = ab1.auction_id
                           )
                           AND ab1.is_winning_bid = 0";
        $db->query($winning_bid_sql);
        
        // Update artwork availability for ended auctions with winners
        $artwork_sold_sql = "UPDATE artworks aw
                            INNER JOIN auctions a ON aw.artwork_id = a.product_id
                            INNER JOIN auction_bids ab ON a.id = ab.auction_id AND ab.is_winning_bid = 1
                            SET aw.is_available = 0, aw.on_auction = 0
                            WHERE a.status = 'ended'";
        $db->query($artwork_sold_sql);
        
        // Update artwork availability for ended auctions without winners
        $artwork_unsold_sql = "UPDATE artworks aw
                              INNER JOIN auctions a ON aw.artwork_id = a.product_id
                              LEFT JOIN auction_bids ab ON a.id = ab.auction_id AND ab.is_winning_bid = 1
                              SET aw.on_auction = 0
                              WHERE a.status = 'ended' AND ab.auction_id IS NULL";
        $db->query($artwork_unsold_sql);
        
    } catch (Exception $e) {
        // Log error but don't stop execution
        error_log("Error updating expired auctions: " . $e->getMessage());
    }
}

/**
 * Manually end an auction and determine winner
 */
function endAuctionManually($auction_id) {
    global $db;
    
    try {
        $db->begin_transaction();
        
        // Check if auction exists and is active
        $check_sql = "SELECT a.id, a.product_id, a.status, a.current_bid,
                            (SELECT MAX(ab.bid_amount) FROM auction_bids ab WHERE ab.auction_id = a.id) as highest_bid,
                            (SELECT ab.user_id FROM auction_bids ab WHERE ab.auction_id = a.id ORDER BY ab.bid_amount DESC, ab.bid_time ASC LIMIT 1) as winner_id
                     FROM auctions a 
                     WHERE a.id = ?";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("i", $auction_id);
        $check_stmt->execute();
        $auction = $check_stmt->get_result()->fetch_assoc();
        
        if (!$auction) {
            sendErrorResponse("Auction not found", 404);
            return;
        }
        
        if ($auction['status'] !== 'active') {
            sendErrorResponse("Only active auctions can be ended", 400);
            return;
        }
        
        // End the auction
        $end_sql = "UPDATE auctions SET status = 'ended', current_bid = COALESCE(?, current_bid) WHERE id = ?";
        $end_stmt = $db->prepare($end_sql);
        $end_stmt->bind_param("di", $auction['highest_bid'], $auction_id);
        $end_stmt->execute();
        
        $winner_info = null;
        
        if ($auction['winner_id']) {
            // Mark winning bid
            $winning_bid_sql = "UPDATE auction_bids 
                               SET is_winning_bid = 1 
                               WHERE auction_id = ? 
                               AND user_id = ? 
                               AND bid_amount = ?";
            $winning_stmt = $db->prepare($winning_bid_sql);
            $winning_stmt->bind_param("iid", $auction_id, $auction['winner_id'], $auction['highest_bid']);
            $winning_stmt->execute();
            
            // Mark artwork as sold
            $artwork_sql = "UPDATE artworks SET is_available = 0, on_auction = 0 WHERE artwork_id = ?";
            $artwork_stmt = $db->prepare($artwork_sql);
            $artwork_stmt->bind_param("i", $auction['product_id']);
            $artwork_stmt->execute();
            
            // Get winner info
            $winner_sql = "SELECT user_id, first_name, last_name, email FROM users WHERE user_id = ?";
            $winner_stmt = $db->prepare($winner_sql);
            $winner_stmt->bind_param("i", $auction['winner_id']);
            $winner_stmt->execute();
            $winner_info = $winner_stmt->get_result()->fetch_assoc();
        } else {
            // No bids, make artwork available again
            $artwork_sql = "UPDATE artworks SET on_auction = 0 WHERE artwork_id = ?";
            $artwork_stmt = $db->prepare($artwork_sql);
            $artwork_stmt->bind_param("i", $auction['product_id']);
            $artwork_stmt->execute();
        }
        
        $db->commit();
        
        $response_data = [
            'auction_id' => $auction_id,
            'final_bid' => $auction['highest_bid'] ?: $auction['current_bid'],
            'has_winner' => !is_null($auction['winner_id'])
        ];
        
        if ($winner_info) {
            $response_data['winner'] = [
                'user_id' => (int)$winner_info['user_id'],
                'name' => $winner_info['first_name'] . ' ' . $winner_info['last_name'],
                'email' => $winner_info['email']
            ];
        }
        
        sendSuccessResponse($response_data, "Auction ended successfully");
        
    } catch (Exception $e) {
        $db->rollback();
        sendErrorResponse("Error ending auction: " . $e->getMessage());
    }
}

/**
 * Cancel auction
 */
function cancelAuction($auction_id) {
    global $db;
    
    try {
        $db->begin_transaction();
        
        // Get auction details
        $auction_sql = "SELECT id, product_id, status FROM auctions WHERE id = ?";
        $auction_stmt = $db->prepare($auction_sql);
        $auction_stmt->bind_param("i", $auction_id);
        $auction_stmt->execute();
        $auction = $auction_stmt->get_result()->fetch_assoc();
        
        if (!$auction) {
            sendErrorResponse("Auction not found", 404);
            return;
        }
        
        if ($auction['status'] !== 'active') {
            sendErrorResponse("Only active auctions can be cancelled", 400);
            return;
        }
        
        // Cancel auction
        $cancel_sql = "UPDATE auctions SET status = 'cancelled' WHERE id = ?";
        $cancel_stmt = $db->prepare($cancel_sql);
        $cancel_stmt->bind_param("i", $auction_id);
        $cancel_stmt->execute();
        
        // Make artwork available again
        $artwork_sql = "UPDATE artworks SET on_auction = 0 WHERE artwork_id = ?";
        $artwork_stmt = $db->prepare($artwork_sql);
        $artwork_stmt->bind_param("i", $auction['product_id']);
        $artwork_stmt->execute();
        
        $db->commit();
        
        sendSuccessResponse(['auction_id' => $auction_id], "Auction cancelled successfully");
        
    } catch (Exception $e) {
        $db->rollback();
        sendErrorResponse("Error cancelling auction: " . $e->getMessage());
    }
}

/**
 * Format auction data for admin view
 */
function formatAuctionAdminData($row) {
    return [
        'auction_id' => (int)$row['auction_id'],
        'starting_bid' => (float)$row['starting_bid'],
        'current_bid' => (float)$row['current_bid'],
        'start_time' => $row['start_time'],
        'end_time' => $row['end_time'],
        'status' => $row['auction_status'],
        'time_remaining_seconds' => (int)$row['time_remaining_seconds'],
        'is_active' => $row['auction_status'] === 'active' && $row['time_remaining_seconds'] > 0,
        'total_bids' => isset($row['total_bids']) ? (int)$row['total_bids'] : 0,
        'highest_bid' => isset($row['highest_bid']) ? (float)$row['highest_bid'] : (float)$row['starting_bid'],
        'unique_bidders' => isset($row['unique_bidders']) ? (int)$row['unique_bidders'] : 0,
        'artwork' => [
            'artwork_id' => (int)$row['artwork_id'],
            'title' => $row['artwork_title'],
            'description' => $row['artwork_description'],
            'original_price' => (float)$row['artwork_price'],
            'dimensions' => $row['dimensions'],
            'year' => $row['year'],
            'material' => $row['material'],
            'image' => $row['artwork_image'],
            'type' => $row['artwork_type'],
            'is_available' => (bool)$row['is_available'],
            'on_auction' => (bool)$row['on_auction']
        ],
        'artist' => [
            'artist_id' => (int)$row['artist_id'],
            'name' => $row['artist_first_name'] . ' ' . $row['artist_last_name'],
            'first_name' => $row['artist_first_name'],
            'last_name' => $row['artist_last_name'],
            'email' => $row['artist_email'] ?? null,
            'profile_picture' => $row['artist_profile_picture'],
            'specialty' => $row['art_specialty'],
            'years_of_experience' => $row['years_of_experience'] ? (int)$row['years_of_experience'] : null,
            'location' => $row['artist_location']
        ],
        'winner' => $row['winner_id'] ? [
            'user_id' => (int)$row['winner_id'],
            'name' => $row['winner_first_name'] . ' ' . $row['winner_last_name'],
            'email' => $row['winner_email']
        ] : null,
        'created_at' => $row['auction_created_at']
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