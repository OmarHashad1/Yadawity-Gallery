<?php



/*
-- Artwork Marketplace Database Schema

-- Users table (combined with artist information)
CREATE TABLE users (
user_id INT AUTO_INCREMENT PRIMARY KEY,
email VARCHAR(255) UNIQUE NOT NULL,
password VARCHAR(255) NOT NULL,
first_name VARCHAR(100) NOT NULL,
last_name VARCHAR(100) NOT NULL,
phone VARCHAR(20),
user_type ENUM('artist', 'buyer', 'admin') DEFAULT 'buyer',
profile_picture VARCHAR(500),
bio TEXT,
is_active TINYINT(1) DEFAULT 1,
-- Artist-specific fields (nullable)
art_specialty VARCHAR(255) NULL,
years_of_experience INT NULL,
achievements TEXT NULL,
artist_bio TEXT NULL,
location VARCHAR(255) NULL,
education TEXT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Course table
CREATE TABLE courses (
course_id INT AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(255) NOT NULL,
rate DECIMAL(3,2) DEFAULT 0.00 COMMENT 'Course rating out of 5',
artist_id INT NOT NULL,
duration_date INT NOT NULL COMMENT 'Duration in months',
description TEXT,
requirement TEXT,
difficulty ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
course_type ENUM('online', 'offline', 'hybrid') NOT NULL,
price DECIMAL(10,2) NOT NULL,
thumbnail VARCHAR(500),
is_published TINYINT(1) DEFAULT 0,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (artist_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Course enrollment table
CREATE TABLE course_enrollments (
id INT AUTO_INCREMENT PRIMARY KEY,
course_id INT NOT NULL,
user_id INT NOT NULL,
is_payed TINYINT(1) DEFAULT 0,
is_active TINYINT(1) DEFAULT 1,
enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE,
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
UNIQUE KEY unique_enrollment (course_id, user_id)
);

CREATE TABLE galleries (
    gallery_id INT AUTO_INCREMENT PRIMARY KEY,
    artist_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    gallery_type ENUM('virtual', 'physical') NOT NULL,
    
    -- Virtual gallery fields
    price DECIMAL(10,2) NULL,              -- Price for virtual access
    
    -- Physical gallery fields  
    address TEXT NULL,
    city VARCHAR(100) NULL,
    phone VARCHAR(20) NULL,
    
    start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    duration INT NOT NULL, --in minutes               
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Artwork table
CREATE TABLE artworks (
artwork_id INT AUTO_INCREMENT PRIMARY KEY,
artist_id INT NOT NULL,
title VARCHAR(255) NOT NULL,
description TEXT,
price DECIMAL(10,2) NOT NULL,
dimensions VARCHAR(100),
year YEAR,
material VARCHAR(255),
artwork_image VARCHAR(500),
type ENUM('painting', 'sculpture', 'photography', 'digital', 'mixed_media', 'other') NOT NULL,
is_available TINYINT(1) DEFAULT 1,
on_auction TINYINT(1) DEFAULT 0,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (artist_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Update gallery_items to reference artworks
ALTER TABLE gallery_items
ADD FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE;

-- Orders table
CREATE TABLE orders (
id INT AUTO_INCREMENT PRIMARY KEY,
buyer_id INT NOT NULL,
total_amount DECIMAL(10,2) NOT NULL,
status ENUM('pending', 'paid', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
shipping_address TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (buyer_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE order_items (
id INT AUTO_INCREMENT PRIMARY KEY,
order_id INT NOT NULL,
artwork_id INT NOT NULL,
price DECIMAL(10,2) NOT NULL,
quantity INT DEFAULT 1,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE
);

-- Artist reviews table
CREATE TABLE artist_reviews (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
artist_id INT NOT NULL,
artwork_id INT,
rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
feedback TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
FOREIGN KEY (artist_id) REFERENCES users(user_id) ON DELETE CASCADE,
FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE SET NULL
);

-- Subscribers table (for artist subscription plans)
CREATE TABLE subscribers (
id INT AUTO_INCREMENT PRIMARY KEY,
artist_id INT NOT NULL,
plan ENUM('basic', 'premium', 'pro') NOT NULL,
duration INT NOT NULL COMMENT 'Duration in months',
start_date DATE NOT NULL,
end_date DATE NOT NULL,
is_active TINYINT(1) DEFAULT 1,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (artist_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Exam table
CREATE TABLE exams (
exam_id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
need_doctor TINYINT(1) DEFAULT 0,
draw_img VARCHAR(500),
exam_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
results TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Sessions table (for user sessions, course sessions, or gallery sessions)
CREATE TABLE sessions (
session_id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
session_type ENUM('user_login', 'course', 'gallery_visit', 'exam') NOT NULL,
reference_id INT COMMENT 'ID of course, gallery, or exam depending on session_type',
start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
end_time TIMESTAMP NULL,
duration INT COMMENT 'Session duration in minutes',
ip_address VARCHAR(45),
user_agent TEXT,
is_active TINYINT(1) DEFAULT 1,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Auction table
CREATE TABLE auctions (
id INT AUTO_INCREMENT PRIMARY KEY,
product_id INT NOT NULL,
artist_id INT NOT NULL,
starting_bid DECIMAL(10,2) NOT NULL,
current_bid DECIMAL(10,2) DEFAULT 0.00,
start_time DATETIME NOT NULL,
end_time DATETIME NOT NULL,
status ENUM('active', 'ended', 'cancelled') DEFAULT 'active',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (product_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE,
FOREIGN KEY (artist_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Auction bids table (to track all bids placed on auctions)
CREATE TABLE auction_bids (
id INT AUTO_INCREMENT PRIMARY KEY,
auction_id INT NOT NULL,
user_id INT NOT NULL,
bid_amount DECIMAL(10,2) NOT NULL,
bid_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
is_winning_bid TINYINT(1) DEFAULT 0,
FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE,
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE user_login_sessions (
session_id VARCHAR(128) PRIMARY KEY, -- Unique session token
user_id INT NOT NULL, -- Reference to logged-in user
login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- When user logged in
expires_at TIMESTAMP NOT NULL, -- Session expiration time
is_active TINYINT(1) DEFAULT 1, -- Session active status
logout_time TIMESTAMP NULL, -- When user logged out
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE cart (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL, -- Links to the user
artwork_id INT NOT NULL, -- Links to the artwork
quantity INT DEFAULT 1, -- Quantity (usually 1 for unique artworks)
added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- When added to cart
is_active TINYINT(1) DEFAULT 1, -- Active/inactive status
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE,
UNIQUE KEY unique_cart_item (user_id, artwork_id) -- Prevents duplicates
);
*/

?>
