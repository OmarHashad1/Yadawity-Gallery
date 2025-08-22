<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Auction Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .test-section {
            background: #f5f5f5;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <h1>Auction Form Integration Test</h1>
    
    <?php
    session_start();
    
    // Simulate logged in user for testing
    $_SESSION['user_id'] = 1; // Use a test user ID
    
    echo "<div class='test-section info'>";
    echo "<h3>Test Configuration</h3>";
    echo "<p><strong>User ID:</strong> " . $_SESSION['user_id'] . "</p>";
    echo "<p><strong>Session Status:</strong> " . (isset($_SESSION['user_id']) ? 'Logged In' : 'Not Logged In') . "</p>";
    echo "</div>";
    
    // Test database connection
    try {
        require_once 'API/db.php';
        echo "<div class='test-section success'>";
        echo "<h3>✓ Database Connection</h3>";
        echo "<p>Successfully connected to database</p>";
        echo "</div>";
        
        // Check required tables
        $tables = ['artworks', 'auctions', 'artwork_photos'];
        echo "<div class='test-section'>";
        echo "<h3>Database Tables Check</h3>";
        
        foreach ($tables as $table) {
            $result = $db->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "<p>✓ Table '$table' exists</p>";
            } else {
                echo "<p style='color: red;'>✗ Table '$table' missing</p>";
            }
        }
        echo "</div>";
        
        // Check artworks table structure
        echo "<div class='test-section'>";
        echo "<h3>Artworks Table Structure</h3>";
        $result = $db->query("DESCRIBE artworks");
        if ($result) {
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                echo "<li><strong>" . $row['Field'] . "</strong>: " . $row['Type'] . 
                     ($row['Null'] === 'NO' ? ' (Required)' : ' (Optional)') . "</li>";
            }
            echo "</ul>";
        }
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='test-section error'>";
        echo "<h3>✗ Database Connection Failed</h3>";
        echo "<p>Error: " . $e->getMessage() . "</p>";
        echo "</div>";
    }
    ?>
    
    <div class="test-section">
        <h3>Form Submission Test</h3>
        <p>To test the auction form submission:</p>
        <ol>
            <li>Open the artist portal: <a href="artistPortal.php" target="_blank">Artist Portal</a></li>
            <li>Navigate to the "Add Auction" section</li>
            <li>Fill in all required fields</li>
            <li>Upload a primary image and any additional images</li>
            <li>Submit the form</li>
        </ol>
        <p><strong>Expected behavior:</strong></p>
        <ul>
            <li>Form validation should work in real-time</li>
            <li>Primary image should be displayed in preview</li>
            <li>Auction should be created in both artworks and auctions tables</li>
            <li>Images should be uploaded to /uploads/ directory</li>
            <li>Success message should be displayed</li>
        </ul>
    </div>
    
    <div class="test-section">
        <h3>API Endpoint Test</h3>
        <p>API Endpoint: <code>/API/addAuction.php</code></p>
        <p>The API now expects these form fields:</p>
        <ul>
            <li><strong>title</strong> - Artwork title</li>
            <li><strong>starting_bid</strong> - Starting bid amount</li>
            <li><strong>style</strong> - Art style</li>
            <li><strong>width, height</strong> - Dimensions</li>
            <li><strong>description</strong> - Artwork description</li>
            <li><strong>start_date, end_date</strong> - Auction dates</li>
            <li><strong>primary_image</strong> - Primary image file</li>
            <li><strong>artwork_images[]</strong> - Additional images</li>
        </ul>
    </div>
    
    <div class="test-section">
        <h3>Key Changes Made</h3>
        <ul>
            <li>✅ Auction form now follows artwork form structure</li>
            <li>✅ Primary image functionality implemented</li>
            <li>✅ Dual-table insertion (artworks + auctions)</li>
            <li>✅ API updated to handle new field names</li>
            <li>✅ Preview function updated to show primary image</li>
            <li>✅ Form reset includes primary image clearing</li>
        </ul>
    </div>
    
</body>
</html>
