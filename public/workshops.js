// Sample galleries data
const galleries = [
  {
    id: 1,
    title: "Contemporary Art Gallery Cairo",
    artist: "Dr. Ahmed Hassan",
    category: "Cognitive Art Therapy",
    location: "Cairo",
    street: "Tahrir Street",
    date: "2025-07-30",
    time: "09:00",
    price: 100,
    rating: 4.8,
    image: "./image/slide1.jpg",
    description: "Experience cutting-edge contemporary art in the heart of Cairo",
    openHours: "9:00 AM - 12:00 PM",
    capacity: 50,
    available: true
  },
  {
    id: 2,
    title: "Watercolor Dreams Gallery",
    artist: "Dr. Sara Youssef",
    category: "Dialectical Behavior Therapy (DBT) with Art",
    location: "Alexandria",
    street: "El Merghany Street",
    date: "2025-07-31",
    time: "11:00",
    price: 120,
    rating: 4.6,
    image: "./image/AllentownArtMuseum_Gallery01_DiscoverLehighValley_2450c76f-4de5-402c-a060-d0a8ff3b1d37.jpg",
    description: "Traditional watercolor masterpieces by local artists",
    openHours: "12:00 PM - 5:00 PM",
    capacity: 30,
    available: true
  },
  {
    id: 3,
    title: "Digital Art Showcase",
    artist: "Dr. Mona Khaled",
    category: "Trauma-Informed Art Therapy",
    location: "Giza",
    street: "El Haram Street",
    date: "2025-08-01",
    time: "12:30",
    price: 150,
    rating: 4.9,
    image: "./image/STC_EDS_MINAG_R_L_2011_229-001.jpg",
    description: "Innovative digital artworks and interactive installations",
    openHours: "5:00 PM - 8:00 PM",
    capacity: 40,
    available: true
  },
  {
    id: 4,
    title: "Portrait Gallery",
    artist: "Dr. Tarek Nabil",
    category: "Behavioral Art Therapy",
    location: "Mansoura",
    street: "Port Said Street",
    date: "2025-08-02",
    time: "09:00",
    price: 90,
    rating: 4.7,
    image: "./image/photo-1554907984-15263bfd63bd.jpeg",
    description: "Stunning portrait collection from emerging artists",
    openHours: "9:00 AM - 12:00 PM",
    capacity: 25,
    available: true
  },
  {
    id: 5,
    title: "Sculpture Garden",
    artist: "Samaa",
    location: "Aswan",
    date: "this-weekend",
    timeRange: "business-hours",
    rating: 4.5,
    image: "./image/darker_image.webp",
    description: "Beautiful outdoor sculpture exhibition",
    openHours: "9:00 AM - 5:00 PM",
    capacity: 60,
    available: true
  },
  {
    id: 6,
    title: "Photography Studio",
    artist: "Mariem",
    location: "Sharm El Sheikh",
    date: "next-weekend",
    timeRange: "extended-hours",
    rating: 4.9,
    image: "./image/2d58ceedffd1ba6b3e8e2adc4371208f.jpg",
    description: "Contemporary photography exhibition and workspace",
    openHours: "9:00 AM - 9:00 PM",
    capacity: 35,
    available: true
  },
  {
    id: 7,
    title: "Mixed Media Workshop",
    artist: "Soha",
    location: "Hurghada",
    date: "this-month",
    timeRange: "afternoon",
    rating: 4.4,
    image: "./image/Artist-PainterLookingAtCamera.webp",
    description: "Hands-on mixed media art experience",
    openHours: "12:00 PM - 5:00 PM",
    capacity: 20,
    available: true
  },
  {
    id: 8,
    title: "Street Art Gallery",
    artist: "Essam",
    location: "Port Said",
    date: "next-month",
    timeRange: "evening",
    rating: 4.6,
    image: "./image/artist-sitting-on-the-floor.jpg",
    description: "Urban art and street culture exhibition",
    openHours: "5:00 PM - 8:00 PM",
    capacity: 45,
    available: true
  },
  {
    id: 9,
    title: "Art Nouveau Collection",
    artist: "Mazen",
    location: "Suez",
    date: "today",
    timeRange: "night",
    rating: 4.7,
    image: "./image/photo.jpeg",
    description: "Classic Art Nouveau pieces and modern interpretations",
    openHours: "8:00 PM - 11:00 PM",
    capacity: 30,
    available: true
  },
  {
    id: 10,
    title: "Local Artists Collective",
    artist: "Noraa",
    location: "Mansoura",
    date: "tomorrow",
    timeRange: "early-morning",
    rating: 4.5,
    image: "./image/Team image.jpeg",
    description: "Showcasing the best of local artistic talent",
    openHours: "6:00 AM - 9:00 AM",
    capacity: 55,
    available: true
  },
  {
    id: 11,
    title: "International Art Space",
    artist: "Nermmen",
    location: "Tanta",
    date: "this-week",
    timeRange: "late-night",
    rating: 4.8,
    image: "./image/images.jpeg",
    description: "Global artists showcase with diverse cultural perspectives",
    openHours: "11:00 PM - 2:00 AM",
    capacity: 40,
    available: true
  }
]

