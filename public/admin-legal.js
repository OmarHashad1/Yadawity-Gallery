// Legal & Compliance Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeLegalManagement();
    initializeSearch();
    initializeFilters();
    initializeModals();
    initializeNotifications();
    initializeChecklistHandlers();
    loadComplianceData();
    loadDocuments();
    loadPolicies();
    loadChecklist();
    loadAuditTrail();

    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (mobileMenuToggle && sidebar) {
        mobileMenuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
});

// Global variables
let documentsData = [];
let policiesData = [];
let checklistData = [];
let auditData = [];
let filteredDocuments = [];

// Sample compliance alerts data
const sampleAlerts = [
    {
        id: 'alert1',
        type: 'critical',
        title: 'Insurance Policy Expiring Soon',
        description: 'General liability insurance expires in 7 days. Renewal required immediately.',
        dueDate: '2024-02-01',
        category: 'Insurance',
        actions: ['Renew Policy', 'Contact Agent']
    },
    {
        id: 'alert2',
        type: 'warning',
        title: 'Artist Contract Review Due',
        description: 'Monthly review of artist contracts is due. 5 contracts require attention.',
        dueDate: '2024-02-05',
        category: 'Contracts',
        actions: ['Review Contracts', 'Schedule Meeting']
    },
    {
        id: 'alert3',
        type: 'info',
        title: 'Privacy Policy Update Available',
        description: 'New GDPR requirements suggest updating privacy policy to include recent changes.',
        dueDate: '2024-02-15',
        category: 'Policies',
        actions: ['Review Changes', 'Update Policy']
    }
];

// Sample legal documents data
const sampleDocuments = [
    {
        id: 'DOC001',
        title: 'General Liability Insurance',
        type: 'insurance',
        status: 'active',
        createdDate: '2023-01-15',
        expiryDate: '2024-02-01',
        responsibleParty: 'Legal Department',
        description: 'Comprehensive liability coverage for gallery operations',
        fileUrl: '#'
    },
    {
        id: 'DOC002',
        title: 'Artist Consignment Agreement Template',
        type: 'contract',
        status: 'active',
        createdDate: '2023-03-10',
        expiryDate: '2025-03-10',
        responsibleParty: 'Contracts Manager',
        description: 'Standard template for artist consignment agreements',
        fileUrl: '#'
    },
    {
        id: 'DOC003',
        title: 'Data Privacy Policy',
        type: 'policy',
        status: 'pending',
        createdDate: '2023-12-01',
        expiryDate: null,
        responsibleParty: 'Compliance Officer',
        description: 'Updated privacy policy for GDPR compliance',
        fileUrl: '#'
    },
    {
        id: 'DOC004',
        title: 'Business License',
        type: 'license',
        status: 'active',
        createdDate: '2023-01-01',
        expiryDate: '2024-12-31',
        responsibleParty: 'Operations Manager',
        description: 'Municipal business operation license',
        fileUrl: '#'
    },
    {
        id: 'DOC005',
        title: 'Exhibition Space Rental Agreement',
        type: 'agreement',
        status: 'draft',
        createdDate: '2024-01-15',
        expiryDate: null,
        responsibleParty: 'Events Coordinator',
        description: 'Template for renting exhibition spaces to third parties',
        fileUrl: '#'
    }
];

// Sample policies data
const samplePolicies = [
    {
        id: 'POL001',
        title: 'Employee Code of Conduct',
        description: 'Guidelines for professional behavior and ethical standards',
        lastUpdated: '2023-09-15',
        version: '2.1',
        status: 'active',
        category: 'HR',
        icon: 'fas fa-users'
    },
    {
        id: 'POL002',
        title: 'Artwork Handling Procedures',
        description: 'Safety protocols for handling and displaying artwork',
        lastUpdated: '2023-11-20',
        version: '1.3',
        status: 'active',
        category: 'Operations',
        icon: 'fas fa-paint-brush'
    },
    {
        id: 'POL003',
        title: 'Customer Data Protection',
        description: 'Procedures for protecting customer personal information',
        lastUpdated: '2023-12-01',
        version: '3.0',
        status: 'pending',
        category: 'Privacy',
        icon: 'fas fa-shield-alt'
    },
    {
        id: 'POL004',
        title: 'Financial Transaction Security',
        description: 'Security measures for processing payments and transactions',
        lastUpdated: '2023-10-10',
        version: '1.8',
        status: 'active',
        category: 'Finance',
        icon: 'fas fa-credit-card'
    }
];

