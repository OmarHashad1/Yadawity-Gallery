// Sample courses data
const courses = [
  {
    id: 1,
    title: "Digital Painting Masterclass",
    instructor: "Sarah Johnson",
    category: "Digital Art",
    difficulty: "intermediate",
    duration: "12 weeks",
    students: 1250,
    rating: 4.8,
    price: 149,
    originalPrice: 199,
    image: "/placeholder.svg?height=220&width=320",
    description: "Master digital painting techniques from concept to completion",
  },
  {
    id: 2,
    title: "Traditional Watercolor Basics",
    instructor: "Ahmed Hassan",
    category: "Traditional Art",
    difficulty: "beginner",
    duration: "8 weeks",
    students: 890,
    rating: 4.6,
    price: 89,
    originalPrice: null,
    image: "/placeholder.svg?height=220&width=320",
    description: "Learn the fundamentals of watercolor painting",
  },
  {
    id: 3,
    title: "Character Design for Games",
    instructor: "Maria Rodriguez",
    category: "Concept Art",
    difficulty: "advanced",
    duration: "16 weeks",
    students: 567,
    rating: 4.9,
    price: 299,
    originalPrice: 399,
    image: "/placeholder.svg?height=220&width=320",
    description: "Create compelling characters for video games and animation",
  },
  {
    id: 4,
    title: "Portrait Drawing Fundamentals",
    instructor: "David Chen",
    category: "Drawing",
    difficulty: "beginner",
    duration: "10 weeks",
    students: 1456,
    rating: 4.7,
    price: 119,
    originalPrice: null,
    image: "/placeholder.svg?height=220&width=320",
    description: "Master the art of realistic portrait drawing",
  },
  {
    id: 5,
    title: "3D Modeling with Blender",
    instructor: "Alex Thompson",
    category: "3D Art",
    difficulty: "intermediate",
    duration: "14 weeks",
    students: 723,
    rating: 4.5,
    price: 199,
    originalPrice: 249,
    image: "/placeholder.svg?height=220&width=320",
    description: "Learn professional 3D modeling techniques",
  },
  {
    id: 6,
    title: "Fine Art Oil Painting",
    instructor: "Isabella Martinez",
    category: "Fine Art",
    difficulty: "advanced",
    duration: "18 weeks",
    students: 345,
    rating: 4.9,
    price: 349,
    originalPrice: null,
    image: "/placeholder.svg?height=220&width=320",
    description: "Classical oil painting techniques and methods",
  },
  {
    id: 7,
    title: "Ceramic Pottery Workshop",
    instructor: "Michael Brown",
    category: "Ceramics",
    difficulty: "beginner",
    duration: "6 weeks",
    students: 234,
    rating: 4.4,
    price: 159,
    originalPrice: 189,
    image: "/placeholder.svg?height=220&width=320",
    description: "Hands-on pottery and ceramic techniques",
  },
  {
    id: 8,
    title: "Street Art & Murals",
    instructor: "Carlos Rivera",
    category: "Street Art",
    difficulty: "intermediate",
    duration: "8 weeks",
    students: 456,
    rating: 4.6,
    price: 129,
    originalPrice: null,
    image: "/placeholder.svg?height=220&width=320",
    description: "Urban art techniques and mural creation",
  },
  {
    id: 9,
    title: "Fashion Illustration",
    instructor: "Sophie Laurent",
    category: "Fashion Art",
    difficulty: "intermediate",
    duration: "10 weeks",
    students: 678,
    rating: 4.7,
    price: 179,
    originalPrice: 219,
    image: "/placeholder.svg?height=220&width=320",
    description: "Create stunning fashion illustrations and designs",
  },
  {
    id: 10,
    title: "Typography & Lettering",
    instructor: "James Wilson",
    category: "Typography",
    difficulty: "beginner",
    duration: "7 weeks",
    students: 892,
    rating: 4.5,
    price: 99,
    originalPrice: null,
    image: "/placeholder.svg?height=220&width=320",
    description: "Master the art of beautiful lettering and typography",
  },
  {
    id: 11,
    title: "Photography Composition",
    instructor: "Emma Davis",
    category: "Photography",
    difficulty: "beginner",
    duration: "9 weeks",
    students: 1123,
    rating: 4.8,
    price: 139,
    originalPrice: 169,
    image: "/placeholder.svg?height=220&width=320",
    description: "Learn professional photography composition techniques",
  },
  {
    id: 12,
    title: "Abstract Art Exploration",
    instructor: "Robert Kim",
    category: "Fine Art",
    difficulty: "intermediate",
    duration: "11 weeks",
    students: 445,
    rating: 4.6,
    price: 189,
    originalPrice: null,
    image: "/placeholder.svg?height=220&width=320",
    description: "Explore abstract art techniques and creative expression",
  },
]

