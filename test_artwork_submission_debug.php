<?php
// Test script to debug artwork submission
session_start();

// Check what's being sent in the POST request
echo "<h2>POST Data Debug</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>FILES Data Debug</h2>";
echo "<pre>";
print_r($_FILES);
echo "</pre>";

echo "<h2>Session Data</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Check artwork_photos table
require_once "API/db.php";

echo "<h2>Recent artwork_photos entries</h2>";
$sql = "SELECT * FROM artwork_photos ORDER BY photo_id DESC LIMIT 10";
$result = $db->query($sql);

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>photo_id</th><th>artwork_id</th><th>image_path</th><th>is_primary</th><th>created_at</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['photo_id'] . "</td>";
        echo "<td>" . $row['artwork_id'] . "</td>";
        echo "<td>" . $row['image_path'] . "</td>";
        echo "<td>" . $row['is_primary'] . "</td>";
        echo "<td>" . ($row['created_at'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "Error: " . $db->error;
}

echo "<h2>Recent artworks entries</h2>";
$sql = "SELECT artwork_id, title, artist_id, artwork_image FROM artworks ORDER BY artwork_id DESC LIMIT 5";
$result = $db->query($sql);

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>artwork_id</th><th>title</th><th>artist_id</th><th>artwork_image</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['artwork_id'] . "</td>";
        echo "<td>" . $row['title'] . "</td>";
        echo "<td>" . $row['artist_id'] . "</td>";
        echo "<td>" . $row['artwork_image'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "Error: " . $db->error;
}
?>
