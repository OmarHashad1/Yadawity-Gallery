// DOM Elements
const artworkSearchInput = document.getElementById('searchInput');
const artworksGrid = document.getElementById('artworksGrid');
const artworkCount = document.getElementById('artworkCount');

// Filter state for artwork page
let artworkActiveFilters = {
  search: '',
  category: 'all',
  sort: 'featured',
  minPrice: '',
  maxPrice: ''
};

// Initialize filters
document.addEventListener('DOMContentLoaded', () => {
  setupArtworkEventListeners();
});

// Setup event listeners when DOM is loaded
function setupArtworkEventListeners() {
  // Search input event listener
  if (artworkSearchInput) {
    artworkSearchInput.addEventListener('input', debounce(() => {
      applyFilters();
    }, 300));

    artworkSearchInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        applyFilters();
      }
    });
  }

  // Category filter
  const categoryFilter = document.getElementById('categoryFilter');
  if (categoryFilter) {
    categoryFilter.addEventListener('change', (e) => {
      artworkActiveFilters.category = e.target.value;
      updateArtworkActiveFilters();
      applyFilters();
    });
  }

  // Sort filter
  const sortByFilter = document.getElementById('sortBy');
  if (sortByFilter) {
    sortByFilter.addEventListener('change', (e) => {
      artworkActiveFilters.sort = e.target.value;
      updateArtworkActiveFilters();
      applyFilters();
    });
  }

  // Price range filters
  const minPriceFilter = document.getElementById('minPrice');
  if (minPriceFilter) {
    minPriceFilter.addEventListener('change', (e) => {
      artworkActiveFilters.minPrice = e.target.value;
      updateArtworkActiveFilters();
      applyFilters();
    });
  }

  const maxPriceFilter = document.getElementById('maxPrice');
  if (maxPriceFilter) {
    maxPriceFilter.addEventListener('change', (e) => {
      artworkActiveFilters.maxPrice = e.target.value;
      updateArtworkActiveFilters();
      applyFilters();
    });
  }
}

// Apply filters function - Updated to work with API
function applyFilters() {
  if (!artworkSearchInput) return;
  
  // Update search term in activeFilters
  artworkActiveFilters.search = artworkSearchInput.value.trim();
  
  // Update active filters display
  updateArtworkActiveFilters();
  
  // Load artworks with current filters
  loadArtworksWithFilters(1); // Reset to page 1 when applying filters
}

// Clear all filters
function clearAllFilters() {
  // Reset filter state
  artworkActiveFilters = {
    search: '',
    category: 'all',
    sort: 'featured',
    minPrice: '',
    maxPrice: ''
  };
  
  // Reset form inputs
  if (artworkSearchInput) artworkSearchInput.value = '';
  
  const categoryFilter = document.getElementById('categoryFilter');
  if (categoryFilter) categoryFilter.value = 'all';
  
  const sortByFilter = document.getElementById('sortBy');
  if (sortByFilter) sortByFilter.value = 'featured';
  
  const minPriceFilter = document.getElementById('minPrice');
  if (minPriceFilter) minPriceFilter.value = '';
  
  const maxPriceFilter = document.getElementById('maxPrice');
  if (maxPriceFilter) maxPriceFilter.value = '';
  
  // Update display and apply filters
  updateArtworkActiveFilters();
  applyFilters();
}

