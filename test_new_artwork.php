<?php
// Test to see the latest artworks and their images
echo "<h2>Latest Artworks Test</h2>";

// Get the most recent artworks
$api_url = 'http://localhost/API/getAllArtworks.php?limit=10&sort_by=created_at&sort_order=DESC';
$response = file_get_contents($api_url);

if ($response === FALSE) {
    echo "Error fetching API data";
} else {
    $data = json_decode($response, true);
    if ($data && isset($data['data'])) {
        echo "<h3>Most Recent Artworks:</h3>";
        foreach ($data['data'] as $artwork) {
            echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px; display: inline-block; width: 300px;'>";
            echo "<h4>ID: " . $artwork['artwork_id'] . " - " . htmlspecialchars($artwork['title']) . "</h4>";
            echo "<p><strong>Artist:</strong> " . htmlspecialchars($artwork['artist']['full_name']) . "</p>";
            echo "<p><strong>artwork_image:</strong> " . ($artwork['artwork_image'] ?? 'NULL') . "</p>";
            echo "<p><strong>artwork_photo:</strong> " . ($artwork['artwork_photo'] ?? 'NULL') . "</p>";
            echo "<p><strong>image_src:</strong> " . ($artwork['image_src'] ?? 'NULL') . "</p>";
            echo "<p><strong>image_missing:</strong> " . (isset($artwork['image_missing']) ? ($artwork['image_missing'] ? 'true' : 'false') : 'false') . "</p>";
            
            if (isset($artwork['image_src'])) {
                echo "<div>";
                echo "<img src='" . $artwork['image_src'] . "' style='max-width: 250px; max-height: 200px; border: 1px solid #ddd;' onerror='this.style.border=\"2px solid red\"; this.alt=\"Image not found\"'>";
                echo "</div>";
            }
            echo "</div>";
        }
    } else {
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}

echo "<hr>";
echo "<h3>Recent files in uploads/artworks directory:</h3>";

// Check what files are in the uploads/artworks directory
$upload_dir = __DIR__ . '/uploads/artworks/';
if (is_dir($upload_dir)) {
    $files = scandir($upload_dir);
    $files = array_diff($files, array('.', '..'));
    
    // Sort by modification time (newest first)
    usort($files, function($a, $b) use ($upload_dir) {
        return filemtime($upload_dir . $b) - filemtime($upload_dir . $a);
    });
    
    echo "<ul>";
    foreach (array_slice($files, 0, 10) as $file) {
        $file_time = date('Y-m-d H:i:s', filemtime($upload_dir . $file));
        echo "<li><strong>$file</strong> (modified: $file_time)</li>";
    }
    echo "</ul>";
} else {
    echo "Directory not found: $upload_dir";
}
?>
