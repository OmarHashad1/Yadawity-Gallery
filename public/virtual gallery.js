// Sample virtual galleries data
const virtualGalleries = [
  {
    id: 1,
    title: "Contemporary Art VR Experience",
    artist: "mohamed",
    price: 25,
    duration: 45,
    rating: 4.8,
    image: "./image/slide1.jpg",
    description: "Experience cutting-edge contemporary art in virtual reality",
    features: ["VR Compatible", "Interactive", "360° View"],
    type: "premium",
    available: true
  },
  {
    id: 2,
    title: "Watercolor Dreams Virtual Gallery",
    artist: "ahmed",
    price: 18,
    duration: 35,
    rating: 4.6,
    image: "./image/AllentownArtMuseum_Gallery01_DiscoverLehighValley_2450c76f-4de5-402c-a060-d0a8ff3b1d37.jpg",
    description: "Traditional watercolor masterpieces in immersive virtual environment",
    features: ["HD Quality", "Guided Tour", "Art History"],
    type: "standard",
    available: true
  },
  {
    id: 3,
    title: "Digital Art Interactive Showcase",
    artist: "essraa",
    price: 32,
    duration: 60,
    rating: 4.9,
    image: "./image/STC_EDS_MINAG_R_L_2011_229-001.jpg",
    description: "Innovative digital artworks and interactive installations",
    features: ["Interactive", "AR Elements", "Behind-the-scenes"],
    type: "premium",
    available: true
  },
  {
    id: 4,
    title: "Portrait Gallery Virtual Tour",
    artist: "noor",
    price: 22,
    duration: 40,
    rating: 4.7,
    image: "./image/photo-1554907984-15263bfd63bd.jpeg",
    description: "Stunning portrait collection from emerging artists",
    features: ["VR Compatible", "Artist Commentary", "Close-up Details"],
    type: "standard",
    available: true
  },
  {
    id: 5,
    title: "3D Sculpture Garden Experience",
    artist: "samaa",
    price: 28,
    duration: 50,
    rating: 4.5,
    image: "./image/darker_image.webp",
    description: "Beautiful 3D sculpture exhibition in virtual space",
    features: ["3D Modeling", "Spatial Audio", "Interactive"],
    type: "premium",
    available: true
  },
  {
    id: 6,
    title: "Photography Studio Virtual Visit",
    artist: "mariem",
    price: 35,
    duration: 55,
    rating: 4.9,
    image: "./image/2d58ceedffd1ba6b3e8e2adc4371208f.jpg",
    description: "Contemporary photography exhibition and workspace tour",
    features: ["Ultra HD", "Professional Insights", "Equipment Demo"],
    type: "premium",
    available: true
  },
  {
    id: 7,
    title: "Mixed Media Workshop VR",
    artist: "soha",
    price: 42,
    duration: 75,
    rating: 4.4,
    image: "./image/Artist-PainterLookingAtCamera.webp",
    description: "Hands-on mixed media art experience in virtual reality",
    features: ["Interactive Workshop", "Material Demo", "Technique Guide"],
    type: "premium",
    available: true
  },
  {
    id: 8,
    title: "Emerging Artists Collective",
    artist: "essam",
    price: 15,
    duration: 30,
    rating: 4.3,
    image: "./image/artist-sitting-on-the-floor.jpg",
    description: "Discover new talent in this virtual emerging artists showcase",
    features: ["Emerging Artists", "Fresh Perspectives", "Affordable"],
    type: "standard",
    available: true
  },
  {
    id: 9,
    title: "Master Painter's Studio",
    artist: "mazen",
    price: 65,
    duration: 90,
    rating: 4.8,
    image: "./image/d4s5689-2-300dpi-1500x998.jpg",
    description: "Exclusive access to master painter's private studio",
    features: ["Master Class", "Exclusive Access", "Technique Analysis"],
    type: "premium",
    available: true
  },
  {
    id: 10,
    title: "Abstract Art Journey",
    artist: "noraa",
    price: 38,
    duration: 65,
    rating: 4.6,
    image: "./image/photoo.webp",
    description: "Journey through abstract art movements and styles",
    features: ["Art History", "Movement Analysis", "Interactive Timeline"],
    type: "standard",
    available: true
  },
  {
    id: 11,
    title: "Cultural Heritage VR Experience",
    artist: "nermen",
    price: 78,
    duration: 120,
    rating: 4.9,
    image: "./image/https___s3.us-east-1.amazonaws.com_uploads.thevendry.co_23050_1701148429274_240517998_10159366377565970_3232763438623679454_n.webp",
    description: "Immersive cultural heritage experience with historical context",
    features: ["Cultural Heritage", "Historical Context", "Educational", "Premium Experience"],
    type: "premium",
    available: true
  }
]

