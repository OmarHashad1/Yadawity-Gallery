<?php
// Database setup for reviews functionality
require_once './API/db.php';

try {
    // Create reviews table for artwork reviews
    $createReviewsTable = "
        CREATE TABLE IF NOT EXISTS reviews (
            review_id INT AUTO_INCREMENT PRIMARY KEY,
            artwork_id INT NOT NULL,
            user_id INT NOT NULL,
            rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
            comment TEXT,
            review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_approved TINYINT DEFAULT 1,
            INDEX idx_artwork_id (artwork_id),
            INDEX idx_user_id (user_id),
            INDEX idx_review_date (review_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    // Create course_reviews table for course reviews
    $createCourseReviewsTable = "
        CREATE TABLE IF NOT EXISTS course_reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            course_id INT NOT NULL,
            user_id INT NOT NULL,
            rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
            comment TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_approved TINYINT DEFAULT 1,
            INDEX idx_course_id (course_id),
            INDEX idx_user_id (user_id),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    // Create users table if it doesn't exist
    $createUsersTable = "
        CREATE TABLE IF NOT EXISTS users (
            user_id INT AUTO_INCREMENT PRIMARY KEY,
            user_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password_hash VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    // Create artworks table if it doesn't exist
    $createArtworksTable = "
        CREATE TABLE IF NOT EXISTS artworks (
            artwork_id INT AUTO_INCREMENT PRIMARY KEY,
            artist_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(10,2),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_artist_id (artist_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    // Execute table creation queries
    if ($db->query($createUsersTable)) {
        echo "Users table created successfully.\n";
    } else {
        echo "Error creating users table: " . $db->error . "\n";
    }
    
    if ($db->query($createArtworksTable)) {
        echo "Artworks table created successfully.\n";
    } else {
        echo "Error creating artworks table: " . $db->error . "\n";
    }
    
    if ($db->query($createReviewsTable)) {
        echo "Reviews table created successfully.\n";
    } else {
        echo "Error creating reviews table: " . $db->error . "\n";
    }
    
    if ($db->query($createCourseReviewsTable)) {
        echo "Course reviews table created successfully.\n";
    } else {
        echo "Error creating course reviews table: " . $db->error . "\n";
    }
    
    // Insert sample data for testing
    insertSampleData($db);
    
    echo "Database setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error setting up database: " . $e->getMessage() . "\n";
}

function insertSampleData($db) {
    try {
        // Insert sample users
        $insertUsers = "
            INSERT IGNORE INTO users (user_id, first_name, last_name, email, password, user_type) VALUES 
            (1, 'Test', 'Artist', 'artist@yadawity.com', 'hashed_password', 'artist'),
            (2, 'Sarah', 'Ahmed', 'sarah@example.com', 'hashed_password', 'buyer'),
            (3, 'Mohamed', 'Hassan', 'mohamed@example.com', 'hashed_password', 'buyer'),
            (4, 'Fatma', 'Ali', 'fatma@example.com', 'hashed_password', 'buyer'),
            (5, 'Ahmed', 'Khaled', 'ahmed@example.com', 'hashed_password', 'buyer')
        ";
        
        // Insert sample artworks
        $insertArtworks = "
            INSERT IGNORE INTO artworks (artwork_id, artist_id, title, description, price) VALUES 
            (1, 1, 'Abstract Composition', 'A beautiful abstract artwork with vibrant colors', 1500.00),
            (2, 1, 'Modern Landscape', 'Contemporary landscape painting', 2200.00),
            (3, 1, 'Portrait Study', 'Realistic portrait in oil', 1800.00),
            (4, 1, 'Urban Sketches', 'Collection of city sketches', 900.00)
        ";
        
        // Insert sample reviews for artworks
        $insertReviews = "
            INSERT IGNORE INTO reviews (artwork_id, user_id, rating, comment, review_date) VALUES 
            (1, 2, 5, 'Amazing artwork! The colors are vibrant and the technique is masterful.', '2025-08-10 14:30:00'),
            (1, 3, 4, 'Beautiful piece, exactly as described. Fast delivery too!', '2025-08-09 16:45:00'),
            (2, 4, 5, 'Absolutely stunning landscape! The attention to detail is incredible.', '2025-08-08 11:20:00'),
            (2, 5, 4, 'Great artwork, really captures the mood of the scene.', '2025-08-07 09:15:00'),
            (3, 2, 5, 'The portrait is so lifelike! Amazing skill.', '2025-08-06 13:40:00'),
            (4, 3, 3, 'Nice sketches, good for the price point.', '2025-08-05 15:25:00')
        ";
        
        // Insert sample course reviews (assuming courses table exists)
        $insertCourseReviews = "
            INSERT IGNORE INTO course_reviews (course_id, user_id, rating, comment, created_at) VALUES 
            (1, 2, 5, 'Excellent course! Learned so much about painting techniques.', '2025-08-12 10:30:00'),
            (1, 3, 4, 'Very informative and well-structured. Highly recommend!', '2025-08-11 14:20:00'),
            (2, 4, 5, 'Amazing instructor and great content. Worth every penny!', '2025-08-10 16:45:00')
        ";
        
        if ($db->query($insertUsers)) {
            echo "Sample users inserted successfully.\n";
        }
        
        if ($db->query($insertArtworks)) {
            echo "Sample artworks inserted successfully.\n";
        }
        
        if ($db->query($insertReviews)) {
            echo "Sample reviews inserted successfully.\n";
        }
        
        if ($db->query($insertCourseReviews)) {
            echo "Sample course reviews inserted successfully.\n";
        }
        
    } catch (Exception $e) {
        echo "Error inserting sample data: " . $e->getMessage() . "\n";
    }
}
?>
