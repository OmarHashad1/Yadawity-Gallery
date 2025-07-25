// Admin Gallery Management JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeAdminGallery();
});

function initializeAdminGallery() {
    initializeSidebar();
    initializeTabs();
    loadVirtualGalleries();
    loadExhibitions();
    loadPartnerGalleries();
    loadFeaturedContent();
    initializeModals();
    initializeEventListeners();
    initializeFilters();
}

// Sample data
const virtualGalleries = [
    {
        id: 1,
        name: 'Contemporary Visions',
        type: 'Virtual Gallery',
        description: 'A curated collection of contemporary artworks exploring modern themes and techniques.',
        location: 'Virtual Space',
        status: 'active',
        artworks: 24,
        visitors: 1847,
        created: '2025-01-15',
        image: 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=400&h=200&fit=crop'
    },
    {
        id: 2,
        name: 'Egyptian Heritage',
        type: 'Virtual Gallery',
        description: 'Celebrating Egyptian cultural heritage through traditional and modern interpretations.',
        location: 'Virtual Space',
        status: 'active',
        artworks: 18,
        visitors: 2156,
        created: '2025-01-10',
        image: 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=200&fit=crop'
    },
    {
        id: 3,
        name: 'Emerging Artists Showcase',
        type: 'Virtual Gallery',
        description: 'Platform for showcasing work from up-and-coming artists.',
        location: 'Virtual Space',
        status: 'draft',
        artworks: 12,
        visitors: 0,
        created: '2025-01-20',
        image: 'https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=400&h=200&fit=crop'
    }
];

const exhibitions = [
    {
        id: 1,
        title: 'Harmony in Color',
        type: 'Group Exhibition',
        description: 'An exploration of color theory through the works of five contemporary artists.',
        artist: 'Marina Kovač & Others',
        gallery: 'Contemporary Visions',
        startDate: '2025-02-01',
        endDate: '2025-03-15',
        status: 'upcoming',
        visitors: 0,
        artworks: 15
    },
    {
        id: 2,
        title: 'Digital Renaissance',
        type: 'Solo Exhibition',
        description: 'Marcus Rodriguez presents his latest collection of digital art pieces.',
        artist: 'Marcus Rodriguez',
        gallery: 'Virtual Gallery 1',
        startDate: '2025-01-15',
        endDate: '2025-02-28',
        status: 'active',
        visitors: 892,
        artworks: 12
    },
    {
        id: 3,
        title: 'Textures of Time',
        type: 'Themed Exhibition',
        description: 'A journey through different artistic periods and their unique textures.',
        artist: 'Various Artists',
        gallery: 'Egyptian Heritage',
        startDate: '2024-12-01',
        endDate: '2025-01-20',
        status: 'ended',
        visitors: 1543,
        artworks: 22
    }
];

const partnerGalleries = [
    {
        id: 1,
        name: 'Cairo Modern Art Gallery',
        contact: 'Ahmed Farouk',
        email: 'contact@cairomodern.com',
        phone: '+20 2 1234 5678',
        address: 'Downtown Cairo, Egypt',
        type: 'Full Partnership',
        commission: 15.0,
        exhibitions: 3,
        revenue: 45800
    },
    {
        id: 2,
        name: 'Alexandria Heritage Center',
        contact: 'Layla Mahmoud',
        email: 'info@alexheritage.org',
        phone: '+20 3 9876 5432',
        address: 'Alexandria, Egypt',
        type: 'Exhibition Partner',
        commission: 10.0,
        exhibitions: 2,
        revenue: 28400
    },
    {
        id: 3,
        name: 'Aswan Cultural Gallery',
        contact: 'Omar Hassan',
        email: 'gallery@aswan-culture.com',
        phone: '+20 97 555 1234',
        address: 'Aswan, Egypt',
        type: 'Promotion Partner',
        commission: 8.0,
        exhibitions: 1,
        revenue: 12300
    }
];

