<?php
// Test to verify all placeholder images have been removed
echo "<h2>Testing Artwork Display Without Placeholders</h2>";

$api_url = 'http://localhost/API/getAllArtworks.php?limit=10';
$response = file_get_contents($api_url);

if ($response === FALSE) {
    echo "Error fetching API data";
} else {
    $data = json_decode($response, true);
    if ($data && isset($data['data'])) {
        echo "<h3>Artworks (No Placeholders):</h3>";
        echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;'>";
        
        foreach ($data['data'] as $artwork) {
            echo "<div style='border: 1px solid #ddd; padding: 15px; background: white;'>";
            echo "<h4>" . htmlspecialchars($artwork['title']) . " (ID: " . $artwork['artwork_id'] . ")</h4>";
            
            // Show image info
            echo "<p><strong>Has image_src:</strong> " . (isset($artwork['image_src']) && !empty($artwork['image_src']) ? 'YES' : 'NO') . "</p>";
            if (isset($artwork['image_src']) && !empty($artwork['image_src'])) {
                echo "<p><strong>Image path:</strong> " . $artwork['image_src'] . "</p>";
                echo "<div style='text-align: center; margin: 10px 0;'>";
                echo "<img src='" . $artwork['image_src'] . "' style='max-width: 200px; max-height: 150px; border: 1px solid #ccc;' onerror='this.style.display=\"none\"; this.nextElementSibling.style.display=\"block\";'>";
                echo "<div style='display: none; padding: 20px; background: #f8f9fa; border: 2px dashed #ddd; color: #999;'>Image not found</div>";
                echo "</div>";
            } else {
                echo "<div style='text-align: center; margin: 10px 0; padding: 20px; background: #f8f9fa; border: 2px dashed #ddd; color: #999;'>";
                echo "No image available";
                echo "</div>";
            }
            
            if (isset($artwork['debug_info'])) {
                echo "<p><small><strong>Debug:</strong> " . $artwork['debug_info'] . "</small></p>";
            }
            
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}
?>
