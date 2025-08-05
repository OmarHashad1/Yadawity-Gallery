// Inventory Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeInventory();
    initializeSearch();
    initializeFilters();
    initializeBulkActions();
    initializeModals();
    initializeNotifications();
    loadInventoryData();
    loadAlerts();
    loadRecentActivity();

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
let inventoryData = [];
let filteredData = [];
let selectedItems = new Set();
let currentView = 'table';

// Sample inventory data
const sampleInventory = [
    {
        id: 'ART001',
        title: 'Sunset over Mountains',
        artist: 'Maria Rodriguez',
        medium: 'Oil on Canvas',
        dimensions: '24" x 36"',
        price: 850,
        status: 'available',
        dateAdded: '2024-01-15',
        location: 'Gallery A',
        category: 'landscape',
        stock: 1,
        lastUpdated: '2024-01-20'
    },
    {
        id: 'ART002',
        title: 'Urban Dreams',
        artist: 'James Chen',
        medium: 'Acrylic on Canvas',
        dimensions: '18" x 24"',
        price: 650,
        status: 'sold',
        dateAdded: '2024-01-10',
        location: 'Gallery B',
        category: 'abstract',
        stock: 0,
        lastUpdated: '2024-01-18'
    },
    {
        id: 'ART003',
        title: 'Ocean Waves',
        artist: 'Sarah Johnson',
        medium: 'Watercolor',
        dimensions: '16" x 20"',
        price: 450,
        status: 'available',
        dateAdded: '2024-01-12',
        location: 'Gallery A',
        category: 'landscape',
        stock: 2,
        lastUpdated: '2024-01-19'
    },
    {
        id: 'ART004',
        title: 'City Lights',
        artist: 'David Wilson',
        medium: 'Digital Print',
        dimensions: '20" x 30"',
        price: 300,
        status: 'reserved',
        dateAdded: '2024-01-08',
        location: 'Gallery C',
        category: 'photography',
        stock: 3,
        lastUpdated: '2024-01-17'
    },
    {
        id: 'ART005',
        title: 'Abstract Flow',
        artist: 'Lisa Park',
        medium: 'Mixed Media',
        dimensions: '22" x 28"',
        price: 750,
        status: 'on-hold',
        dateAdded: '2024-01-05',
        location: 'Gallery B',
        category: 'abstract',
        stock: 1,
        lastUpdated: '2024-01-16'
    }
];

// Sample alerts data
const sampleAlerts = [
    {
        id: 'alert1',
        artworkId: 'ART006',
        title: 'Vintage Portrait',
        artist: 'Emily Davis',
        currentStock: 0,
        minStock: 1,
        urgency: 'high'
    },
    {
        id: 'alert2',
        artworkId: 'ART007',
        title: 'Modern Sculpture',
        artist: 'Michael Brown',
        currentStock: 1,
        minStock: 2,
        urgency: 'medium'
    }
];

// Sample recent activity data
const sampleActivity = [
    {
        id: 'activity1',
        type: 'add',
        title: 'New artwork added',
        description: '"Sunset over Mountains" by Maria Rodriguez added to inventory',
        timestamp: '2 hours ago',
        icon: '➕'
    },
    {
        id: 'activity2',
        type: 'update',
        title: 'Price updated',
        description: '"Urban Dreams" price changed from $600 to $650',
        timestamp: '4 hours ago',
        icon: '💰'
    },
    {
        id: 'activity3',
        type: 'sale',
        title: 'Artwork sold',
        description: '"Abstract Flow" marked as sold',
        timestamp: '6 hours ago',
        icon: '✅'
    },
    {
        id: 'activity4',
        type: 'location',
        title: 'Location changed',
        description: '"Ocean Waves" moved from Gallery B to Gallery A',
        timestamp: '1 day ago',
        icon: '📍'
    },
    {
        id: 'activity5',
        type: 'delete',
        title: 'Artwork removed',
        description: '"Old Painting" removed from inventory',
        timestamp: '2 days ago',
        icon: '🗑️'
    }
];

