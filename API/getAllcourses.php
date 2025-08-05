<?php
require_once "db.php";

// Set proper headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


function getFilterParameters() {
    return [
        'limit' => isset($_GET['limit']) ? (int)$_GET['limit'] : null,
        'offset' => isset($_GET['offset']) ? (int)$_GET['offset'] : 0,
        'difficulty' => isset($_GET['difficulty']) ? trim($_GET['difficulty']) : null,
        'course_type' => isset($_GET['course_type']) ? trim($_GET['course_type']) : null,
        'artist_id' => isset($_GET['artist_id']) ? (int)$_GET['artist_id'] : null,
        'published_only' => isset($_GET['published_only']) ? (bool)$_GET['published_only'] : true,
        'sort_by' => isset($_GET['sort_by']) ? trim($_GET['sort_by']) : 'created_at',
        'sort_order' => isset($_GET['sort_order']) && strtoupper($_GET['sort_order']) === 'ASC' ? 'ASC' : 'DESC',
        'min_price' => isset($_GET['min_price']) ? (float)$_GET['min_price'] : null,
        'max_price' => isset($_GET['max_price']) ? (float)$_GET['max_price'] : null
    ];
}


function buildCoursesQuery() {
    return "
        SELECT 
            c.course_id,
            c.title,
            c.rate,
            c.artist_id,
            c.duration_date,
            c.description,
            c.requirement,
            c.difficulty,
            c.course_type,
            c.price,
            c.thumbnail,
            c.is_published,
            c.created_at,
            u.first_name as artist_first_name,
            u.last_name as artist_last_name,
            u.profile_picture as artist_profile_picture,
            u.art_specialty,
            u.years_of_experience,
            u.location as artist_location,
            u.bio as artist_bio,
            COUNT(DISTINCT ce.id) as total_enrollments,
            COUNT(DISTINCT ce_paid.id) as paid_enrollments,
            COUNT(DISTINCT ce_active.id) as active_enrollments
        FROM courses c
        LEFT JOIN users u ON c.artist_id = u.user_id
        LEFT JOIN course_enrollments ce ON c.course_id = ce.course_id
        LEFT JOIN course_enrollments ce_paid ON c.course_id = ce_paid.course_id AND ce_paid.is_payed = 1
        LEFT JOIN course_enrollments ce_active ON c.course_id = ce_active.course_id AND ce_active.is_active = 1
        WHERE u.is_active = 1
    ";
}


function addFilterConditions($query, $filters, &$params, &$types) {
    if ($filters['difficulty']) {
        $query .= " AND c.difficulty = ?";
        $params[] = $filters['difficulty'];
        $types .= "s";
    }

    if ($filters['course_type']) {
        $query .= " AND c.course_type = ?";
        $params[] = $filters['course_type'];
        $types .= "s";
    }

    if ($filters['artist_id']) {
        $query .= " AND c.artist_id = ?";
        $params[] = $filters['artist_id'];
        $types .= "i";
    }

    if ($filters['published_only']) {
        $query .= " AND c.is_published = 1";
    }

    if ($filters['min_price'] !== null) {
        $query .= " AND c.price >= ?";
        $params[] = $filters['min_price'];
        $types .= "d";
    }

    if ($filters['max_price'] !== null) {
        $query .= " AND c.price <= ?";
        $params[] = $filters['max_price'];
        $types .= "d";
    }

    return $query;
}


