<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT');
header('Access-Control-Allow-Headers: Content-Type');

include_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get all courses with artist info
            $sql = "SELECT c.course_id, c.title, c.price, c.difficulty, c.course_type, c.is_published, c.created_at,
                           u.first_name, u.last_name, u.email
                    FROM courses c 
                    JOIN users u ON c.artist_id = u.user_id 
                    ORDER BY c.created_at DESC";
            
            $stmt = $pdo->query($sql);
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $courses
            ]);
            break;
            
        case 'PUT':
            // Update course publish status
            $input = json_decode(file_get_contents('php://input'), true);
            $courseId = $input['course_id'];
            $isPublished = $input['is_published'];
            
            $stmt = $pdo->prepare("UPDATE courses SET is_published = ? WHERE course_id = ?");
            $result = $stmt->execute([$isPublished, $courseId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Course updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update course']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
