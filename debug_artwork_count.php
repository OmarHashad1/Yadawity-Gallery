<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'API/db.php';

echo "<h2>Artwork Count Debug for User ID 17</h2>";

// Check what tables exist
echo "<h3>Available Tables:</h3>";
$tables_query = "SHOW TABLES";
$result = $db->query($tables_query);
if ($result) {
    while ($row = $result->fetch_array()) {
        echo "- " . $row[0] . "<br>";
    }
} else {
    echo "Error getting tables: " . $db->error . "<br>";
}

// Check if artwork table exists and its structure
echo "<h3>Artwork Table Check:</h3>";
$artwork_check = $db->query("SHOW TABLES LIKE 'artwork'");
if ($artwork_check && $artwork_check->num_rows > 0) {
    echo "✅ Artwork table exists<br>";
    
    // Check table structure
    echo "<h4>Artwork Table Structure:</h4>";
    $structure = $db->query("DESCRIBE artwork");
    if ($structure) {
        while ($row = $structure->fetch_assoc()) {
            echo $row['Field'] . " - " . $row['Type'] . "<br>";
        }
    }
    
    // Count all artworks
    $total_count = $db->query("SELECT COUNT(*) as total FROM artwork");
    if ($total_count) {
        $total = $total_count->fetch_assoc();
        echo "<h4>Total artworks in table: " . $total['total'] . "</h4>";
    }
    
    // Check artworks for user 17
    echo "<h4>Artworks for User ID 17:</h4>";
    $user_artworks = $db->query("SELECT * FROM artwork WHERE artist_id = 17");
    if ($user_artworks) {
        if ($user_artworks->num_rows > 0) {
            echo "Found " . $user_artworks->num_rows . " artworks for user 17:<br>";
            while ($artwork = $user_artworks->fetch_assoc()) {
                echo "- Artwork ID: " . $artwork['artwork_id'] . 
                     ", Title: " . ($artwork['title'] ?? 'No title') . 
                     ", Available: " . ($artwork['is_available'] ?? 'N/A') . "<br>";
            }
        } else {
            echo "No artworks found for user 17<br>";
        }
    } else {
        echo "Error querying artworks: " . $db->error . "<br>";
    }
    
    // Check different possible column names
    echo "<h4>Testing Different Column Names:</h4>";
    $columns_to_test = ['artist_id', 'user_id', 'creator_id', 'artist_user_id'];
    foreach ($columns_to_test as $column) {
        $test_query = "SELECT COUNT(*) as count FROM artwork WHERE $column = 17";
        $test_result = $db->query($test_query);
        if ($test_result) {
            $count = $test_result->fetch_assoc();
            echo "Using $column: " . $count['count'] . " artworks<br>";
        } else {
            echo "Column $column doesn't exist or error: " . $db->error . "<br>";
        }
    }
    
} else {
    echo "❌ Artwork table does not exist<br>";
    
    // Check for similar table names
    echo "<h4>Looking for similar table names:</h4>";
    $similar_tables = $db->query("SHOW TABLES LIKE '%art%'");
    if ($similar_tables && $similar_tables->num_rows > 0) {
        while ($row = $similar_tables->fetch_array()) {
            echo "- " . $row[0] . "<br>";
        }
    } else {
        echo "No tables with 'art' in the name found<br>";
    }
}

$db->close();
?>
