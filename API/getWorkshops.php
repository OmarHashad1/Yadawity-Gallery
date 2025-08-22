<?php
require_once "db.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	http_response_code(200);
	exit();
}


// Query workshops
$sql = "SELECT workshop_id, title, category, city, street, date, capacity, open_time, price, doctor_name, doctor_description, doctor_photo, workshop_description, workshop_photo, created_at, is_active FROM workshops WHERE is_active = 1 ORDER BY date ASC";
$result = $db->query($sql);

// helper to find an image file in ../image/; tries exact name then basename.* matches
function find_image_file($filename) {
	$imageDir = __DIR__ . '/../image/';
	if (!$filename) return null;
	$full = $imageDir . $filename;
	if (file_exists($full)) return $filename;
	// try matching basename with any extension (case-insensitive)
	$basename = pathinfo($filename, PATHINFO_FILENAME);
	$matches = glob($imageDir . $basename . '.*');
	if ($matches && count($matches) > 0) {
		// return the filename portion
		return basename($matches[0]);
	}
	// try case-insensitive search
	$files = scandir($imageDir);
	foreach ($files as $f) {
		if (stripos($f, $basename) !== false) return $f;
	}
	return null;
}

$workshops = [];
if ($result === false) {
	// SQL error, output error message for debugging
	echo json_encode([
		"success" => false,
		"error" => "Database query failed",
		"sql_error" => $db->error
	]);
	exit;
}

if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		// Resolve image filenames; return filename or null so frontend can use placeholder
		$workshopPhoto = find_image_file($row['workshop_photo']);
		$doctorPhoto = find_image_file($row['doctor_photo']);

	$workshops[] = [
			'id' => $row['workshop_id'],
			'title' => $row['title'],
			'category' => $row['category'],
			'city' => $row['city'],
			'street' => $row['street'],
			'date' => $row['date'],
			'capacity' => (int)$row['capacity'],
			'open_time' => $row['open_time'],
			'price' => (float)$row['price'],
			'doctor_name' => $row['doctor_name'],
			'doctor_description' => $row['doctor_description'],
			'doctor_photo' => $doctorPhoto ? $doctorPhoto : 'placeholder-artwork.jpg', // filename or placeholder
			'workshop_description' => $row['workshop_description'],
			'workshop_photo' => $workshopPhoto ? $workshopPhoto : 'placeholder-artwork.jpg', // filename or placeholder
			'created_at' => $row['created_at'],
		];
	}
}

echo json_encode(["success" => true, "data" => $workshops]);
exit;
