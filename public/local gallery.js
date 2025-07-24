// Sample gallery data
const galleries = [
  {
    id: 1,
    title: "Modern Art Showcase",
    artist: "picasso",
    location: "cairo street",
    date: "this week",
    time: "10:00 AM",
    price: "$25",
    image: "/placeholder.svg?height=200&width=320",
    description: "Experience contemporary masterpieces in an intimate setting",
  },
  {
    id: 2,
    title: "Classical Portraits",
    artist: "mohammed",
    location: "masr elgdeda",
    date: "this week",
    time: "2:00 PM",
    price: "$30",
    image: "/placeholder.svg?height=200&width=320",
    description: "Timeless portraits from renowned artists",
  },
  {
    id: 3,
    title: "Abstract Expressions",
    artist: "nermeen",
    location: "kornesh el nail",
    date: "this month",
    time: "6:00 PM",
    price: "$35",
    image: "/placeholder.svg?height=200&width=320",
    description: "Dive into the world of abstract art and creativity",
  },
  {
    id: 4,
    title: "Sculpture Garden",
    artist: "picasso",
    location: "maadii",
    date: "this week",
    time: "11:00 AM",
    price: "$40",
    image: "/placeholder.svg?height=200&width=320",
    description: "Three-dimensional art in a beautiful garden setting",
  },
  {
    id: 5,
    title: "Photography Exhibition",
    artist: "mohammed",
    location: "cairo street",
    date: "this month",
    time: "4:00 PM",
    price: "$20",
    image: "/placeholder.svg?height=200&width=320",
    description: "Capturing moments through the lens of master photographers",
  },
  {
    id: 6,
    title: "Digital Art Revolution",
    artist: "nermeen",
    location: "masr elgdeda",
    date: "this week",
    time: "7:00 PM",
    price: "$45",
    image: "/placeholder.svg?height=200&width=320",
    description: "Explore the future of art through digital mediums",
  },
  {
    id: 7,
    title: "Watercolor Dreams",
    artist: "picasso",
    location: "kornesh el nail",
    date: "this month",
    time: "1:00 PM",
    price: "$28",
    image: "/placeholder.svg?height=200&width=320",
    description: "Delicate watercolor paintings that capture emotion",
  },
  {
    id: 8,
    title: "Mixed Media Madness",
    artist: "mohammed",
    location: "maadii",
    date: "this week",
    time: "3:00 PM",
    price: "$32",
    image: "/placeholder.svg?height=200&width=320",
    description: "Art that breaks boundaries with mixed materials",
  },
  {
    id: 9,
    title: "Minimalist Zen",
    artist: "nermeen",
    location: "cairo street",
    date: "this month",
    time: "5:00 PM",
    price: "$22",
    image: "/placeholder.svg?height=200&width=320",
    description: "Find peace in the simplicity of minimalist art",
  },
  {
    id: 10,
    title: "Street Art Culture",
    artist: "picasso",
    location: "masr elgdeda",
    date: "this week",
    time: "8:00 PM",
    price: "$18",
    image: "/placeholder.svg?height=200&width=320",
    description: "Urban art that tells the story of the streets",
  },
  {
    id: 11,
    title: "Renaissance Revival",
    artist: "mohammed",
    location: "kornesh el nail",
    date: "this month",
    time: "12:00 PM",
    price: "$50",
    image: "/placeholder.svg?height=200&width=320",
    description: "Step back in time with Renaissance masterpieces",
  },
  {
    id: 12,
    title: "Contemporary Fusion",
    artist: "nermeen",
    location: "maadii",
    date: "this week",
    time: "9:00 AM",
    price: "$38",
    image: "/placeholder.svg?height=200&width=320",
    description: "Where traditional meets modern in perfect harmony",
  },
]

// Global variables
let filteredGalleries = [...galleries]
let activeFilters = {}

// DOM elements
const searchInput = document.getElementById("searchInput")
const artistFilter = document.getElementById("artistFilter")
const locationFilter = document.getElementById("locationFilter")
const dateFilter = document.getElementById("dateFilter")
const timeRange = document.getElementById("timeRange")
const activeFiltersContainer = document.getElementById("activeFilters")
const searchResults = document.getElementById("searchResults")
const coursesGrid = document.getElementById("coursesGrid")
const courseCount = document.getElementById("courseCount")
const noResults = document.getElementById("noResults")

