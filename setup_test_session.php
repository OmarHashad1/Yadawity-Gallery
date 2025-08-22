<?php
session_start();

// Set up test session
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'test_artist';

echo "Session set up for testing artwork submission<br>";
echo "User ID: " . $_SESSION['user_id'] . "<br>";
echo "Username: " . $_SESSION['username'] . "<br>";
echo "<br>";
echo "<a href='test_artwork_api.php'>Now test the artwork API</a><br>";
echo "<a href='artistPortal.php'>Go to Artist Portal</a>";
?>