const featuredContent = {
    homepage: [
        {
            id: 1,
            title: 'Contemporary Visions',
            type: 'Virtual Gallery',
            image: 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=280&h=150&fit=crop',
            selected: true
        },
        {
            id: 2,
            title: 'Egyptian Heritage',
            type: 'Virtual Gallery',
            image: 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=280&h=150&fit=crop',
            selected: false
        }
    ],
    trending: [
        {
            id: 1,
            title: 'Digital Renaissance',
            type: 'Solo Exhibition',
            image: 'https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=280&h=150&fit=crop',
            selected: true
        },
        {
            id: 2,
            title: 'Harmony in Color',
            type: 'Group Exhibition',
            image: 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=280&h=150&fit=crop',
            selected: false
        }
    ]
};

// Initialize sidebar functionality
function initializeSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });

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

            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));

            this.classList.add('active');
            const targetPane = document.getElementById(targetTab);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });
}

// Load virtual galleries
function loadVirtualGalleries() {
    const galleriesGrid = document.getElementById('virtualGalleriesGrid');
    if (!galleriesGrid) return;

    galleriesGrid.innerHTML = '';

    if (virtualGalleries.length === 0) {
        galleriesGrid.innerHTML = `
            <div class="emptyState" style="grid-column: 1 / -1;">
                <i class="fas fa-images"></i>
                <h3>No Virtual Galleries</h3>
                <p>Create your first virtual gallery to get started</p>
                <button class="btn btn-primary" onclick="showGalleryModal()">
                    <i class="fas fa-plus"></i>
                    Create Gallery
                </button>
            </div>
        `;
        return;
    }

    virtualGalleries.forEach(gallery => {
        const galleryCard = document.createElement('div');
        galleryCard.className = 'galleryCard';
        galleryCard.innerHTML = `
            <div class="galleryImage">
                <img src="${gallery.image}" alt="${gallery.name}">
                <span class="galleryStatus status-${gallery.status}">${formatStatus(gallery.status)}</span>
            </div>
            <div class="galleryInfo">
                <div class="galleryHeader">
                    <div>
                        <h3 class="galleryTitle">${gallery.name}</h3>
                        <p class="galleryType">${gallery.type}</p>
                    </div>
                    <div class="galleryActions">
                        <button class="btn btn-sm btn-outline" onclick="editGallery(${gallery.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline" onclick="deleteGallery(${gallery.id})" style="color: var(--danger-red); border-color: var(--danger-red);">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <p class="galleryDescription">${gallery.description}</p>
                <div class="galleryStats">
                    <span class="galleryStat">
                        <i class="fas fa-images"></i>
                        ${gallery.artworks} artworks
                    </span>
                    <span class="galleryStat">
                        <i class="fas fa-eye"></i>
                        ${gallery.visitors.toLocaleString()} visitors
                    </span>
                </div>
            </div>
        `;
        galleriesGrid.appendChild(galleryCard);
    });
}

// Load exhibitions
function loadExhibitions() {
    const exhibitionsList = document.getElementById('exhibitionsList');
    if (!exhibitionsList) return;

    exhibitionsList.innerHTML = '';

    if (exhibitions.length === 0) {
        exhibitionsList.innerHTML = `
            <div class="emptyState">
                <i class="fas fa-calendar-alt"></i>
                <h3>No Exhibitions</h3>
                <p>Create your first exhibition to showcase artworks</p>
                <button class="btn btn-primary" onclick="showExhibitionModal()">
                    <i class="fas fa-plus"></i>
                    Create Exhibition
                </button>
            </div>
        `;
        return;
    }

    exhibitions.forEach(exhibition => {
        const exhibitionCard = document.createElement('div');
        exhibitionCard.className = 'exhibitionCard';
        exhibitionCard.innerHTML = `
            <div class="exhibitionHeader">
                <div class="exhibitionInfo">
                    <h3>${exhibition.title}</h3>
                    <div class="exhibitionMeta">
                        <span><i class="fas fa-user"></i> ${exhibition.artist}</span>
                        <span><i class="fas fa-building"></i> ${exhibition.gallery}</span>
                        <span><i class="fas fa-calendar"></i> ${formatDateRange(exhibition.startDate, exhibition.endDate)}</span>
                        <span><i class="fas fa-tag"></i> ${exhibition.type}</span>
                    </div>
                    <span class="exhibitionStatus status-${exhibition.status}">${formatStatus(exhibition.status)}</span>
                </div>
                <div class="exhibitionActions">
                    <button class="btn btn-sm btn-outline" onclick="editExhibition(${exhibition.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline" onclick="deleteExhibition(${exhibition.id})" style="color: var(--danger-red); border-color: var(--danger-red);">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <p class="exhibitionDescription">${exhibition.description}</p>
            <div class="exhibitionFooter">
                <span><i class="fas fa-images"></i> ${exhibition.artworks} artworks</span>
                <span><i class="fas fa-eye"></i> ${exhibition.visitors.toLocaleString()} visitors</span>
            </div>
        `;
        exhibitionsList.appendChild(exhibitionCard);
    });
}

