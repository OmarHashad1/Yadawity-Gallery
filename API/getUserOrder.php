<?php

require_once "db.php";
header('Content-Type: application/json');

// Get user_id from user_login cookie (token)
if (!isset($_COOKIE['user_login'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}
$token = $_COOKIE['user_login'];

// Lookup user_id from user_login_sessions table using the token
$stmt = $db->prepare("SELECT user_id FROM user_login_sessions WHERE session_id = ? AND is_active = 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->bind_result($user_id);
$found = $stmt->fetch();
$stmt->close();
if (!$found || !$user_id) {
    echo json_encode(['error' => 'Invalid or expired login token']);
    exit;
}

// Get all orders for this user
$sql = "SELECT o.id AS order_id, o.total_amount, o.status, o.created_at, 
               oi.id AS order_item_id, oi.artwork_id, oi.price, oi.quantity,
               a.title AS artwork_title, a.artwork_image
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN artworks a ON oi.artwork_id = a.artwork_id
        WHERE o.buyer_id = ?
        ORDER BY o.created_at DESC, o.id DESC";

$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $oid = $row['order_id'];
    if (!isset($orders[$oid])) {
        $orders[$oid] = [
            'order_id' => $oid,
            'total_amount' => $row['total_amount'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
            'items' => []
        ];
    }
    $orders[$oid]['items'][] = [
        'order_item_id' => $row['order_item_id'],
        'artwork_id' => $row['artwork_id'],
        'artwork_title' => $row['artwork_title'],
        'artwork_image' => $row['artwork_image'],
        'price' => $row['price'],
        'quantity' => $row['quantity']
    ];
}
$stmt->close();

echo json_encode(array_values($orders));
