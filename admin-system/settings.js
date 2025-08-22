let currentCategory = '';

document.addEventListener('DOMContentLoaded', function() {
    // Check authentication
    if (!localStorage.getItem('csrf_token')) {
        window.location.href = 'login.php';
        return;
    }

    // Display user info
    const userInfo = document.getElementById('userInfo');
    const userName = localStorage.getItem('user_name') || 'Admin';
    userInfo.textContent = `Welcome, ${userName}`;

    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-open');
        });
    }

    // Load settings
    loadSettings();

    // Add category filter event listeners
    document.querySelectorAll('input[name="settingCategory"]').forEach(radio => {
        radio.addEventListener('change', function() {
            currentCategory = this.value;
            loadSettings();
        });
    });
});

async function loadSettings() {
    try {
        let url = '/admin-system/API/settings.php';
        if (currentCategory) {
            url += `?category=${encodeURIComponent(currentCategory)}`;
        }

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.status === 401 || response.status === 403) {
            localStorage.clear();
            window.location.href = 'login.php';
            return;
        }

        const data = await response.json();
        
        if (response.ok && data.data) {
            displaySettings(data.data);
        } else {
            console.error('Failed to load settings:', data.error);
        }
    } catch (error) {
        console.error('Error loading settings:', error);
    }
}

function displaySettings(settings) {
    const tbody = document.getElementById('settingsTableBody');
    
    if (!settings || settings.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No settings found</td></tr>';
        return;
    }

    let html = '';
    settings.forEach(setting => {
        const categoryClass = getCategoryClass(setting.category);
        const truncatedValue = setting.value.length > 50 ? 
            setting.value.substring(0, 50) + '...' : setting.value;
        
        html += `
            <tr>
                <td><code>${setting.setting_key}</code></td>
                <td>
                    <div class="text-truncate" style="max-width: 200px;" title="${setting.value}">
                        ${truncatedValue}
                    </div>
                </td>
                <td><span class="badge ${categoryClass}">${setting.category}</span></td>
                <td>${setting.description || '-'}</td>
                <td>${new Date(setting.updated_at).toLocaleDateString()}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editSetting('${setting.setting_key}')">
                        Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteSetting('${setting.setting_key}')">
                        Delete
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function getCategoryClass(category) {
    const categoryClasses = {
        'general': 'bg-primary',
        'email': 'bg-info',
        'payment': 'bg-success',
        'security': 'bg-warning',
        'system': 'bg-secondary'
    };
    return categoryClasses[category] || 'bg-secondary';
}

function openAddSettingModal() {
    document.getElementById('settingModalTitle').textContent = 'Add New Setting';
    document.getElementById('settingForm').reset();
    document.getElementById('settingId').value = '';
}

async function editSetting(settingKey) {
    try {
        const response = await fetch(`/admin-system/API/settings.php?key=${settingKey}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            const setting = data.data;
            
            document.getElementById('settingModalTitle').textContent = 'Edit Setting';
            document.getElementById('settingId').value = setting.setting_key;
            document.getElementById('settingKey').value = setting.setting_key;
            document.getElementById('settingValue').value = setting.value;
            document.getElementById('settingCategory').value = setting.category;
            document.getElementById('settingDescription').value = setting.description || '';
            
            // Disable key editing for existing settings
            document.getElementById('settingKey').disabled = true;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('addSettingModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Error loading setting details:', error);
    }
}

async function saveSetting() {
    const settingKey = document.getElementById('settingId').value;
    const isEdit = settingKey !== '';
    
    const settingData = {
        setting_key: document.getElementById('settingKey').value,
        value: document.getElementById('settingValue').value,
        category: document.getElementById('settingCategory').value,
        description: document.getElementById('settingDescription').value
    };

    try {
        const url = isEdit ? `/admin-system/API/settings.php?key=${settingKey}` : '/admin-system/API/settings.php';
        const method = isEdit ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': localStorage.getItem('csrf_token')
            },
            body: JSON.stringify(settingData)
        });

        if (response.status === 401 || response.status === 403) {
            localStorage.clear();
            window.location.href = 'login.php';
            return;
        }

        const data = await response.json();
        
        if (response.ok) {
            // Close modal and reload settings
            const modal = bootstrap.Modal.getInstance(document.getElementById('addSettingModal'));
            modal.hide();
            
            // Reset form and enable key field
            document.getElementById('settingForm').reset();
            document.getElementById('settingKey').disabled = false;
            
            loadSettings();
        } else {
            alert('Error: ' + (data.error || 'Failed to save setting'));
        }
    } catch (error) {
        console.error('Error saving setting:', error);
        alert('Error saving setting. Please try again.');
    }
}

async function deleteSetting(settingKey) {
    if (!confirm('Are you sure you want to delete this setting? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`/admin-system/API/settings.php?key=${settingKey}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': localStorage.getItem('csrf_token')
            }
        });

        if (response.status === 401 || response.status === 403) {
            localStorage.clear();
            window.location.href = 'login.php';
            return;
        }

        const data = await response.json();
        
        if (response.ok) {
            loadSettings();
        } else {
            alert('Error: ' + (data.error || 'Failed to delete setting'));
        }
    } catch (error) {
        console.error('Error deleting setting:', error);
        alert('Error deleting setting. Please try again.');
    }
}

// Reset form when modal is closed
document.getElementById('addSettingModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('settingForm').reset();
    document.getElementById('settingKey').disabled = false;
});

function logout() {
    fetch('/admin-system/API/logout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': localStorage.getItem('csrf_token')
        }
    }).finally(() => {
        localStorage.clear();
        window.location.href = 'login.php';
    });
}
