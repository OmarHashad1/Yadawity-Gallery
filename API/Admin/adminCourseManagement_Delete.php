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
    $hardDelete = $input['hard_delete'] ?? false;
    
    // Check if course exists
    $checkStmt = $pdo->prepare("
        SELECT c.course_id, c.title, u.first_name, u.last_name 
        FROM courses c 
        JOIN users u ON c.artist_id = u.user_id 
        WHERE c.course_id = ?
    ");
    $checkStmt->execute([$courseId]);
    $course = $checkStmt->fetch();
    
    if (!$course) {
        sendResponse(false, 'Course not found', null, 404);
    }
    
    // Check if course has enrollments
    $enrollmentCheck = $pdo->prepare("SELECT COUNT(*) as enrollment_count FROM course_enrollments WHERE course_id = ?");
    $enrollmentCheck->execute([$courseId]);
    $enrollmentCount = $enrollmentCheck->fetch()['enrollment_count'];
    
    if ($enrollmentCount > 0 && !$hardDelete) {
        sendResponse(false, 'Cannot delete course with enrollments. Use hard delete to force removal.', null, 400);
    }
    
    $pdo->beginTransaction();
    
    try {
        if ($hardDelete) {
            // Hard delete - completely remove course and related data
            $pdo->prepare("DELETE FROM course_enrollments WHERE course_id = ?")->execute([$courseId]);
            $stmt = $pdo->prepare("DELETE FROM courses WHERE course_id = ?");
            $result = $stmt->execute([$courseId]);
            
            $action = "permanently deleted";
            
        } else {
            // Soft delete - unpublish course
            $stmt = $pdo->prepare("UPDATE courses SET is_published = 0 WHERE course_id = ?");
            $result = $stmt->execute([$courseId]);
            
            $action = "unpublished";
        }
        
        if (!$result) {
            throw new Exception('Failed to delete course');
        }
        
        $pdo->commit();
        
        sendResponse(true, "Course '{$course['title']}' by {$course['first_name']} {$course['last_name']} $action successfully");
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    sendResponse(false, 'Error deleting course: ' . $e->getMessage(), null, 500);
}
?>
