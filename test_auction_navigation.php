<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Auction Navigation</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: #f5f5f5;
        }
        .test-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .step-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
        }
        .button-state {
            display: flex;
            gap: 15px;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-success { background: #28a745; color: white; }
        .hidden { display: none !important; }
        .visible { display: inline-flex !important; }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🎨 Auction Form Navigation Test</h1>
        
        <div class="step-info">
            <h3>Testing Auction Form Step Navigation</h3>
            <p>This test simulates the auction form button behavior across all 3 steps:</p>
            <ul>
                <li><strong>Steps 1-2:</strong> Previous button visible (if not step 1), Next button visible, Publish button hidden</li>
                <li><strong>Step 3:</strong> Previous button visible, Next button hidden, Publish button visible</li>
            </ul>
        </div>

        <!-- Simulate auction form navigation elements -->
        <div id="auctionCurrentStepNum" style="font-size: 18px; font-weight: bold; margin: 20px 0;">1</div>
        
        <div style="margin: 30px 0;">
            <h4>Current Button States:</h4>
            
            <div class="button-state">
                <button id="auctionPrevStep" class="btn btn-secondary" style="display: none;">
                    ← Previous
                </button>
                <span id="prevBtnState">Hidden</span>
            </div>
            
            <div class="button-state">
                <button id="auctionNextStep" class="btn btn-primary" style="display: inline-flex;">
                    Next →
                </button>
                <span id="nextBtnState">Visible</span>
            </div>
            
            <div class="button-state">
                <button id="launchAuctionBtn" class="btn btn-success" style="display: none;">
                    🚀 Publish Auction
                </button>
                <span id="publishBtnState">Hidden</span>
            </div>
        </div>

        <div style="margin: 30px 0;">
            <h4>Test Controls:</h4>
            <button onclick="testStep(1)" class="btn btn-primary">Test Step 1</button>
            <button onclick="testStep(2)" class="btn btn-primary">Test Step 2</button>
            <button onclick="testStep(3)" class="btn btn-primary">Test Step 3</button>
        </div>

        <div id="testResults" style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px;">
            <h4>Test Results:</h4>
            <div id="resultsList"></div>
        </div>
    </div>

    <script>
        // Simulate the getCurrentAuctionStep function
        let currentAuctionStep = 1;
        
        function getCurrentAuctionStep() {
            return currentAuctionStep;
        }
        
        // Copy the exact updateAuctionStepNavigation function from artist-portal.js
        function updateAuctionStepNavigation() {
            const currentStep = getCurrentAuctionStep();
            const prevBtn = document.getElementById('auctionPrevStep');
            const nextBtn = document.getElementById('auctionNextStep');
            const launchBtn = document.getElementById('launchAuctionBtn');
            const stepNumDisplay = document.getElementById('auctionCurrentStepNum');
            
            // Update step number display
            if (stepNumDisplay) {
                stepNumDisplay.textContent = currentStep;
            }
            
            // Show/hide previous button
            if (prevBtn) {
                prevBtn.style.display = currentStep > 1 ? 'inline-flex' : 'none';
            }
            
            // Show/hide next/launch buttons
            if (nextBtn && launchBtn) {
                if (currentStep === 3) {
                    // On final step, show launch button instead of next
                    nextBtn.style.display = 'none';
                    launchBtn.style.display = 'inline-flex';
                } else {
                    // On other steps, show next button
                    nextBtn.style.display = 'inline-flex';
                    launchBtn.style.display = 'none';
                }
            }
            
            // Update state indicators
            updateStateIndicators();
        }
        
        function updateStateIndicators() {
            const prevBtn = document.getElementById('auctionPrevStep');
            const nextBtn = document.getElementById('auctionNextStep');
            const launchBtn = document.getElementById('launchAuctionBtn');
            
            document.getElementById('prevBtnState').textContent = 
                prevBtn.style.display === 'none' ? 'Hidden' : 'Visible';
            document.getElementById('nextBtnState').textContent = 
                nextBtn.style.display === 'none' ? 'Hidden' : 'Visible';
            document.getElementById('publishBtnState').textContent = 
                launchBtn.style.display === 'none' ? 'Hidden' : 'Visible';
        }
        
        function testStep(step) {
            currentAuctionStep = step;
            updateAuctionStepNavigation();
            
            // Log test result
            const resultsList = document.getElementById('resultsList');
            const result = document.createElement('div');
            const currentStep = getCurrentAuctionStep();
            const prevBtn = document.getElementById('auctionPrevStep');
            const nextBtn = document.getElementById('auctionNextStep');
            const launchBtn = document.getElementById('launchAuctionBtn');
            
            const prevVisible = prevBtn.style.display !== 'none';
            const nextVisible = nextBtn.style.display !== 'none';
            const publishVisible = launchBtn.style.display !== 'none';
            
            let status = '✅ PASS';
            let details = [];
            
            // Validate expected behavior
            if (step === 1) {
                if (prevVisible) { status = '❌ FAIL'; details.push('Previous should be hidden on step 1'); }
                if (!nextVisible) { status = '❌ FAIL'; details.push('Next should be visible on step 1'); }
                if (publishVisible) { status = '❌ FAIL'; details.push('Publish should be hidden on step 1'); }
            } else if (step === 2) {
                if (!prevVisible) { status = '❌ FAIL'; details.push('Previous should be visible on step 2'); }
                if (!nextVisible) { status = '❌ FAIL'; details.push('Next should be visible on step 2'); }
                if (publishVisible) { status = '❌ FAIL'; details.push('Publish should be hidden on step 2'); }
            } else if (step === 3) {
                if (!prevVisible) { status = '❌ FAIL'; details.push('Previous should be visible on step 3'); }
                if (nextVisible) { status = '❌ FAIL'; details.push('Next should be hidden on step 3'); }
                if (!publishVisible) { status = '❌ FAIL'; details.push('Publish should be visible on step 3'); }
            }
            
            result.innerHTML = `
                <div style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <strong>Step ${step} Test:</strong> ${status}<br>
                    <small>Previous: ${prevVisible ? 'Visible' : 'Hidden'} | 
                    Next: ${nextVisible ? 'Visible' : 'Hidden'} | 
                    Publish: ${publishVisible ? 'Visible' : 'Hidden'}</small>
                    ${details.length > 0 ? '<br><span style="color: red;">Issues: ' + details.join(', ') + '</span>' : ''}
                </div>
            `;
            
            resultsList.appendChild(result);
        }
        
        // Initialize the navigation
        updateAuctionStepNavigation();
        
        // Run all tests automatically
        setTimeout(() => {
            console.log('Running automated tests...');
            testStep(1);
            testStep(2);
            testStep(3);
        }, 500);
    </script>
</body>
</html>