// Sample checklist data
const sampleChecklist = [
    {
        category: 'Legal Documentation',
        items: [
            {
                id: 'check1',
                title: 'Business License Renewal',
                description: 'Verify business license is current and renew if necessary',
                completed: true,
                dueDate: '2024-01-31',
                priority: 'high'
            },
            {
                id: 'check2',
                title: 'Insurance Policy Review',
                description: 'Annual review of all insurance policies and coverage amounts',
                completed: false,
                dueDate: '2024-02-15',
                priority: 'high'
            },
            {
                id: 'check3',
                title: 'Contract Template Updates',
                description: 'Update standard contract templates with latest legal requirements',
                completed: true,
                dueDate: '2024-01-15',
                priority: 'medium'
            }
        ]
    },
    {
        category: 'Data Protection',
        items: [
            {
                id: 'check4',
                title: 'GDPR Compliance Audit',
                description: 'Quarterly audit of data protection practices and procedures',
                completed: false,
                dueDate: '2024-03-01',
                priority: 'high'
            },
            {
                id: 'check5',
                title: 'Privacy Policy Update',
                description: 'Review and update privacy policy for website and services',
                completed: false,
                dueDate: '2024-02-20',
                priority: 'medium'
            },
            {
                id: 'check6',
                title: 'Data Backup Verification',
                description: 'Verify customer data backup procedures are working correctly',
                completed: true,
                dueDate: '2024-01-30',
                priority: 'high'
            }
        ]
    },
    {
        category: 'Intellectual Property',
        items: [
            {
                id: 'check7',
                title: 'Copyright Documentation',
                description: 'Ensure all artwork has proper copyright documentation',
                completed: true,
                dueDate: '2024-01-20',
                priority: 'high'
            },
            {
                id: 'check8',
                title: 'Artist Rights Verification',
                description: 'Verify artists have proper rights to sell their artwork',
                completed: false,
                dueDate: '2024-02-10',
                priority: 'medium'
            }
        ]
    }
];

// Sample audit trail data
const sampleAuditTrail = [
    {
        id: 'audit1',
        action: 'Document Updated',
        description: 'Privacy Policy v3.0 updated with new GDPR requirements',
        user: 'Legal Department',
        timestamp: '2 hours ago',
        type: 'update',
        icon: 'fas fa-edit'
    },
    {
        id: 'audit2',
        action: 'Contract Signed',
        description: 'Artist consignment agreement signed with Maria Rodriguez',
        user: 'Contracts Manager',
        timestamp: '4 hours ago',
        type: 'create',
        icon: 'fas fa-signature'
    },
    {
        id: 'audit3',
        action: 'Policy Review',
        description: 'Employee Code of Conduct reviewed and approved',
        user: 'HR Manager',
        timestamp: '1 day ago',
        type: 'review',
        icon: 'fas fa-check-circle'
    },
    {
        id: 'audit4',
        action: 'License Renewed',
        description: 'Business license renewed for 2024',
        user: 'Operations Manager',
        timestamp: '2 days ago',
        type: 'renewal',
        icon: 'fas fa-certificate'
    },
    {
        id: 'audit5',
        action: 'Compliance Check',
        description: 'Quarterly compliance review completed',
        user: 'Compliance Officer',
        timestamp: '3 days ago',
        type: 'audit',
        icon: 'fas fa-clipboard-check'
    }
];

// Initialize legal management
function initializeLegalManagement() {
    // Add document button
    const addDocumentBtn = document.querySelector('.add-document-btn');
    if (addDocumentBtn) {
        addDocumentBtn.addEventListener('click', function() {
            showAddDocumentModal();
        });
    }

    // Add policy button
    const addPolicyBtn = document.querySelector('.add-policy-btn');
    if (addPolicyBtn) {
        addPolicyBtn.addEventListener('click', function() {
            showAddPolicyModal();
        });
    }

    // Document actions
    document.addEventListener('click', function(e) {
        if (e.target.matches('.view-doc-btn') || e.target.closest('.view-doc-btn')) {
            const docId = e.target.closest('[data-document-id]').dataset.documentId;
            viewDocument(docId);
        }
        if (e.target.matches('.edit-doc-btn') || e.target.closest('.edit-doc-btn')) {
            const docId = e.target.closest('[data-document-id]').dataset.documentId;
            editDocument(docId);
        }
        if (e.target.matches('.delete-doc-btn') || e.target.closest('.delete-doc-btn')) {
            const docId = e.target.closest('[data-document-id]').dataset.documentId;
            deleteDocument(docId);
        }
    });

    // Policy actions
    document.addEventListener('click', function(e) {
        if (e.target.matches('.edit-policy-btn') || e.target.closest('.edit-policy-btn')) {
            const policyId = e.target.closest('[data-policy-id]').dataset.policyId;
            editPolicy(policyId);
        }
        if (e.target.matches('.view-policy-btn') || e.target.closest('.view-policy-btn')) {
            const policyId = e.target.closest('[data-policy-id]').dataset.policyId;
            viewPolicy(policyId);
        }
    });
}

