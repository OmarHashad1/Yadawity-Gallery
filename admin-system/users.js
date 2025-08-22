let currentPage = 1;
let currentSearch = '';

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

    // Load users
    loadUsers();

    // Add search input event listener
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchUsers();
        }
    });
});

async function loadUsers(page = 1, search = '') {
    try {
        let url = `/admin-system/API/user.php?page=${page}&limit=20`;
        if (search) {
            url += `&q=${encodeURIComponent(search)}`;
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
            displayUsers(data.data);
            updatePagination(data.meta);
        } else {
            console.error('Failed to load users:', data.error);
        }
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

function displayUsers(users) {
    const tbody = document.getElementById('usersTableBody');
    
    if (!users || users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No users found</td></tr>';
        return;
    }

    let html = '';
    users.forEach(user => {
        const statusClass = user.is_active ? 'bg-success' : 'bg-danger';
        const statusText = user.is_active ? 'Active' : 'Inactive';
        
        html += `
            <tr>
                <td>${user.user_id}</td>
                <td>${user.first_name} ${user.last_name}</td>
                <td>${user.email}</td>
                <td><span class="badge bg-secondary">${user.user_type}</span></td>
                <td><span class="badge ${statusClass}">${statusText}</span></td>
                <td>${new Date(user.created_at).toLocaleDateString()}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editUser(${user.user_id})">
                        Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.user_id})">
                        Delete
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function updatePagination(meta) {
    const pagination = document.getElementById('pagination');
    
    if (!meta || meta.total <= meta.limit) {
        pagination.innerHTML = '';
        return;
    }

    const totalPages = Math.ceil(meta.total / meta.limit);
    let html = '';

    // Previous button
    html += `
        <li class="page-item ${meta.page <= 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${meta.page - 1})">Previous</a>
        </li>
    `;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === meta.page) {
            html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${i})">${i}</a></li>`;
        }
    }

    // Next button
    html += `
        <li class="page-item ${meta.page >= totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${meta.page + 1})">Next</a>
        </li>
    `;

    pagination.innerHTML = html;
}

function changePage(page) {
    if (page < 1) return;
    currentPage = page;
    loadUsers(currentPage, currentSearch);
}

function searchUsers() {
    currentSearch = document.getElementById('searchInput').value.trim();
    currentPage = 1;
    loadUsers(currentPage, currentSearch);
}

function openAddUserModal() {
    document.getElementById('userModalTitle').textContent = 'Add New User';
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('password').required = true;
}

async function editUser(userId) {
    try {
        const response = await fetch(`/admin-system/API/user.php?id=${userId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            const user = data.data;
            
            document.getElementById('userModalTitle').textContent = 'Edit User';
            document.getElementById('userId').value = user.user_id;
            document.getElementById('firstName').value = user.first_name;
            document.getElementById('lastName').value = user.last_name;
            document.getElementById('email').value = user.email;
            document.getElementById('userType').value = user.user_type;
            document.getElementById('phone').value = user.phone || '';
            document.getElementById('isActive').checked = user.is_active == 1;
            document.getElementById('password').required = false;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('userModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Error loading user details:', error);
    }
}

async function saveUser() {
    const userId = document.getElementById('userId').value;
    const isEdit = userId !== '';
    
    const userData = {
        first_name: document.getElementById('firstName').value,
        last_name: document.getElementById('lastName').value,
        email: document.getElementById('email').value,
        user_type: document.getElementById('userType').value,
        phone: document.getElementById('phone').value,
        is_active: document.getElementById('isActive').checked ? 1 : 0
    };

    // Add password if provided or if creating new user
    const password = document.getElementById('password').value;
    if (password || !isEdit) {
        userData.password = password;
    }

    try {
        const url = isEdit ? `/admin-system/API/user.php?id=${userId}` : '/admin-system/API/user.php';
        const method = isEdit ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': localStorage.getItem('csrf_token')
            },
            body: JSON.stringify(userData)
        });

        if (response.status === 401 || response.status === 403) {
            localStorage.clear();
            window.location.href = 'login.php';
            return;
        }

        const data = await response.json();
        
        if (response.ok) {
            // Close modal and reload users
            const modal = bootstrap.Modal.getInstance(document.getElementById('userModal'));
            modal.hide();
            loadUsers(currentPage, currentSearch);
        } else {
            alert('Error: ' + (data.error || 'Failed to save user'));
        }
    } catch (error) {
        console.error('Error saving user:', error);
        alert('Error saving user. Please try again.');
    }
}

async function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user?')) {
        return;
    }

    try {
        const response = await fetch(`/admin-system/API/user.php?id=${userId}`, {
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
            loadUsers(currentPage, currentSearch);
        } else {
            alert('Error: ' + (data.error || 'Failed to delete user'));
        }
    } catch (error) {
        console.error('Error deleting user:', error);
        alert('Error deleting user. Please try again.');
    }
}

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