// Initialize inventory
function initializeInventory() {
    const viewToggle = document.querySelector('.view-toggle');
    if (viewToggle) {
        viewToggle.addEventListener('click', function(e) {
            if (e.target.matches('[data-view]')) {
                const view = e.target.dataset.view;
                switchView(view);
                
                // Update active button
                viewToggle.querySelectorAll('.btn').forEach(btn => btn.classList.remove('btn-primary'));
                e.target.classList.add('btn-primary');
            }
        });
    }
}

// Initialize search functionality
function initializeSearch() {
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            filterInventory(query);
        });
    }
}

// Initialize filters
function initializeFilters() {
    const filters = document.querySelectorAll('.filter-select, .filter-input');
    filters.forEach(filter => {
        filter.addEventListener('change', applyFilters);
    });
}

// Initialize bulk actions
function initializeBulkActions() {
    const selectAllCheckbox = document.querySelector('#selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
                if (isChecked) {
                    selectedItems.add(checkbox.value);
                } else {
                    selectedItems.delete(checkbox.value);
                }
            });
            updateBulkActionsUI();
        });
    }

    // Bulk action buttons
    document.addEventListener('click', function(e) {
        if (e.target.matches('.bulk-action-btn')) {
            const action = e.target.dataset.action;
            handleBulkAction(action);
        }
    });
}

