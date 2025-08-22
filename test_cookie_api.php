<?php
// Test the new API endpoint with cookie authentication
require_once 'API/db.php';

// First, let's create a test cookie like the login system would
$user_id = 1;
$email = 'test@example.com'; // We need to get the actual email from database

// Get user email from database
$stmt = $db->prepare("SELECT email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $email = $user['email'];
    
    // Create cookie like login.php does
    $cookieHash = hash('sha256', $email . 'yadawity_salt');
    $cookieValue = $user_id . '_' . $cookieHash;
    
    // Set the cookie
    $_COOKIE['user_login'] = $cookieValue;
    
    echo "Created test cookie: " . $cookieValue . "\n";
    echo "Testing API endpoint...\n\n";
    
    // Set GET parameters
    $_GET['type'] = 'all';
    $_GET['page'] = '1';
    $_GET['limit'] = '5';
    
    // Capture API output
    ob_start();
    include 'API/getArtistReviews.php';
    $output = ob_get_clean();
    
    echo "API Response:\n";
    echo $output;
    
} else {
    echo "No user found with ID 1\n";
}

$stmt->close();
?>