// Initialize the page
document.addEventListener("DOMContentLoaded", () => {
  renderGalleries(galleries)
  updateCourseCount(galleries.length)
  setupEventListeners()
  setupNavigation()
})

// Setup event listeners
function setupEventListeners() {
  // Search input
  searchInput.addEventListener("input", debounce(applyFilters, 300))

  // Filter dropdowns
  artistFilter.addEventListener("change", applyFilters)
  locationFilter.addEventListener("change", applyFilters)
  dateFilter.addEventListener("change", applyFilters)
  timeRange.addEventListener("input", debounce(applyFilters, 300))

  // Arrow buttons for time range
  const arrowUp = document.querySelector(".min-arrow-up")
  const arrowDown = document.querySelector(".min-arrow-down")

  if (arrowUp) {
    arrowUp.addEventListener("click", () => {
      const currentValue = timeRange.value
      if (currentValue === "pm") {
        timeRange.value = "am"
      } else {
        timeRange.value = "pm"
      }
      applyFilters()
    })
  }

  if (arrowDown) {
    arrowDown.addEventListener("click", () => {
      const currentValue = timeRange.value
      if (currentValue === "am") {
        timeRange.value = "pm"
      } else {
        timeRange.value = "am"
      }
      applyFilters()
    })
  }
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
  const selectedLocation = locationFilter.value
  const selectedDate = dateFilter.value
  const selectedTime = timeRange.value.toLowerCase()

  // Reset active filters
  activeFilters = {}

  // Filter galleries
  filteredGalleries = galleries.filter((gallery) => {
    let matches = true

    // Search term filter
    if (searchTerm) {
      const searchableText =
        `${gallery.title} ${gallery.artist} ${gallery.location} ${gallery.date} ${gallery.time}`.toLowerCase()
      matches = matches && searchableText.includes(searchTerm)
      if (searchTerm) activeFilters.search = searchTerm
    }

    // Artist filter
    if (selectedArtist) {
      matches = matches && gallery.artist === selectedArtist
      activeFilters.artist = selectedArtist
    }

    // Location filter
    if (selectedLocation) {
      matches = matches && gallery.location === selectedLocation
      activeFilters.location = selectedLocation
    }

    // Date filter
    if (selectedDate) {
      matches = matches && gallery.date === selectedDate
      activeFilters.date = selectedDate
    }

    // Time filter
    if (selectedTime) {
      const galleryTime = gallery.time.toLowerCase()
      if (selectedTime === "am") {
        matches = matches && galleryTime.includes("am")
      } else if (selectedTime === "pm") {
        matches = matches && galleryTime.includes("pm")
      }
      activeFilters.time = selectedTime
    }

    return matches
  })

  // Update UI
  renderActiveFilters()
  renderGalleries(filteredGalleries)
  updateSearchResults()
  updateCourseCount(filteredGalleries.length)

  // Show/hide no results
  if (filteredGalleries.length === 0) {
    coursesGrid.style.display = "none"
    noResults.style.display = "block"
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
    case "artist":
      artistFilter.value = ""
      break
    case "location":
      locationFilter.value = ""
      break
    case "date":
      dateFilter.value = ""
      break
    case "time":
      timeRange.value = ""
      break
  }
  applyFilters()
}

// Clear all filters
function clearAllFilters() {
  searchInput.value = ""
  artistFilter.value = ""
  locationFilter.value = ""
  dateFilter.value = ""
  timeRange.value = ""

  activeFilters = {}
  filteredGalleries = [...galleries]

  renderActiveFilters()
  renderGalleries(galleries)
  updateSearchResults()
  updateCourseCount(galleries.length)

  coursesGrid.style.display = "grid"
  noResults.style.display = "none"
}

