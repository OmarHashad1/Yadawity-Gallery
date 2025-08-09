<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'db.php';

try {
    // Get request method and action
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    $course_id = $_GET['course_id'] ?? '';
    
    // Route to appropriate function based on method and action
    switch ($method) {
        case 'GET':
            handleGetRequest($action, $course_id);
            break;
        case 'POST':
            handlePostRequest($action);
            break;
        case 'PUT':
            handlePutRequest($action, $course_id);
            break;
        case 'DELETE':
            handleDeleteRequest($action, $course_id);
            break;
        default:
            sendErrorResponse("Method not allowed", 405);
    }

} catch (Exception $e) {
    sendErrorResponse("Server error: " . $e->getMessage(), 500);
}

/**
 * Handle GET requests
 */
function handleGetRequest($action, $course_id) {
    global $db;
    
    // If no action specified, default to list
    if (empty($action)) {
        $action = 'list';
    }
    
    switch ($action) {
        case 'list':
        case '': // Handle empty action as list
            getAllCoursesAdmin();
            break;
        case 'details':
            if (!$course_id) {
                sendErrorResponse("Course ID is required", 400);
                return;
            }
            getCourseDetails($course_id);
            break;
        case 'enrollments':
            if (!$course_id) {
                sendErrorResponse("Course ID is required", 400);
                return;
            }
            getCourseEnrollments($course_id);
            break;
        case 'stats':
            getCourseStats();
            break;
        case 'artists':
            getAvailableArtists();
            break;
        case 'reviews':
            if (!$course_id) {
                sendErrorResponse("Course ID is required", 400);
                return;
            }
            getCourseReviews($course_id);
            break;
        case 'test':
            // Debug endpoint to see what parameters we're receiving
            sendSuccessResponse([
                'method' => $_SERVER['REQUEST_METHOD'],
                'get_params' => $_GET,
                'post_params' => $_POST,
                'action' => $action,
                'course_id' => $course_id,
                'query_string' => $_SERVER['QUERY_STRING'] ?? 'none'
            ], "Debug information");
            break;
        default:
            sendErrorResponse("Invalid action: '{$action}'. Available actions: list, details, enrollments, stats, artists, reviews", 400);
    }
}

/**
 * Handle POST requests
 */
function handlePostRequest($action) {
    switch ($action) {
        case 'create':
            createCourse();
            break;
        case 'bulk-update':
            bulkUpdateCourses();
            break;
        case 'enroll-user':
            enrollUserInCourse();
            break;
        default:
            sendErrorResponse("Invalid action", 400);
    }
}

/**
 * Handle PUT requests
 */
function handlePutRequest($action, $course_id) {
    if (!$course_id) {
        sendErrorResponse("Course ID is required", 400);
        return;
    }
    
    switch ($action) {
        case 'update':
            updateCourse($course_id);
            break;
        case 'publish':
            updateCoursePublishStatus($course_id);
            break;
        case 'pricing':
            updateCoursePricing($course_id);
            break;
        default:
            sendErrorResponse("Invalid action", 400);
    }
}

/**
 * Handle DELETE requests
 */
function handleDeleteRequest($action, $course_id) {
    if (!$course_id) {
        sendErrorResponse("Course ID is required", 400);
        return;
    }
    
    switch ($action) {
        case 'delete':
            deleteCourse($course_id);
            break;
        case 'unenroll':
            unenrollUserFromCourse($course_id);
            break;
        default:
            sendErrorResponse("Invalid action", 400);
    }
}

/**
 * Get all courses with admin details
 */
