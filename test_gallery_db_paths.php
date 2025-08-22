<?php
// Test script to check actual image paths in database
require_once './API/db.php';

echo "<h1>Gallery Images Database Check</h1>";

// Check galleries table for primary images
echo "<h2>Primary Images in Galleries Table:</h2>";
$result = $conn->query("SELECT id, title, img, gallery_type 
                       FROM galleries 
                       WHERE img IS NOT NULL 
                       ORDER BY id DESC 
                       LIMIT 10");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Gallery ID</th><th>Title</th><th>Primary Image Path</th><th>Type</th><th>File Exists</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $imagePath = $row['img'];
        $fullPath = dirname(__DIR__) . '/' . $imagePath;
        $fileExists = file_exists($fullPath) ? 'Yes' : 'No';
        
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($imagePath) . "</td>";
        echo "<td>" . $row['gallery_type'] . "</td>";
        echo "<td style='color: " . ($fileExists === 'Yes' ? 'green' : 'red') . "'>" . $fileExists . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No galleries with primary images found.</p>";
}

// Check gallery_photos table
echo "<h2>All Images in Gallery_Photos Table:</h2>";
$result = $conn->query("SELECT gp.*, g.title as gallery_title 
                       FROM gallery_photos gp 
                       LEFT JOIN galleries g ON gp.gallery_id = g.id 
                       ORDER BY gp.id DESC 
                       LIMIT 20");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Photo ID</th><th>Gallery ID</th><th>Gallery Title</th><th>Image Path</th><th>Is Primary</th><th>File Exists</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $imagePath = $row['image_path'];
        $fullPath = dirname(__DIR__) . '/' . $imagePath;
        $fileExists = file_exists($fullPath) ? 'Yes' : 'No';
        
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['gallery_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['gallery_title'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($imagePath) . "</td>";
        echo "<td style='color: " . ($row['is_primary'] ? 'blue' : 'black') . "'>" . ($row['is_primary'] ? 'Yes' : 'No') . "</td>";
        echo "<td style='color: " . ($fileExists === 'Yes' ? 'green' : 'red') . "'>" . $fileExists . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No gallery photos found.</p>";
}

// Count primary images
echo "<h2>Statistics:</h2>";
$result = $conn->query("SELECT COUNT(*) as total_galleries FROM galleries WHERE img IS NOT NULL");
$galleryCount = $result ? $result->fetch_assoc()['total_galleries'] : 0;

$result = $conn->query("SELECT COUNT(*) as total_photos FROM gallery_photos");
$photoCount = $result ? $result->fetch_assoc()['total_photos'] : 0;

$result = $conn->query("SELECT COUNT(*) as primary_photos FROM gallery_photos WHERE is_primary = 1");
$primaryCount = $result ? $result->fetch_assoc()['primary_photos'] : 0;

echo "<ul>";
echo "<li><strong>Galleries with primary images:</strong> $galleryCount</li>";
echo "<li><strong>Total gallery photos:</strong> $photoCount</li>";
echo "<li><strong>Primary photos in gallery_photos:</strong> $primaryCount</li>";
echo "</ul>";

$conn->close();
?>
