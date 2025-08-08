// Artwork Pagination and Filtering System
// Clean implementation with exactly 6 artworks per page

// Global variables
let allArtworks = []
let filteredArtworks = []
let currentPage = 1
const ARTWORKS_PER_PAGE = 6 // Exactly 6 artworks per page
let totalPages = 1

// DOM elements
const searchInput = document.getElementById("searchInput")
const categoryFilter = document.getElementById("categoryFilter")
const sortByFilter = document.getElementById("sortBy")
const minPriceInput = document.getElementById("minPrice")
const maxPriceInput = document.getElementById("maxPrice")
const artworksGrid = document.getElementById("artworksGrid")

// Initialize the page
document.addEventListener("DOMContentLoaded", () => {
  initializeArtworkSystem()
})

// Initialize the artwork system
function initializeArtworkSystem() {
  // Get all artwork cards and store them
  allArtworks = Array.from(document.querySelectorAll('.enhanced-artwork-card'))
  filteredArtworks = [...allArtworks]
  
  // Calculate total pages
  calculateTotalPages()
  
  // Setup event listeners
  setupEventListeners()
  
  // Initial render
  renderCurrentPage()
  updatePaginationDisplay()
}

// Setup event listeners
function setupEventListeners() {
  if (searchInput) {
    searchInput.addEventListener("input", debounce(applyFilters, 300))
  }
  if (categoryFilter) categoryFilter.addEventListener("change", applyFilters)
  if (sortByFilter) sortByFilter.addEventListener("change", applyFilters)
  if (minPriceInput) minPriceInput.addEventListener("input", debounce(applyFilters, 300))
  if (maxPriceInput) maxPriceInput.addEventListener("input", debounce(applyFilters, 300))
}

// Apply all filters
function applyFilters() {
  const searchTerm = searchInput?.value.toLowerCase().trim() || ''
  const selectedCategory = categoryFilter?.value || 'all'
  const selectedSort = sortByFilter?.value || 'featured'
  const minPrice = parseInt(minPriceInput?.value) || 0
  const maxPrice = parseInt(maxPriceInput?.value) || Infinity

  // Start with all artworks
  filteredArtworks = allArtworks.filter(artwork => {
    // Search filter
    if (searchTerm) {
      const title = artwork.querySelector('.enhanced-artwork-title')?.textContent.toLowerCase() || ''
      const artist = artwork.querySelector('.enhanced-artwork-artist')?.textContent.toLowerCase() || ''
      const category = artwork.dataset.category?.toLowerCase() || ''
      const description = artwork.querySelector('.enhanced-artwork-description')?.textContent.toLowerCase() || ''
      
      const searchableText = `${title} ${artist} ${category} ${description}`
      if (!searchableText.includes(searchTerm)) return false
    }

    // Category filter
    if (selectedCategory !== 'all' && artwork.dataset.category !== selectedCategory) {
      return false
    }

    // Price filter
    const price = parseInt(artwork.dataset.price) || 0
    if (price < minPrice || price > maxPrice) {
      return false
    }

    return true
  })

  // Sort artworks
  sortArtworks(selectedSort)
  
  // Reset to first page
  currentPage = 1
  
  // Update display
  calculateTotalPages()
  renderCurrentPage()
  updatePaginationDisplay()
}

// Sort artworks based on selected option
function sortArtworks(sortBy) {
  switch (sortBy) {
    case 'price-low':
      filteredArtworks.sort((a, b) => 
        (parseInt(a.dataset.price) || 0) - (parseInt(b.dataset.price) || 0)
      )
      break
    case 'price-high':
      filteredArtworks.sort((a, b) => 
        (parseInt(b.dataset.price) || 0) - (parseInt(a.dataset.price) || 0)
      )
      break
    case 'artist':
      filteredArtworks.sort((a, b) => {
        const artistA = a.querySelector('.enhanced-artwork-artist')?.textContent || ''
        const artistB = b.querySelector('.enhanced-artwork-artist')?.textContent || ''
        return artistA.localeCompare(artistB)
      })
      break
    case 'newest':
      // If you have date data, implement here. For now, reverse order
      filteredArtworks.reverse()
      break
    default: // 'featured'
      // Keep original order
      break
  }
}

// Calculate total pages based on filtered artworks
function calculateTotalPages() {
  totalPages = Math.ceil(filteredArtworks.length / ARTWORKS_PER_PAGE)
  if (totalPages === 0) totalPages = 1
}