function getAllCoursesAdmin() {
    global $db;
    
    try {
        $search = $_GET['search'] ?? '';
        $difficulty = $_GET['difficulty'] ?? '';
        $course_type = $_GET['course_type'] ?? '';
        $artist_id = $_GET['artist_id'] ?? '';
        $is_published = $_GET['is_published'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = min(100, max(10, (int)($_GET['limit'] ?? 20)));
        $offset = ($page - 1) * $limit;
        
        // Build WHERE conditions
        $conditions = ["u.is_active = 1"];
        $params = [];
        
        if (!empty($search)) {
            $conditions[] = "(c.title LIKE ? OR c.description LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
            $search_param = "%{$search}%";
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
        }
        
        if (!empty($difficulty)) {
            $conditions[] = "c.difficulty = ?";
            $params[] = $difficulty;
        }
        
        if (!empty($course_type)) {
            $conditions[] = "c.course_type = ?";
            $params[] = $course_type;
        }
        
        if (!empty($artist_id)) {
            $conditions[] = "c.artist_id = ?";
            $params[] = $artist_id;
        }
        
        if ($is_published !== '') {
            $conditions[] = "c.is_published = ?";
            $params[] = (int)$is_published;
        }
        
        $where_clause = "WHERE " . implode(" AND ", $conditions);
        
        // Main query
        $sql = "SELECT 
                    c.course_id,
                    c.title,
                    c.rate,
                    c.duration_date,
                    c.description,
                    c.requirement,
                    c.difficulty,
                    c.course_type,
                    c.price,
                    c.thumbnail,
                    c.is_published,
                    c.created_at,
                    
                    -- Artist details
                    u.user_id as artist_id,
                    u.first_name as artist_first_name,
                    u.last_name as artist_last_name,
                    u.email as artist_email,
                    u.profile_picture as artist_profile_picture,
                    u.art_specialty,
                    u.years_of_experience,
                    u.location as artist_location,
                    
                    -- Enrollment statistics
                    (SELECT COUNT(*) FROM course_enrollments ce WHERE ce.course_id = c.course_id AND ce.is_active = 1) as total_enrollments,
                    (SELECT COUNT(*) FROM course_enrollments ce WHERE ce.course_id = c.course_id AND ce.is_payed = 1 AND ce.is_active = 1) as paid_enrollments,
                    (SELECT COALESCE(SUM(c2.price), 0) FROM course_enrollments ce2 INNER JOIN courses c2 ON ce2.course_id = c2.course_id WHERE ce2.course_id = c.course_id AND ce2.is_payed = 1) as total_revenue
                    
                FROM courses c
                INNER JOIN users u ON c.artist_id = u.user_id
                $where_clause
                ORDER BY c.created_at DESC
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $types = str_repeat('s', count($params) - 2) . 'ii';
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = formatCourseAdminData($row);
        }
        
        // Get total count for pagination
        $count_sql = "SELECT COUNT(*) as total 
                      FROM courses c
                      INNER JOIN users u ON c.artist_id = u.user_id
                      $where_clause";
        
        $count_params = array_slice($params, 0, -2); // Remove limit and offset
        $count_stmt = $db->prepare($count_sql);
        if (!empty($count_params)) {
            $count_types = str_repeat('s', count($count_params));
            $count_stmt->bind_param($count_types, ...$count_params);
        }
        $count_stmt->execute();
        $total_count = $count_stmt->get_result()->fetch_assoc()['total'];
        
        sendSuccessResponse([
            'courses' => $courses,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total_count' => (int)$total_count,
                'total_pages' => ceil($total_count / $limit)
            ]
        ], "Courses retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching courses: " . $e->getMessage());
    }
}

/**
 * Get detailed course information
 */
function getCourseDetails($course_id) {
    global $db;
    
    try {
        $sql = "SELECT 
                    c.course_id,
                    c.title,
                    c.rate,
                    c.duration_date,
                    c.description,
                    c.requirement,
                    c.difficulty,
                    c.course_type,
                    c.price,
                    c.thumbnail,
                    c.is_published,
                    c.created_at,
                    
                    -- Artist details
                    u.user_id as artist_id,
                    u.first_name as artist_first_name,
                    u.last_name as artist_last_name,
                    u.email as artist_email,
                    u.profile_picture as artist_profile_picture,
                    u.art_specialty,
                    u.years_of_experience,
                    u.location as artist_location,
                    u.bio as artist_bio,
                    u.achievements as artist_achievements
                    
                FROM courses c
                INNER JOIN users u ON c.artist_id = u.user_id
                WHERE c.course_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $course_data = formatCourseAdminData($row);
            
            // Get enrollment details
            $enrollment_sql = "SELECT 
                                  ce.id as enrollment_id,
                                  ce.is_payed,
                                  ce.is_active,
                                  ce.enrollment_date,
                                  u.user_id,
                                  u.first_name,
                                  u.last_name,
                                  u.email,
                                  u.profile_picture
                               FROM course_enrollments ce
                               INNER JOIN users u ON ce.user_id = u.user_id
                               WHERE ce.course_id = ?
                               ORDER BY ce.enrollment_date DESC";
            
            $enrollment_stmt = $db->prepare($enrollment_sql);
            $enrollment_stmt->bind_param("i", $course_id);
            $enrollment_stmt->execute();
            $enrollment_result = $enrollment_stmt->get_result();
            
            $enrollments = [];
            while ($enrollment_row = $enrollment_result->fetch_assoc()) {
                $enrollments[] = [
                    'enrollment_id' => (int)$enrollment_row['enrollment_id'],
                    'is_paid' => (bool)$enrollment_row['is_payed'],
                    'is_active' => (bool)$enrollment_row['is_active'],
                    'enrollment_date' => $enrollment_row['enrollment_date'],
                    'student' => [
                        'user_id' => (int)$enrollment_row['user_id'],
                        'name' => $enrollment_row['first_name'] . ' ' . $enrollment_row['last_name'],
                        'email' => $enrollment_row['email'],
                        'profile_picture' => $enrollment_row['profile_picture']
                    ]
                ];
            }
            
            $course_data['enrollments'] = $enrollments;
            $course_data['enrollment_summary'] = [
                'total_enrollments' => count($enrollments),
                'paid_enrollments' => count(array_filter($enrollments, fn($e) => $e['is_paid'])),
                'active_enrollments' => count(array_filter($enrollments, fn($e) => $e['is_active'])),
                'total_revenue' => array_sum(array_map(fn($e) => $e['is_paid'] ? $course_data['price'] : 0, $enrollments))
            ];
            
            sendSuccessResponse($course_data, "Course details retrieved successfully");
        } else {
            sendErrorResponse("Course not found", 404);
        }
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching course details: " . $e->getMessage());
    }
}

