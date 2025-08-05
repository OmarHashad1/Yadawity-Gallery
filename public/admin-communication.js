// Admin Communication JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeAdminCommunication();
});

function initializeAdminCommunication() {
    initializeSidebar();
    loadSampleTickets();
    loadSampleMessages();
    initializeFilters();
    initializeModals();
    initializeEventListeners();
}

// Sample data
const sampleTickets = [
    {
        id: 'TKT-2025-001',
        customer: 'Sarah Ahmed',
        email: 'sarah.ahmed@email.com',
        subject: 'Payment issue with artwork purchase',
        category: 'payment',
        priority: 'high',
        status: 'open',
        created: '2025-01-24 10:30',
        description: 'Unable to complete payment for Abstract Harmony artwork. Credit card keeps getting declined.'
    },
    {
        id: 'TKT-2025-002',
        customer: 'Mohamed Hassan',
        email: 'm.hassan@email.com',
        subject: 'Shipping delay inquiry',
        category: 'shipping',
        priority: 'medium',
        status: 'pending',
        created: '2025-01-24 09:15',
        description: 'Order placed 5 days ago but no shipping updates received yet.'
    },
    {
        id: 'TKT-2025-003',
        customer: 'Layla Mahmoud',
        email: 'layla.m@email.com',
        subject: 'Artist verification request',
        category: 'account',
        priority: 'medium',
        status: 'resolved',
        created: '2025-01-23 16:45',
        description: 'Requesting artist account verification to start selling artwork.'
    },
    {
        id: 'TKT-2025-004',
        customer: 'Ahmed Farouk',
        email: 'a.farouk@email.com',
        subject: 'Website loading issues',
        category: 'technical',
        priority: 'low',
        status: 'closed',
        created: '2025-01-23 14:20',
        description: 'Gallery page takes too long to load on mobile device.'
    },
    {
        id: 'TKT-2025-005',
        customer: 'Nadia Rostom',
        email: 'nadia.rostom@email.com',
        subject: 'Commission artwork inquiry',
        category: 'artwork',
        priority: 'medium',
        status: 'open',
        created: '2025-01-24 11:45',
        description: 'Interested in commissioning a custom portrait. Need pricing information.'
    }
];

