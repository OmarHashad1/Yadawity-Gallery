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

// Pagination variables
let currentPage = 1;
let totalPages = 1;
let totalCount = 0;
let isLoading = false;
const itemsPerPage = 9;

// Initialize filters and setup when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  setupArtworkEventListeners();
  
  // Suppress image loading errors globally
  window.addEventListener('error', function(e) {
    if (e.target && e.target.tagName === 'IMG') {
      // This is an image loading error, suppress it
      e.preventDefault();
      e.stopPropagation();
      return false;
    }
  }, true);
  
  loadArtworks(1);
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

// Update active filters display
function updateArtworkActiveFilters() {
  const activeFiltersContainer = document.getElementById('activeFilters');
  const activeFiltersSection = document.getElementById('activeFiltersSection');
  
  if (!activeFiltersContainer) return;
  
  activeFiltersContainer.innerHTML = '';
  let hasActiveFilters = false;

  // Search filter tag
  if (artworkActiveFilters.search) {
    addFilterTag('Search', `"${artworkActiveFilters.search}"`, () => {
      artworkActiveFilters.search = '';
      if (artworkSearchInput) artworkSearchInput.value = '';
      updateArtworkActiveFilters();
      applyFilters();
    });
    hasActiveFilters = true;
  }

  // Category filter tag
  if (artworkActiveFilters.category !== 'all') {
    addFilterTag('Category', artworkActiveFilters.category, () => {
      artworkActiveFilters.category = 'all';
      const categoryFilter = document.getElementById('categoryFilter');
      if (categoryFilter) categoryFilter.value = 'all';
      updateArtworkActiveFilters();
      applyFilters();
    });
    hasActiveFilters = true;
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
    hasActiveFilters = true;
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
    hasActiveFilters = true;
  }

  // Show/hide the active filters section
  if (activeFiltersSection) {
    activeFiltersSection.style.display = hasActiveFilters ? 'block' : 'none';
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

// Apply filters function - Updated to work with API
function applyFilters() {
  if (!artworkSearchInput) return;
  
  // Update search term in activeFilters
  artworkActiveFilters.search = artworkSearchInput.value.trim();
  
  // Load artworks with current filters
  loadArtworks(1); // Reset to page 1 when applying filters
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

// Functions for artwork interaction
function viewArtwork(artworkId) {
  // Redirect to artwork detail page or open modal
  window.location.href = `product-preview.php?id=${artworkId}`;
}

function addToCart(artworkId) {
  // Add artwork to cart
  console.log('Adding artwork to cart:', artworkId);
  
  // You can implement cart functionality here
  fetch('./API/addToCart.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      artwork_id: artworkId,
      quantity: 1
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('Artwork added to cart successfully!');
      // Update cart icon or counter if needed
    } else {
      alert('Failed to add artwork to cart: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while adding to cart');
  });
}

function addToWishlist(artworkId) {
  // Add artwork to wishlist
  console.log('Adding artwork to wishlist:', artworkId);
  
  // You can implement wishlist functionality here
  fetch('./API/addToWishlist.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      artwork_id: artworkId
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('Artwork added to wishlist successfully!');
      // Update wishlist icon
      const button = event.target.closest('.wishlist-btn');
      button.innerHTML = '<i class="fas fa-heart"></i>';
      button.style.color = 'red';
    } else {
      alert('Failed to add artwork to wishlist: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while adding to wishlist');
  });
}

// Load artworks with pagination and filters
function loadArtworks(page = 1) {
  if (isLoading) return;
  
  isLoading = true;
  const artworksGrid = document.getElementById('artworksGrid');
  const loadingMessage = document.getElementById('loadingMessage');
  
  // Show loading message
  if (loadingMessage) {
    loadingMessage.style.display = 'block';
  }
  
  // Build API URL with pagination and filters
  const params = new URLSearchParams({
    page: page,
    limit: itemsPerPage
  });
  
  // Add filters if they exist
  if (typeof artworkActiveFilters !== 'undefined') {
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
  }
  
  const apiUrl = `./API/getAllArtworks.php?${params.toString()}`;
  console.log('Loading artworks from:', apiUrl);
  
  fetch(apiUrl)
  .then(response => {
    console.log('API Response status:', response.status);
    return response.json();
  })
  .then(data => {
    console.log('API Response data:', data);
    if (data.success && data.data) {
      currentPage = page; // Update currentPage FIRST
      renderArtworks(data.data);
      updatePagination(data.pagination || {}, data.total_count || 0);
      updateArtworkCount(data.total_count || 0);
    } else {
      console.log('API returned no data or error:', data);
      showError('No artworks available', data.message);
    }
  })
  .catch(error => {
    console.error('Error loading artworks:', error);
    showError('Unable to load artworks', 'Please check your connection and try again.');
  })
  .finally(() => {
    isLoading = false;
    if (loadingMessage) {
      loadingMessage.style.display = 'none';
    }
  });
}

// Render artworks to the grid
function renderArtworks(artworks) {
  const artworksGrid = document.getElementById('artworksGrid');
  const loadingMessage = document.getElementById('loadingMessage');
  
  // Clear existing content
  artworksGrid.innerHTML = '';
  
  artworks.forEach(artwork => {
    const card = createArtworkCard(artwork);
    artworksGrid.appendChild(card);
  });
  
  // Re-add loading message for future use
  if (loadingMessage) {
    artworksGrid.appendChild(loadingMessage);
    loadingMessage.style.display = 'none';
  }
}

// Create individual artwork card
function createArtworkCard(artwork) {
  const card = document.createElement('div');
  card.className = 'enhanced-artwork-card';
  card.setAttribute('data-category', artwork.type);
  card.setAttribute('data-price', artwork.price);
  
  // Only show image if artwork has image_src and it's not missing
  let imageElement = '';
  
  if (artwork.image_src && !artwork.image_missing) {
    imageElement = `
      <img src="${escapeHtml(artwork.image_src)}" 
           alt="${escapeHtml(artwork.title)}" 
           class="enhanced-artwork-image" 
           loading="lazy" 
           onerror="this.src='/image/placeholder-artwork.jpg'"
           data-artwork-id="${artwork.artwork_id}">
    `;
  } else {
    imageElement = `
      <img src="/image/placeholder-artwork.jpg" 
           alt="${escapeHtml(artwork.title)}" 
           class="enhanced-artwork-image" 
           loading="lazy" 
           data-artwork-id="${artwork.artwork_id}">
    `;
  }
  
  // Generate action button
  let actionButton = '';
  if (artwork.is_available && !artwork.on_auction) {
    actionButton = `<button class="enhanced-add-to-cart" onclick="addToCart(${artwork.artwork_id})">Add to Cart</button>`;
  } else {
    actionButton = `<button class="enhanced-add-to-cart" disabled>${escapeHtml(artwork.status_text)}</button>`;
  }
  
  card.innerHTML = `
    <div class="artwork-image-container">
      ${imageElement}
      <div class="artwork-overlay">
        <div class="quick-actions">
          <button class="quick-action-btn" onclick="viewArtwork(${artwork.artwork_id})">
            <i class="fas fa-eye"></i>
          </button>
        </div>
      </div>
    </div>
    <div class="enhanced-artwork-info">
      <div class="artwork-category">${escapeHtml(artwork.category)}</div>
      <h3 class="enhanced-artwork-title">${escapeHtml(artwork.title)}</h3>
      <p class="enhanced-artwork-artist">${escapeHtml(artwork.artist.display_name)}</p>
      <p class="enhanced-artwork-price">${escapeHtml(artwork.formatted_price)}</p>
      <p class="artwork-dimensions">${escapeHtml(artwork.dimensions)}</p>
      <p class="enhanced-artwork-description">${escapeHtml(artwork.description)}</p>
      <div class="artwork-actions">
        ${actionButton}
        <button class="wishlist-btn" onclick="addToWishlist(${artwork.artwork_id})">
          <i class="far fa-heart"></i>
        </button>
      </div>
    </div>
  `;
  
  // Add event listeners for image error handling
  const img = card.querySelector('.enhanced-artwork-image');
  if (img) {
    img.addEventListener('error', (e) => {
      // Prevent the error from appearing in console
      e.preventDefault();
      e.stopPropagation();
      handleImageError(img, artwork.artwork_id);
      return false;
    });
  }
  
  return card;
}

// Update pagination controls
function updatePagination(pagination, total) {
  totalPages = pagination.total_pages || 1;
  totalCount = total;
  
  const paginationNumbers = document.getElementById('paginationNumbers');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  
  if (!paginationNumbers) return;
  
  // Update button states
  if (prevBtn) {
    prevBtn.disabled = !pagination.has_previous;
  }
  if (nextBtn) {
    nextBtn.disabled = !pagination.has_next;
  }
  
  // Generate pagination numbers
  paginationNumbers.innerHTML = generatePaginationNumbers(currentPage, totalPages);
}

// Generate pagination number buttons
function generatePaginationNumbers(current, total) {
  let html = '';
  
  if (total <= 7) {
    // Show all pages if total is 7 or less
    for (let i = 1; i <= total; i++) {
      html += `<button class="pagination-number ${i === current ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
    }
  } else {
    // Show condensed pagination for more than 7 pages
    if (current <= 4) {
      // Show first 5 pages
      for (let i = 1; i <= 5; i++) {
        html += `<button class="pagination-number ${i === current ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
      }
      html += '<span class="pagination-dots">...</span>';
      html += `<button class="pagination-number" onclick="goToPage(${total})">${total}</button>`;
    } else if (current >= total - 3) {
      // Show last 5 pages
      html += `<button class="pagination-number" onclick="goToPage(1)">1</button>`;
      html += '<span class="pagination-dots">...</span>';
      for (let i = total - 4; i <= total; i++) {
        html += `<button class="pagination-number ${i === current ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
      }
    } else {
      // Show middle pages
      html += `<button class="pagination-number" onclick="goToPage(1)">1</button>`;
      html += '<span class="pagination-dots">...</span>';
      for (let i = current - 1; i <= current + 1; i++) {
        html += `<button class="pagination-number ${i === current ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
      }
      html += '<span class="pagination-dots">...</span>';
      html += `<button class="pagination-number" onclick="goToPage(${total})">${total}</button>`;
    }
  }
  
  return html;
}