/**
 * Create new course
 */
function createCourse() {
    global $db;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required_fields = ['title', 'artist_id', 'duration_date', 'difficulty', 'course_type', 'price'];
        foreach ($required_fields as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                sendErrorResponse("Missing required field: $field", 400);
                return;
            }
        }
        
        // Validate artist exists and is an artist
        $artist_sql = "SELECT user_id, first_name, last_name FROM users WHERE user_id = ? AND user_type = 'artist' AND is_active = 1";
        $artist_stmt = $db->prepare($artist_sql);
        $artist_stmt->bind_param("i", $input['artist_id']);
        $artist_stmt->execute();
        $artist_result = $artist_stmt->get_result();
        
        if (!$artist = $artist_result->fetch_assoc()) {
            sendErrorResponse("Artist not found or not active", 404);
            return;
        }
        
        // Validate enum values
        $valid_difficulties = ['beginner', 'intermediate', 'advanced'];
        $valid_course_types = ['online', 'offline', 'hybrid'];
        
        if (!in_array($input['difficulty'], $valid_difficulties)) {
            sendErrorResponse("Invalid difficulty level", 400);
            return;
        }
        
        if (!in_array($input['course_type'], $valid_course_types)) {
            sendErrorResponse("Invalid course type", 400);
            return;
        }
        
        // Validate numeric fields
        if ($input['duration_date'] <= 0) {
            sendErrorResponse("Duration must be greater than 0", 400);
            return;
        }
        
        if ($input['price'] < 0) {
            sendErrorResponse("Price must be non-negative", 400);
            return;
        }
        
        // Create course
        $course_sql = "INSERT INTO courses (title, artist_id, duration_date, description, requirement, difficulty, course_type, price, thumbnail, is_published) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $course_stmt = $db->prepare($course_sql);
        $course_stmt->bind_param("siissssdsi", 
            $input['title'],
            $input['artist_id'],
            $input['duration_date'],
            $input['description'] ?? '',
            $input['requirement'] ?? '',
            $input['difficulty'],
            $input['course_type'],
            $input['price'],
            $input['thumbnail'] ?? '',
            $input['is_published'] ?? 0
        );
        $course_stmt->execute();
        
        $course_id = $db->insert_id;
        
        sendSuccessResponse([
            'course_id' => $course_id,
            'title' => $input['title'],
            'artist_name' => $artist['first_name'] . ' ' . $artist['last_name']
        ], "Course created successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error creating course: " . $e->getMessage());
    }
}

/**
 * Update course details
 */
function updateCourse($course_id) {
    global $db;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Check if course exists
        $check_sql = "SELECT course_id, title FROM courses WHERE course_id = ?";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("i", $course_id);
        $check_stmt->execute();
        $course = $check_stmt->get_result()->fetch_assoc();
        
        if (!$course) {
            sendErrorResponse("Course not found", 404);
            return;
        }
        
        $update_fields = [];
        $params = [];
        $types = '';
        
        // Update allowed fields
        if (isset($input['title'])) {
            $update_fields[] = "title = ?";
            $params[] = $input['title'];
            $types .= 's';
        }
        
        if (isset($input['description'])) {
            $update_fields[] = "description = ?";
            $params[] = $input['description'];
            $types .= 's';
        }
        
        if (isset($input['requirement'])) {
            $update_fields[] = "requirement = ?";
            $params[] = $input['requirement'];
            $types .= 's';
        }
        
        if (isset($input['difficulty'])) {
            $valid_difficulties = ['beginner', 'intermediate', 'advanced'];
            if (!in_array($input['difficulty'], $valid_difficulties)) {
                sendErrorResponse("Invalid difficulty level", 400);
                return;
            }
            $update_fields[] = "difficulty = ?";
            $params[] = $input['difficulty'];
            $types .= 's';
        }
        
        if (isset($input['course_type'])) {
            $valid_course_types = ['online', 'offline', 'hybrid'];
            if (!in_array($input['course_type'], $valid_course_types)) {
                sendErrorResponse("Invalid course type", 400);
                return;
            }
            $update_fields[] = "course_type = ?";
            $params[] = $input['course_type'];
            $types .= 's';
        }
        
        if (isset($input['price'])) {
            if ($input['price'] < 0) {
                sendErrorResponse("Price must be non-negative", 400);
                return;
            }
            $update_fields[] = "price = ?";
            $params[] = $input['price'];
            $types .= 'd';
        }
        
        if (isset($input['duration_date'])) {
            if ($input['duration_date'] <= 0) {
                sendErrorResponse("Duration must be greater than 0", 400);
                return;
            }
            $update_fields[] = "duration_date = ?";
            $params[] = $input['duration_date'];
            $types .= 'i';
        }
        
        if (isset($input['thumbnail'])) {
            $update_fields[] = "thumbnail = ?";
            $params[] = $input['thumbnail'];
            $types .= 's';
        }
        
        if (empty($update_fields)) {
            sendErrorResponse("No valid fields to update", 400);
            return;
        }
        
        $params[] = $course_id;
        $types .= 'i';
        
        $update_sql = "UPDATE courses SET " . implode(", ", $update_fields) . " WHERE course_id = ?";
        $stmt = $db->prepare($update_sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        sendSuccessResponse(['course_id' => $course_id], "Course updated successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error updating course: " . $e->getMessage());
    }
}

