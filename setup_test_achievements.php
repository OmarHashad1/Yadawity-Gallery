<?php
require_once 'API/db.php';

echo "<h2>Adding Test Achievements for User 17</h2>";

// Check if artist_achievements table exists
$table_check = $db->query("SHOW TABLES LIKE 'artist_achievements'");
if ($table_check && $table_check->num_rows > 0) {
    echo "✅ Artist_achievements table exists<br><br>";
    
    // First, check if user 17 already has achievements
    $check_query = "SELECT COUNT(*) as count FROM artist_achievements WHERE user_id = 17";
    $check_result = $db->query($check_query);
    $count = 0;
    if ($check_result) {
        $row = $check_result->fetch_assoc();
        $count = $row['count'];
    }
    
    echo "Current achievements for user 17: $count<br><br>";
    
    if ($count == 0) {
        echo "Adding test achievements...<br>";
        
        // Insert some test achievements
        $achievements = [
            "First Place in Local Art Competition 2024",
            "Featured Artist at City Gallery",
            "Best Contemporary Art Award"
        ];
        
        $insert_query = "INSERT INTO artist_achievements (user_id, achievement_name) VALUES (?, ?)";
        $stmt = $db->prepare($insert_query);
        
        foreach ($achievements as $achievement) {
            $stmt->bind_param("is", $user_id, $achievement);
            $user_id = 17;
            if ($stmt->execute()) {
                echo "✅ Added: $achievement<br>";
            } else {
                echo "❌ Failed to add: $achievement<br>";
            }
        }
        $stmt->close();
    }
    
    // Now display all achievements for user 17
    echo "<br><h3>Current achievements:</h3>";
    $query = "SELECT achievement_name FROM artist_achievements WHERE user_id = 17";
    $result = $db->query($query);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['achievement_name'] . "<br>";
        }
    } else {
        echo "No achievements found.<br>";
    }
    
} else {
    echo "❌ Artist_achievements table does not exist. Creating it...<br>";
    
    $create_table = "CREATE TABLE artist_achievements (
        achievement_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        achievement_name VARCHAR(255) NOT NULL
    )";
    
    if ($db->query($create_table)) {
        echo "✅ Table created successfully<br>";
    } else {
        echo "❌ Failed to create table: " . $db->error . "<br>";
    }
}

echo '<br><a href="API/getArtistInfo.php" target="_blank">Test API Response</a>';

$db->close();
?>