// Load partner galleries
function loadPartnerGalleries() {
    const partnersGrid = document.getElementById('partnersGrid');
    if (!partnersGrid) return;

    partnersGrid.innerHTML = '';

    if (partnerGalleries.length === 0) {
        partnersGrid.innerHTML = `
            <div class="emptyState" style="grid-column: 1 / -1;">
                <i class="fas fa-handshake"></i>
                <h3>No Partner Galleries</h3>
                <p>Add partner galleries to expand your network</p>
                <button class="btn btn-primary" onclick="showPartnerModal()">
                    <i class="fas fa-plus"></i>
                    Add Partner
                </button>
            </div>
        `;
        return;
    }

    partnerGalleries.forEach(partner => {
        const partnerCard = document.createElement('div');
        partnerCard.className = 'partnerCard';
        partnerCard.innerHTML = `
            <div class="partnerHeader">
                <div class="partnerInfo">
                    <h4>${partner.name}</h4>
                    <span class="partnerType">${partner.type}</span>
                </div>
                <div class="galleryActions">
                    <button class="btn btn-sm btn-outline" onclick="editPartner(${partner.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline" onclick="deletePartner(${partner.id})" style="color: var(--danger-red); border-color: var(--danger-red);">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="partnerContact">
                <p><i class="fas fa-user"></i> ${partner.contact}</p>
                <p><i class="fas fa-envelope"></i> ${partner.email}</p>
                <p><i class="fas fa-phone"></i> ${partner.phone}</p>
                <p><i class="fas fa-map-marker-alt"></i> ${partner.address}</p>
            </div>
            <div class="partnerStats">
                <span>${partner.commission}% commission</span>
                <span>${partner.exhibitions} exhibitions</span>
                <span>EGP ${partner.revenue.toLocaleString()}</span>
            </div>
        `;
        partnersGrid.appendChild(partnerCard);
    });
}

// Load featured content
function loadFeaturedContent() {
    loadFeaturedSection('homepageFeatured', featuredContent.homepage);
    loadFeaturedSection('trendingExhibitions', featuredContent.trending);
}

function loadFeaturedSection(containerId, items) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = '';

    items.forEach(item => {
        const featuredItem = document.createElement('div');
        featuredItem.className = `featuredItem ${item.selected ? 'selected' : ''}`;
        featuredItem.onclick = () => toggleFeaturedItem(containerId, item.id);
        featuredItem.innerHTML = `
            <div class="featuredItemImage">
                <img src="${item.image}" alt="${item.title}">
            </div>
            <div class="featuredItemInfo">
                <h4 class="featuredItemTitle">${item.title}</h4>
                <p class="featuredItemMeta">${item.type}</p>
            </div>
        `;
        container.appendChild(featuredItem);
    });

    // Add controls
    const controlsDiv = document.createElement('div');
    controlsDiv.className = 'featuredControls';
    controlsDiv.innerHTML = `
        <button class="featuredControlBtn" onclick="selectAllFeatured('${containerId}')">Select All</button>
        <button class="featuredControlBtn" onclick="clearAllFeatured('${containerId}')">Clear All</button>
        <button class="featuredControlBtn active" onclick="saveFeatured('${containerId}')">Save Changes</button>
    `;
    container.parentElement.appendChild(controlsDiv);
}