/**
 * Update course publish status
 */
function updateCoursePublishStatus($course_id) {
    global $db;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['is_published'])) {
            sendErrorResponse("Published status is required", 400);
            return;
        }
        
        $is_published = (int)$input['is_published'];
        
        // Check if course exists
        $check_sql = "SELECT course_id, title, is_published FROM courses WHERE course_id = ?";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("i", $course_id);
        $check_stmt->execute();
        $course = $check_stmt->get_result()->fetch_assoc();
        
        if (!$course) {
            sendErrorResponse("Course not found", 404);
            return;
        }
        
        // Update publish status
        $update_sql = "UPDATE courses SET is_published = ? WHERE course_id = ?";
        $update_stmt = $db->prepare($update_sql);
        $update_stmt->bind_param("ii", $is_published, $course_id);
        $update_stmt->execute();
        
        $status_text = $is_published ? 'published' : 'unpublished';
        
        sendSuccessResponse([
            'course_id' => $course_id,
            'title' => $course['title'],
            'is_published' => (bool)$is_published,
            'status' => $status_text
        ], "Course $status_text successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error updating course publish status: " . $e->getMessage());
    }
}

/**
 * Get course statistics
 */
function getCourseStats() {
    global $db;
    
    try {
        $time_period = $_GET['period'] ?? 'month';
        $time_condition = getTimeCondition($time_period);
        
        // Total courses
        $total_sql = "SELECT COUNT(*) as total FROM courses $time_condition";
        $total_result = $db->query($total_sql);
        $total_courses = $total_result->fetch_assoc()['total'];
        
        // Published courses
        $published_sql = "SELECT COUNT(*) as published FROM courses WHERE is_published = 1 $time_condition";
        $published_result = $db->query($published_sql);
        $published_courses = $published_result->fetch_assoc()['published'];
        
        // Total enrollments
        $enrollments_sql = "SELECT COUNT(*) as enrollments FROM course_enrollments ce INNER JOIN courses c ON ce.course_id = c.course_id WHERE ce.is_active = 1 $time_condition";
        $enrollments_result = $db->query($enrollments_sql);
        $total_enrollments = $enrollments_result->fetch_assoc()['enrollments'];
        
        // Total revenue from courses
        $revenue_sql = "SELECT COALESCE(SUM(c.price), 0) as revenue 
                       FROM course_enrollments ce 
                       INNER JOIN courses c ON ce.course_id = c.course_id 
                       WHERE ce.is_payed = 1 AND ce.is_active = 1 $time_condition";
        $revenue_result = $db->query($revenue_sql);
        $total_revenue = $revenue_result->fetch_assoc()['revenue'];
        
        // Average rating
        $rating_sql = "SELECT AVG(rate) as avg_rating FROM courses WHERE rate > 0 $time_condition";
        $rating_result = $db->query($rating_sql);
        $avg_rating = $rating_result->fetch_assoc()['avg_rating'] ?: 0;
        
        // Most popular difficulty levels
        $difficulty_sql = "SELECT difficulty, COUNT(*) as count 
                          FROM courses c 
                          $time_condition 
                          GROUP BY difficulty 
                          ORDER BY count DESC";
        $difficulty_result = $db->query($difficulty_sql);
        $difficulty_stats = [];
        while ($row = $difficulty_result->fetch_assoc()) {
            $difficulty_stats[] = [
                'difficulty' => ucfirst($row['difficulty']),
                'count' => (int)$row['count']
            ];
        }
        
        // Course type distribution
        $type_sql = "SELECT course_type, COUNT(*) as count 
                    FROM courses c 
                    $time_condition 
                    GROUP BY course_type 
                    ORDER BY count DESC";
        $type_result = $db->query($type_sql);
        $type_stats = [];
        while ($row = $type_result->fetch_assoc()) {
            $type_stats[] = [
                'type' => ucfirst($row['course_type']),
                'count' => (int)$row['count']
            ];
        }
        
        // Top artists by course count
        $artists_sql = "SELECT u.user_id, u.first_name, u.last_name, COUNT(*) as course_count,
                              COALESCE(SUM(CASE WHEN ce.is_payed = 1 THEN c.price ELSE 0 END), 0) as total_revenue
                       FROM courses c 
                       INNER JOIN users u ON c.artist_id = u.user_id 
                       LEFT JOIN course_enrollments ce ON c.course_id = ce.course_id AND ce.is_active = 1
                       $time_condition 
                       GROUP BY u.user_id 
                       ORDER BY course_count DESC 
                       LIMIT 5";
        $artists_result = $db->query($artists_sql);
        $top_artists = [];
        while ($row = $artists_result->fetch_assoc()) {
            $top_artists[] = [
                'artist_id' => (int)$row['user_id'],
                'name' => $row['first_name'] . ' ' . $row['last_name'],
                'course_count' => (int)$row['course_count'],
                'total_revenue' => (float)$row['total_revenue']
            ];
        }
        
        sendSuccessResponse([
            'overview' => [
                'total_courses' => (int)$total_courses,
                'published_courses' => (int)$published_courses,
                'total_enrollments' => (int)$total_enrollments,
                'total_revenue' => (float)$total_revenue,
                'average_rating' => round((float)$avg_rating, 2)
            ],
            'difficulty_distribution' => $difficulty_stats,
            'type_distribution' => $type_stats,
            'top_artists' => $top_artists,
            'period' => $time_period
        ], "Course statistics retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching course statistics: " . $e->getMessage());
    }
}

