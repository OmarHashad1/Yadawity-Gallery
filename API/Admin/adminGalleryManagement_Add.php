<?php
include_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, 'Invalid JSON input', null, 400);
    }
    
    // Validate required fields
    $required = ['artist_id', 'title', 'gallery_type', 'duration'];
    $error = validateRequired($input, $required);
    if ($error) {
        sendResponse(false, $error, null, 400);
    }
    
    $artistId = $input['artist_id'];
    $title = $input['title'];
    $description = $input['description'] ?? '';
    $galleryType = $input['gallery_type'];
    $price = $input['price'] ?? null;
    $address = $input['address'] ?? null;
    $city = $input['city'] ?? null;
    $phone = $input['phone'] ?? null;
    $startDate = $input['start_date'] ?? date('Y-m-d H:i:s');
    $duration = $input['duration'];
    $isActive = $input['is_active'] ?? 1;
    
    // Validate artist exists
    $artistCheck = $pdo->prepare("SELECT user_id FROM users WHERE user_id = ? AND user_type = 'artist'");
    $artistCheck->execute([$artistId]);
    if (!$artistCheck->fetch()) {
        sendResponse(false, 'Artist not found or user is not an artist', null, 404);
    }
    
    // Validate gallery type
    $validTypes = ['virtual', 'physical'];
    if (!in_array($galleryType, $validTypes)) {
        sendResponse(false, 'Invalid gallery type. Must be virtual or physical', null, 400);
    }
    
    // Validate required fields based on gallery type
    if ($galleryType === 'physical') {
        if (empty($address) || empty($city)) {
            sendResponse(false, 'Address and city are required for physical galleries', null, 400);
        }
    } elseif ($galleryType === 'virtual') {
        if (empty($price)) {
            sendResponse(false, 'Price is required for virtual galleries', null, 400);
        }
    }
    
    // Validate start date
    $startTimestamp = strtotime($startDate);
    if (!$startTimestamp) {
        sendResponse(false, 'Invalid start date format', null, 400);
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO galleries (artist_id, title, description, gallery_type, price, address, 
                              city, phone, start_date, duration, is_active) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $artistId, $title, $description, $galleryType, $price, $address, 
        $city, $phone, date('Y-m-d H:i:s', $startTimestamp), $duration, $isActive
    ]);
    
    if ($result) {
        $galleryId = $pdo->lastInsertId();
        
        // Get the created gallery with artist info
        $getGalleryStmt = $pdo->prepare("
            SELECT g.gallery_id, g.title, g.description, g.gallery_type, g.price, g.address, 
                   g.city, g.phone, g.start_date, g.duration, g.is_active, g.created_at,
                   u.user_id as artist_id, u.first_name, u.last_name, u.email
            FROM galleries g 
            JOIN users u ON g.artist_id = u.user_id 
            WHERE g.gallery_id = ?
        ");
        $getGalleryStmt->execute([$galleryId]);
        $gallery = $getGalleryStmt->fetch();
        
        sendResponse(true, 'Gallery created successfully', $gallery, 201);
    } else {
        sendResponse(false, 'Failed to create gallery', null, 500);
    }

} catch (Exception $e) {
    sendResponse(false, 'Error creating gallery: ' . $e->getMessage(), null, 500);
}
?>
