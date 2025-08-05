<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db.php';

function getArtistInfo($artist_id) {
    global $db;
    
    try {
        // Validate input
        if (!$artist_id || !is_numeric($artist_id)) {
            throw new InvalidArgumentException('Valid artist ID is required');
        }
        
        // Prepare and execute query to get artist information
        $query = "SELECT 
                    profile_picture,
                    first_name,
                    last_name,
                    art_specialty,
                    years_of_experience,
                    achievements,
                    artist_bio,
                    location,
                    education
                  FROM users 
                  WHERE user_id = ? AND user_type = 'artist' AND is_active = 1";
        
        $stmt = $db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database prepare error: ' . $db->error);
        }
        
        $stmt->bind_param("i", $artist_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Database execution error: ' . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Artist not found', 404);
        }
        
        $artist = $result->fetch_assoc();
        
        // Format and return the response
        return [
            'success' => true,
            'data' => [
                'profile_picture' => $artist['profile_picture'],
                'first_name' => $artist['first_name'],
                'last_name' => $artist['last_name'],
                'full_name' => $artist['first_name'] . ' ' . $artist['last_name'],
                'art_specialty' => $artist['art_specialty'],
                'years_of_experience' => (int)$artist['years_of_experience'],
                'achievements' => $artist['achievements'],
                'artist_bio' => $artist['artist_bio'],
                'location' => $artist['location'],
                'education' => $artist['education']
            ]
        ];
        
    } catch (InvalidArgumentException $e) {
        http_response_code(400);
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    } catch (Exception $e) {
        $code = $e->getCode() === 404 ? 404 : 500;
        http_response_code($code);
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
    }
}

try {
    // Get artist ID from query parameter
    $artist_id = isset($_GET['artist_id']) ? (int)$_GET['artist_id'] : null;
    
    // Get artist information
    $response = getArtistInfo($artist_id);
    
    // Output JSON response
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($db) && $db) {
        $db->close();
    }
}
?>