// Global variables
let filteredGalleries = [...galleries]
let activeFilters = {}
let currentPage = 1
let galleriesPerPage = 6 // Show 6 galleries per page instead of all 12
let totalPages = 1

// DOM elements
const searchInput = document.getElementById("searchInput")
const doctorFilter = document.getElementById("doctorFilter")
const categoryFilter = document.getElementById("categoryFilter")
const cityFilter = document.getElementById("cityFilter")
const streetFilter = document.getElementById("streetFilter")
const minPriceInput = document.getElementById("minPrice")
const maxPriceInput = document.getElementById("maxPrice")
const dateFilter = document.getElementById("dateFilter")
const timeFilter = document.getElementById("timeFilter")
const activeFiltersContainer = document.getElementById("activeFilters")
const searchResults = document.getElementById("searchResults")
const coursesGrid = document.getElementById("coursesGrid")
const courseCount = document.getElementById("courseCount")
const noResults = document.getElementById("noResults")

// Initialize the page
document.addEventListener("DOMContentLoaded", () => {
  filteredGalleries = [...galleries]
  totalPages = Math.ceil(galleries.length / galleriesPerPage)
  renderGalleries(galleries)
  updatePaginationControls()
  setupEventListeners()
  setupNavigation()
})

// Setup event listeners
function setupEventListeners() {
  // Search input
  searchInput.addEventListener("input", debounce(applyFilters, 300))

  // Filter dropdowns and inputs
  if (doctorFilter) doctorFilter.addEventListener("change", applyFilters)
  if (categoryFilter) categoryFilter.addEventListener("change", applyFilters)
  if (cityFilter) cityFilter.addEventListener("change", applyFilters)
  if (streetFilter) streetFilter.addEventListener("change", applyFilters)
  if (minPriceInput) minPriceInput.addEventListener("input", debounce(applyFilters, 300))
  if (maxPriceInput) maxPriceInput.addEventListener("input", debounce(applyFilters, 300))
  if (dateFilter) dateFilter.addEventListener("change", applyFilters)
  if (timeFilter) timeFilter.addEventListener("change", applyFilters)
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
  const selectedDoctor = doctorFilter ? doctorFilter.value : ""
  const selectedCategory = categoryFilter ? categoryFilter.value : ""
  const selectedCity = cityFilter ? cityFilter.value : ""
  const selectedStreet = streetFilter ? streetFilter.value : ""
  const minPrice = minPriceInput ? Number.parseFloat(minPriceInput.value) || 0 : 0
  const maxPrice = maxPriceInput ? Number.parseFloat(maxPriceInput.value) || Number.POSITIVE_INFINITY : Number.POSITIVE_INFINITY
  const selectedDate = dateFilter ? dateFilter.value : ""
  const selectedTime = timeFilter ? timeFilter.value : ""

  // Reset active filters
  activeFilters = {}

  // Filter galleries
  filteredGalleries = galleries.filter((gallery) => {
    let matches = true

    // Search term filter
    if (searchTerm) {
      const searchableText =
        `${gallery.title} ${gallery.artist} ${gallery.location} ${gallery.description}`.toLowerCase()
      matches = matches && searchableText.includes(searchTerm)
      if (searchTerm) activeFilters.search = searchTerm
    }


    // Doctor filter (artist)
    if (selectedDoctor) {
      matches = matches && gallery.artist === selectedDoctor
      activeFilters.doctor = selectedDoctor
    }

    // Category filter
    if (selectedCategory) {
      matches = matches && gallery.category === selectedCategory
      activeFilters.category = selectedCategory
    }

    // City filter
    if (selectedCity) {
      matches = matches && gallery.location === selectedCity
      activeFilters.city = selectedCity
    }

    // Street filter
    if (selectedStreet) {
      matches = matches && gallery.street === selectedStreet
      activeFilters.street = selectedStreet
    }

    // Price filter
    if (minPrice > 0 || maxPrice < Number.POSITIVE_INFINITY) {
      matches = matches && gallery.price >= minPrice && gallery.price <= maxPrice
      activeFilters.price = `$${minPrice} - $${maxPrice === Number.POSITIVE_INFINITY ? "∞" : maxPrice}`
    }

    // Date filter
    if (selectedDate) {
      matches = matches && gallery.date === selectedDate
      activeFilters.date = selectedDate
    }

    // Time filter
    if (selectedTime) {
      matches = matches && gallery.time === selectedTime
      activeFilters.time = selectedTime
    }

    return matches
  })

  // Reset to first page when filters change
  currentPage = 1

  // Update UI
  renderActiveFilters()
  renderGalleries(filteredGalleries)
  updateSearchResults()
  updatePaginationControls()

  // Show/hide no results
  if (filteredGalleries.length === 0) {
    coursesGrid.style.display = "none"
    noResults.style.display = "block"
    // Hide pagination when no results
    const paginationSection = document.querySelector(".pagination-section")
    if (paginationSection) {
      paginationSection.style.display = "none"
    }
  } else {
    coursesGrid.style.display = "grid"
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
    case "category":
      categoryFilter.value = ""
      break
    case "difficulty":
      difficultyFilter.value = ""
      break
    case "duration":
      durationFilter.value = ""
      break
    case "price":
      minPriceInput.value = ""
      maxPriceInput.value = ""
      break
  }
  applyFilters()
}

