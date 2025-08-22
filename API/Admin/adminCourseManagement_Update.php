<?php
include_once 'db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendResponse(false, 'Invalid JSON input', null, 400);
    }
    
    // Validate required fields
    $error = validateRequired($input, ['course_id']);
    if ($error) {
        sendResponse(false, $error, null, 400);
    }
    
    $courseId = $input['course_id'];
    
    // Check if course exists
    $checkStmt = $pdo->prepare("SELECT course_id FROM courses WHERE course_id = ?");
    $checkStmt->execute([$courseId]);
    if (!$checkStmt->fetch()) {
        sendResponse(false, 'Course not found', null, 404);
    }
    
    $updateFields = [];
    $params = [];
    
    // Build dynamic update query
    $allowedFields = [
        'title', 'rate', 'duration_date', 'description', 'requirement', 
        'difficulty', 'course_type', 'price', 'thumbnail', 'is_published'
    ];
    
    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updateFields[] = "$field = ?";
            $params[] = $input[$field];
        }
    }
    
    if (empty($updateFields)) {
        sendResponse(false, 'No valid fields to update', null, 400);
    }
    
    // Validate difficulty if being updated
    if (isset($input['difficulty'])) {
        $validDifficulties = ['beginner', 'intermediate', 'advanced'];
        if (!in_array($input['difficulty'], $validDifficulties)) {
            sendResponse(false, 'Invalid difficulty level', null, 400);
        }
    }
    
    // Validate course type if being updated
    if (isset($input['course_type'])) {
        $validTypes = ['online', 'offline', 'hybrid'];
        if (!in_array($input['course_type'], $validTypes)) {
            sendResponse(false, 'Invalid course type', null, 400);
        }
    }
    
    $params[] = $courseId;
    $sql = "UPDATE courses SET " . implode(', ', $updateFields) . " WHERE course_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);
    
    if ($result) {
        // Get updated course data with instructor info
        $getCourseStmt = $pdo->prepare("
            SELECT c.course_id, c.title, c.rate, c.duration_date, c.description, c.requirement,
                   c.difficulty, c.course_type, c.price, c.thumbnail, c.is_published, c.created_at,
                   u.user_id as artist_id, u.first_name, u.last_name, u.email,
                   COUNT(ce.id) as enrollment_count
            FROM courses c 
            JOIN users u ON c.artist_id = u.user_id 
            LEFT JOIN course_enrollments ce ON c.course_id = ce.course_id AND ce.is_active = 1
            WHERE c.course_id = ?
            GROUP BY c.course_id
        ");
        $getCourseStmt->execute([$courseId]);
        $course = $getCourseStmt->fetch();
        
        sendResponse(true, 'Course updated successfully', $course);
    } else {
        sendResponse(false, 'Failed to update course', null, 500);
    }

} catch (Exception $e) {
    sendResponse(false, 'Error updating course: ' . $e->getMessage(), null, 500);
}
?>
