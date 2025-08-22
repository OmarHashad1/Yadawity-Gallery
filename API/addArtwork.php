<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

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

// File upload function for auction artwork images
function uploadAuctionImage($file, $artistId, $artworkId, $imageIndex, $uploadDir = null) {
    try {
        // Set default upload directory with correct path
        if ($uploadDir === null) {
            $uploadDir = dirname(__DIR__) . '/uploads/artworks/';
        }
        
        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return ['success' => false, 'message' => 'Failed to create upload directory: ' . $uploadDir];
            }
        }
        
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
        
        // Check file size (max 50MB for auction images)
        if ($file['size'] > 50 * 1024 * 1024) {
            return ['success' => false, 'message' => 'File size too large. Maximum 50MB allowed for auction images.'];
        }
        
        // Validate image dimensions (minimum 300x300 for auction images)
     
        
        
        // Generate unique filename
        $fileHash = md5_file($file['tmp_name']);
        $timestamp = time();
        $filename = "artist_{$artistId}_artwork_{$artworkId}_img_{$imageIndex}_{$timestamp}_{$fileHash}.{$fileExtension}";
        $targetPath = $uploadDir . $filename;
        
        // Move uploaded file - use copy for testing if move_uploaded_file fails
        $moveSuccess = false;
        if (is_uploaded_file($file['tmp_name'])) {
            // Real uploaded file
            $moveSuccess = move_uploaded_file($file['tmp_name'], $targetPath);
        } else {
            // For testing purposes - use copy instead
            $moveSuccess = copy($file['tmp_name'], $targetPath);
        }
        
        if (!$moveSuccess) {
            return ['success' => false, 'message' => 'Failed to move uploaded file.'];
        }
        
        // Verify the file was uploaded successfully
        if (!file_exists($targetPath)) {
            return ['success' => false, 'message' => 'File upload verification failed.'];
        }
        
        return [
            'success' => true,
            'filename' => $filename,
            'path' => $targetPath,
            'size' => $file['size']
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Upload error: ' . $e->getMessage()];
    }
}

