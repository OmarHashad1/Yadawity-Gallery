<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include_once 'db.php';

try {
    $userId = $_GET['user_id'] ?? '';
    
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        exit;
    }
    
    // Get user details
    $stmt = $pdo->prepare("
        SELECT user_id, email, first_name, last_name, phone, user_type, profile_picture, 
               bio, is_active, art_specialty, years_of_experience, achievements, 
               artist_bio, location, education, created_at
        FROM users 
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    // Get user's artworks if artist
    $artworks = [];
    if ($user['user_type'] === 'artist') {
        $stmt = $pdo->prepare("
            SELECT artwork_id, title, price, type, is_available, created_at
            FROM artworks 
            WHERE artist_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        $artworks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get user's orders if buyer
    $orders = [];
    if ($user['user_type'] === 'buyer') {
        $stmt = $pdo->prepare("
            SELECT id, total_amount, status, created_at
            FROM orders 
            WHERE buyer_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'user' => $user,
            'artworks' => $artworks,
            'orders' => $orders
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
