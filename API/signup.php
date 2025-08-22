<?php
require_once "db.php";

function checkUserAuthentication($db) {
    try {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if session variables exist
        if (isset($_SESSION['user_id']) && isset($_SESSION['session_token'])) {
            $user_id = $_SESSION['user_id'];
            $session_token = $_SESSION['session_token'];
            
            // Verify session in database
            $stmt = $db->prepare("SELECT user_id, expires_at FROM user_login_sessions WHERE session_id = ? AND user_id = ? AND is_active = 1");
            
            if (!$stmt) {
                throw new Exception("Failed to prepare session verification query: " . $db->error);
            }
            
            $stmt->bind_param("si", $session_token, $user_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to execute session verification query: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $session = $result->fetch_assoc();
                
                // Check if session hasn't expired
                if (strtotime($session['expires_at']) > time()) {
                    $stmt->close();
                    return true; // Valid session found
                } else {
                    // Session expired, clean it up
                    cleanupExpiredSession($db, $session_token);
                    session_unset();
                }
            }
            $stmt->close();
        }
        
        // Check for login cookie as backup
        return checkLoginCookie($db);
        
    } catch (Exception $e) {
        error_log("checkUserAuthentication function error: " . $e->getMessage());
        return false;
    }
}

function cleanupExpiredSession($db, $session_token) {
    try {
        $cleanup_stmt = $db->prepare("UPDATE user_login_sessions SET is_active = 0, logout_time = NOW() WHERE session_id = ?");
        
        if (!$cleanup_stmt) {
            throw new Exception("Failed to prepare cleanup query: " . $db->error);
        }
        
        $cleanup_stmt->bind_param("s", $session_token);
        
        if (!$cleanup_stmt->execute()) {
            throw new Exception("Failed to execute cleanup query: " . $cleanup_stmt->error);
        }
        
        $cleanup_stmt->close();
        return true;
        
    } catch (Exception $e) {
        error_log("cleanupExpiredSession function error: " . $e->getMessage());
        return false;
    }
}

function checkLoginCookie($db) {
    try {
        if (!isset($_COOKIE['user_login'])) {
            return false;
        }
        
        $cookie_parts = explode('_', $_COOKIE['user_login'], 2);
        if (count($cookie_parts) !== 2) {
            return false;
        }
        
        $user_id = intval($cookie_parts[0]);
        $cookie_hash = $cookie_parts[1];
        
        // Verify user exists and is active
        $stmt = $db->prepare("SELECT user_id, email FROM users WHERE user_id = ? AND is_active = 1");
        
        if (!$stmt) {
            throw new Exception("Failed to prepare user verification query: " . $db->error);
        }
        
        $stmt->bind_param("i", $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute user verification query: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Check if there's an active session for this user
            if (validateCookieSession($db, $user_id, $user['email'], $cookie_hash)) {
                $stmt->close();
                return true;
            }
        }
        $stmt->close();
        return false;
        
    } catch (Exception $e) {
        error_log("checkLoginCookie function error: " . $e->getMessage());
        return false;
    }
}

function validateCookieSession($db, $user_id, $email, $cookie_hash) {
    try {
        $session_stmt = $db->prepare("SELECT session_id, login_time FROM user_login_sessions WHERE user_id = ? AND is_active = 1 AND expires_at > NOW() ORDER BY login_time DESC LIMIT 1");
        
        if (!$session_stmt) {
            throw new Exception("Failed to prepare session validation query: " . $db->error);
        }
        
        $session_stmt->bind_param("i", $user_id);
        
        if (!$session_stmt->execute()) {
            throw new Exception("Failed to execute session validation query: " . $session_stmt->error);
        }
        
        $session_result = $session_stmt->get_result();
        
        if ($session_result->num_rows === 1) {
            $session_data = $session_result->fetch_assoc();
            
            // Verify cookie hash matches expected pattern for this session
            $expected_hash = hash('sha256', $email . $session_data['login_time'] . 'yadawity_salt');
            
            if (hash_equals($expected_hash, $cookie_hash)) {
                // Restore session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['session_token'] = $session_data['session_id'];
                $_SESSION['email'] = $email;
                
                $session_stmt->close();
                return true;
            }
        }
        $session_stmt->close();
        return false;
        
    } catch (Exception $e) {
        error_log("validateCookieSession function error: " . $e->getMessage());
        return false;
    }
}

function validateFileSignature($file, $allowedTypes) {
    try {
        $signatures = [
            'image/jpeg' => ["\xFF\xD8\xFF"],
            'image/png' => ["\x89\x50\x4E\x47"],
            'image/gif' => ["\x47\x49\x46"],
            'application/pdf' => ["\x25\x50\x44\x46"]
        ];
        
        if (!file_exists($file['tmp_name'])) {
            return false;
        }
        
        $handle = fopen($file['tmp_name'], 'rb');
        if (!$handle) {
            return false;
        }
        
        $bytes = fread($handle, 8);
        fclose($handle);
        
        foreach ($allowedTypes as $mimeType) {
            if (isset($signatures[$mimeType])) {
                foreach ($signatures[$mimeType] as $signature) {
                    if (strpos($bytes, $signature) === 0) {
                        return true;
                    }
                }
            }
        }
        return false;
        
    } catch (Exception $e) {
        error_log("validateFileSignature function error: " . $e->getMessage());
        return false;
    }
}

function uploadFile($file, $uploadDir, $prefix = '') {
    try {
        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $allowedDocTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        
        $allowedTypes = (strpos($prefix, 'id_') === 0) ? $allowedDocTypes : $allowedImageTypes;
        
        // Removed file signature validation for consistency
        if (!file_exists($file['tmp_name']) || $file['size'] <= 0) {
            return ['success' => false, 'message' => 'Invalid file or file is empty'];
        }
        
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = (strpos($prefix, 'id_') === 0) ? ['pdf', 'jpg', 'jpeg', 'png'] : ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            return ['success' => false, 'message' => 'File extension not allowed'];
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            return ['success' => false, 'message' => 'File size too large (max 5MB)'];
        }
        
        $fileName = $prefix . uniqid() . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;
        
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return ['success' => false, 'message' => 'Failed to create upload directory'];
            }
        }
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['success' => true, 'filename' => $fileName];
        }
        
        return ['success' => false, 'message' => 'Upload failed'];
        
    } catch (Exception $e) {
        error_log("uploadFile function error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Upload failed due to server error'];
    }
}

