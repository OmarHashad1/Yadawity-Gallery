<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Course statistics
    $totalCourses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    $publishedCourses = $pdo->query("SELECT COUNT(*) FROM courses WHERE is_published = 1")->fetchColumn();
    $unpublishedCourses = $pdo->query("SELECT COUNT(*) FROM courses WHERE is_published = 0")->fetchColumn();
    $totalEnrollments = $pdo->query("SELECT COUNT(*) FROM course_enrollments")->fetchColumn();
    
    // Course by type and difficulty
    $courseTypes = $pdo->query("
        SELECT course_type, COUNT(*) as count, AVG(price) as avg_price
        FROM courses 
        GROUP BY course_type
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    $courseDifficulty = $pdo->query("
        SELECT difficulty, COUNT(*) as count, AVG(price) as avg_price
        FROM courses 
        GROUP BY difficulty
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Top courses by enrollment
    $topCourses = $pdo->query("
        SELECT c.title, c.price, c.difficulty, c.course_type, 
               u.first_name, u.last_name, COUNT(ce.id) as enrollment_count
        FROM courses c
        JOIN users u ON c.artist_id = u.user_id
        LEFT JOIN course_enrollments ce ON c.course_id = ce.course_id
        GROUP BY c.course_id
        ORDER BY enrollment_count DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Revenue by course
    $courseRevenue = $pdo->query("
        SELECT c.title, c.price, COUNT(ce.id) as paid_enrollments,
               (c.price * COUNT(ce.id)) as total_revenue
        FROM courses c
        LEFT JOIN course_enrollments ce ON c.course_id = ce.course_id AND ce.is_payed = 1
        GROUP BY c.course_id
        ORDER BY total_revenue DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'summary' => [
                'total_courses' => $totalCourses,
                'published_courses' => $publishedCourses,
                'unpublished_courses' => $unpublishedCourses,
                'total_enrollments' => $totalEnrollments
            ],
            'course_types' => $courseTypes,
            'course_difficulty' => $courseDifficulty,
            'top_courses' => $topCourses,
            'course_revenue' => $courseRevenue
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
