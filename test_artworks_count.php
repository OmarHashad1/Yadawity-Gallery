<?php
require_once 'API/db.php';

echo "<h2>Artworks Count Test for User 17</h2>";

// Check if artworks table exists
$table_check = $db->query("SHOW TABLES LIKE 'artworks'");
if ($table_check && $table_check->num_rows > 0) {
    echo "✅ Artworks table exists<br><br>";
    
    // Get total count for user 17
    $query = "SELECT COUNT(*) as count FROM artworks WHERE artist_id = 17";
    $result = $db->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<strong>Total artworks for artist ID 17: " . $row['count'] . "</strong><br><br>";
    }
    
    // Show some artwork details
    $query = "SELECT artwork_id, title, artist_id FROM artworks WHERE artist_id = 17 LIMIT 5";
    $result = $db->query($query);
    if ($result && $result->num_rows > 0) {
        echo "<h3>Sample artworks:</h3>";
        while ($row = $result->fetch_assoc()) {
            echo "ID: " . $row['artwork_id'] . " - Title: " . $row['title'] . " - Artist ID: " . $row['artist_id'] . "<br>";
        }
    }
} else {
    echo "❌ Artworks table does not exist<br>";
}

$db->close();
?>
