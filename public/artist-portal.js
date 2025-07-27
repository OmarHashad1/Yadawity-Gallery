// ==========================================
// ARTIST PORTAL JAVASCRIPT
// ==========================================

// Global Variables
let currentSection = 'dashboard';
let currentStep = 1;
let totalSteps = 4;

// ==========================================
// MOBILE MENU FUNCTIONALITY
// ==========================================

function initializeMobileMenu() {
    const mobileMenuIcon = document.getElementById('mobileMenuIcon');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('artistSidebar');
    
    // Mobile menu icon click handler
    if (mobileMenuIcon) {
        mobileMenuIcon.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }
    
    // Sidebar toggle button click handler
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 1024) {
            const sidebar = document.getElementById('artistSidebar');
            const mobileMenuIcon = document.getElementById('mobileMenuIcon');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            if (sidebar && sidebar.classList.contains('active')) {
                if (!sidebar.contains(e.target) && 
                    !mobileMenuIcon.contains(e.target) && 
                    (!sidebarToggle || !sidebarToggle.contains(e.target))) {
                    closeSidebar();
                }
            }
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        const sidebar = document.getElementById('artistSidebar');
        if (window.innerWidth > 1024 && sidebar) {
            sidebar.classList.remove('active');
        }
    });
}

function toggleSidebar() {
    const sidebar = document.getElementById('artistSidebar');
    const mobileMenuIcon = document.getElementById('mobileMenuIcon');
    
    if (sidebar) {
        sidebar.classList.toggle('active');
        
        // Update menu icon
        if (mobileMenuIcon) {
            mobileMenuIcon.classList.toggle('active');
            const icon = mobileMenuIcon.querySelector('i');
            if (icon) {
                if (sidebar.classList.contains('active')) {
                    icon.className = 'fas fa-times';
                } else {
                    icon.className = 'fas fa-bars';
                }
            }
        }
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('artistSidebar');
    const mobileMenuIcon = document.getElementById('mobileMenuIcon');
    
    if (sidebar) {
        sidebar.classList.remove('active');
        
        // Reset menu icon
        if (mobileMenuIcon) {
            mobileMenuIcon.classList.remove('active');
            const icon = mobileMenuIcon.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-bars';
            }
        }
    }
}

// ==========================================
// SIDEBAR NAVIGATION
// ==========================================

function initializeSidebarNavigation() {
    const sidebarLinks = document.querySelectorAll('.sidebarLink');
    
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetSection = this.getAttribute('data-section');
            if (targetSection) {
                switchSection(targetSection);
                
                // Close sidebar on mobile after navigation
                if (window.innerWidth <= 1024) {
                    closeSidebar();
                }
            }
        });
    });
}