// Render artworks for current page
function renderCurrentPage() {
  // Hide all artworks first
  allArtworks.forEach(artwork => {
    artwork.style.display = 'none'
  })

  // Calculate which artworks to show
  const startIndex = (currentPage - 1) * ARTWORKS_PER_PAGE
  const endIndex = startIndex + ARTWORKS_PER_PAGE
  const artworksToShow = filteredArtworks.slice(startIndex, endIndex)

  // Show artworks for current page
  artworksToShow.forEach(artwork => {
    artwork.style.display = 'block'
  })

  // Scroll to top smoothly
  scrollToTop()
}

// Update pagination display
function updatePaginationDisplay() {
  const paginationSection = document.querySelector(".pagination-section")
  const prevBtn = document.getElementById("prevBtn")
  const nextBtn = document.getElementById("nextBtn")
  const paginationNumbers = document.getElementById("paginationNumbers")

  // Show/hide pagination section
  if (totalPages <= 1) {
    if (paginationSection) paginationSection.style.display = "none"
    return
  } else {
    if (paginationSection) paginationSection.style.display = "block"
  }

  // Update navigation buttons
  if (prevBtn) {
    prevBtn.disabled = currentPage === 1
    prevBtn.classList.toggle('disabled', currentPage === 1)
  }

  if (nextBtn) {
    nextBtn.disabled = currentPage === totalPages
    nextBtn.classList.toggle('disabled', currentPage === totalPages)
  }

  // Update page numbers
  if (paginationNumbers) {
    paginationNumbers.innerHTML = ""
    
    // Simple pagination for better UX
    if (totalPages <= 7) {
      // Show all pages if 7 or fewer
      for (let i = 1; i <= totalPages; i++) {
        createPageButton(i, paginationNumbers)
      }
    } else {
      // Show smart pagination with ellipsis
      createSmartPagination(paginationNumbers)
    }
  }
}

// Create a page button
function createPageButton(pageNum, container) {
  const button = document.createElement("button")
  button.className = `pagination-number ${pageNum === currentPage ? "active" : ""}`
  button.textContent = pageNum
  button.onclick = () => goToPage(pageNum)
  container.appendChild(button)
}

// Create smart pagination with ellipsis
function createSmartPagination(container) {
  const delta = 2 // Number of pages to show around current page

  // Always show first page
  createPageButton(1, container)

  // Show ellipsis if needed
  if (currentPage > delta + 2) {
    const ellipsis = document.createElement("span")
    ellipsis.className = "pagination-dots"
    ellipsis.textContent = "..."
    container.appendChild(ellipsis)
  }

  // Show pages around current page
  const start = Math.max(2, currentPage - delta)
  const end = Math.min(totalPages - 1, currentPage + delta)

  for (let i = start; i <= end; i++) {
    createPageButton(i, container)
  }

  // Show ellipsis if needed
  if (currentPage < totalPages - delta - 1) {
    const ellipsis = document.createElement("span")
    ellipsis.className = "pagination-dots"
    ellipsis.textContent = "..."
    container.appendChild(ellipsis)
  }

  // Always show last page (if not already shown)
  if (totalPages > 1) {
    createPageButton(totalPages, container)
  }
}

// Navigation functions
function previousPage() {
  if (currentPage > 1) {
    currentPage--
    renderCurrentPage()
    updatePaginationDisplay()
  }
}

function nextPage() {
  if (currentPage < totalPages) {
    currentPage++
    renderCurrentPage()
    updatePaginationDisplay()
  }
}

function goToPage(page) {
  if (page >= 1 && page <= totalPages && page !== currentPage) {
    currentPage = page
    renderCurrentPage()
    updatePaginationDisplay()
  }
}

// Clear all filters
function clearAllFilters() {
  if (searchInput) searchInput.value = ""
  if (categoryFilter) categoryFilter.value = "all"
  if (sortByFilter) sortByFilter.value = "featured"
  if (minPriceInput) minPriceInput.value = ""
  if (maxPriceInput) maxPriceInput.value = ""

  // Reset to show all artworks
  filteredArtworks = [...allArtworks]
  currentPage = 1
  
  calculateTotalPages()
  renderCurrentPage()
  updatePaginationDisplay()
}

// Utility functions
function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

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

// Export functions for global access
window.applyFilters = applyFilters
window.clearAllFilters = clearAllFilters
window.previousPage = previousPage
window.nextPage = nextPage
window.goToPage = goToPage