<?php
header('Content-Type: application/json');

// Test the database connection and check if tables exist
try {
    require_once 'API/db.php';
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Database connection successful',
        'tests' => [
            'database_connected' => true,
            'artworks_table' => checkTableExists($conn, 'artworks'),
            'artwork_photos_table' => checkTableExists($conn, 'artwork_photos'),
            'artworks_has_image_column' => checkColumnExists($conn, 'artworks', 'artwork_image'),
            'artwork_photos_has_primary_column' => checkColumnExists($conn, 'artwork_photos', 'is_primary'),
            'uploads_directory_writable' => is_writable('./uploads/artworks/') || mkdir('./uploads/artworks/', 0755, true)
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function checkTableExists($conn, $tableName) {
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");
    return $result->num_rows > 0;
}

function checkColumnExists($conn, $tableName, $columnName) {
    $result = $conn->query("SHOW COLUMNS FROM $tableName LIKE '$columnName'");
    return $result->num_rows > 0;
}
?>
