<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Gallery statistics
    $totalGalleries = $pdo->query("SELECT COUNT(*) FROM galleries")->fetchColumn();
    $activeGalleries = $pdo->query("SELECT COUNT(*) FROM galleries WHERE is_active = 1")->fetchColumn();
    $inactiveGalleries = $pdo->query("SELECT COUNT(*) FROM galleries WHERE is_active = 0")->fetchColumn();
    $virtualGalleries = $pdo->query("SELECT COUNT(*) FROM galleries WHERE gallery_type = 'virtual'")->fetchColumn();
    $physicalGalleries = $pdo->query("SELECT COUNT(*) FROM galleries WHERE gallery_type = 'physical'")->fetchColumn();
    
    // Gallery revenue (virtual galleries only)
    $virtualRevenue = $pdo->query("
        SELECT COALESCE(SUM(price), 0) as total_revenue
        FROM galleries 
        WHERE gallery_type = 'virtual' AND is_active = 1
    ")->fetchColumn();
    
    // Galleries by city (physical only)
    $galleriesByCity = $pdo->query("
        SELECT city, COUNT(*) as count
        FROM galleries 
        WHERE gallery_type = 'physical' AND city IS NOT NULL
        GROUP BY city
        ORDER BY count DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent galleries
    $recentGalleries = $pdo->query("
        SELECT g.gallery_id, g.title, g.gallery_type, g.price, g.city, g.is_active, g.created_at,
               u.first_name, u.last_name
        FROM galleries g
        JOIN users u ON g.artist_id = u.user_id
        ORDER BY g.created_at DESC
        LIMIT 15
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Average duration by type
    $avgDuration = $pdo->query("
        SELECT gallery_type, AVG(duration) as avg_duration
        FROM galleries
        WHERE duration > 0
        GROUP BY gallery_type
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'summary' => [
                'total_galleries' => $totalGalleries,
                'active_galleries' => $activeGalleries,
                'inactive_galleries' => $inactiveGalleries,
                'virtual_galleries' => $virtualGalleries,
                'physical_galleries' => $physicalGalleries,
                'virtual_revenue' => round($virtualRevenue, 2)
            ],
            'galleries_by_city' => $galleriesByCity,
            'recent_galleries' => $recentGalleries,
            'average_duration' => $avgDuration
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
