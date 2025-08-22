<?php
require_once 'API/db.php';

echo "<h1>Database Connection Test</h1>";

try {
    echo "<p>✅ Database connected successfully</p>";
    echo "<p>Database: " . $db->get_server_info() . "</p>";
    
    // Test tables exist
    $tables = ['artworks', 'artwork_photos', 'users'];
    foreach ($tables as $table) {
        $result = $db->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "<p>✅ Table '$table' exists</p>";
        } else {
            echo "<p>❌ Table '$table' missing</p>";
        }
    }
    
    // Test artwork table structure
    echo "<h3>Artworks Table Structure:</h3>";
    $result = $db->query("DESCRIBE artworks");
    if ($result) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li><strong>" . $row['Field'] . "</strong>: " . $row['Type'] . "</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}
?>
