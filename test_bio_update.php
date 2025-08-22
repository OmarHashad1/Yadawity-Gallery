<?php
require_once 'API/db.php';

// Simulate the profile update with bio data
$profileData = [
    'artist_bio' => 'This is a test bio update from debug script'
];

$user_id = 17; // Your user ID

try {
    echo "=== Testing Bio Update ===\n";
    echo "User ID: $user_id\n";
    echo "Bio data to save: " . $profileData['artist_bio'] . "\n\n";
    
    // Check current bio
    $stmt = $db->prepare("SELECT artist_bio FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo "Current bio in database:\n";
        echo "Length: " . strlen($row['artist_bio']) . " characters\n";
        echo "Content: " . $row['artist_bio'] . "\n\n";
    }
    
    // Test the update
    $updateStmt = $db->prepare("UPDATE users SET artist_bio = ? WHERE user_id = ?");
    $updateStmt->bind_param("si", $profileData['artist_bio'], $user_id);
    
    if ($updateStmt->execute()) {
        echo "✅ Bio update executed successfully\n";
        echo "Affected rows: " . $db->affected_rows . "\n\n";
        
        // Verify the update
        $verifyStmt = $db->prepare("SELECT artist_bio FROM users WHERE user_id = ?");
        $verifyStmt->bind_param("i", $user_id);
        $verifyStmt->execute();
        $verifyResult = $verifyStmt->get_result();
        
        if ($verifyRow = $verifyResult->fetch_assoc()) {
            echo "Updated bio in database:\n";
            echo "Length: " . strlen($verifyRow['artist_bio']) . " characters\n";
            echo "Content: " . $verifyRow['artist_bio'] . "\n";
        }
    } else {
        echo "❌ Bio update failed: " . $db->error . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    if (isset($db)) {
        $db->close();
    }
}
?>
