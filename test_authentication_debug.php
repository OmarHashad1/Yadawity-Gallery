<?php
// Test authentication debug script
require_once 'API/db.php';

echo "<h1>Authentication Debug Test</h1>\n";

// Check what cookies are set
echo "<h2>Current Cookies:</h2>\n";
if (empty($_COOKIE)) {
    echo "No cookies found\n";
} else {
    foreach ($_COOKIE as $name => $value) {
        if ($name === 'user_login') {
            echo "Cookie: $name = " . substr($value, 0, 20) . "...\n";
            
            // Parse cookie
            $parts = explode('_', $value, 2);
            if (count($parts) === 2) {
                echo "Cookie user_id: " . $parts[0] . "\n";
                echo "Cookie hash: " . substr($parts[1], 0, 20) . "...\n";
                
                // Test authentication
                $user_id = (int)$parts[0];
                $provided_hash = $parts[1];
                
                if ($user_id > 0) {
                    $stmt = $db->prepare("SELECT email, is_active FROM users WHERE user_id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $user = $result->fetch_assoc();
                        $expected_hash = hash('sha256', $user['email'] . 'yadawity_salt');
                        
                        echo "User found: " . $user['email'] . "\n";
                        echo "User active: " . ($user['is_active'] ? 'Yes' : 'No') . "\n";
                        echo "Hash matches: " . ($provided_hash === $expected_hash ? 'Yes' : 'No') . "\n";
                        
                        if ($provided_hash === $expected_hash && $user['is_active']) {
                            echo "<strong>Authentication: VALID for user_id $user_id</strong>\n";
                        } else {
                            echo "<strong>Authentication: INVALID</strong>\n";
                        }
                    } else {
                        echo "User not found in database\n";
                    }
                    $stmt->close();
                }
            }
        } else {
            echo "Cookie: $name = $value\n";
        }
    }
}

echo "\n<h2>Session Data:</h2>\n";
session_start();
if (empty($_SESSION)) {
    echo "No session data\n";
} else {
    foreach ($_SESSION as $key => $value) {
        echo "Session: $key = $value\n";
    }
}

echo "\n<h2>Test API Call:</h2>\n";
echo "Making API call to getArtistStatistics.php...\n";

// Simulate the API call
ob_start();
$_SERVER['REQUEST_METHOD'] = 'GET';
include 'API/getArtistStatistics.php';
$api_output = ob_get_clean();

echo "API Response:\n";
echo "<pre>" . htmlspecialchars($api_output) . "</pre>\n";
?>
