<?php
// Test file upload permissions
$uploadDir = './uploads/artworks/';

// Check if directory is writable
if (is_writable($uploadDir)) {
    echo "✅ Upload directory is writable\n";
} else {
    echo "❌ Upload directory is NOT writable\n";
}

// Test creating a file
$testFile = $uploadDir . 'test_' . time() . '.txt';
$result = file_put_contents($testFile, 'Test content');

if ($result !== false) {
    echo "✅ File creation test successful\n";
    // Clean up test file
    unlink($testFile);
} else {
    echo "❌ File creation test failed\n";
}

// Check directory permissions
$perms = fileperms($uploadDir);
echo "Directory permissions: " . substr(sprintf('%o', $perms), -4) . "\n";
?>
