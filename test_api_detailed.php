<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>API Debug Test</h2>";

try {
    require_once 'API/db.php';
    echo "✅ Database connection included successfully<br>";
    
    if ($db->connect_error) {
        echo "❌ Database connection error: " . $db->connect_error . "<br>";
    } else {
        echo "✅ Database connected successfully<br>";
    }
    
    // Test basic query
    $query = "SELECT COUNT(*) as count FROM users";
    $result = $db->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        echo "✅ Users table accessible, total users: " . $row['count'] . "<br>";
    } else {
        echo "❌ Error accessing users table: " . $db->error . "<br>";
    }
    
    // Test specific user query
    $query = "SELECT user_id, first_name, last_name, user_type FROM users WHERE user_id = 17";
    $result = $db->query($query);
    if ($result) {
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo "✅ User 17 found: " . $user['first_name'] . " " . $user['last_name'] . " (" . $user['user_type'] . ")<br>";
        } else {
            echo "❌ User 17 not found<br>";
        }
    } else {
        echo "❌ Error querying user 17: " . $db->error . "<br>";
    }
    
    // Test API directly
    echo "<h3>Testing API Function</h3>";
    
    $_GET['artist_id'] = 17;
    ob_start();
    include 'API/getArtistInfo.php';
    $output = ob_get_clean();
    
    echo "API Output:<br><pre>" . htmlentities($output) . "</pre>";
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
}
?>