// Initialize modals
function initializeModals() {
    const modals = ['galleryModal', 'exhibitionModal', 'partnerModal'];
    
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        const closeBtn = document.getElementById(`close${modalId.charAt(0).toUpperCase() + modalId.slice(1, -5)}Modal`);
        const cancelBtn = document.getElementById(`cancel${modalId.charAt(0).toUpperCase() + modalId.slice(1, -5)}`);

        // Close modal handlers
        [closeBtn, cancelBtn].forEach(btn => {
            if (btn) {
                btn.addEventListener('click', () => closeModal(modal));
            }
        });

        // Backdrop click
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal(modal);
            });
        }
    });

    // Form submissions
    const galleryForm = document.getElementById('galleryForm');
    if (galleryForm) {
        galleryForm.addEventListener('submit', handleGallerySubmit);
    }

    const exhibitionForm = document.getElementById('exhibitionForm');
    if (exhibitionForm) {
        exhibitionForm.addEventListener('submit', handleExhibitionSubmit);
    }

    const partnerForm = document.getElementById('partnerForm');
    if (partnerForm) {
        partnerForm.addEventListener('submit', handlePartnerSubmit);
    }
}

// Initialize event listeners
function initializeEventListeners() {
    // Header actions
    const newExhibitionBtn = document.getElementById('newExhibitionBtn');
    if (newExhibitionBtn) {
        newExhibitionBtn.addEventListener('click', () => showExhibitionModal());
    }

    const newGalleryBtn = document.getElementById('newGalleryBtn');
    if (newGalleryBtn) {
        newGalleryBtn.addEventListener('click', () => showGalleryModal());
    }

    const addPartnerBtn = document.getElementById('addPartnerBtn');
    if (addPartnerBtn) {
        addPartnerBtn.addEventListener('click', () => showPartnerModal());
    }
}

// Initialize filters
function initializeFilters() {
    const galleryStatusFilter = document.getElementById('galleryStatusFilter');
    const gallerySearch = document.getElementById('gallerySearch');
    const exhibitionStatusFilter = document.getElementById('exhibitionStatusFilter');
    const exhibitionTypeFilter = document.getElementById('exhibitionTypeFilter');

    if (galleryStatusFilter) {
        galleryStatusFilter.addEventListener('change', filterGalleries);
    }

    if (gallerySearch) {
        gallerySearch.addEventListener('input', debounce(filterGalleries, 300));
    }

    if (exhibitionStatusFilter) {
        exhibitionStatusFilter.addEventListener('change', filterExhibitions);
    }

    if (exhibitionTypeFilter) {
        exhibitionTypeFilter.addEventListener('change', filterExhibitions);
    }
}

// Modal functions
function showGalleryModal(galleryId = null) {
    const modal = document.getElementById('galleryModal');
    const form = document.getElementById('galleryForm');
    
    if (galleryId) {
        const gallery = virtualGalleries.find(g => g.id === galleryId);
        if (gallery) {
            document.getElementById('galleryName').value = gallery.name;
            document.getElementById('galleryType').value = gallery.type.toLowerCase().replace(' ', '_');
            document.getElementById('galleryDescription').value = gallery.description;
            document.getElementById('galleryLocation').value = gallery.location;
            document.getElementById('galleryStatus').value = gallery.status;
        }
    } else {
        form.reset();
    }
    
    showModal('galleryModal');
}

function showExhibitionModal(exhibitionId = null) {
    const modal = document.getElementById('exhibitionModal');
    const form = document.getElementById('exhibitionForm');
    
    // Populate gallery options
    const gallerySelect = document.getElementById('exhibitionGallery');
    gallerySelect.innerHTML = '<option value="">Select Gallery</option>';
    virtualGalleries.forEach(gallery => {
        const option = document.createElement('option');
        option.value = gallery.name;
        option.textContent = gallery.name;
        gallerySelect.appendChild(option);
    });
    
    if (exhibitionId) {
        const exhibition = exhibitions.find(e => e.id === exhibitionId);
        if (exhibition) {
            document.getElementById('exhibitionTitle').value = exhibition.title;
            document.getElementById('exhibitionType').value = exhibition.type.toLowerCase().replace(' ', '_');
            document.getElementById('exhibitionStartDate').value = exhibition.startDate;
            document.getElementById('exhibitionEndDate').value = exhibition.endDate;
            document.getElementById('exhibitionDescription').value = exhibition.description;
            document.getElementById('exhibitionArtist').value = exhibition.artist;
            document.getElementById('exhibitionGallery').value = exhibition.gallery;
            document.getElementById('exhibitionStatus').value = exhibition.status;
        }
    } else {
        form.reset();
    }
    
    showModal('exhibitionModal');
}

