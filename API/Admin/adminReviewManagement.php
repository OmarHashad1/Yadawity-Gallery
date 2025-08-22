<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

include_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get all reviews with detailed information
            $reviews = $pdo->query("
                SELECT ar.id, ar.rating, ar.feedback, ar.created_at,
                       u1.first_name as reviewer_first_name, u1.last_name as reviewer_last_name, 
                       u1.email as reviewer_email,
                       u2.first_name as artist_first_name, u2.last_name as artist_last_name, 
                       u2.email as artist_email,
                       a.title as artwork_title, a.artwork_id
                FROM artist_reviews ar
                JOIN users u1 ON ar.user_id = u1.user_id
                JOIN users u2 ON ar.artist_id = u2.user_id
                LEFT JOIN artworks a ON ar.artwork_id = a.artwork_id
                ORDER BY ar.created_at DESC
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Review statistics
            $stats = [
                'total_reviews' => count($reviews),
                'average_rating' => $pdo->query("SELECT AVG(rating) FROM artist_reviews")->fetchColumn(),
                'rating_distribution' => $pdo->query("
                    SELECT rating, COUNT(*) as count
                    FROM artist_reviews
                    GROUP BY rating
                    ORDER BY rating DESC
                ")->fetchAll(PDO::FETCH_ASSOC),
                'recent_reviews' => array_slice($reviews, 0, 10)
            ];
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'reviews' => $reviews,
                    'statistics' => $stats
                ]
            ]);
            break;
            
        case 'DELETE':
            // Delete a review
            $input = json_decode(file_get_contents('php://input'), true);
            $reviewId = $input['review_id'];
            
            $stmt = $pdo->prepare("DELETE FROM artist_reviews WHERE id = ?");
            $result = $stmt->execute([$reviewId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Review deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete review']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
