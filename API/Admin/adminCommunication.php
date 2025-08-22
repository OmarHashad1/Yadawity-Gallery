<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Get all reviews/communications
    $sql = "SELECT ar.id, ar.rating, ar.feedback, ar.created_at,
                   u1.first_name as reviewer_first_name, u1.last_name as reviewer_last_name, u1.email as reviewer_email,
                   u2.first_name as artist_first_name, u2.last_name as artist_last_name, u2.email as artist_email,
                   a.title as artwork_title
            FROM artist_reviews ar
            JOIN users u1 ON ar.user_id = u1.user_id
            JOIN users u2 ON ar.artist_id = u2.user_id
            LEFT JOIN artworks a ON ar.artwork_id = a.artwork_id
            ORDER BY ar.created_at DESC";
    
    $stmt = $pdo->query($sql);
    $communications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $communications
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
