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

    // Load galleries
    loadGalleries();
});

async function loadGalleries(page = 1, status = '') {
    try {
        let url = `/admin-system/API/galleries.php?page=${page}&limit=20`;
        if (status !== '') {
            url += `&is_active=${status}`;
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
            displayGalleries(data.data);
            updatePagination(data.meta);
        } else {
            console.error('Failed to load galleries:', data.error);
        }
    } catch (error) {
        console.error('Error loading galleries:', error);
    }
}

function displayGalleries(galleries) {
    const tbody = document.getElementById('galleriesTableBody');
    
    if (!galleries || galleries.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No galleries found</td></tr>';
        return;
    }

    let html = '';
    galleries.forEach(gallery => {
        const statusClass = gallery.is_active ? 'bg-success' : 'bg-danger';
        const statusText = gallery.is_active ? 'Active' : 'Inactive';
        const typeClass = gallery.gallery_type === 'virtual' ? 'bg-info' : 'bg-warning';
        
        html += `
            <tr>
                <td>${gallery.gallery_id}</td>
                <td>${gallery.title}</td>
                <td>${gallery.artist_id}</td>
                <td><span class="badge ${typeClass}">${gallery.gallery_type}</span></td>
                <td>${gallery.price ? '$' + gallery.price.toLocaleString() : 'Free'}</td>
                <td>${gallery.duration} days</td>
                <td><span class="badge ${statusClass}">${statusText}</span></td>
                <td>${new Date(gallery.start_date).toLocaleDateString()}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editGallery(${gallery.gallery_id})">
                        Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteGallery(${gallery.gallery_id})">
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
    loadGalleries(currentPage, currentStatus);
}

function filterGalleries() {
    currentStatus = document.getElementById('statusFilter').value;
    currentPage = 1;
    loadGalleries(currentPage, currentStatus);
}

function openAddGalleryModal() {
    document.getElementById('galleryModalTitle').textContent = 'Add New Gallery';
    document.getElementById('galleryForm').reset();
    document.getElementById('galleryId').value = '';
    document.getElementById('startDate').value = new Date().toISOString().slice(0, 16);
}

async function editGallery(galleryId) {
    try {
        const response = await fetch(`/admin-system/API/galleries.php?id=${galleryId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            const gallery = data.data;
            
            document.getElementById('galleryModalTitle').textContent = 'Edit Gallery';
            document.getElementById('galleryId').value = gallery.gallery_id;
            document.getElementById('title').value = gallery.title;
            document.getElementById('artistId').value = gallery.artist_id;
            document.getElementById('galleryType').value = gallery.gallery_type;
            document.getElementById('duration').value = gallery.duration;
            document.getElementById('price').value = gallery.price || '';
            document.getElementById('startDate').value = gallery.start_date.replace(' ', 'T');
            document.getElementById('isActive').checked = gallery.is_active == 1;
            document.getElementById('description').value = gallery.description || '';
            document.getElementById('address').value = gallery.address || '';
            document.getElementById('city').value = gallery.city || '';
            document.getElementById('phone').value = gallery.phone || '';
            document.getElementById('image').value = gallery.img || '';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('galleryModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Error loading gallery details:', error);
    }
}

async function saveGallery() {
    const galleryId = document.getElementById('galleryId').value;
    const isEdit = galleryId !== '';
    
    const galleryData = {
        title: document.getElementById('title').value,
        artist_id: parseInt(document.getElementById('artistId').value),
        gallery_type: document.getElementById('galleryType').value,
        duration: parseInt(document.getElementById('duration').value),
        price: document.getElementById('price').value ? parseFloat(document.getElementById('price').value) : null,
        start_date: document.getElementById('startDate').value,
        is_active: document.getElementById('isActive').checked ? 1 : 0,
        description: document.getElementById('description').value,
        address: document.getElementById('address').value,
        city: document.getElementById('city').value,
        phone: document.getElementById('phone').value,
        img: document.getElementById('image').value
    };

    try {
        const url = isEdit ? `/admin-system/API/galleries.php?id=${galleryId}` : '/admin-system/API/galleries.php';
        const method = isEdit ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': localStorage.getItem('csrf_token')
            },
            body: JSON.stringify(galleryData)
        });

        if (response.status === 401 || response.status === 403) {
            localStorage.clear();
            window.location.href = 'login.php';
            return;
        }

        const data = await response.json();
        
        if (response.ok) {
            // Close modal and reload galleries
            const modal = bootstrap.Modal.getInstance(document.getElementById('galleryModal'));
            modal.hide();
            loadGalleries(currentPage, currentStatus);
        } else {
            alert('Error: ' + (data.error || 'Failed to save gallery'));
        }
    } catch (error) {
        console.error('Error saving gallery:', error);
        alert('Error saving gallery. Please try again.');
    }
}

async function deleteGallery(galleryId) {
    if (!confirm('Are you sure you want to delete this gallery?')) {
        return;
    }

    try {
        const response = await fetch(`/admin-system/API/galleries.php?id=${galleryId}`, {
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
            loadGalleries(currentPage, currentStatus);
        } else {
            alert('Error: ' + (data.error || 'Failed to delete gallery'));
        }
    } catch (error) {
        console.error('Error deleting gallery:', error);
        alert('Error deleting gallery. Please try again.');
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
