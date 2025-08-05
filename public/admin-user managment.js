// User Management System
class UserManager {
    constructor() {
        this.users = [...usersData];
        this.filteredUsers = [...this.users];
        this.currentPage = 1;
        this.recordsPerPage = 10;
        this.currentEditUserId = null;
        this.currentDeleteUserId = null;
        
        this.initializeEventListeners();
        this.renderUsers();
        this.updateStats();
    }

    initializeEventListeners() {
        // Modal controls
        document.getElementById('addUserBtn').addEventListener('click', () => this.openUserModal());
        document.getElementById('closeModal').addEventListener('click', () => this.closeUserModal());
        document.getElementById('cancelBtn').addEventListener('click', () => this.closeUserModal());
        document.getElementById('closeDeleteModal').addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('cancelDelete').addEventListener('click', () => this.closeDeleteModal());
        
        // Form submission
        document.getElementById('userForm').addEventListener('submit', (e) => this.handleFormSubmit(e));
        document.getElementById('confirmDelete').addEventListener('click', () => this.deleteUser());
        
        // Search and filter
        document.getElementById('searchInput').addEventListener('input', () => this.handleSearch());
        document.getElementById('searchBtn').addEventListener('click', () => this.handleSearch());
        document.getElementById('statusFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('roleFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('sortBy').addEventListener('change', () => this.applySorting());
        
        // Pagination
        document.getElementById('recordsPerPage').addEventListener('change', () => this.changeRecordsPerPage());
        document.getElementById('prevPage').addEventListener('click', () => this.previousPage());
        document.getElementById('nextPage').addEventListener('click', () => this.nextPage());
        
        // Select all checkbox
        document.getElementById('selectAll').addEventListener('change', (e) => this.selectAllUsers(e));
        
        // Other controls
        document.getElementById('refreshBtn').addEventListener('click', () => this.refreshData());
        document.getElementById('exportBtn').addEventListener('click', () => this.exportUsers());
        
        // Close modal when clicking outside
        document.getElementById('userModal').addEventListener('click', (e) => {
            if (e.target.id === 'userModal') this.closeUserModal();
        });
        document.getElementById('deleteModal').addEventListener('click', (e) => {
            if (e.target.id === 'deleteModal') this.closeDeleteModal();
        });
    }

    renderUsers() {
        const tbody = document.getElementById('usersTableBody');
        const startIndex = (this.currentPage - 1) * this.recordsPerPage;
        const endIndex = startIndex + this.recordsPerPage;
        const usersToShow = this.filteredUsers.slice(startIndex, endIndex);
        
        tbody.innerHTML = '';
        
        usersToShow.forEach(user => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <input type="checkbox" class="userSelect" data-user-id="${user.id}">
                </td>
                <td>
                    <div class="userInfo">
                        <div class="userAvatar">
                            ${user.firstName.charAt(0)}${user.lastName.charAt(0)}
                        </div>
                        <div class="userDetails">
                            <h4>${user.firstName} ${user.lastName}</h4>
                            <p>#${user.id}</p>
                        </div>
                    </div>
                </td>
                <td>${user.email}</td>
                <td>
                    <span class="roleBadge role-${user.role}">
                        ${user.role.toUpperCase()}
                    </span>
                </td>
                <td>
                    <span class="statusBadge status-${user.status}">
                        ${user.status.toUpperCase()}
                    </span>
                </td>
                <td>${user.lastLogin}</td>
                <td>${this.formatDate(user.created)}</td>
                <td>
                    <button class="actionBtn btn-view" onclick="userManager.viewUser(${user.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="actionBtn btn-edit" onclick="userManager.editUser(${user.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="actionBtn btn-delete" onclick="userManager.confirmDeleteUser(${user.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
        
        this.updatePaginationInfo();
    }

    updateStats() {
        const activeUsers = this.users.filter(u => u.status === 'active').length;
        const inactiveUsers = this.users.filter(u => u.status === 'inactive').length;
        const newUsersThisMonth = this.users.filter(u => {
            const created = new Date(u.created);
            const now = new Date();
            return created.getMonth() === now.getMonth() && created.getFullYear() === now.getFullYear();
        }).length;
        
        document.getElementById('totalUsers').textContent = this.users.length;
        document.getElementById('activeUsers').textContent = activeUsers;
        document.getElementById('inactiveUsers').textContent = inactiveUsers;
        document.getElementById('newUsers').textContent = newUsersThisMonth;
    }

    openUserModal(user = null) {
        const modal = document.getElementById('userModal');
        const title = document.getElementById('modalTitle');
        const form = document.getElementById('userForm');
        
        if (user) {
            title.textContent = 'Edit User';
            this.currentEditUserId = user.id;
            this.populateForm(user);
        } else {
            title.textContent = 'Add New User';
            this.currentEditUserId = null;
            form.reset();
        }
        
        modal.classList.add('active');
    }

    closeUserModal() {
        document.getElementById('userModal').classList.remove('active');
        document.getElementById('userForm').reset();
        this.currentEditUserId = null;
    }

    populateForm(user) {
        document.getElementById('userId').value = user.id;
        document.getElementById('firstName').value = user.firstName;
        document.getElementById('lastName').value = user.lastName;
        document.getElementById('email').value = user.email;
        document.getElementById('phone').value = user.phone;
        document.getElementById('role').value = user.role;
        document.getElementById('status').value = user.status;
        document.getElementById('notes').value = user.notes;
    }

    handleFormSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const userData = {
            firstName: formData.get('firstName') || document.getElementById('firstName').value,
            lastName: formData.get('lastName') || document.getElementById('lastName').value,
            email: formData.get('email') || document.getElementById('email').value,
            phone: formData.get('phone') || document.getElementById('phone').value,
            role: formData.get('role') || document.getElementById('role').value,
            status: formData.get('status') || document.getElementById('status').value,
            notes: formData.get('notes') || document.getElementById('notes').value
        };
        
        if (this.currentEditUserId) {
            this.updateUser(this.currentEditUserId, userData);
        } else {
            this.addUser(userData);
        }
        
        this.closeUserModal();
    }

    addUser(userData) {
        const newUser = {
            id: Math.max(...this.users.map(u => u.id)) + 1,
            ...userData,
            lastLogin: 'Never',
            created: new Date().toISOString().split('T')[0]
        };
        
        this.users.unshift(newUser);
        this.applyFilters();
        this.updateStats();
        this.showNotification('User added successfully!', 'success');
    }

    updateUser(userId, userData) {
        const userIndex = this.users.findIndex(u => u.id === userId);
        if (userIndex !== -1) {
            this.users[userIndex] = { ...this.users[userIndex], ...userData };
            this.applyFilters();
            this.updateStats();
            this.showNotification('User updated successfully!', 'success');
        }
    }

    viewUser(userId) {
        const user = this.users.find(u => u.id === userId);
        if (user) {
            alert(`User Details:\n\nName: ${user.firstName} ${user.lastName}\nEmail: ${user.email}\nPhone: ${user.phone}\nRole: ${user.role}\nStatus: ${user.status}\nLast Login: ${user.lastLogin}\nCreated: ${user.created}\nNotes: ${user.notes}`);
        }
    }

    editUser(userId) {
        const user = this.users.find(u => u.id === userId);
        if (user) {
            this.openUserModal(user);
        }
    }

    confirmDeleteUser(userId) {
        this.currentDeleteUserId = userId;
        document.getElementById('deleteModal').classList.add('active');
    }

    closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
        this.currentDeleteUserId = null;
    }

    deleteUser() {
        if (this.currentDeleteUserId) {
            this.users = this.users.filter(u => u.id !== this.currentDeleteUserId);
            this.applyFilters();
            this.updateStats();
            this.closeDeleteModal();
            this.showNotification('User deleted successfully!', 'success');
        }
    }

    handleSearch() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        
        if (searchTerm === '') {
            this.filteredUsers = [...this.users];
        } else {
            this.filteredUsers = this.users.filter(user => 
                user.firstName.toLowerCase().includes(searchTerm) ||
                user.lastName.toLowerCase().includes(searchTerm) ||
                user.email.toLowerCase().includes(searchTerm) ||
                user.phone.includes(searchTerm)
            );
        }
        
        this.currentPage = 1;
        this.renderUsers();
    }

    applyFilters() {
        const statusFilter = document.getElementById('statusFilter').value;
        const roleFilter = document.getElementById('roleFilter').value;
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        
        this.filteredUsers = this.users.filter(user => {
            const matchesSearch = searchTerm === '' || 
                user.firstName.toLowerCase().includes(searchTerm) ||
                user.lastName.toLowerCase().includes(searchTerm) ||
                user.email.toLowerCase().includes(searchTerm) ||
                user.phone.includes(searchTerm);
            
            const matchesStatus = statusFilter === '' || user.status === statusFilter;
            const matchesRole = roleFilter === '' || user.role === roleFilter;
            
            return matchesSearch && matchesStatus && matchesRole;
        });
        
        this.currentPage = 1;
        this.applySorting();
    }

    applySorting() {
        const sortBy = document.getElementById('sortBy').value;
        
        this.filteredUsers.sort((a, b) => {
            switch (sortBy) {
                case 'name':
                    return `${a.firstName} ${a.lastName}`.localeCompare(`${b.firstName} ${b.lastName}`);
                case 'email':
                    return a.email.localeCompare(b.email);
                case 'created':
                    return new Date(b.created) - new Date(a.created);
                case 'lastLogin':
                    if (a.lastLogin === 'Never' && b.lastLogin === 'Never') return 0;
                    if (a.lastLogin === 'Never') return 1;
                    if (b.lastLogin === 'Never') return -1;
                    return new Date(b.lastLogin) - new Date(a.lastLogin);
                default:
                    return 0;
            }
        });
        
        this.renderUsers();
    }

    changeRecordsPerPage() {
        this.recordsPerPage = parseInt(document.getElementById('recordsPerPage').value);
        this.currentPage = 1;
        this.renderUsers();
    }

    previousPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.renderUsers();
        }
    }

    nextPage() {
        const totalPages = Math.ceil(this.filteredUsers.length / this.recordsPerPage);
        if (this.currentPage < totalPages) {
            this.currentPage++;
            this.renderUsers();
        }
    }

    updatePaginationInfo() {
        const totalUsers = this.filteredUsers.length;
        const startIndex = (this.currentPage - 1) * this.recordsPerPage;
        const endIndex = Math.min(startIndex + this.recordsPerPage, totalUsers);
        const totalPages = Math.ceil(totalUsers / this.recordsPerPage);
        
        document.getElementById('paginationInfo').textContent = 
            `Showing ${startIndex + 1}-${endIndex} of ${totalUsers} users`;
        
        document.getElementById('prevPage').disabled = this.currentPage === 1;
        document.getElementById('nextPage').disabled = this.currentPage === totalPages;
    }

    selectAllUsers(e) {
        const checkboxes = document.querySelectorAll('.userSelect');
        checkboxes.forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
    }

    refreshData() {
        this.users = [...usersData];
        this.applyFilters();
        this.updateStats();
        this.showNotification('Data refreshed successfully!', 'info');
    }

    exportUsers() {
        const selectedUsers = this.getSelectedUsers();
        const usersToExport = selectedUsers.length > 0 ? selectedUsers : this.filteredUsers;
        
        const csvContent = this.convertToCSV(usersToExport);
        this.downloadCSV(csvContent, 'users_export.csv');
        this.showNotification(`Exported ${usersToExport.length} users successfully!`, 'success');
    }

    getSelectedUsers() {
        const selectedIds = Array.from(document.querySelectorAll('.userSelect:checked'))
            .map(checkbox => parseInt(checkbox.dataset.userId));
        return this.users.filter(user => selectedIds.includes(user.id));
    }

    convertToCSV(users) {
        const headers = ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Role', 'Status', 'Last Login', 'Created', 'Notes'];
        const csvRows = [headers.join(',')];
        
        users.forEach(user => {
            const row = [
                user.id,
                `"${user.firstName}"`,
                `"${user.lastName}"`,
                `"${user.email}"`,
                `"${user.phone}"`,
                user.role,
                user.status,
                user.lastLogin,
                user.created,
                `"${user.notes.replace(/"/g, '""')}"`
            ];
            csvRows.push(row.join(','));
        });
        
        return csvRows.join('\n');
    }

    downloadCSV(csvContent, filename) {
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            background: ${type === 'success' ? 'var(--success-green)' : type === 'error' ? 'var(--danger-red)' : 'var(--blue-accent)'};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            z-index: 3000;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        `;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
}

// Initialize the user manager when the page loads
let userManager;
document.addEventListener('DOMContentLoaded', () => {
    userManager = new UserManager();
});

// Handle mobile sidebar toggle
document.addEventListener('DOMContentLoaded', () => {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    
    sidebarToggle?.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 1024 && 
            !sidebar.contains(e.target) && 
            !e.target.matches('.sidebarToggle')) {
            sidebar.classList.remove('active');
        }
    });
});




// Sample user data
const usersData = [
    {
        id: 1,
        firstName: "John",
        lastName: "Smith",
        email: "john.smith@email.com",
        phone: "+1 (555) 123-4567",
        role: "customer",
        status: "active",
        lastLogin: "2024-01-15",
        created: "2023-06-15",
        notes: "Regular customer, prefers window seating"
    },
    {
        id: 2,
        firstName: "Sarah",
        lastName: "Johnson",
        email: "sarah.johnson@email.com",
        phone: "+1 (555) 987-6543",
        role: "vip",
        status: "active",
        lastLogin: "2024-01-14",
        created: "2022-03-20",
        notes: "VIP member, anniversary dinner monthly"
    },
    {
        id: 3,
        firstName: "Michael",
        lastName: "Chen",
        email: "michael.chen@email.com",
        phone: "+1 (555) 456-7890",
        role: "customer",
        status: "active",
        lastLogin: "2024-01-13",
        created: "2023-11-08",
        notes: "Food allergies: nuts, shellfish"
    },
    {
        id: 4,
        firstName: "Emily",
        lastName: "Davis",
        email: "emily.davis@email.com",
        phone: "+1 (555) 321-0987",
        role: "admin",
        status: "active",
        lastLogin: "2024-01-15",
        created: "2021-05-12",
        notes: "System administrator"
    },
    {
        id: 5,
        firstName: "Robert",
        lastName: "Wilson",
        email: "robert.wilson@email.com",
        phone: "+1 (555) 654-3210",
        role: "customer",
        status: "inactive",
        lastLogin: "2023-12-01",
        created: "2023-02-18",
        notes: "Moved to different city"
    },
    {
        id: 6,
        firstName: "Lisa",
        lastName: "Anderson",
        email: "lisa.anderson@email.com",
        phone: "+1 (555) 789-0123",
        role: "vip",
        status: "active",
        lastLogin: "2024-01-12",
        created: "2022-08-25",
        notes: "Corporate events coordinator"
    },
    {
        id: 7,
        firstName: "David",
        lastName: "Martinez",
        email: "david.martinez@email.com",
        phone: "+1 (555) 234-5678",
        role: "customer",
        status: "pending",
        lastLogin: "Never",
        created: "2024-01-10",
        notes: "New registration, pending verification"
    },
    {
        id: 8,
        firstName: "Jennifer",
        lastName: "Taylor",
        email: "jennifer.taylor@email.com",
        phone: "+1 (555) 876-5432",
        role: "customer",
        status: "active",
        lastLogin: "2024-01-11",
        created: "2023-09-03",
        notes: "Birthday party regular"
    },
    {
        id: 9,
        firstName: "Christopher",
        lastName: "Brown",
        email: "christopher.brown@email.com",
        phone: "+1 (555) 345-6789",
        role: "customer",
        status: "active",
        lastLogin: "2024-01-14",
        created: "2023-07-22",
        notes: "Business lunches weekly"
    },
    {
        id: 10,
        firstName: "Amanda",
        lastName: "Miller",
        email: "amanda.miller@email.com",
        phone: "+1 (555) 567-8901",
        role: "vip",
        status: "active",
        lastLogin: "2024-01-15",
        created: "2021-12-05",
        notes: "Wine connoisseur, special requests"
    }
];

// Additional users to reach 248 total (for demonstration)
for (let i = 11; i <= 248; i++) {
    const roles = ['customer', 'vip', 'admin'];
    const statuses = ['active', 'inactive', 'pending'];
    const firstNames = ['Alex', 'Jordan', 'Taylor', 'Morgan', 'Casey', 'Riley', 'Avery', 'Quinn', 'Sage', 'River'];
    const lastNames = ['Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez'];
    
    const firstName = firstNames[Math.floor(Math.random() * firstNames.length)];
    const lastName = lastNames[Math.floor(Math.random() * lastNames.length)];
    const role = roles[Math.floor(Math.random() * roles.length)];
    const status = statuses[Math.floor(Math.random() * statuses.length)];
    
    usersData.push({
        id: i,
        firstName: firstName,
        lastName: lastName,
        email: `${firstName.toLowerCase()}.${lastName.toLowerCase()}${i}@email.com`,
        phone: `+1 (555) ${String(Math.floor(Math.random() * 900) + 100)}-${String(Math.floor(Math.random() * 9000) + 1000)}`,
        role: role,
        status: status,
        lastLogin: status === 'active' ? `2024-01-${String(Math.floor(Math.random() * 15) + 1).padStart(2, '0')}` : 
                  status === 'pending' ? 'Never' : `2023-${String(Math.floor(Math.random() * 12) + 1).padStart(2, '0')}-${String(Math.floor(Math.random() * 28) + 1).padStart(2, '0')}`,
        created: `202${Math.floor(Math.random() * 4) + 1}-${String(Math.floor(Math.random() * 12) + 1).padStart(2, '0')}-${String(Math.floor(Math.random() * 28) + 1).padStart(2, '0')}`,
        notes: `Auto-generated user #${i}`
    });
}