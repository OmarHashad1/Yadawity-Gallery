let currentPage = 1;
let currentType = '';

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

    // Load artworks
    loadArtworks();
});

async function loadArtworks(page = 1, type = '') {
    try {
        let url = `/admin-system/API/artworks.php?page=${page}&limit=20`;
        if (type) {
            url += `&type=${encodeURIComponent(type)}`;
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
            displayArtworks(data.data);
            updatePagination(data.meta);
        } else {
            console.error('Failed to load artworks:', data.error);
        }
    } catch (error) {
        console.error('Error loading artworks:', error);
    }
}

function displayArtworks(artworks) {
    const tbody = document.getElementById('artworksTableBody');
    
    if (!artworks || artworks.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No artworks found</td></tr>';
        return;
    }

    let html = '';
    artworks.forEach(artwork => {
        const statusClass = artwork.is_available ? 'bg-success' : 'bg-danger';
        const statusText = artwork.is_available ? 'Available' : 'Unavailable';
        const auctionClass = artwork.on_auction ? 'bg-warning' : 'bg-secondary';
        const auctionText = artwork.on_auction ? 'Yes' : 'No';
        
        html += `
            <tr>
                <td>${artwork.artwork_id}</td>
                <td>${artwork.title}</td>
                <td>${artwork.artist_id}</td>
                <td><span class="badge bg-info">${artwork.type}</span></td>
                <td>$${(artwork.price || 0).toLocaleString()}</td>
                <td><span class="badge ${statusClass}">${statusText}</span></td>
                <td><span class="badge ${auctionClass}">${auctionText}</span></td>
                <td>${new Date(artwork.created_at).toLocaleDateString()}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editArtwork(${artwork.artwork_id})">
                        Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteArtwork(${artwork.artwork_id})">
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
    loadArtworks(currentPage, currentType);
}

function filterArtworks() {
    currentType = document.getElementById('typeFilter').value;
    currentPage = 1;
    loadArtworks(currentPage, currentType);
}

function openAddArtworkModal() {
    document.getElementById('artworkModalTitle').textContent = 'Add New Artwork';
    document.getElementById('artworkForm').reset();
    document.getElementById('artworkId').value = '';
}

async function editArtwork(artworkId) {
    try {
        const response = await fetch(`/admin-system/API/artworks.php?id=${artworkId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            const artwork = data.data;
            
            document.getElementById('artworkModalTitle').textContent = 'Edit Artwork';
            document.getElementById('artworkId').value = artwork.artwork_id;
            document.getElementById('title').value = artwork.title;
            document.getElementById('artistId').value = artwork.artist_id;
            document.getElementById('type').value = artwork.type;
            document.getElementById('price').value = artwork.price;
            document.getElementById('description').value = artwork.description || '';
            document.getElementById('dimensions').value = artwork.dimensions || '';
            document.getElementById('year').value = artwork.year || '';
            document.getElementById('material').value = artwork.material || '';
            document.getElementById('artworkImage').value = artwork.artwork_image || '';
            document.getElementById('isAvailable').checked = artwork.is_available == 1;
            document.getElementById('onAuction').checked = artwork.on_auction == 1;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('artworkModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Error loading artwork details:', error);
    }
}

async function saveArtwork() {
    const artworkId = document.getElementById('artworkId').value;
    const isEdit = artworkId !== '';
    
    const artworkData = {
        title: document.getElementById('title').value,
        artist_id: parseInt(document.getElementById('artistId').value),
        type: document.getElementById('type').value,
        price: parseFloat(document.getElementById('price').value),
        description: document.getElementById('description').value,
        dimensions: document.getElementById('dimensions').value,
        year: document.getElementById('year').value,
        material: document.getElementById('material').value,
        artwork_image: document.getElementById('artworkImage').value,
        is_available: document.getElementById('isAvailable').checked ? 1 : 0,
        on_auction: document.getElementById('onAuction').checked ? 1 : 0
    };

    try {
        const url = isEdit ? `/admin-system/API/artworks.php?id=${artworkId}` : '/admin-system/API/artworks.php';
        const method = isEdit ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': localStorage.getItem('csrf_token')
            },
            body: JSON.stringify(artworkData)
        });

        if (response.status === 401 || response.status === 403) {
            localStorage.clear();
            window.location.href = 'login.php';
            return;
        }

        const data = await response.json();
        
        if (response.ok) {
            // Close modal and reload artworks
            const modal = bootstrap.Modal.getInstance(document.getElementById('artworkModal'));
            modal.hide();
            loadArtworks(currentPage, currentType);
        } else {
            alert('Error: ' + (data.error || 'Failed to save artwork'));
        }
    } catch (error) {
        console.error('Error saving artwork:', error);
        alert('Error saving artwork. Please try again.');
    }
}

async function deleteArtwork(artworkId) {
    if (!confirm('Are you sure you want to delete this artwork?')) {
        return;
    }

    try {
        const response = await fetch(`/admin-system/API/artworks.php?id=${artworkId}`, {
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
            loadArtworks(currentPage, currentType);
        } else {
            alert('Error: ' + (data.error || 'Failed to delete artwork'));
        }
    } catch (error) {
        console.error('Error deleting artwork:', error);
        alert('Error deleting artwork. Please try again.');
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