// Global variables
let filteredGalleries = [...virtualGalleries]
let currentPage = 1
const galleriesPerPage = 6

// Function to apply filters
function applyFilters() {
    filteredGalleries = virtualGalleries.filter(gallery => {
        // Artist filter
        const artistSelect = document.getElementById('artistSelect')
        if (artistSelect && artistSelect.value && artistSelect.value !== 'all') {
            if (gallery.artist.toLowerCase() !== artistSelect.value.toLowerCase()) {
                return false
            }
        }

        // Price filter
        const priceSelect = document.getElementById('priceSelect')
        if (priceSelect && priceSelect.value && priceSelect.value !== 'all') {
            const priceRange = priceSelect.value
            if (priceRange === '100+') {
                if (gallery.price <= 100) return false
            } else {
                const [min, max] = priceRange.split('-').map(Number)
                if (gallery.price < min || gallery.price > max) {
                    return false
                }
            }
        }

        // Duration filter
        const durationSelect = document.getElementById('durationSelect')
        if (durationSelect && durationSelect.value && durationSelect.value !== 'all') {
            const durationRange = durationSelect.value
            if (durationRange === '120+') {
                if (gallery.duration <= 120) return false
            } else {
                const [min, max] = durationRange.split('-').map(Number)
                if (gallery.duration < min || gallery.duration > max) {
                    return false
                }
            }
        }

        return true
    })

    currentPage = 1
    renderGalleries(filteredGalleries)
    updateResultsCount()
}