function addSortingAndPagination($query, $filters, &$params, &$types) {
    // Group by course to handle the LEFT JOINs with enrollments
    $query .= " GROUP BY c.course_id";

    // Add sorting
    $allowed_sort_fields = ['created_at', 'price', 'title', 'rate', 'duration_date', 'total_enrollments'];
    if (in_array($filters['sort_by'], $allowed_sort_fields)) {
        $query .= " ORDER BY " . $filters['sort_by'] . " " . $filters['sort_order'];
    } else {
        $query .= " ORDER BY c.created_at DESC";
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
            SELECT COUNT(DISTINCT c.course_id) as total
            FROM courses c
            LEFT JOIN users u ON c.artist_id = u.user_id
            WHERE u.is_active = 1
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


function formatCourseData($row) {
    $course = [
        'course_id' => (int)$row['course_id'],
        'title' => $row['title'],
        'description' => $row['description'],
        'requirement' => $row['requirement'],
        'rate' => round((float)$row['rate'], 2),
        'price' => (float)$row['price'],
        'duration_date' => (int)$row['duration_date'],
        'difficulty' => $row['difficulty'],
        'course_type' => $row['course_type'],
        'thumbnail' => $row['thumbnail'],
        'is_published' => (bool)$row['is_published'],
        'created_at' => $row['created_at'],
        'artist' => [
            'artist_id' => (int)$row['artist_id'],
            'first_name' => $row['artist_first_name'],
            'last_name' => $row['artist_last_name'],
            'full_name' => $row['artist_first_name'] . ' ' . $row['artist_last_name'],
            'profile_picture' => $row['artist_profile_picture'],
            'art_specialty' => $row['art_specialty'],
            'years_of_experience' => $row['years_of_experience'] ? (int)$row['years_of_experience'] : null,
            'location' => $row['artist_location'],
            'bio' => $row['artist_bio']
        ],
        'enrollments' => [
            'total' => (int)$row['total_enrollments'],
            'paid' => (int)$row['paid_enrollments'],
            'active' => (int)$row['active_enrollments']
        ],
        'course_info' => [
            'duration_months' => (int)$row['duration_date'],
            'difficulty_level' => ucfirst($row['difficulty']),
            'delivery_method' => ucfirst($row['course_type']),
            'rating' => round((float)$row['rate'], 1),
            'enrollment_rate' => $row['total_enrollments'] > 0 ? round(($row['paid_enrollments'] / $row['total_enrollments']) * 100, 1) : 0
        ]
    ];

    // Add thumbnail URL if exists
    if ($course['thumbnail']) {
        $course['thumbnail_url'] = '../uploads/courses/' . $course['thumbnail'];
    }

    // Add artist profile picture URL if exists
    if ($course['artist']['profile_picture']) {
        $course['artist']['profile_picture_url'] = '../uploads/profiles/' . $course['artist']['profile_picture'];
    }

    // Add course status
    $course['status'] = [
        'is_available' => $course['is_published'],
        'enrollment_open' => $course['is_published'],
        'popularity' => $course['enrollments']['total'] > 20 ? 'high' : ($course['enrollments']['total'] > 5 ? 'medium' : 'low')
    ];

    return $course;
}

function getAllCourses($db, $filters) {
    try {
        // Build the main query
        $query = buildCoursesQuery();
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
        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = formatCourseData($row);
        }

        $stmt->close();

        return [
            'courses' => $courses,
            'total_count' => $total_count
        ];
        
    } catch (Exception $e) {
        throw new Exception("Error fetching courses: " . $e->getMessage());
    }
}

function buildResponse($courses, $total_count, $filters) {
    $response = [
        'success' => true,
        'message' => 'Courses retrieved successfully',
        'data' => $courses,
        'total_count' => $total_count,
        'returned_count' => count($courses)
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
        'difficulty' => $filters['difficulty'],
        'course_type' => $filters['course_type'],
        'artist_id' => $filters['artist_id'],
        'published_only' => $filters['published_only'],
        'price_range' => [
            'min' => $filters['min_price'],
            'max' => $filters['max_price']
        ],
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
    error_log("getAllCourses API Error: " . $message);
    
    $response = [
        'success' => false,
        'message' => 'An error occurred while retrieving courses: ' . $message,
        'error_code' => 'INTERNAL_ERROR',
        'data' => [],
        'total_count' => 0
    ];
    
    http_response_code($statusCode);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}


function handleGetAllCourses() {
    global $db;
    
    try {
        // Validate database connection
        if (!isset($db) || $db->connect_error) {
            throw new Exception("Database connection failed: " . ($db->connect_error ?? "Connection not established"));
        }

        // Get filter parameters
        $filters = getFilterParameters();

        // Get all courses
        $result = getAllCourses($db, $filters);

        // Build complete response
        $response = buildResponse($result['courses'], $result['total_count'], $filters);

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

handleGetAllCourses();

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