function showPartnerModal(partnerId = null) {
    const modal = document.getElementById('partnerModal');
    const form = document.getElementById('partnerForm');
    
    if (partnerId) {
        const partner = partnerGalleries.find(p => p.id === partnerId);
        if (partner) {
            document.getElementById('partnerName').value = partner.name;
            document.getElementById('partnerContact').value = partner.contact;
            document.getElementById('partnerEmail').value = partner.email;
            document.getElementById('partnerPhone').value = partner.phone;
            document.getElementById('partnerAddress').value = partner.address;
            document.getElementById('partnershipType').value = partner.type.toLowerCase().replace(' ', '_');
            document.getElementById('partnerCommission').value = partner.commission;
        }
    } else {
        form.reset();
    }
    
    showModal('partnerModal');
}

// Form handlers
function handleGallerySubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const galleryData = {
        name: formData.get('galleryName') || document.getElementById('galleryName').value,
        type: formatGalleryType(document.getElementById('galleryType').value),
        description: document.getElementById('galleryDescription').value,
        location: document.getElementById('galleryLocation').value || 'Virtual Space',
        status: document.getElementById('galleryStatus').value
    };

    showNotification('Saving gallery...', 'info');
    
    setTimeout(() => {
        showNotification(`Gallery "${galleryData.name}" saved successfully`, 'success');
        closeModal(document.getElementById('galleryModal'));
        
        // Add to galleries array (simulated)
        virtualGalleries.push({
            id: virtualGalleries.length + 1,
            ...galleryData,
            artworks: 0,
            visitors: 0,
            created: new Date().toISOString().split('T')[0],
            image: 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=400&h=200&fit=crop'
        });
        
        loadVirtualGalleries();
    }, 1500);
}

function handleExhibitionSubmit(e) {
    e.preventDefault();
    
    const exhibitionData = {
        title: document.getElementById('exhibitionTitle').value,
        type: formatExhibitionType(document.getElementById('exhibitionType').value),
        startDate: document.getElementById('exhibitionStartDate').value,
        endDate: document.getElementById('exhibitionEndDate').value,
        description: document.getElementById('exhibitionDescription').value,
        artist: document.getElementById('exhibitionArtist').value,
        gallery: document.getElementById('exhibitionGallery').value,
        status: document.getElementById('exhibitionStatus').value
    };

    showNotification('Saving exhibition...', 'info');
    
    setTimeout(() => {
        showNotification(`Exhibition "${exhibitionData.title}" saved successfully`, 'success');
        closeModal(document.getElementById('exhibitionModal'));
        
        exhibitions.push({
            id: exhibitions.length + 1,
            ...exhibitionData,
            visitors: 0,
            artworks: 0
        });
        
        loadExhibitions();
    }, 1500);
}

function handlePartnerSubmit(e) {
    e.preventDefault();
    
    const partnerData = {
        name: document.getElementById('partnerName').value,
        contact: document.getElementById('partnerContact').value,
        email: document.getElementById('partnerEmail').value,
        phone: document.getElementById('partnerPhone').value,
        address: document.getElementById('partnerAddress').value,
        type: formatPartnershipType(document.getElementById('partnershipType').value),
        commission: parseFloat(document.getElementById('partnerCommission').value)
    };

    showNotification('Adding partner...', 'info');
    
    setTimeout(() => {
        showNotification(`Partner "${partnerData.name}" added successfully`, 'success');
        closeModal(document.getElementById('partnerModal'));
        
        partnerGalleries.push({
            id: partnerGalleries.length + 1,
            ...partnerData,
            exhibitions: 0,
            revenue: 0
        });
        
        loadPartnerGalleries();
    }, 1500);
}