// Function to render galleries
function renderGalleries(galleriesToRender) {
    const galleriesContainer = document.getElementById('galleriesContainer')
    const startIndex = (currentPage - 1) * galleriesPerPage
    const endIndex = startIndex + galleriesPerPage
    const currentGalleries = galleriesToRender.slice(startIndex, endIndex)

    if (currentGalleries.length === 0) {
        galleriesContainer.innerHTML = '<div class="no-results">No virtual galleries found matching your criteria.</div>'
        return
    }

    const galleriesHTML = currentGalleries.map(gallery => `
        <div class="virtual-gallery-card">
            <div class="gallery-image-container">
                <div class="availability-badge ${gallery.available ? 'available' : 'unavailable'}">
                    ${gallery.available ? 'AVAILABLE' : 'UNAVAILABLE'}
                </div>
                <img src="${gallery.image}" alt="${gallery.title}" class="gallery-image">
            </div>
            <div class="gallery-content">
                <h3 class="gallery-title">${gallery.title}</h3>
                <div class="gallery-artist">
                    <i class="fas fa-user-circle"></i>
                    ${gallery.artist.charAt(0).toUpperCase() + gallery.artist.slice(1)}
                </div>
                <p class="gallery-description">${gallery.description}</p>
                <div class="gallery-features">
                    ${gallery.features.map(feature => `<span class="feature-tag">${feature}</span>`).join('')}
                </div>
                <div class="gallery-bottom-info">
                    <div class="gallery-duration">
                        <i class="fas fa-clock"></i>
                        ${gallery.duration} min
                    </div>
                    <div class="gallery-rating">
                        <i class="fas fa-star"></i>
                        ${gallery.rating}
                    </div>
                </div>
                <div class="gallery-price">${gallery.price}</div>
                <div class="gallery-actions">
                    <button class="enter-gallery-btn" onclick="enterVirtualGallery(${gallery.id})" ${!gallery.available ? 'disabled' : ''}>
                        ${gallery.available ? 'Enter Gallery' : 'Unavailable'}
                    </button>
                    <button class="wishlist-btn" onclick="addToWishlist(${gallery.id})" title="Add to Wishlist">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('')

    galleriesContainer.innerHTML = galleriesHTML
    updatePagination(galleriesToRender.length)
}

// Pagination functions
function changePage(direction) {
    currentPage += direction
    renderGalleries(filteredGalleries)
}

function previousPage() {
    if (currentPage > 1) {
        changePage(-1)
    }
}

function nextPage() {
    const totalPages = Math.ceil(filteredGalleries.length / galleriesPerPage)
    if (currentPage < totalPages) {
        changePage(1)
    }
}

function goToPage(page) {
    currentPage = page
    renderGalleries(filteredGalleries)
}

// Function to update pagination
function updatePagination(totalGalleries) {
    const totalPages = Math.ceil(totalGalleries / galleriesPerPage)
    const prevBtn = document.getElementById('prevBtn')
    const nextBtn = document.getElementById('nextBtn')
    const pageInfo = document.getElementById('pageInfo')
    const paginationNumbers = document.getElementById('paginationNumbers')

    if (prevBtn) prevBtn.disabled = currentPage === 1
    if (nextBtn) nextBtn.disabled = currentPage === totalPages || totalPages === 0
    if (pageInfo) pageInfo.textContent = totalPages > 0 ? `Page ${currentPage} of ${totalPages}` : 'No results'
    
    // Update pagination numbers
    if (paginationNumbers && totalPages > 0) {
        let paginationHTML = ''
        
        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === currentPage ? 'active' : ''
            paginationHTML += `<button class="pagination-number ${activeClass}" onclick="goToPage(${i})">${i}</button>`
        }
        
        paginationNumbers.innerHTML = paginationHTML
    }
}

// Function to update results count
function updateResultsCount() {
    const resultsCount = document.getElementById('resultsCount')
    if (resultsCount) {
        resultsCount.textContent = `Showing ${filteredGalleries.length} virtual galleries`
    }
}

// Function to enter virtual gallery
function enterVirtualGallery(galleryId) {
    const gallery = virtualGalleries.find(g => g.id === galleryId)
    if (gallery && gallery.available) {
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
                `Duration: ${gallery.duration} minutes\n` +
                `Rating: ${gallery.rating}/5.0\n` +
                `Price: $${gallery.price}\n\n` +
                `Features: ${gallery.features.join(", ")}\n\n` +
                `${gallery.description}\n\n` +
                `🚀 Launching virtual experience...\n` +
                `Thank you for choosing Yadawity Virtual Galleries!`
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

// Clear all filters function
function clearAllFilters() {
    const artistSelect = document.getElementById('artistSelect')
    const priceSelect = document.getElementById('priceSelect')
    const durationSelect = document.getElementById('durationSelect')
    
    if (artistSelect) artistSelect.value = 'all'
    if (priceSelect) priceSelect.value = 'all'
    if (durationSelect) durationSelect.value = 'all'
    
    filteredGalleries = [...virtualGalleries]
    currentPage = 1
    renderGalleries(virtualGalleries)
    updateResultsCount()
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    renderGalleries(virtualGalleries)
    updateResultsCount()
    
    // Set up filter event listeners
    const artistSelect = document.getElementById('artistSelect')
    const priceSelect = document.getElementById('priceSelect')
    const durationSelect = document.getElementById('durationSelect')
    
    if (artistSelect) artistSelect.addEventListener('change', applyFilters)
    if (priceSelect) priceSelect.addEventListener('change', applyFilters)
    if (durationSelect) durationSelect.addEventListener('change', applyFilters)
});

// Additional functions for the new card design
function viewGalleryDetails(galleryId) {
    const gallery = virtualGalleries.find(g => g.id === galleryId)
    if (gallery) {
        // You can implement a modal or redirect to a details page
        alert(`Gallery Details:\n\nTitle: ${gallery.title}\nArtist: ${gallery.artist}\nPrice: $${gallery.price}\nDuration: ${gallery.duration} minutes\nRating: ${gallery.rating}\n\nDescription: ${gallery.description}`)
    }
}

function addToWishlist(galleryId) {
    const gallery = virtualGalleries.find(g => g.id === galleryId)
    if (gallery) {
        // You can implement wishlist functionality here
        alert(`"${gallery.title}" has been added to your wishlist!`)
        // Example: Add visual feedback
        const wishlistBtn = event.target.closest('.wishlistBtn')
        if (wishlistBtn) {
            wishlistBtn.innerHTML = '<i class="fas fa-check"></i>'
            setTimeout(() => {
                wishlistBtn.innerHTML = '<i class="fas fa-heart"></i>'
            }, 2000)
        }
    }
}