// Admin System JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeAdminSystem();
});

function initializeAdminSystem() {
    initializeSidebar();
    initializeTabs();
    loadSystemRoles();
    loadSystemLogs();
    initializeQuickActions();
    initializeEventListeners();
    startSystemMonitoring();
}

// Sample data
const systemRoles = [
    {
        id: 1,
        name: 'Super Admin',
        description: 'Full system access with all permissions',
        permissions: ['user_management', 'system_settings', 'content_management', 'financial_reports', 'security_management'],
        userCount: 2,
        lastModified: '2025-01-20'
    },
    {
        id: 2,
        name: 'Content Manager',
        description: 'Manage artworks, artists, and gallery content',
        permissions: ['content_management', 'artist_verification', 'artwork_approval'],
        userCount: 5,
        lastModified: '2025-01-22'
    },
    {
        id: 3,
        name: 'Customer Support',
        description: 'Handle customer inquiries and support tickets',
        permissions: ['support_tickets', 'user_communication', 'order_management'],
        userCount: 8,
        lastModified: '2025-01-23'
    },
    {
        id: 4,
        name: 'Marketing Manager',
        description: 'Manage promotional campaigns and marketing content',
        permissions: ['marketing_campaigns', 'newsletter_management', 'analytics_view'],
        userCount: 3,
        lastModified: '2025-01-24'
    }
];

const systemPermissions = [
    'user_management', 'system_settings', 'content_management', 'financial_reports',
    'security_management', 'artist_verification', 'artwork_approval', 'support_tickets',
    'user_communication', 'order_management', 'marketing_campaigns', 'newsletter_management',
    'analytics_view', 'backup_restore', 'maintenance_mode', 'audit_logs'
];

const systemLogs = [
    {
        time: '2025-01-24 14:32:15',
        level: 'info',
        message: 'User admin@yadawity.com logged in from IP 192.168.1.100'
    },
    {
        time: '2025-01-24 14:30:45',
        level: 'security',
        message: 'Failed login attempt for user hacker@evil.com from IP 45.123.45.67'
    },
    {
        time: '2025-01-24 14:28:30',
        level: 'warning',
        message: 'Database connection pool nearing maximum capacity (18/20 connections)'
    },
    {
        time: '2025-01-24 14:25:12',
        level: 'info',
        message: 'Automated backup completed successfully - 2.4GB stored'
    },
    {
        time: '2025-01-24 14:22:08',
        level: 'error',
        message: 'Payment processing failed for order #ORD-2025-1847 - Gateway timeout'
    },
    {
        time: '2025-01-24 14:20:33',
        level: 'info',
        message: 'New artwork submitted by artist marina.kovac@email.com - pending approval'
    },
    {
        time: '2025-01-24 14:18:55',
        level: 'security',
        message: 'SSL certificate renewed successfully - expires 2026-01-24'
    },
    {
        time: '2025-01-24 14:15:42',
        level: 'warning',
        message: 'Cache memory usage above 85% - consider clearing cache'
    }
];

// Initialize sidebar functionality
function initializeSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 1024) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    }
}

// Initialize tabs functionality
function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tabBtn');
    const tabPanes = document.querySelectorAll('.tabPane');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;

            // Remove active class from all tabs and panes
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));

            // Add active class to clicked tab and corresponding pane
            this.classList.add('active');
            const targetPane = document.getElementById(targetTab);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });
}

// Load system roles
function loadSystemRoles() {
    const rolesGrid = document.getElementById('rolesGrid');
    if (!rolesGrid) return;

    rolesGrid.innerHTML = '';

    systemRoles.forEach(role => {
        const roleCard = document.createElement('div');
        roleCard.className = 'roleCard';
        roleCard.innerHTML = `
            <div class="roleHeader">
                <div class="roleInfo">
                    <h4>${role.name}</h4>
                    <p>${role.description}</p>
                </div>
                <div class="roleActions">
                    <button class="btn btn-sm btn-outline" onclick="editRole(${role.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline" onclick="deleteRole(${role.id})" style="color: var(--danger-red); border-color: var(--danger-red);">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            
            <div class="rolePermissions">
                <h5>Permissions (${role.permissions.length})</h5>
                <div class="permissionTags">
                    ${role.permissions.slice(0, 3).map(perm => 
                        `<span class="permissionTag">${formatPermission(perm)}</span>`
                    ).join('')}
                    ${role.permissions.length > 3 ? `<span class="permissionTag">+${role.permissions.length - 3} more</span>` : ''}
                </div>
            </div>
            
            <div class="roleStats">
                <span><i class="fas fa-users"></i> ${role.userCount} users</span>
                <span><i class="fas fa-calendar"></i> Modified ${formatDate(role.lastModified)}</span>
            </div>
        `;
        rolesGrid.appendChild(roleCard);
    });
}

