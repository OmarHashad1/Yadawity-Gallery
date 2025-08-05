<?php
require_once "db.php";

// Set proper headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');


function getFilterParameters() {
    return [
        'limit' => isset($_GET['limit']) ? (int)$_GET['limit'] : null,
        'offset' => isset($_GET['offset']) ? (int)$_GET['offset'] : 0,
        'type' => isset($_GET['type']) ? trim($_GET['type']) : null,
        'artist_id' => isset($_GET['artist_id']) ? (int)$_GET['artist_id'] : null,
        'available_only' => isset($_GET['available_only']) ? (bool)$_GET['available_only'] : false,
        'sort_by' => isset($_GET['sort_by']) ? trim($_GET['sort_by']) : 'created_at',
        'sort_order' => isset($_GET['sort_order']) && strtoupper($_GET['sort_order']) === 'ASC' ? 'ASC' : 'DESC',
        'min_price' => isset($_GET['min_price']) ? (float)$_GET['min_price'] : null,
        'max_price' => isset($_GET['max_price']) ? (float)$_GET['max_price'] : null,
        'year' => isset($_GET['year']) ? (int)$_GET['year'] : null,
        'material' => isset($_GET['material']) ? trim($_GET['material']) : null
    ];
}


function buildArtworksQuery(): string {
    return "
        SELECT 
            a.artwork_id,
            a.artist_id,
            a.title,
            a.description,
            a.price,
            a.dimensions,
            a.artwork_image,
            a.type,
            a.is_available,
            a.on_auction,
            u.first_name as artist_first_name,
            u.last_name as artist_last_name,
            COUNT(ar.id) as review_count,
            COALESCE(AVG(ar.rating), 0) as average_rating
        FROM artworks a
        LEFT JOIN users u ON a.artist_id = u.user_id
        LEFT JOIN artist_reviews ar ON a.artist_id = ar.artist_id
        WHERE u.is_active = 1 AND a.on_auction = 0
    ";
}

function addFilterConditions($query, $filters, &$params, &$types): mixed {
    if ($filters['type']) {
        $query .= " AND a.type = ?";
        $params[] = $filters['type'];
        $types .= "s";
    }

    if ($filters['artist_id']) {
        $query .= " AND a.artist_id = ?";
        $params[] = $filters['artist_id'];
        $types .= "i";
    }

    if ($filters['available_only']) {
        $query .= " AND a.is_available = 1";
    }

    if ($filters['min_price'] !== null) {
        $query .= " AND a.price >= ?";
        $params[] = $filters['min_price'];
        $types .= "d";
    }

    if ($filters['max_price'] !== null) {
        $query .= " AND a.price <= ?";
        $params[] = $filters['max_price'];
        $types .= "d";
    }

    if ($filters['year']) {
        $query .= " AND a.year = ?";
        $params[] = $filters['year'];
        $types .= "i";
    }

    if ($filters['material']) {
        $query .= " AND a.material LIKE ?";
        $params[] = '%' . $filters['material'] . '%';
        $types .= "s";
    }

    return $query;
}

function addSortingAndPagination($query, $filters, &$params, &$types) {
    // Group by artwork to handle the LEFT JOIN with reviews
    $query .= " GROUP BY a.artwork_id";

    // Add sorting
    $allowed_sort_fields = ['created_at', 'price', 'title', 'year', 'average_rating'];
    if (in_array($filters['sort_by'], $allowed_sort_fields)) {
        // Handle the ambiguous created_at column by specifying table alias
        if ($filters['sort_by'] === 'created_at') {
            $query .= " ORDER BY a.created_at " . $filters['sort_order'];
        } else {
            $query .= " ORDER BY " . $filters['sort_by'] . " " . $filters['sort_order'];
        }
    } else {
        $query .= " ORDER BY a.created_at DESC";
    }

    // Add pagination
    if ($filters['limit']) {
        $query .= " LIMIT ? OFFSET ?";
        $params[] = $filters['limit'];
        $params[] = $filters['offset'];
        $types .= "ii";
    }

    return $query;
}


function getTotalCount($db, $filters) {
    try {
        $count_query = "
            SELECT COUNT(DISTINCT a.artwork_id) as total
            FROM artworks a
            LEFT JOIN users u ON a.artist_id = u.user_id
            WHERE u.is_active = 1 AND a.on_auction = 0
        ";

        $count_params = [];
        $count_types = "";

        $count_query = addFilterConditions($count_query, $filters, $count_params, $count_types);

        $count_stmt = $db->prepare($count_query);
        if (!empty($count_params)) {
            $count_stmt->bind_param($count_types, ...$count_params);
        }
        
        if (!$count_stmt->execute()) {
            throw new Exception("Count query execution failed: " . $count_stmt->error);
        }
        
        $count_result = $count_stmt->get_result();
        $total_count = $count_result->fetch_assoc()['total'];
        $count_stmt->close();

        return (int)$total_count;
        
    } catch (Exception $e) {
        throw new Exception("Error getting total count: " . $e->getMessage());
    }
}