// Render galleries
function renderGalleries(galleriesToRender) {
  coursesGrid.innerHTML = ""

  galleriesToRender.forEach((gallery) => {
    const galleryCard = document.createElement("div")
    galleryCard.className = "gallery-card"

    galleryCard.innerHTML = `
            <img src="${gallery.image}" alt="${gallery.title}" class="gallery-image">
            <div class="gallery-content">
                <h3 class="gallery-title">${gallery.title}</h3>
                <div class="gallery-artist">by ${gallery.artist.charAt(0).toUpperCase() + gallery.artist.slice(1)}</div>
                <div class="gallery-location">
                    <i class="fas fa-map-marker-alt"></i>
                    ${gallery.location.charAt(0).toUpperCase() + gallery.location.slice(1)}
                </div>
                <div class="gallery-details">
                    <div class="gallery-date">
                        <i class="fas fa-calendar"></i>
                        ${gallery.date.charAt(0).toUpperCase() + gallery.date.slice(1)}
                    </div>
                    <div class="gallery-time">
                        <i class="fas fa-clock"></i>
                        ${gallery.time}
                    </div>
                </div>
                <div class="gallery-price">${gallery.price}</div>
                <button class="book-btn" onclick="bookGallery(${gallery.id})">
                    <i class="fas fa-ticket-alt"></i> Book Now
                </button>
            </div>
        `

    coursesGrid.appendChild(galleryCard)
  })
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

// Update course count
function updateCourseCount(count) {
  if (count === galleries.length) {
    courseCount.textContent = `Showing all ${count} galleries`
  } else {
    courseCount.textContent = `Showing ${count} of ${galleries.length} galleries`
  }
}

// Book gallery function
function bookGallery(galleryId) {
  const gallery = galleries.find((g) => g.id === galleryId)
  if (gallery) {
    alert(
      `Booking confirmed for "${gallery.title}" by ${gallery.artist}!\n\nLocation: ${gallery.location}\nDate: ${gallery.date}\nTime: ${gallery.time}\nPrice: ${gallery.price}\n\nThank you for choosing Yadawity Gallery Hub!`,
    )

    // Here you would typically integrate with a booking system
    // For now, we'll just show a success message
    const bookBtn = event.target
    const originalText = bookBtn.innerHTML
    bookBtn.innerHTML = '<i class="fas fa-check"></i> Booked!'
    bookBtn.style.background = "linear-gradient(45deg, #22c55e, #16a34a)"
    bookBtn.disabled = true

    setTimeout(() => {
      bookBtn.innerHTML = originalText
      bookBtn.style.background = ""
      bookBtn.disabled = false
    }, 3000)
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

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault()
    const target = document.querySelector(this.getAttribute("href"))
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      })
    }
  })
})

// Add loading animation for gallery cards
function addLoadingAnimation() {
  coursesGrid.innerHTML = ""
  for (let i = 0; i < 6; i++) {
    const loadingCard = document.createElement("div")
    loadingCard.className = "gallery-card loading"
    loadingCard.innerHTML = `
            <div class="loading-image"></div>
            <div class="gallery-content">
                <div class="loading-text"></div>
                <div class="loading-text short"></div>
                <div class="loading-text medium"></div>
            </div>
        `
    coursesGrid.appendChild(loadingCard)
  }
}

// Add CSS for loading animation
const loadingStyles = `
.gallery-card.loading {
    animation: pulse 1.5s ease-in-out infinite;
}

.loading-image {
    width: 100%;
    height: 200px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

.loading-text {
    height: 16px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    margin-bottom: 8px;
    border-radius: 4px;
}

.loading-text.short {
    width: 60%;
}

.loading-text.medium {
    width: 80%;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}
`

// Add loading styles to document
const styleSheet = document.createElement("style")
styleSheet.textContent = loadingStyles
document.head.appendChild(styleSheet)

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
  const burgerCartCount = document.getElementById("burgerCartCount")
  // This would typically get the count from localStorage or a backend
  const count = 0

  if (cartCount) cartCount.textContent = count
  if (burgerCartCount) burgerCartCount.textContent = count
}

function updateWishlistCount() {
  const wishlistCount = document.getElementById("wishlistCount")
  const burgerWishlistCount = document.getElementById("burgerWishlistCount")
  // This would typically get the count from localStorage or a backend
  const count = 0

  if (wishlistCount) {
    wishlistCount.textContent = count
    wishlistCount.style.display = count > 0 ? "block" : "none"
  }
  if (burgerWishlistCount) {
    burgerWishlistCount.textContent = count
  }
}

// Initialize counters
updateCartCount()
updateWishlistCount()

// Export functions for global access
window.applyFilters = applyFilters
window.clearAllFilters = clearAllFilters
window.removeFilter = removeFilter
window.bookGallery = bookGallery
