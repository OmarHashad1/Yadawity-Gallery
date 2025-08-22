<?php
// Simple test of the new API
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'API/db.php';

// Get user email for test
$stmt = $db->prepare("SELECT email FROM users WHERE user_id = 1");
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user) {
    $email = $user['email'];
    $cookieHash = hash('sha256', $email . 'yadawity_salt');
    $cookieValue = '1_' . $cookieHash;
    
    // Set cookie
    $_COOKIE['user_login'] = $cookieValue;
    $_GET['type'] = 'all';
    
    echo "Testing with cookie: " . $cookieValue . "\n";
    echo "User email: " . $email . "\n\n";
    
    // Test the authentication function directly
    function checkUserCookie() {
        global $db;
        
        if (isset($_COOKIE['user_login'])) {
            $cookieValue = $_COOKIE['user_login'];
            $parts = explode('_', $cookieValue, 2);
            
            if (count($parts) === 2) {
                $userId = $parts[0];
                $cookieHash = $parts[1];
                
                $stmt = $db->prepare("SELECT user_id, email, first_name, last_name, user_type FROM users WHERE user_id = ? AND is_active = 1");
                if ($stmt) {
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows === 1) {
                        $user = $result->fetch_assoc();
                        $expectedHash = hash('sha256', $user['email'] . 'yadawity_salt');
                        
                        echo "Expected hash: " . $expectedHash . "\n";
                        echo "Cookie hash: " . $cookieHash . "\n";
                        
                        if ($cookieHash === $expectedHash) {
                            $stmt->close();
                            return [
                                'active' => true,
                                'user_id' => $user['user_id'],
                                'user_name' => $user['first_name'] . ' ' . $user['last_name'],
                                'user_type' => $user['user_type']
                            ];
                        }
                    }
                    $stmt->close();
                }
            }
        }
        
        return ['active' => false];
    }
    
    $userAuth = checkUserCookie();
    print_r($userAuth);
    
} else {
    echo "No user found\n";
}
?>
