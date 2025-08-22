<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

include_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get all users with optional filters
            $userType = $_GET['user_type'] ?? '';
            $isActive = $_GET['is_active'] ?? '';
            
            $sql = "SELECT user_id, email, first_name, last_name, phone, user_type, is_active, created_at FROM users WHERE 1=1";
            $params = [];
            
            if ($userType) {
                $sql .= " AND user_type = ?";
                $params[] = $userType;
            }
            
            if ($isActive !== '') {
                $sql .= " AND is_active = ?";
                $params[] = $isActive;
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $users
            ]);
            break;
            
        case 'POST':
            // Create new user
            $input = json_decode(file_get_contents('php://input'), true);
            $email = $input['email'];
            $password = password_hash($input['password'], PASSWORD_DEFAULT);
            $firstName = $input['first_name'];
            $lastName = $input['last_name'];
            $phone = $input['phone'] ?? '';
            $userType = $input['user_type'] ?? 'user';
            
            $stmt = $pdo->prepare("INSERT INTO users (email, password, first_name, last_name, phone, user_type) VALUES (?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([$email, $password, $firstName, $lastName, $phone, $userType]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'User created successfully', 'user_id' => $pdo->lastInsertId()]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create user']);
            }
            break;
            
        case 'PUT':
            // Update user information
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $input['user_id'];
            $updateFields = [];
            $params = [];
            
            if (isset($input['first_name'])) {
                $updateFields[] = "first_name = ?";
                $params[] = $input['first_name'];
            }
            if (isset($input['last_name'])) {
                $updateFields[] = "last_name = ?";
                $params[] = $input['last_name'];
            }
            if (isset($input['email'])) {
                $updateFields[] = "email = ?";
                $params[] = $input['email'];
            }
            if (isset($input['phone'])) {
                $updateFields[] = "phone = ?";
                $params[] = $input['phone'];
            }
            if (isset($input['is_active'])) {
                $updateFields[] = "is_active = ?";
                $params[] = $input['is_active'];
            }
            if (isset($input['user_type'])) {
                $updateFields[] = "user_type = ?";
                $params[] = $input['user_type'];
            }
            
            $params[] = $userId;
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE user_id = ?";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update user']);
            }
            break;
            
        case 'DELETE':
            // Deactivate user (soft delete)
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $input['user_id'];
            
            $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE user_id = ?");
            $result = $stmt->execute([$userId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'User deactivated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to deactivate user']);
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
