// Sample virtual gallery data
const virtualGalleries = [
  {
    id: 1,
    title: "Cubist Revolution VR",
    artist: "picasso",
    price: 25,
    originalPrice: 35,
    image: "/placeholder.svg?height=220&width=350",
    description: "Step into Picasso's revolutionary cubist world through immersive VR technology",
    duration: "45 min",
    rating: 4.9,
    features: ["VR Compatible", "Interactive", "Audio Guide", "HD Quality"],
    type: "premium",
  },
  {
    id: 2,
    title: "Islamic Art Heritage",
    artist: "mohammed",
    price: 15,
    originalPrice: null,
    image: "/placeholder.svg?height=220&width=350",
    description: "Explore the intricate beauty of Islamic geometric patterns and calligraphy",
    duration: "30 min",
    rating: 4.7,
    features: ["360° View", "Zoom Details", "Historical Context"],
    type: "standard",
  },
  {
    id: 3,
    title: "Contemporary Expressions",
    artist: "nermeen",
    price: 35,
    originalPrice: 45,
    image: "/placeholder.svg?height=220&width=350",
    description: "Modern artistic expressions through digital mediums and interactive installations",
    duration: "60 min",
    rating: 4.8,
    features: ["VR Compatible", "Interactive", "Artist Commentary", "4K Resolution"],
    type: "premium",
  },
  {
    id: 4,
    title: "Renaissance Masterpieces",
    artist: "leonardo",
    price: 40,
    originalPrice: 50,
    image: "/placeholder.svg?height=220&width=350",
    description: "Walk through the halls of Renaissance art with Leonardo's greatest works",
    duration: "75 min",
    rating: 5.0,
    features: ["VR Compatible", "Ultra HD", "Expert Commentary", "Interactive Elements"],
    type: "premium",
  },
  {
    id: 5,
    title: "Impressionist Gardens",
    artist: "monet",
    price: 20,
    originalPrice: null,
    image: "/placeholder.svg?height=220&width=350",
    description: "Experience Monet's garden and water lilies in a peaceful virtual environment",
    duration: "40 min",
    rating: 4.6,
    features: ["360° View", "Seasonal Changes", "Nature Sounds"],
    type: "standard",
  },
  {
    id: 6,
    title: "Abstract Dimensions",
    artist: "picasso",
    price: 30,
    originalPrice: null,
    image: "/placeholder.svg?height=220&width=350",
    description: "Dive into abstract art forms in a three-dimensional virtual space",
    duration: "50 min",
    rating: 4.5,
    features: ["VR Compatible", "3D Navigation", "Color Theory Guide"],
    type: "premium",
  },
  {
    id: 7,
    title: "Calligraphy Masterclass",
    artist: "mohammed",
    price: 18,
    originalPrice: 25,
    image: "/placeholder.svg?height=220&width=350",
    description: "Learn the art of Arabic calligraphy through interactive virtual lessons",
    duration: "35 min",
    rating: 4.4,
    features: ["Interactive", "Step-by-step", "Practice Mode"],
    type: "standard",
  },
  {
    id: 8,
    title: "Digital Art Fusion",
    artist: "nermeen",
    price: 28,
    originalPrice: null,
    image: "/placeholder.svg?height=220&width=350",
    description: "Where traditional art meets cutting-edge digital technology",
    duration: "45 min",
    rating: 4.7,
    features: ["Interactive", "AR Elements", "Behind-the-scenes"],
    type: "standard",
  },
  {
    id: 9,
    title: "The Last Supper Experience",
    artist: "leonardo",
    price: 50,
    originalPrice: 65,
    image: "/placeholder.svg?height=220&width=350",
    description: "An unprecedented close-up experience of Leonardo's masterpiece",
    duration: "90 min",
    rating: 4.9,
    features: ["VR Compatible", "Ultra HD", "Historical Recreation", "Expert Analysis"],
    type: "premium",
  },
  {
    id: 10,
    title: "Water Lilies Sanctuary",
    artist: "monet",
    price: 22,
    originalPrice: 30,
    image: "/placeholder.svg?height=220&width=350",
    description: "Find tranquility in Monet's water lily pond through virtual meditation",
    duration: "55 min",
    rating: 4.8,
    features: ["360° View", "Meditation Mode", "Seasonal Variations", "Nature Audio"],
    type: "standard",
  },
  {
    id: 11,
    title: "Guernica Unveiled",
    artist: "picasso",
    price: 32,
    originalPrice: null,
    image: "/placeholder.svg?height=220&width=350",
    description: "Uncover the layers of meaning in Picasso's powerful anti-war masterpiece",
    duration: "65 min",
    rating: 4.9,
    features: ["VR Compatible", "Historical Context", "Interactive Analysis", "Documentary"],
    type: "premium",
  },
  {
    id: 12,
    title: "Geometric Harmony",
    artist: "mohammed",
    price: 24,
    originalPrice: null,
    image: "/placeholder.svg?height=220&width=350",
    description: "Discover the mathematical beauty behind Islamic geometric art patterns",
    duration: "42 min",
    rating: 4.6,
    features: ["Interactive", "Mathematical Insights", "Pattern Builder"],
    type: "standard",
  },
]

