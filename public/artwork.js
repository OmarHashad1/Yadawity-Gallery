const artworks = [
    {
      id: 1,
      title: "The Noble Portrait",
      artist: "Lady Catherine Pemberton",
      category: "portraits",
      price: 45000,
      image: "https://picsum.photos/600/500?random=1",
      dimensions: "24\" × 36\"",
      description: "A masterful oil painting capturing the essence of aristocratic grace and dignity through classical techniques.",
      badges: ["featured", "new"],
      height: 500
    },
    {
      id: 2,
      title: "Urban Reflection",
      artist: "Hassan Mohamed",
      category: "abstract",
      price: 28000,
      image: "https://picsum.photos/600/320?random=2",
      dimensions: "18\" × 24\"",
      description: "Contemporary abstract piece exploring urban life through bold colors and dynamic compositions.",
      badges: ["limited"],
      height: 320
    },
    {
      id: 3,
      title: "Serene Landscape",
      artist: "Amira Hassan",
      category: "landscapes",
      price: 32000,
      image: "https://picsum.photos/600/400?random=3",
      dimensions: "30\" × 40\"",
      description: "A breathtaking landscape that captures the tranquil beauty of nature with exceptional detail.",
      badges: ["featured"],
      height: 400
    },
    {
      id: 4,
      title: "Motion Study",
      artist: "Ahmed Mostafa",
      category: "abstract",
      price: 15000,
      image: "https://picsum.photos/600/280?random=4",
      dimensions: "16\" × 20\"",
      description: "Dynamic abstract composition exploring movement and energy through gestural brushwork.",
      badges: ["new"],
      height: 280
    },
    {
      id: 5,
      title: "The Scholar",
      artist: "Omar Farouk",
      category: "portraits",
      price: 38000,
      image: "https://picsum.photos/600/450?random=5",
      dimensions: "20\" × 30\"",
      description: "Intimate portrait showcasing intellectual depth and contemplative nature through masterful technique.",
      badges: ["featured"],
      height: 450
    },
    {
      id: 6,
      title: "Bronze Elegance",
      artist: "Mariam Salah",
      category: "sculptures",
      price: 55000,
      image: "https://picsum.photos/600/600?random=6",
      dimensions: "12\" × 8\" × 20\"",
      description: "Exquisite bronze sculpture demonstrating classical form and contemporary sensibility.",
      badges: ["limited", "featured"],
      height: 600
    },
    {
      id: 7,
      title: "Digital Dreams",
      artist: "Yasmin Ali",
      category: "mixed-media",
      price: 22000,
      image: "https://picsum.photos/600/350?random=7",
      dimensions: "24\" × 32\"",
      description: "Innovative mixed-media work combining traditional painting with digital elements.",
      badges: ["new"],
      height: 350
    },
    {
      id: 8,
      title: "Captured Moment",
      artist: "Karim Hassan",
      category: "photography",
      price: 8000,
      image: "https://picsum.photos/600/300?random=8",
      dimensions: "16\" × 24\"",
      description: "Fine art photography capturing a fleeting moment of human emotion and natural beauty.",
      badges: [],
      height: 300
    },
    {
      id: 9,
      title: "Mountain Majesty",
      artist: "Nadia Mahmoud",
      category: "landscapes",
      price: 42000,
      image: "https://picsum.photos/600/380?random=9",
      dimensions: "36\" × 48\"",
      description: "Spectacular landscape painting showcasing the raw power and beauty of mountain wilderness.",
      badges: ["featured"],
      height: 380
    },
    {
      id: 10,
      title: "Abstract Symphony",
      artist: "Mahmoud Saeed",
      category: "abstract",
      price: 35000,
      image: "https://picsum.photos/600/420?random=10",
      dimensions: "28\" × 36\"",
      description: "Vibrant abstract composition that explores color relationships and musical rhythms in visual form.",
      badges: ["limited"],
      height: 420
    },
    {
      id: 11,
      title: "The Artisan",
      artist: "Fatima Ahmed",
      category: "portraits",
      price: 29000,
      image: "https://picsum.photos/600/480?random=11",
      dimensions: "18\" × 28\"",
      description: "Portrait celebrating the dignity of craft and the wisdom found in skilled hands.",
      badges: ["new"],
      height: 480
    },
    {
      id: 12,
      title: "Mixed Emotions",
      artist: "Ali Rashad",
      category: "mixed-media",
      price: 33000,
      image: "https://picsum.photos/600/360?random=12",
      dimensions: "22\" × 30\"",
      description: "Complex mixed-media work exploring human psychology through layered materials and techniques.",
      badges: ["featured"],
      height: 360
    }
  ];

  let currentArtworks = [...artworks];
  let displayedCount = 8;
  let activeCategory = 'all';
  let activeSortBy = 'featured';

  // Initialize the gallery
  function initGallery() {
    renderArtworks();
    setupFilters();
    setupSorting();
    setupPriceFilter();
    setupLoadMore();
    setupInteractions();
    updateStats();
  }

  // Render artworks in the gallery
  function renderArtworks() {
    const grid = document.getElementById('artworksGrid');
    grid.innerHTML = '';
    
    const artworksToShow = currentArtworks.slice(0, displayedCount);
    
    artworksToShow.forEach((artwork, index) => {
      const card = createArtworkCard(artwork, index);
      grid.appendChild(card);
    });

    // Setup card interactions
    setupCardInteractions();
  }

  // Create enhanced artwork card
  function createArtworkCard(artwork, index) {
    const card = document.createElement('div');
    card.className = 'enhanced-artwork-card';
    card.style.animationDelay = `${index * 0.1}s`;
    card.dataset.category = artwork.category;
    card.dataset.price = artwork.price;

    card.innerHTML = `
      <div class="artwork-image-container">
        <img class="enhanced-artwork-image" 
             src="${artwork.image}" 
             alt="${artwork.title}" 
             style="height: ${artwork.height}px; object-fit: cover;">
        
        <div class="artwork-overlay">
          <div class="quick-actions">
            <button class="quick-action-btn" title="Quick View" data-action="view" data-id="${artwork.id}">
              <i class="fas fa-eye"></i>
            </button>
            <button class="quick-action-btn" title="Add to Wishlist" data-action="wishlist" data-id="${artwork.id}">
              <i class="fas fa-heart"></i>
            </button>
            <button class="quick-action-btn" title="Share" data-action="share" data-id="${artwork.id}">
              <i class="fas fa-share-alt"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="enhanced-artwork-info">
        <div class="artwork-category">${artwork.category.replace('-', ' ')}</div>
        <h3 class="enhanced-artwork-title">${artwork.title}</h3>
        <p class="enhanced-artwork-artist">by ${artwork.artist}</p>
        <p class="enhanced-artwork-price">$${artwork.price.toLocaleString()}</p>
        <p class="artwork-dimensions">${artwork.dimensions}</p>
        <p class="enhanced-artwork-description">${artwork.description}</p>
        
        <div class="artwork-actions">
          <button class="enhanced-add-to-cart" data-id="${artwork.id}">
            Add to Cart
          </button>
          <button class="wishlist-btn" data-id="${artwork.id}" title="Add to Wishlist">
            <i class="fas fa-heart"></i>
          </button>
        </div>
      </div>
    `;

    return card;
  }

  // Setup filter functionality
  function setupFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        if (btn.dataset.category) {
          // Remove active class from all buttons
          filterButtons.forEach(b => b.classList.remove('active'));
          // Add active class to clicked button
          btn.classList.add('active');
          
          activeCategory = btn.dataset.category;
          applyFilters();
        }
      });
    });
  }

  // Setup sorting functionality
  function setupSorting() {
    const sortSelect = document.getElementById('sortBy');
    
    sortSelect.addEventListener('change', () => {
      activeSortBy = sortSelect.value;
      applySorting();
    });
  }

  // Setup price filtering
  function setupPriceFilter() {
    const applyBtn = document.getElementById('applyPriceFilter');
    
    applyBtn.addEventListener('click', () => {
      applyFilters();
    });
  }

  // Apply filters and sorting
  function applyFilters() {
    let filtered = [...artworks];
    
    // Apply category filter
    if (activeCategory !== 'all') {
      filtered = filtered.filter(artwork => artwork.category === activeCategory);
    }
    
    // Apply price filter
    const minPrice = parseInt(document.getElementById('minPrice').value) || 0;
    const maxPrice = parseInt(document.getElementById('maxPrice').value) || Infinity;
    
    filtered = filtered.filter(artwork => 
      artwork.price >= minPrice && artwork.price <= maxPrice
    );
    
    currentArtworks = filtered;
    applySorting();
  }

  // Apply sorting
  function applySorting() {
    switch(activeSortBy) {
      case 'price-low':
        currentArtworks.sort((a, b) => a.price - b.price);
        break;
      case 'price-high':
        currentArtworks.sort((a, b) => b.price - a.price);
        break;
      case 'newest':
        currentArtworks.sort((a, b) => b.id - a.id);
        break;
      case 'artist':
        currentArtworks.sort((a, b) => a.artist.localeCompare(b.artist));
        break;
      case 'featured':
      default:
        currentArtworks.sort((a, b) => {
          const aFeatured = a.badges.includes('featured') ? 1 : 0;
          const bFeatured = b.badges.includes('featured') ? 1 : 0;
          return bFeatured - aFeatured;
        });
        break;
    }
    
    displayedCount = Math.min(8, currentArtworks.length);
    renderArtworks();
    updateStats();
    updateLoadMoreButton();
  }

  // Setup load more functionality
  function setupLoadMore() {
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    
    loadMoreBtn.addEventListener('click', () => {
      displayedCount = Math.min(displayedCount + 8, currentArtworks.length);
      renderArtworks();
      updateLoadMoreButton();
      updateStats();
    });
  }

  // Update load more button visibility
  function updateLoadMoreButton() {
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    
    if (displayedCount >= currentArtworks.length) {
      loadMoreBtn.style.display = 'none';
    } else {
      loadMoreBtn.style.display = 'block';
    }
  }

  // Update gallery statistics
  function updateStats() {
    document.getElementById('artworkCount').textContent = Math.min(displayedCount, currentArtworks.length);
    document.getElementById('totalArtworks').textContent = currentArtworks.length;
  }

  // Setup card interactions
  function setupCardInteractions() {
    // Add to cart buttons
    document.querySelectorAll('.enhanced-add-to-cart').forEach(btn => {
      btn.addEventListener('click', function() {
        const artworkId = this.dataset.id;
        const artwork = artworks.find(a => a.id == artworkId);
        
        // Simple feedback animation
        this.textContent = 'Added!';
        this.style.background = '#28a745';
        
        setTimeout(() => {
          this.textContent = 'Add to Cart';
          this.style.background = '';
        }, 2000);
        
        console.log(`Added "${artwork.title}" to cart`);
      });
    });

    // Wishlist buttons
    document.querySelectorAll('.wishlist-btn, .quick-action-btn[data-action="wishlist"]').forEach(btn => {
      btn.addEventListener('click', function() {
        const artworkId = this.dataset.id;
        const artwork = artworks.find(a => a.id == artworkId);
        
        this.style.color = '#e74c3c';
        console.log(`Added "${artwork.title}" to wishlist`);
      });
    });

    // Quick action buttons
    document.querySelectorAll('.quick-action-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const action = this.dataset.action;
        const artworkId = this.dataset.id;
        const artwork = artworks.find(a => a.id == artworkId);
        
        switch(action) {
          case 'view':
            console.log(`Quick view for "${artwork.title}"`);
            break;
          case 'share':
            console.log(`Share "${artwork.title}"`);
            break;
        }
      });
    });
  }

  // Setup general interactions
  function setupInteractions() {
    // Search functionality
    const searchInput = document.getElementById('navbarSearch');
    if (searchInput) {
      searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        if (searchTerm) {
          currentArtworks = artworks.filter(artwork =>
            artwork.title.toLowerCase().includes(searchTerm) ||
            artwork.artist.toLowerCase().includes(searchTerm) ||
            artwork.category.toLowerCase().includes(searchTerm)
          );
        } else {
          currentArtworks = [...artworks];
        }
        displayedCount = Math.min(8, currentArtworks.length);
        renderArtworks();
        updateStats();
        updateLoadMoreButton();
      });
    }
  }

  // Initialize everything when DOM is loaded
  document.addEventListener('DOMContentLoaded', initGallery);