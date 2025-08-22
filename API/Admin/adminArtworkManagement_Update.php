<?php
include_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, 'Invalid JSON input', null, 400);
    }
    
    // Validate required fields
    $error = validateRequired($input, ['artwork_id']);
    if ($error) {
        sendResponse(false, $error, null, 400);
    }
    
    $artworkId = $input['artwork_id'];
    
    // Check if artwork exists
    $checkStmt = $pdo->prepare("SELECT artwork_id FROM artworks WHERE artwork_id = ?");
    $checkStmt->execute([$artworkId]);
    if (!$checkStmt->fetch()) {
        sendResponse(false, 'Artwork not found', null, 404);
    }
    
    $updateFields = [];
    $params = [];
    
    // Build dynamic update query
    $allowedFields = [
        'title', 'description', 'price', 'dimensions', 'year', 'material', 
        'artwork_image', 'type', 'is_available', 'on_auction'
    ];
    
    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updateFields[] = "$field = ?";
            $params[] = $input[$field];
        }
    }
    
    if (empty($updateFields)) {
        sendResponse(false, 'No valid fields to update', null, 400);
    }
    
    // Validate artwork type if being updated
    if (isset($input['type'])) {
        $validTypes = ['painting', 'sculpture', 'photography', 'digital', 'mixed_media', 'other'];
        if (!in_array($input['type'], $validTypes)) {
            sendResponse(false, 'Invalid artwork type', null, 400);
        }
    }
    
    $params[] = $artworkId;
    $sql = "UPDATE artworks SET " . implode(', ', $updateFields) . " WHERE artwork_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);
    
    if ($result) {
        // Get updated artwork data with artist info
        $getArtworkStmt = $pdo->prepare("
            SELECT a.artwork_id, a.title, a.description, a.price, a.dimensions, a.year, 
                   a.material, a.artwork_image, a.type, a.is_available, a.on_auction, a.created_at,
                   u.first_name, u.last_name, u.email, u.user_id as artist_id
            FROM artworks a 
            JOIN users u ON a.artist_id = u.user_id 
            WHERE a.artwork_id = ?
        ");
        $getArtworkStmt->execute([$artworkId]);
        $artwork = $getArtworkStmt->fetch();
        
        sendResponse(true, 'Artwork updated successfully', $artwork);
    } else {
        sendResponse(false, 'Failed to update artwork', null, 500);
    }

} catch (Exception $e) {
    sendResponse(false, 'Error updating artwork: ' . $e->getMessage(), null, 500);
}
?>
