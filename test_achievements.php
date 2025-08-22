<?php
require_once 'API/db.php';

echo "<h2>Artist Achievements Test for User 17</h2>";

// Check if artist_achievements table exists
$table_check = $db->query("SHOW TABLES LIKE 'artist_achievements'");
if ($table_check && $table_check->num_rows > 0) {
    echo "✅ Artist_achievements table exists<br><br>";
    
    // Get achievements for user 17
    $query = "SELECT achievement_name FROM artist_achievements WHERE user_id = 17";
    $result = $db->query($query);
    if ($result && $result->num_rows > 0) {
        echo "<strong>Achievements for artist ID 17:</strong><br>";
        $achievements = [];
        while ($row = $result->fetch_assoc()) {
            $achievements[] = $row['achievement_name'];
            echo "- " . $row['achievement_name'] . "<br>";
        }
        echo "<br><strong>As JSON array:</strong> " . json_encode($achievements) . "<br>";
    } else {
        echo "❌ No achievements found for artist ID 17<br>";
    }
    
    // Show total count
    $count_query = "SELECT COUNT(*) as count FROM artist_achievements WHERE user_id = 17";
    $count_result = $db->query($count_query);
    if ($count_result) {
        $count_row = $count_result->fetch_assoc();
        echo "<br><strong>Total achievements: " . $count_row['count'] . "</strong><br>";
    }
} else {
    echo "❌ Artist_achievements table does not exist<br>";
}

$db->close();
?>
