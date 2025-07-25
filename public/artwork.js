// Filter state
let activeFilters = {
  search: '',
  category: 'all',
  sort: 'featured',
  minPrice: '',
  maxPrice: ''
};

// Initialize filters
document.addEventListener('DOMContentLoaded', () => {
  // Search functionality
  const searchInput = document.getElementById('searchInput');
  const searchBtn = document.querySelector('.search-btn');

  searchInput.addEventListener('input', (e) => {
    activeFilters.search = e.target.value.toLowerCase();
    applyFilters();
  });

  searchBtn.addEventListener('click', () => {
    applyFilters();
  });

  searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
      applyFilters();
    }
  });

  // Category filter
  document.getElementById('categoryFilter').addEventListener('change', (e) => {
    activeFilters.category = e.target.value;
    updateActiveFilters();
    applyFilters();
  });

  // Sort filter
  document.getElementById('sortBy').addEventListener('change', (e) => {
    activeFilters.sort = e.target.value;
    updateActiveFilters();
    applyFilters();
  });

  // Price range filters
  document.getElementById('minPrice').addEventListener('change', (e) => {
    activeFilters.minPrice = e.target.value;
    updateActiveFilters();
    applyFilters();
  });

  document.getElementById('maxPrice').addEventListener('change', (e) => {
    activeFilters.maxPrice = e.target.value;
    updateActiveFilters();
    applyFilters();
  });
});

// Update active filters display
function updateActiveFilters() {
  const activeFiltersContainer = document.getElementById('activeFilters');
  activeFiltersContainer.innerHTML = '';

  // Category filter tag
  if (activeFilters.category !== 'all') {
    addFilterTag('Category', activeFilters.category, () => {
      activeFilters.category = 'all';
      document.getElementById('categoryFilter').value = 'all';
      updateActiveFilters();
      applyFilters();
    });
  }

  // Price range filter tag
  if (activeFilters.minPrice || activeFilters.maxPrice) {
    const priceText = `$${activeFilters.minPrice || '0'} - $${activeFilters.maxPrice || '∞'}`;
    addFilterTag('Price', priceText, () => {
      activeFilters.minPrice = '';
      activeFilters.maxPrice = '';
      document.getElementById('minPrice').value = '';
      document.getElementById('maxPrice').value = '';
      updateActiveFilters();
      applyFilters();
    });
  }

  // Sort filter tag
  if (activeFilters.sort !== 'featured') {
    const sortText = activeFilters.sort.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    addFilterTag('Sort', sortText, () => {
      activeFilters.sort = 'featured';
      document.getElementById('sortBy').value = 'featured';
      updateActiveFilters();
      applyFilters();
    });
  }
}

// Add a filter tag to the active filters container
function addFilterTag(type, value, removeCallback) {
  const activeFiltersContainer = document.getElementById('activeFilters');
  
  const filterTag = document.createElement('div');
  filterTag.className = 'filter-tag';
  filterTag.innerHTML = `
    <span>${type}: ${value}</span>
    <span class="remove-filter">×</span>
  `;

  filterTag.querySelector('.remove-filter').addEventListener('click', removeCallback);
  activeFiltersContainer.appendChild(filterTag);
}

// Clear all filters
function clearAllFilters() {
  activeFilters = {
    search: '',
    category: 'all',
    sort: 'featured',
    minPrice: '',
    maxPrice: ''
  };

  // Reset form elements
  document.getElementById('categoryFilter').value = 'all';
  document.getElementById('sortBy').value = 'featured';
  document.getElementById('minPrice').value = '';
  document.getElementById('maxPrice').value = '';

  updateActiveFilters();
  applyFilters();
}

// Apply filters to artwork grid
function applyFilters() {
  const artworks = document.querySelectorAll('.enhanced-artwork-card');
  let visibleCount = 0;

  artworks.forEach(artwork => {
    let visible = true;

    // Search filter
    if (activeFilters.search) {
      const title = artwork.querySelector('.enhanced-artwork-title').textContent.toLowerCase();
      const artist = artwork.querySelector('.enhanced-artwork-artist').textContent.toLowerCase();
      const category = artwork.dataset.category.toLowerCase();
      const description = artwork.querySelector('.enhanced-artwork-description').textContent.toLowerCase();
      
      visible = title.includes(activeFilters.search) || 
                artist.includes(activeFilters.search) || 
                category.includes(activeFilters.search) ||
                description.includes(activeFilters.search);
    }

    // Category filter
    if (visible && activeFilters.category !== 'all') {
      visible = artwork.dataset.category === activeFilters.category;
    }

    // Price filter
    const price = parseInt(artwork.dataset.price);
    if (activeFilters.minPrice && price < parseInt(activeFilters.minPrice)) {
      visible = false;
    }
    if (activeFilters.maxPrice && price > parseInt(activeFilters.maxPrice)) {
      visible = false;
    }

    artwork.style.display = visible ? '' : 'none';
    if (visible) visibleCount++;
  });

  // Update artwork count
  document.getElementById('artworkCount').textContent = visibleCount;

  // Sort artworks
  sortArtworks();
}

// Sort artworks based on current sort selection
function sortArtworks() {
  const artworksGrid = document.getElementById('artworksGrid');
  const artworks = Array.from(artworksGrid.children);

  artworks.sort((a, b) => {
    switch (activeFilters.sort) {
      case 'price-low':
        return parseInt(a.dataset.price) - parseInt(b.dataset.price);
      case 'price-high':
        return parseInt(b.dataset.price) - parseInt(a.dataset.price);
      case 'newest':
        // You would need to add data-date attributes to implement this
        return 0;
      case 'artist':
        const artistA = a.querySelector('.enhanced-artwork-artist').textContent;
        const artistB = b.querySelector('.enhanced-artwork-artist').textContent;
        return artistA.localeCompare(artistB);
      default:
        return 0;
    }
  });

  // Re-append sorted artworks
  artworks.forEach(artwork => {
    artworksGrid.appendChild(artwork);
  });
}