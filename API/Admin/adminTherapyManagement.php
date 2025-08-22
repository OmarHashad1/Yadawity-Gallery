<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Get all art therapy exams
    $exams = $pdo->query("
        SELECT e.exam_id, e.need_doctor, e.draw_img, e.exam_date, e.status, e.results,
               u.first_name, u.last_name, u.email, u.phone
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
        'need_doctor' => count(array_filter($exams, function($e) { return $e['need_doctor']; }))
    ];

    echo json_encode([
        'success' => true,
        'data' => [
            'exams' => $exams,
            'statistics' => $stats
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