/**
 * Get available artists for course creation
 */
function getAvailableArtists() {
    global $db;
    
    try {
        $search = $_GET['search'] ?? '';
        
        $conditions = ["user_type = 'artist'", "is_active = 1"];
        $params = [];
        
        if (!empty($search)) {
            $conditions[] = "(first_name LIKE ? OR last_name LIKE ? OR art_specialty LIKE ?)";
            $search_param = "%{$search}%";
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
        }
        
        $where_clause = "WHERE " . implode(" AND ", $conditions);
        
        $sql = "SELECT 
                    user_id,
                    first_name,
                    last_name,
                    email,
                    profile_picture,
                    art_specialty,
                    years_of_experience,
                    location,
                    (SELECT COUNT(*) FROM courses WHERE artist_id = users.user_id) as course_count,
                    (SELECT COUNT(*) FROM courses WHERE artist_id = users.user_id AND is_published = 1) as published_course_count
                FROM users
                $where_clause
                ORDER BY first_name, last_name
                LIMIT 50";
        
        if (!empty($params)) {
            $stmt = $db->prepare($sql);
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $db->query($sql);
        }
        
        $artists = [];
        while ($row = $result->fetch_assoc()) {
            $artists[] = [
                'artist_id' => (int)$row['user_id'],
                'name' => $row['first_name'] . ' ' . $row['last_name'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'email' => $row['email'],
                'profile_picture' => $row['profile_picture'],
                'specialty' => $row['art_specialty'],
                'years_of_experience' => $row['years_of_experience'] ? (int)$row['years_of_experience'] : null,
                'location' => $row['location'],
                'course_count' => (int)$row['course_count'],
                'published_course_count' => (int)$row['published_course_count']
            ];
        }
        
        sendSuccessResponse($artists, "Available artists retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching available artists: " . $e->getMessage());
    }
}

/**
 * Delete course
 */
function deleteCourse($course_id) {
    global $db;
    
    try {
        $db->begin_transaction();
        
        // Check if course exists and has enrollments
        $check_sql = "SELECT c.course_id, c.title, 
                            (SELECT COUNT(*) FROM course_enrollments WHERE course_id = c.course_id) as enrollment_count
                     FROM courses c 
                     WHERE c.course_id = ?";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("i", $course_id);
        $check_stmt->execute();
        $course = $check_stmt->get_result()->fetch_assoc();
        
        if (!$course) {
            sendErrorResponse("Course not found", 404);
            return;
        }
        
        if ($course['enrollment_count'] > 0) {
            sendErrorResponse("Cannot delete course with existing enrollments", 400);
            return;
        }
        
        // Delete course
        $delete_sql = "DELETE FROM courses WHERE course_id = ?";
        $delete_stmt = $db->prepare($delete_sql);
        $delete_stmt->bind_param("i", $course_id);
        $delete_stmt->execute();
        
        $db->commit();
        
        sendSuccessResponse([
            'course_id' => $course_id,
            'title' => $course['title']
        ], "Course deleted successfully");
        
    } catch (Exception $e) {
        $db->rollback();
        sendErrorResponse("Error deleting course: " . $e->getMessage());
    }
}

/**
 * Bulk update courses
 */
function bulkUpdateCourses() {
    global $db;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['course_ids']) || !is_array($input['course_ids'])) {
            sendErrorResponse("Course IDs array is required", 400);
            return;
        }
        
        if (!isset($input['action'])) {
            sendErrorResponse("Bulk action is required", 400);
            return;
        }
        
        $course_ids = array_map('intval', $input['course_ids']);
        $action = $input['action'];
        
        if (empty($course_ids)) {
            sendErrorResponse("No course IDs provided", 400);
            return;
        }
        
        $placeholders = str_repeat('?,', count($course_ids) - 1) . '?';
        $updated_count = 0;
        
        switch ($action) {
            case 'publish':
                $sql = "UPDATE courses SET is_published = 1 WHERE course_id IN ($placeholders)";
                $stmt = $db->prepare($sql);
                $stmt->bind_param(str_repeat('i', count($course_ids)), ...$course_ids);
                $stmt->execute();
                $updated_count = $stmt->affected_rows;
                break;
                
            case 'unpublish':
                $sql = "UPDATE courses SET is_published = 0 WHERE course_id IN ($placeholders)";
                $stmt = $db->prepare($sql);
                $stmt->bind_param(str_repeat('i', count($course_ids)), ...$course_ids);
                $stmt->execute();
                $updated_count = $stmt->affected_rows;
                break;
                
            case 'update_price':
                if (!isset($input['price']) || !is_numeric($input['price'])) {
                    sendErrorResponse("Price is required for price update", 400);
                    return;
                }
                $sql = "UPDATE courses SET price = ? WHERE course_id IN ($placeholders)";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('d' . str_repeat('i', count($course_ids)), $input['price'], ...$course_ids);
                $stmt->execute();
                $updated_count = $stmt->affected_rows;
                break;
                
            default:
                sendErrorResponse("Invalid bulk action. Available: publish, unpublish, update_price", 400);
                return;
        }
        
        sendSuccessResponse([
            'updated_count' => $updated_count,
            'action' => $action,
            'course_ids' => $course_ids
        ], "Bulk update completed successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error in bulk update: " . $e->getMessage());
    }
}

