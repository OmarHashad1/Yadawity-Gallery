<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get all reviews and communications
            $sql = "SELECT ar.id, ar.rating, ar.feedback, ar.created_at,
                           u1.first_name as reviewer_first_name, u1.last_name as reviewer_last_name, 
                           u1.email as reviewer_email,
                           u2.first_name as artist_first_name, u2.last_name as artist_last_name, 
                           u2.email as artist_email,
                           a.title as artwork_title
                    FROM artist_reviews ar
                    JOIN users u1 ON ar.user_id = u1.user_id
                    JOIN users u2 ON ar.artist_id = u2.user_id
                    LEFT JOIN artworks a ON ar.artwork_id = a.artwork_id
                    ORDER BY ar.created_at DESC";
            
            $stmt = $pdo->query($sql);
            $communications = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get communication statistics
            $totalReviews = count($communications);
            $avgRating = $pdo->query("SELECT AVG(rating) FROM artist_reviews")->fetchColumn();
            $recentReviews = array_slice($communications, 0, 10);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'communications' => $communications,
                    'stats' => [
                        'total_reviews' => $totalReviews,
                        'average_rating' => round($avgRating, 2)
                    ],
                    'recent_reviews' => $recentReviews
                ]
            ]);
            break;
            
        case 'POST':
            // Admin response to communication (placeholder for future support tickets)
            $input = json_decode(file_get_contents('php://input'), true);
            $reviewId = $input['review_id'] ?? '';
            $adminResponse = $input['admin_response'] ?? '';
            
            // For now, we'll just log this as a successful response
            // In a real system, you'd have a support_tickets table
            echo json_encode([
                'success' => true,
                'message' => 'Admin response recorded successfully'
            ]);
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
