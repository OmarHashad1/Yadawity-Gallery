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
            // Get all exams with detailed user info
            $exams = $pdo->query("
                SELECT e.exam_id, e.need_doctor, e.draw_img, e.exam_date, e.status, e.results, e.created_at,
                       u.first_name, u.last_name, u.email, u.phone, u.user_id
                FROM exams e
                JOIN users u ON e.user_id = u.user_id
                ORDER BY e.exam_date DESC
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Exam statistics
            $stats = [
                'total_exams' => count($exams),
                'pending_exams' => count(array_filter($exams, function($e) { return $e['status'] === 'pending'; })),
                'completed_exams' => count(array_filter($exams, function($e) { return $e['status'] === 'completed'; })),
                'cancelled_exams' => count(array_filter($exams, function($e) { return $e['status'] === 'cancelled'; })),
                'need_doctor_review' => count(array_filter($exams, function($e) { return $e['need_doctor']; })),
                'with_drawings' => count(array_filter($exams, function($e) { return !empty($e['draw_img']); }))
            ];
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'exams' => $exams,
                    'statistics' => $stats
                ]
            ]);
            break;
            
        case 'PUT':
            // Update exam status or add results
            $input = json_decode(file_get_contents('php://input'), true);
            $examId = $input['exam_id'];
            $status = $input['status'] ?? null;
            $results = $input['results'] ?? null;
            $needDoctor = $input['need_doctor'] ?? null;
            
            $updates = [];
            $params = [];
            
            if ($status) {
                $updates[] = "status = ?";
                $params[] = $status;
            }
            if ($results) {
                $updates[] = "results = ?";
                $params[] = $results;
            }
            if ($needDoctor !== null) {
                $updates[] = "need_doctor = ?";
                $params[] = $needDoctor;
            }
            
            if (!empty($updates)) {
                $params[] = $examId;
                $sql = "UPDATE exams SET " . implode(", ", $updates) . " WHERE exam_id = ?";
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute($params);
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Exam updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update exam']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
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