// Updated loadArtworks function to include filters
function loadArtworksWithFilters(page = 1) {
  if (typeof isLoading !== 'undefined' && isLoading) return;
  
  if (typeof isLoading !== 'undefined') {
    isLoading = true;
  }
  
  const artworksGrid = document.getElementById('artworksGrid');
  const loadingMessage = document.getElementById('loadingMessage');
  
  // Show loading message
  if (loadingMessage) {
    loadingMessage.style.display = 'block';
  }
  
  // Build API URL with filters
  const params = new URLSearchParams({
    page: page,
    limit: typeof itemsPerPage !== 'undefined' ? itemsPerPage : 9
  });
  
  // Add filters to params
  if (artworkActiveFilters.search) {
    params.append('search', artworkActiveFilters.search);
  }
  
  if (artworkActiveFilters.category && artworkActiveFilters.category !== 'all') {
    params.append('category', artworkActiveFilters.category);
  }
  
  if (artworkActiveFilters.sort && artworkActiveFilters.sort !== 'featured') {
    params.append('sort_by', artworkActiveFilters.sort);
  }
  
  if (artworkActiveFilters.minPrice) {
    params.append('min_price', artworkActiveFilters.minPrice);
  }
  
  if (artworkActiveFilters.maxPrice) {
    params.append('max_price', artworkActiveFilters.maxPrice);
  }
  
  const apiUrl = `./API/getAllArtworks.php?${params.toString()}`;
  
  fetch(apiUrl)
  .then(response => response.json())
  .then(data => {
    if (data.success && data.data) {
      if (typeof currentPage !== 'undefined') {
        currentPage = page;
      }
      
      // Call the render functions if they exist
      if (typeof renderArtworks === 'function') {
        renderArtworks(data.data);
      }
      if (typeof updatePagination === 'function') {
        updatePagination(data.pagination || {}, data.total_count || 0);
      }
      if (typeof updateArtworkCount === 'function') {
        updateArtworkCount(data.returned_count || 0, data.total_count || 0);
      }
    } else {
      if (typeof showError === 'function') {
        showError('No artworks available', data.message);
      }
    }
  })
  .catch(error => {
    console.error('Error loading artworks:', error);
    if (typeof showError === 'function') {
      showError('Unable to load artworks', 'Please check your connection and try again.');
    }
  })
  .finally(() => {
    if (typeof isLoading !== 'undefined') {
      isLoading = false;
    }
    if (loadingMessage) {
      loadingMessage.style.display = 'none';
    }
  });
}

// Update active filters display
function updateArtworkActiveFilters() {
  const activeFiltersContainer = document.getElementById('activeFilters');
  if (!activeFiltersContainer) return;
  
  activeFiltersContainer.innerHTML = '';

  // Category filter tag
  if (artworkActiveFilters.category !== 'all') {
    addFilterTag('Category', artworkActiveFilters.category, () => {
      artworkActiveFilters.category = 'all';
      const categoryFilter = document.getElementById('categoryFilter');
      if (categoryFilter) categoryFilter.value = 'all';
      updateArtworkActiveFilters();
      applyFilters();
    });
  }

  // Price range filter tag
  if (artworkActiveFilters.minPrice || artworkActiveFilters.maxPrice) {
    const priceText = `$${artworkActiveFilters.minPrice || '0'} - $${artworkActiveFilters.maxPrice || '∞'}`;
    addFilterTag('Price', priceText, () => {
      artworkActiveFilters.minPrice = '';
      artworkActiveFilters.maxPrice = '';
      const minPriceFilter = document.getElementById('minPrice');
      const maxPriceFilter = document.getElementById('maxPrice');
      if (minPriceFilter) minPriceFilter.value = '';
      if (maxPriceFilter) maxPriceFilter.value = '';
      updateArtworkActiveFilters();
      applyFilters();
    });
  }

  // Sort filter tag
  if (artworkActiveFilters.sort !== 'featured') {
    const sortText = artworkActiveFilters.sort.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    addFilterTag('Sort', sortText, () => {
      artworkActiveFilters.sort = 'featured';
      const sortByFilter = document.getElementById('sortBy');
      if (sortByFilter) sortByFilter.value = 'featured';
      updateArtworkActiveFilters();
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

// Debounce function to limit API calls
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

// Override the main loadArtworks function to use filters
if (typeof loadArtworks === 'function') {
  const originalLoadArtworks = loadArtworks;
  loadArtworks = function(page = 1) {
    loadArtworksWithFilters(page);
  };
}
