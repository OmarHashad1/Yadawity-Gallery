<?php
require_once "db.php";

// Set proper headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

function validateCourseId() {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("Course ID is required");
    }
    
    $course_id = (int)$_GET['id'];
    if ($course_id <= 0) {
        throw new Exception("Invalid course ID");
    }
    
    return $course_id;
}

function buildCourseQuery() {
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
            u.email as artist_email,
            u.phone as artist_phone,
            COUNT(DISTINCT ar.id) as review_count,
            COALESCE(AVG(ar.rating), 0) as average_rating,
            COUNT(DISTINCT ce.id) as enrollment_count
        FROM courses c
        LEFT JOIN users u ON c.artist_id = u.user_id
        LEFT JOIN artist_reviews ar ON c.artist_id = ar.artist_id
        LEFT JOIN course_enrollments ce ON c.course_id = ce.course_id AND ce.is_active = 1
        WHERE c.course_id = ? AND u.is_active = 1
        GROUP BY c.course_id
    ";
}

function formatCourseData($row) {
    $course = [
        'course_id' => (int)$row['course_id'],
        'title' => $row['title'],
        'description' => $row['description'],
        'rate' => (float)$row['rate'],
        'price' => (float)$row['price'],
        'formatted_price' => '$' . number_format((float)$row['price'], 2),
        'duration_date' => (int)$row['duration_date'],
        'duration_text' => $row['duration_date'] . ' month' . ($row['duration_date'] > 1 ? 's' : ''),
        'requirement' => $row['requirement'],
        'difficulty' => $row['difficulty'],
        'difficulty_text' => ucfirst($row['difficulty']),
        'course_type' => $row['course_type'],
        'course_type_text' => ucfirst($row['course_type']),
        'thumbnail' => $row['thumbnail'],
        'is_published' => (bool)$row['is_published'],
        'enrollment_count' => (int)$row['enrollment_count'],
        'created_at' => $row['created_at'],
        'artist' => [
            'artist_id' => (int)$row['artist_id'],
            'first_name' => $row['artist_first_name'],
            'last_name' => $row['artist_last_name'],
            'full_name' => $row['artist_first_name'] . ' ' . $row['artist_last_name'],
            'display_name' => 'By ' . $row['artist_first_name'] . ' ' . $row['artist_last_name'],
            'profile_picture' => $row['artist_profile_picture'],
            'art_specialty' => $row['art_specialty'],
            'years_of_experience' => $row['years_of_experience'] ? (int)$row['years_of_experience'] : null,
            'location' => $row['artist_location'],
            'bio' => $row['artist_bio'],
            'email' => $row['artist_email'],
            'phone' => $row['artist_phone']
        ],
        'reviews' => [
            'count' => (int)$row['review_count'],
            'average_rating' => round((float)$row['average_rating'], 2),
            'course_rating' => (float)$row['rate']
        ]
    ];

    // Add thumbnail URL
    if ($course['thumbnail']) {
        $course['thumbnail_url'] = './image/' . $course['thumbnail'];
        $course['image_src'] = './image/' . $course['thumbnail'];
    } else {
        $course['thumbnail_url'] = './image/placeholder-course.jpg';
        $course['image_src'] = './image/placeholder-course.jpg';
    }

    // Add artist profile picture URL
    if ($course['artist']['profile_picture']) {
        $course['artist']['profile_picture_url'] = './uploads/profiles/' . $course['artist']['profile_picture'];
    } else {
        $course['artist']['profile_picture_url'] = './image/default-artist.jpg';
    }

    // Add course status and availability
    $course['status'] = [
        'is_published' => $course['is_published'],
        'is_available' => $course['is_published'],
        'status_text' => $course['is_published'] ? 'Available' : 'Not Published',
        'availability_text' => $course['is_published'] ? 'Open for Enrollment' : 'Coming Soon'
    ];

    // Add difficulty level information
    $difficulty_levels = [
        'beginner' => ['level' => 1, 'description' => 'No prior experience required'],
        'intermediate' => ['level' => 2, 'description' => 'Some experience recommended'],
        'advanced' => ['level' => 3, 'description' => 'Significant experience required']
    ];

    $course['difficulty_info'] = $difficulty_levels[$course['difficulty']] ?? ['level' => 1, 'description' => 'Level not specified'];

    // Add course type information
    $course_types = [
        'online' => 'Online Course - Learn from anywhere',
        'offline' => 'In-Person Course - Physical attendance required',
        'hybrid' => 'Hybrid Course - Combination of online and in-person'
    ];

    $course['course_type_description'] = $course_types[$course['course_type']] ?? 'Course type not specified';

    // Add truncated description for previews
    if ($course['description']) {
        $course['short_description'] = strlen($course['description']) > 150 
            ? substr($course['description'], 0, 150) . '...' 
            : $course['description'];
    } else {
        $course['short_description'] = 'No description available.';
    }

    return $course;
}

function getCourseById($db, $course_id) {
    try {
        $query = buildCourseQuery();
        $stmt = $db->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Query preparation failed: " . $db->error);
        }
        
        $stmt->bind_param("i", $course_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if (!$row) {
            throw new Exception("Course not found", 404);
        }
        
        $stmt->close();
        
        return formatCourseData($row);
        
    } catch (Exception $e) {
        if ($e->getCode() === 404) {
            throw $e;
        }
        throw new Exception("Error fetching course: " . $e->getMessage());
    }
}

function sendSuccessResponse($course) {
    $response = [
        'success' => true,
        'message' => 'Course retrieved successfully',
        'data' => $course
    ];
    
    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function sendErrorResponse($message, $statusCode = 500) {
    error_log("getCourseInfo API Error: " . $message);
    
    $response = [
        'success' => false,
        'message' => $message,
        'error_code' => $statusCode === 404 ? 'NOT_FOUND' : 'INTERNAL_ERROR',
        'data' => null
    ];
    
    http_response_code($statusCode);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function handleGetCourseInfo() {
    global $db;
    
    try {
        // Validate database connection
        if (!isset($db) || $db->connect_error) {
            throw new Exception("Database connection failed: " . ($db->connect_error ?? "Connection not established"));
        }

        // Validate and get course ID
        $course_id = validateCourseId();

        // Get course information
        $course = getCourseById($db, $course_id);

        // Send success response
        sendSuccessResponse($course);

    } catch (Exception $e) {
        // Send error response
        $statusCode = $e->getCode() === 404 ? 404 : 500;
        sendErrorResponse($e->getMessage(), $statusCode);
    } finally {
        // Close database connection if it exists
        if (isset($db) && !$db->connect_error) {
            $db->close();
        }
    }
}

// Execute the main function
handleGetCourseInfo();
?>