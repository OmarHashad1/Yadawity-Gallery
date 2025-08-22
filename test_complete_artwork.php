<!DOCTYPE html>
<html>
<head>
    <title>Test Complete Artwork Submission</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <h1>Testing Complete Artwork Submission</h1>
    <div id="results"></div>
    
    <script>
        async function testCompleteSubmission() {
            const results = document.getElementById('results');
            results.innerHTML = '<p>Testing complete artwork submission...</p>';
            
            // Create form data exactly like the artwork form
            const formData = new FormData();
            formData.append('title', 'Beautiful Sunset Painting');
            formData.append('price', '2500.00');
            formData.append('category', 'painting');
            formData.append('description', 'A beautiful sunset painting created with acrylic paints on canvas. This artwork captures the serene beauty of nature.');
            formData.append('style', 'impressionism');
            formData.append('material', 'Acrylic on canvas');
            formData.append('width', '60');
            formData.append('height', '80');
            formData.append('year', '2024');
            
            try {
                console.log('Submitting artwork data...');
                
                const response = await fetch('/API/addArtwork.php', {
                    method: 'POST',
                    credentials: 'include',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                console.log('Response status:', response.status);
                
                const responseText = await response.text();
                console.log('Raw response:', responseText);
                
                let data;
                try {
                    data = JSON.parse(responseText);
                    console.log('Parsed data:', data);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    throw new Error('Invalid JSON response: ' + responseText);
                }
                
                results.innerHTML = `
                    <h3>Test Results:</h3>
                    <p><strong>Status:</strong> ${response.status}</p>
                    <p><strong>Success:</strong> ${data.success ? '✅ Yes' : '❌ No'}</p>
                    <p><strong>Message:</strong> ${data.message || 'No message'}</p>
                    ${data.errors ? '<p><strong>Errors:</strong><br>' + data.errors.map(e => `• ${e}`).join('<br>') + '</p>' : ''}
                    ${data.data ? '<p><strong>Artwork ID:</strong> ' + data.data.artwork_id + '</p>' : ''}
                    <h4>Full Response:</h4>
                    <pre style="background: #f5f5f5; padding: 10px; border-radius: 5px;">${JSON.stringify(data, null, 2)}</pre>
                `;
                
                if (data.success) {
                    // Test completed successfully
                    console.log('✅ Artwork submission test PASSED');
                } else {
                    console.log('❌ Artwork submission test FAILED:', data.message);
                }
                
            } catch (error) {
                results.innerHTML = `
                    <h3>❌ Error During Test:</h3>
                    <p style="color: red; font-weight: bold;">${error.message}</p>
                    <p>Check the browser console for more details.</p>
                `;
                console.error('Test error:', error);
            }
        }
        
        // Run test when page loads
        window.addEventListener('load', () => {
            // Wait a moment for session to be established
            setTimeout(testCompleteSubmission, 1000);
        });
    </script>
</body>
</html>