// Initialize modals
function initializeModals() {
    // Add artwork modal
    const addArtworkBtn = document.querySelector('.add-artwork-btn');
    if (addArtworkBtn) {
        addArtworkBtn.addEventListener('click', function() {
            showAddArtworkModal();
        });
    }

    // Edit artwork buttons
    document.addEventListener('click', function(e) {
        if (e.target.matches('.edit-btn') || e.target.closest('.edit-btn')) {
            const artworkId = e.target.closest('[data-artwork-id]').dataset.artworkId;
            showEditArtworkModal(artworkId);
        }
    });

    // Delete artwork buttons
    document.addEventListener('click', function(e) {
        if (e.target.matches('.delete-btn') || e.target.closest('.delete-btn')) {
            const artworkId = e.target.closest('[data-artwork-id]').dataset.artworkId;
            showDeleteConfirmation(artworkId);
        }
    });

    // Modal close buttons
    document.addEventListener('click', function(e) {
        if (e.target.matches('.modal-close') || e.target.matches('.modal-backdrop')) {
            closeModals();
        }
    });

    // Form submissions
    document.addEventListener('submit', function(e) {
        if (e.target.matches('#addArtworkForm')) {
            e.preventDefault();
            handleAddArtwork(e.target);
        }
        if (e.target.matches('#editArtworkForm')) {
            e.preventDefault();
            handleEditArtwork(e.target);
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

// Load inventory data
function loadInventoryData() {
    inventoryData = [...sampleInventory];
    filteredData = [...inventoryData];
    renderInventory();
    updateStats();
}

// Load alerts
function loadAlerts() {
    const alertsList = document.querySelector('.alerts-list');
    if (!alertsList) return;

    alertsList.innerHTML = sampleAlerts.map(alert => `
        <div class="alert-item">
            <div class="alert-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="alert-info">
                <h4>${alert.title}</h4>
                <p>Stock: ${alert.currentStock} (Min: ${alert.minStock}) - ${alert.artist}</p>
            </div>
            <div class="alert-actions">
                <button class="btn btn-sm btn-primary" onclick="restockItem('${alert.artworkId}')">
                    Restock
                </button>
                <button class="btn btn-sm btn-secondary" onclick="dismissAlert('${alert.id}')">
                    Dismiss
                </button>
            </div>
        </div>
    `).join('');
}

// Load recent activity
function loadRecentActivity() {
    const activityList = document.querySelector('.activity-list');
    if (!activityList) return;

    activityList.innerHTML = sampleActivity.map(activity => `
        <div class="activity-item">
            <div class="activity-icon">
                ${activity.icon}
            </div>
            <div class="activity-info">
                <h4>${activity.title}</h4>
                <p>${activity.description}</p>
            </div>
            <div class="activity-time">
                ${activity.timestamp}
            </div>
        </div>
    `).join('');
}

// Switch between table and grid view
function switchView(view) {
    currentView = view;
    const tableView = document.querySelector('.table-view');
    const gridView = document.querySelector('.grid-view');

    if (view === 'grid') {
        tableView.style.display = 'none';
        gridView.style.display = 'block';
        renderGridView();
    } else {
        tableView.style.display = 'block';
        gridView.style.display = 'none';
        renderTableView();
    }
}

// Filter inventory
function filterInventory(query) {
    filteredData = inventoryData.filter(item => 
        item.title.toLowerCase().includes(query) ||
        item.artist.toLowerCase().includes(query) ||
        item.id.toLowerCase().includes(query) ||
        item.medium.toLowerCase().includes(query)
    );
    renderInventory();
}

// Apply filters
function applyFilters() {
    const statusFilter = document.querySelector('#statusFilter')?.value;
    const categoryFilter = document.querySelector('#categoryFilter')?.value;
    const priceMinFilter = document.querySelector('#priceMin')?.value;
    const priceMaxFilter = document.querySelector('#priceMax')?.value;

    filteredData = inventoryData.filter(item => {
        let matches = true;

        if (statusFilter && statusFilter !== 'all') {
            matches = matches && item.status === statusFilter;
        }

        if (categoryFilter && categoryFilter !== 'all') {
            matches = matches && item.category === categoryFilter;
        }

        if (priceMinFilter) {
            matches = matches && item.price >= parseFloat(priceMinFilter);
        }

        if (priceMaxFilter) {
            matches = matches && item.price <= parseFloat(priceMaxFilter);
        }

        return matches;
    });

    renderInventory();
}

// Render inventory based on current view
function renderInventory() {
    if (currentView === 'grid') {
        renderGridView();
    } else {
        renderTableView();
    }
    updateStats();
}

// Render table view
function renderTableView() {
    const tbody = document.querySelector('.inventory-table tbody');
    if (!tbody) return;

    if (filteredData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>No artwork found</h3>
                    <p>Try adjusting your filters or search terms</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = filteredData.map(item => `
        <tr data-artwork-id="${item.id}">
            <td>
                <input type="checkbox" class="item-checkbox" value="${item.id}" onchange="handleItemSelection(this)">
            </td>
            <td>
                <div class="artwork-table-image">
                    <div class="placeholder">🎨</div>
                </div>
            </td>
            <td>
                <div class="artwork-title">${item.title}</div>
                <div class="artwork-subtitle">${item.id}</div>
            </td>
            <td>${item.artist}</td>
            <td>${item.medium}</td>
            <td>$${item.price.toLocaleString()}</td>
            <td>
                <span class="artwork-status status-${item.status}">${item.status}</span>
            </td>
            <td>
                <span class="stock-indicator ${getStockClass(item.stock)}">${item.stock}</span>
            </td>
            <td>
                <div class="table-actions">
                    <button class="action-btn edit-btn" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete-btn danger" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Render grid view
function renderGridView() {
    const gridContainer = document.querySelector('.inventory-grid');
    if (!gridContainer) return;

    if (filteredData.length === 0) {
        gridContainer.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h3>No artwork found</h3>
                <p>Try adjusting your filters or search terms</p>
            </div>
        `;
        return;
    }

    gridContainer.innerHTML = filteredData.map(item => `
        <div class="artwork-card" data-artwork-id="${item.id}">
            <div class="artwork-image">
                <div class="placeholder">🎨</div>
            </div>
            <div class="artwork-info">
                <h3>${item.title}</h3>
                <div class="artwork-meta">
                    <span><i class="fas fa-user"></i> ${item.artist}</span>
                    <span><i class="fas fa-palette"></i> ${item.medium}</span>
                    <span><i class="fas fa-ruler"></i> ${item.dimensions}</span>
                    <span><i class="fas fa-tag"></i> ${item.id}</span>
                </div>
                <div class="artwork-price">$${item.price.toLocaleString()}</div>
                <div class="artwork-actions">
                    <span class="artwork-status status-${item.status}">${item.status}</span>
                    <div class="table-actions">
                        <button class="action-btn edit-btn" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete-btn danger" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Update statistics
function updateStats() {
    const totalCount = document.querySelector('[data-stat="total"]');
    const availableCount = document.querySelector('[data-stat="available"]');
    const soldCount = document.querySelector('[data-stat="sold"]');
    const totalValue = document.querySelector('[data-stat="value"]');

    if (totalCount) totalCount.textContent = inventoryData.length;
    if (availableCount) availableCount.textContent = inventoryData.filter(item => item.status === 'available').length;
    if (soldCount) soldCount.textContent = inventoryData.filter(item => item.status === 'sold').length;
    if (totalValue) {
        const value = inventoryData.reduce((sum, item) => sum + item.price, 0);
        totalValue.textContent = `$${value.toLocaleString()}`;
    }
}

// Get stock indicator class
function getStockClass(stock) {
    if (stock === 0) return 'out-of-stock';
    if (stock <= 2) return 'low-stock';
    return 'in-stock';
}

// Handle item selection
function handleItemSelection(checkbox) {
    if (checkbox.checked) {
        selectedItems.add(checkbox.value);
    } else {
        selectedItems.delete(checkbox.value);
    }
    updateBulkActionsUI();
}

// Update bulk actions UI
function updateBulkActionsUI() {
    const selectedCount = document.querySelector('.selected-count');
    const bulkActions = document.querySelector('.bulk-actions');

    if (selectedCount) {
        selectedCount.textContent = `${selectedItems.size} items selected`;
    }

    if (bulkActions) {
        bulkActions.style.display = selectedItems.size > 0 ? 'block' : 'none';
    }
}

// Handle bulk actions
function handleBulkAction(action) {
    if (selectedItems.size === 0) {
        showNotification('Please select items first', 'warning');
        return;
    }

    const items = Array.from(selectedItems);
    
    switch (action) {
        case 'delete':
            if (confirm(`Delete ${items.length} selected items?`)) {
                items.forEach(id => {
                    inventoryData = inventoryData.filter(item => item.id !== id);
                });
                filteredData = filteredData.filter(item => !items.includes(item.id));
                selectedItems.clear();
                renderInventory();
                updateBulkActionsUI();
                showNotification(`${items.length} items deleted successfully`, 'success');
            }
            break;
        case 'archive':
            items.forEach(id => {
                const item = inventoryData.find(item => item.id === id);
                if (item) item.status = 'archived';
            });
            filteredData = [...inventoryData];
            selectedItems.clear();
            renderInventory();
            updateBulkActionsUI();
            showNotification(`${items.length} items archived successfully`, 'success');
            break;
        case 'update-price':
            const newPrice = prompt('Enter new price for selected items:');
            if (newPrice && !isNaN(newPrice)) {
                items.forEach(id => {
                    const item = inventoryData.find(item => item.id === id);
                    if (item) item.price = parseFloat(newPrice);
                });
                filteredData = [...inventoryData];
                selectedItems.clear();
                renderInventory();
                updateBulkActionsUI();
                showNotification(`Prices updated for ${items.length} items`, 'success');
            }
            break;
        case 'change-location':
            const newLocation = prompt('Enter new location for selected items:');
            if (newLocation) {
                items.forEach(id => {
                    const item = inventoryData.find(item => item.id === id);
                    if (item) item.location = newLocation;
                });
                filteredData = [...inventoryData];
                selectedItems.clear();
                renderInventory();
                updateBulkActionsUI();
                showNotification(`Location updated for ${items.length} items`, 'success');
            }
            break;
    }
}

// Show add artwork modal
function showAddArtworkModal() {
    const modalHTML = `
        <div class="modal-backdrop">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Add New Artwork</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <form id="addArtworkForm">
                    <div class="form-group">
                        <label for="artworkTitle">Title *</label>
                        <input type="text" id="artworkTitle" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="artworkArtist">Artist *</label>
                        <input type="text" id="artworkArtist" name="artist" required>
                    </div>
                    <div class="form-group">
                        <label for="artworkMedium">Medium *</label>
                        <select id="artworkMedium" name="medium" required>
                            <option value="">Select medium</option>
                            <option value="Oil on Canvas">Oil on Canvas</option>
                            <option value="Acrylic on Canvas">Acrylic on Canvas</option>
                            <option value="Watercolor">Watercolor</option>
                            <option value="Digital Print">Digital Print</option>
                            <option value="Mixed Media">Mixed Media</option>
                            <option value="Sculpture">Sculpture</option>
                            <option value="Photography">Photography</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="artworkDimensions">Dimensions</label>
                        <input type="text" id="artworkDimensions" name="dimensions" placeholder='e.g., 24" x 36"'>
                    </div>
                    <div class="form-group">
                        <label for="artworkPrice">Price *</label>
                        <input type="number" id="artworkPrice" name="price" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="artworkCategory">Category *</label>
                        <select id="artworkCategory" name="category" required>
                            <option value="">Select category</option>
                            <option value="landscape">Landscape</option>
                            <option value="portrait">Portrait</option>
                            <option value="abstract">Abstract</option>
                            <option value="still-life">Still Life</option>
                            <option value="photography">Photography</option>
                            <option value="sculpture">Sculpture</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="artworkLocation">Location</label>
                        <select id="artworkLocation" name="location">
                            <option value="Gallery A">Gallery A</option>
                            <option value="Gallery B">Gallery B</option>
                            <option value="Gallery C">Gallery C</option>
                            <option value="Storage">Storage</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="artworkStock">Stock Quantity</label>
                        <input type="number" id="artworkStock" name="stock" min="0" value="1">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Artwork</button>
                    </div>
                </form>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// Show edit artwork modal
function showEditArtworkModal(artworkId) {
    const artwork = inventoryData.find(item => item.id === artworkId);
    if (!artwork) return;

    const modalHTML = `
        <div class="modal-backdrop">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Edit Artwork</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <form id="editArtworkForm" data-artwork-id="${artwork.id}">
                    <div class="form-group">
                        <label for="editTitle">Title *</label>
                        <input type="text" id="editTitle" name="title" value="${artwork.title}" required>
                    </div>
                    <div class="form-group">
                        <label for="editArtist">Artist *</label>
                        <input type="text" id="editArtist" name="artist" value="${artwork.artist}" required>
                    </div>
                    <div class="form-group">
                        <label for="editMedium">Medium *</label>
                        <select id="editMedium" name="medium" required>
                            <option value="Oil on Canvas" ${artwork.medium === 'Oil on Canvas' ? 'selected' : ''}>Oil on Canvas</option>
                            <option value="Acrylic on Canvas" ${artwork.medium === 'Acrylic on Canvas' ? 'selected' : ''}>Acrylic on Canvas</option>
                            <option value="Watercolor" ${artwork.medium === 'Watercolor' ? 'selected' : ''}>Watercolor</option>
                            <option value="Digital Print" ${artwork.medium === 'Digital Print' ? 'selected' : ''}>Digital Print</option>
                            <option value="Mixed Media" ${artwork.medium === 'Mixed Media' ? 'selected' : ''}>Mixed Media</option>
                            <option value="Sculpture" ${artwork.medium === 'Sculpture' ? 'selected' : ''}>Sculpture</option>
                            <option value="Photography" ${artwork.medium === 'Photography' ? 'selected' : ''}>Photography</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editDimensions">Dimensions</label>
                        <input type="text" id="editDimensions" name="dimensions" value="${artwork.dimensions || ''}" placeholder='e.g., 24" x 36"'>
                    </div>
                    <div class="form-group">
                        <label for="editPrice">Price *</label>
                        <input type="number" id="editPrice" name="price" value="${artwork.price}" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="editStatus">Status *</label>
                        <select id="editStatus" name="status" required>
                            <option value="available" ${artwork.status === 'available' ? 'selected' : ''}>Available</option>
                            <option value="sold" ${artwork.status === 'sold' ? 'selected' : ''}>Sold</option>
                            <option value="reserved" ${artwork.status === 'reserved' ? 'selected' : ''}>Reserved</option>
                            <option value="on-hold" ${artwork.status === 'on-hold' ? 'selected' : ''}>On Hold</option>
                            <option value="archived" ${artwork.status === 'archived' ? 'selected' : ''}>Archived</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editCategory">Category *</label>
                        <select id="editCategory" name="category" required>
                            <option value="landscape" ${artwork.category === 'landscape' ? 'selected' : ''}>Landscape</option>
                            <option value="portrait" ${artwork.category === 'portrait' ? 'selected' : ''}>Portrait</option>
                            <option value="abstract" ${artwork.category === 'abstract' ? 'selected' : ''}>Abstract</option>
                            <option value="still-life" ${artwork.category === 'still-life' ? 'selected' : ''}>Still Life</option>
                            <option value="photography" ${artwork.category === 'photography' ? 'selected' : ''}>Photography</option>
                            <option value="sculpture" ${artwork.category === 'sculpture' ? 'selected' : ''}>Sculpture</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editLocation">Location</label>
                        <select id="editLocation" name="location">
                            <option value="Gallery A" ${artwork.location === 'Gallery A' ? 'selected' : ''}>Gallery A</option>
                            <option value="Gallery B" ${artwork.location === 'Gallery B' ? 'selected' : ''}>Gallery B</option>
                            <option value="Gallery C" ${artwork.location === 'Gallery C' ? 'selected' : ''}>Gallery C</option>
                            <option value="Storage" ${artwork.location === 'Storage' ? 'selected' : ''}>Storage</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editStock">Stock Quantity</label>
                        <input type="number" id="editStock" name="stock" value="${artwork.stock}" min="0">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Artwork</button>
                    </div>
                </form>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// Show delete confirmation
function showDeleteConfirmation(artworkId) {
    const artwork = inventoryData.find(item => item.id === artworkId);
    if (!artwork) return;

    if (confirm(`Are you sure you want to delete "${artwork.title}" by ${artwork.artist}?`)) {
        inventoryData = inventoryData.filter(item => item.id !== artworkId);
        filteredData = filteredData.filter(item => item.id !== artworkId);
        renderInventory();
        showNotification('Artwork deleted successfully', 'success');
    }
}

// Handle add artwork form submission
function handleAddArtwork(form) {
    const formData = new FormData(form);
    const newArtwork = {
        id: `ART${String(inventoryData.length + 1).padStart(3, '0')}`,
        title: formData.get('title'),
        artist: formData.get('artist'),
        medium: formData.get('medium'),
        dimensions: formData.get('dimensions') || '',
        price: parseFloat(formData.get('price')),
        status: 'available',
        dateAdded: new Date().toISOString().split('T')[0],
        location: formData.get('location') || 'Gallery A',
        category: formData.get('category'),
        stock: parseInt(formData.get('stock')) || 1,
        lastUpdated: new Date().toISOString().split('T')[0]
    };

    inventoryData.push(newArtwork);
    filteredData = [...inventoryData];
    renderInventory();
    closeModals();
    showNotification('Artwork added successfully', 'success');
}

// Handle edit artwork form submission
function handleEditArtwork(form) {
    const artworkId = form.dataset.artworkId;
    const formData = new FormData(form);
    const artwork = inventoryData.find(item => item.id === artworkId);

    if (artwork) {
        artwork.title = formData.get('title');
        artwork.artist = formData.get('artist');
        artwork.medium = formData.get('medium');
        artwork.dimensions = formData.get('dimensions') || '';
        artwork.price = parseFloat(formData.get('price'));
        artwork.status = formData.get('status');
        artwork.category = formData.get('category');
        artwork.location = formData.get('location');
        artwork.stock = parseInt(formData.get('stock')) || 0;
        artwork.lastUpdated = new Date().toISOString().split('T')[0];

        filteredData = [...inventoryData];
        renderInventory();
        closeModals();
        showNotification('Artwork updated successfully', 'success');
    }
}

// Close all modals
function closeModals() {
    const modals = document.querySelectorAll('.modal-backdrop');
    modals.forEach(modal => modal.remove());
}

// Restock item
function restockItem(artworkId) {
    const quantity = prompt('Enter quantity to add:');
    if (quantity && !isNaN(quantity)) {
        const artwork = inventoryData.find(item => item.id === artworkId);
        if (artwork) {
            artwork.stock += parseInt(quantity);
            renderInventory();
            showNotification(`Restocked ${artwork.title} with ${quantity} units`, 'success');
        }
    }
}

// Dismiss alert
function dismissAlert(alertId) {
    const alertElement = document.querySelector(`[data-alert-id="${alertId}"]`);
    if (alertElement) {
        alertElement.remove();
        showNotification('Alert dismissed', 'info');
    }
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
