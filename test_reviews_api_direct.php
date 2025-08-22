<?php
// Test the API directly like a browser would
session_start();

// Set test session like artistPortal.php does
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_name'] = 'Test Artist';
}

// Include the API file 
// First set up the $_GET parameters
$_GET['type'] = 'all';
$_GET['page'] = '1';
$_GET['limit'] = '10';

// Capture output
ob_start();
include 'API/getArtistReviews.php';
$output = ob_get_clean();

echo "API Output:\n";
echo $output;
echo "\n";
?>
