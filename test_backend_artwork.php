<?php
// Test backend artwork API functionality
session_start();

// Set a test user session
$_SESSION['user_id'] = 1; // Assuming user ID 1 exists

echo "<h1>Backend API Test for Artwork Primary Images</h1>";

// Test 1: Check if the API accepts the new parameters
echo "<h2>Test 1: API Parameter Validation</h2>";

// Simulate POST data that would come from the frontend
$_POST = [
    'title' => 'Test Artwork with Primary Image',
    'price' => '500',
    'category' => 'painting',
    'description' => 'This is a test artwork to verify primary image functionality',
    'style' => 'contemporary',
    'material' => 'oil on canvas',
    'width' => '50',
    'height' => '60',
    'year' => '2025'
];

echo "<h3>Simulated POST Data:</h3>";
echo "<pre>" . print_r($_POST, true) . "</pre>";

// Test 2: Check database structure
echo "<h2>Test 2: Database Structure Validation</h2>";

require_once './API/db.php';

// Check artworks table structure
echo "<h3>Artworks Table Structure:</h3>";
$result = $conn->query("DESCRIBE artworks");
if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    $hasArtworkImageColumn = false;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
        
        if ($row['Field'] === 'artwork_image') {
            $hasArtworkImageColumn = true;
        }
    }
    echo "</table>";
    
    if ($hasArtworkImageColumn) {
        echo "<p style='color: green;'>✅ artwork_image column exists</p>";
    } else {
        echo "<p style='color: red;'>❌ artwork_image column missing</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Error checking artworks table: " . $conn->error . "</p>";
}

// Check artwork_photos table structure
echo "<h3>Artwork_Photos Table Structure:</h3>";
$result = $conn->query("DESCRIBE artwork_photos");
if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    $hasIsPrimaryColumn = false;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
        
        if ($row['Field'] === 'is_primary') {
            $hasIsPrimaryColumn = true;
        }
    }
    echo "</table>";
    
    if ($hasIsPrimaryColumn) {
        echo "<p style='color: green;'>✅ is_primary column exists</p>";
    } else {
        echo "<p style='color: red;'>❌ is_primary column missing</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Error checking artwork_photos table: " . $conn->error . "</p>";
}

// Test 3: Check upload directory
echo "<h2>Test 3: Upload Directory Validation</h2>";

$uploadDir = dirname(__FILE__) . '/uploads/artworks/';
echo "<p><strong>Upload Directory:</strong> $uploadDir</p>";

if (is_dir($uploadDir)) {
    echo "<p style='color: green;'>✅ Upload directory exists</p>";
    
    if (is_writable($uploadDir)) {
        echo "<p style='color: green;'>✅ Upload directory is writable</p>";
    } else {
        echo "<p style='color: red;'>❌ Upload directory is not writable</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Upload directory does not exist</p>";
    
    // Try to create it
    if (mkdir($uploadDir, 0755, true)) {
        echo "<p style='color: green;'>✅ Upload directory created successfully</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create upload directory</p>";
    }
}

// Test 4: Check API file syntax
echo "<h2>Test 4: API File Syntax Check</h2>";

$apiFile = './API/addArtwork.php';
if (file_exists($apiFile)) {
    echo "<p style='color: green;'>✅ addArtwork.php exists</p>";
    
    // Check for syntax errors
    $output = shell_exec("php -l $apiFile 2>&1");
    if (strpos($output, 'No syntax errors') !== false) {
        echo "<p style='color: green;'>✅ No PHP syntax errors</p>";
    } else {
        echo "<p style='color: red;'>❌ PHP syntax errors found:</p>";
        echo "<pre>$output</pre>";
    }
} else {
    echo "<p style='color: red;'>❌ addArtwork.php not found</p>";
}

// Test 5: Check for missing functions
echo "<h2>Test 5: Function Availability Check</h2>";

if (function_exists('uploadAuctionImage')) {
    echo "<p style='color: green;'>✅ uploadAuctionImage function available</p>";
} else {
    echo "<p style='color: red;'>❌ uploadAuctionImage function missing</p>";
}

// Test 6: Simulate file upload structure
echo "<h2>Test 6: File Upload Structure Test</h2>";

// Simulate the $_FILES structure that would come from the frontend
$_FILES = [
    'primary_image' => [
        'name' => 'test_primary.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => '/tmp/test_primary.jpg',
        'error' => UPLOAD_ERR_NO_FILE, // Simulate no file uploaded
        'size' => 0
    ],
    'artwork_images' => [
        'name' => ['test_additional1.jpg', 'test_additional2.jpg'],
        'type' => ['image/jpeg', 'image/jpeg'], 
        'tmp_name' => ['/tmp/test1.jpg', '/tmp/test2.jpg'],
        'error' => [UPLOAD_ERR_NO_FILE, UPLOAD_ERR_NO_FILE], // Simulate no files uploaded
        'size' => [0, 0]
    ]
];

echo "<h3>Simulated \$_FILES Structure:</h3>";
echo "<pre>" . print_r($_FILES, true) . "</pre>";

echo "<p><strong>File structure simulation complete.</strong> In a real test, you would upload actual image files.</p>";

echo "<h2>🎯 Test Summary</h2>";
echo "<p>This test validates the backend structure for artwork primary image functionality. ";
echo "To fully test, you would need to upload actual image files through the frontend form.</p>";

$conn->close();
?>