/**
 * Get course enrollments
 */
function getCourseEnrollments($course_id) {
    global $db;
    
    try {
        // Check if course exists
        $check_sql = "SELECT course_id, title FROM courses WHERE course_id = ?";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("i", $course_id);
        $check_stmt->execute();
        
        if (!$check_stmt->get_result()->fetch_assoc()) {
            sendErrorResponse("Course not found", 404);
            return;
        }
        
        // Get enrollments
        $sql = "SELECT 
                    ce.id as enrollment_id,
                    ce.is_payed,
                    ce.is_active,
                    ce.enrollment_date,
                    ce.created_at,
                    u.user_id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.profile_picture,
                    u.phone,
                    u.user_type
                FROM course_enrollments ce
                INNER JOIN users u ON ce.user_id = u.user_id
                WHERE ce.course_id = ?
                ORDER BY ce.enrollment_date DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $enrollments = [];
        while ($row = $result->fetch_assoc()) {
            $enrollments[] = [
                'enrollment_id' => (int)$row['enrollment_id'],
                'is_paid' => (bool)$row['is_payed'],
                'is_active' => (bool)$row['is_active'],
                'enrollment_date' => $row['enrollment_date'],
                'created_at' => $row['created_at'],
                'student' => [
                    'user_id' => (int)$row['user_id'],
                    'name' => $row['first_name'] . ' ' . $row['last_name'],
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'user_type' => $row['user_type'],
                    'profile_picture' => $row['profile_picture']
                ]
            ];
        }
        
        sendSuccessResponse([
            'course_id' => (int)$course_id,
            'enrollments' => $enrollments,
            'summary' => [
                'total_enrollments' => count($enrollments),
                'paid_enrollments' => count(array_filter($enrollments, fn($e) => $e['is_paid'])),
                'active_enrollments' => count(array_filter($enrollments, fn($e) => $e['is_active']))
            ]
        ], "Course enrollments retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching course enrollments: " . $e->getMessage());
    }
}

/**
 * Get course reviews
 */
