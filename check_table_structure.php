<?php
require_once "API/db.php";

echo "<h2>Artworks Table Structure:</h2>";

// Check table structure
$result = $db->query("DESCRIBE artworks");
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . $db->error;
}

echo "<h2>Sample Artwork Data:</h2>";

// Check actual data
$result = $db->query("SELECT artwork_id, title, artwork_image, artwork_photo FROM artworks LIMIT 5");
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>artwork_id</th><th>title</th><th>artwork_image</th><th>artwork_photo</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['artwork_id'] . "</td>";
        echo "<td>" . $row['title'] . "</td>";
        echo "<td>" . ($row['artwork_image'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['artwork_photo'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . $db->error;
}

$db->close();
?>