// Global variables
let filteredGalleries = [...virtualGalleries]
let activeFilters = {}

// DOM elements
const searchInput = document.getElementById("searchInput")
const artistFilter = document.getElementById("artistFilter")
const minPriceInput = document.getElementById("minPrice")
const maxPriceInput = document.getElementById("maxPrice")
const quickFilterBtns = document.querySelectorAll(".quick-filter-btn")
const activeFiltersContainer = document.getElementById("activeFilters")
const searchResults = document.getElementById("searchResults")
const galleriesGrid = document.getElementById("galleriesGrid")
const galleryCount = document.getElementById("galleryCount")
const noResults = document.getElementById("noResults")

// Initialize the page
document.addEventListener("DOMContentLoaded", () => {
  renderGalleries(virtualGalleries)
  updateGalleryCount(virtualGalleries.length)
  setupEventListeners()
  setupNavigation()
})

// Setup event listeners
function setupEventListeners() {
  // Search input
  searchInput.addEventListener("input", debounce(applyFilters, 300))

  // Filter dropdowns and inputs
  artistFilter.addEventListener("change", applyFilters)
  minPriceInput.addEventListener("input", debounce(applyFilters, 300))
  maxPriceInput.addEventListener("input", debounce(applyFilters, 300))

  // Quick filter buttons
  quickFilterBtns.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      // Remove active class from all buttons
      quickFilterBtns.forEach((b) => b.classList.remove("active"))

      // Add active class to clicked button
      e.target.classList.add("active")

      // Apply price range
      const priceRange = e.target.dataset.price
      if (priceRange) {
        const [min, max] = priceRange.split("-")
        minPriceInput.value = min
        maxPriceInput.value = max
        applyFilters()
      }
    })
  })
}

// Setup navigation functionality
function setupNavigation() {
  const navToggle = document.getElementById("navToggle")
  const navMenu = document.getElementById("navMenu")
  const userAccount = document.getElementById("userAccount")
  const userMenu = document.getElementById("userMenu")

  // Mobile menu toggle
  if (navToggle && navMenu) {
    navToggle.addEventListener("click", () => {
      navMenu.classList.toggle("active")
    })
  }

  // User dropdown
  if (userAccount && userMenu) {
    userAccount.addEventListener("click", (e) => {
      e.preventDefault()
      userMenu.classList.toggle("active")
    })

    // Close dropdown when clicking outside
    document.addEventListener("click", (e) => {
      if (!userAccount.contains(e.target) && !userMenu.contains(e.target)) {
        userMenu.classList.remove("active")
      }
    })
  }

  // Navbar search
  const navbarSearch = document.getElementById("navbarSearch")
  const searchButton = document.getElementById("searchButton")

  if (searchButton) {
    searchButton.addEventListener("click", () => {
      const query = navbarSearch.value.trim()
      if (query) {
        searchInput.value = query
        applyFilters()
      }
    })
  }

  if (navbarSearch) {
    navbarSearch.addEventListener("keypress", (e) => {
      if (e.key === "Enter") {
        const query = navbarSearch.value.trim()
        if (query) {
          searchInput.value = query
          applyFilters()
        }
      }
    })
  }
}

