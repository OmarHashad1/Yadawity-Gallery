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

require_csrf_for_write();

$storageDir = __DIR__ . DIRECTORY_SEPARATOR . '_storage';
$settingsFile = $storageDir . DIRECTORY_SEPARATOR . 'settings.json';

function ensure_storage_exists($dir): void { if (!is_dir($dir)) { @mkdir($dir, 0775, true); } }
function load_settings($file): array {
	if (!file_exists($file)) { return default_settings(); }
	$raw = file_get_contents($file);
	$decoded = json_decode($raw, true);
	return is_array($decoded) ? $decoded : default_settings();
}
function save_settings($file, array $data): bool { return (bool)file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); }
function default_settings(): array {
	return [
		'site_name' => 'Yadawity Admin',
		'support_email' => 'support@yadawity.com',
		'currency' => 'USD',
		'csrf_required' => true,
		'maintenance_mode' => false
	];
}
function filter_allowed(array $input): array {
	$allowedKeys = ['site_name','support_email','currency','csrf_required','maintenance_mode'];
	$out = [];
	foreach ($allowedKeys as $k) { if (array_key_exists($k, $input)) { $out[$k] = $input[$k]; } }
	if (isset($out['support_email']) && !filter_var($out['support_email'], FILTER_VALIDATE_EMAIL)) { unset($out['support_email']); }
	if (isset($out['currency'])) { $out['currency'] = preg_replace('/[^A-Z]/','', strtoupper((string)$out['currency'])); $out['currency'] = substr($out['currency'], 0, 3); }
	if (isset($out['csrf_required'])) { $out['csrf_required'] = (bool)$out['csrf_required']; }
	if (isset($out['maintenance_mode'])) { $out['maintenance_mode'] = (bool)$out['maintenance_mode']; }
	return $out;
}

try {
	if ($method === 'GET') {
		ensure_storage_exists($storageDir);
		$data = load_settings($settingsFile);
		send_json(['data' => $data]);
	}

	if ($method === 'POST') {
		ensure_storage_exists($storageDir);
		$current = load_settings($settingsFile);
		$body = sanitize(parse_json_body());
		$update = filter_allowed($body);
		if (empty($update)) { send_json(['error' => 'No valid settings provided'], 422); }
		$merged = array_merge($current, $update);
		if (!save_settings($settingsFile, $merged)) { send_json(['error' => 'Failed to save settings'], 500); }
		send_json(['message' => 'Settings saved', 'data' => $merged]);
	}

	if ($method === 'PUT' || $method === 'PATCH') {
		ensure_storage_exists($storageDir);
		$current = load_settings($settingsFile);
		$body = sanitize(parse_json_body());
		$update = filter_allowed($body);
		if (empty($update)) { send_json(['error' => 'No valid settings provided'], 422); }
		$merged = array_merge($current, $update);
		if (!save_settings($settingsFile, $merged)) { send_json(['error' => 'Failed to save settings'], 500); }
		send_json(['message' => 'Settings updated', 'data' => $merged]);
	}

	if ($method === 'DELETE') {
		ensure_storage_exists($storageDir);
		$current = load_settings($settingsFile);
		$params = sanitize(parse_json_body());
		$key = $params['key'] ?? ($_GET['key'] ?? null);
		if ($key === 'all') { $defaults = default_settings(); save_settings($settingsFile, $defaults); send_json(['message' => 'Settings reset to defaults', 'data' => $defaults]); }
		if (!$key) { send_json(['error' => 'Missing key'], 400); }
		if (!array_key_exists($key, $current)) { send_json(['error' => 'Key not found'], 404); }
		unset($current[$key]);
		if (!save_settings($settingsFile, $current)) { send_json(['error' => 'Failed to save settings'], 500); }
		send_json(['message' => 'Setting deleted', 'data' => $current]);
	}

	method_not_allowed($allowedMethods);
} catch (Throwable $e) { send_json(['error' => 'Server error', 'details' => $e->getMessage()], 500); }


