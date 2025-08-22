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
            // Get all artworks with artist info
            $sql = "SELECT a.artwork_id, a.title, a.price, a.type, a.is_available, a.on_auction, a.created_at,
                           u.first_name, u.last_name, u.email
                    FROM artworks a 
                    JOIN users u ON a.artist_id = u.user_id 
                    ORDER BY a.created_at DESC";
            
            $stmt = $pdo->query($sql);
            $artworks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $artworks
            ]);
            break;
            
        case 'POST':
            // Create new artwork (admin can add artwork on behalf of artists)
            $input = json_decode(file_get_contents('php://input'), true);
            $title = $input['title'];
            $description = $input['description'] ?? '';
            $price = $input['price'];
            $artistId = $input['artist_id'];
            $type = $input['type'];
            $medium = $input['medium'] ?? '';
            $dimensions = $input['dimensions'] ?? '';
            
            $stmt = $pdo->prepare("INSERT INTO artworks (title, description, price, artist_id, type, medium, dimensions) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([$title, $description, $price, $artistId, $type, $medium, $dimensions]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Artwork created successfully', 'artwork_id' => $pdo->lastInsertId()]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create artwork']);
            }
            break;
            
        case 'PUT':
            // Update artwork details
            $input = json_decode(file_get_contents('php://input'), true);
            $artworkId = $input['artwork_id'];
            $updateFields = [];
            $params = [];
            
            if (isset($input['title'])) {
                $updateFields[] = "title = ?";
                $params[] = $input['title'];
            }
            if (isset($input['description'])) {
                $updateFields[] = "description = ?";
                $params[] = $input['description'];
            }
            if (isset($input['price'])) {
                $updateFields[] = "price = ?";
                $params[] = $input['price'];
            }
            if (isset($input['is_available'])) {
                $updateFields[] = "is_available = ?";
                $params[] = $input['is_available'];
            }
            if (isset($input['on_auction'])) {
                $updateFields[] = "on_auction = ?";
                $params[] = $input['on_auction'];
            }
            if (isset($input['type'])) {
                $updateFields[] = "type = ?";
                $params[] = $input['type'];
            }
            
            $params[] = $artworkId;
            $sql = "UPDATE artworks SET " . implode(', ', $updateFields) . " WHERE artwork_id = ?";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Artwork updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update artwork']);
            }
            break;
            
        case 'DELETE':
            // Delete artwork (soft delete by marking as unavailable)
            $input = json_decode(file_get_contents('php://input'), true);
            $artworkId = $input['artwork_id'];
            
            $stmt = $pdo->prepare("UPDATE artworks SET is_available = 0, deleted_at = NOW() WHERE artwork_id = ?");
            $result = $stmt->execute([$artworkId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Artwork deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete artwork']);
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
