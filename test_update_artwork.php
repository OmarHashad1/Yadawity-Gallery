<?php
// Simple test for updateArtwork.php
echo "Testing updateArtwork API...\n";

// Simulate POST data
$_POST = [
    'artwork_id' => '149',
    'title' => 'Test Artwork',
    'price' => '100',
    'category' => 'paintings',
    'style' => 'abstract',
    'medium' => 'oil',
    'width' => '50',
    'height' => '70',
    'depth' => '',
    'year' => '2025',
    'is_available' => '1',
    'on_auction' => '0',
    'description' => 'Test description'
];

// Set required server variables
$_SERVER['REQUEST_METHOD'] = 'POST';

// Mock cookie for authentication (using user ID 17 who owns artwork 149)
$_COOKIE['user_login'] = '17_e0f523fbf5c358ba58c70019074a0f7da13c9e4b3c5cd15beb4656605034b22c';

echo "POST data set up:\n";
print_r($_POST);

echo "\nIncluding updateArtwork.php...\n";

// Capture output
ob_start();
include 'API/updateArtwork.php';
$output = ob_get_clean();

echo "API Response:\n";
echo $output;
?>
