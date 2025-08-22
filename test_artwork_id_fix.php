<?php
// Test the fixed artwork submission
session_start();

// Check if user is logged in (simulate being logged in as artist_id = 17)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 17; // Set for testing
    $_SESSION['role'] = 'artist';
}

echo "<h2>Testing Artwork Submission with Multiple Images</h2>";

// Test data
$testData = [
    'title' => 'Test Artwork with Multiple Images',
    'price' => '250.00',
    'category' => 'painting',
    'description' => 'This is a test artwork with multiple images to verify correct artwork_id handling.',
    'style' => 'contemporary',
    'material' => 'acrylic on canvas',
    'width' => '30',
    'height' => '40',
    'year' => '2025'
];

echo "<h3>Test Data:</h3>";
echo "<pre>";
print_r($testData);
echo "</pre>";

echo "<h3>Instructions:</h3>";
echo "<p>1. Go to your artist portal</p>";
echo "<p>2. Add a new artwork with multiple images</p>";
echo "<p>3. Check the uploaded filenames - they should now contain the correct artwork_id instead of 0</p>";
echo "<p>4. Check the artwork_photos table to verify artwork_id is correct</p>";

echo "<h3>Check Database:</h3>";
require_once "API/db.php";

// Show recent artwork_photos entries
$sql = "SELECT ap.photo_id, ap.artwork_id, ap.image_path, ap.is_primary, a.title 
        FROM artwork_photos ap 
        LEFT JOIN artworks a ON ap.artwork_id = a.artwork_id 
        ORDER BY ap.photo_id DESC LIMIT 10";
$result = $db->query($sql);

if ($result) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th style='padding: 8px;'>Photo ID</th>";
    echo "<th style='padding: 8px;'>Artwork ID</th>";
    echo "<th style='padding: 8px;'>Image Path</th>";
    echo "<th style='padding: 8px;'>Is Primary</th>";
    echo "<th style='padding: 8px;'>Artwork Title</th>";
    echo "</tr>";
    
    while ($row = $result->fetch_assoc()) {
        $bgColor = $row['artwork_id'] == 0 ? '#ffcccc' : '#ccffcc'; // Red for artwork_id=0, green for valid
        echo "<tr style='background-color: {$bgColor};'>";
        echo "<td style='padding: 8px;'>" . $row['photo_id'] . "</td>";
        echo "<td style='padding: 8px; font-weight: bold;'>" . $row['artwork_id'] . "</td>";
        echo "<td style='padding: 8px; font-size: 12px;'>" . $row['image_path'] . "</td>";
        echo "<td style='padding: 8px;'>" . ($row['is_primary'] ? 'Yes' : 'No') . "</td>";
        echo "<td style='padding: 8px;'>" . $row['title'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "<p><strong>Legend:</strong> Red rows = artwork_id is 0 (problem), Green rows = valid artwork_id</p>";
} else {
    echo "Error: " . $db->error;
}
?>
