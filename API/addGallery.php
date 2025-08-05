<?php
session_start();
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

// Use correct database connection variable
$conn = $db;

$artist_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 11;

// Function to validate input
function validateInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Function to validate phone
function validatePhone($phone) {
    return preg_match('/^[0-9+\-\s()]+$/', $phone);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Log received data for debugging
        error_log("Received POST data: " . print_r($_POST, true));
        
        // Validate required fields
        $errors = [];
        
        // Get and validate form data
        $title = validateInput($_POST['title'] ?? '');
        $description = validateInput($_POST['description'] ?? '');
        $gallery_type = validateInput($_POST['gallery_type'] ?? '');
        $price = validateInput($_POST['price'] ?? '0');
        $address = validateInput($_POST['address'] ?? '');
        $city = validateInput($_POST['city'] ?? '');
        $phone = validateInput($_POST['phone'] ?? '');
        $duration = validateInput($_POST['duration'] ?? '');
        $start_date = validateInput($_POST['start_date'] ?? '');
        
        // Validation checks
        if (empty($title)) {
            $errors[] = 'Gallery title is required';
        } elseif (strlen($title) < 3) {
            $errors[] = 'Gallery title must be at least 3 characters long';
        }
        
        if (empty($description)) {
            $errors[] = 'Gallery description is required';
        }
        
        if (empty($gallery_type) || !in_array($gallery_type, ['virtual', 'physical'])) {
            $errors[] = 'Please select a valid gallery type';
        }
        
        // Validate start date - required and must be in the future
        if (empty($start_date)) {
            $errors[] = 'Start date is required';
        } else {
            $start_timestamp = strtotime($start_date);
            $current_timestamp = time();
            
            if ($start_timestamp === false) {
                $errors[] = 'Invalid start date format';
            } elseif ($start_timestamp <= $current_timestamp) {
                $errors[] = 'Start date must be in the future';
            }
        }
        
        if ($gallery_type === 'physical') {
            if (empty($address)) {
                $errors[] = 'Address is required for physical galleries';
            }
            
            if (empty($city)) {
                $errors[] = 'City is required for physical galleries';
            }
            
            if (empty($phone)) {
                $errors[] = 'Phone number is required for physical galleries';
            } elseif (!validatePhone($phone)) {
                $errors[] = 'Please enter a valid phone number';
            }
        } else if ($gallery_type === 'virtual') {
            if (empty($duration)) {
                $errors[] = 'Duration is required for virtual galleries';
            } elseif (!is_numeric($duration) || $duration < 1) {
                $errors[] = 'Duration must be a valid positive number in minutes';
            } elseif ($duration > 120) {
                $errors[] = 'Duration cannot exceed 2 hours (120 minutes)';
            }
        }
        
        if (!empty($price)) {
            if (!is_numeric($price) || $price < 0) {
                $errors[] = 'Price must be a valid positive number';
            }
        }
        
        // If there are validation errors, return them
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
            exit;
        }
        
        // Store data as plain text (no encryption)
        $plain_address = !empty($address) ? $address : null;
        $plain_phone = !empty($phone) ? $phone : null;
        
        // Set is_active based on gallery type
        $is_active = ($gallery_type === 'virtual') ? 1 : 0;
        
        // Convert empty strings to appropriate values for database
        $price = empty($price) ? null : floatval($price);
        
        // Handle duration - set to 0 for physical galleries, actual value for virtual
        if ($gallery_type === 'virtual') {
            $duration = !empty($duration) ? intval($duration) : 7; // Default 7 days for virtual
        } else {
            $duration = 0; // Set to 0 for physical galleries
        }
        
        // Prepare SQL statement
        $sql = "INSERT INTO galleries (artist_id, title, description, gallery_type, price, address, city, phone, duration, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        
        // Bind parameters with correct types
        $stmt->bind_param(
            "isssdsssii",
            $artist_id,
            $title,
            $description,
            $gallery_type,
            $price,
            $plain_address,
            $city,
            $plain_phone,
            $duration,
            $is_active
        );
        
        // Execute the statement
        if ($stmt->execute()) {
            $gallery_id = $conn->insert_id;
            
            // Return success message based on gallery type
            if ($gallery_type === 'virtual') {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Gallery published successfully!',
                    'gallery_id' => $gallery_id
                ]);
            } else {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Gallery submitted successfully! An admin will contact you soon.',
                    'gallery_id' => $gallery_id
                ]);
            }
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Gallery API Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error adding gallery: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>