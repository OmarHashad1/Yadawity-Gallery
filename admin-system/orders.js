let currentPage = 1;
let currentStatus = '';
let currentDate = '';

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

    // Set default date to today
    document.getElementById('orderDate').value = new Date().toISOString().split('T')[0];

    // Load orders
    loadOrders();
});

async function loadOrders(page = 1, status = '', date = '') {
    try {
        let url = `/admin-system/API/orders.php?page=${page}&limit=20`;
        if (status) {
            url += `&status=${encodeURIComponent(status)}`;
        }
        if (date) {
            url += `&order_date=${encodeURIComponent(date)}`;
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
            displayOrders(data.data);
            updatePagination(data.meta);
        } else {
            console.error('Failed to load orders:', data.error);
        }
    } catch (error) {
        console.error('Error loading orders:', error);
    }
}

function displayOrders(orders) {
    const tbody = document.getElementById('ordersTableBody');
    
    if (!orders || orders.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No orders found</td></tr>';
        return;
    }

    let html = '';
    orders.forEach(order => {
        const statusClass = getStatusClass(order.status);
        
        html += `
            <tr>
                <td>${order.id}</td>
                <td>${order.order_number}</td>
                <td>${order.buyer_name}</td>
                <td>$${(order.total_amount || 0).toLocaleString()}</td>
                <td><span class="badge ${statusClass}">${order.status}</span></td>
                <td>${new Date(order.order_date).toLocaleDateString()}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editOrder(${order.id})">
                        Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteOrder(${order.id})">
                        Delete
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function getStatusClass(status) {
    const statusClasses = {
        'pending': 'bg-warning',
        'confirmed': 'bg-info',
        'shipped': 'bg-primary',
        'delivered': 'bg-success',
        'cancelled': 'bg-danger'
    };
    return statusClasses[status] || 'bg-secondary';
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
    loadOrders(currentPage, currentStatus, currentDate);
}

function filterOrders() {
    currentStatus = document.getElementById('statusFilter').value;
    currentDate = document.getElementById('dateFilter').value;
    currentPage = 1;
    loadOrders(currentPage, currentStatus, currentDate);
}

function openAddOrderModal() {
    document.getElementById('orderModalTitle').textContent = 'Add New Order';
    document.getElementById('orderForm').reset();
    document.getElementById('orderId').value = '';
    document.getElementById('orderDate').value = new Date().toISOString().split('T')[0];
}

async function editOrder(orderId) {
    try {
        const response = await fetch(`/admin-system/API/orders.php?id=${orderId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            const order = data.data;
            
            document.getElementById('orderModalTitle').textContent = 'Edit Order';
            document.getElementById('orderId').value = order.id;
            document.getElementById('orderNumber').value = order.order_number;
            document.getElementById('buyerId').value = order.buyer_id;
            document.getElementById('buyerName').value = order.buyer_name;
            document.getElementById('totalAmount').value = order.total_amount;
            document.getElementById('status').value = order.status;
            document.getElementById('orderDate').value = order.order_date;
            document.getElementById('shippingAddress').value = order.shipping_address || '';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('orderModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Error loading order details:', error);
    }
}

async function saveOrder() {
    const orderId = document.getElementById('orderId').value;
    const isEdit = orderId !== '';
    
    const orderData = {
        order_number: document.getElementById('orderNumber').value,
        buyer_id: parseInt(document.getElementById('buyerId').value),
        buyer_name: document.getElementById('buyerName').value,
        total_amount: parseFloat(document.getElementById('totalAmount').value),
        status: document.getElementById('status').value,
        order_date: document.getElementById('orderDate').value,
        shipping_address: document.getElementById('shippingAddress').value
    };

    try {
        const url = isEdit ? `/admin-system/API/orders.php?id=${orderId}` : '/admin-system/API/orders.php';
        const method = isEdit ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': localStorage.getItem('csrf_token')
            },
            body: JSON.stringify(orderData)
        });

        if (response.status === 401 || response.status === 403) {
            localStorage.clear();
            window.location.href = 'login.php';
            return;
        }

        const data = await response.json();
        
        if (response.ok) {
            // Close modal and reload orders
            const modal = bootstrap.Modal.getInstance(document.getElementById('orderModal'));
            modal.hide();
            loadOrders(currentPage, currentStatus, currentDate);
        } else {
            alert('Error: ' + (data.error || 'Failed to save order'));
        }
    } catch (error) {
        console.error('Error saving order:', error);
        alert('Error saving order. Please try again.');
    }
}

async function deleteOrder(orderId) {
    if (!confirm('Are you sure you want to delete this order?')) {
        return;
    }

    try {
        const response = await fetch(`/admin-system/API/orders.php?id=${orderId}`, {
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
            loadOrders(currentPage, currentStatus, currentDate);
        } else {
            alert('Error: ' + (data.error || 'Failed to delete order'));
        }
    } catch (error) {
        console.error('Error deleting order:', error);
        alert('Error deleting order. Please try again.');
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
