// Fetch courses from API
let courses = [];
let filteredCourses = [];
let activeFilters = {};
let currentPage = 1;
let coursesPerPage = 6; // Show 6 courses per page
let totalPages = 1;

async function fetchCoursesFromAPI() {
  try {
    const response = await fetch('./API/getAllcourses.php');
    const result = await response.json();
    if (result.success && Array.isArray(result.data)) {
      // Map API data to expected format for rendering
      courses = result.data.map(course => ({
        id: course.course_id,
        title: course.title,
        instructor: course.artist ? course.artist.full_name : 'Unknown',
        category: course.course_type || '',
        difficulty: course.difficulty || '',
        duration: course.duration_date ? `${course.duration_date} weeks` : '',
        students: course.enrollments ? course.enrollments.total : 0,
        rating: course.rate || 0,
        price: course.price || 0,
        originalPrice: null, // You can update this if your API provides it
        image: course.thumbnail_url ? course.thumbnail_url.replace('..', '.') : './image/placeholder.jpg',
        description: course.description || '',
      }));
      filteredCourses = [...courses];
      totalPages = Math.ceil(courses.length / coursesPerPage);
      updateFilterDropdowns();
      renderCourses(courses);
      updatePaginationControls();
    } else {
      courses = [];
      filteredCourses = [];
      updateFilterDropdowns();
      renderCourses([]);
    }
  } catch (error) {
    console.error('Failed to fetch courses:', error);
    courses = [];
    filteredCourses = [];
    updateFilterDropdowns();
    renderCourses([]);
  }
}

// Dynamically update filter dropdowns based on course data
function updateFilterDropdowns() {
  // Category
  const categories = Array.from(new Set(courses.map(c => c.category).filter(Boolean)));
  categoryFilter.innerHTML = '<option value="">All Categories</option>' +
    categories.map(cat => `<option value="${cat}">${cat}</option>`).join('');

  // Difficulty
  const difficulties = Array.from(new Set(courses.map(c => c.difficulty).filter(Boolean)));
  difficultyFilter.innerHTML = '<option value="">All Levels</option>' +
    difficulties.map(diff => `<option value="${diff}">${capitalize(diff)}</option>`).join('');

  // Duration (show unique week values, e.g. 8 weeks, 12 weeks)
  const durations = Array.from(new Set(courses.map(c => c.duration).filter(Boolean)));
  durationFilter.innerHTML = '<option value="">Any Duration</option>' +
    durations.map(dur => `<option value="${dur}">${dur}</option>`).join('');
}

function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}
// ...existing code...
// ...existing code...

// Global variables

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
  fetchCoursesFromAPI();
  setupEventListeners();
  setupNavigation();
});

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