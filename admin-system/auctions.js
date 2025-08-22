let currentPage = 1;
let currentStatus = '';

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

    // Load auctions
    loadAuctions();
});

async function loadAuctions(page = 1, status = '') {
    try {
        let url = `/admin-system/API/auctions.php?page=${page}&limit=20`;
        if (status) {
            url += `&status=${encodeURIComponent(status)}`;
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
            displayAuctions(data.data);
            updatePagination(data.meta);
        } else {
            console.error('Failed to load auctions:', data.error);
        }
    } catch (error) {
        console.error('Error loading auctions:', error);
    }
}

function displayAuctions(auctions) {
    const tbody = document.getElementById('auctionsTableBody');
    
    if (!auctions || auctions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No auctions found</td></tr>';
        return;
    }

    let html = '';
    auctions.forEach(auction => {
        const statusClass = getStatusClass(auction.status);
        
        html += `
            <tr>
                <td>${auction.id}</td>
                <td>${auction.product_id}</td>
                <td>${auction.artist_id}</td>
                <td>$${(auction.starting_bid || 0).toLocaleString()}</td>
                <td>$${(auction.current_bid || 0).toLocaleString()}</td>
                <td>${new Date(auction.start_time).toLocaleString()}</td>
                <td>${new Date(auction.end_time).toLocaleString()}</td>
                <td><span class="badge ${statusClass}">${auction.status}</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editAuction(${auction.id})">
                        Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteAuction(${auction.id})">
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
        'upcoming': 'bg-secondary',
        'starting_soon': 'bg-info',
        'active': 'bg-success',
        'sold': 'bg-primary',
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
    loadAuctions(currentPage, currentStatus);
}

function filterAuctions() {
    currentStatus = document.getElementById('statusFilter').value;
    currentPage = 1;
    loadAuctions(currentPage, currentStatus);
}

function openAddAuctionModal() {
    document.getElementById('auctionModalTitle').textContent = 'Add New Auction';
    document.getElementById('auctionForm').reset();
    document.getElementById('auctionId').value = '';
    document.getElementById('currentBid').value = '0.00';
}

async function editAuction(auctionId) {
    try {
        const response = await fetch(`/admin-system/API/auctions.php?id=${auctionId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            const auction = data.data;
            
            document.getElementById('auctionModalTitle').textContent = 'Edit Auction';
            document.getElementById('auctionId').value = auction.id;
            document.getElementById('productId').value = auction.product_id;
            document.getElementById('artistId').value = auction.artist_id;
            document.getElementById('startingBid').value = auction.starting_bid;
            document.getElementById('currentBid').value = auction.current_bid || 0;
            document.getElementById('startTime').value = auction.start_time.replace(' ', 'T');
            document.getElementById('endTime').value = auction.end_time.replace(' ', 'T');
            document.getElementById('status').value = auction.status;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('auctionModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Error loading auction details:', error);
    }
}

async function saveAuction() {
    const auctionId = document.getElementById('auctionId').value;
    const isEdit = auctionId !== '';
    
    const auctionData = {
        product_id: parseInt(document.getElementById('productId').value),
        artist_id: parseInt(document.getElementById('artistId').value),
        starting_bid: parseFloat(document.getElementById('startingBid').value),
        current_bid: parseFloat(document.getElementById('currentBid').value),
        start_time: document.getElementById('startTime').value,
        end_time: document.getElementById('endTime').value,
        status: document.getElementById('status').value
    };

    try {
        const url = isEdit ? `/admin-system/API/auctions.php?id=${auctionId}` : '/admin-system/API/auctions.php';
        const method = isEdit ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': localStorage.getItem('csrf_token')
            },
            body: JSON.stringify(auctionData)
        });

        if (response.status === 401 || response.status === 403) {
            localStorage.clear();
            window.location.href = 'login.php';
            return;
        }

        const data = await response.json();
        
        if (response.ok) {
            // Close modal and reload auctions
            const modal = bootstrap.Modal.getInstance(document.getElementById('auctionModal'));
            modal.hide();
            loadAuctions(currentPage, currentStatus);
        } else {
            alert('Error: ' + (data.error || 'Failed to save auction'));
        }
    } catch (error) {
        console.error('Error saving auction:', error);
        alert('Error saving auction. Please try again.');
    }
}

async function deleteAuction(auctionId) {
    if (!confirm('Are you sure you want to delete this auction?')) {
        return;
    }

    try {
        const response = await fetch(`/admin-system/API/auctions.php?id=${auctionId}`, {
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
            loadAuctions(currentPage, currentStatus);
        } else {
            alert('Error: ' + (data.error || 'Failed to delete auction'));
        }
    } catch (error) {
        console.error('Error deleting auction:', error);
        alert('Error deleting auction. Please try again.');
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