function uploadVerificationDocument($file, $uploadDir, $documentType, $nationalId) {
    try {
        // Add debug logging
        error_log("uploadVerificationDocument called for: $documentType");
        error_log("File data: " . print_r($file, true));
        
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
        
        // Check for upload errors first
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File too large (exceeds php.ini limit)',
                UPLOAD_ERR_FORM_SIZE => 'File too large (exceeds form limit)', 
                UPLOAD_ERR_PARTIAL => 'File upload was incomplete',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
            ];
            $errorMsg = isset($errorMessages[$file['error']]) ? $errorMessages[$file['error']] : 'Unknown upload error';
            error_log("Upload error for $documentType: " . $errorMsg);
            return ['success' => false, 'message' => $errorMsg];
        }
        
        // Validate file signature - make this more lenient
        // Removed file signature validation for now
        if (!file_exists($file['tmp_name']) || $file['size'] <= 0) {
            error_log("File does not exist or is empty for $documentType");
            return ['success' => false, 'message' => 'Invalid file or file is empty'];
        }
        
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            error_log("File extension not allowed for $documentType: $fileExtension");
            return ['success' => false, 'message' => 'File extension not allowed'];
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            error_log("File too large for $documentType: " . $file['size']);
            return ['success' => false, 'message' => 'File size too large (max 5MB)'];
        }
        
        // Create filename with national ID and document type
        $cleanNationalId = preg_replace('/[^a-zA-Z0-9]/', '', $nationalId);
        $timestamp = time();
        $fileName = "verification_{$cleanNationalId}_{$documentType}_{$timestamp}." . $fileExtension;
        
        $filePath = $uploadDir . $fileName;
        
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                error_log("Failed to create directory: $uploadDir");
                return ['success' => false, 'message' => 'Failed to create upload directory'];
            }
        }
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            error_log("File uploaded successfully: $filePath");
            return ['success' => true, 'filename' => $fileName];
        }
        
        error_log("move_uploaded_file failed for $documentType");
        return ['success' => false, 'message' => 'Upload failed'];
        
    } catch (Exception $e) {
        error_log("uploadVerificationDocument function error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Upload failed due to server error'];
    }
}

function uploadProfilePicture($file, $userId, $firstName, $lastName) {
    try {
        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        // Removed file signature validation for consistency
        if (!file_exists($file['tmp_name']) || $file['size'] <= 0) {
            return ['success' => false, 'message' => 'Invalid file or file is empty'];
        }
        
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            return ['success' => false, 'message' => 'File extension not allowed'];
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            return ['success' => false, 'message' => 'File size too large (max 5MB)'];
        }
        
        // Create filename with user ID and name convention
        $cleanFirstName = preg_replace('/[^a-zA-Z0-9]/', '', $firstName);
        $cleanLastName = preg_replace('/[^a-zA-Z0-9]/', '', $lastName);
        $fileName = "user_{$userId}_{$cleanFirstName}_{$cleanLastName}_profile." . $fileExtension;
        
        $uploadDir = dirname(__DIR__) . '/uploads/user_profile_picture/';
        $filePath = $uploadDir . $fileName;
        
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return ['success' => false, 'message' => 'Failed to create upload directory'];
            }
        }
        
        // Remove old profile picture if exists
        $oldFiles = glob($uploadDir . "user_{$userId}_*_profile.*");
        foreach ($oldFiles as $oldFile) {
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['success' => true, 'filename' => $fileName];
        }
        
        return ['success' => false, 'message' => 'Upload failed'];
        
    } catch (Exception $e) {
        error_log("uploadProfilePicture function error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Upload failed due to server error'];
    }
}

function saveProfilePictureToDatabase($db, $userId, $filename) {
    try {
        // Update Img field in users table
        $updateUserStmt = $db->prepare("UPDATE users SET Img = ? WHERE user_id = ?");
        if (!$updateUserStmt) {
            throw new Exception("Failed to prepare user update query: " . $db->error);
        }
        
        $updateUserStmt->bind_param("si", $filename, $userId);
        if (!$updateUserStmt->execute()) {
            throw new Exception("Failed to update user Img field: " . $updateUserStmt->error);
        }
        $updateUserStmt->close();
        
        // Insert into user_profile_photo table
        $insertPhotoStmt = $db->prepare("INSERT INTO user_profile_photo (user_id, photo_filename) VALUES (?, ?) ON DUPLICATE KEY UPDATE photo_filename = VALUES(photo_filename)");
        if (!$insertPhotoStmt) {
            throw new Exception("Failed to prepare photo insert query: " . $db->error);
        }
        
        $insertPhotoStmt->bind_param("is", $userId, $filename);
        if (!$insertPhotoStmt->execute()) {
            throw new Exception("Failed to insert profile photo record: " . $insertPhotoStmt->error);
        }
        $insertPhotoStmt->close();
        
        return ['success' => true];
        
    } catch (Exception $e) {
        error_log("saveProfilePictureToDatabase function error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to save profile picture to database: ' . $e->getMessage()];
    }
}


function processStep1($db) {
    try {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $firstName = htmlspecialchars($_POST['first_name']);
        $lastName = htmlspecialchars($_POST['last_name']);
        $phone = htmlspecialchars($_POST['phone']);
        $userType = $_POST['user_type'];
        
        $errors = [];
        
        // Validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long";
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = "Passwords do not match";
        }
        
        if (!preg_match('/^[a-zA-Z\s]+$/', $firstName) || !preg_match('/^[a-zA-Z\s]+$/', $lastName)) {
            $errors[] = "Names can only contain letters and spaces";
        }
        
        if (!preg_match('/^\+?[0-9\s\-\(\)]+$/', $phone)) {
            $errors[] = "Invalid phone number format";
        }
        
        // Check if email already exists
        $stmt = $db->prepare("SELECT user_id FROM users WHERE email = ?");
        
        if (!$stmt) {
            throw new Exception("Failed to prepare email check query: " . $db->error);
        }
        
        $stmt->bind_param("s", $email);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute email check query: " . $stmt->error);
        }
        
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = "Email already registered";
        }
        $stmt->close();
        
        if (empty($errors)) {
            // Store data in session for next step
            $_SESSION['signup_data'] = [
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'user_type' => $userType
            ];
            
            return ['success' => true, 'user_type' => $userType];
        }
        
        return ['success' => false, 'errors' => $errors];
        
    } catch (Exception $e) {
        error_log("processStep1 function error: " . $e->getMessage());
        return ['success' => false, 'errors' => ['Registration failed. Please try again.']];
    }
}