// Load system logs
function loadSystemLogs() {
    const logsContainer = document.getElementById('logsContainer');
    if (!logsContainer) return;

    logsContainer.innerHTML = '';

    systemLogs.forEach(log => {
        const logEntry = document.createElement('div');
        logEntry.className = 'logEntry';
        logEntry.innerHTML = `
            <span class="logTime">${log.time}</span>
            <span class="logLevel ${log.level}">${log.level}</span>
            <span class="logMessage">${log.message}</span>
        `;
        logsContainer.appendChild(logEntry);
    });

    // Auto-scroll to bottom
    logsContainer.scrollTop = logsContainer.scrollHeight;
}

// Initialize quick actions
function initializeQuickActions() {
    const quickActionButtons = {
        'clearCacheBtn': clearSystemCache,
        'optimizeDbBtn': optimizeDatabase,
        'securityScanBtn': runSecurityScan,
        'updateSystemBtn': checkSystemUpdates
    };

    Object.entries(quickActionButtons).forEach(([buttonId, action]) => {
        const button = document.getElementById(buttonId);
        if (button) {
            button.addEventListener('click', action);
        }
    });
}

// Initialize event listeners
function initializeEventListeners() {
    // Main action buttons
    const backupBtn = document.getElementById('backupBtn');
    if (backupBtn) {
        backupBtn.addEventListener('click', createSystemBackup);
    }

    const maintenanceBtn = document.getElementById('maintenanceBtn');
    if (maintenanceBtn) {
        maintenanceBtn.addEventListener('click', toggleMaintenanceMode);
    }

    // Configuration save
    const saveConfigBtn = document.getElementById('saveConfigBtn');
    if (saveConfigBtn) {
        saveConfigBtn.addEventListener('click', saveConfiguration);
    }

    // Role management
    const addRoleBtn = document.getElementById('addRoleBtn');
    if (addRoleBtn) {
        addRoleBtn.addEventListener('click', function() {
            showRoleModal();
        });
    }

    // Log filter
    const logFilter = document.getElementById('logFilter');
    if (logFilter) {
        logFilter.addEventListener('change', filterLogs);
    }

    // Export logs
    const exportLogsBtn = document.getElementById('exportLogsBtn');
    if (exportLogsBtn) {
        exportLogsBtn.addEventListener('click', exportSystemLogs);
    }

    // Role modal
    initializeRoleModal();
}

// Quick Action Functions
function clearSystemCache() {
    const button = document.getElementById('clearCacheBtn');
    button.classList.add('loading');
    
    showNotification('Clearing system cache...', 'info');
    
    setTimeout(() => {
        button.classList.remove('loading');
        showNotification('System cache cleared successfully', 'success');
        updateCacheStatus();
    }, 3000);
}

function optimizeDatabase() {
    const button = document.getElementById('optimizeDbBtn');
    button.classList.add('loading');
    
    showNotification('Optimizing database...', 'info');
    
    setTimeout(() => {
        button.classList.remove('loading');
        showNotification('Database optimization completed', 'success');
        updateDatabaseStats();
    }, 5000);
}

function runSecurityScan() {
    const button = document.getElementById('securityScanBtn');
    button.classList.add('loading');
    
    showNotification('Running security scan...', 'info');
    
    setTimeout(() => {
        button.classList.remove('loading');
        showNotification('Security scan completed - No threats detected', 'success');
        addSecurityLog();
    }, 7000);
}

function checkSystemUpdates() {
    const button = document.getElementById('updateSystemBtn');
    button.classList.add('loading');
    
    showNotification('Checking for system updates...', 'info');
    
    setTimeout(() => {
        button.classList.remove('loading');
        showNotification('System is up to date', 'success');
    }, 4000);
}

