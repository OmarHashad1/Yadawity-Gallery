<?php
require_once 'API/db.php';

try {
    // First, let's see what's currently in the database for user_id 17
    $stmt = $db->prepare("SELECT user_id, first_name, last_name, email FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $user_id = 17;
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo "Current data for user_id 17:\n";
        echo "Name: " . $row['first_name'] . " " . $row['last_name'] . "\n";
        echo "Current Email: " . $row['email'] . "\n\n";
        
        // Update the email to the correct one
        $new_email = "omarhashad22@gmail.com";
        $update_stmt = $db->prepare("UPDATE users SET email = ? WHERE user_id = ?");
        $update_stmt->bind_param("si", $new_email, $user_id);
        
        if ($update_stmt->execute()) {
            echo "✅ Email updated successfully!\n";
            echo "New Email: " . $new_email . "\n";
            
            // Verify the update
            $verify_stmt = $db->prepare("SELECT email FROM users WHERE user_id = ?");
            $verify_stmt->bind_param("i", $user_id);
            $verify_stmt->execute();
            $verify_result = $verify_stmt->get_result();
            
            if ($verify_row = $verify_result->fetch_assoc()) {
                echo "Verified Email: " . $verify_row['email'] . "\n";
            }
        } else {
            echo "❌ Failed to update email: " . $db->error . "\n";
        }
    } else {
        echo "User ID 17 not found in database.\n";
        echo "Let's check what user_ids exist with Omar:\n";
        
        $check_stmt = $db->prepare("SELECT user_id, first_name, last_name, email FROM users WHERE first_name = 'Omar'");
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        while ($omar = $check_result->fetch_assoc()) {
            echo "Found Omar - user_id: " . $omar['user_id'] . ", email: " . $omar['email'] . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    if (isset($db)) {
        $db->close();
    }
}
?>
