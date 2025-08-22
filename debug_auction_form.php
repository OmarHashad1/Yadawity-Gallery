<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Auction Form</title>
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
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
        }
        .debug-output {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border: 1px solid #dee2e6;
            font-family: monospace;
            white-space: pre-wrap;
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
        .btn-secondary { background: #6c757d; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🐛 Auction Form Debug Tool</h1>
        
        <div class="step-info">
            <h3>Debugging Auction Form Navigation</h3>
            <p>This tool will help identify why the auction form isn't showing the publish button and preview on step 3.</p>
        </div>

        <div style="margin: 20px 0;">
            <h4>Test Controls:</h4>
            <button onclick="debugCurrentState()" class="btn btn-primary">Debug Current State</button>
            <button onclick="testStepNavigation()" class="btn btn-secondary">Test Navigation</button>
            <button onclick="openAuctionForm()" class="btn btn-success">Open Auction Form</button>
        </div>

        <div id="debugOutput" class="debug-output">
            Click "Debug Current State" to start debugging...
        </div>
    </div>

    <script>
        function debugCurrentState() {
            let output = "=== AUCTION FORM DEBUG REPORT ===\n\n";
            
            // Check if we're in an iframe or parent window
            let targetDocument = document;
            let targetWindow = window;
            
            // Try to access parent if in iframe
            try {
                if (window.parent && window.parent.document) {
                    targetDocument = window.parent.document;
                    targetWindow = window.parent;
                    output += "✓ Found parent document\n";
                } else {
                    output += "ℹ Using current document\n";
                }
            } catch (e) {
                output += "ℹ Cannot access parent: " + e.message + "\n";
            }
            
            output += "\n--- CHECKING AUCTION FORM ELEMENTS ---\n";
            
            // Check auction form existence
            const auctionForm = targetDocument.getElementById('addAuctionForm');
            output += "Auction Form: " + (auctionForm ? "✓ Found" : "❌ Not found") + "\n";
            
            // Check steps
            const steps = targetDocument.querySelectorAll('#addAuctionForm .formStep');
            output += "Form Steps: " + steps.length + " found\n";
            
            steps.forEach((step, index) => {
                const stepNum = step.getAttribute('data-step');
                const isActive = step.classList.contains('active');
                output += `  Step ${stepNum}: ${isActive ? 'ACTIVE' : 'inactive'}\n`;
            });
            
            // Check buttons
            const nextBtn = targetDocument.getElementById('auctionNextStep');
            const prevBtn = targetDocument.getElementById('auctionPrevStep');
            const launchBtn = targetDocument.getElementById('launchAuctionBtn');
            const stepDisplay = targetDocument.getElementById('auctionCurrentStepNum');
            
            output += "\n--- BUTTON STATUS ---\n";
            output += "Next Button: " + (nextBtn ? "✓ Found" : "❌ Not found");
            if (nextBtn) output += " | Display: " + nextBtn.style.display + " | Visible: " + (nextBtn.offsetParent !== null);
            output += "\n";
            
            output += "Previous Button: " + (prevBtn ? "✓ Found" : "❌ Not found");
            if (prevBtn) output += " | Display: " + prevBtn.style.display + " | Visible: " + (prevBtn.offsetParent !== null);
            output += "\n";
            
            output += "Launch Button: " + (launchBtn ? "✓ Found" : "❌ Not found");
            if (launchBtn) output += " | Display: " + launchBtn.style.display + " | Visible: " + (launchBtn.offsetParent !== null);
            output += "\n";
            
            output += "Step Display: " + (stepDisplay ? "✓ Found" : "❌ Not found");
            if (stepDisplay) output += " | Text: '" + stepDisplay.textContent + "'";
            output += "\n";
            
            // Check preview container
            const previewContainer = targetDocument.getElementById('auctionPreview');
            output += "\n--- PREVIEW CONTAINER ---\n";
            output += "Preview Container: " + (previewContainer ? "✓ Found" : "❌ Not found");
            if (previewContainer) {
                output += " | HTML Length: " + previewContainer.innerHTML.length;
                if (previewContainer.innerHTML.length > 0) {
                    output += " | Has Content: ✓";
                } else {
                    output += " | Has Content: ❌ Empty";
                }
            }
            output += "\n";
            
            // Check if functions exist
            output += "\n--- JAVASCRIPT FUNCTIONS ---\n";
            const functions = ['getCurrentAuctionStep', 'showAuctionStep', 'updateAuctionStepNavigation', 'generateAuctionPreview'];
            functions.forEach(funcName => {
                const exists = typeof targetWindow[funcName] === 'function';
                output += funcName + ": " + (exists ? "✓ Found" : "❌ Not found") + "\n";
            });
            
            // Try to get current step
            if (typeof targetWindow.getCurrentAuctionStep === 'function') {
                try {
                    const currentStep = targetWindow.getCurrentAuctionStep();
                    output += "\n--- CURRENT STATE ---\n";
                    output += "Current Step: " + currentStep + "\n";
                } catch (e) {
                    output += "\n--- ERROR ---\n";
                    output += "Error getting current step: " + e.message + "\n";
                }
            }
            
            // Check if auction section is active
            const auctionSection = targetDocument.getElementById('auction-section');
            output += "\nAuction Section: " + (auctionSection ? "✓ Found" : "❌ Not found");
            if (auctionSection) {
                output += " | Active: " + (auctionSection.classList.contains('active') ? "✓" : "❌");
            }
            output += "\n";
            
            document.getElementById('debugOutput').textContent = output;
        }
        
        function testStepNavigation() {
            let output = "=== TESTING STEP NAVIGATION ===\n\n";
            
            // Try to access parent window functions
            let targetWindow = window;
            try {
                if (window.parent && window.parent.document) {
                    targetWindow = window.parent;
                }
            } catch (e) {
                output += "Cannot access parent: " + e.message + "\n";
            }
            
            if (typeof targetWindow.showAuctionStep === 'function' && 
                typeof targetWindow.updateAuctionStepNavigation === 'function') {
                
                output += "Testing step navigation...\n";
                
                // Test each step
                for (let step = 1; step <= 3; step++) {
                    try {
                        output += `\nTesting Step ${step}:\n`;
                        targetWindow.showAuctionStep(step);
                        targetWindow.updateAuctionStepNavigation();
                        
                        // Check button states
                        const nextBtn = targetWindow.document.getElementById('auctionNextStep');
                        const launchBtn = targetWindow.document.getElementById('launchAuctionBtn');
                        
                        if (nextBtn && launchBtn) {
                            output += `  Next Button Display: ${nextBtn.style.display}\n`;
                            output += `  Launch Button Display: ${launchBtn.style.display}\n`;
                            
                            if (step === 3) {
                                if (nextBtn.style.display === 'none' && launchBtn.style.display !== 'none') {
                                    output += "  ✓ Step 3 navigation working correctly\n";
                                } else {
                                    output += "  ❌ Step 3 navigation not working - Next should be hidden, Launch should be visible\n";
                                }
                            }
                        }
                    } catch (e) {
                        output += `  ❌ Error testing step ${step}: ${e.message}\n`;
                    }
                }
            } else {
                output += "❌ Navigation functions not found\n";
            }
            
            document.getElementById('debugOutput').textContent = output;
        }
        
        function openAuctionForm() {
            // Try to open the auction form in parent window
            try {
                if (window.parent && window.parent.document) {
                    // Switch to auction section
                    if (typeof window.parent.switchSection === 'function') {
                        window.parent.switchSection('auction');
                        document.getElementById('debugOutput').textContent = "✓ Switched to auction section in parent window. You can now test the form.";
                    } else {
                        document.getElementById('debugOutput').textContent = "❌ switchSection function not found in parent window.";
                    }
                } else {
                    // Open in new tab
                    window.open('/artistPortal.php#auction', '_blank');
                    document.getElementById('debugOutput').textContent = "✓ Opened auction form in new tab.";
                }
            } catch (e) {
                document.getElementById('debugOutput').textContent = "❌ Error: " + e.message;
            }
        }
    </script>
</body>
</html>
