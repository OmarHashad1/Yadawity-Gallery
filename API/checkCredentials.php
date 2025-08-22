<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include 'db.php';

// Use correct database connection variable
$conn = $db;

try {
    // Check if user_login cookie exists
    if (!isset($_COOKIE['user_login'])) {
        echo json_encode([
            'success' => false,
            'message' => 'No authentication cookie found',
            'user_id' => null
        ]);
        exit;
    }
    
    $cookieValue = $_COOKIE['user_login'];
    
    // Extract user ID from cookie (format: user_id_hash)
    $parts = explode('_', $cookieValue, 2);
    if (count($parts) !== 2) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid cookie format',
            'user_id' => null
        ]);
        exit;
    }
    
    $user_id = (int)$parts[0];
    $provided_hash = $parts[1];
    
    if ($user_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid user ID in cookie',
            'user_id' => null
        ]);
        exit;
    }
    
    // Get user data
    $stmt = $conn->prepare("SELECT email, is_active FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found',
            'user_id' => null
        ]);
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // Check if user is active
    if (!$user['is_active']) {
        echo json_encode([
            'success' => false,
            'message' => 'User account is inactive',
            'user_id' => null
        ]);
        exit;
    }
    
    // Get all active login sessions for this user
    $session_stmt = $conn->prepare("
        SELECT login_time 
        FROM user_login_sessions 
        WHERE user_id = ? AND is_active = 1 
        ORDER BY login_time DESC
    ");
    $session_stmt->bind_param("i", $user_id);
    $session_stmt->execute();
    $session_result = $session_stmt->get_result();
    
    // Try to validate the hash against any active session
    $valid_session = false;
    while ($session = $session_result->fetch_assoc()) {
        $expected_hash = hash('sha256', $user['email'] . $session['login_time'] . 'yadawity_salt');
        if ($provided_hash === $expected_hash) {
            $valid_session = true;
            break;
        }
    }
    
    if (!$valid_session) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid session hash',
            'user_id' => null
        ]);
        exit;
    }
    
    // Authentication successful
    echo json_encode([
        'success' => true,
        'message' => 'User authenticated successfully',
        'user_id' => $user_id
    ]);
    
} catch (Exception $e) {
    error_log("checkCredentials Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Server error during authentication',
        'user_id' => null
    ]);
}

$conn->close();
?>
