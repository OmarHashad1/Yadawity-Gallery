#!/usr/bin/env php
<?php
// Script to generate valid user cookies for testing
// Usage: php generate_test_cookie.php <user_id>

if ($argc < 2) {
    echo "Usage: php generate_test_cookie.php <user_id>\n";
    echo "Example: php generate_test_cookie.php 2\n";
    exit(1);
}

$user_id = (int)$argv[1];

// Database connection
require_once 'API/db.php';

try {
    // Get user info
    $stmt = $db->prepare("SELECT user_id, email, first_name, last_name FROM users WHERE user_id = ? AND is_active = 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo "User ID $user_id not found or not active.\n";
        exit(1);
    }
    
    $user = $result->fetch_assoc();
    
    // Generate cookie hash
    $hash = hash('sha256', $user['email'] . 'yadawity_salt');
    $cookie = $user_id . '_' . $hash;
    
    echo "User: {$user['first_name']} {$user['last_name']} ({$user['email']})\n";
    echo "Cookie: $cookie\n";
    echo "\nTo set this cookie in your browser console, run:\n";
    echo "document.cookie = 'user_login=$cookie; path=/; expires=' + new Date(Date.now() + 30*24*60*60*1000).toUTCString();\n";
    echo "\nOr test with curl:\n";
    echo "curl \"http://localhost/API/getArtistStatistics.php\" -H \"Cookie: user_login=$cookie\"\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} finally {
    if (isset($stmt)) $stmt->close();
    $db->close();
}
?>
