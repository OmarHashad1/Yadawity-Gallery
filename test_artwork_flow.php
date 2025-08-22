<?php
// Test the exact flow you described:
// 1. Get artwork_id
// 2. Look up filename in artwork_photos table  
// 3. Render from uploads/artworks folder

echo "<h2>Testing Artwork Image Flow: artwork_id → artwork_photos → uploads/artworks</h2>";

$api_url = 'http://localhost/API/getAllArtworks.php?limit=5';
$response = file_get_contents($api_url);

if ($response === FALSE) {
    echo "Error fetching API data";
} else {
    $data = json_decode($response, true);
    if ($data && isset($data['data'])) {
        echo "<h3>Flow Test Results:</h3>";
        foreach ($data['data'] as $artwork) {
            echo "<div style='border: 2px solid #333; margin: 15px; padding: 15px; background: #f9f9f9;'>";
            
            // Step 1: Show artwork_id
            echo "<h4>Step 1: Artwork ID = " . $artwork['artwork_id'] . "</h4>";
            echo "<p><strong>Title:</strong> " . htmlspecialchars($artwork['title']) . "</p>";
            
            // Step 2: Show filename from artwork_photos table
            echo "<h4>Step 2: Filename from artwork_photos table</h4>";
            echo "<p><strong>artwork_photo_filename:</strong> " . ($artwork['artwork_photo_filename'] ?? 'NULL') . "</p>";
            echo "<p><strong>Fallback (artwork_image):</strong> " . ($artwork['artwork_image'] ?? 'NULL') . "</p>";
            
            // Step 3: Show path to uploads/artworks
            echo "<h4>Step 3: Path to uploads/artworks folder</h4>";
            echo "<p><strong>Generated image_src:</strong> " . ($artwork['image_src'] ?? 'NULL') . "</p>";
            echo "<p><strong>Image missing:</strong> " . (isset($artwork['image_missing']) ? ($artwork['image_missing'] ? 'YES' : 'NO') : 'UNKNOWN') . "</p>";
            
            if (isset($artwork['debug_info'])) {
                echo "<p><strong>Debug:</strong> " . $artwork['debug_info'] . "</p>";
            }
            
            // Step 4: Show the actual image
            echo "<h4>Step 4: Rendered Image</h4>";
            if (isset($artwork['image_src'])) {
                echo "<div style='text-align: center; padding: 10px; background: white; border: 1px solid #ddd;'>";
                echo "<img src='" . $artwork['image_src'] . "' style='max-width: 200px; max-height: 150px;' onerror='this.style.border=\"3px solid red\"; this.alt=\"❌ Image not found: " . $artwork['image_src'] . "\"'>";
                echo "<br><small>Path: " . $artwork['image_src'] . "</small>";
                echo "</div>";
            }
            
            echo "<hr style='margin: 10px 0;'>";
            echo "</div>";
        }
    } else {
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}

// Additional verification - show actual database relationships
echo "<hr><h3>Database Verification</h3>";
require_once "API/db.php";

$result = $db->query("
    SELECT 
        a.artwork_id, 
        a.title, 
        a.artwork_image,
        ap.image_path as photo_filename,
        ap.photo_id
    FROM artworks a 
    LEFT JOIN artwork_photos ap ON a.artwork_id = ap.artwork_id 
    ORDER BY a.artwork_id DESC 
    LIMIT 5
");

if ($result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #eee;'><th>Artwork ID</th><th>Title</th><th>artwork_image (fallback)</th><th>photo_filename (primary)</th><th>Photo ID</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['artwork_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . ($row['artwork_image'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['photo_filename'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['photo_id'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

$db->close();
?>