// Cleanup function for uploaded files in case of error
function cleanupUploadedFiles($filenames, $uploadDir = '../uploads/artworks/') {
    foreach ($filenames as $filename) {
        $filePath = $uploadDir . $filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}

try {
    // Start transaction
    $db->autocommit(false);
    
    // Get form data
    $postData = $_POST;
    
    // Determine if this is auction or regular artwork based on field names
    $isAuction = isset($postData['start_date']) && isset($postData['end_date']);
    
    // Normalize field names - handle both auction and regular artwork formats
    if (!$isAuction) {
        // For regular artwork, map to expected field names
        if (isset($postData['title']) && !isset($postData['artwork_title'])) {
            $postData['artwork_title'] = $postData['title'];
        }
        if (isset($postData['price']) && !isset($postData['starting_bid'])) {
            $postData['starting_bid'] = $postData['price'];
        }
        if (isset($postData['style']) && !isset($postData['art_style'])) {
            $postData['art_style'] = $postData['style'];
        }
        if (isset($postData['material'])) {
            // Material is already in the correct field name
        }
    }
    
    // Validate required fields
    $errors = [];
    if ($isAuction) {
        // Auction required fields
        $requiredFields = [
            'artwork_title' => 'Artwork Title',
            'starting_bid' => 'Starting Bid',
            'art_style' => 'Art Style',
            'width' => 'Width',
            'height' => 'Height',
            'year' => 'Year',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'description' => 'Description'
        ];
    } else {
        // Regular artwork required fields
        $requiredFields = [
            'artwork_title' => 'Artwork Title',
            'starting_bid' => 'Price',
            'category' => 'Category',
            'description' => 'Description'
        ];
    }
    
    foreach ($requiredFields as $field => $label) {
        if (empty($postData[$field])) {
            $errors[] = "$label is required";
        }
    }
    
    // Validate numeric fields
    if (!empty($postData['starting_bid'])) {
        if (!is_numeric($postData['starting_bid']) || $postData['starting_bid'] <= 0) {
            $errors[] = 'Starting bid must be a positive number';
        }
    }
    
    if (!empty($postData['width'])) {
        if (!is_numeric($postData['width']) || $postData['width'] <= 0) {
            $errors[] = 'Width must be a positive number';
        }
    }
    
    if (!empty($postData['height'])) {
        if (!is_numeric($postData['height']) || $postData['height'] <= 0) {
            $errors[] = 'Height must be a positive number';
        }
    }
    
    if (!empty($postData['year'])) {
        $currentYear = date('Y');
        if (!is_numeric($postData['year']) || $postData['year'] < 1800 || $postData['year'] > $currentYear) {
            $errors[] = "Year must be between 1800 and $currentYear";
        }
    }
    
    // Validate dates (only for auctions)
    if ($isAuction) {
        if (!empty($postData['start_date'])) {
            $startDate = DateTime::createFromFormat('Y-m-d\TH:i', $postData['start_date']);
            if (!$startDate) {
                $errors[] = 'Invalid start date format';
            } else {
                $now = new DateTime();
                if ($startDate <= $now) {
                    $errors[] = 'Start date must be in the future';
                }
            }
        }
        
        if (!empty($postData['end_date'])) {
            $endDate = DateTime::createFromFormat('Y-m-d\TH:i', $postData['end_date']);
            if (!$endDate) {
                $errors[] = 'Invalid end date format';
            } else if (isset($startDate) && $endDate <= $startDate) {
                $errors[] = 'End date must be after start date';
            }
        }
        
        // Validate images are provided for auctions
        if (!isset($_FILES['auction_images']) || empty($_FILES['auction_images']['name'][0])) {
            $errors[] = 'At least one image is required for auction';
        }
    } else {
        // For regular artwork, check for artwork_image
        if (!isset($_FILES['artwork_image']) || $_FILES['artwork_image']['error'] !== UPLOAD_ERR_OK) {
            // Image is optional for regular artwork
        }
    }
    
    // Return validation errors if any
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors
        ]);
        exit;
    }
    
    // Get artist ID from session
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Authentication required. Please log in to add artwork.'
        ]);
        exit;
    }
    
    // Extract validated data
    $artist_id = (int)$_SESSION['user_id'];
    $artwork_title = $postData['artwork_title'];
    $starting_bid = floatval($postData['starting_bid']);
    
    // Handle optional fields with defaults
    $art_style = $postData['art_style'] ?? $postData['style'] ?? 'contemporary';
    $width = !empty($postData['width']) ? floatval($postData['width']) : null;
    $height = !empty($postData['height']) ? floatval($postData['height']) : null;
    $depth = !empty($postData['depth']) ? floatval($postData['depth']) : null;
    $year = !empty($postData['year']) ? intval($postData['year']) : date('Y');
    $description = $postData['description'];
    $material = $postData['material'] ?? $art_style;
    $category = $postData['category'] ?? 'painting';
    
    // Create dimensions string if width and height are provided
    $dimensions = '';
    if ($width && $height) {
        $dimensions = $width . 'cm × ' . $height . 'cm';
        if ($depth !== null) {
            $dimensions .= ' × ' . $depth . 'cm';
        }
    }
    
    // Only process auction-specific fields if this is an auction
    if ($isAuction) {
        $start_date = $postData['start_date'];
        $end_date = $postData['end_date'];
        
        // Format datetime strings for database
        $start_datetime = date('Y-m-d H:i:s', strtotime($start_date));
        $end_datetime = date('Y-m-d H:i:s', strtotime($end_date));
    }
    
    // Handle image uploads first to get the primary image path
    $primaryImagePath = null;
    $uploaded_images = [];
    $tempUploadedFiles = []; // Store temp file info for later processing
    
    // Check if primary image is uploaded
    if (isset($_FILES['primary_image']) && $_FILES['primary_image']['error'] === UPLOAD_ERR_OK) {
        // Store temp file info, don't upload yet
        $tempUploadedFiles['primary_image'] = $_FILES['primary_image'];
    }
    
    // Handle multiple images (auction_images[] for auctions, artwork_images[] for artworks)
    $imageField = $isAuction ? 'auction_images' : 'artwork_images';
    if (isset($_FILES[$imageField]) && is_array($_FILES[$imageField]['name'])) {
        $tempUploadedFiles['additional_images'] = [];
        for ($i = 0; $i < count($_FILES[$imageField]['name']); $i++) {
            if ($_FILES[$imageField]['error'][$i] === UPLOAD_ERR_OK) {
                $tempUploadedFiles['additional_images'][] = [
                    'name' => $_FILES[$imageField]['name'][$i],
                    'type' => $_FILES[$imageField]['type'][$i],
                    'tmp_name' => $_FILES[$imageField]['tmp_name'][$i],
                    'size' => $_FILES[$imageField]['size'][$i],
                    'error' => $_FILES[$imageField]['error'][$i]
                ];
            }
        }
    }
    
    // Check if single artwork_image is uploaded (from regular artwork form)
    if (isset($_FILES['artwork_image']) && $_FILES['artwork_image']['error'] === UPLOAD_ERR_OK) {
        if (!isset($tempUploadedFiles['primary_image'])) {
            $tempUploadedFiles['primary_image'] = $_FILES['artwork_image'];
        }
    }
    
    // Map art style to artwork type
    $typeMapping = [
        'abstract' => 'painting',
        'realism' => 'painting',
        'impressionism' => 'painting',
        'cubism' => 'painting',
        'expressionism' => 'painting',
        'surrealism' => 'painting',
        'pop-art' => 'painting',
        'minimalism' => 'mixed_media',
        'contemporary' => 'mixed_media',
        'sculpture' => 'sculpture',
        'photography' => 'photography',
        'digital' => 'digital'
    ];
    $artworkType = isset($typeMapping[$art_style]) ? $typeMapping[$art_style] : 'painting';
    
    // Insert artwork into artworks table
    if ($isAuction) {
        // For auctions, set on_auction = 1
        $artworkSql = "INSERT INTO artworks (
            artist_id, title, description, price, dimensions, year, 
            material, artwork_image, type, is_available, on_auction
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1)";
    } else {
        // For regular artwork, set on_auction = 0
        $artworkSql = "INSERT INTO artworks (
            artist_id, title, description, price, dimensions, year, 
            material, artwork_image, type, is_available, on_auction
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0)";
    }
    
    $artworkStmt = $db->prepare($artworkSql);
    if (!$artworkStmt) {
        throw new Exception("Failed to prepare artwork statement: " . $db->error);
    }
    
    // Handle dimensions - use null if empty
    $dimensionsForDb = !empty($dimensions) ? $dimensions : null;
    $tempPrimaryImagePath = null; // Will be updated after processing images
    
    $artworkStmt->bind_param(
        "issdsisss",
        $artist_id,
        $artwork_title,
        $description,
        $starting_bid, // Use starting bid as price
        $dimensionsForDb,
        $year,
        $material,
        $tempPrimaryImagePath, // Will be null initially
        $artworkType
    );
    
    if (!$artworkStmt->execute()) {
        throw new Exception("Failed to insert artwork: " . $artworkStmt->error);
    }
    
    $artwork_id = $db->insert_id;
    $artworkStmt->close();
    
    // Now process the uploaded images with the correct artwork_id
    $primaryImagePath = null;
    $uploaded_images = [];
    $totalUploadedImages = [];
    $uploadErrors = [];
    
    // Process primary image if uploaded
    if (isset($tempUploadedFiles['primary_image'])) {
        $upload_result = uploadAuctionImage($tempUploadedFiles['primary_image'], $artist_id, $artwork_id, 0);
        if ($upload_result['success']) {
            $primaryImagePath = $upload_result['filename'];
        } else {
            throw new Exception('Primary image upload failed: ' . $upload_result['message']);
        }
    }
    
    // Process additional images if uploaded
    if (isset($tempUploadedFiles['additional_images']) && !empty($tempUploadedFiles['additional_images'])) {
        foreach ($tempUploadedFiles['additional_images'] as $index => $file) {
            $upload_result = uploadAuctionImage($file, $artist_id, $artwork_id, $index + 1);
            if ($upload_result['success']) {
                $uploaded_images[] = [
                    'path' => $upload_result['filename'],
                    'is_primary' => 0 // Additional images are not primary
                ];
            } else {
                $uploadErrors[] = "Failed to upload image " . ($index + 1) . ": " . $upload_result['message'];
            }
        }
    }
    
    // If no primary image was uploaded but we have additional images, use the first one as primary
    if ($primaryImagePath === null && !empty($uploaded_images)) {
        $primaryImagePath = $uploaded_images[0]['path'];
        $uploaded_images[0]['is_primary'] = 1;
    }
    
    // Update the artwork record with the primary image path if we have one
    if ($primaryImagePath !== null) {
        $updateImageSql = "UPDATE artworks SET artwork_image = ? WHERE artwork_id = ?";
        $updateStmt = $db->prepare($updateImageSql);
        if ($updateStmt) {
            $updateStmt->bind_param("si", $primaryImagePath, $artwork_id);
            $updateStmt->execute();
            $updateStmt->close();
        }
    }
    
    // Only create auction if this is an auction
    if ($isAuction) {
        // Insert auction into auctions table
        $auctionSql = "INSERT INTO auctions (
            product_id, artist_id, starting_bid, current_bid, 
            start_time, end_time, status
        ) VALUES (?, ?, ?, ?, ?, ?, 'active')";
        
        $auctionStmt = $db->prepare($auctionSql);
        if (!$auctionStmt) {
            throw new Exception("Failed to prepare auction statement: " . $db->error);
        }
        
        $auctionStmt->bind_param(
            "iiddss",
            $artwork_id,    // This references artwork_id from artworks table
            $artist_id,     // Include artist_id as required by the table
            $starting_bid,
            $starting_bid,  // current_bid starts as starting_bid
            $start_datetime,
            $end_datetime
        );
        
        if (!$auctionStmt->execute()) {
            throw new Exception("Failed to insert auction: " . $auctionStmt->error);
        }
        
        $auction_id = $db->insert_id;
        $auctionStmt->close();
    } else {
        $auction_id = null; // No auction created for regular artwork
    }
    
    // Handle image uploads and store in artwork_photos table
    // Variables already initialized after artwork insertion
    
    // Insert primary image into artwork_photos table if exists
    if ($primaryImagePath !== null) {
        $photoSql = "INSERT INTO artwork_photos (artwork_id, image_path, is_primary) VALUES (?, ?, 1)";
        $photoStmt = $db->prepare($photoSql);
        
        if ($photoStmt) {
            $photoStmt->bind_param("is", $artwork_id, $primaryImagePath);
            if (!$photoStmt->execute()) {
                throw new Exception("Failed to insert primary photo record: " . $photoStmt->error);
            }
            $photoStmt->close();
            $totalUploadedImages[] = $primaryImagePath;
        } else {
            throw new Exception("Failed to prepare primary photo statement: " . $db->error);
        }
    }
    
    // Insert additional images into artwork_photos table
    if (!empty($uploaded_images)) {
        $photoSql = "INSERT INTO artwork_photos (artwork_id, image_path, is_primary) VALUES (?, ?, ?)";
        $photoStmt = $db->prepare($photoSql);
        
        if ($photoStmt) {
            foreach ($uploaded_images as $image) {
                // Only add if it's not the same as the primary image
                if ($image['path'] !== $primaryImagePath) {
                    $photoStmt->bind_param("isi", $artwork_id, $image['path'], $image['is_primary']);
                    if (!$photoStmt->execute()) {
                        throw new Exception("Failed to insert photo record: " . $photoStmt->error);
                    }
                    $totalUploadedImages[] = $image['path'];
                }
            }
            $photoStmt->close();
        } else {
            throw new Exception("Failed to prepare photo statement: " . $db->error);
        }
    }
    
    // For auctions, at least one image is required
    if ($isAuction && empty($totalUploadedImages)) {
        throw new Exception("No images were successfully uploaded for auction.");
    }
    
    // Commit transaction
    $db->commit();
    
    // Success response
    http_response_code(200);
    if ($isAuction) {
        echo json_encode([
            'success' => true,
            'message' => 'Auction created successfully!',
            'data' => [
                'artwork_id' => $artwork_id,
                'auction_id' => $auction_id,
                'artwork_title' => $artwork_title,
                'starting_bid' => $starting_bid,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'images_uploaded' => count($totalUploadedImages),
                'image_files' => $totalUploadedImages
            ],
            'warnings' => !empty($uploadErrors) ? $uploadErrors : null
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Artwork added successfully!',
            'data' => [
                'artwork_id' => $artwork_id,
                'artwork_title' => $artwork_title,
                'price' => $starting_bid,
                'category' => $category,
                'images_uploaded' => count($totalUploadedImages),
                'image_files' => $totalUploadedImages
            ],
            'warnings' => !empty($uploadErrors) ? $uploadErrors : null
        ]);
    }
    
} catch (Exception $e) {
    // Rollback transaction
    $db->rollback();
    
    // Clean up any uploaded files
    if (!empty($totalUploadedImages)) {
        cleanupUploadedFiles($totalUploadedImages);
    }
    
    error_log("Auction creation error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to create auction: ' . $e->getMessage(),
        'error_code' => 'AUCTION_CREATION_FAILED'
    ]);
} finally {
    // Reset autocommit
    $db->autocommit(true);
}
?>
