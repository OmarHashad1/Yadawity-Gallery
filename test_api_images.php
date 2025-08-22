<?php
// Test the updated API
$api_url = 'http://localhost/API/getAllArtworks.php?limit=5';
$response = file_get_contents($api_url);

if ($response === FALSE) {
    echo "Error fetching API data";
} else {
    $data = json_decode($response, true);
    if ($data && isset($data['artworks'])) {
        echo "<h2>API Test Results:</h2>";
        foreach ($data['artworks'] as $artwork) {
            echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
            echo "<h3>Artwork ID: " . $artwork['artwork_id'] . " - " . $artwork['title'] . "</h3>";
            echo "<p><strong>artwork_image:</strong> " . ($artwork['artwork_image'] ?? 'NULL') . "</p>";
            echo "<p><strong>artwork_photo:</strong> " . ($artwork['artwork_photo'] ?? 'NULL') . "</p>";
            echo "<p><strong>image_src:</strong> " . ($artwork['image_src'] ?? 'NULL') . "</p>";
            echo "<p><strong>image_missing:</strong> " . (isset($artwork['image_missing']) ? ($artwork['image_missing'] ? 'true' : 'false') : 'false') . "</p>";
            if (isset($artwork['image_src'])) {
                echo "<img src='" . $artwork['image_src'] . "' style='max-width: 200px; max-height: 200px;' onerror='this.style.border=\"2px solid red\"'>";
            }
            echo "</div>";
        }
    } else {
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}
?>