function switchSection(sectionName) {
    // Hide all sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => {
        section.classList.remove('active');
    });
    
    // Show target section
    const targetSection = document.getElementById(`${sectionName}-section`);
    if (targetSection) {
        targetSection.classList.add('active');
    }
    
    // Update sidebar active state
    const sidebarLinks = document.querySelectorAll('.sidebarLink');
    sidebarLinks.forEach(link => {
        link.classList.remove('active');
    });
    
    const activeLink = document.querySelector(`[data-section="${sectionName}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
    
    currentSection = sectionName;
}

// ==========================================
// DASHBOARD FUNCTIONALITY
// ==========================================

function initializeDashboard() {
    // Refresh button
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
            
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
                showNotification('Dashboard refreshed successfully!', 'success');
            }, 1500);
        });
    }
    
    // Initialize charts if Chart.js is available
    if (typeof Chart !== 'undefined') {
        initializeSalesChart();
    }
}

function initializeSalesChart() {
    const chartCanvas = document.getElementById('salesChart');
    if (!chartCanvas) return;
    
    const ctx = chartCanvas.getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [{
                label: 'Sales (EGP)',
                data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
                borderColor: '#6B4423',
                backgroundColor: 'rgba(107, 68, 35, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'EGP ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

// ==========================================
// ORDERS FUNCTIONALITY
// ==========================================

function initializeOrders() {
    // Order status filter
    const statusFilter = document.getElementById('orderStatusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            filterOrders(this.value);
        });
    }
    
    // Order search
    const orderSearch = document.getElementById('orderSearch');
    if (orderSearch) {
        orderSearch.addEventListener('input', function() {
            searchOrders(this.value);
        });
    }
    
    // Export button
    const exportBtn = document.getElementById('exportOrdersBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            exportOrders();
        });
    }
}

function filterOrders(status) {
    const rows = document.querySelectorAll('.dataTable tbody tr');
    
    rows.forEach(row => {
        if (!status) {
            row.style.display = '';
        } else {
            const statusBadge = row.querySelector('.statusBadge');
            if (statusBadge && statusBadge.textContent.toLowerCase().includes(status)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}

function searchOrders(searchTerm) {
    const rows = document.querySelectorAll('.dataTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm.toLowerCase())) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function exportOrders() {
    showNotification('Orders exported successfully!', 'success');
}

function viewOrder(orderId) {
    showNotification(`Viewing order ${orderId}`, 'info');
}

function trackOrder(orderId) {
    showNotification(`Tracking order ${orderId}`, 'info');
}

// ==========================================
// PROFILE FUNCTIONALITY
// ==========================================

function initializeProfile() {
    // Character counters
    const bioTextarea = document.getElementById('artistBio');
    const bioCounter = document.getElementById('bioCharCount');
    
    if (bioTextarea && bioCounter) {
        bioTextarea.addEventListener('input', function() {
            bioCounter.textContent = this.value.length;
        });
        
        // Initialize counter
        bioCounter.textContent = bioTextarea.value.length;
    }
    
    // Achievement management
    initializeAchievements();
    
    // Password functionality
    initializePasswordFields();
    
    // Form submissions
    const artistInfoForm = document.getElementById('artistInfoForm');
    if (artistInfoForm) {
        artistInfoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveArtistInfo();
        });
    }
    
    const securityForm = document.getElementById('securityForm');
    if (securityForm) {
        securityForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveSecuritySettings();
        });
    }
}

function initializeAchievements() {
    const addBtn = document.getElementById('addAchievementBtn');
    const newAchievementInput = document.getElementById('newAchievement');
    
    if (addBtn && newAchievementInput) {
        addBtn.addEventListener('click', function() {
            addAchievement();
        });
        
        newAchievementInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addAchievement();
            }
        });
    }
    
    // Remove achievement buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('removeAchievement') || e.target.closest('.removeAchievement')) {
            e.preventDefault();
            const achievementItem = e.target.closest('.achievementItem');
            if (achievementItem) {
                achievementItem.remove();
            }
        }
    });
}

function addAchievement() {
    const input = document.getElementById('newAchievement');
    const list = document.getElementById('achievementsList');
    
    if (input && list && input.value.trim()) {
        const achievementItem = document.createElement('div');
        achievementItem.className = 'achievementItem';
        achievementItem.innerHTML = `
            <span>${input.value.trim()}</span>
            <button type="button" class="removeAchievement"><i class="fas fa-times"></i></button>
        `;
        
        list.appendChild(achievementItem);
        input.value = '';
    }
}

function initializePasswordFields() {
    const newPasswordField = document.getElementById('newPassword');
    const confirmPasswordField = document.getElementById('confirmPassword');
    
    if (newPasswordField) {
        newPasswordField.addEventListener('input', function() {
            updatePasswordStrength(this.value);
            checkPasswordMatch();
        });
    }
    
    if (confirmPasswordField) {
        confirmPasswordField.addEventListener('input', checkPasswordMatch);
    }
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

function updatePasswordStrength(password) {
    const strengthBar = document.querySelector('.strengthFill');
    const strengthText = document.querySelector('.strengthText');
    
    if (!strengthBar || !strengthText) return;
    
    let strength = 0;
    let text = 'Very Weak';
    let color = '#ff4444';
    
    if (password.length >= 8) strength += 20;
    if (password.match(/[a-z]/)) strength += 20;
    if (password.match(/[A-Z]/)) strength += 20;
    if (password.match(/[0-9]/)) strength += 20;
    if (password.match(/[^a-zA-Z0-9]/)) strength += 20;
    
    if (strength >= 80) {
        text = 'Very Strong';
        color = '#22c55e';
    } else if (strength >= 60) {
        text = 'Strong';
        color = '#84cc16';
    } else if (strength >= 40) {
        text = 'Medium';
        color = '#f59e0b';
    } else if (strength >= 20) {
        text = 'Weak';
        color = '#f97316';
    }
    
    strengthBar.style.width = strength + '%';
    strengthBar.style.backgroundColor = color;
    strengthText.textContent = text;
}

function checkPasswordMatch() {
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    const matchIndicator = document.getElementById('matchIndicator');
    
    if (!newPassword || !confirmPassword || !matchIndicator) return;
    
    if (confirmPassword.value === '') {
        matchIndicator.textContent = '';
        return;
    }
    
    if (newPassword.value === confirmPassword.value) {
        matchIndicator.textContent = '✓ Passwords match';
        matchIndicator.style.color = '#22c55e';
    } else {
        matchIndicator.textContent = '✗ Passwords do not match';
        matchIndicator.style.color = '#ef4444';
    }
}

function saveArtistInfo() {
    showNotification('Profile updated successfully!', 'success');
}

function saveSecuritySettings() {
    showNotification('Security settings updated successfully!', 'success');
}

// ==========================================
// ARTWORK FORM FUNCTIONALITY
// ==========================================

function initializeArtworkForm() {
    const nextBtn = document.getElementById('nextStep');
    const prevBtn = document.getElementById('prevStep');
    const publishBtn = document.getElementById('publishBtn');
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (validateCurrentStep()) {
                nextStep();
            }
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            previousStep();
        });
    }
    
    if (publishBtn) {
        publishBtn.addEventListener('click', function(e) {
            e.preventDefault();
            publishArtwork();
        });
    }
    
    // Price calculation
    const priceInput = document.getElementById('artworkPrice');
    if (priceInput) {
        priceInput.addEventListener('input', function() {
            calculateNetPrice(this.value);
        });
    }
    
    // Description character counter
    const descInput = document.getElementById('artworkDescription');
    const descCounter = document.getElementById('descCharCount');
    
    if (descInput && descCounter) {
        descInput.addEventListener('input', function() {
            descCounter.textContent = this.value.length;
        });
    }
    
    // Tags functionality
    initializeTags();
    
    // Image upload
    initializeImageUpload();
    
    // Form submission
    const artworkForm = document.getElementById('addArtworkForm');
    if (artworkForm) {
        artworkForm.addEventListener('submit', function(e) {
            e.preventDefault();
            publishArtwork();
        });
    }
}

function validateCurrentStep() {
    const currentStepElement = document.querySelector(`.formStep[data-step="${currentStep}"]`);
    if (!currentStepElement) return false;
    
    const requiredFields = currentStepElement.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('error');
            isValid = false;
        } else {
            field.classList.remove('error');
        }
    });
    
    if (!isValid) {
        showNotification('Please fill in all required fields', 'error');
    }
    
    return isValid;
}

function nextStep() {
    if (currentStep < totalSteps) {
        // Hide current step
        const currentStepElement = document.querySelector(`.formStep[data-step="${currentStep}"]`);
        if (currentStepElement) {
            currentStepElement.classList.remove('active');
        }
        
        // Update progress
        const currentProgressStep = document.querySelector(`.progressStep[data-step="${currentStep}"]`);
        if (currentProgressStep) {
            currentProgressStep.classList.remove('active');
            currentProgressStep.classList.add('completed');
        }
        
        currentStep++;
        
        // Show next step
        const nextStepElement = document.querySelector(`.formStep[data-step="${currentStep}"]`);
        if (nextStepElement) {
            nextStepElement.classList.add('active');
        }
        
        // Update progress
        const nextProgressStep = document.querySelector(`.progressStep[data-step="${currentStep}"]`);
        if (nextProgressStep) {
            nextProgressStep.classList.add('active');
        }
        
        updateStepNavigation();
        
        // Generate preview if we're on the last step
        if (currentStep === totalSteps) {
            generateArtworkPreview();
        }
    }
}

function previousStep() {
    if (currentStep > 1) {
        // Hide current step
        const currentStepElement = document.querySelector(`.formStep[data-step="${currentStep}"]`);
        if (currentStepElement) {
            currentStepElement.classList.remove('active');
        }
        
        // Update progress
        const currentProgressStep = document.querySelector(`.progressStep[data-step="${currentStep}"]`);
        if (currentProgressStep) {
            currentProgressStep.classList.remove('active');
        }
        
        currentStep--;
        
        // Show previous step
        const prevStepElement = document.querySelector(`.formStep[data-step="${currentStep}"]`);
        if (prevStepElement) {
            prevStepElement.classList.add('active');
        }
        
        // Update progress
        const prevProgressStep = document.querySelector(`.progressStep[data-step="${currentStep}"]`);
        if (prevProgressStep) {
            prevProgressStep.classList.add('active');
            prevProgressStep.classList.remove('completed');
        }
        
        updateStepNavigation();
    }
}

function updateStepNavigation() {
    const stepNum = document.getElementById('currentStepNum');
    const prevBtn = document.getElementById('prevStep');
    const nextBtn = document.getElementById('nextStep');
    const publishBtn = document.getElementById('publishBtn');
    
    if (stepNum) {
        stepNum.textContent = currentStep;
    }
    
    if (prevBtn) {
        prevBtn.style.display = currentStep > 1 ? 'block' : 'none';
    }
    
    if (nextBtn && publishBtn) {
        if (currentStep === totalSteps) {
            nextBtn.style.display = 'none';
            publishBtn.style.display = 'block';
        } else {
            nextBtn.style.display = 'block';
            publishBtn.style.display = 'none';
        }
    }
}

function calculateNetPrice(price) {
    const netPriceElement = document.getElementById('netPrice');
    if (netPriceElement) {
        const numPrice = parseFloat(price) || 0;
        const platformFee = numPrice * 0.05;
        const netPrice = numPrice - platformFee;
        netPriceElement.textContent = netPrice.toFixed(2);
    }
}

function initializeTags() {
    const tagsInput = document.getElementById('artworkTags');
    const tagsList = document.getElementById('tagsList');
    
    if (tagsInput && tagsList) {
        tagsInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addTag(this.value.trim());
                this.value = '';
            }
        });
    }
}

function addTag(tagText) {
    if (!tagText) return;
    
    const tagsList = document.getElementById('tagsList');
    if (!tagsList) return;
    
    const tag = document.createElement('span');
    tag.className = 'tag';
    tag.innerHTML = `
        ${tagText}
        <button type="button" onclick="removeTag(this)">×</button>
    `;
    
    tagsList.appendChild(tag);
}

function removeTag(button) {
    button.parentElement.remove();
}

function initializeImageUpload() {
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('artworkImages');
    
    if (uploadZone && fileInput) {
        uploadZone.addEventListener('click', function() {
            fileInput.click();
        });
        
        uploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        uploadZone.addEventListener('dragleave', function() {
            this.classList.remove('dragover');
        });
        
        uploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });
        
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });
    }
}

function handleFiles(files) {
    const uploadedImages = document.getElementById('uploadedImages');
    if (!uploadedImages) return;
    
    Array.from(files).forEach(file => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imagePreview = document.createElement('div');
                imagePreview.className = 'imagePreview';
                imagePreview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="removeImage" onclick="removeImage(this)">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                uploadedImages.appendChild(imagePreview);
            };
            reader.readAsDataURL(file);
        }
    });
}

function removeImage(button) {
    button.parentElement.remove();
}

function generateArtworkPreview() {
    const previewContainer = document.getElementById('artworkPreview');
    if (!previewContainer) return;
    
    const title = document.getElementById('artworkName').value;
    const price = document.getElementById('artworkPrice').value;
    const category = document.getElementById('artworkCategory').value;
    const description = document.getElementById('artworkDescription').value;
    
    previewContainer.innerHTML = `
        <div class="artworkPreviewCard">
            <h3>Artwork Preview</h3>
            <div class="previewContent">
                <h4>${title || 'Untitled Artwork'}</h4>
                <p class="previewPrice">EGP ${price || '0.00'}</p>
                <p class="previewCategory">${category || 'Category not selected'}</p>
                <p class="previewDescription">${description || 'No description provided'}</p>
            </div>
        </div>
    `;
}

function publishArtwork() {
    showNotification('Artwork published successfully!', 'success');
    
    // Reset form
    currentStep = 1;
    updateStepNavigation();
    
    // Switch to dashboard
    setTimeout(() => {
        switchSection('dashboard');
    }, 2000);
}

// ==========================================
// AUCTION FUNCTIONALITY
// ==========================================

function initializeAuction() {
    const auctionForm = document.getElementById('addAuctionForm');
    if (auctionForm) {
        auctionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            startAuction();
        });
    }
    
    // Multi-file upload for auction images
    const auctionImages = document.getElementById('auctionImages');
    if (auctionImages) {
        auctionImages.addEventListener('change', function() {
            handleAuctionImages(this.files);
        });
    }
}

function handleAuctionImages(files) {
    const preview = document.getElementById('auctionImagePreview');
    if (!preview) return;
    
    preview.innerHTML = '';
    
    Array.from(files).forEach(file => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'auctionPreviewImage';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    });
}

function startAuction() {
    showNotification('Auction started successfully!', 'success');
}

// ==========================================
// UTILITY FUNCTIONS
// ==========================================

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="closeNotification(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add to page
    let container = document.querySelector('.notification-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'notification-container';
        document.body.appendChild(container);
    }
    
    container.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}

function getNotificationIcon(type) {
    switch (type) {
        case 'success': return 'fa-check-circle';
        case 'error': return 'fa-exclamation-circle';
        case 'warning': return 'fa-exclamation-triangle';
        default: return 'fa-info-circle';
    }
}

function closeNotification(button) {
    const notification = button.closest('.notification');
    if (notification && notification.parentNode) {
        notification.parentNode.removeChild(notification);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

// ==========================================
// INITIALIZATION
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeMobileMenu();
    initializeSidebarNavigation();
    initializeDashboard();
    initializeOrders();
    initializeProfile();
    initializeArtworkForm();
    initializeAuction();
    
    console.log('Artist Portal initialized successfully');
});

// Handle page visibility changes
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        // Page is hidden
        closeSidebar();
    }
});