// Clear all filters
function clearAllFilters() {
  searchInput.value = ""
  categoryFilter.value = ""
  difficultyFilter.value = ""
  durationFilter.value = ""
  minPriceInput.value = ""
  maxPriceInput.value = ""

  activeFilters = {}
  filteredGalleries = [...galleries]
  currentPage = 1

  renderActiveFilters()
  renderGalleries(galleries)
  updateSearchResults()
  updatePaginationControls()

  coursesGrid.style.display = "grid"
  noResults.style.display = "none"
}

// Render courses
function renderGalleries(galleriesToRender) {
  coursesGrid.innerHTML = ""

  // Pagination logic
  const startIndex = (currentPage - 1) * galleriesPerPage
  const endIndex = startIndex + galleriesPerPage
  const paginatedGalleries = galleriesToRender.slice(startIndex, endIndex)

  paginatedGalleries.forEach((gallery) => {
    const galleryCard = document.createElement("div");
    galleryCard.className = "course-card";

    const starsHTML = Array(5)
      .fill()
      .map((_, i) => `<span class="star">${i < Math.floor(gallery.rating) ? "★" : "☆"}</span>`)
      .join("");

    const availabilityBadge = gallery.available 
      ? `<div class="availability-badge available"><span class="availability-dot"></span><span>Available</span></div>`
      : `<div class="availability-badge unavailable"><span class="availability-dot"></span><span>Booked</span></div>`;

    const priceHTML = gallery.price !== undefined ? `$${gallery.price}` : '';

    galleryCard.innerHTML = `
      ${availabilityBadge}
      <div class="course-rating">
        <div class="stars-container">${starsHTML}</div>
        <span class="rating-text">${gallery.rating}</span>
      </div>
      <img src="${gallery.image}" alt="${gallery.title}" class="course-image">
      <div class="course-overlay">
        <div class="quick-actions">
          <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
        </div>
      </div>
      <div class="course-content">
        <h3 class="course-title">${gallery.title}</h3>
        <div class="course-instructor"><b>Doctor:</b> ${gallery.artist}</div>
        <div class="course-category"><b>Category:</b> ${gallery.category || ''}</div>
        <div class="course-location">
          <span><i class='fas fa-map-marker-alt'></i> <b>City:</b> ${gallery.location || ''}</span>
          <span><i class='fas fa-road'></i> <b>Street:</b> ${gallery.street || ''}</span>
        </div>
        <div class="course-datetime">
          <span><i class='fas fa-calendar-alt'></i> <b>Date:</b> ${gallery.date || ''}</span>
          <span><i class='fas fa-clock'></i> <b>Time:</b> ${gallery.time || ''}</span>
        </div>
        <div class="course-meta">
          <div class="course-duration"><i class="fas fa-clock"></i> <b>Open:</b> ${gallery.openHours}</div>
          <div class="course-students"><i class="fas fa-users"></i> <b>Capacity:</b> ${gallery.capacity}</div>
        </div>
        <div class="course-price-info">
          <div class="course-price"><span class="price">${priceHTML}</span></div>
        </div>
        <button class="enroll-btn" onclick="bookGallery(${gallery.id})">
          <i class="fas fa-calendar-check"></i> Book Visit
        </button>
      </div>
    `;

    coursesGrid.appendChild(galleryCard);
  });

  // Add quick view event listeners
  document.querySelectorAll('.quick-action-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const galleryCard = e.target.closest('.course-card');
      const galleryId = parseInt(galleryCard.querySelector('.enroll-btn').getAttribute('onclick').match(/\d+/)[0]);
      openQuickView(galleryId);
    });
  });

  // Update pagination info
  totalPages = Math.ceil(galleriesToRender.length / galleriesPerPage)
  updatePaginationInfo()
}

