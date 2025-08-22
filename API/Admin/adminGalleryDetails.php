<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    $galleryId = $_GET['gallery_id'] ?? '';
    
    if (!$galleryId) {
        echo json_encode(['success' => false, 'message' => 'Gallery ID required']);
        exit;
    }
    
    // Get gallery details
    $stmt = $pdo->prepare("
        SELECT g.*, u.first_name, u.last_name, u.email, u.art_specialty
        FROM galleries g
        JOIN users u ON g.artist_id = u.user_id
        WHERE g.gallery_id = ?
    ");
    $stmt->execute([$galleryId]);
    $gallery = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$gallery) {
        echo json_encode(['success' => false, 'message' => 'Gallery not found']);
        exit;
    }
    
    // Get artist's artworks (potential gallery content)
    $stmt = $pdo->prepare("
        SELECT artwork_id, title, price, type, artwork_image, is_available
        FROM artworks 
        WHERE artist_id = ?
        ORDER BY created_at DESC
        LIMIT 20
    ");
    $stmt->execute([$gallery['artist_id']]);
    $artworks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'gallery' => $gallery,
            'artworks' => $artworks,
            'total_artworks' => count($artworks)
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
