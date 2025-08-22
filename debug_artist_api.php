<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'API/db.php';

echo "<h2>Database Connection Test</h2>";
if ($db->connect_error) {
    echo "❌ Database connection failed: " . $db->connect_error;
} else {
    echo "✅ Database connected successfully<br>";
}

echo "<h2>Table Structure Check</h2>";

// Check if users table exists and has required columns
$query = "DESCRIBE users";
$result = $db->query($query);
if ($result) {
    echo "<h3>Users table structure:</h3>";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} else {
    echo "❌ Users table not found or error: " . $db->error . "<br>";
}

// Check if artwork table exists
$query = "DESCRIBE artwork";
$result = $db->query($query);
if ($result) {
    echo "<h3>Artwork table structure:</h3>";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} else {
    echo "❌ Artwork table not found or error: " . $db->error . "<br>";
}

// Check if artist_reviews table exists
$query = "DESCRIBE artist_reviews";
$result = $db->query($query);
if ($result) {
    echo "<h3>Artist_reviews table structure:</h3>";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} else {
    echo "❌ Artist_reviews table not found or error: " . $db->error . "<br>";
}

echo "<h2>Test Data Check</h2>";

// Check if artist with ID 17 exists
$query = "SELECT user_id, first_name, last_name, user_type, is_active FROM users WHERE user_id = 17";
$result = $db->query($query);
if ($result && $result->num_rows > 0) {
    echo "<h3>Artist ID 17 found:</h3>";
    $artist = $result->fetch_assoc();
    foreach ($artist as $key => $value) {
        echo "$key: $value<br>";
    }
} else {
    echo "❌ Artist with ID 17 not found<br>";
}

echo "<h2>API Test</h2>";
echo '<a href="API/getArtistInfo.php?artist_id=17" target="_blank">Test API Direct Link</a><br>';

$db->close();
?>
