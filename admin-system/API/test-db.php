<?php
// Test script to verify database connection and user data
header('Content-Type: text/html');

echo "<h1>Database Connection Test</h1>";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=yadawity', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test user query
    $stmt = $pdo->prepare('SELECT user_id, email, password, user_type, first_name, last_name, is_active FROM users WHERE email = ?');
    $stmt->execute(['rawysalim3@gmail.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<h2>User Data Found:</h2>";
        echo "<pre>";
        print_r($user);
        echo "</pre>";
        
        // Test password verification
        $testPassword = 'your_actual_password_here'; // Replace with actual password
        echo "<h3>Password Verification Test:</h3>";
        echo "<p>Testing password: " . htmlspecialchars($testPassword) . "</p>";
        
        if (password_verify($testPassword, $user['password'])) {
            echo "<p style='color: green;'>✅ Password verification successful!</p>";
        } else {
            echo "<p style='color: red;'>❌ Password verification failed!</p>";
            echo "<p>Hash in DB: " . htmlspecialchars($user['password']) . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ User not found!</p>";
    }
    
    // Show all users
    echo "<h2>All Users in Database:</h2>";
    $stmt = $pdo->query('SELECT user_id, email, user_type, first_name, last_name, is_active FROM users LIMIT 10');
    $users = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Email</th><th>Type</th><th>Name</th><th>Active</th></tr>";
    foreach ($users as $u) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($u['user_id']) . "</td>";
        echo "<td>" . htmlspecialchars($u['email']) . "</td>";
        echo "<td>" . htmlspecialchars($u['user_type']) . "</td>";
        echo "<td>" . htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) . "</td>";
        echo "<td>" . ($u['is_active'] ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

