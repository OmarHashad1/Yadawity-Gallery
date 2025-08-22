<?php
session_start();

// Set up test session like artistPortal.php does
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test Artist';

// Now call the API
$url = 'http://localhost/API/getArtistReviews.php';
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Cookie: PHPSESSID=' . session_id()
    ]
]);

$response = file_get_contents($url, false, $context);
echo "API Response:\n";
echo $response;
echo "\n\nHeaders:\n";
print_r($http_response_header);
?>
