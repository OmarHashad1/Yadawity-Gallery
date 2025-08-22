<?php
// Simple diagnostic to check table structure
include 'API/db.php';
$conn = $db;

echo "<h2>Database Diagnostic</h2>";

// Check what tables exist
echo "<h3>Available Tables:</h3>";
$tables = $conn->query("SHOW TABLES");
if ($tables) {
    while ($row = $tables->fetch_array()) {
        echo "- " . $row[0] . "<br>";
    }
}

echo "<br><h3>Reviews Table Structure (if exists):</h3>";
$result = $conn->query("SHOW TABLES LIKE 'reviews'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✅ reviews table exists</p>";
    
    $structure = $conn->query("DESCRIBE reviews");
    if ($structure) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $structure->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='font-weight: bold; color: blue;'>" . $row['Field'] . "</td>";
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
