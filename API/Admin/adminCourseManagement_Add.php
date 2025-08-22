<?php
include_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, 'Invalid JSON input', null, 400);
    }
    
    // Validate required fields
    $required = ['title', 'artist_id', 'duration_date', 'difficulty', 'course_type', 'price'];
    $error = validateRequired($input, $required);
    if ($error) {
        sendResponse(false, $error, null, 400);
    }
    
    $title = $input['title'];
    $artistId = $input['artist_id'];
    $durationDate = $input['duration_date'];
    $description = $input['description'] ?? '';
    $requirement = $input['requirement'] ?? '';
    $difficulty = $input['difficulty'];
    $courseType = $input['course_type'];
    $price = $input['price'];
    $thumbnail = $input['thumbnail'] ?? null;
    $isPublished = $input['is_published'] ?? 0;
    
    // Validate instructor exists and is an artist
    $artistCheck = $pdo->prepare("SELECT user_id FROM users WHERE user_id = ? AND user_type = 'artist'");
    $artistCheck->execute([$artistId]);
    if (!$artistCheck->fetch()) {
        sendResponse(false, 'Instructor not found or user is not an artist', null, 404);
    }
    
    // Validate difficulty
    $validDifficulties = ['beginner', 'intermediate', 'advanced'];
    if (!in_array($difficulty, $validDifficulties)) {
        sendResponse(false, 'Invalid difficulty level', null, 400);
    }
    
    // Validate course type
    $validTypes = ['online', 'offline', 'hybrid'];
    if (!in_array($courseType, $validTypes)) {
        sendResponse(false, 'Invalid course type', null, 400);
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO courses (title, artist_id, duration_date, description, requirement, 
                           difficulty, course_type, price, thumbnail, is_published) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $title, $artistId, $durationDate, $description, $requirement, 
        $difficulty, $courseType, $price, $thumbnail, $isPublished
    ]);
    
    if ($result) {
        $courseId = $pdo->lastInsertId();
        
        // Get the created course with instructor info
        $getCourseStmt = $pdo->prepare("
            SELECT c.course_id, c.title, c.rate, c.duration_date, c.description, c.requirement,
                   c.difficulty, c.course_type, c.price, c.thumbnail, c.is_published, c.created_at,
                   u.user_id as artist_id, u.first_name, u.last_name, u.email
            FROM courses c 
            JOIN users u ON c.artist_id = u.user_id 
            WHERE c.course_id = ?
        ");
        $getCourseStmt->execute([$courseId]);
        $course = $getCourseStmt->fetch();
        
        sendResponse(true, 'Course created successfully', $course, 201);
    } else {
        sendResponse(false, 'Failed to create course', null, 500);
    }

} catch (Exception $e) {
    sendResponse(false, 'Error creating course: ' . $e->getMessage(), null, 500);
}
?>
