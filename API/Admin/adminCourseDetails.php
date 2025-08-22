<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    $courseId = $_GET['course_id'] ?? '';
    
    if (!$courseId) {
        echo json_encode(['success' => false, 'message' => 'Course ID required']);
        exit;
    }
    
    // Get course details
    $stmt = $pdo->prepare("
        SELECT c.*, u.first_name, u.last_name, u.email, u.art_specialty
        FROM courses c
        JOIN users u ON c.artist_id = u.user_id
        WHERE c.course_id = ?
    ");
    $stmt->execute([$courseId]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        echo json_encode(['success' => false, 'message' => 'Course not found']);
        exit;
    }
    
    // Get enrollments
    $stmt = $pdo->prepare("
        SELECT ce.enrollment_date, ce.is_payed, ce.is_active,
               u.first_name, u.last_name, u.email
        FROM course_enrollments ce
        JOIN users u ON ce.user_id = u.user_id
        WHERE ce.course_id = ?
        ORDER BY ce.enrollment_date DESC
    ");
    $stmt->execute([$courseId]);
    $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get enrollment statistics
    $totalEnrollments = count($enrollments);
    $paidEnrollments = count(array_filter($enrollments, function($e) { return $e['is_payed']; }));
    $activeEnrollments = count(array_filter($enrollments, function($e) { return $e['is_active']; }));

    echo json_encode([
        'success' => true,
        'data' => [
            'course' => $course,
            'enrollments' => $enrollments,
            'stats' => [
                'total_enrollments' => $totalEnrollments,
                'paid_enrollments' => $paidEnrollments,
                'active_enrollments' => $activeEnrollments,
                'revenue' => $paidEnrollments * $course['price']
            ]
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