const sampleMessages = [
    {
        sender: 'Customer Support',
        content: 'New ticket assigned: Payment issue with order #12847',
        time: '5 minutes ago',
        avatar: 'CS'
    },
    {
        sender: 'Marina Kovač',
        content: 'Thank you for approving my artist verification!',
        time: '1 hour ago',
        avatar: 'MK'
    },
    {
        sender: 'System Alert',
        content: 'High volume of support tickets detected - consider additional staffing',
        time: '2 hours ago',
        avatar: 'SA'
    },
    {
        sender: 'Omar Farouk',
        content: 'Question about auction commission rates',
        time: '3 hours ago',
        avatar: 'OF'
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

// Load sample tickets
function loadSampleTickets() {
    const tableBody = document.getElementById('ticketsTableBody');
    if (!tableBody) return;

    tableBody.innerHTML = '';

    sampleTickets.forEach(ticket => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${ticket.id}</strong></td>
            <td>
                <div>${ticket.customer}</div>
                <small style="color: var(--text-light);">${ticket.email}</small>
            </td>
            <td>${ticket.subject}</td>
            <td><span class="badge category-${ticket.category}">${formatCategory(ticket.category)}</span></td>
            <td><span class="priorityBadge priority-${ticket.priority}">${formatPriority(ticket.priority)}</span></td>
            <td><span class="statusBadge status-${ticket.status}">${formatStatus(ticket.status)}</span></td>
            <td>${formatDate(ticket.created)}</td>
            <td>
                <button class="actionBtn btn-view" onclick="viewTicket('${ticket.id}')">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="actionBtn btn-reply" onclick="replyTicket('${ticket.id}')">
                    <i class="fas fa-reply"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// Load sample messages
function loadSampleMessages() {
    const messagesList = document.getElementById('messagesList');
    if (!messagesList) return;

    messagesList.innerHTML = '';

    sampleMessages.forEach(message => {
        const messageElement = document.createElement('div');
        messageElement.className = 'messageItem';
        messageElement.innerHTML = `
            <div class="messageAvatar">${message.avatar}</div>
            <div class="messageContent">
                <div class="messageHeader">
                    <span class="messageSender">${message.sender}</span>
                    <span class="messageTime">${message.time}</span>
                </div>
                <div class="messageText">${message.content}</div>
            </div>
        `;
        messagesList.appendChild(messageElement);
    });
}

// Initialize filters
function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const searchInput = document.getElementById('ticketSearch');

    [statusFilter, priorityFilter, categoryFilter].forEach(filter => {
        if (filter) {
            filter.addEventListener('change', applyFilters);
        }
    });

    if (searchInput) {
        searchInput.addEventListener('input', debounce(applyFilters, 300));
    }
}

// Apply filters to tickets table
function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value;
    const priorityFilter = document.getElementById('priorityFilter').value;
    const categoryFilter = document.getElementById('categoryFilter').value;
    const searchTerm = document.getElementById('ticketSearch').value.toLowerCase();

    const filteredTickets = sampleTickets.filter(ticket => {
        const matchesStatus = !statusFilter || ticket.status === statusFilter;
        const matchesPriority = !priorityFilter || ticket.priority === priorityFilter;
        const matchesCategory = !categoryFilter || ticket.category === categoryFilter;
        const matchesSearch = !searchTerm || 
            ticket.subject.toLowerCase().includes(searchTerm) ||
            ticket.customer.toLowerCase().includes(searchTerm) ||
            ticket.id.toLowerCase().includes(searchTerm);

        return matchesStatus && matchesPriority && matchesCategory && matchesSearch;
    });

    updateTicketsTable(filteredTickets);
}

// Update tickets table with filtered data
function updateTicketsTable(tickets) {
    const tableBody = document.getElementById('ticketsTableBody');
    if (!tableBody) return;

    tableBody.innerHTML = '';

    if (tickets.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-light);">
                <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                No tickets found matching your criteria
            </td>
        `;
        tableBody.appendChild(row);
        return;
    }

    tickets.forEach(ticket => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${ticket.id}</strong></td>
            <td>
                <div>${ticket.customer}</div>
                <small style="color: var(--text-light);">${ticket.email}</small>
            </td>
            <td>${ticket.subject}</td>
            <td><span class="badge category-${ticket.category}">${formatCategory(ticket.category)}</span></td>
            <td><span class="priorityBadge priority-${ticket.priority}">${formatPriority(ticket.priority)}</span></td>
            <td><span class="statusBadge status-${ticket.status}">${formatStatus(ticket.status)}</span></td>
            <td>${formatDate(ticket.created)}</td>
            <td>
                <button class="actionBtn btn-view" onclick="viewTicket('${ticket.id}')">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="actionBtn btn-reply" onclick="replyTicket('${ticket.id}')">
                    <i class="fas fa-reply"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// Initialize modals
function initializeModals() {
    const modals = document.querySelectorAll('.modal');
    const closeButtons = document.querySelectorAll('.modalClose');

    // Close modal functionality
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = button.closest('.modal');
            closeModal(modal);
        });
    });

    // Close modal when clicking backdrop
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal(modal);
            }
        });
    });

    // Escape key to close modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            modals.forEach(modal => {
                if (modal.classList.contains('active')) {
                    closeModal(modal);
                }
            });
        }
    });
}

// Initialize event listeners
function initializeEventListeners() {
    // New announcement button
    const newAnnouncementBtn = document.getElementById('newAnnouncementBtn');
    if (newAnnouncementBtn) {
        newAnnouncementBtn.addEventListener('click', function() {
            showModal('announcementModal');
        });
    }

    // Refresh button
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            refreshData();
        });
    }

    // Export button
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            exportTickets();
        });
    }

    // Compose message button
    const composeBtn = document.getElementById('composeBtn');
    if (composeBtn) {
        composeBtn.addEventListener('click', function() {
            showNotification('Compose message feature will be implemented soon', 'info');
        });
    }

    // Announcement form
    const announcementForm = document.getElementById('announcementForm');
    if (announcementForm) {
        announcementForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendAnnouncement();
        });
    }

    // Cancel announcement
    const cancelAnnouncement = document.getElementById('cancelAnnouncement');
    if (cancelAnnouncement) {
        cancelAnnouncement.addEventListener('click', function() {
            closeModal(document.getElementById('announcementModal'));
        });
    }
}

// Ticket actions
function viewTicket(ticketId) {
    const ticket = sampleTickets.find(t => t.id === ticketId);
    if (!ticket) return;

    const modalBody = document.getElementById('ticketModalBody');
    modalBody.innerHTML = `
        <div class="ticketDetails">
            <div class="ticketHeader">
                <h3>${ticket.subject}</h3>
                <div class="ticketMeta">
                    <span class="statusBadge status-${ticket.status}">${formatStatus(ticket.status)}</span>
                    <span class="priorityBadge priority-${ticket.priority}">${formatPriority(ticket.priority)}</span>
                </div>
            </div>
            
            <div class="ticketInfo">
                <div class="infoRow">
                    <strong>Ticket ID:</strong> ${ticket.id}
                </div>
                <div class="infoRow">
                    <strong>Customer:</strong> ${ticket.customer} (${ticket.email})
                </div>
                <div class="infoRow">
                    <strong>Category:</strong> ${formatCategory(ticket.category)}
                </div>
                <div class="infoRow">
                    <strong>Created:</strong> ${formatDate(ticket.created)}
                </div>
            </div>
            
            <div class="ticketDescription">
                <h4>Description:</h4>
                <p>${ticket.description}</p>
            </div>
            
            <div class="ticketActions">
                <button class="btn btn-primary" onclick="replyTicket('${ticket.id}')">
                    <i class="fas fa-reply"></i>
                    Reply
                </button>
                <button class="btn btn-secondary" onclick="updateTicketStatus('${ticket.id}', 'resolved')">
                    <i class="fas fa-check"></i>
                    Mark Resolved
                </button>
            </div>
        </div>
    `;

    showModal('ticketModal');
}

function replyTicket(ticketId) {
    showNotification(`Reply feature for ticket ${ticketId} will be implemented soon`, 'info');
    closeModal(document.getElementById('ticketModal'));
}

function updateTicketStatus(ticketId, newStatus) {
    const ticket = sampleTickets.find(t => t.id === ticketId);
    if (ticket) {
        ticket.status = newStatus;
        loadSampleTickets();
        showNotification(`Ticket ${ticketId} status updated to ${formatStatus(newStatus)}`, 'success');
        closeModal(document.getElementById('ticketModal'));
    }
}

// Send announcement
function sendAnnouncement() {
    const title = document.getElementById('announcementTitle').value;
    const content = document.getElementById('announcementContent').value;
    const target = document.getElementById('announcementTarget').value;

    if (!title || !content) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }

    // Simulate sending announcement
    setTimeout(() => {
        showNotification(`Announcement "${title}" sent to ${target === 'all' ? 'all users' : target}`, 'success');
        closeModal(document.getElementById('announcementModal'));
        document.getElementById('announcementForm').reset();
    }, 1000);
}

// Refresh data
function refreshData() {
    showNotification('Refreshing data...', 'info');
    
    // Simulate refresh
    setTimeout(() => {
        loadSampleTickets();
        loadSampleMessages();
        showNotification('Data refreshed successfully', 'success');
    }, 1500);
}

// Export tickets
function exportTickets() {
    showNotification('Exporting tickets data...', 'info');
    
    // Simulate export
    setTimeout(() => {
        showNotification('Tickets exported successfully', 'success');
    }, 2000);
}

// Utility functions
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

function formatCategory(category) {
    const categories = {
        payment: 'Payment',
        artwork: 'Artwork',
        shipping: 'Shipping',
        account: 'Account',
        technical: 'Technical'
    };
    return categories[category] || category;
}

function formatPriority(priority) {
    return priority.charAt(0).toUpperCase() + priority.slice(1);
}

function formatStatus(status) {
    const statuses = {
        open: 'Open',
        pending: 'Pending',
        resolved: 'Resolved',
        closed: 'Closed'
    };
    return statuses[status] || status;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
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

// Add notification styles
const notificationStyles = `
    .notification {
        position: fixed;
        right: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        padding: 15px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-width: 300px;
        max-width: 400px;
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        border-left: 4px solid var(--primary-brown);
    }

    .notification.show {
        transform: translateX(0);
    }

    .notification-success {
        border-left-color: var(--success-green);
    }

    .notification-error {
        border-left-color: var(--danger-red);
    }

    .notification-warning {
        border-left-color: var(--warning-orange);
    }

    .notification-info {
        border-left-color: var(--blue-accent);
    }

    .notification-content {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
    }

    .notification-close {
        background: none;
        border: none;
        font-size: 1rem;
        color: var(--text-light);
        cursor: pointer;
        padding: 5px;
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    .notification-close:hover {
        background: rgba(0, 0, 0, 0.1);
    }
`;

// Add notification styles to head
const styleSheet = document.createElement('style');
styleSheet.textContent = notificationStyles;
document.head.appendChild(styleSheet);
