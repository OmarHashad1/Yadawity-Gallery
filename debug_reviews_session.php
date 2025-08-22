<?php
session_start();

// Set test session like artistPortal.php does
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_name'] = 'Test Artist';
}

// Debug session
echo "Session Debug:\n";
echo "Session ID: " . session_id() . "\n";
echo "Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET') . "\n";
echo "Session user_name: " . (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'NOT SET') . "\n";
echo "All session data:\n";
print_r($_SESSION);

// Test database connection
echo "\n\nDatabase Connection Test:\n";

try {
    $db = new mysqli('127.0.0.1', 'root', '', 'yadawity');
    if ($db->connect_error) {
        echo "❌ Database connection failed: " . $db->connect_error . "\n";
    } else {
        echo "✅ Database connection successful\n";
        
        // Test reviews query with proper JOIN
        $test_artist_id = 1;
        $query = "SELECT COUNT(*) as count FROM reviews r JOIN artworks a ON r.artwork_id = a.artwork_id WHERE a.artist_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $test_artist_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        echo "Reviews count for artist ID 1: " . $row['count'] . "\n";
        
        $stmt->close();
        
        // Test course reviews query with proper JOIN
        $query2 = "SELECT COUNT(*) as count FROM course_reviews cr JOIN courses c ON cr.course_id = c.course_id WHERE c.artist_id = ?";
        $stmt2 = $db->prepare($query2);
        $stmt2->bind_param("i", $test_artist_id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $row2 = $result2->fetch_assoc();
        echo "Course reviews count for artist ID 1: " . $row2['count'] . "\n";
        
        $stmt2->close();
        $db->close();
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>
