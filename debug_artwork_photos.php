<?php
// Debug artwork photos display
session_start();
require_once "API/db.php";

if (!isset($_GET['artwork_id'])) {
    die("Please provide artwork_id parameter");
}

$artwork_id = (int)$_GET['artwork_id'];

echo "<h2>Debug Artwork Photos for Artwork ID: $artwork_id</h2>";

// Check if artwork exists
$artworkSql = "SELECT artwork_id, title, artwork_image FROM artworks WHERE artwork_id = ?";
$stmt = $db->prepare($artworkSql);
$stmt->bind_param("i", $artwork_id);
$stmt->execute();
$artworkResult = $stmt->get_result();

if ($artworkResult->num_rows === 0) {
    die("Artwork not found");
}

$artwork = $artworkResult->fetch_assoc();
echo "<h3>Artwork Info:</h3>";
echo "<p><strong>Title:</strong> " . $artwork['title'] . "</p>";
echo "<p><strong>Primary Image:</strong> " . ($artwork['artwork_image'] ?: 'None') . "</p>";

// Check artwork_photos table
echo "<h3>Photos in artwork_photos table:</h3>";
$photosSql = "SELECT * FROM artwork_photos WHERE artwork_id = ? ORDER BY is_primary DESC, photo_id ASC";
$stmt = $db->prepare($photosSql);
$stmt->bind_param("i", $artwork_id);
$stmt->execute();
$photosResult = $stmt->get_result();

if ($photosResult->num_rows === 0) {
    echo "<p style='color: red;'>No photos found in artwork_photos table for this artwork.</p>";
} else {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th style='padding: 8px;'>Photo ID</th>";
    echo "<th style='padding: 8px;'>Image Path</th>";
    echo "<th style='padding: 8px;'>Is Primary</th>";
    echo "<th style='padding: 8px;'>File Exists</th>";
    echo "<th style='padding: 8px;'>Preview</th>";
    echo "</tr>";
    
    while ($photo = $photosResult->fetch_assoc()) {
        $filePath = "/Applications/XAMPP/xamppfiles/htdocs/uploads/artworks/" . $photo['image_path'];
        $fileExists = file_exists($filePath);
        $webPath = "/uploads/artworks/" . $photo['image_path'];
        
        echo "<tr>";
        echo "<td style='padding: 8px;'>" . $photo['photo_id'] . "</td>";
        echo "<td style='padding: 8px; font-size: 12px;'>" . $photo['image_path'] . "</td>";
        echo "<td style='padding: 8px;'>" . ($photo['is_primary'] ? 'Yes' : 'No') . "</td>";
        echo "<td style='padding: 8px; color: " . ($fileExists ? 'green' : 'red') . ";'>" . ($fileExists ? 'Yes' : 'No') . "</td>";
        echo "<td style='padding: 8px;'>";
        if ($fileExists) {
            echo "<img src='$webPath' style='width: 100px; height: 100px; object-fit: cover;' alt='Preview'>";
        } else {
            echo "No preview";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Test the API directly
echo "<h3>API Response Test:</h3>";
echo "<p>Testing: <code>/API/getArtworkInfo.php?id=$artwork_id</code></p>";

$apiUrl = "http://localhost/API/getArtworkInfo.php?id=$artwork_id";
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

if ($data && isset($data['success']) && $data['success']) {
    echo "<p style='color: green;'>✅ API call successful</p>";
    if (isset($data['data']['photos'])) {
        echo "<p><strong>Photos returned by API:</strong> " . count($data['data']['photos']) . "</p>";
        echo "<pre>";
        print_r($data['data']['photos']);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>❌ No photos field in API response</p>";
    }
} else {
    echo "<p style='color: red;'>❌ API call failed</p>";
    echo "<pre>$response</pre>";
}

echo "<h3>Instructions:</h3>";
echo "<p>1. If photos exist in database but files don't exist: The image upload process failed</p>";
echo "<p>2. If no photos in database: The insertion into artwork_photos table failed</p>";
echo "<p>3. If API returns empty photos: Check the getArtworkInfo.php function</p>";
?>

<style>
table { font-family: Arial, sans-serif; }
pre { background: #f5f5f5; padding: 10px; border-radius: 4px; }
</style>