// CRUD operations
function editGallery(galleryId) {
    showGalleryModal(galleryId);
}

function deleteGallery(galleryId) {
    const gallery = virtualGalleries.find(g => g.id === galleryId);
    if (!gallery) return;

    if (confirm(`Are you sure you want to delete "${gallery.name}"? This action cannot be undone.`)) {
        showNotification(`Gallery "${gallery.name}" deleted successfully`, 'success');
        
        const index = virtualGalleries.findIndex(g => g.id === galleryId);
        if (index > -1) {
            virtualGalleries.splice(index, 1);
            loadVirtualGalleries();
        }
    }
}

function editExhibition(exhibitionId) {
    showExhibitionModal(exhibitionId);
}

function deleteExhibition(exhibitionId) {
    const exhibition = exhibitions.find(e => e.id === exhibitionId);
    if (!exhibition) return;

    if (confirm(`Are you sure you want to delete "${exhibition.title}"? This action cannot be undone.`)) {
        showNotification(`Exhibition "${exhibition.title}" deleted successfully`, 'success');
        
        const index = exhibitions.findIndex(e => e.id === exhibitionId);
        if (index > -1) {
            exhibitions.splice(index, 1);
            loadExhibitions();
        }
    }
}

function editPartner(partnerId) {
    showPartnerModal(partnerId);
}

function deletePartner(partnerId) {
    const partner = partnerGalleries.find(p => p.id === partnerId);
    if (!partner) return;

    if (confirm(`Are you sure you want to delete "${partner.name}"? This action cannot be undone.`)) {
        showNotification(`Partner "${partner.name}" deleted successfully`, 'success');
        
        const index = partnerGalleries.findIndex(p => p.id === partnerId);
        if (index > -1) {
            partnerGalleries.splice(index, 1);
            loadPartnerGalleries();
        }
    }
}

// Filter functions
function filterGalleries() {
    const statusFilter = document.getElementById('galleryStatusFilter').value;
    const searchTerm = document.getElementById('gallerySearch').value.toLowerCase();

    const filteredGalleries = virtualGalleries.filter(gallery => {
        const matchesStatus = !statusFilter || gallery.status === statusFilter;
        const matchesSearch = !searchTerm || 
            gallery.name.toLowerCase().includes(searchTerm) ||
            gallery.description.toLowerCase().includes(searchTerm);

        return matchesStatus && matchesSearch;
    });

    updateGalleriesDisplay(filteredGalleries);
}

function filterExhibitions() {
    const statusFilter = document.getElementById('exhibitionStatusFilter').value;
    const typeFilter = document.getElementById('exhibitionTypeFilter').value;

    const filteredExhibitions = exhibitions.filter(exhibition => {
        const matchesStatus = !statusFilter || exhibition.status === statusFilter;
        const matchesType = !typeFilter || exhibition.type.toLowerCase().replace(' ', '_') === typeFilter;

        return matchesStatus && matchesType;
    });

    updateExhibitionsDisplay(filteredExhibitions);
}

function updateGalleriesDisplay(galleriesToShow) {
    // Similar to loadVirtualGalleries but with filtered data
    const galleriesGrid = document.getElementById('virtualGalleriesGrid');
    if (!galleriesGrid) return;

    galleriesGrid.innerHTML = '';

    galleriesToShow.forEach(gallery => {
        const galleryCard = document.createElement('div');
        galleryCard.className = 'galleryCard';
        galleryCard.innerHTML = `
            <div class="galleryImage">
                <img src="${gallery.image}" alt="${gallery.name}">
                <span class="galleryStatus status-${gallery.status}">${formatStatus(gallery.status)}</span>
            </div>
            <div class="galleryInfo">
                <div class="galleryHeader">
                    <div>
                        <h3 class="galleryTitle">${gallery.name}</h3>
                        <p class="galleryType">${gallery.type}</p>
                    </div>
                    <div class="galleryActions">
                        <button class="btn btn-sm btn-outline" onclick="editGallery(${gallery.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline" onclick="deleteGallery(${gallery.id})" style="color: var(--danger-red); border-color: var(--danger-red);">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <p class="galleryDescription">${gallery.description}</p>
                <div class="galleryStats">
                    <span class="galleryStat">
                        <i class="fas fa-images"></i>
                        ${gallery.artworks} artworks
                    </span>
                    <span class="galleryStat">
                        <i class="fas fa-eye"></i>
                        ${gallery.visitors.toLocaleString()} visitors
                    </span>
                </div>
            </div>
        `;
        galleriesGrid.appendChild(galleryCard);
    });
}

