<?php
include_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, 'Invalid JSON input', null, 400);
    }
    
    // Validate required fields
    $error = validateRequired($input, ['user_id']);
    if ($error) {
        sendResponse(false, $error, null, 400);
    }
    
    $userId = $input['user_id'];
    
    // Check if user exists
    $checkStmt = $pdo->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $checkStmt->execute([$userId]);
    if (!$checkStmt->fetch()) {
        sendResponse(false, 'User not found', null, 404);
    }
    
    $updateFields = [];
    $params = [];
    
    // Build dynamic update query
    $allowedFields = [
        'first_name', 'last_name', 'email', 'phone', 'user_type', 
        'is_active', 'location', 'bio', 'art_specialty', 'years_of_experience'
    ];
    
    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updateFields[] = "$field = ?";
            $params[] = $input[$field];
        }
    }
    
    // Handle password update separately if provided
    if (isset($input['password']) && !empty($input['password'])) {
        $updateFields[] = "password = ?";
        $params[] = password_hash($input['password'], PASSWORD_DEFAULT);
    }
    
    if (empty($updateFields)) {
        sendResponse(false, 'No valid fields to update', null, 400);
    }
    
    // Check for duplicate email if email is being updated
    if (isset($input['email'])) {
        $emailCheckStmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $emailCheckStmt->execute([$input['email'], $userId]);
        if ($emailCheckStmt->fetch()) {
            sendResponse(false, 'Email already exists', null, 409);
        }
    }
    
    $params[] = $userId;
    $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE user_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);
    
    if ($result) {
        // Get updated user data
        $getUserStmt = $pdo->prepare("
            SELECT user_id, email, first_name, last_name, phone, user_type, 
                   is_active, location, bio, art_specialty, years_of_experience, created_at 
            FROM users WHERE user_id = ?
        ");
        $getUserStmt->execute([$userId]);
        $user = $getUserStmt->fetch();
        
        sendResponse(true, 'User updated successfully', $user);
    } else {
        sendResponse(false, 'Failed to update user', null, 500);
    }

} catch (Exception $e) {
    sendResponse(false, 'Error updating user: ' . $e->getMessage(), null, 500);
}
?>
