<?php
// Test script to check gallery_photos table structure and data
require_once './API/db.php';

echo "<h1>Gallery Photos Test</h1>";

// Check if gallery_photos table exists and its structure
echo "<h2>Table Structure:</h2>";
$result = $conn->query("DESCRIBE gallery_photos");
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . $conn->error;
}

// Check recent gallery_photos data
echo "<h2>Recent Gallery Photos:</h2>";
$result = $conn->query("SELECT gp.*, g.title as gallery_title 
                       FROM gallery_photos gp 
                       LEFT JOIN galleries g ON gp.gallery_id = g.id 
                       ORDER BY gp.id DESC 
                       LIMIT 10");
if ($result) {
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Gallery ID</th><th>Gallery Title</th><th>Image Path</th><th>Is Primary</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['gallery_id'] . "</td>";
            echo "<td>" . $row['gallery_title'] . "</td>";
            echo "<td>" . $row['image_path'] . "</td>";
            echo "<td>" . ($row['is_primary'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No gallery photos found.";
    }
} else {
    echo "Error: " . $conn->error;
}

// Check galleries with their primary images
echo "<h2>Galleries with Primary Images:</h2>";
$result = $conn->query("SELECT id, title, img, gallery_type 
                       FROM galleries 
                       ORDER BY id DESC 
                       LIMIT 10");
if ($result) {
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Title</th><th>Primary Image (img)</th><th>Type</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['title'] . "</td>";
            echo "<td>" . $row['img'] . "</td>";
            echo "<td>" . $row['gallery_type'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No galleries found.";
    }
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
