<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Get all active user sessions
    $sessions = $pdo->query("
        SELECT uls.session_id, uls.user_id, uls.login_time, uls.expires_at, uls.is_active,
               u.first_name, u.last_name, u.email, u.user_type
        FROM user_login_sessions uls
        JOIN users u ON uls.user_id = u.user_id
        ORDER BY uls.login_time DESC
        LIMIT 100
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Session statistics
    $stats = [
        'total_sessions' => count($sessions),
        'active_sessions' => count(array_filter($sessions, function($s) { return $s['is_active']; })),
        'expired_sessions' => count(array_filter($sessions, function($s) { return !$s['is_active']; })),
        'artist_sessions' => count(array_filter($sessions, function($s) { return $s['user_type'] === 'artist' && $s['is_active']; })),
        'buyer_sessions' => count(array_filter($sessions, function($s) { return $s['user_type'] === 'buyer' && $s['is_active']; }))
    ];

    echo json_encode([
        'success' => true,
        'data' => [
            'sessions' => $sessions,
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
