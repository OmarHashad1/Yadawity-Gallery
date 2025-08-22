<?php
session_start();

// Set up a test session for artwork creation
$_SESSION['user_id'] = 1; // Test user ID
$_SESSION['username'] = 'test_artist';

echo json_encode([
    'session_set' => true,
    'user_id' => $_SESSION['user_id'],
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
