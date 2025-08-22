<?php
include_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, 'Invalid JSON input', null, 400);
    }
    
    // Validate required fields
    $required = ['email', 'password', 'first_name', 'last_name'];
    $error = validateRequired($input, $required);
    if ($error) {
        sendResponse(false, $error, null, 400);
    }
    
    $email = $input['email'];
    $password = password_hash($input['password'], PASSWORD_DEFAULT);
    $firstName = $input['first_name'];
    $lastName = $input['last_name'];
    $phone = $input['phone'] ?? '';
    $userType = $input['user_type'] ?? 'buyer';
    $location = $input['location'] ?? '';
    $bio = $input['bio'] ?? '';
    
    // Check if email already exists
    $checkStmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $checkStmt->execute([$email]);
    if ($checkStmt->fetch()) {
        sendResponse(false, 'Email already exists', null, 409);
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO users (email, password, first_name, last_name, phone, user_type, location, bio) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([$email, $password, $firstName, $lastName, $phone, $userType, $location, $bio]);
    
    if ($result) {
        $userId = $pdo->lastInsertId();
        
        // Get the created user data
        $getUserStmt = $pdo->prepare("
            SELECT user_id, email, first_name, last_name, phone, user_type, 
                   is_active, location, created_at 
            FROM users WHERE user_id = ?
        ");
        $getUserStmt->execute([$userId]);
        $user = $getUserStmt->fetch();
        
        sendResponse(true, 'User created successfully', $user, 201);
    } else {
        sendResponse(false, 'Failed to create user', null, 500);
    }

} catch (Exception $e) {
    sendResponse(false, 'Error creating user: ' . $e->getMessage(), null, 500);
}
?>