// Main Actions
function createSystemBackup() {
    const button = document.getElementById('backupBtn');
    button.classList.add('loading');
    
    showNotification('Creating system backup...', 'info');
    
    setTimeout(() => {
        button.classList.remove('loading');
        showNotification('System backup created successfully', 'success');
        updateBackupStatus();
    }, 8000);
}

function toggleMaintenanceMode() {
    const button = document.getElementById('maintenanceBtn');
    const maintenanceToggle = document.getElementById('maintenanceMode');
    
    if (maintenanceToggle.checked) {
        // Disable maintenance mode
        maintenanceToggle.checked = false;
        button.innerHTML = '<i class="fas fa-tools"></i> Maintenance Mode';
        showNotification('Maintenance mode disabled', 'success');
    } else {
        // Enable maintenance mode
        maintenanceToggle.checked = true;
        button.innerHTML = '<i class="fas fa-tools"></i> Exit Maintenance';
        showNotification('Maintenance mode enabled', 'warning');
    }
}

function saveConfiguration() {
    const button = document.getElementById('saveConfigBtn');
    button.classList.add('loading');
    
    showNotification('Saving configuration...', 'info');
    
    setTimeout(() => {
        button.classList.remove('loading');
        showNotification('Configuration saved successfully', 'success');
    }, 2000);
}

// Role Management
function initializeRoleModal() {
    const roleModal = document.getElementById('roleModal');
    const closeRoleModal = document.getElementById('closeRoleModal');
    const cancelRole = document.getElementById('cancelRole');
    const roleForm = document.getElementById('roleForm');

    // Close modal handlers
    [closeRoleModal, cancelRole].forEach(button => {
        if (button) {
            button.addEventListener('click', function() {
                closeModal(roleModal);
            });
        }
    });

    // Form submission
    if (roleForm) {
        roleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveRole();
        });
    }

    // Load permissions in modal
    loadPermissionsGrid();

    // Close on backdrop click
    if (roleModal) {
        roleModal.addEventListener('click', function(e) {
            if (e.target === roleModal) {
                closeModal(roleModal);
            }
        });
    }
}

function showRoleModal(roleId = null) {
    const modal = document.getElementById('roleModal');
    const form = document.getElementById('roleForm');
    
    if (roleId) {
        // Edit existing role
        const role = systemRoles.find(r => r.id === roleId);
        if (role) {
            document.getElementById('roleName').value = role.name;
            document.getElementById('roleDescription').value = role.description;
            
            // Check appropriate permissions
            const checkboxes = document.querySelectorAll('#permissionsGrid input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = role.permissions.includes(checkbox.value);
            });
        }
    } else {
        // New role
        form.reset();
        const checkboxes = document.querySelectorAll('#permissionsGrid input[type="checkbox"]');
        checkboxes.forEach(checkbox => checkbox.checked = false);
    }
    
    showModal('roleModal');
}

function loadPermissionsGrid() {
    const permissionsGrid = document.getElementById('permissionsGrid');
    if (!permissionsGrid) return;

    permissionsGrid.innerHTML = '';

    systemPermissions.forEach(permission => {
        const permissionItem = document.createElement('div');
        permissionItem.className = 'permissionItem';
        permissionItem.innerHTML = `
            <input type="checkbox" id="perm_${permission}" value="${permission}">
            <label for="perm_${permission}">${formatPermission(permission)}</label>
        `;
        permissionsGrid.appendChild(permissionItem);
    });
}

function saveRole() {
    const roleName = document.getElementById('roleName').value;
    const roleDescription = document.getElementById('roleDescription').value;
    const selectedPermissions = Array.from(
        document.querySelectorAll('#permissionsGrid input:checked')
    ).map(checkbox => checkbox.value);

    if (!roleName || selectedPermissions.length === 0) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }

    // Simulate saving
    showNotification('Saving role...', 'info');
    
    setTimeout(() => {
        showNotification(`Role "${roleName}" saved successfully`, 'success');
        closeModal(document.getElementById('roleModal'));
        
        // Add new role to list (in real app, this would be from server)
        systemRoles.push({
            id: systemRoles.length + 1,
            name: roleName,
            description: roleDescription,
            permissions: selectedPermissions,
            userCount: 0,
            lastModified: new Date().toISOString().split('T')[0]
        });
        
        loadSystemRoles();
    }, 1500);
}

function editRole(roleId) {
    showRoleModal(roleId);
}

