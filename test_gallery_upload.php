<?php
// Test script to verify gallery upload directory
echo "<h1>Gallery Upload Directory Test</h1>";

$uploadDir = dirname(__DIR__) . '/uploads/galleries/';
echo "<h2>Upload Directory Path:</h2>";
echo "<p><strong>Full Path:</strong> $uploadDir</p>";

echo "<h2>Directory Status:</h2>";
echo "<ul>";
echo "<li><strong>Exists:</strong> " . (is_dir($uploadDir) ? 'Yes' : 'No') . "</li>";
echo "<li><strong>Writable:</strong> " . (is_writable($uploadDir) ? 'Yes' : 'No') . "</li>";
echo "<li><strong>Readable:</strong> " . (is_readable($uploadDir) ? 'Yes' : 'No') . "</li>";

if (is_dir($uploadDir)) {
    $permissions = substr(sprintf('%o', fileperms($uploadDir)), -4);
    echo "<li><strong>Permissions:</strong> $permissions</li>";
}
echo "</ul>";

echo "<h2>Files in Gallery Directory:</h2>";
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    if ($files) {
        echo "<ul>";
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $uploadDir . $file;
                $fileSize = filesize($filePath);
                $fileTime = date('Y-m-d H:i:s', filemtime($filePath));
                echo "<li><strong>$file</strong> - Size: " . number_format($fileSize) . " bytes - Modified: $fileTime</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p>No files found or unable to read directory.</p>";
    }
} else {
    echo "<p>Directory does not exist.</p>";
}

echo "<h2>Test Upload Simulation:</h2>";
echo "<p>Relative path that would be stored in database: <code>uploads/galleries/[filename]</code></p>";
echo "<p>Example: <code>uploads/galleries/gallery_" . uniqid() . "_" . time() . ".jpg</code></p>";
?>
