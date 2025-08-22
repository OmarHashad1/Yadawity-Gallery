<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get all course enrollments with student and course info
            $enrollments = $pdo->query("
                SELECT ce.id, ce.enrollment_date, ce.is_payed, ce.is_active,
                       u.first_name, u.last_name, u.email, u.phone,
                       c.title as course_title, c.price, c.difficulty,
                       artist.first_name as artist_first_name, artist.last_name as artist_last_name
                FROM course_enrollments ce
                JOIN users u ON ce.user_id = u.user_id
                JOIN courses c ON ce.course_id = c.course_id
                JOIN users artist ON c.artist_id = artist.user_id
                ORDER BY ce.enrollment_date DESC
                LIMIT 100
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Enrollment statistics
            $stats = [
                'total_enrollments' => count($enrollments),
                'paid_enrollments' => count(array_filter($enrollments, function($e) { return $e['is_payed']; })),
                'active_enrollments' => count(array_filter($enrollments, function($e) { return $e['is_active']; })),
                'total_revenue' => array_sum(array_map(function($e) { 
                    return $e['is_payed'] ? $e['price'] : 0; 
                }, $enrollments))
            ];
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'enrollments' => $enrollments,
                    'statistics' => $stats
                ]
            ]);
            break;
            
        case 'PUT':
            // Update enrollment status
            $input = json_decode(file_get_contents('php://input'), true);
            $enrollmentId = $input['enrollment_id'];
            $field = $input['field']; // 'is_payed' or 'is_active'
            $value = $input['value'];
            
            if (in_array($field, ['is_payed', 'is_active'])) {
                $stmt = $pdo->prepare("UPDATE course_enrollments SET $field = ? WHERE id = ?");
                $result = $stmt->execute([$value, $enrollmentId]);
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Enrollment updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update enrollment']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid field']);
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
