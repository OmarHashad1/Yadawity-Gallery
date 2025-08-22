<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

require_once "db.php";

// Use correct database connection variable
$conn = $db;

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

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required. Please log in to create an auction.'
    ]);
    exit;
}

$artist_id = (int)$_SESSION['user_id'];

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
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return ['success' => false, 'message' => 'Invalid image file or corrupted image.'];
        }
        
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        
        if ($width < 300 || $height < 300) {
            return ['success' => false, 'message' => "Image dimensions too small. Minimum required: 300x300 pixels. Current: {$width}x{$height} pixels."];
        }
        
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
    
    // Validate required fields
    $errors = [];
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
    
    // Validate dates
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
    
    // Validate images are provided
    if (!isset($_FILES['auction_images']) || empty($_FILES['auction_images']['name'][0])) {
        $errors[] = 'At least one image is required for auction';
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
    
    // Extract validated data
    $artwork_title = $postData['artwork_title'];
    $starting_bid = floatval($postData['starting_bid']);
    $art_style = $postData['art_style'];
    $width = floatval($postData['width']);
    $height = floatval($postData['height']);
    $depth = !empty($postData['depth']) ? floatval($postData['depth']) : null;
    $year = intval($postData['year']);
    $start_date = $postData['start_date'];
    $end_date = $postData['end_date'];
    $description = $postData['description'];
    
    // Create dimensions string
    $dimensions = $width . 'cm × ' . $height . 'cm';
    if ($depth !== null) {
        $dimensions .= ' × ' . $depth . 'cm';
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
    
    // Format datetime strings for database
    $start_datetime = date('Y-m-d H:i:s', strtotime($start_date));
    $end_datetime = date('Y-m-d H:i:s', strtotime($end_date));
    
    // Insert artwork into artworks table with on_auction = 1
    $artworkSql = "INSERT INTO artworks (
        artist_id, title, description, price, dimensions, year, 
        material, type, is_available, on_auction
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, 1)";
    
    $artworkStmt = $db->prepare($artworkSql);
    if (!$artworkStmt) {
        throw new Exception("Failed to prepare artwork statement: " . $db->error);
    }
    
    $artworkStmt->bind_param(
        "issdsiss",
        $artist_id,
        $artwork_title,
        $description,
        $starting_bid, // Use starting bid as initial price
        $dimensions,
        $year,
        $art_style,
        $artworkType
    );
    
    if (!$artworkStmt->execute()) {
        throw new Exception("Failed to insert artwork: " . $artworkStmt->error);
    }
    
    $artwork_id = $db->insert_id; // This will be the artwork_id from the artworks table
    $artworkStmt->close();
    
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
    
    // Handle multiple image uploads
    $uploadedImages = [];
    $uploadErrors = [];
    
    if (isset($_FILES['auction_images'])) {
        $fileCount = count($_FILES['auction_images']['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            // Skip empty file slots
            if (empty($_FILES['auction_images']['name'][$i])) {
                continue;
            }
            
            // Create file array for individual image
            $file = [
                'name' => $_FILES['auction_images']['name'][$i],
                'type' => $_FILES['auction_images']['type'][$i],
                'tmp_name' => $_FILES['auction_images']['tmp_name'][$i],
                'error' => $_FILES['auction_images']['error'][$i],
                'size' => $_FILES['auction_images']['size'][$i]
            ];
            
            // Skip files with upload errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $uploadErrors[] = "Upload error for image " . ($i + 1) . ": " . $file['name'];
                continue;
            }
            
            // Upload the image
            $uploadResult = uploadAuctionImage($file, $artist_id, $artwork_id, $i + 1);
            
            if ($uploadResult['success']) {
                $uploadedImages[] = $uploadResult['filename'];
                
                // Insert into artwork_photos table
                $isPrimary = ($i === 0) ? 1 : 0; // First image is primary
                $photoSql = "INSERT INTO artwork_photos (artwork_id, image_path, is_primary) VALUES (?, ?, ?)";
                $photoStmt = $db->prepare($photoSql);
                
                if ($photoStmt) {
                    $photoStmt->bind_param("isi", $artwork_id, $uploadResult['filename'], $isPrimary);
                    if (!$photoStmt->execute()) {
                        throw new Exception("Failed to insert photo record: " . $photoStmt->error);
                    }
                    $photoStmt->close();
                } else {
                    throw new Exception("Failed to prepare photo statement: " . $db->error);
                }
            } else {
                $uploadErrors[] = "Failed to upload image " . ($i + 1) . ": " . $uploadResult['message'];
            }
        }
    }
    
    // Check if we have at least one successful upload
    if (empty($uploadedImages)) {
        throw new Exception("No images were successfully uploaded. Errors: " . implode(', ', $uploadErrors));
    }
    
    // Commit transaction
    $db->commit();
    
    // Success response
    http_response_code(200);
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
            'images_uploaded' => count($uploadedImages),
            'image_files' => $uploadedImages
        ],
        'warnings' => !empty($uploadErrors) ? $uploadErrors : null
    ]);
    
} catch (Exception $e) {
    // Rollback transaction
    $db->rollback();
    
    // Clean up any uploaded files
    if (!empty($uploadedImages)) {
        cleanupUploadedFiles($uploadedImages);
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
