<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT');
header('Access-Control-Allow-Headers: Content-Type');

include_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get all galleries with artist info
            $sql = "SELECT g.gallery_id, g.title, g.gallery_type, g.price, g.city, g.duration, g.is_active, g.created_at,
                           u.first_name, u.last_name, u.email
                    FROM galleries g 
                    JOIN users u ON g.artist_id = u.user_id 
                    ORDER BY g.created_at DESC";
            
            $stmt = $pdo->query($sql);
            $galleries = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $galleries
            ]);
            break;
            
        case 'PUT':
            // Update gallery status
            $input = json_decode(file_get_contents('php://input'), true);
            $galleryId = $input['gallery_id'];
            $isActive = $input['is_active'];
            
            $stmt = $pdo->prepare("UPDATE galleries SET is_active = ? WHERE gallery_id = ?");
            $result = $stmt->execute([$isActive, $galleryId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Gallery updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update gallery']);
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
