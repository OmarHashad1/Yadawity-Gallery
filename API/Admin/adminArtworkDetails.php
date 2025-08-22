<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    $artworkId = $_GET['artwork_id'] ?? '';
    
    if (!$artworkId) {
        echo json_encode(['success' => false, 'message' => 'Artwork ID required']);
        exit;
    }
    
    // Get artwork details with artist info
    $stmt = $pdo->prepare("
        SELECT a.*, u.first_name, u.last_name, u.email, u.art_specialty
        FROM artworks a
        JOIN users u ON a.artist_id = u.user_id
        WHERE a.artwork_id = ?
    ");
    $stmt->execute([$artworkId]);
    $artwork = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$artwork) {
        echo json_encode(['success' => false, 'message' => 'Artwork not found']);
        exit;
    }
    
    // Get artwork photos
    $stmt = $pdo->prepare("
        SELECT photo_id, image_path, is_primary
        FROM artwork_photos 
        WHERE artwork_id = ?
    ");
    $stmt->execute([$artworkId]);
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Check if in any active auction
    $stmt = $pdo->prepare("
        SELECT id, starting_bid, current_bid, start_time, end_time, status
        FROM auctions 
        WHERE product_id = ? AND status = 'active'
    ");
    $stmt->execute([$artworkId]);
    $auction = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get reviews for this artwork
    $stmt = $pdo->prepare("
        SELECT ar.rating, ar.feedback, ar.created_at, u.first_name, u.last_name
        FROM artist_reviews ar
        JOIN users u ON ar.user_id = u.user_id
        WHERE ar.artwork_id = ?
        ORDER BY ar.created_at DESC
    ");
    $stmt->execute([$artworkId]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'artwork' => $artwork,
            'photos' => $photos,
            'auction' => $auction,
            'reviews' => $reviews
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
