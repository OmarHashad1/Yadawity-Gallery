<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db.php';

function updateArtistProfile($artist_id, $profileData) {
    global $db;
    
    try {
        // Start transaction
        $db->autocommit(false);
        
        // Validate input
        if (!$artist_id || !is_numeric($artist_id)) {
            throw new InvalidArgumentException('Valid artist ID is required');
        }
        
        // Verify artist exists
        $check_query = "SELECT user_id FROM users WHERE user_id = ? AND user_type = 'artist' AND is_active = 1";
        $stmt = $db->prepare($check_query);
        $stmt->bind_param("i", $artist_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Artist not found', 404);
        }
        $stmt->close();
        
        // Build update query for users table
        $updateFields = [];
        $params = [];
        $types = "";
        
        if (isset($profileData['bio'])) {
            $updateFields[] = "bio = ?";
            $params[] = $profileData['bio'];
            $types .= "s";
        }
        
        // Try to handle phone number - check if column exists first
        if (isset($profileData['phone_number'])) {
            // Check if phone_number column exists
            $column_check = $db->query("SHOW COLUMNS FROM users LIKE 'phone_number'");
            if ($column_check && $column_check->num_rows > 0) {
                $updateFields[] = "phone_number = ?";
                $params[] = $profileData['phone_number'];
                $types .= "s";
            } else {
                // Try alternative column names
                $alt_check = $db->query("SHOW COLUMNS FROM users LIKE 'phone'");
                if ($alt_check && $alt_check->num_rows > 0) {
                    $updateFields[] = "phone = ?";
                    $params[] = $profileData['phone_number'];
                    $types .= "s";
                } else {
                    // Log that phone column doesn't exist but don't fail
                    error_log("Phone column not found in users table");
                }
            }
        }
        
        if (isset($profileData['email'])) {
            $updateFields[] = "email = ?";
            $params[] = $profileData['email'];
            $types .= "s";
        }
        
        if (isset($profileData['art_specialty'])) {
            $updateFields[] = "art_specialty = ?";
            $params[] = $profileData['art_specialty'];
            $types .= "s";
        }
        
        if (isset($profileData['years_of_experience'])) {
            $updateFields[] = "years_of_experience = ?";
            $params[] = (int)$profileData['years_of_experience'];
            $types .= "i";
        }
        
        if (isset($profileData['location'])) {
            $updateFields[] = "location = ?";
            $params[] = $profileData['location'];
            $types .= "s";
        }
        
        if (isset($profileData['education'])) {
            $updateFields[] = "education = ?";
            $params[] = $profileData['education'];
            $types .= "s";
        }
        
        // Update users table if there are fields to update
        if (!empty($updateFields)) {
            $params[] = $artist_id;
            $types .= "i";
            
            $update_query = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE user_id = ?";
            $stmt = $db->prepare($update_query);
            
            if (!$stmt) {
                throw new Exception('Database prepare error: ' . $db->error);
            }
            
            $stmt->bind_param($types, ...$params);
            
            if (!$stmt->execute()) {
                throw new Exception('Database execution error: ' . $stmt->error);
            }
            
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            // Log successful update
            error_log("Profile update successful. Updated fields: " . implode(", ", array_map(function($field) {
                return explode(" = ", $field)[0];
            }, $updateFields)) . ". Affected rows: $affected_rows");
            
        } else {
            // Log that no profile fields were updated
            error_log("No profile fields were updated - all fields either missing or columns don't exist");
        }
        
        // Handle achievements updates
        if (isset($profileData['achievements'])) {
            handleAchievementsUpdate($artist_id, $profileData['achievements']);
        }
        
        // Commit transaction
        $db->commit();
        $db->autocommit(true);
        
        // Create success message with details
        $updated_fields = [];
        if (!empty($updateFields)) {
            $updated_fields = array_map(function($field) {
                return explode(" = ", $field)[0];
            }, $updateFields);
        }
        
        $message = 'Profile updated successfully!';
        if (!empty($updated_fields)) {
            $message .= ' Updated: ' . implode(', ', $updated_fields);
        }
        
        if (isset($profileData['achievements'])) {
            $message .= ' (including achievements)';
        }
        
        return [
            'success' => true,
            'message' => $message,
            'updated_fields' => $updated_fields
        ];
        
    } catch (InvalidArgumentException $e) {
        $db->rollback();
        $db->autocommit(true);
        http_response_code(400);
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    } catch (Exception $e) {
        $db->rollback();
        $db->autocommit(true);
        $code = $e->getCode() === 404 ? 404 : 500;
        http_response_code($code);
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

function handleAchievementsUpdate($artist_id, $achievementsData) {
    global $db;
    
    // Check if artist_achievements table exists
    $table_check = $db->query("SHOW TABLES LIKE 'artist_achievements'");
    if (!$table_check || $table_check->num_rows === 0) {
        // Create table if it doesn't exist
        $create_table = "CREATE TABLE artist_achievements (
            achievement_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            achievement_name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if (!$db->query($create_table)) {
            throw new Exception('Failed to create achievements table: ' . $db->error);
        }
    }
    
    // Handle achievements based on operation type
    if (isset($achievementsData['operation'])) {
        switch ($achievementsData['operation']) {
            case 'add':
                if (isset($achievementsData['achievement'])) {
                    addAchievement($artist_id, $achievementsData['achievement']);
                }
                break;
                
            case 'delete':
                if (isset($achievementsData['achievement_id'])) {
                    deleteAchievement($artist_id, $achievementsData['achievement_id']);
                } elseif (isset($achievementsData['achievement'])) {
                    deleteAchievementByName($artist_id, $achievementsData['achievement']);
                }
                break;
                
            case 'replace_all':
                if (isset($achievementsData['achievements']) && is_array($achievementsData['achievements'])) {
                    replaceAllAchievements($artist_id, $achievementsData['achievements']);
                }
                break;
        }
    }
}

function addAchievement($artist_id, $achievement) {
    global $db;
    
    $insert_query = "INSERT INTO artist_achievements (user_id, achievement_name) VALUES (?, ?)";
    $stmt = $db->prepare($insert_query);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare achievement insert: ' . $db->error);
    }
    
    $stmt->bind_param("is", $artist_id, $achievement);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to add achievement: ' . $stmt->error);
    }
    
    $stmt->close();
}