function deleteRole(roleId) {
    const role = systemRoles.find(r => r.id === roleId);
    if (!role) return;

    if (confirm(`Are you sure you want to delete the role "${role.name}"? This action cannot be undone.`)) {
        showNotification(`Role "${role.name}" deleted successfully`, 'success');
        
        // Remove from array (in real app, this would be server call)
        const index = systemRoles.findIndex(r => r.id === roleId);
        if (index > -1) {
            systemRoles.splice(index, 1);
            loadSystemRoles();
        }
    }
}

// Log Management
function filterLogs() {
    const filter = document.getElementById('logFilter').value;
    const logEntries = document.querySelectorAll('.logEntry');

    logEntries.forEach(entry => {
        const level = entry.querySelector('.logLevel').textContent;
        if (!filter || level === filter) {
            entry.style.display = 'flex';
        } else {
            entry.style.display = 'none';
        }
    });
}

function exportSystemLogs() {
    showNotification('Exporting system logs...', 'info');
    
    setTimeout(() => {
        showNotification('System logs exported successfully', 'success');
    }, 2000);
}

// System Monitoring
function startSystemMonitoring() {
    // Simulate real-time log updates
    setInterval(() => {
        addRandomLogEntry();
    }, 30000); // Add new log every 30 seconds

    // Update system stats
    setInterval(() => {
        updateSystemStats();
    }, 60000); // Update stats every minute
}

function addRandomLogEntry() {
    const logTypes = [
        { level: 'info', message: 'User session refreshed automatically' },
        { level: 'info', message: 'Database connection pool optimized' },
        { level: 'security', message: 'SSL certificate check passed' },
        { level: 'warning', message: 'Memory usage approaching threshold' }
    ];

    const randomLog = logTypes[Math.floor(Math.random() * logTypes.length)];
    const newLog = {
        time: new Date().toLocaleString(),
        level: randomLog.level,
        message: randomLog.message
    };

    systemLogs.unshift(newLog);
    
    // Keep only last 50 logs
    if (systemLogs.length > 50) {
        systemLogs.pop();
    }

    loadSystemLogs();
}

// Update Functions
function updateCacheStatus() {
    // Update cache-related stats
    showNotification('Cache status updated', 'info');
}

function updateDatabaseStats() {
    // Update database size in stats
    const dbStat = document.querySelector('.statsGrid .statCard:nth-child(2) h3');
    if (dbStat) {
        dbStat.textContent = '2.3GB';
    }
}

function updateBackupStatus() {
    // Update last backup time
    const backupStat = document.querySelector('.statsGrid .statCard:nth-child(4) h3');
    if (backupStat) {
        backupStat.textContent = 'Just now';
    }
}

function updateSystemStats() {
    // Simulate random system stat updates
    const stats = document.querySelectorAll('.statChange');
    stats.forEach(stat => {
        if (Math.random() > 0.8) { // 20% chance to update each stat
            const change = (Math.random() * 2 - 1).toFixed(1); // Random change between -1 and 1
            if (stat.textContent.includes('%')) {
                stat.textContent = `${change > 0 ? '+' : ''}${change}%`;
            }
        }
    });
}

function addSecurityLog() {
    const securityLog = {
        time: new Date().toLocaleString(),
        level: 'security',
        message: 'Security scan completed - System integrity verified'
    };
    
    systemLogs.unshift(securityLog);
    loadSystemLogs();
}

// Utility Functions
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modal) {
    if (modal) {
        modal.classList.remove('active');
    }
}

function formatPermission(permission) {
    return permission.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric'
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close">
            <i class="fas fa-times"></i>
        </button>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Position notification
    const notifications = document.querySelectorAll('.notification');
    const index = Array.from(notifications).indexOf(notification);
    notification.style.top = `${20 + (index * 70)}px`;

    // Show notification
    setTimeout(() => notification.classList.add('show'), 100);

    // Auto remove
    setTimeout(() => removeNotification(notification), 5000);

    // Close button
    notification.querySelector('.notification-close').addEventListener('click', () => {
        removeNotification(notification);
    });
}

function removeNotification(notification) {
    notification.classList.remove('show');
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
        repositionNotifications();
    }, 300);
}

function repositionNotifications() {
    const notifications = document.querySelectorAll('.notification');
    notifications.forEach((notification, index) => {
        notification.style.top = `${20 + (index * 70)}px`;
    });
}

function getNotificationIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}
