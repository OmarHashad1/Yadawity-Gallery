<?php
require_once 'API/db.php';

try {
    // Check current bio for user_id 17
    $stmt = $db->prepare("SELECT user_id, first_name, last_name, artist_bio FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $user_id = 17;
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo "Current data for user_id 17:\n";
        echo "Name: " . $row['first_name'] . " " . $row['last_name'] . "\n";
        echo "Current Bio Length: " . strlen($row['artist_bio']) . " characters\n";
        echo "Current Bio Content:\n";
        echo "--------------------\n";
        echo $row['artist_bio'] . "\n";
        echo "--------------------\n";
    } else {
        echo "User ID 17 not found in database.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    if (isset($db)) {
        $db->close();
    }
}
?>
