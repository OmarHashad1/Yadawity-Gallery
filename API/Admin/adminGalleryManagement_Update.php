<?php
include_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, 'Invalid JSON input', null, 400);
    }
    
    // Validate required fields
    $error = validateRequired($input, ['gallery_id']);
    if ($error) {
        sendResponse(false, $error, null, 400);
    }
    
    $galleryId = $input['gallery_id'];
    
    // Check if gallery exists
    $checkStmt = $pdo->prepare("
        SELECT g.gallery_id, g.gallery_type 
        FROM galleries g 
        WHERE g.gallery_id = ?
    ");
    $checkStmt->execute([$galleryId]);
    $gallery = $checkStmt->fetch();
    
    if (!$gallery) {
        sendResponse(false, 'Gallery not found', null, 404);
    }
    
    $updateFields = [];
    $params = [];
    
    // Build dynamic update query
    $allowedFields = [
        'title', 'description', 'price', 'address', 'city', 'phone', 
        'start_date', 'duration', 'is_active'
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
    
    // Validate fields based on gallery type
    if ($gallery['gallery_type'] === 'physical') {
        if (isset($input['address']) && empty($input['address'])) {
            sendResponse(false, 'Address cannot be empty for physical galleries', null, 400);
        }
        if (isset($input['city']) && empty($input['city'])) {
            sendResponse(false, 'City cannot be empty for physical galleries', null, 400);
        }
    } elseif ($gallery['gallery_type'] === 'virtual') {
        if (isset($input['price']) && (empty($input['price']) || $input['price'] <= 0)) {
            sendResponse(false, 'Price must be greater than 0 for virtual galleries', null, 400);
        }
    }
    
    // Validate start date if being updated
    if (isset($input['start_date'])) {
        $startTimestamp = strtotime($input['start_date']);
        if (!$startTimestamp) {
            sendResponse(false, 'Invalid start date format', null, 400);
        }
        // Update the parameter to proper format
        $params[array_search($input['start_date'], $params)] = date('Y-m-d H:i:s', $startTimestamp);
    }
    
    $params[] = $galleryId;
    $sql = "UPDATE galleries SET " . implode(', ', $updateFields) . " WHERE gallery_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);
    
    if ($result) {
        // Get updated gallery data with artist info
        $getGalleryStmt = $pdo->prepare("
            SELECT g.gallery_id, g.title, g.description, g.gallery_type, g.price, g.address, 
                   g.city, g.phone, g.start_date, g.duration, g.is_active, g.created_at,
                   u.user_id as artist_id, u.first_name, u.last_name, u.email
            FROM galleries g 
            JOIN users u ON g.artist_id = u.user_id 
            WHERE g.gallery_id = ?
        ");
        $getGalleryStmt->execute([$galleryId]);
        $updatedGallery = $getGalleryStmt->fetch();
        
        sendResponse(true, 'Gallery updated successfully', $updatedGallery);
    } else {
        sendResponse(false, 'Failed to update gallery', null, 500);
    }

} catch (Exception $e) {
    sendResponse(false, 'Error updating gallery: ' . $e->getMessage(), null, 500);
}
?>