function getCourseReviews($course_id) {
    global $db;
    
    try {
        // Check if course exists
        $check_sql = "SELECT course_id, title FROM courses WHERE course_id = ?";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("i", $course_id);
        $check_stmt->execute();
        $course = $check_stmt->get_result()->fetch_assoc();
        
        if (!$course) {
            sendErrorResponse("Course not found", 404);
            return;
        }
        
        // Get reviews through artist reviews table
        // Note: The database schema shows artist_reviews table, which can be adapted for course reviews
        $sql = "SELECT 
                    ar.id as review_id,
                    ar.rating,
                    ar.feedback,
                    ar.created_at,
                    u.user_id,
                    u.first_name,
                    u.last_name,
                    u.profile_picture
                FROM artist_reviews ar
                INNER JOIN users u ON ar.user_id = u.user_id
                INNER JOIN courses c ON ar.artist_id = c.artist_id
                WHERE c.course_id = ?
                ORDER BY ar.created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $reviews = [];
        $total_rating = 0;
        while ($row = $result->fetch_assoc()) {
            $reviews[] = [
                'review_id' => (int)$row['review_id'],
                'rating' => (int)$row['rating'],
                'feedback' => $row['feedback'],
                'created_at' => $row['created_at'],
                'reviewer' => [
                    'user_id' => (int)$row['user_id'],
                    'name' => $row['first_name'] . ' ' . $row['last_name'],
                    'profile_picture' => $row['profile_picture']
                ]
            ];
            $total_rating += (int)$row['rating'];
        }
        
        $average_rating = count($reviews) > 0 ? round($total_rating / count($reviews), 2) : 0;
        
        sendSuccessResponse([
            'course_id' => (int)$course_id,
            'course_title' => $course['title'],
            'reviews' => $reviews,
            'summary' => [
                'total_reviews' => count($reviews),
                'average_rating' => $average_rating,
                'rating_distribution' => [
                    '5_star' => count(array_filter($reviews, fn($r) => $r['rating'] == 5)),
                    '4_star' => count(array_filter($reviews, fn($r) => $r['rating'] == 4)),
                    '3_star' => count(array_filter($reviews, fn($r) => $r['rating'] == 3)),
                    '2_star' => count(array_filter($reviews, fn($r) => $r['rating'] == 2)),
                    '1_star' => count(array_filter($reviews, fn($r) => $r['rating'] == 1))
                ]
            ]
        ], "Course reviews retrieved successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error fetching course reviews: " . $e->getMessage());
    }
}

/**
 * Enroll user in course
 */
function enrollUserInCourse() {
    global $db;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['course_id']) || !isset($input['user_id'])) {
            sendErrorResponse("Course ID and User ID are required", 400);
            return;
        }
        
        $course_id = (int)$input['course_id'];
        $user_id = (int)$input['user_id'];
        $is_paid = isset($input['is_paid']) ? (int)$input['is_paid'] : 0;
        
        // Check if course exists and is published
        $course_sql = "SELECT course_id, title, price, is_published FROM courses WHERE course_id = ?";
        $course_stmt = $db->prepare($course_sql);
        $course_stmt->bind_param("i", $course_id);
        $course_stmt->execute();
        $course = $course_stmt->get_result()->fetch_assoc();
        
        if (!$course) {
            sendErrorResponse("Course not found", 404);
            return;
        }
        
        if (!$course['is_published']) {
            sendErrorResponse("Cannot enroll in unpublished course", 400);
            return;
        }
        
        // Check if user exists
        $user_sql = "SELECT user_id, first_name, last_name, email FROM users WHERE user_id = ? AND is_active = 1";
        $user_stmt = $db->prepare($user_sql);
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user = $user_stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            sendErrorResponse("User not found or inactive", 404);
            return;
        }
        
        // Check if already enrolled
        $enrollment_check_sql = "SELECT id FROM course_enrollments WHERE course_id = ? AND user_id = ?";
        $enrollment_check_stmt = $db->prepare($enrollment_check_sql);
        $enrollment_check_stmt->bind_param("ii", $course_id, $user_id);
        $enrollment_check_stmt->execute();
        
        if ($enrollment_check_stmt->get_result()->fetch_assoc()) {
            sendErrorResponse("User is already enrolled in this course", 400);
            return;
        }
        
        // Create enrollment
        $enroll_sql = "INSERT INTO course_enrollments (course_id, user_id, is_payed, is_active) VALUES (?, ?, ?, 1)";
        $enroll_stmt = $db->prepare($enroll_sql);
        $enroll_stmt->bind_param("iii", $course_id, $user_id, $is_paid);
        $enroll_stmt->execute();
        
        $enrollment_id = $db->insert_id;
        
        sendSuccessResponse([
            'enrollment_id' => $enrollment_id,
            'course_id' => $course_id,
            'course_title' => $course['title'],
            'user_id' => $user_id,
            'user_name' => $user['first_name'] . ' ' . $user['last_name'],
            'is_paid' => (bool)$is_paid,
            'course_price' => (float)$course['price']
        ], "User enrolled in course successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error enrolling user: " . $e->getMessage());
    }
}

/**
 * Unenroll user from course
 */
