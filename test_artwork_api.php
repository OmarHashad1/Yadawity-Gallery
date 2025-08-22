<!DOCTYPE html>
<html>
<head>
    <title>Artwork Form Test</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <h1>Testing Artwork Form Submission</h1>
    <div id="results"></div>
    
    <script>
        // Test artwork form submission with sample data
        async function testArtworkSubmission() {
            const results = document.getElementById('results');
            results.innerHTML = '<p>Testing artwork form submission...</p>';
            
            // Create form data
            const formData = new FormData();
            formData.append('title', 'Test Artwork');
            formData.append('price', '1500');
            formData.append('category', 'painting');
            formData.append('description', 'This is a test artwork description for validation purposes.');
            formData.append('style', 'abstract');
            formData.append('width', '50');
            formData.append('height', '70');
            formData.append('year', '2024');
            formData.append('material', 'Acrylic on canvas');
            
            try {
                const response = await fetch('/API/addArtwork.php', {
                    method: 'POST',
                    credentials: 'include',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                const responseText = await response.text();
                console.log('Raw response:', responseText);
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    throw new Error('Invalid JSON response: ' + responseText);
                }
                
                results.innerHTML = `
                    <h3>Test Results:</h3>
                    <p><strong>Status:</strong> ${response.status}</p>
                    <p><strong>Success:</strong> ${data.success ? 'Yes' : 'No'}</p>
                    <p><strong>Message:</strong> ${data.message || 'No message'}</p>
                    ${data.errors ? '<p><strong>Errors:</strong> ' + JSON.stringify(data.errors) + '</p>' : ''}
                    <h4>Full Response:</h4>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
                
            } catch (error) {
                results.innerHTML = `
                    <h3>Error During Test:</h3>
                    <p style="color: red;">${error.message}</p>
                `;
                console.error('Test error:', error);
            }
        }
        
        // Run test when page loads
        window.addEventListener('load', testArtworkSubmission);
    </script>
</body>
</html>