// Helper functions for display formatting
function formatDateDisplay(date) {
  const dateMap = {
    'today': 'Today',
    'tomorrow': 'Tomorrow',
    'this-week': 'This Week',
    'next-week': 'Next Week',
    'this-weekend': 'This Weekend',
    'next-weekend': 'Next Weekend',
    'this-month': 'This Month',
    'next-month': 'Next Month'
  }
  return dateMap[date] || date
}

function formatTimeDisplay(timeRange) {
  const timeMap = {
    'morning': 'Morning',
    'afternoon': 'Afternoon',
    'evening': 'Evening',
    'night': 'Night',
    'early-morning': 'Early Morning',
    'late-night': 'Late Night',
    'business-hours': 'Business Hours',
    'extended-hours': 'Extended Hours'
  }
  return timeMap[timeRange] || timeRange
}

// Open quick view modal
function openQuickView(galleryId) {
  const gallery = galleries.find(g => g.id === galleryId);
  if (!gallery) return;
  
  // Create modal overlay
  const overlay = document.createElement('div');
  overlay.className = 'quick-view-overlay';
  overlay.innerHTML = `
    <div class="quick-view-modal">
      <div class="quick-view-content">
        <img src="${gallery.image}" alt="${gallery.title}" class="quick-view-image">
        <div class="quick-view-details">
          <h2>${gallery.title}</h2>
          <p class="instructor">Artist: ${gallery.artist}</p>
          <p class="description">${gallery.description}</p>
          <div class="meta-info">
            <span><i class="fas fa-map-marker-alt"></i> ${gallery.location}</span>
            <span><i class="fas fa-clock"></i> ${gallery.openHours}</span>
            <span><i class="fas fa-calendar"></i> ${formatDateDisplay(gallery.date)}</span>
            <span><i class="fas fa-users"></i> ${gallery.capacity} capacity</span>
            <span><i class="fas fa-star"></i> ${gallery.rating}</span>
          </div>
          <div class="price-info">
            <span class="price">$${course.price}</span>
            ${course.originalPrice ? `<span class="original-price">$${course.originalPrice}</span>` : ''}
          </div>
          <button class="enroll-btn" onclick="enrollCourse(${course.id})">
            <i class="fas fa-graduation-cap"></i> Enroll Now
          </button>
        </div>
        <button class="close-modal"><i class="fas fa-times"></i></button>
      </div>
    </div>
  `;

  // Add close functionality
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay || e.target.closest('.close-modal')) {
      document.body.removeChild(overlay);
    }
  });

  // Add escape key to close
  const handleEscape = (e) => {
    if (e.key === 'Escape') {
      document.body.removeChild(overlay);
      document.removeEventListener('keydown', handleEscape);
    }
  };
  document.addEventListener('keydown', handleEscape);

  document.body.appendChild(overlay);
}

// Update search results text
function updateSearchResults() {
  if (Object.keys(activeFilters).length > 0) {
    const filterCount = Object.keys(activeFilters).length
    const resultCount = filteredGalleries.length
    searchResults.innerHTML = `Found ${resultCount} galleries with ${filterCount} active filter${filterCount > 1 ? "s" : ""}`
    searchResults.style.display = "block"
  } else {
    searchResults.style.display = "none"
  }
}

// Update gallery count
function updateGalleryCount(count) {
  if (count === galleries.length) {
    courseCount.textContent = `Showing all ${count} galleries`
  } else {
    courseCount.textContent = `Showing ${count} of ${galleries.length} galleries`
  }
}

