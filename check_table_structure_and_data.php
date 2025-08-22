<?php
require_once 'API/db.php';

try {
    // Check table structure
    $result = $db->query("SHOW COLUMNS FROM users");
    
    echo "Columns in users table:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    echo "\nLet's also check what user IDs exist:\n";
    $users = $db->query("SELECT * FROM users LIMIT 5");
    while ($user = $users->fetch_assoc()) {
        echo "User: ";
        foreach ($user as $key => $value) {
            echo "$key=$value, ";
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    if (isset($db)) {
        $db->close();
    }
}
?>
