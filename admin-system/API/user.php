<?php
require_once "db.php";

header('Content-Type: application/json');

// Require admin authentication for all operations
require_admin();

// Check session timeout
check_session_timeout();

$allowedMethods = ['GET','POST','PUT','PATCH','DELETE','OPTIONS','HEAD'];

function send_json($data, int $status = 200): void {
	http_response_code($status);
	echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	exit;
}

function method_not_allowed(array $methods): void {
	header('Allow: ' . implode(', ', $methods));
	send_json(['error' => 'Method Not Allowed'], 405);
}

function parse_json_body(): array {
	$raw = file_get_contents('php://input');
	$body = json_decode($raw, true);
	return is_array($body) ? $body : [];
}

function sanitize($value) {
	if (is_array($value)) {
		return array_map('sanitize', $value);
	}
	return htmlspecialchars(trim((string)$value), ENT_QUOTES, 'UTF-8');
}

// CSRF protection is now handled by require_csrf_for_write() from auth.php

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'HEAD') {
	header('Allow: ' . implode(', ', $allowedMethods));
	exit;
}

if ($method === 'OPTIONS') {
	header('Allow: ' . implode(', ', $allowedMethods));
	header('Access-Control-Allow-Methods: ' . implode(', ', $allowedMethods));
	header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');
	exit;
}

if (!in_array($method, $allowedMethods, true)) {
	method_not_allowed($allowedMethods);
}

require_csrf_for_write();

try {
	if (!isset($pdo) || !($pdo instanceof PDO)) {
		throw new RuntimeException('Database connection not available');
	}

	if ($method === 'GET') {
		$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
		if ($id) {
			$stmt = $pdo->prepare('SELECT user_id, email, first_name, last_name, phone, user_type, profile_picture, img, national_id, bio, is_active, art_specialty, years_of_experience, achievements, artist_bio, location, education, created_at FROM users WHERE user_id = ?');
			$stmt->execute([$id]);
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			if (!$user) { send_json(['error' => 'User not found'], 404); }
			send_json(['data' => $user]);
		}
		$page = max(1, (int)($_GET['page'] ?? 1));
		$limit = min(100, max(1, (int)($_GET['limit'] ?? 20)));
		$offset = ($page - 1) * $limit;
		$search = isset($_GET['q']) ? '%' . ($_GET['q']) . '%' : null;
		if ($search) {
			$stmt = $pdo->prepare('SELECT SQL_CALC_FOUND_ROWS user_id, email, first_name, last_name, phone, user_type, profile_picture, img, is_active, created_at FROM users WHERE email LIKE ? OR first_name LIKE ? OR last_name LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?');
			$stmt->bindValue(1, $search, PDO::PARAM_STR);
			$stmt->bindValue(2, $search, PDO::PARAM_STR);
			$stmt->bindValue(3, $search, PDO::PARAM_STR);
			$stmt->bindValue(4, $limit, PDO::PARAM_INT);
			$stmt->bindValue(5, $offset, PDO::PARAM_INT);
			$stmt->execute();
		} else {
			$stmt = $pdo->prepare('SELECT SQL_CALC_FOUND_ROWS user_id, email, first_name, last_name, phone, user_type, profile_picture, img, is_active, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?');
			$stmt->bindValue(1, $limit, PDO::PARAM_INT);
			$stmt->bindValue(2, $offset, PDO::PARAM_INT);
			$stmt->execute();
		}
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$total = (int)$pdo->query('SELECT FOUND_ROWS()')->fetchColumn();
		send_json(['data' => $rows, 'meta' => ['page' => $page, 'limit' => $limit, 'total' => $total]]);
	}

	if ($method === 'POST') {
		$body = sanitize(parse_json_body());
		$email = $body['email'] ?? '';
		$password = $body['password'] ?? '';
		$first = $body['first_name'] ?? '';
		$last = $body['last_name'] ?? '';
		$userType = $body['user_type'] ?? 'buyer';
		$phone = $body['phone'] ?? null;
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { send_json(['error' => 'Invalid email'], 422); }
		if (strlen($password) < 6) { send_json(['error' => 'Password too short'], 422); }
		if (!in_array($userType, ['artist','buyer','admin'], true)) { send_json(['error' => 'Invalid user_type'], 422); }
		$hash = password_hash($password, PASSWORD_BCRYPT);
		$stmt = $pdo->prepare('INSERT INTO users (email, password, first_name, last_name, phone, user_type) VALUES (:email, :password, :first_name, :last_name, :phone, :user_type)');
		$stmt->execute([
			':email' => $email,
			':password' => $hash,
			':first_name' => $first,
			':last_name' => $last,
			':phone' => $phone,
			':user_type' => $userType,
		]);
		$id = (int)$pdo->lastInsertId();
		send_json(['message' => 'User created', 'id' => $id], 201);
	}

	if ($method === 'PUT' || $method === 'PATCH') {
		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		if ($id <= 0) { send_json(['error' => 'Missing id'], 400); }
		$body = sanitize(parse_json_body());
		$fields = [];
		$params = [];
		if (isset($body['email'])) {
			if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL)) { send_json(['error' => 'Invalid email'], 422); }
			$fields[] = 'email = :email';
			$params[':email'] = $body['email'];
		}
		if (isset($body['first_name'])) { $fields[] = 'first_name = :first_name'; $params[':first_name'] = $body['first_name']; }
		if (isset($body['last_name'])) { $fields[] = 'last_name = :last_name'; $params[':last_name'] = $body['last_name']; }
		if (isset($body['phone'])) { $fields[] = 'phone = :phone'; $params[':phone'] = $body['phone']; }
		if (isset($body['user_type'])) {
			if (!in_array($body['user_type'], ['artist','buyer','admin'], true)) { send_json(['error' => 'Invalid user_type'], 422); }
			$fields[] = 'user_type = :user_type';
			$params[':user_type'] = $body['user_type'];
		}
		if (isset($body['is_active'])) { $fields[] = 'is_active = :is_active'; $params[':is_active'] = (int)!!$body['is_active']; }
		if (isset($body['password'])) {
			if (strlen((string)$body['password']) < 6) { send_json(['error' => 'Password too short'], 422); }
			$fields[] = 'password = :password';
			$params[':password'] = password_hash((string)$body['password'], PASSWORD_BCRYPT);
		}
		if (empty($fields)) { send_json(['error' => 'No fields to update'], 400); }
		$params[':id'] = $id;
		$sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE user_id = :id';
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		send_json(['message' => 'User updated']);
	}

	if ($method === 'DELETE') {
		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		if ($id <= 0) { send_json(['error' => 'Missing id'], 400); }
		$stmt = $pdo->prepare('DELETE FROM users WHERE user_id = ?');
		$stmt->execute([$id]);
		if ($stmt->rowCount() === 0) { send_json(['error' => 'User not found'], 404); }
		send_json(['message' => 'User deleted']);
	}

	method_not_allowed($allowedMethods);
} catch (PDOException $e) {
	$code = $e->errorInfo[1] ?? 0;
	if ($code === 1062) { send_json(['error' => 'Duplicate entry'], 409); }
	send_json(['error' => 'Database error', 'details' => $e->getMessage()], 500);
} catch (Throwable $e) {
	send_json(['error' => 'Server error', 'details' => $e->getMessage()], 500);
}


