<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "db.php";

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

// File upload function with duplicate prevention
function uploadArtworkImage($file, $artistId, $artworkId = null, $uploadDir = '../uploads/artworks/') {
    try {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.'];
        }
        
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            return ['success' => false, 'message' => 'Invalid file extension.'];
        }
        
        // Check file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'message' => 'File size too large. Maximum 5MB allowed.'];
        }
        
        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return ['success' => false, 'message' => 'Failed to create upload directory.'];
            }
        }
        
        // Generate hash of file content to check for duplicates
        $fileHash = md5_file($file['tmp_name']);
        
        // Check for existing files with same hash for this artist/artwork combination
        if ($artworkId) {
            $pattern = $uploadDir . "artist_{$artistId}_artwork_{$artworkId}_*";
            $existingFiles = glob($pattern);
            foreach ($existingFiles as $existingFile) {
                if (file_exists($existingFile) && md5_file($existingFile) === $fileHash) {
                    return [
                        'success' => false, 
                        'message' => 'This image has already been uploaded for this artwork.',
                        'duplicate' => true
                    ];
                }
            }
        } else {
            // For temp files, check against existing temp files for this artist
            $pattern = $uploadDir . "artist_{$artistId}_temp_*";
            $existingFiles = glob($pattern);
            foreach ($existingFiles as $existingFile) {
                if (file_exists($existingFile) && md5_file($existingFile) === $fileHash) {
                    return [
                        'success' => false, 
                        'message' => 'This image has already been uploaded.',
                        'duplicate' => true
                    ];
                }
            }
        }
        
        // Generate structured filename with hash for uniqueness
        $timestamp = time();
        $shortHash = substr($fileHash, 0, 8); // Use first 8 characters of hash
        
        if ($artworkId) {
            $fileName = "artist_{$artistId}_artwork_{$artworkId}_{$timestamp}_{$shortHash}.{$fileExtension}";
        } else {
            // For initial upload before artwork ID is generated
            $fileName = "artist_{$artistId}_temp_{$timestamp}_{$shortHash}.{$fileExtension}";
        }
        
        $filePath = $uploadDir . $fileName;
        
        // Check if filename already exists (unlikely but safe)
        if (file_exists($filePath)) {
            return ['success' => false, 'message' => 'File with this name already exists.'];
        }
        
        // Move uploaded file to temporary location first
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return [
                'success' => true, 
                'filename' => $fileName, 
                'filepath' => $filePath,
                'hash' => $fileHash
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to upload file.'];
        
    } catch (Exception $e) {
        error_log("File upload error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Upload failed due to server error.'];
    }
}

// Function to clean up uploaded files if artwork creation fails
function cleanupUploadedFiles($filenames, $uploadDir = '../uploads/artworks/') {
    if (!$filenames || !is_array($filenames)) {
        return;
    }
    
    foreach ($filenames as $filename) {
        $filePath = $uploadDir . $filename;
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                error_log("Cleaned up file: " . $filename);
            } else {
                error_log("Failed to clean up file: " . $filename);
            }
        }
    }
}

// Function to rename temporary file with actual artwork ID
function renameTempArtworkImage($tempFilename, $artistId, $artworkId, $uploadDir = '../uploads/artworks/') {
    try {
        if (!$tempFilename || !$artistId || !$artworkId) {
            return $tempFilename; // Return original if parameters are missing
        }
        
        $tempPath = $uploadDir . $tempFilename;
        
        // Check if temporary file exists
        if (!file_exists($tempPath)) {
            error_log("Temporary file not found: " . $tempPath);
            return $tempFilename;
        }
        
        // Extract file extension and hash from temp filename
        $fileExtension = pathinfo($tempFilename, PATHINFO_EXTENSION);
        
        // Extract hash from temp filename (assuming format: artist_X_temp_timestamp_hash.ext)
        $filenameParts = explode('_', basename($tempFilename, '.' . $fileExtension));
        $shortHash = end($filenameParts); // Get the last part which should be the hash
        
        // Generate final filename with artwork ID
        $timestamp = time();
        $finalFilename = "artist_{$artistId}_artwork_{$artworkId}_{$timestamp}_{$shortHash}.{$fileExtension}";
        $finalPath = $uploadDir . $finalFilename;
        
        // Check if target filename already exists
        if (file_exists($finalPath)) {
            error_log("Target filename already exists: " . $finalFilename);
            return $tempFilename; // Keep temp name if conflict
        }
        
        // Rename the file
        if (rename($tempPath, $finalPath)) {
            error_log("File renamed from {$tempFilename} to {$finalFilename}");
            return $finalFilename;
        } else {
            error_log("Failed to rename file from {$tempFilename} to {$finalFilename}");
            return $tempFilename; // Return original if rename fails
        }
        
    } catch (Exception $e) {
        error_log("File rename error: " . $e->getMessage());
        return $tempFilename; // Return original if error occurs
    }
}

