<?php
include_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, 'Invalid JSON input', null, 400);
    }
    
    // Validate required fields
    $required = ['title', 'price', 'artist_id', 'type'];
    $error = validateRequired($input, $required);
    if ($error) {
        sendResponse(false, $error, null, 400);
    }
    
    $title = $input['title'];
    $description = $input['description'] ?? '';
    $price = $input['price'];
    $artistId = $input['artist_id'];
    $type = $input['type'];
    $material = $input['material'] ?? '';
    $dimensions = $input['dimensions'] ?? '';
    $year = $input['year'] ?? null;
    $artworkImage = $input['artwork_image'] ?? null;
    
    // Validate artist exists
    $artistCheck = $pdo->prepare("SELECT user_id FROM users WHERE user_id = ? AND user_type = 'artist'");
    $artistCheck->execute([$artistId]);
    if (!$artistCheck->fetch()) {
        sendResponse(false, 'Artist not found or user is not an artist', null, 404);
    }
    
    // Validate artwork type
    $validTypes = ['painting', 'sculpture', 'photography', 'digital', 'mixed_media', 'other'];
    if (!in_array($type, $validTypes)) {
        sendResponse(false, 'Invalid artwork type', null, 400);
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO artworks (title, description, price, artist_id, type, material, dimensions, year, artwork_image) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $title, $description, $price, $artistId, $type, $material, $dimensions, $year, $artworkImage
    ]);
    
    if ($result) {
        $artworkId = $pdo->lastInsertId();
        
        // Get the created artwork with artist info
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
        
        sendResponse(true, 'Artwork created successfully', $artwork, 201);
    } else {
        sendResponse(false, 'Failed to create artwork', null, 500);
    }

} catch (Exception $e) {
    sendResponse(false, 'Error creating artwork: ' . $e->getMessage(), null, 500);
}
?>
