// DOM Elements
const searchInput = document.getElementById('searchInput');
const artworksGrid = document.getElementById('artworksGrid');
const artworkCount = document.getElementById('artworkCount');

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
  setupEventListeners();
  updateArtworkCount();
});

// Setup event listeners when DOM is loaded
function setupEventListeners() {
  // Search input event listener
  if (searchInput) {
    searchInput.addEventListener('input', debounce(() => {
      applyFilters();
    }, 300));

    searchInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        applyFilters();
      }
    });
  }

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
};

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

// Apply filters function
function applyFilters() {
  const searchTerm = searchInput.value.toLowerCase().trim();
  const artworkCards = document.querySelectorAll('.artwork-card');
  let visibleCount = 0;

  artworkCards.forEach(card => {
    const title = card.querySelector('.artwork-title').textContent.toLowerCase();
    const artist = card.querySelector('.artwork-artist').textContent.toLowerCase();
    const category = card.querySelector('.artwork-category').textContent.toLowerCase();
    const description = card.querySelector('.artwork-description').textContent.toLowerCase();

    const matchesSearch = !searchTerm || 
        title.includes(searchTerm) ||
        artist.includes(searchTerm) ||
        category.includes(searchTerm) ||
        description.includes(searchTerm);

    if (matchesSearch) {
      card.style.display = '';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });

  updateArtworkCount(visibleCount);
}

// Update artwork count display
function updateArtworkCount(count) {
  const totalCount = document.querySelectorAll('.artwork-card').length;
  if (artworkCount) {
    if (!count || count === totalCount) {
      artworkCount.textContent = totalCount;
    } else {
      artworkCount.textContent = `${count} of ${totalCount}`;
    }
  }
}

// Debounce function to limit how often a function is called
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

// Clear all filters
function clearAllFilters() {
  if (searchInput) {
    searchInput.value = '';
  }
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
// Export functions for global access
window.applyFilters = applyFilters;
window.clearAllFilters = clearAllFilters;
// Export functions for global access
window.applyFilters = applyFilters;
window.clearAllFilters = clearAllFilters;

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