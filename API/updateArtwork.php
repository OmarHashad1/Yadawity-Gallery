<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

require_once "db.php";

// Function to validate user cookie and get user ID
function validateUserCookie($db) {
    if (!isset($_COOKIE['user_login'])) {
        return null;
    }
    
    $cookieValue = $_COOKIE['user_login'];
    
    // Extract user ID from cookie (format: user_id_hash)
    $parts = explode('_', $cookieValue, 2);
    if (count($parts) !== 2) {
        return null;
    }
    
    $user_id = (int)$parts[0];
    $provided_hash = $parts[1];
    
    if ($user_id <= 0) {
        return null;
    }
    
    // Verify the hash against the user's email
    $stmt = $db->prepare("SELECT email, is_active FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return null;
    }
    
    $user = $result->fetch_assoc();
    
    // Check if user is active
    if (!$user['is_active']) {
        return null;
    }
    
    // Verify the hash
    $expected_hash = hash('sha256', $user['email'] . 'yadawity_salt');
    
    if ($provided_hash !== $expected_hash) {
        return null;
    }
    
    return $user_id;
}

// Function to get authenticated user ID
function getAuthenticatedUserId($db) {
    // First try to get from cookie
    $cookie_user_id = validateUserCookie($db);
    if ($cookie_user_id) {
        return $cookie_user_id;
    }
    
    // Fallback to session
    if (isset($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    }
    
    // No authentication found
    return null;
}

// Set proper headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Only POST requests are accepted.',
        'error_code' => 'METHOD_NOT_ALLOWED'
    ]);
    exit;
}

// Function to upload artwork image
function uploadArtworkImage($file, $artistId, $artworkId) {
    try {
        $uploadDir = dirname(__DIR__) . '/image/';
        
        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return ['success' => false, 'message' => 'Failed to create upload directory'];
            }
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type. Please upload JPEG, PNG, GIF, or WebP images only.'];
        }
        
        // Get file extension
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            return ['success' => false, 'message' => 'Invalid file extension. Please upload JPEG, PNG, GIF, or WebP images only.'];
        }
        
        // Validate file size (50MB limit)
        $maxSize = 50 * 1024 * 1024; // 50MB in bytes
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'File size too large. Maximum size is 50MB.'];
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload error: ' . $file['error']];
        }
        
        // Generate unique filename
        $timestamp = time();
        $randomId = uniqid();
        $filename = "artwork_{$artistId}_{$artworkId}_{$timestamp}_{$randomId}.{$fileExtension}";
        $targetPath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $targetPath
            ];
        } else {
            return ['success' => false, 'message' => 'Failed to move uploaded file'];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Upload error: ' . $e->getMessage()];
    }
}