function updateExhibitionsDisplay(exhibitionsToShow) {
    // Similar to loadExhibitions but with filtered data
    const exhibitionsList = document.getElementById('exhibitionsList');
    if (!exhibitionsList) return;

    exhibitionsList.innerHTML = '';

    exhibitionsToShow.forEach(exhibition => {
        const exhibitionCard = document.createElement('div');
        exhibitionCard.className = 'exhibitionCard';
        exhibitionCard.innerHTML = `
            <div class="exhibitionHeader">
                <div class="exhibitionInfo">
                    <h3>${exhibition.title}</h3>
                    <div class="exhibitionMeta">
                        <span><i class="fas fa-user"></i> ${exhibition.artist}</span>
                        <span><i class="fas fa-building"></i> ${exhibition.gallery}</span>
                        <span><i class="fas fa-calendar"></i> ${formatDateRange(exhibition.startDate, exhibition.endDate)}</span>
                        <span><i class="fas fa-tag"></i> ${exhibition.type}</span>
                    </div>
                    <span class="exhibitionStatus status-${exhibition.status}">${formatStatus(exhibition.status)}</span>
                </div>
                <div class="exhibitionActions">
                    <button class="btn btn-sm btn-outline" onclick="editExhibition(${exhibition.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline" onclick="deleteExhibition(${exhibition.id})" style="color: var(--danger-red); border-color: var(--danger-red);">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <p class="exhibitionDescription">${exhibition.description}</p>
            <div class="exhibitionFooter">
                <span><i class="fas fa-images"></i> ${exhibition.artworks} artworks</span>
                <span><i class="fas fa-eye"></i> ${exhibition.visitors.toLocaleString()} visitors</span>
            </div>
        `;
        exhibitionsList.appendChild(exhibitionCard);
    });
}

// Featured content functions
function toggleFeaturedItem(containerId, itemId) {
    const sectionName = containerId === 'homepageFeatured' ? 'homepage' : 'trending';
    const item = featuredContent[sectionName].find(item => item.id === itemId);
    
    if (item) {
        item.selected = !item.selected;
        loadFeaturedSection(containerId, featuredContent[sectionName]);
    }
}

function selectAllFeatured(containerId) {
    const sectionName = containerId === 'homepageFeatured' ? 'homepage' : 'trending';
    featuredContent[sectionName].forEach(item => item.selected = true);
    loadFeaturedSection(containerId, featuredContent[sectionName]);
}

function clearAllFeatured(containerId) {
    const sectionName = containerId === 'homepageFeatured' ? 'homepage' : 'trending';
    featuredContent[sectionName].forEach(item => item.selected = false);
    loadFeaturedSection(containerId, featuredContent[sectionName]);
}

function saveFeatured(containerId) {
    const sectionName = containerId === 'homepageFeatured' ? 'homepage' : 'trending';
    const selectedCount = featuredContent[sectionName].filter(item => item.selected).length;
    
    showNotification(`Featured ${sectionName} updated - ${selectedCount} items selected`, 'success');
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

function formatStatus(status) {
    return status.charAt(0).toUpperCase() + status.slice(1);
}

function formatGalleryType(type) {
    return type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
}

function formatExhibitionType(type) {
    return type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
}

function formatPartnershipType(type) {
    return type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
}

function formatDateRange(startDate, endDate) {
    const start = new Date(startDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    const end = new Date(endDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    return `${start} - ${end}`;
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
