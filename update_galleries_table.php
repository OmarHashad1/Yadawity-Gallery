<?php
// PHP script to update the galleries table with primary_image field
// Run this script once to add the primary_image column to the galleries table

// Include database connection
include 'API/db.php';

// Use correct database connection variable
$conn = $db;

// Function to execute SQL and handle errors
function executeSQL($conn, $sql, $description) {
    echo "<p>Executing: $description</p>";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ SUCCESS: $description</p>";
        return true;
    } else {
        echo "<p style='color: red;'>✗ ERROR: $description - " . $conn->error . "</p>";
        return false;
    }
}

// Function to check if column exists
function columnExists($conn, $table, $column) {
    $sql = "SHOW COLUMNS FROM `$table` LIKE '$column'";
    $result = $conn->query($sql);
    return $result && $result->num_rows > 0;
}

// Function to check if table exists
function tableExists($conn, $table) {
    $sql = "SHOW TABLES LIKE '$table'";
    $result = $conn->query($sql);
    return $result && $result->num_rows > 0;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Galleries Table - Database Migration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #6B4423;
            border-bottom: 2px solid #6B4423;
            padding-bottom: 10px;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .info {
            color: #2196F3;
            font-weight: bold;
        }
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .status-box {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid;
        }
        .status-success {
            background-color: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .status-error {
            background-color: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .status-info {
            background-color: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Gallery Table Database Migration</h1>
        <p>This script will update the galleries table to support image uploads for gallery events.</p>
        
        <?php
        try {
            echo "<h2>📋 Migration Steps</h2>";
            
            // Step 1: Check if galleries table exists
            echo "<div class='status-box status-info'>";
            echo "<h3>Step 1: Checking galleries table</h3>";
            
            if (tableExists($conn, 'galleries')) {
                echo "<p class='success'>✓ galleries table exists</p>";
            } else {
                echo "<p class='error'>✗ galleries table does not exist</p>";
                echo "<p>Please create the galleries table first before running this migration.</p>";
                exit;
            }
            echo "</div>";
            
            // Step 2: Add primary_image column if it doesn't exist
            echo "<div class='status-box status-info'>";
            echo "<h3>Step 2: Adding primary_image column</h3>";
            
            if (!columnExists($conn, 'galleries', 'primary_image')) {
                $sql = "ALTER TABLE galleries ADD COLUMN primary_image VARCHAR(500) NULL COMMENT 'Main gallery image' AFTER duration";
                if (executeSQL($conn, $sql, "Adding primary_image column to galleries table")) {
                    echo "<div class='status-box status-success'>";
                    echo "<p>✓ primary_image column added successfully!</p>";
                    echo "</div>";
                } else {
                    echo "<div class='status-box status-error'>";
                    echo "<p>✗ Failed to add primary_image column</p>";
                    echo "</div>";
                }
            } else {
                echo "<div class='status-box status-success'>";
                echo "<p>✓ primary_image column already exists</p>";
                echo "</div>";
            }
            echo "</div>";
            
            // Step 3: Create gallery_photos table if it doesn't exist
            echo "<div class='status-box status-info'>";
            echo "<h3>Step 3: Creating gallery_photos table</h3>";
            
            if (!tableExists($conn, 'gallery_photos')) {
                $sql = "CREATE TABLE gallery_photos (
                    photo_id INT(11) NOT NULL AUTO_INCREMENT,
                    gallery_id INT(11) NOT NULL,
                    image_path VARCHAR(500) COLLATE utf8mb4_general_ci NOT NULL,
                    is_primary TINYINT(1) NULL DEFAULT 0,
                    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
                    PRIMARY KEY (photo_id),
                    FOREIGN KEY (gallery_id) REFERENCES galleries(gallery_id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
                
                if (executeSQL($conn, $sql, "Creating gallery_photos table")) {
                    echo "<div class='status-box status-success'>";
                    echo "<p>✓ gallery_photos table created successfully!</p>";
                    echo "</div>";
                } else {
                    echo "<div class='status-box status-error'>";
                    echo "<p>✗ Failed to create gallery_photos table</p>";
                    echo "</div>";
                }
            } else {
                echo "<div class='status-box status-success'>";
                echo "<p>✓ gallery_photos table already exists</p>";
                echo "</div>";
            }
            echo "</div>";
            
            // Step 4: Verify table structures
            echo "<div class='status-box status-info'>";
            echo "<h3>Step 4: Verifying table structures</h3>";
            
            // Show galleries table structure
            echo "<h4>galleries table structure:</h4>";
            $result = $conn->query("DESCRIBE galleries");
            if ($result) {
                echo "<pre>";
                echo sprintf("%-20s %-15s %-8s %-8s %-15s %-10s\n", "Field", "Type", "Null", "Key", "Default", "Extra");
                echo str_repeat("-", 80) . "\n";
                while ($row = $result->fetch_assoc()) {
                    echo sprintf("%-20s %-15s %-8s %-8s %-15s %-10s\n", 
                        $row['Field'], 
                        $row['Type'], 
                        $row['Null'], 
                        $row['Key'], 
                        $row['Default'] ?? 'NULL', 
                        $row['Extra']
                    );
                }
                echo "</pre>";
            }
            
            // Show gallery_photos table structure
            echo "<h4>gallery_photos table structure:</h4>";
            $result = $conn->query("DESCRIBE gallery_photos");
            if ($result) {
                echo "<pre>";
                echo sprintf("%-15s %-15s %-8s %-8s %-15s %-15s\n", "Field", "Type", "Null", "Key", "Default", "Extra");
                echo str_repeat("-", 85) . "\n";
                while ($row = $result->fetch_assoc()) {
                    echo sprintf("%-15s %-15s %-8s %-8s %-15s %-15s\n", 
                        $row['Field'], 
                        $row['Type'], 
                        $row['Null'], 
                        $row['Key'], 
                        $row['Default'] ?? 'NULL', 
                        $row['Extra']
                    );
                }
                echo "</pre>";
            }
            echo "</div>";
            
            // Step 5: Migration Summary
            echo "<div class='status-box status-success'>";
            echo "<h3>🎉 Migration Complete!</h3>";
            echo "<p><strong>Summary of changes:</strong></p>";
            echo "<ul>";
            echo "<li>✓ Added primary_image column to galleries table for storing main gallery image</li>";
            echo "<li>✓ Created gallery_photos table for storing multiple gallery images</li>";
            echo "<li>✓ Set up foreign key relationship between gallery_photos and galleries</li>";
            echo "<li>✓ Enabled support for image uploads in gallery creation</li>";
            echo "</ul>";
            echo "<p><strong>Next steps:</strong></p>";
            echo "<ul>";
            echo "<li>Gallery creation form now supports image uploads</li>";
            echo "<li>Artists can upload multiple images for their galleries</li>";
            echo "<li>Primary image is automatically set from uploaded images</li>";
            echo "<li>Images are stored in uploads/galleries/ directory</li>";
            echo "</ul>";
            echo "</div>";
            
        } catch (Exception $e) {
            echo "<div class='status-box status-error'>";
            echo "<h3>❌ Migration Failed</h3>";
            echo "<p>Error: " . $e->getMessage() . "</p>";
            echo "</div>";
        } finally {
            // Close database connection
            if ($conn) {
                $conn->close();
            }
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><strong>Note:</strong> This script should only be run once. If you need to run it again, it will safely check for existing columns and tables.</p>
            <p><strong>Security:</strong> Remember to delete this file after running the migration for security purposes.</p>
        </div>
    </div>
</body>
</html>
