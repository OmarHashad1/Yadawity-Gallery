<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Cookie Debug Test</h2>";

echo "<h3>All Cookies:</h3>";
foreach ($_COOKIE as $name => $value) {
    echo "$name: $value<br>";
}

echo "<h3>User Login Cookie Analysis:</h3>";
if (isset($_COOKIE['user_login'])) {
    $cookie_value = $_COOKIE['user_login'];
    echo "Cookie value: $cookie_value<br>";
    
    $parts = explode('_', $cookie_value);
    echo "Parts after splitting by '_': <br>";
    foreach ($parts as $i => $part) {
        echo "Part $i: $part<br>";
    }
    
    if (count($parts) > 0 && is_numeric($parts[0])) {
        $user_id = (int)$parts[0];
        echo "<strong>Extracted User ID: $user_id</strong><br>";
    } else {
        echo "❌ Could not extract numeric user ID<br>";
    }
} else {
    echo "❌ user_login cookie not found<br>";
}

echo "<h3>Test API with Cookie:</h3>";
echo '<a href="API/getArtistInfo.php" target="_blank">Test API (should use cookie)</a><br>';
?>
