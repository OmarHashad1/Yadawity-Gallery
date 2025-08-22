<?php
// Test database tables and columns
include 'API/db.php';
$conn = $db;

echo "<h2>Database Tables Check</h2>";

// Check if artist_reviews table exists
$result = $conn->query("SHOW TABLES LIKE 'artist_reviews'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✅ artist_reviews table exists</p>";
    
    // Show table structure
    echo "<h3>artist_reviews table structure:</h3>";
    $structure = $conn->query("DESCRIBE artist_reviews");
    if ($structure) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $structure->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p style='color: red;'>❌ artist_reviews table does NOT exist</p>";
    
    // Check what tables do exist
    echo "<h3>Available tables:</h3>";
    $tables = $conn->query("SHOW TABLES");
    if ($tables) {
        while ($row = $tables->fetch_array()) {
            echo "- " . $row[0] . "<br>";
        }
    }
}

// Check if reviews table exists (the one from the image)
echo "<br><h3>Checking 'reviews' table:</h3>";
$result = $conn->query("SHOW TABLES LIKE 'reviews'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✅ reviews table exists</p>";
    
    // Show table structure
    echo "<h3>reviews table structure:</h3>";
    $structure = $conn->query("DESCRIBE reviews");
    if ($structure) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $structure->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p style='color: red;'>❌ reviews table does NOT exist</p>";
}

$conn->close();
?>
