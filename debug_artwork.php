<!DOCTYPE html>
<html>
<head>
    <title>Artwork Form Debug</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .debug-section { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .error { background: #ffebee; color: #c62828; }
        .success { background: #e8f5e9; color: #2e7d32; }
    </style>
</head>
<body>
    <h1>Artwork Form Debug Test</h1>
    
    <div class="debug-section">
        <h3>JavaScript Console Check</h3>
        <p>Open browser console (F12) and check for any JavaScript errors when loading the artist portal.</p>
    </div>
    
    <div class="debug-section">
        <h3>Common Issues to Check:</h3>
        <ul>
            <li><strong>Form Elements:</strong> Make sure all required form elements exist (artworkName, artworkPrice, etc.)</li>
            <li><strong>File Upload:</strong> Check if primary image upload is working</li>
            <li><strong>Validation:</strong> Verify real-time validation indicators appear</li>
            <li><strong>Step Navigation:</strong> Test if Next/Previous buttons work</li>
            <li><strong>API Connection:</strong> Check if addArtwork.php API is accessible</li>
        </ul>
    </div>
    
    <div class="debug-section">
        <h3>Quick Tests:</h3>
        <ol>
            <li>Open <a href="artistPortal.php" target="_blank">Artist Portal</a></li>
            <li>Go to "Add Artwork" section</li>
            <li>Fill in artwork name - check if validation indicator appears</li>
            <li>Try to click "Next" without filling required fields</li>
            <li>Upload a primary image - check if preview appears</li>
            <li>Complete all steps and try to publish</li>
        </ol>
    </div>
    
    <div class="debug-section">
        <h3>API Test:</h3>
        <p>Test API endpoint: <a href="API/addArtwork.php" target="_blank">API/addArtwork.php</a></p>
        <p>Should return JSON error about missing data when accessed directly</p>
    </div>
    
    <script>
        // Basic JavaScript test
        console.log('Debug script loaded');
        
        // Test if main functions exist
        setTimeout(() => {
            const errors = [];
            
            // Check if essential functions exist
            if (typeof publishArtwork === 'undefined') {
                errors.push('publishArtwork function not found');
            }
            
            if (typeof validateCurrentStep === 'undefined') {
                errors.push('validateCurrentStep function not found');
            }
            
            // Check if essential elements exist
            if (!document.getElementById('artworkName')) {
                errors.push('artworkName input not found');
            }
            
            if (!document.getElementById('publishBtn')) {
                errors.push('publishBtn button not found');
            }
            
            if (errors.length > 0) {
                console.error('Debug Issues Found:', errors);
                alert('Issues found: ' + errors.join(', '));
            } else {
                console.log('Basic checks passed');
            }
        }, 2000);
    </script>
</body>
</html>
