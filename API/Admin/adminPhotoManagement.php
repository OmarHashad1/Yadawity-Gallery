<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once '../db.php';

try {
    // Get all artwork photos with artwork and artist info
    $photos = $pdo->query("
        SELECT ap.photo_id, ap.image_path, ap.is_primary, ap.created_at,
               a.artwork_id, a.title, a.type,
               u.first_name, u.last_name, u.email
        FROM artwork_photos ap
        JOIN artworks a ON ap.artwork_id = a.artwork_id
        JOIN users u ON a.artist_id = u.user_id
        ORDER BY ap.created_at DESC
        LIMIT 100
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Photo statistics
    $stats = [
        'total_photos' => count($photos),
        'primary_photos' => count(array_filter($photos, function($p) { return $p['is_primary']; })),
        'secondary_photos' => count(array_filter($photos, function($p) { return !$p['is_primary']; })),
        'artworks_with_photos' => $pdo->query("SELECT COUNT(DISTINCT artwork_id) FROM artwork_photos")->fetchColumn(),
        'artworks_without_photos' => $pdo->query("SELECT COUNT(*) FROM artworks WHERE artwork_id NOT IN (SELECT DISTINCT artwork_id FROM artwork_photos)")->fetchColumn()
    ];

    echo json_encode([
        'success' => true,
        'data' => [
            'photos' => $photos,
            'statistics' => $stats
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
