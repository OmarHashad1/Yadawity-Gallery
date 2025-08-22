<?php
require_once 'API/db.php';

echo "<h2>Quick Artwork Count Test</h2>";

// Test both table names
$tables_to_test = ['artwork', 'artworks'];

foreach ($tables_to_test as $table_name) {
    echo "<h3>Testing table: $table_name</h3>";
    
    $check_table = $db->query("SHOW TABLES LIKE '$table_name'");
    if ($check_table && $check_table->num_rows > 0) {
        echo "✅ Table $table_name exists<br>";
        
        // Count for user 17
        $count_query = "SELECT COUNT(*) as count FROM $table_name WHERE artist_id = 17";
        $result = $db->query($count_query);
        if ($result) {
            $count = $result->fetch_assoc();
            echo "Artworks for user 17: " . $count['count'] . "<br>";
        } else {
            echo "Error counting: " . $db->error . "<br>";
        }
    } else {
        echo "❌ Table $table_name does not exist<br>";
    }
}

// Test the API
echo "<h3>Test API Response:</h3>";
$api_url = "http://localhost/API/getArtistInfo.php";
$response = file_get_contents($api_url);
echo "<pre>" . htmlentities($response) . "</pre>";
?>