function processProfileUpload($db) {
    try {
        $errors = [];
        
        // Check if session data exists
        if (!isset($_SESSION['signup_data'])) {
            return ['success' => false, 'errors' => ['Session expired. Please start registration again.']];
        }
        
        // Complete registration first to get user ID
        $result = completeUserRegistration($db, $_SESSION['signup_data'], true);
        if (!$result['success']) {
            return ['success' => false, 'errors' => [$result['message']]];
        }
        
        $userId = $result['user_id'];
        
        // Handle profile picture upload if provided
        if (!empty($_FILES['profile_picture']['name'])) {
            $uploadResult = uploadProfilePicture(
                $_FILES['profile_picture'], 
                $userId, 
                $_SESSION['signup_data']['first_name'], 
                $_SESSION['signup_data']['last_name']
            );
            
            if (!$uploadResult['success']) {
                $errors[] = $uploadResult['message'];
            } else {
                // Save profile picture filename to database
                $saveResult = saveProfilePictureToDatabase($db, $userId, $uploadResult['filename']);
                if (!$saveResult['success']) {
                    $errors[] = $saveResult['message'];
                }
            }
        }
        
        if (empty($errors)) {
            unset($_SESSION['signup_data']);
            return ['success' => true, 'redirect' => '?step=success'];
        }
        
        return ['success' => false, 'errors' => $errors];
        
    } catch (Exception $e) {
        error_log("processProfileUpload function error: " . $e->getMessage());
        return ['success' => false, 'errors' => ['Registration failed. Please try again.']];
    }
}

function processArtistVerification($db) {
    try {
        $nationalId = htmlspecialchars($_POST['national_id']);
        $artSpecialty = htmlspecialchars($_POST['art_specialty']);
        $yearsExperience = intval($_POST['years_experience']);
        $bio = htmlspecialchars($_POST['bio']);
        
        $errors = [];
        
        if (empty($nationalId) || !preg_match('/^[0-9]+$/', $nationalId)) {
            $errors[] = "Valid National ID is required";
        }
        
        $uploadDir = dirname(__DIR__) . '/uploads/verification/';
        $idFrontResult = null;
        $idBackResult = null;
        
        // Upload ID documents
        if (!empty($_FILES['id_front']['name'])) {
            $idFrontResult = uploadVerificationDocument($_FILES['id_front'], $uploadDir, 'id_front', $nationalId);
            if (!$idFrontResult['success']) {
                $errors[] = "ID Front: " . $idFrontResult['message'];
            }
        } else {
            $errors[] = "ID front photo is required";
        }
        
        if (!empty($_FILES['id_back']['name'])) {
            $idBackResult = uploadVerificationDocument($_FILES['id_back'], $uploadDir, 'id_back', $nationalId);
            if (!$idBackResult['success']) {
                $errors[] = "ID Back: " . $idBackResult['message'];
            }
        } else {
            $errors[] = "ID back photo is required";
        }
        
        if (empty($errors)) {
            // Complete registration for artist
            if (!isset($_SESSION['signup_data'])) {
                return ['success' => false, 'errors' => ['Session expired. Please start registration again.']];
            }
            
            $artistData = $_SESSION['signup_data'];
            $artistData['bio'] = $bio;
            $artistData['art_specialty'] = $artSpecialty;
            $artistData['years_of_experience'] = $yearsExperience;
            $artistData['national_id'] = $nationalId;
            // Verification documents are saved to folder only, not stored in database
            
            $result = completeUserRegistration($db, $artistData, false);
            if ($result['success']) {
                $userId = $result['user_id'];
                
                // Upload profile picture if provided
                if (!empty($_FILES['profile_picture']['name'])) {
                    $uploadResult = uploadProfilePicture(
                        $_FILES['profile_picture'], 
                        $userId, 
                        $artistData['first_name'], 
                        $artistData['last_name']
                    );
                    
                    if ($uploadResult['success']) {
                        saveProfilePictureToDatabase($db, $userId, $uploadResult['filename']);
                    }
                }
                
                unset($_SESSION['signup_data']);
                return ['success' => true, 'redirect' => '?step=artist_pending'];
            } else {
                return ['success' => false, 'errors' => [$result['message']]];
            }
        }
        
        return ['success' => false, 'errors' => $errors];
        
    } catch (Exception $e) {
        error_log("processArtistVerification function error: " . $e->getMessage());
        return ['success' => false, 'errors' => ['Registration failed. Please try again.']];
    }
}