function formatArtworkData($row) {
    $artwork = [
        'artwork_id' => (int)$row['artwork_id'],
        'title' => $row['title'],
        'description' => $row['description'],
        'price' => (float)$row['price'],
        'formatted_price' => '$' . number_format((float)$row['price'], 0),
        'dimensions' => $row['dimensions'],
        'artwork_image' => $row['artwork_image'],
        'type' => $row['type'],
        'category' => ucfirst($row['type']),
        'is_available' => (bool)$row['is_available'],
        'on_auction' => (bool)$row['on_auction'],
        'artist' => [
            'artist_id' => (int)$row['artist_id'],
            'first_name' => $row['artist_first_name'],
            'last_name' => $row['artist_last_name'],
            'full_name' => $row['artist_first_name'] . ' ' . $row['artist_last_name'],
            'display_name' => 'By ' . $row['artist_first_name'] . ' ' . $row['artist_last_name']
        ],
        'reviews' => [
            'count' => (int)$row['review_count'],
            'average_rating' => round((float)$row['average_rating'], 2)
        ]
    ];

    // Add image URL
    if ($artwork['artwork_image']) {
        $artwork['artwork_image_url'] = './image/' . $artwork['artwork_image'];
        $artwork['image_src'] = './image/' . $artwork['artwork_image'];
    } else {
        $artwork['artwork_image_url'] = './image/placeholder-artwork.jpg';
        $artwork['image_src'] = './image/placeholder-artwork.jpg';
    }

    // Add status for card display
    $artwork['status_text'] = $artwork['on_auction'] ? 'On Auction' : ($artwork['is_available'] ? 'Available' : 'Sold');

    return $artwork;
}


function getAllArtworks($db, $filters) {
    try {
        // Build the main query
        $query = buildArtworksQuery();
        $params = [];
        $types = "";

        // Add filter conditions
        $query = addFilterConditions($query, $filters, $params, $types);
        
        // Add sorting and pagination
        $query = addSortingAndPagination($query, $filters, $params, $types);

        // Prepare and execute the query
        $stmt = $db->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Query preparation failed: " . $db->error);
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();

        // Get total count for pagination
        $total_count = getTotalCount($db, $filters);

        // Format the results
        $artworks = [];
        while ($row = $result->fetch_assoc()) {
            $artworks[] = formatArtworkData($row);
        }

        $stmt->close();

        return [
            'artworks' => $artworks,
            'total_count' => $total_count
        ];
        
    } catch (Exception $e) {
        throw new Exception("Error fetching artworks: " . $e->getMessage());
    }
}


function buildResponse($artworks, $total_count, $filters) {
    $response = [
        'success' => true,
        'message' => 'Artworks retrieved successfully',
        'data' => $artworks,
        'total_count' => $total_count,
        'returned_count' => count($artworks)
    ];

    // Add pagination info if applicable
    if ($filters['limit']) {
        $response['pagination'] = [
            'limit' => $filters['limit'],
            'offset' => $filters['offset'],
            'total_pages' => ceil($total_count / $filters['limit']),
            'current_page' => floor($filters['offset'] / $filters['limit']) + 1,
            'has_next' => ($filters['offset'] + $filters['limit']) < $total_count,
            'has_previous' => $filters['offset'] > 0
        ];
    }

    // Add filter summary
    $response['filters_applied'] = [
        'type' => $filters['type'],
        'artist_id' => $filters['artist_id'],
        'available_only' => $filters['available_only'],
        'price_range' => [
            'min' => $filters['min_price'],
            'max' => $filters['max_price']
        ],
        'year' => $filters['year'],
        'material' => $filters['material'],
        'sort_by' => $filters['sort_by'],
        'sort_order' => $filters['sort_order']
    ];

    return $response;
}


function sendSuccessResponse($response) {
    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function sendErrorResponse($message, $statusCode = 500) {
    // Log error (in production, use proper logging)
    error_log("getAllProduct API Error: " . $message);
    
    $response = [
        'success' => false,
        'message' => 'An error occurred while retrieving artworks: ' . $message,
        'error_code' => 'INTERNAL_ERROR',
        'data' => [],
        'total_count' => 0
    ];
    
    http_response_code($statusCode);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function handleGetAllProducts() {
    global $db;
    
    try {
        // Validate database connection
        if (!isset($db) || $db->connect_error) {
            throw new Exception("Database connection failed: " . ($db->connect_error ?? "Connection not established"));
        }

        // Get filter parameters
        $filters = getFilterParameters();

        // Get all artworks
        $result = getAllArtworks($db, $filters);

        // Build complete response
        $response = buildResponse($result['artworks'], $result['total_count'], $filters);

        // Send success response
        sendSuccessResponse($response);

    } catch (Exception $e) {
        // Send error response
        sendErrorResponse($e->getMessage());
    } finally {
        // Close database connection if it exists
        if (isset($db) && !$db->connect_error) {
            $db->close();
        }
    }
}

// Execute the main function
handleGetAllProducts();

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

-- Gallery table
CREATE TABLE galleries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artist_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    gallery_type ENUM('virtual', 'physical') NOT NULL,
    location VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (artist_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Gallery items table
CREATE TABLE gallery_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gallery_id INT NOT NULL,
    artwork_id INT NOT NULL,
    start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    duration INT NOT NULL COMMENT 'Duration in days',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (gallery_id) REFERENCES galleries(id) ON DELETE CASCADE
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


*/