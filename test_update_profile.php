<?php
// Test the updateArtistProfile API
header('Content-Type: text/html; charset=UTF-8');

echo "<h2>Testing Update Profile API</h2>";

// Test 1: Check if API endpoint exists
echo "<h3>Test 1: API Endpoint Check</h3>";
$api_file = './API/updateArtistProfile.php';
if (file_exists($api_file)) {
    echo "✅ API file exists<br>";
} else {
    echo "❌ API file NOT found at: $api_file<br>";
}

// Test 2: Check database connection
echo "<h3>Test 2: Database Connection</h3>";
try {
    require_once './API/db.php';
    if ($db) {
        echo "✅ Database connection successful<br>";
        
        // Check if users table exists
        $result = $db->query("SHOW TABLES LIKE 'users'");
        if ($result && $result->num_rows > 0) {
            echo "✅ Users table exists<br>";
        } else {
            echo "❌ Users table NOT found<br>";
        }
        
        // Check for artist users
        $result = $db->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'artist'");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "✅ Found {$row['count']} artist users<br>";
        }
        
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 3: Check cookie
echo "<h3>Test 3: Cookie Check</h3>";
if (isset($_COOKIE['user_login'])) {
    $cookie_value = $_COOKIE['user_login'];
    echo "✅ Cookie found: $cookie_value<br>";
    
    $parts = explode('_', $cookie_value);
    if (count($parts) > 0 && is_numeric($parts[0])) {
        $user_id = (int)$parts[0];
        echo "✅ Extracted user ID: $user_id<br>";
        
        // Check if this user exists in database
        if (isset($db)) {
            $stmt = $db->prepare("SELECT user_id, username, email, user_type FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                echo "✅ User found in database:<br>";
                echo "&nbsp;&nbsp;ID: {$user['user_id']}<br>";
                echo "&nbsp;&nbsp;Username: {$user['username']}<br>";
                echo "&nbsp;&nbsp;Email: {$user['email']}<br>";
                echo "&nbsp;&nbsp;Type: {$user['user_type']}<br>";
            } else {
                echo "❌ User ID $user_id NOT found in database<br>";
            }
            $stmt->close();
        }
    } else {
        echo "❌ Invalid cookie format<br>";
    }
} else {
    echo "❌ No user_login cookie found<br>";
    echo "Available cookies: " . implode(', ', array_keys($_COOKIE)) . "<br>";
}

// Test 4: Test API call directly
echo "<h3>Test 4: Direct API Test</h3>";
if (isset($user_id)) {
    echo "<form method='post' action='test_update_profile.php'>";
    echo "<input type='hidden' name='test_api' value='1'>";
    echo "<input type='hidden' name='user_id' value='$user_id'>";
    echo "<button type='submit'>Test Update API</button>";
    echo "</form>";
}

// Handle API test
if (isset($_POST['test_api']) && isset($_POST['user_id'])) {
    echo "<h4>API Test Result:</h4>";
    
    $test_data = [
        'artist_bio' => 'Test bio update at ' . date('Y-m-d H:i:s'),
        'location' => 'Test location'
    ];
    
    // Simulate the API call
    $postData = json_encode($test_data);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/API/updateArtistProfile.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postData)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, 'user_login=' . $_COOKIE['user_login']);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo "HTTP Code: $httpCode<br>";
    echo "Response: <pre>$response</pre>";
    
    curl_close($ch);
}

if (isset($db)) {
    $db->close();
}
?>