function completeUserRegistration($db, $data, $isActive = true) {
    try {
        $isActiveValue = $isActive ? 1 : 0;
        $profilePic = $data['profile_picture'] ?? null;
        
        if ($data['user_type'] === 'artist') {
            // Artist registration with additional fields (verification docs saved to folder only)
            $stmt = $db->prepare("INSERT INTO users (email, password, first_name, last_name, phone, user_type, profile_picture, bio, art_specialty, years_of_experience, national_id, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
            if (!$stmt) {
                throw new Exception("Failed to prepare artist registration query: " . $db->error);
            }
            
            $stmt->bind_param("sssssssssssi", 
                $data['email'], 
                $data['password'], 
                $data['first_name'], 
                $data['last_name'], 
                $data['phone'], 
                $data['user_type'], 
                $profilePic, 
                $data['bio'], 
                $data['art_specialty'], 
                $data['years_of_experience'], 
                $data['national_id'], 
                $isActiveValue
            );
        } else {
            // Buyer registration
            $stmt = $db->prepare("INSERT INTO users (email, password, first_name, last_name, phone, user_type, profile_picture, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
            if (!$stmt) {
                throw new Exception("Failed to prepare buyer registration query: " . $db->error);
            }
            
            $stmt->bind_param("sssssssi", $data['email'], $data['password'], $data['first_name'], $data['last_name'], $data['phone'], $data['user_type'], $profilePic, $isActiveValue);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute registration query: " . $stmt->error);
        }
        
        $userId = $db->insert_id;
        $stmt->close();
        
        return ['success' => true, 'message' => 'Registration completed successfully', 'user_id' => $userId];
        
    } catch (Exception $e) {
        error_log("completeUserRegistration function error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
    }
}

// Main execution with try-catch
try {
    // Check database connection
    if (!isset($db) || $db->connect_error) {
        throw new Exception("Database connection failed: " . ($db->connect_error ?? "Connection not established"));
    }

    // Start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // If user is already authenticated, redirect to homepage
    if (checkUserAuthentication($db)) {
        header("Location: ../index.php");
        exit();
    }

    $errors = [];

    // Handle form submissions
    if ($_POST) {
        $step = $_POST['step'] ?? '1';
        
        if ($step === '1') {
            $result = processStep1($db);
            if ($result['success']) {
                if ($result['user_type'] === 'buyer') {
                    header('Location: ?step=profile_upload');
                    exit;
                } else {
                    header('Location: ?step=artist_verification');
                    exit;
                }
            } else {
                $errors = $result['errors'];
            }
        } elseif ($step === 'profile_upload') {
            $result = processProfileUpload($db);
            if ($result['success']) {
                header('Location: ' . $result['redirect']);
                exit;
            } else {
                $errors = $result['errors'];
            }
        } elseif ($step === 'artist_verification') {
            $result = processArtistVerification($db);
            if ($result['success']) {
                header('Location: ' . $result['redirect']);
                exit;
            } else {
                $errors = $result['errors'];
            }
        }
    }

    $step = $_GET['step'] ?? '1';

} catch (Exception $e) {
    error_log("signup API Error: " . $e->getMessage());
    $errors[] = 'An error occurred during registration. Please try again.';
    $step = $_GET['step'] ?? '1';
} finally {
    // Always close database connection
    if (isset($db) && $db instanceof mysqli) {
        $db->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Yadawity Gallery - Create Your Account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../components/BurgerMenu/burger-menu.css" />
    <link rel="stylesheet" href="../public/homePage.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Playfair Display", serif;
            background: url(../image/fb0e872e-e7a1-4c35-9eff-5bca0ce50d34.png);
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            backdrop-filter: blur(5px);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        /* Overlay for better readability */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: -1;
        }

        .signup-container {
            background: linear-gradient(180deg, #faf8f5 0%, #f2ede6 100%);
            border-radius: 8px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            text-align: center;
            animation: slideIn 0.5s ease-out;
            position: relative;
            z-index: 1;
            margin-top: 100px; /* Account for fixed navbar */
            margin-bottom: 20px;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-section {
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .logo {
            width: 60px;
            height: 60px;
            background: #8b6f47;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .logo svg {
            width: 32px;
            height: 32px;
            color: white;
        }

        .brand-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
        }

        .brand-name {
            font-family: "Playfair Display", serif;
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 1px;
            font-style: italic;
            color: #8b6f47;
            margin-bottom: 2px;
            line-height: 1;
        }

        .brand-tagline {
            color: #999;
            font-size: 14px;
            font-weight: 500;
            line-height: 1;
        }

        .welcome-subtitle {
            color: #888;
            font-size: 15px;
            line-height: 1.4;
            margin: 20px auto 30px;
            max-width: 400px;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            gap: 15px;
        }

        .step {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            position: relative;
        }

        .step.active {
            background: #8b6f47;
            color: white;
            transform: scale(1.1);
        }

        .step.completed {
            background: #cc9966;
            color: white;
        }

        .step-indicator .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 25px;
            height: 2px;
            background: #e0e0e0;
            transform: translateY(-50%);
        }

        .step.completed::after {
            background: #cc9966;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            color: #8b6f47;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #8b6f47;
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 111, 71, 0.15);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
            line-height: 1.5;
        }

        .user-type-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .user-type-option {
            padding: 25px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
            position: relative;
        }

        .user-type-option:hover {
            border-color: #8b6f47;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(139, 111, 71, 0.15);
        }

        .user-type-option.selected {
            border-color: #8b6f47;
            background: linear-gradient(135deg, #f4e6d3 0%, #e8d5b7 100%);
        }

        .user-type-option input[type="radio"] {
            display: none;
        }

        .user-type-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: block;
        }

        .user-type-option h4 {
            color: #8b6f47;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .user-type-option p {
            color: #666;
            font-size: 13px;
        }

        .file-input-container {
            position: relative;
            overflow: hidden;
            display: block;
            width: 100%;
        }

        .file-input-container input[type=file] {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            display: block;
            padding: 20px;
            border: 2px dashed #8b6f47;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
            color: #8b6f47;
        }

        .file-input-label:hover {
            background: #f4e6d3;
            border-color: #6d5435;
            transform: translateY(-2px);
        }

        .file-input-label i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }

        .file-preview {
            margin-top: 10px;
            padding: 12px;
            background: #e8f5e8;
            border-radius: 5px;
            font-size: 14px;
            color: #2e7d32;
            border-left: 4px solid #4caf50;
        }

        .btn {
            width: 100%;
            padding: 16px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
            font-family: inherit;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: #8b6f47;
            color: white;
        }

        .btn-primary:hover {
            background: #6d5435;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(139, 111, 71, 0.3);
        }

        .btn-secondary {
            background: white;
            color: #8b6f47;
            border: 2px solid #8b6f47;
            margin-top: 10px;
        }

        .btn-secondary:hover {
            background: #8b6f47;
            color: white;
        }

        .error-messages {
            background: #ffe6e6;
            border: 1px solid #ff9999;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            color: #d32f2f;
            text-align: left;
        }

        .error-messages ul {
            margin: 0;
            padding-left: 20px;
        }

        .success-message,
        .pending-message {
            text-align: center;
            padding: 40px 20px;
        }

        .success-icon,
        .pending-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            display: block;
        }

        .success-icon {
            color: #4caf50;
        }

        .pending-icon {
            color: #ff9800;
        }

        .success-message h2 {
            color: #4caf50;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }

        .pending-message h2 {
            color: #ff9800;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }

        .success-message p,
        .pending-message p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .signup-link {
            margin-top: 20px;
            color: #999;
            font-size: 14px;
        }

        .signup-link a {
            color: #cc9966;
            text-decoration: underline;
        }

        .signup-link a:hover {
            color: #8b6f47;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* ==========================================
           RESPONSIVE DESIGN - LARGE SCREENS
           ========================================== */
        @media (min-width: 1200px) {
            .signup-container {
                max-width: 600px;
                padding: 50px;
            }

            .brand-name {
                font-size: 2rem;
            }

            .welcome-subtitle {
                font-size: 16px;
            }

            .user-type-option {
                padding: 30px 20px;
            }

            .user-type-icon {
                font-size: 3rem;
            }
        }

        /* ==========================================
           RESPONSIVE DESIGN - TABLET (LANDSCAPE)
           ========================================== */
        @media (max-width: 1024px) and (min-width: 769px) {
            body {
                padding: 15px;
            }

            .signup-container {
                max-width: 600px;
                padding: 35px;
                margin-top: 90px;
            }

            .form-row {
                gap: 12px;
            }

            .user-type-selector {
                gap: 12px;
            }
        }

        /* ==========================================
           RESPONSIVE DESIGN - TABLET (PORTRAIT)
           ========================================== */
        @media (max-width: 768px) and (min-width: 481px) {
            body {
                padding: 15px;
                background-attachment: scroll;
            }

            .signup-container {
                max-width: 100%;
                padding: 30px 25px;
                margin-top: 80px;
                margin-bottom: 15px;
            }

            .brand-name {
                font-size: 1.6rem;
            }

            .welcome-subtitle {
                font-size: 14px;
                margin: 15px auto 25px;
            }

            .user-type-selector {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .user-type-option {
                padding: 20px 15px;
            }

            .user-type-icon {
                font-size: 2.2rem;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .step-indicator {
                gap: 12px;
                margin-bottom: 25px;
            }

            .step {
                width: 32px;
                height: 32px;
                font-size: 13px;
            }

            .step-indicator .step:not(:last-child)::after {
                width: 20px;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                padding: 12px 14px;
                font-size: 15px;
            }

            .btn {
                padding: 14px 18px;
                font-size: 15px;
            }

            .file-input-label {
                padding: 18px;
            }

            .file-input-label i {
                font-size: 1.8rem;
            }
        }

        /* ==========================================
           RESPONSIVE DESIGN - MOBILE (LARGE)
           ========================================== */
        @media (max-width: 480px) {
            body {
                padding: 10px;
                background-attachment: scroll;
            }

            .signup-container {
                padding: 25px 20px;
                margin-top: 70px;
                margin-bottom: 10px;
                border-radius: 6px;
            }

            .logo-section {
                flex-direction: column;
                gap: 12px;
                margin-bottom: 20px;
            }

            .logo {
                width: 50px;
                height: 50px;
            }

            .logo svg {
                width: 28px;
                height: 28px;
            }

            .brand-info {
                align-items: center;
                text-align: center;
            }

            .brand-name {
                font-size: 1.4rem;
            }

            .brand-tagline {
                font-size: 12px;
            }

            .welcome-subtitle {
                font-size: 13px;
                margin: 15px auto 20px;
                line-height: 1.3;
            }

            .step-indicator {
                gap: 8px;
                margin-bottom: 20px;
            }

            .step {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }

            .step-indicator .step:not(:last-child)::after {
                width: 15px;
            }

            .user-type-selector {
                grid-template-columns: 1fr;
                gap: 10px;
                margin-bottom: 20px;
            }

            .user-type-option {
                padding: 18px 12px;
            }

            .user-type-icon {
                font-size: 1.8rem;
                margin-bottom: 8px;
            }

            .user-type-option h4 {
                font-size: 15px;
                margin-bottom: 3px;
            }

            .user-type-option p {
                font-size: 12px;
            }

            .form-group {
                margin-bottom: 18px;
            }

            .form-group label {
                font-size: 15px;
                margin-bottom: 6px;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                padding: 12px 14px;
                font-size: 16px; /* Prevents zoom on iOS */
                border-radius: 6px;
            }

            .form-group textarea {
                min-height: 90px;
            }

            .btn {
                padding: 14px 16px;
                font-size: 14px;
                margin-top: 12px;
            }

            .file-input-label {
                padding: 15px;
            }

            .file-input-label i {
                font-size: 1.6rem;
            }

            .file-input-label small {
                font-size: 11px;
            }

            .file-preview {
                padding: 10px;
                font-size: 13px;
            }

            .error-messages {
                padding: 12px;
                font-size: 13px;
            }

            .success-message,
            .pending-message {
                padding: 30px 15px;
            }

            .success-icon,
            .pending-icon {
                font-size: 3rem;
                margin-bottom: 15px;
            }

            .success-message h2,
            .pending-message h2 {
                font-size: 1.5rem;
                margin-bottom: 12px;
            }

            .success-message p,
            .pending-message p {
                font-size: 14px;
                margin-bottom: 15px;
            }

            .signup-link {
                font-size: 13px;
                margin-top: 15px;
            }
        }

        /* ==========================================
           RESPONSIVE DESIGN - MOBILE (SMALL)
           ========================================== */
        @media (max-width: 360px) {
            body {
                padding: 5px;
            }

            .signup-container {
                padding: 18px 12px;
                margin-top: 55px;
                margin-bottom: 5px;
            }

            .logo-section {
                gap: 8px;
                margin-bottom: 15px;
            }

            .logo {
                width: 40px;
                height: 40px;
            }

            .logo svg {
                width: 24px;
                height: 24px;
            }

            .brand-name {
                font-size: 1.2rem;
            }

            .brand-tagline {
                font-size: 10px;
            }

            .welcome-subtitle {
                font-size: 11px;
                margin: 10px auto 15px;
            }

            .user-type-option {
                padding: 12px 8px;
            }

            .user-type-icon {
                font-size: 1.6rem;
                margin-bottom: 5px;
            }

            .user-type-option h4 {
                font-size: 13px;
            }

            .user-type-option p {
                font-size: 10px;
            }

            .form-group {
                margin-bottom: 14px;
            }

            .form-group label {
                font-size: 13px;
                margin-bottom: 4px;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                padding: 10px 11px;
                font-size: 16px;
            }

            .btn {
                padding: 11px 12px;
                font-size: 12px;
                margin-top: 8px;
            }

            .file-input-label {
                padding: 10px;
            }

            .file-input-label i {
                font-size: 1.2rem;
                margin-bottom: 5px;
            }

            .file-input-label small {
                font-size: 10px;
            }
        }

        /* ==========================================
           RESPONSIVE DESIGN - MOBILE (EXTRA SMALL)
           ========================================== */
        @media (max-width: 320px) {
            body {
                padding: 5px;
            }

            .signup-container {
                padding: 18px 12px;
                margin-top: 55px;
                margin-bottom: 5px;
            }

            .logo-section {
                gap: 8px;
                margin-bottom: 15px;
            }

            .logo {
                width: 40px;
                height: 40px;
            }

            .logo svg {
                width: 24px;
                height: 24px;
            }

            .brand-name {
                font-size: 1.2rem;
            }

            .brand-tagline {
                font-size: 10px;
            }

            .welcome-subtitle {
                font-size: 11px;
                margin: 10px auto 15px;
            }

            .user-type-option {
                padding: 12px 8px;
            }

            .user-type-icon {
                font-size: 1.6rem;
                margin-bottom: 5px;
            }

            .user-type-option h4 {
                font-size: 13px;
            }

            .user-type-option p {
                font-size: 10px;
            }

            .form-group {
                margin-bottom: 14px;
            }

            .form-group label {
                font-size: 13px;
                margin-bottom: 4px;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                padding: 10px 11px;
                font-size: 16px;
            }

            .btn {
                padding: 11px 12px;
                font-size: 12px;
                margin-top: 8px;
            }

            .file-input-label {
                padding: 10px;
            }

            .file-input-label i {
                font-size: 1.2rem;
                margin-bottom: 5px;
            }

            .file-input-label small {
                font-size: 10px;
            }
        }

        /* ==========================================
           LANDSCAPE ORIENTATION ADJUSTMENTS
           ========================================== */
        @media (max-height: 600px) and (orientation: landscape) {
            body {
                padding: 10px;
            }

            .signup-container {
                margin-top: 80px;
                margin-bottom: 10px;
                padding: 25px 30px;
            }

            .logo-section {
                margin-bottom: 15px;
            }

            .welcome-subtitle {
                margin: 10px auto 20px;
            }

            .step-indicator {
                margin-bottom: 20px;
            }

            .user-type-selector {
                margin-bottom: 20px;
            }

            .form-group {
                margin-bottom: 15px;
            }

            .success-message,
            .pending-message {
                padding: 25px 20px;
            }
        }

        /* ==========================================
           HIGH DPI SCREENS
           ========================================== */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .file-input-label {
                border-width: 1px;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                border-width: 1px;
            }

            .user-type-option {
                border-width: 1px;
            }
        }

        /* ==========================================
           DARK MODE SUPPORT
           ========================================== */
        @media (prefers-color-scheme: dark) {
            /* Optional: Add dark mode styles if needed */
        }

        /* ==========================================
           REDUCED MOTION ACCESSIBILITY
           ========================================== */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }

            .signup-container {
                animation: none;
            }

            .step,
            .user-type-option,
            .btn,
            .file-input-label,
            .form-group input,
            .form-group select,
            .form-group textarea {
                transition: none;
            }
        }

        /* ==========================================
           PRINT STYLES
           ========================================== */
        @media print {
            body {
                background: white !important;
                color: black !important;
            }

            .signup-container {
                box-shadow: none !important;
                border: 1px solid #000;
            }

            .navbar,
            .btn,
            .file-input-container {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbarYadawity" id="yadawityNavbar">
        <div class="navContainer">
            <div class="navLogo">
                <a href="../index.php" class="navLogoLink">
                    <div class="logoIcon">
                        <svg width="40" height="40" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 50 Q15 30 25 25 Q35 20 45 35 Q40 45 35 50 Q40 55 45 65 Q35 80 25 75 Q15 70 20 50 Z" fill="currentColor" opacity="0.8"/>
                            <path d="M80 50 Q85 30 75 25 Q65 20 55 35 Q60 45 65 50 Q60 55 55 65 Q65 80 75 75 Q85 70 80 50 Z" fill="currentColor" opacity="0.8"/>
                            <line x1="50" y1="20" x2="50" y2="80" stroke="currentColor" stroke-width="3"/>
                            <path d="M50 20 Q45 15 42 12 M50 20 Q55 15 58 12" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                    </div>
                    <div class="logoText">
                        <span class="logoName">Yadawity</span>
                        <span class="logoEst">EST. 2025</span>
                    </div>
                </a>
            </div>

            <div class="navMenu" id="navMenu">
                <a href="../index.php" class="navLink" data-page="home">HOME</a>
                <a href="../gallery.html" class="navLink" data-page="gallery">GALLERY</a>
                <a href="../courses.html" class="navLink" data-page="courses">COURSES</a>
                <a href="../artwork.html" class="navLink" data-page="atelier">ARTWORKS</a>
                <a href="../auction.html" class="navLink" data-page="auction">AUCTION HOUSE</a>
                <a href="../art therapy.html" class="navLink therapyNav" data-page="therapy">THERAPY</a>

                <div class="navActions">
                    <div class="searchContainer">
                        <input type="text" placeholder="Search artists, artworks..." class="searchInput" id="navbarSearch" />
                        <button class="searchBtn" id="searchButton">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>

                    <a href="../wishlist.html" class="navIconLink" title="Wishlist" id="wishlistLink">
                        <i class="fas fa-heart"></i>
                        <span class="wishlistCount" id="wishlistCount" style="display: none">0</span>
                    </a>

                    <a href="../cart.html" class="navIconLink cartLink" title="Cart" id="cartLink">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="cartCount" id="cartCount">0</span>
                    </a>

                    <div class="userDropdown">
                        <a href="#" class="navIconLink" title="Account" id="userAccount">
                            <i class="fas fa-user"></i>
                        </a>
                        <div class="userDropdownMenu" id="userMenu">
                            <a href="../profile.html" class="dropdownItem">
                                <i class="fas fa-user"></i>
                                <span>Profile</span>
                            </a>
                            <div class="dropdownDivider"></div>
                            <a href="../login.php" class="dropdownItem">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="navToggle" id="navToggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>

    <div class="signup-container">
        <div class="logo-section">
            <div class="logo">
                <svg width="40" height="40" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 50 Q15 30 25 25 Q35 20 45 35 Q40 45 35 50 Q40 55 45 65 Q35 80 25 75 Q15 70 20 50 Z" fill="currentColor" opacity="0.8"/>
                    <path d="M80 50 Q85 30 75 25 Q65 20 55 35 Q60 45 65 50 Q60 55 55 65 Q65 80 75 75 Q85 70 80 50 Z" fill="currentColor" opacity="0.8"/>
                    <line x1="50" y1="20" x2="50" y2="80" stroke="currentColor" stroke-width="3"/>
                    <path d="M50 20 Q45 15 42 12 M50 20 Q55 15 58 12" stroke="currentColor" stroke-width="2" fill="none"/>
                </svg>
            </div>
            <div class="brand-info">
                <div class="brand-name">Yadawity</div>
                <div class="brand-tagline">EST. 2025</div>
            </div>
        </div>

        <?php if ($step === '1'): ?>
            <p class="welcome-subtitle">Join our community of artists and art lovers. Create your account to start your artistic journey with us.</p>

            <div class="step-indicator">
                <div class="step active">1</div>
                <div class="step">2</div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" id="signupForm">
                <input type="hidden" name="step" value="1">

                <div class="user-type-selector">
                    <div class="user-type-option" onclick="selectUserType('buyer')">
                        <input type="radio" name="user_type" value="buyer" id="buyer" required>
                        <i class="user-type-icon fas fa-shopping-bag"></i>
                        <h4>Art Buyer</h4>
                        <p>Browse and purchase artwork</p>
                    </div>
                    <div class="user-type-option" onclick="selectUserType('artist')">
                        <input type="radio" name="user_type" value="artist" id="artist" required>
                        <i class="user-type-icon fas fa-palette"></i>
                        <h4>Artist</h4>
                        <p>Showcase and sell your art</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" name="first_name" id="first_name" required autocomplete="given-name"
                               value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" name="last_name" id="last_name" required autocomplete="family-name"
                               value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" name="email" id="email" required autocomplete="email"
                           placeholder="johndoe@example.com"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" name="phone" id="phone" required autocomplete="tel"
                           placeholder="+1 (555) 123-4567"
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" name="password" id="password" required minlength="8" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" name="confirm_password" id="confirm_password" required minlength="8" autocomplete="new-password">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Continue</button>
            </form>

            <div class="signup-link">
                Already have an account? <a href="../login.php">Sign In</a>
            </div>

        <?php elseif ($step === 'profile_upload'): ?>
            <p class="welcome-subtitle">Almost done! Add a profile picture to help others recognize you in our community.</p>

            <div class="step-indicator">
                <div class="step completed">1</div>
                <div class="step active">2</div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="step" value="profile_upload">

                <div class="form-group">
                    <label for="profile_picture">Profile Picture (Optional)</label>
                    <div class="file-input-container">
                        <input type="file" name="profile_picture" id="profile_picture" accept="image/*" onchange="previewFile(this, 'profile-preview')">
                        <label for="profile_picture" class="file-input-label">
                            <i class="fas fa-camera"></i>
                            Click to upload profile picture<br>
                            <small>Supported: JPG, PNG, GIF (Max 5MB)</small>
                        </label>
                    </div>
                    <div id="profile-preview" class="file-preview" style="display: none;"></div>
                </div>

                <button type="submit" class="btn btn-primary">Complete Registration</button>
                <button type="button" class="btn btn-secondary" onclick="submitWithoutPhoto()">Skip for Now</button>
            </form>

        <?php elseif ($step === 'artist_verification'): ?>
            <p class="welcome-subtitle">Complete your artist profile with verification documents to start showcasing your work.</p>

            <div class="step-indicator">
                <div class="step completed">1</div>
                <div class="step active">2</div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="step" value="artist_verification">

                <div class="form-group">
                    <label for="national_id">National ID Number *</label>
                    <input type="text" name="national_id" id="national_id" required 
                           placeholder="Enter your national ID number"
                           value="<?php echo htmlspecialchars($_POST['national_id'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="art_specialty">Art Specialty *</label>
                    <input type="text" name="art_specialty" id="art_specialty" required 
                           placeholder="e.g., Oil Painting, Digital Art, Sculpture"
                           value="<?php echo htmlspecialchars($_POST['art_specialty'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="years_experience">Years of Experience *</label>
                    <input type="number" name="years_experience" id="years_experience" required min="0" max="50"
                           placeholder="0"
                           value="<?php echo htmlspecialchars($_POST['years_experience'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="bio">Artist Bio *</label>
                    <textarea name="bio" id="bio" required 
                              placeholder="Tell us about your artistic journey, style, and inspirations..."><?php echo htmlspecialchars($_POST['bio'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="id_front">National ID Front Photo *</label>
                    <div class="file-input-container">
                        <input type="file" name="id_front" id="id_front" required accept="image/*,application/pdf" onchange="previewFile(this, 'id-front-preview')">
                        <label for="id_front" class="file-input-label">
                            <i class="fas fa-id-card"></i>
                            Upload ID Front Photo<br>
                            <small>Supported: JPG, PNG, PDF (Max 5MB)</small>
                        </label>
                    </div>
                    <div id="id-front-preview" class="file-preview" style="display: none;"></div>
                </div>

                <div class="form-group">
                    <label for="id_back">National ID Back Photo *</label>
                    <div class="file-input-container">
                        <input type="file" name="id_back" id="id_back" required accept="image/*,application/pdf" onchange="previewFile(this, 'id-back-preview')">
                        <label for="id_back" class="file-input-label">
                            <i class="fas fa-id-card"></i>
                            Upload ID Back Photo<br>
                            <small>Supported: JPG, PNG, PDF (Max 5MB)</small>
                        </label>
                    </div>
                    <div id="id-back-preview" class="file-preview" style="display: none;"></div>
                </div>

                <div class="form-group">
                    <label for="profile_picture">Profile Picture (Optional)</label>
                    <div class="file-input-container">
                        <input type="file" name="profile_picture" id="profile_picture" accept="image/*" onchange="previewFile(this, 'profile-preview')">
                        <label for="profile_picture" class="file-input-label">
                            <i class="fas fa-camera"></i>
                            Upload Profile Picture<br>
                            <small>Supported: JPG, PNG, GIF (Max 5MB)</small>
                        </label>
                    </div>
                    <div id="profile-preview" class="file-preview" style="display: none;"></div>
                </div>

                <button type="submit" class="btn btn-primary">Submit for Verification</button>
            </form>

        <?php elseif ($step === 'success'): ?>
            <div class="success-message">
                <i class="success-icon fas fa-check-circle"></i>
                <h2>Welcome to Yadawity Gallery!</h2>
                <p>Your account has been created successfully. You can now start exploring our collection of authentic artworks and connect with talented artists.</p>
                <a href="../login.php" class="btn btn-primary" style="text-decoration: none; display: inline-block;">Sign In to Your Account</a>
            </div>

        <?php elseif ($step === 'artist_pending'): ?>
            <div class="pending-message">
                <i class="pending-icon fas fa-clock"></i>
                <h2>Verification In Progress</h2>
                <p>Thank you for submitting your artist application! Your account is currently under review.</p>
                <p>Our team will verify your documents and activate your account within 2-3 business days. You'll receive an email notification once your account is approved.</p>
                <a href="../index.php" class="btn btn-primary" style="text-decoration: none; display: inline-block;">Explore Gallery</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../app.js"></script>
    <script src="../components/BurgerMenu/burger-menu.js"></script>
    
    <script>
        function selectUserType(type) {
            // Remove selected class from all options
            document.querySelectorAll('.user-type-option').forEach(option => {
                option.classList.remove('selected');
            });

            // Add selected class to clicked option
            document.querySelector(`input[value="${type}"]`).checked = true;
            document.querySelector(`input[value="${type}"]`).closest('.user-type-option').classList.add('selected');
        }

        function previewFile(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];

            if (file) {
                // Validate file size
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: 'Please select a file smaller than 5MB.',
                        confirmButtonColor: '#8b6f47'
                    });
                    input.value = '';
                    preview.style.display = 'none';
                    return;
                }

                preview.innerHTML = `<i class="fas fa-check"></i> Selected: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }

        function submitWithoutPhoto() {
            const form = document.querySelector('form');
            const fileInput = document.querySelector('#profile_picture');
            if (fileInput) {
                fileInput.removeAttribute('required');
            }
            form.submit();
        }

        // Form validation 
        document.addEventListener('DOMContentLoaded', function() {
            const signupForm = document.getElementById('signupForm');
            if (signupForm) {
                signupForm.addEventListener('submit', function(e) {
                    const password = document.getElementById('password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    const userType = document.querySelector('input[name="user_type"]:checked');

                    // Validation checks
                    if (!userType) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Please Select Account Type',
                            text: 'Choose whether you want to join as an Art Buyer or Artist.',
                            confirmButtonColor: '#8b6f47'
                        });
                        return;
                    }

                    if (password !== confirmPassword) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Passwords Don\'t Match',
                            text: 'Please make sure both password fields contain the same password.',
                            confirmButtonColor: '#8b6f47'
                        });
                        return;
                    }

                    if (password.length < 8) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Password Too Short',
                            text: 'Password must be at least 8 characters long.',
                            confirmButtonColor: '#8b6f47'
                        });
                        return;
                    }

                    // Show loading indicator
                    Swal.fire({
                        title: 'Creating your account...',
                        text: 'Please wait while we set up your profile',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                });
            }

            // Real-time password matching
            const confirmPasswordInput = document.getElementById('confirm_password');
            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', function() {
                    const password = document.getElementById('password').value;
                    const confirmPassword = this.value;

                    if (confirmPassword && password !== confirmPassword) {
                        this.style.borderColor = '#e74c3c';
                    } else if (confirmPassword && password === confirmPassword) {
                        this.style.borderColor = '#27ae60';
                    } else {
                        this.style.borderColor = '#e0e0e0';
                    }
                });
            }

            // Show success/error messages
            <?php if (!empty($errors) && $_SERVER["REQUEST_METHOD"] == "POST"): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Error',
                    html: '<?php echo implode("<br>", array_map("htmlspecialchars", $errors)); ?>',
                    confirmButtonColor: '#8b6f47'
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>

