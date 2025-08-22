<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db.php'; // Include database connection

try {
    // Query to get all user login sessions
    $query = "SELECT * FROM user_login_sessions ORDER BY login_time DESC";
    $result = $db->query($query);
    
    if (!$result) {
        http_response_code(500);
        echo json_encode(['error' => 'Query failed: ' . $db->error]);
        exit();
    }
    
    // Fetch all sessions
    $sessions = [];
    while ($row = $result->fetch_assoc()) {
        $sessions[] = $row;
    }
    
    // Return success response with sessions
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $sessions,
        'count' => count($sessions)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
} finally {
    // Close database connection
    $db->close();
}
?>