// Main execution
try {
    // Debug: Log received data
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
    
    // Validate database connection
    if (!isset($db) || $db->connect_error) {
        throw new Exception("Database connection failed: " . ($db->connect_error ?? "Unknown error"));
    }
    
    // Get POST data
    $postData = $_POST;
    
    // Track uploaded files for cleanup if needed
    $uploadedFiles = [];
    
    // Required fields validation
    $errors = [];
    
    if (empty($postData['title'])) {
        $errors[] = 'Artwork title is required';
    }
    
    if (empty($postData['price']) || !is_numeric($postData['price']) || $postData['price'] <= 0) {
        $errors[] = 'Valid price is required';
    }
    
    if (empty($postData['category'])) {
        $errors[] = 'Category is required';
    }
    
    if (empty($postData['description'])) {
        $errors[] = 'Description is required';
    }
    
    if (empty($postData['artist_id']) || !is_numeric($postData['artist_id'])) {
        $errors[] = 'Valid artist ID is required';
    }
    
    // Validate dimensions if provided
    $width = null;
    $height = null;
    $depth = null;
    
    if (!empty($postData['width'])) {
        if (!is_numeric($postData['width']) || $postData['width'] <= 0) {
            $errors[] = 'Valid width is required if specified';
        } else {
            $width = floatval($postData['width']);
        }
    }
    
    if (!empty($postData['height'])) {
        if (!is_numeric($postData['height']) || $postData['height'] <= 0) {
            $errors[] = 'Valid height is required if specified';
        } else {
            $height = floatval($postData['height']);
        }
    }
    
    if (!empty($postData['depth'])) {
        if (!is_numeric($postData['depth']) || $postData['depth'] <= 0) {
            $errors[] = 'Valid depth is required if specified';
        } else {
            $depth = floatval($postData['depth']);
        }
    }
    
    // Return validation errors if any
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors,
            'received_data' => $postData
        ]);
        exit;
    }
    
    // Handle image upload AFTER validation passes but BEFORE database insertion
    $artworkImage = null;
    if (isset($_FILES['artwork_image']) && $_FILES['artwork_image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = uploadArtworkImage($_FILES['artwork_image'], $postData['artist_id']);
        if (!$uploadResult['success']) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Image upload failed',
                'error' => $uploadResult['message'],
                'duplicate' => $uploadResult['duplicate'] ?? false
            ]);
            exit;
        }
        $artworkImage = $uploadResult['filename'];
        $uploadedFiles[] = $artworkImage; // Track for potential cleanup
    }
    
    // Sanitize input
    $title = trim(strip_tags($postData['title']));
    $description = trim(strip_tags($postData['description']));
    $price = floatval($postData['price']);
    $category = trim(strtolower($postData['category']));
    $material = !empty($postData['material']) ? trim($postData['material']) : '';
    $style = !empty($postData['style']) ? trim($postData['style']) : '';
    $year = !empty($postData['year']) ? intval($postData['year']) : date('Y');
    $artist_id = intval($postData['artist_id']);
    
    // Create dimensions string if dimensions are provided
    $dimensions = null;
    if ($width !== null && $height !== null) {
        $dimensions = $width . 'cm × ' . $height . 'cm';
        if ($depth !== null) {
            $dimensions .= ' × ' . $depth . 'cm';
        }
    }
    
    // Map category to database enum values
    $typeMapping = [
        'painting' => 'painting',
        'sculpture' => 'sculpture', 
        'photography' => 'photography',
        'digital' => 'digital',
        'mixed' => 'mixed_media',
        'mixed_media' => 'mixed_media',
        'textile' => 'other',
        'other' => 'other'
    ];
    $artworkType = isset($typeMapping[$category]) ? $typeMapping[$category] : 'other';
    
    // Build material string from style and material
    $materialString = '';
    if (!empty($style)) {
        $materialString = $style;
    }
    if (!empty($material)) {
        $materialString .= (!empty($materialString) ? ' - ' : '') . $material;
    }
    if (empty($materialString)) {
        $materialString = 'Mixed Media'; // Default value
    }
    
    // Prepare SQL statement - matching the exact database schema
    $sql = "INSERT INTO artworks (
        artist_id, title, description, price, dimensions, year, 
        material, artwork_image, type, is_available, on_auction
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0)";
    
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $db->error);
    }
    
    // Debug: Log the values being bound
    error_log("Binding values: artist_id=$artist_id, title=$title, description=$description, price=$price, dimensions=$dimensions, year=$year, material=$materialString, artwork_image=$artworkImage, type=$artworkType");
    
    $stmt->bind_param(
        "issdsisss",
        $artist_id,
        $title,
        $description,
        $price,
        $dimensions,
        $year,
        $materialString,
        $artworkImage,
        $artworkType
    );
    
    // Execute database insertion
    if (!$stmt->execute()) {
        // Database insertion failed - clean up uploaded files
        cleanupUploadedFiles($uploadedFiles);
        throw new Exception("Failed to insert artwork: " . $stmt->error);
    }
    
    $artworkId = $db->insert_id;
    $stmt->close();
    
    // Rename uploaded image file with actual artwork ID (only if database insertion succeeded)
    $finalImageName = $artworkImage;
    if ($artworkImage) {
        $renameResult = renameTempArtworkImage($artworkImage, $artist_id, $artworkId);
        if ($renameResult && $renameResult !== $artworkImage) {
            $finalImageName = $renameResult; // Use renamed file
            
            // Update database with final image name
            $updateImageSql = "UPDATE artworks SET artwork_image = ? WHERE artwork_id = ?";
            $updateStmt = $db->prepare($updateImageSql);
            if ($updateStmt) {
                $updateStmt->bind_param("si", $finalImageName, $artworkId);
                $updateStmt->execute();
                $updateStmt->close();
            }
        }
    }
    
    // Send success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Artwork published successfully!',
        'data' => [
            'artwork_id' => $artworkId,
            'title' => $title,
            'price' => $price,
            'dimensions' => $dimensions,
            'type' => $artworkType,
            'material' => $materialString,
            'artwork_image' => $finalImageName,
            'year' => $year
        ]
    ]);
    
} catch (Exception $e) {
    // Clean up any uploaded files if artwork creation failed
    if (isset($uploadedFiles) && !empty($uploadedFiles)) {
        cleanupUploadedFiles($uploadedFiles);
    }
    
    error_log("addArtwork error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while adding the artwork',
        'error' => $e->getMessage()
    ]);
} finally {
    // Close database connection if it exists
    if (isset($db) && !$db->connect_error) {
        $db->close();
    }
}
?>