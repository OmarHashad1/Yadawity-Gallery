<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    // Get total users
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    
    // Get total artists
    $totalArtists = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'artist'")->fetchColumn();
    
    // Get total buyers
    $totalBuyers = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'buyer'")->fetchColumn();
    
    // Get total artworks
    $totalArtworks = $pdo->query("SELECT COUNT(*) FROM artworks")->fetchColumn();
    
    // Get total active auctions
    $activeAuctions = $pdo->query("SELECT COUNT(*) FROM auctions WHERE status = 'active'")->fetchColumn();
    
    // Get total courses
    $totalCourses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    
    // Get total orders
    $totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    
    // Get total galleries
    $totalGalleries = $pdo->query("SELECT COUNT(*) FROM galleries")->fetchColumn();

    echo json_encode([
        'success' => true,
        'data' => [
            'total_users' => $totalUsers,
            'total_artists' => $totalArtists,
            'total_buyers' => $totalBuyers,
            'total_artworks' => $totalArtworks,
            'active_auctions' => $activeAuctions,
            'total_courses' => $totalCourses,
            'total_orders' => $totalOrders,
            'total_galleries' => $totalGalleries
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching dashboard data: ' . $e->getMessage()
    ]);
}
?>