// Initialize search functionality
function initializeSearch() {
    const searchInput = document.querySelector('#documentSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            filterDocuments(query);
        });
    }
}

// Initialize filters
function initializeFilters() {
    const filters = document.querySelectorAll('#documentTypeFilter, #statusFilter');
    filters.forEach(filter => {
        filter.addEventListener('change', applyDocumentFilters);
    });
}

// Initialize modals
function initializeModals() {
    // Modal close buttons
    document.addEventListener('click', function(e) {
        if (e.target.matches('.modal-close') || e.target.matches('.modal-backdrop')) {
            closeModals();
        }
    });

    // Form submissions
    document.addEventListener('submit', function(e) {
        if (e.target.matches('#addDocumentForm')) {
            e.preventDefault();
            handleAddDocument(e.target);
        }
        if (e.target.matches('#editDocumentForm')) {
            e.preventDefault();
            handleEditDocument(e.target);
        }
        if (e.target.matches('#addPolicyForm')) {
            e.preventDefault();
            handleAddPolicy(e.target);
        }
    });
}

// Initialize notifications
function initializeNotifications() {
    // Auto-hide notifications after 5 seconds
    setTimeout(() => {
        const notifications = document.querySelectorAll('.notification');
        notifications.forEach(notification => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        });
    }, 5000);
}

// Initialize checklist handlers
function initializeChecklistHandlers() {
    // Category toggle
    document.addEventListener('click', function(e) {
        if (e.target.matches('.category-header') || e.target.closest('.category-header')) {
            const categoryHeader = e.target.closest('.category-header');
            const categoryItems = categoryHeader.nextElementSibling;
            categoryItems.classList.toggle('expanded');
        }
    });

    // Checklist item completion
    document.addEventListener('change', function(e) {
        if (e.target.matches('.item-checkbox')) {
            const itemId = e.target.dataset.itemId;
            const isCompleted = e.target.checked;
            updateChecklistItem(itemId, isCompleted);
        }
    });
}

// Load compliance data
function loadComplianceData() {
    documentsData = [...sampleDocuments];
    policiesData = [...samplePolicies];
    checklistData = [...sampleChecklist];
    auditData = [...sampleAuditTrail];
    filteredDocuments = [...documentsData];
    
    updateStats();
    loadComplianceAlerts();
}

// Load compliance alerts
function loadComplianceAlerts() {
    const alertsContainer = document.querySelector('#complianceAlerts');
    if (!alertsContainer) return;

    if (sampleAlerts.length === 0) {
        alertsContainer.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>No compliance alerts</h3>
                <p>All compliance requirements are up to date</p>
            </div>
        `;
        return;
    }

    alertsContainer.innerHTML = sampleAlerts.map(alert => `
        <div class="alert-item ${alert.type}" data-alert-id="${alert.id}">
            <div class="alert-icon">
                <i class="fas fa-${alert.type === 'critical' ? 'exclamation-circle' : alert.type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
            </div>
            <div class="alert-content">
                <h4>${alert.title}</h4>
                <p>${alert.description}</p>
                <div class="alert-meta">
                    <span><i class="fas fa-calendar"></i> Due: ${alert.dueDate}</span>
                    <span><i class="fas fa-folder"></i> ${alert.category}</span>
                </div>
            </div>
            <div class="alert-actions">
                ${alert.actions.map(action => `
                    <button class="btn btn-sm btn-primary" onclick="handleAlertAction('${alert.id}', '${action}')">
                        ${action}
                    </button>
                `).join('')}
                <button class="btn btn-sm btn-secondary" onclick="dismissAlert('${alert.id}')">
                    Dismiss
                </button>
            </div>
        </div>
    `).join('');
}

// Load documents
function loadDocuments() {
    renderDocuments();
}

// Load policies
function loadPolicies() {
    const policiesGrid = document.querySelector('#policiesGrid');
    if (!policiesGrid) return;

    if (policiesData.length === 0) {
        policiesGrid.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <h3>No policies found</h3>
                <p>Create your first policy document</p>
                <button class="btn btn-primary add-policy-btn">Add Policy</button>
            </div>
        `;
        return;
    }

    policiesGrid.innerHTML = policiesData.map(policy => `
        <div class="policy-card" data-policy-id="${policy.id}">
            <div class="policy-header">
                <div class="policy-icon">
                    <i class="${policy.icon}"></i>
                </div>
                <div class="policy-info">
                    <h3>${policy.title}</h3>
                    <p>${policy.description}</p>
                </div>
            </div>
            <div class="policy-meta">
                <span>Version ${policy.version}</span>
                <span class="policy-status status-${policy.status}">${policy.status}</span>
            </div>
            <div class="policy-actions">
                <button class="btn btn-sm btn-secondary view-policy-btn" title="View">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-primary edit-policy-btn" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
        </div>
    `).join('');
}