// Update pagination info
function updatePaginationInfo() {
  const paginationInfo = document.getElementById("paginationInfo")
  if (paginationInfo) {
    const startGallery = (currentPage - 1) * galleriesPerPage + 1
    const endGallery = Math.min(currentPage * galleriesPerPage, filteredGalleries.length)
    paginationInfo.textContent = `Showing ${startGallery} - ${endGallery} of ${filteredGalleries.length} galleries`
  }
}

// Pagination functions
function previousPage() {
  if (currentPage > 1) {
    currentPage--
    renderGalleries(filteredGalleries)
    updatePaginationControls()
    scrollToTop()
  }
}

function nextPage() {
  if (currentPage < totalPages) {
    currentPage++
    renderGalleries(filteredGalleries)
    updatePaginationControls()
    scrollToTop()
  }
}

function goToPage(page) {
  if (page >= 1 && page <= totalPages) {
    currentPage = page
    renderGalleries(filteredGalleries)
    updatePaginationControls()
    scrollToTop()
  }
}

function updatePaginationControls() {
  const prevBtn = document.getElementById("prevBtn")
  const nextBtn = document.getElementById("nextBtn")
  const paginationNumbers = document.getElementById("paginationNumbers")

  // Update previous button
  if (prevBtn) {
    prevBtn.disabled = currentPage === 1
  }

  // Update next button
  if (nextBtn) {
    nextBtn.disabled = currentPage === totalPages
  }

  // Update pagination numbers
  if (paginationNumbers) {
    paginationNumbers.innerHTML = ""

    if (totalPages <= 1) {
      // Hide pagination if only one page
      const paginationSection = document.querySelector(".pagination-section")
      if (paginationSection) {
        paginationSection.style.display = "none"
      }
      return
    } else {
      // Show pagination if more than one page
      const paginationSection = document.querySelector(".pagination-section")
      if (paginationSection) {
        paginationSection.style.display = "block"
      }
    }

    const maxVisiblePages = 5
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2))
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1)

    // Adjust start page if we're near the end
    if (endPage - startPage < maxVisiblePages - 1) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1)
    }

    // Add first page and dots if needed
    if (startPage > 1) {
      const firstPageBtn = document.createElement("button")
      firstPageBtn.className = "pagination-number"
      firstPageBtn.textContent = "1"
      firstPageBtn.onclick = () => goToPage(1)
      paginationNumbers.appendChild(firstPageBtn)

      if (startPage > 2) {
        const dots = document.createElement("span")
        dots.className = "pagination-dots"
        dots.textContent = "..."
        paginationNumbers.appendChild(dots)
      }
    }

    // Add visible page numbers
    for (let i = startPage; i <= endPage; i++) {
      const pageBtn = document.createElement("button")
      pageBtn.className = `pagination-number ${i === currentPage ? "active" : ""}`
      pageBtn.textContent = i
      pageBtn.onclick = () => goToPage(i)
      paginationNumbers.appendChild(pageBtn)
    }

    // Add last page and dots if needed
    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        const dots = document.createElement("span")
        dots.className = "pagination-dots"
        dots.textContent = "..."
        paginationNumbers.appendChild(dots)
      }

      const lastPageBtn = document.createElement("button")
      lastPageBtn.className = "pagination-number"
      lastPageBtn.textContent = totalPages
      lastPageBtn.onclick = () => goToPage(totalPages)
      paginationNumbers.appendChild(lastPageBtn)
    }
  }

  // Update gallery count with pagination info
  updateGalleryCountWithPagination()
}

function updateGalleryCountWithPagination() {
  const startGallery = (currentPage - 1) * galleriesPerPage + 1
  const endGallery = Math.min(currentPage * galleriesPerPage, filteredGalleries.length)
  
  if (filteredGalleries.length === 0) {
    courseCount.textContent = "No galleries found"
  } else if (filteredGalleries.length <= galleriesPerPage) {
    courseCount.textContent = `Showing ${filteredGalleries.length} galler${filteredGalleries.length === 1 ? 'y' : 'ies'}`
  } else {
    courseCount.textContent = `Showing ${startGallery}-${endGallery} of ${filteredGalleries.length} galleries`
  }
}

function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

// Book gallery function
function bookGallery(galleryId) {
  const gallery = galleries.find((g) => g.id === galleryId)
  if (gallery) {
    alert(`Booking request sent for ${gallery.title}!\nArtist: ${gallery.artist}\nLocation: ${gallery.location}\nTime: ${gallery.openHours}`)
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
    const grid = document.getElementById("coursesGrid")
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
window.bookGallery = bookGallery
window.previousPage = previousPage
window.nextPage = nextPage
window.goToPage = goToPage