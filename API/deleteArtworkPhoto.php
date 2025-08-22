<?php
require_once 'db.php';

// Set content type to JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Check if photo_id is provided
    if (!isset($input['photo_id']) || empty($input['photo_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Photo ID is required',
            'error_code' => 'MISSING_PHOTO_ID'
        ]);
        exit;
    }
    
    $photo_id = intval($input['photo_id']);
    
    // Start transaction
    $db->begin_transaction();
    
    try {
        // First, check if the photo exists and get its details
        $stmt = $db->prepare("SELECT image_path, artwork_id, is_primary FROM artwork_photos WHERE photo_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }
        
        $stmt->bind_param("i", $photo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $db->rollback();
            http_response_code(404);
            echo json_encode([
                'success' => false, 
                'message' => 'Photo not found',
                'error_code' => 'PHOTO_NOT_FOUND'
            ]);
            exit;
        }
        
        $photo = $result->fetch_assoc();
        $photo_path = $photo['image_path'];
        $artwork_id = $photo['artwork_id'];
        $is_primary = $photo['is_primary'];
        
        // Delete the photo from database
        $stmt = $db->prepare("DELETE FROM artwork_photos WHERE photo_id = ?");
        $stmt->bind_param("i", $photo_id);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Failed to delete photo from database");
        }
        
        // If the deleted photo was primary, set the next photo as primary
        if ($is_primary) {
            // Get the next available photo for this artwork
            $stmt = $db->prepare("SELECT photo_id, image_path FROM artwork_photos WHERE artwork_id = ? ORDER BY created_at ASC LIMIT 1");
            $stmt->bind_param("i", $artwork_id);
            $stmt->execute();
            $next_photo_result = $stmt->get_result();
            
            if ($next_photo_result->num_rows > 0) {
                $next_photo = $next_photo_result->fetch_assoc();
                $next_photo_id = $next_photo['photo_id'];
                $next_photo_path = $next_photo['image_path'];
                
                // Set the next photo as primary
                $stmt = $db->prepare("UPDATE artwork_photos SET is_primary = 1 WHERE photo_id = ?");
                $stmt->bind_param("i", $next_photo_id);
                $stmt->execute();
                
                // Update the artwork table with the new primary image
                $stmt = $db->prepare("UPDATE artworks SET artwork_image = ? WHERE artwork_id = ?");
                $stmt->bind_param("si", $next_photo_path, $artwork_id);
                $stmt->execute();
                
                $new_primary_info = [
                    'new_primary_photo_id' => $next_photo_id,
                    'new_primary_image_path' => $next_photo_path
                ];
            } else {
                // No more photos left, clear the artwork_image
                $stmt = $db->prepare("UPDATE artworks SET artwork_image = NULL WHERE artwork_id = ?");
                $stmt->bind_param("i", $artwork_id);
                $stmt->execute();
                
                $new_primary_info = [
                    'new_primary_photo_id' => null,
                    'new_primary_image_path' => null,
                    'message' => 'No more photos available, artwork image cleared'
                ];
            }
        } else {
            $new_primary_info = null;
        }
        
        // Commit transaction
        $db->commit();
        
        // Try to delete the physical file (optional - don't fail if file doesn't exist)
        $file_path = __DIR__ . '/../uploads/artwork/' . $photo_path;
        if (file_exists($file_path)) {
            @unlink($file_path); // @ suppresses warnings if file can't be deleted
        }
        
        // Return success response with primary photo update info
        $response_data = [
            'photo_id' => $photo_id,
            'artwork_id' => $artwork_id,
            'was_primary' => $is_primary
        ];
        
        if ($new_primary_info !== null) {
            $response_data['primary_update'] = $new_primary_info;
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Photo deleted successfully',
            'data' => $response_data
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    // Log error
    error_log("Delete artwork photo error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete photo: ' . $e->getMessage(),
        'error_code' => 'SERVER_ERROR'
    ]);
}

// Close connection
$db->close();
?>