function deleteAchievement($artist_id, $achievement_id) {
    global $db;
    
    $delete_query = "DELETE FROM artist_achievements WHERE user_id = ? AND achievement_id = ?";
    $stmt = $db->prepare($delete_query);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare achievement delete: ' . $db->error);
    }
    
    $stmt->bind_param("ii", $artist_id, $achievement_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete achievement: ' . $stmt->error);
    }
    
    $stmt->close();
}

function deleteAchievementByName($artist_id, $achievement_name) {
    global $db;
    
    $delete_query = "DELETE FROM artist_achievements WHERE user_id = ? AND achievement_name = ?";
    $stmt = $db->prepare($delete_query);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare achievement delete: ' . $db->error);
    }
    
    $stmt->bind_param("is", $artist_id, $achievement_name);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete achievement: ' . $stmt->error);
    }
    
    $stmt->close();
}

function replaceAllAchievements($artist_id, $achievements) {
    global $db;
    
    // Delete all existing achievements
    $delete_query = "DELETE FROM artist_achievements WHERE user_id = ?";
    $stmt = $db->prepare($delete_query);
    $stmt->bind_param("i", $artist_id);
    $stmt->execute();
    $stmt->close();
    
    // Add new achievements
    if (!empty($achievements)) {
        foreach ($achievements as $achievement) {
            if (!empty($achievement)) {
                addAchievement($artist_id, $achievement);
            }
        }
    }
}

try {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests are allowed', 405);
    }
    
    // Get artist ID from cookie
    $artist_id = null;
    
    if (isset($_COOKIE['user_login'])) {
        $cookie_value = $_COOKIE['user_login'];
        $parts = explode('_', $cookie_value);
        if (count($parts) > 0 && is_numeric($parts[0])) {
            $artist_id = (int)$parts[0];
        }
    }
    
    // Fallback to POST data if cookie is not available
    if (!$artist_id) {
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['artist_id'])) {
            $artist_id = (int)$input['artist_id'];
        }
    }
    
    if (!$artist_id) {
        throw new Exception('Artist ID is required', 400);
    }
    
    // Get profile data from POST request
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON data provided', 400);
    }
    
    // Update artist profile
    $response = updateArtistProfile($artist_id, $input);
    
    // Output JSON response
    echo json_encode($response);
    
} catch (Exception $e) {
    $code = $e->getCode() ?: 500;
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($db) && $db) {
        $db->close();
    }
}
?>