// Global variables
let filteredCourses = [...courses]
let activeFilters = {}

// DOM elements
const searchInput = document.getElementById("searchInput")
const categoryFilter = document.getElementById("categoryFilter")
const difficultyFilter = document.getElementById("difficultyFilter")
const minPriceInput = document.getElementById("minPrice")
const maxPriceInput = document.getElementById("maxPrice")
const durationFilter = document.getElementById("durationFilter")
const activeFiltersContainer = document.getElementById("activeFilters")
const searchResults = document.getElementById("searchResults")
const coursesGrid = document.getElementById("coursesGrid")
const courseCount = document.getElementById("courseCount")
const noResults = document.getElementById("noResults")

// Initialize the page
document.addEventListener("DOMContentLoaded", () => {
  renderCourses(courses)
  updateCourseCount(courses.length)
  setupEventListeners()
  setupNavigation()
})

// Setup event listeners
function setupEventListeners() {
  // Search input
  searchInput.addEventListener("input", debounce(applyFilters, 300))

  // Filter dropdowns and inputs
  categoryFilter.addEventListener("change", applyFilters)
  difficultyFilter.addEventListener("change", applyFilters)
  durationFilter.addEventListener("change", applyFilters)
  minPriceInput.addEventListener("input", debounce(applyFilters, 300))
  maxPriceInput.addEventListener("input", debounce(applyFilters, 300))
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
  const selectedCategory = categoryFilter.value
  const selectedDifficulty = difficultyFilter.value
  const selectedDuration = durationFilter.value
  const minPrice = Number.parseFloat(minPriceInput.value) || 0
  const maxPrice = Number.parseFloat(maxPriceInput.value) || Number.POSITIVE_INFINITY

  // Reset active filters
  activeFilters = {}

  // Filter courses
  filteredCourses = courses.filter((course) => {
    let matches = true

    // Search term filter
    if (searchTerm) {
      const searchableText =
        `${course.title} ${course.instructor} ${course.category} ${course.description}`.toLowerCase()
      matches = matches && searchableText.includes(searchTerm)
      if (searchTerm) activeFilters.search = searchTerm
    }

    // Category filter
    if (selectedCategory) {
      matches = matches && course.category === selectedCategory
      activeFilters.category = selectedCategory
    }

    // Difficulty filter
    if (selectedDifficulty) {
      matches = matches && course.difficulty === selectedDifficulty
      activeFilters.difficulty = selectedDifficulty
    }

    // Duration filter
    if (selectedDuration) {
      const weeks = Number.parseInt(course.duration)
      let durationMatch = false

      if (selectedDuration === "short" && weeks <= 8) durationMatch = true
      if (selectedDuration === "medium" && weeks >= 9 && weeks <= 15) durationMatch = true
      if (selectedDuration === "long" && weeks >= 16) durationMatch = true

      matches = matches && durationMatch
      activeFilters.duration = selectedDuration
    }

    // Price filter
    if (minPrice > 0 || maxPrice < Number.POSITIVE_INFINITY) {
      matches = matches && course.price >= minPrice && course.price <= maxPrice
      if (minPrice > 0 || maxPrice < Number.POSITIVE_INFINITY) {
        activeFilters.price = `$${minPrice} - $${maxPrice === Number.POSITIVE_INFINITY ? "∞" : maxPrice}`
      }
    }

    return matches
  })

  // Update UI
  renderActiveFilters()
  renderCourses(filteredCourses)
  updateSearchResults()
  updateCourseCount(filteredCourses.length)

  // Show/hide no results
  if (filteredCourses.length === 0) {
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
  filteredCourses = [...courses]

  renderActiveFilters()
  renderCourses(courses)
  updateSearchResults()
  updateCourseCount(courses.length)

  coursesGrid.style.display = "grid"
  noResults.style.display = "none"
}

// Render courses
function renderCourses(coursesToRender) {
  coursesGrid.innerHTML = ""

  coursesToRender.forEach((course) => {
    const courseCard = document.createElement("div")
    courseCard.className = "course-card"

    const starsHTML = Array(5)
      .fill()
      .map((_, i) => `<span class="star">${i < Math.floor(course.rating) ? "★" : "☆"}</span>`)
      .join("")

    const priceHTML = course.originalPrice
      ? `$${course.price} <span class="original-price">$${course.originalPrice}</span>`
      : `$${course.price}`

    courseCard.innerHTML = `
      <div class="difficulty-badge difficulty-${course.difficulty}">${course.difficulty}</div>
      <div class="course-rating">
        <div class="stars-container">${starsHTML}</div>
        <span class="rating-text">${course.rating}</span>
      </div>
      <img src="${course.image}" alt="${course.title}" class="course-image">
      <div class="course-content">
        <h3 class="course-title">${course.title}</h3>
        <div class="course-instructor">by ${course.instructor}</div>
        <div class="course-category">${course.category}</div>
        <div class="course-meta">
          <div class="course-duration">
            <i class="fas fa-clock"></i>
            ${course.duration}
          </div>
          <div class="course-students">
            <i class="fas fa-users"></i>
            ${course.students.toLocaleString()}
          </div>
        </div>
        <div class="course-price-info">
          <div class="course-price">
            <span class="price">${priceHTML}</span>
          </div>
        </div>
        <button class="enroll-btn" onclick="enrollCourse(${course.id})">
          <i class="fas fa-graduation-cap"></i> Enroll Now
        </button>
      </div>
    `

    coursesGrid.appendChild(courseCard)
  })
}

// Update search results text
function updateSearchResults() {
  if (Object.keys(activeFilters).length > 0) {
    const filterCount = Object.keys(activeFilters).length
    const resultCount = filteredCourses.length
    searchResults.innerHTML = `Found ${resultCount} courses with ${filterCount} active filter${filterCount > 1 ? "s" : ""}`
    searchResults.style.display = "block"
  } else {
    searchResults.style.display = "none"
  }
}

// Update course count
function updateCourseCount(count) {
  if (count === courses.length) {
    courseCount.textContent = `Showing all ${count} courses`
  } else {
    courseCount.textContent = `Showing ${count} of ${courses.length} courses`
  }
}

// Enroll course function
function enrollCourse(courseId) {
  const course = courses.find((c) => c.id === courseId)
  if (course) {
    alert(
      `🎨 Enrollment confirmed for "${course.title}"!\n\n` +
        `Instructor: ${course.instructor}\n` +
        `Category: ${course.category}\n` +
        `Duration: ${course.duration}\n` +
        `Difficulty: ${course.difficulty.charAt(0).toUpperCase() + course.difficulty.slice(1)}\n` +
        `Price: $${course.price}\n\n` +
        `Welcome to Yadawity Art Academy!\n` +
        `You'll receive course materials and access details via email.`,
    )

    // Here you would typically integrate with a enrollment system
    // For now, we'll just show a success message
    const enrollBtn = event.target
    const originalText = enrollBtn.innerHTML
    enrollBtn.innerHTML = '<i class="fas fa-check"></i> Enrolled!'
    enrollBtn.style.background = "linear-gradient(45deg, #22c55e, #16a34a)"
    enrollBtn.disabled = true

    setTimeout(() => {
      enrollBtn.innerHTML = originalText
      enrollBtn.style.background = ""
      enrollBtn.disabled = false
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
window.enrollCourse = enrollCourse