// Update artwork count display
function updateArtworkCount(total) {
  const countElement = document.getElementById('artworkCount');
  if (countElement) {
    const startRange = ((currentPage - 1) * itemsPerPage) + 1;
    const endRange = Math.min(currentPage * itemsPerPage, total);
    countElement.textContent = `${startRange}-${endRange} of ${total}`;
  }
}

// Show error message
function showError(title, message) {
  const artworksGrid = document.getElementById('artworksGrid');
  artworksGrid.innerHTML = `
    <div class="error-message">
      <div class="message-content">
        <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: #e74c3c; margin-bottom: 1rem;"></i>
        <h3>${escapeHtml(title)}</h3>
        <p>${escapeHtml(message)}</p>
        <button class="retry-btn" onclick="loadArtworks(${currentPage})">Retry</button>
      </div>
    </div>
  `;
}

// Pagination functions
function goToPage(page) {
  if (page !== currentPage && page >= 1 && page <= totalPages) {
    loadArtworks(page);
    
    // Scroll to the gallery header section
    const gallerySection = document.querySelector('.artwork-gallery');
    if (gallerySection) {
      gallerySection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }
}

function previousPage() {
  if (currentPage > 1) {
    goToPage(currentPage - 1);
  }
}

function nextPage() {
  if (currentPage < totalPages) {
    goToPage(currentPage + 1);
  }
}

// Utility function to escape HTML
function escapeHtml(text) {
  if (text == null) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Handle image loading errors - show no image icon instead of hiding
function handleImageError(img, artworkId) {
  // Replace the failed image with a no-image placeholder
  const container = img.parentElement;
  if (container) {
    // Create a proper placeholder div
    const placeholder = document.createElement('div');
    placeholder.className = 'no-image-available';
    placeholder.style.cssText = `
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      height: 200px;
      background-color: #f5f5f5;
      border: 2px dashed #ddd;
      color: #999;
      font-size: 14px;
    `;
    placeholder.innerHTML = `
      <div style="font-size: 48px; margin-bottom: 10px;">🖼️</div>
      <div>Image not available</div>
    `;
    
    // Replace the img with the placeholder
    container.replaceChild(placeholder, img);
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

// Export functions for global access
window.applyFilters = applyFilters;
window.clearAllFilters = clearAllFilters;
window.viewArtwork = viewArtwork;
window.addToCart = addToCart;
window.addToWishlist = addToWishlist;
window.goToPage = goToPage;
window.previousPage = previousPage;
window.nextPage = nextPage;