// Load checklist
function loadChecklist() {
    const checklistContainer = document.querySelector('#complianceChecklist');
    if (!checklistContainer) return;

    checklistContainer.innerHTML = checklistData.map(category => {
        const completedItems = category.items.filter(item => item.completed).length;
        const totalItems = category.items.length;
        const progress = Math.round((completedItems / totalItems) * 100);

        return `
            <div class="checklist-category">
                <div class="category-header">
                    <h3 class="category-title">${category.category}</h3>
                    <div class="category-progress">
                        <span>${completedItems}/${totalItems}</span>
                        <div class="progress-circle ${completedItems === totalItems ? 'complete' : ''}">
                            ${completedItems === totalItems ? '✓' : progress + '%'}
                        </div>
                    </div>
                </div>
                <div class="category-items">
                    ${category.items.map(item => `
                        <div class="checklist-item">
                            <input type="checkbox" class="item-checkbox" data-item-id="${item.id}" ${item.completed ? 'checked' : ''}>
                            <div class="item-content">
                                <h4 class="item-title">${item.title}</h4>
                                <p class="item-description">${item.description}</p>
                                <div class="item-meta">
                                    <span><i class="fas fa-calendar"></i> Due: ${item.dueDate}</span>
                                    <span><i class="fas fa-flag"></i> Priority: ${item.priority}</span>
                                </div>
                            </div>
                            <div class="item-actions">
                                <button class="btn btn-sm btn-secondary" onclick="editChecklistItem('${item.id}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }).join('');

    updateComplianceProgress();
}

// Load audit trail
function loadAuditTrail() {
    const auditTrail = document.querySelector('#auditTrail');
    if (!auditTrail) return;

    if (auditData.length === 0) {
        auditTrail.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h3>No audit records</h3>
                <p>Audit trail will appear here once activities begin</p>
            </div>
        `;
        return;
    }

    auditTrail.innerHTML = auditData.map(audit => `
        <div class="audit-item">
            <div class="audit-icon">
                <i class="${audit.icon}"></i>
            </div>
            <div class="audit-content">
                <h4 class="audit-action">${audit.action}</h4>
                <p class="audit-description">${audit.description}</p>
                <div class="audit-meta">
                    <span><i class="fas fa-user"></i> ${audit.user}</span>
                    <span><i class="fas fa-tag"></i> ${audit.type}</span>
                </div>
            </div>
            <div class="audit-timestamp">
                ${audit.timestamp}
            </div>
        </div>
    `).join('');
}

// Update statistics
function updateStats() {
    const contractsCount = document.querySelector('[data-stat="contracts"]');
    const policiesCount = document.querySelector('[data-stat="policies"]');
    const pendingCount = document.querySelector('[data-stat="pending"]');
    const complianceRate = document.querySelector('[data-stat="compliance"]');

    if (contractsCount) contractsCount.textContent = documentsData.filter(doc => doc.type === 'contract').length;
    if (policiesCount) policiesCount.textContent = policiesData.length;
    if (pendingCount) pendingCount.textContent = documentsData.filter(doc => doc.status === 'pending').length;
    if (complianceRate) {
        const totalItems = checklistData.reduce((sum, category) => sum + category.items.length, 0);
        const completedItems = checklistData.reduce((sum, category) => 
            sum + category.items.filter(item => item.completed).length, 0);
        const rate = totalItems > 0 ? Math.round((completedItems / totalItems) * 100) : 100;
        complianceRate.textContent = `${rate}%`;
    }
}

// Filter documents
function filterDocuments(query) {
    filteredDocuments = documentsData.filter(doc => 
        doc.title.toLowerCase().includes(query) ||
        doc.type.toLowerCase().includes(query) ||
        doc.responsibleParty.toLowerCase().includes(query) ||
        doc.description.toLowerCase().includes(query)
    );
    renderDocuments();
}

// Apply document filters
function applyDocumentFilters() {
    const typeFilter = document.querySelector('#documentTypeFilter')?.value;
    const statusFilter = document.querySelector('#statusFilter')?.value;

    filteredDocuments = documentsData.filter(doc => {
        let matches = true;

        if (typeFilter && typeFilter !== 'all') {
            matches = matches && doc.type === typeFilter;
        }

        if (statusFilter && statusFilter !== 'all') {
            matches = matches && doc.status === statusFilter;
        }

        return matches;
    });

    renderDocuments();
}

// Render documents table
function renderDocuments() {
    const tbody = document.querySelector('#documentsTableBody');
    if (!tbody) return;

    if (filteredDocuments.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>No documents found</h3>
                    <p>Try adjusting your filters or search terms</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = filteredDocuments.map(doc => {
        const isExpiringSoon = doc.expiryDate && new Date(doc.expiryDate) <= new Date(Date.now() + 30 * 24 * 60 * 60 * 1000);
        const isExpired = doc.expiryDate && new Date(doc.expiryDate) <= new Date();

        return `
            <tr data-document-id="${doc.id}">
                <td>
                    <div class="document-info">
                        <div class="document-title">${doc.title}</div>
                        <div class="document-subtitle">${doc.id}</div>
                    </div>
                </td>
                <td>
                    <span class="document-type ${doc.type}">${doc.type}</span>
                </td>
                <td>
                    <span class="document-status ${doc.status}">${doc.status}</span>
                </td>
                <td>${formatDate(doc.createdDate)}</td>
                <td>
                    ${doc.expiryDate ? `
                        <span class="expiry-date ${isExpired ? 'danger' : isExpiringSoon ? 'warning' : ''}">
                            ${formatDate(doc.expiryDate)}
                        </span>
                    ` : '<span class="text-muted">N/A</span>'}
                </td>
                <td>${doc.responsibleParty}</td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view-doc-btn" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn edit-doc-btn" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete-doc-btn danger" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Update compliance progress
function updateComplianceProgress() {
    const totalItems = checklistData.reduce((sum, category) => sum + category.items.length, 0);
    const completedItems = checklistData.reduce((sum, category) => 
        sum + category.items.filter(item => item.completed).length, 0);
    
    const progress = totalItems > 0 ? Math.round((completedItems / totalItems) * 100) : 100;
    
    const completionText = document.querySelector('.completion-text');
    const completionFill = document.querySelector('.completion-fill');
    
    if (completionText) completionText.textContent = `${progress}% Complete`;
    if (completionFill) completionFill.style.width = `${progress}%`;
}

// Update checklist item
function updateChecklistItem(itemId, isCompleted) {
    checklistData.forEach(category => {
        const item = category.items.find(item => item.id === itemId);
        if (item) {
            item.completed = isCompleted;
        }
    });
    
    updateComplianceProgress();
    updateStats();
    showNotification(`Checklist item ${isCompleted ? 'completed' : 'unchecked'}`, 'success');
}

// Format date
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Show add document modal
function showAddDocumentModal() {
    const modalHTML = `
        <div class="modal-backdrop">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Add Legal Document</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <form id="addDocumentForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="docTitle">Document Title *</label>
                            <input type="text" id="docTitle" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="docType">Document Type *</label>
                            <select id="docType" name="type" required>
                                <option value="">Select type</option>
                                <option value="contract">Contract</option>
                                <option value="policy">Policy</option>
                                <option value="agreement">Agreement</option>
                                <option value="license">License</option>
                                <option value="insurance">Insurance</option>
                                <option value="permit">Permit</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="docDescription">Description</label>
                        <textarea id="docDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="docResponsible">Responsible Party *</label>
                            <input type="text" id="docResponsible" name="responsibleParty" required>
                        </div>
                        <div class="form-group">
                            <label for="docExpiry">Expiry Date</label>
                            <input type="date" id="docExpiry" name="expiryDate">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Document</button>
                    </div>
                </form>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// Show add policy modal
function showAddPolicyModal() {
    const modalHTML = `
        <div class="modal-backdrop">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Add Policy</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <form id="addPolicyForm">
                    <div class="form-group">
                        <label for="policyTitle">Policy Title *</label>
                        <input type="text" id="policyTitle" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="policyDescription">Description *</label>
                        <textarea id="policyDescription" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="policyCategory">Category *</label>
                            <select id="policyCategory" name="category" required>
                                <option value="">Select category</option>
                                <option value="HR">HR</option>
                                <option value="Operations">Operations</option>
                                <option value="Privacy">Privacy</option>
                                <option value="Finance">Finance</option>
                                <option value="Legal">Legal</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="policyVersion">Version</label>
                            <input type="text" id="policyVersion" name="version" value="1.0">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Policy</button>
                    </div>
                </form>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// Handle add document
function handleAddDocument(form) {
    const formData = new FormData(form);
    const newDoc = {
        id: `DOC${String(documentsData.length + 1).padStart(3, '0')}`,
        title: formData.get('title'),
        type: formData.get('type'),
        status: 'draft',
        createdDate: new Date().toISOString().split('T')[0],
        expiryDate: formData.get('expiryDate') || null,
        responsibleParty: formData.get('responsibleParty'),
        description: formData.get('description') || '',
        fileUrl: '#'
    };

    documentsData.push(newDoc);
    filteredDocuments = [...documentsData];
    renderDocuments();
    updateStats();
    closeModals();
    showNotification('Document added successfully', 'success');
}

// Handle add policy
function handleAddPolicy(form) {
    const formData = new FormData(form);
    const newPolicy = {
        id: `POL${String(policiesData.length + 1).padStart(3, '0')}`,
        title: formData.get('title'),
        description: formData.get('description'),
        lastUpdated: new Date().toISOString().split('T')[0],
        version: formData.get('version') || '1.0',
        status: 'draft',
        category: formData.get('category'),
        icon: 'fas fa-file-alt'
    };

    policiesData.push(newPolicy);
    loadPolicies();
    updateStats();
    closeModals();
    showNotification('Policy added successfully', 'success');
}

// View document
function viewDocument(docId) {
    const doc = documentsData.find(d => d.id === docId);
    if (doc) {
        showNotification(`Opening ${doc.title}...`, 'info');
        // In a real application, this would open the document viewer
    }
}

// Edit document
function editDocument(docId) {
    const doc = documentsData.find(d => d.id === docId);
    if (doc) {
        showNotification(`Edit functionality for ${doc.title} would open here`, 'info');
        // In a real application, this would open the edit modal
    }
}

// Delete document
function deleteDocument(docId) {
    const doc = documentsData.find(d => d.id === docId);
    if (doc && confirm(`Are you sure you want to delete "${doc.title}"?`)) {
        documentsData = documentsData.filter(d => d.id !== docId);
        filteredDocuments = filteredDocuments.filter(d => d.id !== docId);
        renderDocuments();
        updateStats();
        showNotification('Document deleted successfully', 'success');
    }
}

// View policy
function viewPolicy(policyId) {
    const policy = policiesData.find(p => p.id === policyId);
    if (policy) {
        showNotification(`Opening ${policy.title}...`, 'info');
    }
}

// Edit policy
function editPolicy(policyId) {
    const policy = policiesData.find(p => p.id === policyId);
    if (policy) {
        showNotification(`Edit functionality for ${policy.title} would open here`, 'info');
    }
}

// Edit checklist item
function editChecklistItem(itemId) {
    showNotification(`Edit functionality for checklist item would open here`, 'info');
}

// Handle alert action
function handleAlertAction(alertId, action) {
    showNotification(`Action "${action}" executed for alert ${alertId}`, 'success');
}

// Dismiss alert
function dismissAlert(alertId) {
    const alertElement = document.querySelector(`[data-alert-id="${alertId}"]`);
    if (alertElement) {
        alertElement.remove();
        showNotification('Alert dismissed', 'info');
    }
}

// Refresh alerts
function refreshAlerts() {
    loadComplianceAlerts();
    showNotification('Alerts refreshed', 'success');
}

// Export audit log
function exportAuditLog() {
    showNotification('Exporting audit log...', 'info');
    // In a real application, this would generate and download the audit log
}

// Close all modals
function closeModals() {
    const modals = document.querySelectorAll('.modal-backdrop');
    modals.forEach(modal => modal.remove());
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'times-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
        <span>${message}</span>
        <button class="notification-close">&times;</button>
    `;

    document.body.appendChild(notification);

    // Auto-hide after 5 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 5000);

    // Manual close
    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    });
}
