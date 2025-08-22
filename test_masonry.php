<?php
// Test the masonry layout with different sized images
echo "<h2>Testing Masonry Layout</h2>";

$api_url = 'http://localhost/API/getAllArtworks.php?limit=12';
$response = file_get_contents($api_url);

if ($response === FALSE) {
    echo "Error fetching API data";
} else {
    $data = json_decode($response, true);
    if ($data && isset($data['data'])) {
        echo "<style>
        .test-grid {
            columns: 3;
            column-gap: 30px;
            margin: 20px;
        }
        .test-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            break-inside: avoid;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        .test-image {
            width: 100%;
            height: auto;
            display: block;
        }
        .test-info {
            padding: 15px;
        }
        @media (max-width: 768px) {
            .test-grid { columns: 1; }
        }
        @media (min-width: 769px) and (max-width: 1024px) {
            .test-grid { columns: 2; }
        }
        </style>";
        
        echo "<h3>Masonry Layout Preview:</h3>";
        echo "<div class='test-grid'>";
        
        foreach ($data['data'] as $artwork) {
            echo "<div class='test-card'>";
            
            if (isset($artwork['image_src']) && !empty($artwork['image_src'])) {
                echo "<img src='" . $artwork['image_src'] . "' class='test-image' alt='" . htmlspecialchars($artwork['title']) . "' onerror='this.style.display=\"none\"'>";
            } else {
                echo "<div style='height: 200px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; color: #999;'>No image</div>";
            }
            
            echo "<div class='test-info'>";
            echo "<h4 style='margin: 0 0 10px 0; font-size: 1.1rem;'>" . htmlspecialchars($artwork['title']) . "</h4>";
            echo "<p style='margin: 0; color: #666; font-size: 0.9rem;'>" . htmlspecialchars($artwork['artist']['display_name']) . "</p>";
            echo "<p style='margin: 5px 0 0 0; font-weight: bold; color: #2c3e50;'>" . htmlspecialchars($artwork['formatted_price']) . "</p>";
            echo "</div>";
            echo "</div>";
        }
        
        echo "</div>";
        
        echo "<p><strong>Note:</strong> This preview shows how the masonry layout will look. Images maintain their aspect ratio and cards adjust height accordingly.</p>";
    }
}
?>
