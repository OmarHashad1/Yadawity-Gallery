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
    image: "./image/slide1.jpg",
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
    image: "./image/AllentownArtMuseum_Gallery01_DiscoverLehighValley_2450c76f-4de5-402c-a060-d0a8ff3b1d37.jpg",
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
    image: "./image/STC_EDS_MINAG_R_L_2011_229-001.jpg",
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
    image: "./image/photo-1554907984-15263bfd63bd.jpeg",
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
    image: "./image/darker_image.webp",
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
    image: "./image/2d58ceedffd1ba6b3e8e2adc4371208f.jpg",
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
    image: "./image/Artist-PainterLookingAtCamera.webp",
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
    image: "./image/artist-sitting-on-the-floor.jpg",
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
    image: "./image/photo.jpeg",
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
    image: "./image/Team image.jpeg",
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
    image: "./image/unnamed.jpg",
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
    image: "./image/_grj4724.jpg",
    description: "Explore abstract art techniques and creative expression",
  },
]

// Global variables
let filteredCourses = [...courses]
let activeFilters = {}
let currentPage = 1
let coursesPerPage = 6 // Show 6 courses per page instead of all 12
let totalPages = 1

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
  filteredCourses = [...courses]
  totalPages = Math.ceil(courses.length / coursesPerPage)
  renderCourses(courses)
  updatePaginationControls()
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

  // Reset to first page when filters change
  currentPage = 1

  // Update UI
  renderActiveFilters()
  renderCourses(filteredCourses)
  updateSearchResults()
  updatePaginationControls()

  // Show/hide no results
  if (filteredCourses.length === 0) {
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
  filteredCourses = [...courses]
  currentPage = 1

  renderActiveFilters()
  renderCourses(courses)
  updateSearchResults()
  updatePaginationControls()

  coursesGrid.style.display = "grid"
  noResults.style.display = "none"
}

// Render courses
function renderCourses(coursesToRender) {
  coursesGrid.innerHTML = ""

  // Pagination logic
  const startIndex = (currentPage - 1) * coursesPerPage
  const endIndex = startIndex + coursesPerPage
  const paginatedCourses = coursesToRender.slice(startIndex, endIndex)

  paginatedCourses.forEach((course) => {
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
      <div class="difficulty-badge difficulty-${course.difficulty}">
        <span class="difficulty-dot"></span>
        <span class="difficulty-text">${course.difficulty}</span>
      </div>
      <div class="course-rating">
        <div class="stars-container">${starsHTML}</div>
        <span class="rating-text">${course.rating}</span>
      </div>
      <img src="${course.image}" alt="${course.title}" class="course-image">
      <div class="course-overlay">
        <div class="quick-actions">
          <button class="quick-action-btn"><i class="fas fa-eye"></i></button>
        </div>
      </div>
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

  // Add quick view event listeners
  document.querySelectorAll('.quick-action-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const courseCard = e.target.closest('.course-card');
      const courseId = parseInt(courseCard.querySelector('.enroll-btn').getAttribute('onclick').match(/\d+/)[0]);
      openQuickView(courseId);
    });
  });

  // Update pagination info
  totalPages = Math.ceil(coursesToRender.length / coursesPerPage)
  updatePaginationInfo()
}

// Open quick view modal
function openQuickView(courseId) {
  const course = courses.find(c => c.id === courseId);
  if (!course) return;
  
  // Create modal overlay
  const overlay = document.createElement('div');
  overlay.className = 'quick-view-overlay';
  overlay.innerHTML = `
    <div class="quick-view-modal">
      <div class="quick-view-content">
        <img src="${course.image}" alt="${course.title}" class="quick-view-image">
        <div class="quick-view-details">
          <h2>${course.title}</h2>
          <p class="instructor">by ${course.instructor}</p>
          <p class="description">${course.description}</p>
          <div class="meta-info">
            <span><i class="fas fa-clock"></i> ${course.duration}</span>
            <span><i class="fas fa-users"></i> ${course.students.toLocaleString()} students</span>
            <span><i class="fas fa-star"></i> ${course.rating}</span>
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

// Update pagination info
function updatePaginationInfo() {
  const paginationInfo = document.getElementById("paginationInfo")
  if (paginationInfo) {
    const startCourse = (currentPage - 1) * coursesPerPage + 1
    const endCourse = Math.min(currentPage * coursesPerPage, filteredCourses.length)
    paginationInfo.textContent = `Showing ${startCourse} - ${endCourse} of ${filteredCourses.length} courses`
  }
}

// Pagination functions
function previousPage() {
  if (currentPage > 1) {
    currentPage--
    renderCourses(filteredCourses)
    updatePaginationControls()
    scrollToTop()
  }
}

function nextPage() {
  if (currentPage < totalPages) {
    currentPage++
    renderCourses(filteredCourses)
    updatePaginationControls()
    scrollToTop()
  }
}

function goToPage(page) {
  if (page >= 1 && page <= totalPages) {
    currentPage = page
    renderCourses(filteredCourses)
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

  // Update course count with pagination info
  updateCourseCountWithPagination()
}

function updateCourseCountWithPagination() {
  const startCourse = (currentPage - 1) * coursesPerPage + 1
  const endCourse = Math.min(currentPage * coursesPerPage, filteredCourses.length)
  
  if (filteredCourses.length === 0) {
    courseCount.textContent = "No courses found"
  } else if (filteredCourses.length <= coursesPerPage) {
    courseCount.textContent = `Showing ${filteredCourses.length} course${filteredCourses.length === 1 ? '' : 's'}`
  } else {
    courseCount.textContent = `Showing ${startCourse}-${endCourse} of ${filteredCourses.length} courses`
  }
}

function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

// Enroll course function
function enrollCourse(courseId) {
  const course = courses.find((c) => c.id === courseId)
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
window.previousPage = previousPage
window.nextPage = nextPage
window.goToPage = goToPage