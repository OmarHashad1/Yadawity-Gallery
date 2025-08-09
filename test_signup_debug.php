<?php
// Check current PHP error reporting settings
echo "<h3>Current PHP Error Reporting Settings:</h3>";
echo "display_errors: " . (ini_get('display_errors') ? 'ON' : 'OFF') . "<br>";
echo "error_reporting level: " . error_reporting() . "<br>";
echo "log_errors: " . (ini_get('log_errors') ? 'ON' : 'OFF') . "<br>";

// Test if there are any PHP notices/warnings that might show file paths
echo "<h3>Current file path (for reference):</h3>";
echo __FILE__ . "<br>";

// Check if signup.php has any syntax errors
echo "<h3>Checking signup.php for issues:</h3>";
$output = [];
$returnVar = 0;
exec('php -l "' . __DIR__ . '/API/signup.php"', $output, $returnVar);
if ($returnVar === 0) {
    echo "✅ signup.php syntax is valid<br>";
} else {
    echo "❌ signup.php has syntax errors:<br>";
    foreach ($output as $line) {
        echo htmlspecialchars($line) . "<br>";
    }
}
?>