function unenrollUserFromCourse($course_id) {
    global $db;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['user_id'])) {
            sendErrorResponse("User ID is required", 400);
            return;
        }
        
        $user_id = (int)$input['user_id'];
        
        // Check if enrollment exists
        $check_sql = "SELECT ce.id, c.title, u.first_name, u.last_name 
                     FROM course_enrollments ce
                     INNER JOIN courses c ON ce.course_id = c.course_id
                     INNER JOIN users u ON ce.user_id = u.user_id
                     WHERE ce.course_id = ? AND ce.user_id = ?";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("ii", $course_id, $user_id);
        $check_stmt->execute();
        $enrollment = $check_stmt->get_result()->fetch_assoc();
        
        if (!$enrollment) {
            sendErrorResponse("Enrollment not found", 404);
            return;
        }
        
        // Soft delete by setting is_active to 0
        $unenroll_sql = "UPDATE course_enrollments SET is_active = 0 WHERE course_id = ? AND user_id = ?";
        $unenroll_stmt = $db->prepare($unenroll_sql);
        $unenroll_stmt->bind_param("ii", $course_id, $user_id);
        $unenroll_stmt->execute();
        
        sendSuccessResponse([
            'course_id' => (int)$course_id,
            'course_title' => $enrollment['title'],
            'user_id' => $user_id,
            'user_name' => $enrollment['first_name'] . ' ' . $enrollment['last_name']
        ], "User unenrolled from course successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error unenrolling user: " . $e->getMessage());
    }
}

/**
 * Update course pricing
 */
function updateCoursePricing($course_id) {
    global $db;
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['price']) || !is_numeric($input['price'])) {
            sendErrorResponse("Valid price is required", 400);
            return;
        }
        
        $new_price = (float)$input['price'];
        
        if ($new_price < 0) {
            sendErrorResponse("Price must be non-negative", 400);
            return;
        }
        
        // Check if course exists
        $check_sql = "SELECT course_id, title, price FROM courses WHERE course_id = ?";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("i", $course_id);
        $check_stmt->execute();
        $course = $check_stmt->get_result()->fetch_assoc();
        
        if (!$course) {
            sendErrorResponse("Course not found", 404);
            return;
        }
        
        $old_price = (float)$course['price'];
        
        // Update pricing
        $update_sql = "UPDATE courses SET price = ? WHERE course_id = ?";
        $update_stmt = $db->prepare($update_sql);
        $update_stmt->bind_param("di", $new_price, $course_id);
        $update_stmt->execute();
        
        sendSuccessResponse([
            'course_id' => $course_id,
            'title' => $course['title'],
            'old_price' => $old_price,
            'new_price' => $new_price,
            'price_change' => $new_price - $old_price,
            'percentage_change' => $old_price > 0 ? round((($new_price - $old_price) / $old_price) * 100, 2) : 0
        ], "Course pricing updated successfully");
        
    } catch (Exception $e) {
        sendErrorResponse("Error updating course pricing: " . $e->getMessage());
    }
}

/**
 * Format course data for admin view
 */
function formatCourseAdminData($row) {
    return [
        'course_id' => (int)$row['course_id'],
        'title' => $row['title'],
        'rating' => (float)$row['rate'],
        'duration_months' => (int)$row['duration_date'],
        'description' => $row['description'],
        'requirements' => $row['requirement'],
        'difficulty' => $row['difficulty'],
        'course_type' => $row['course_type'],
        'price' => (float)$row['price'],
        'thumbnail' => $row['thumbnail'],
        'is_published' => (bool)$row['is_published'],
        'created_at' => $row['created_at'],
        'artist' => [
            'artist_id' => (int)$row['artist_id'],
            'name' => $row['artist_first_name'] . ' ' . $row['artist_last_name'],
            'first_name' => $row['artist_first_name'],
            'last_name' => $row['artist_last_name'],
            'email' => $row['artist_email'] ?? null,
            'profile_picture' => $row['artist_profile_picture'],
            'specialty' => $row['art_specialty'],
            'years_of_experience' => $row['years_of_experience'] ? (int)$row['years_of_experience'] : null,
            'location' => $row['artist_location']
        ],
        'enrollment_stats' => [
            'total_enrollments' => isset($row['total_enrollments']) ? (int)$row['total_enrollments'] : 0,
            'paid_enrollments' => isset($row['paid_enrollments']) ? (int)$row['paid_enrollments'] : 0,
            'total_revenue' => isset($row['total_revenue']) ? (float)$row['total_revenue'] : 0
        ]
    ];
}

/**
 * Helper function to get time condition for queries
 */
function getTimeCondition($time_period) {
    switch ($time_period) {
        case 'today':
            return "WHERE DATE(created_at) = CURDATE()";
        case 'week':
            return "WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        case 'month':
            return "WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        case 'year':
            return "WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
        default:
            return "WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    }
}

/**
 * Send success response
 */
function sendSuccessResponse($data, $message = "Success") {
    $response = [
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);
}

/**
 * Send error response
 */
function sendErrorResponse($message, $statusCode = 400) {
    $response = [
        'success' => false,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    http_response_code($statusCode);
    echo json_encode($response, JSON_PRETTY_PRINT);
}

?>