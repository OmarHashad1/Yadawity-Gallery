<?php
// Test the updated bio field functionality
$testData = [
    'bio' => 'Testing bio update - this should save in the bio field, not artist_bio'
];

$postData = json_encode($testData);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/API/updateArtistProfile.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($postData),
    'Cookie: user_login=17_somehash' // Simulate the cookie
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

echo "=== Testing Updated Bio Field ===\n";
echo "Data being sent: " . $postData . "\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

curl_close($ch);

// Check what's actually in the database now
require_once 'API/db.php';
$stmt = $db->prepare("SELECT bio, artist_bio FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$user_id = 17;
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo "Database fields after API call:\n";
    echo "bio field: '" . $row['bio'] . "' (length: " . strlen($row['bio']) . ")\n";
    echo "artist_bio field: '" . $row['artist_bio'] . "' (length: " . strlen($row['artist_bio']) . ")\n";
}

$db->close();
?>