// Apply filters function
function applyFilters() {
  const searchTerm = searchInput.value.toLowerCase().trim()
  const selectedArtist = artistFilter.value
  const minPrice = Number.parseFloat(minPriceInput.value) || 0
  const maxPrice = Number.parseFloat(maxPriceInput.value) || Number.POSITIVE_INFINITY

  // Reset active filters
  activeFilters = {}

  // Filter galleries
  filteredGalleries = virtualGalleries.filter((gallery) => {
    let matches = true

    // Search term filter
    if (searchTerm) {
      const searchableText =
        `${gallery.title} ${gallery.artist} ${gallery.description} ${gallery.features.join(" ")}`.toLowerCase()
      matches = matches && searchableText.includes(searchTerm)
      if (searchTerm) activeFilters.search = searchTerm
    }

    // Artist filter
    if (selectedArtist) {
      matches = matches && gallery.artist === selectedArtist
      activeFilters.artist = selectedArtist
    }

    // Price filter
    if (minPrice > 0 || maxPrice < Number.POSITIVE_INFINITY) {
      matches = matches && gallery.price >= minPrice && gallery.price <= maxPrice
      if (minPrice > 0 || maxPrice < Number.POSITIVE_INFINITY) {
        activeFilters.price = `$${minPrice} - $${maxPrice === Number.POSITIVE_INFINITY ? "∞" : maxPrice}`
      }
    }

    return matches
  })

  // Update UI
  renderActiveFilters()
  renderGalleries(filteredGalleries)
  updateSearchResults()
  updateGalleryCount(filteredGalleries.length)

  // Show/hide no results
  if (filteredGalleries.length === 0) {
    galleriesGrid.style.display = "none"
    noResults.style.display = "block"
  } else {
    galleriesGrid.style.display = "grid"
    noResults.style.display = "none"
  }
}

// Render active filters
function renderActiveFilters() {
  activeFiltersContainer.innerHTML = ""

  Object.entries(activeFilters).forEach(([key, value]) => {
    const filterTag = document.createElement("div")
    filterTag.className = "filter-tag"

    let displayValue = value
    if (key === "search") {
      displayValue = `Search: ${value}`
    } else if (key === "artist") {
      displayValue = `Artist: ${value.charAt(0).toUpperCase() + value.slice(1)}`
    } else {
      displayValue = `${key.charAt(0).toUpperCase() + key.slice(1)}: ${value}`
    }

    filterTag.innerHTML = `
      <span>${displayValue}</span>
      <span class="remove-filter" onclick="removeFilter('${key}')">×</span>
    `

    activeFiltersContainer.appendChild(filterTag)
  })
}

// Remove individual filter
function removeFilter(filterKey) {
  switch (filterKey) {
    case "search":
      searchInput.value = ""
      break
    case "artist":
      artistFilter.value = ""
      break
    case "price":
      minPriceInput.value = ""
      maxPriceInput.value = ""
      // Remove active class from quick filter buttons
      quickFilterBtns.forEach((btn) => btn.classList.remove("active"))
      break
  }
  applyFilters()
}

// Clear all filters
function clearAllFilters() {
  searchInput.value = ""
  artistFilter.value = ""
  minPriceInput.value = ""
  maxPriceInput.value = ""

  // Remove active class from quick filter buttons
  quickFilterBtns.forEach((btn) => btn.classList.remove("active"))

  activeFilters = {}
  filteredGalleries = [...virtualGalleries]

  renderActiveFilters()
  renderGalleries(virtualGalleries)
  updateSearchResults()
  updateGalleryCount(virtualGalleries.length)

  galleriesGrid.style.display = "grid"
  noResults.style.display = "none"
}

// Render galleries
function renderGalleries(galleriesToRender) {
  galleriesGrid.innerHTML = ""

  galleriesToRender.forEach((gallery) => {
    const galleryCard = document.createElement("div")
    galleryCard.className = "virtual-gallery-card"

    const featuresHTML = gallery.features.map((feature) => `<span class="feature-tag">${feature}</span>`).join("")

    const priceHTML = gallery.originalPrice
      ? `${gallery.price}<span class="original-price">$${gallery.originalPrice}</span>`
      : gallery.price

    galleryCard.innerHTML = `
      <div class="virtual-badge-card">${gallery.type.toUpperCase()}</div>
      <img src="${gallery.image}" alt="${gallery.title}" class="gallery-image">
      <div class="gallery-content">
        <h3 class="gallery-title">${gallery.title}</h3>
        <div class="gallery-artist">
          <i class="fas fa-user-circle"></i>
          ${gallery.artist.charAt(0).toUpperCase() + gallery.artist.slice(1)}
        </div>
        <p class="gallery-description">${gallery.description}</p>
        <div class="gallery-features">
          ${featuresHTML}
        </div>
        <div class="gallery-details">
          <div class="gallery-duration">
            <i class="fas fa-clock"></i>
            ${gallery.duration}
          </div>
          <div class="gallery-rating">
            <i class="fas fa-star"></i>
            ${gallery.rating}
          </div>
        </div>
        <div class="gallery-price">$${priceHTML}</div>
        <button class="enter-btn" onclick="enterVirtualGallery(${gallery.id})">
          <i class="fas fa-vr-cardboard"></i> Enter Virtual Gallery
        </button>
      </div>
    `

    galleriesGrid.appendChild(galleryCard)
  })
}

