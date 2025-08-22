<?php
require_once './API/db.php';

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>Database Structure Check</h2>";

try {
    // Check users table structure
    echo "<h3>Users Table Structure:</h3>";
    $result = $db->query("SHOW COLUMNS FROM users");
    
    if ($result) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check specifically for phone columns
        echo "<h4>Phone-related columns:</h4>";
        $phone_check = $db->query("SHOW COLUMNS FROM users LIKE '%phone%'");
        if ($phone_check && $phone_check->num_rows > 0) {
            while ($row = $phone_check->fetch_assoc()) {
                echo "✅ Found: " . $row['Field'] . " (" . $row['Type'] . ")<br>";
            }
        } else {
            echo "❌ No phone columns found<br>";
        }
        
    } else {
        echo "❌ Could not retrieve table structure: " . $db->error;
    }
    
    // Test a sample user query
    echo "<h3>Sample User Data (first user):</h3>";
    $sample = $db->query("SELECT * FROM users LIMIT 1");
    if ($sample && $sample->num_rows > 0) {
        $user = $sample->fetch_assoc();
        echo "<pre>";
        foreach ($user as $key => $value) {
            echo htmlspecialchars($key) . ": " . htmlspecialchars($value) . "\n";
        }
        echo "</pre>";
    } else {
        echo "No users found or query failed.";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

if (isset($db)) {
    $db->close();
}
?>
