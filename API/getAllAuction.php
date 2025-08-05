<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once 'db.php';

function getAuctionQuery() {
    return "SELECT 
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
                
                -- Artist details
                u.user_id as artist_id,
                u.first_name as artist_first_name,
                u.last_name as artist_last_name,
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
                
                -- Bid count
                (SELECT COUNT(*) FROM auction_bids ab WHERE ab.auction_id = a.id) as total_bids
                
            FROM auctions a
            INNER JOIN artworks aw ON a.product_id = aw.artwork_id
            INNER JOIN users u ON a.artist_id = u.user_id
            WHERE u.is_active = 1 AND aw.is_available = 1
            ORDER BY 
                CASE 
                    WHEN a.status = 'active' AND a.end_time > NOW() THEN 1
                    WHEN a.status = 'active' AND a.end_time <= NOW() THEN 2
                    ELSE 3
                END,
                a.end_time ASC";
}

function formatAuctionData($row) {
    return array(
        'auction_id' => (int)$row['auction_id'],
        'starting_bid' => (float)$row['starting_bid'],
        'current_bid' => (float)$row['current_bid'],
        'start_time' => $row['start_time'],
        'end_time' => $row['end_time'],
        'status' => $row['auction_status'],
        'time_remaining_seconds' => (int)$row['time_remaining_seconds'],
        'total_bids' => (int)$row['total_bids'],
        'is_active' => $row['auction_status'] === 'active' && $row['time_remaining_seconds'] > 0,
        'artwork' => array(
            'artwork_id' => (int)$row['artwork_id'],
            'title' => $row['artwork_title'],
            'description' => $row['artwork_description'],
            'original_price' => (float)$row['artwork_price'],
            'dimensions' => $row['dimensions'],
            'year' => $row['year'],
            'material' => $row['material'],
            'image' => $row['artwork_image'],
            'type' => $row['artwork_type']
        ),
        'artist' => array(
            'artist_id' => (int)$row['artist_id'],
            'name' => $row['artist_first_name'] . ' ' . $row['artist_last_name'],
            'first_name' => $row['artist_first_name'],
            'last_name' => $row['artist_last_name'],
            'profile_picture' => $row['artist_profile_picture'],
            'specialty' => $row['art_specialty'],
            'years_of_experience' => $row['years_of_experience'] ? (int)$row['years_of_experience'] : null,
            'location' => $row['artist_location']
        ),
        'auction_created_at' => $row['auction_created_at']
    );
}


function getAllAuctions($db) {
    try {
        $sql = getAuctionQuery();
        $result = $db->query($sql);
        
        if (!$result) {
            throw new Exception("Database query failed: " . $db->error);
        }
        
        $auctions = array();
        
        while ($row = $result->fetch_assoc()) {
            $auctions[] = formatAuctionData($row);
        }
        
        return $auctions;
        
    } catch (Exception $e) {
        throw new Exception("Error fetching auctions: " . $e->getMessage());
    }
}


function sendSuccessResponse($auctions) {
    $response = array(
        'success' => true,
        'message' => 'Auctions retrieved successfully',
        'data' => $auctions,
        'total_count' => count($auctions),
        'timestamp' => date('Y-m-d H:i:s')
    );
    
    echo json_encode($response, JSON_PRETTY_PRINT);
}

function sendErrorResponse($message, $statusCode = 500) {
    $response = array(
        'success' => false,
        'message' => 'Error retrieving auctions',
        'error' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    );
    
    http_response_code($statusCode);
    echo json_encode($response, JSON_PRETTY_PRINT);
}

/**
 * Main function to handle auction retrieval
 */
function handleGetAllAuctions() {
    global $db;
    
    try {
        // Validate database connection
        if (!$db || $db->connect_error) {
            throw new Exception("Database connection failed: " . ($db ? $db->connect_error : "Connection object not found"));
        }
        
        // Get all auctions
        $auctions = getAllAuctions($db);
        
        // Send success response
        sendSuccessResponse($auctions);
        
    } catch (Exception $e) {
        // Send error response
        sendErrorResponse($e->getMessage());
    } finally {
        // Close database connection if it exists
        if ($db && !$db->connect_error) {
            $db->close();
        }
    }
}

// Execute the main function
handleGetAllAuctions();
?>