<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auction Step 3 Test</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: #f5f5f5;
        }
        .test-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin: 5px;
        }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .code-block {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            font-family: monospace;
            border: 1px solid #dee2e6;
        }
        .output {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🎯 Auction Step 3 Fix Test</h1>
        
        <p>This page will help us test and fix the auction form step 3 issue directly.</p>
        
        <h3>Quick Tests:</h3>
        <button onclick="openArtistPortal()" class="btn btn-primary">Open Artist Portal</button>
        <button onclick="testInConsole()" class="btn btn-success">Run Test in Console</button>
        
        <div class="code-block">
            <strong>Manual Console Commands:</strong><br>
            Open Artist Portal → Go to Auction Section → Open Browser Console (F12) → Run:<br><br>
            <code>debugAuctionStep3()</code> - Test step 3 directly<br>
            <code>showAuctionStep(3); updateAuctionStepNavigation()</code> - Force step 3<br>
            <code>generateAuctionPreview()</code> - Generate preview manually
        </div>
        
        <div class="output" id="output">
            Click "Run Test in Console" to see results here.
        </div>
        
        <h3>Expected Behavior on Step 3:</h3>
        <ul>
            <li>✅ Next button should be hidden (<code>style.display = 'none'</code>)</li>
            <li>✅ Publish Auction button should be visible (<code>style.display = 'inline-flex'</code>)</li>
            <li>✅ Preview content should be generated and displayed</li>
            <li>✅ Step indicator should show "3"</li>
        </ul>
        
        <h3>Quick Fix Commands:</h3>
        <div class="code-block">
            If the issue persists after our code changes, try these in the browser console:<br><br>
            <strong>Force Step 3 Navigation:</strong><br>
            <code>
            document.getElementById('auctionNextStep').style.display = 'none';<br>
            document.getElementById('launchAuctionBtn').style.display = 'inline-flex';<br>
            generateAuctionPreview();
            </code>
        </div>
    </div>

    <script>
        function openArtistPortal() {
            window.open('/artistPortal.php', '_blank');
            document.getElementById('output').innerHTML = 
                '✅ Opened Artist Portal in new tab.<br>' +
                '📋 Next steps:<br>' +
                '1. Go to Auction section<br>' +
                '2. Fill out some form fields<br>' +
                '3. Navigate to step 3<br>' +
                '4. Check if Publish button appears and preview is generated';
        }
        
        function testInConsole() {
            const output = document.getElementById('output');
            output.innerHTML = `
                <strong>🔧 Console Test Instructions:</strong><br><br>
                1. Open Artist Portal in new tab: <a href="/artistPortal.php" target="_blank">Open Now</a><br>
                2. Go to Auction Management section<br>
                3. Open Browser Console (F12 or Ctrl+Shift+I)<br>
                4. Run this command: <code>debugAuctionStep3()</code><br>
                5. Check the console output for debugging info<br><br>
                
                <strong>Alternative Test:</strong><br>
                Run: <code>showAuctionStep(3); updateAuctionStepNavigation(); generateAuctionPreview();</code><br><br>
                
                <strong>Check Results:</strong><br>
                • Look for "Publish Auction" button<br>
                • Verify preview content appears<br>
                • Check console for any errors
            `;
        }
    </script>
</body>
</html>