// Update search results text
function updateSearchResults() {
  if (Object.keys(activeFilters).length > 0) {
    const filterCount = Object.keys(activeFilters).length
    const resultCount = filteredGalleries.length
    searchResults.innerHTML = `Found ${resultCount} virtual galleries with ${filterCount} active filter${filterCount > 1 ? "s" : ""}`
    searchResults.style.display = "block"
  } else {
    searchResults.style.display = "none"
  }
}

// Update gallery count
function updateGalleryCount(count) {
  if (count === virtualGalleries.length) {
    galleryCount.textContent = `Showing all ${count} virtual galleries`
  } else {
    galleryCount.textContent = `Showing ${count} of ${virtualGalleries.length} virtual galleries`
  }
}

// Enter virtual gallery function
function enterVirtualGallery(galleryId) {
  const gallery = virtualGalleries.find((g) => g.id === galleryId)
  if (gallery) {
    // Show loading state
    const enterBtn = event.target
    const originalText = enterBtn.innerHTML
    enterBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading Virtual Experience...'
    enterBtn.disabled = true

    // Simulate loading time
    setTimeout(() => {
      alert(
        `🎨 Welcome to "${gallery.title}"!\n\n` +
          `Artist: ${gallery.artist.charAt(0).toUpperCase() + gallery.artist.slice(1)}\n` +
          `Duration: ${gallery.duration}\n` +
          `Rating: ${gallery.rating}/5.0\n` +
          `Price: $${gallery.price}\n\n` +
          `Features: ${gallery.features.join(", ")}\n\n` +
          `${gallery.description}\n\n` +
          `🚀 Launching virtual experience...\n` +
          `Thank you for choosing Yadawity Virtual Galleries!`,
      )

      // Success state
      enterBtn.innerHTML = '<i class="fas fa-check"></i> Experience Launched!'
      enterBtn.style.background = "linear-gradient(45deg, #22c55e, #16a34a)"

      setTimeout(() => {
        enterBtn.innerHTML = originalText
        enterBtn.style.background = ""
        enterBtn.disabled = false
      }, 3000)
    }, 2000)
  }
}

// Debounce function for search input
function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Add keyboard navigation support
document.addEventListener("keydown", (e) => {
  // Escape key to clear search
  if (e.key === "Escape") {
    if (searchInput.value || Object.keys(activeFilters).length > 0) {
      clearAllFilters()
    }
  }

  // Enter key on search input
  if (e.key === "Enter" && e.target === searchInput) {
    applyFilters()
  }
})

// Initialize cart and wishlist counters
function updateCartCount() {
  const cartCount = document.getElementById("cartCount")
  const count = 0 // This would typically get the count from localStorage or a backend

  if (cartCount) cartCount.textContent = count
}

function updateWishlistCount() {
  const wishlistCount = document.getElementById("wishlistCount")
  const count = 0 // This would typically get the count from localStorage or a backend

  if (wishlistCount) {
    wishlistCount.textContent = count
    wishlistCount.style.display = count > 0 ? "block" : "none"
  }
}

// Handle window resize for responsive behavior
window.addEventListener(
  "resize",
  debounce(() => {
    // Recalculate grid layout if needed
    const grid = document.getElementById("galleriesGrid")
    if (grid) {
      grid.style.display = "none"
      setTimeout(() => {
        grid.style.display = "grid"
      }, 10)
    }
  }, 250),
)

// Initialize counters
updateCartCount()
updateWishlistCount()

// Export functions for global access
window.applyFilters = applyFilters
window.clearAllFilters = clearAllFilters
window.removeFilter = removeFilter
window.enterVirtualGallery = enterVirtualGallery