try {
    // Debug: Log received POST data
    error_log('updateArtwork.php - POST data: ' . print_r($_POST, true));
    error_log('updateArtwork.php - FILES data: ' . print_r($_FILES, true));
    
    // Check if user is authenticated
    $user_id = getAuthenticatedUserId($db);
    
    if (!$user_id) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'User not authenticated. Please log in.',
            'error_code' => 'AUTHENTICATION_REQUIRED'
        ]);
        exit;
    }
    
    // Validate required fields
    if (!isset($_POST['artwork_id']) || empty($_POST['artwork_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Artwork ID is required',
            'error_code' => 'MISSING_ARTWORK_ID'
        ]);
        exit;
    }
    
    $artwork_id = (int)$_POST['artwork_id'];
    
    // Verify the artwork belongs to the current user
    $checkStmt = $db->prepare("SELECT artist_id FROM artworks WHERE artwork_id = ?");
    $checkStmt->bind_param("i", $artwork_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Artwork not found. It may have been deleted.',
            'error_code' => 'ARTWORK_NOT_FOUND'
        ]);
        exit;
    }
    
    $artwork = $result->fetch_assoc();
    if ($artwork['artist_id'] != $user_id) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'You are not authorized to edit this artwork.',
            'error_code' => 'UNAUTHORIZED_ACCESS'
        ]);
        exit;
    }
    
    // Get form data - updated to match new edit form field names
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $year = !empty($_POST['year']) ? (int)$_POST['year'] : null;
    
    // Handle new field structure
    $category = trim($_POST['category'] ?? '');  // New category field
    $style = trim($_POST['style'] ?? '');        // New style field
    $medium = trim($_POST['medium'] ?? '');      // New medium field
    $width = !empty($_POST['width']) ? floatval($_POST['width']) : null;    // New width field
    $height = !empty($_POST['height']) ? floatval($_POST['height']) : null;  // New height field
    $depth = !empty($_POST['depth']) ? floatval($_POST['depth']) : null;     // New depth field
    
    // Map form category values to database enum values
    $categoryMapping = [
        'paintings' => 'painting',
        'sculptures' => 'sculpture',
        'textiles' => 'other',
        'wooden_arts' => 'other',
        'ceramic_arts' => 'other',
        'fiber_arts' => 'other'
    ];
    
    // Convert category to database enum value
    if (isset($categoryMapping[$category])) {
        $category = $categoryMapping[$category];
    }
    
    // Build dimensions string from individual fields
    $dimensions = '';
    if ($width && $height) {
        $dimensions = $width . 'x' . $height;
        if ($depth) {
            $dimensions .= 'x' . $depth;
        }
        $dimensions .= ' cm';
    }
    
    // Store style and medium information in the material field (since we don't have separate fields)
    $material_info = [];
    if (!empty($style)) {
        $material_info[] = 'Style: ' . $style;
    }
    if (!empty($medium)) {
        $material_info[] = 'Medium: ' . $medium;
    }
    $material = implode(', ', $material_info);
    
    // For backward compatibility, also check old field names
    if (empty($category)) {
        $category = trim($_POST['type'] ?? '');  // Fallback to old 'type' field
    }
    if (empty($dimensions)) {
        $dimensions = trim($_POST['dimensions'] ?? '');  // Fallback to old 'dimensions' field
    }
    if (empty($material)) {
        $material = trim($_POST['material'] ?? '');  // Fallback to old 'material' field
    }
    
    $is_available = isset($_POST['is_available']) ? (int)$_POST['is_available'] : 1;
    $on_auction = isset($_POST['on_auction']) ? (int)$_POST['on_auction'] : 0;
    
    // Validate required fields
    if (empty($title)) {
        error_log('updateArtwork.php - Missing title field');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Title is required',
            'error_code' => 'MISSING_TITLE'
        ]);
        exit;
    }
    
    if (empty($category)) {
        error_log('updateArtwork.php - Missing category field');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Category is required',
            'error_code' => 'MISSING_CATEGORY'
        ]);
        exit;
    }
    
    if ($price <= 0) {
        error_log('updateArtwork.php - Invalid price: ' . $price);
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Price must be greater than 0',
            'error_code' => 'INVALID_PRICE'
        ]);
        exit;
    }
    
    if (empty($width) || empty($height)) {
        error_log('updateArtwork.php - Missing dimensions - width: ' . $width . ', height: ' . $height);
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Width and height dimensions are required',
            'error_code' => 'MISSING_DIMENSIONS'
        ]);
        exit;
    }
    
    // Style is required for new form structure
    if (empty($style)) {
        error_log('updateArtwork.php - Missing style field');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Art style is required. Please select an art style for your artwork.',
            'error_code' => 'MISSING_STYLE'
        ]);
        exit;
    }
    
    // Handle image upload if provided
    $artwork_image = null;
    if (isset($_FILES['artwork_image']) && $_FILES['artwork_image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = uploadArtworkImage($_FILES['artwork_image'], $user_id, $artwork_id);
        if (!$uploadResult['success']) {
            throw new Exception($uploadResult['message']);
        }
        $artwork_image = $uploadResult['filename'];
    }
    
    // Start transaction
    $db->autocommit(false);
    
    try {
        // Update artwork record
        if ($artwork_image) {
            // Update with new image
            $stmt = $db->prepare("
                UPDATE artworks 
                SET title = ?, description = ?, price = ?, dimensions = ?, year = ?, 
                    material = ?, type = ?, is_available = ?, on_auction = ?, artwork_image = ?
                WHERE artwork_id = ? AND artist_id = ?
            ");
            $stmt->bind_param("ssdsisssisii", 
                $title, $description, $price, $dimensions, $year, 
                $material, $category, $is_available, $on_auction, $artwork_image, 
                $artwork_id, $user_id
            );
        } else {
            // Update without changing image
            $stmt = $db->prepare("
                UPDATE artworks 
                SET title = ?, description = ?, price = ?, dimensions = ?, year = ?, 
                    material = ?, type = ?, is_available = ?, on_auction = ?
                WHERE artwork_id = ? AND artist_id = ?
            ");
            $stmt->bind_param("ssdsisssiii", 
                $title, $description, $price, $dimensions, $year, 
                $material, $category, $is_available, $on_auction, 
                $artwork_id, $user_id
            );
        }
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update artwork due to a database error. Please try again.');
        }
        
        if ($stmt->affected_rows === 0) {
            throw new Exception('No changes were made. The artwork may be unchanged or no longer exists.');
        }
        
        // Commit transaction
        $db->commit();
        
        // Get updated artwork data
        $getStmt = $db->prepare("
            SELECT artwork_id, title, description, price, dimensions, year, 
                   material, type, is_available, on_auction, artwork_image, 
                   created_at
            FROM artworks 
            WHERE artwork_id = ? AND artist_id = ?
        ");
        $getStmt->bind_param("ii", $artwork_id, $user_id);
        $getStmt->execute();
        $updatedArtwork = $getStmt->get_result()->fetch_assoc();
        
        echo json_encode([
            'success' => true,
            'message' => 'Your artwork has been successfully updated with all the new changes.',
            'data' => [
                'artwork' => $updatedArtwork,
                'image_uploaded' => $artwork_image ? true : false,
                'new_image' => $artwork_image
            ]
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    // Restore autocommit
    $db->autocommit(true);
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => 'UPDATE_FAILED'
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($checkStmt)) $checkStmt->close();
    if (isset($getStmt)) $getStmt->close();
    $db->close();
}
?>
