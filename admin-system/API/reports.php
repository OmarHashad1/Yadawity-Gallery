<?php
require_once "db.php";

header('Content-Type: application/json');

// Require admin authentication for all operations
require_admin();

// Check session timeout
check_session_timeout();

$allowedMethods = ['GET','POST','PUT','PATCH','DELETE','OPTIONS','HEAD'];

function send_json($data, int $status = 200): void { http_response_code($status); echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit; }
function method_not_allowed(array $methods): void { header('Allow: ' . implode(', ', $methods)); send_json(['error' => 'Method Not Allowed'], 405); }
function parse_json_body(): array { $raw = file_get_contents('php://input'); $b = json_decode($raw, true); return is_array($b) ? $b : []; }
function sanitize($v){ return is_array($v) ? array_map('sanitize',$v) : htmlspecialchars(trim((string)$v), ENT_QUOTES, 'UTF-8'); }
// CSRF protection is now handled by require_csrf_for_write() from auth.php

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'HEAD') { header('Allow: ' . implode(', ', $allowedMethods)); exit; }
if ($method === 'OPTIONS') { header('Allow: ' . implode(', ', $allowedMethods)); header('Access-Control-Allow-Methods: ' . implode(', ', $allowedMethods)); header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token'); exit; }
if (!in_array($method, $allowedMethods, true)) { method_not_allowed($allowedMethods); }

// POST/PUT act as report generation requests (no persistence)
if (in_array($method, ['POST','PUT','PATCH'], true)) { require_csrf_for_write(); }

try {
	if (!isset($pdo) || !($pdo instanceof PDO)) { throw new RuntimeException('Database connection not available'); }

	if (in_array($method, ['GET','POST','PUT','PATCH'], true)) {
		$params = $method === 'GET' ? $_GET : sanitize(parse_json_body());
		$type = $params['type'] ?? 'sales_summary';
		$from = isset($params['from']) ? substr((string)$params['from'],0,10) : null;
		$to   = isset($params['to']) ? substr((string)$params['to'],0,10) : null;

		$payload = ['type' => $type, 'range' => ['from' => $from, 'to' => $to]];

		switch ($type) {
			case 'sales_summary':
				if ($from && $to) { $stmt = $pdo->prepare('SELECT status, COUNT(*) cnt, COALESCE(SUM(total_amount),0) revenue FROM orders WHERE order_date BETWEEN ? AND ? GROUP BY status'); $stmt->execute([$from,$to]); }
				else { $stmt = $pdo->query('SELECT status, COUNT(*) cnt, COALESCE(SUM(total_amount),0) revenue FROM orders GROUP BY status'); }
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$payload['data'] = ['by_status' => $rows];
				break;

			case 'artist_performance':
				if ($from && $to) { $sql = 'SELECT oi.artist_id, SUM(oi.subtotal) revenue, SUM(oi.quantity) qty FROM order_items oi INNER JOIN orders o ON oi.order_id = o.id WHERE o.order_date BETWEEN ? AND ? GROUP BY oi.artist_id ORDER BY revenue DESC LIMIT 50'; $stmt=$pdo->prepare($sql); $stmt->execute([$from,$to]); }
				else { $sql = 'SELECT artist_id, SUM(subtotal) revenue, SUM(quantity) qty FROM order_items GROUP BY artist_id ORDER BY revenue DESC LIMIT 50'; $stmt=$pdo->query($sql); }
				$payload['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
				break;

			case 'inventory':
				$stmt = $pdo->query('SELECT artwork_id, artist_id, title, price, type, is_available, on_auction, created_at FROM artworks ORDER BY created_at DESC LIMIT 200');
				$payload['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
				break;

			default:
				send_json(['error' => 'Unsupported report type'], 422);
		}

		send_json($payload);
	}

	if ($method === 'DELETE') { method_not_allowed($allowedMethods); }

	method_not_allowed($allowedMethods);
} catch (PDOException $e) { send_json(['error' => 'Database error', 'details' => $e->getMessage()], 500); } catch (Throwable $e) { send_json(['error' => 'Server error', 'details' => $e->getMessage()], 500); }


