// Global variables
let virtualGalleries = [];
let filteredGalleries = [];
let currentPage = 1;
const galleriesPerPage = 6;

// Function to apply filters
function applyFilters() {
    filteredGalleries = virtualGalleries.filter(gallery => {
        // Artist filter
        const artistSelect = document.getElementById('artistSelect');
        if (artistSelect && artistSelect.value && artistSelect.value !== 'all') {
            // Compare with artist_full (case-insensitive)
            if ((gallery.artist_full || '').toLowerCase() !== artistSelect.value.toLowerCase()) {
                return false;
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
                    ${gallery.artist_full}
                </div>
                <p class="gallery-description">${gallery.description}</p>
                <div class="gallery-features">
                    <span class="feature-tag">Type: ${gallery.type}</span>
                    <span class="feature-tag">Start: ${gallery.start_date}</span>
                    <span class="feature-tag">End: ${gallery.end_date || ''}</span>
                    <span class="feature-tag">Location: ${gallery.artist_location || ''}</span>
                    <span class="feature-tag">Specialty: ${gallery.artist_specialty || ''}</span>
                    <span class="feature-tag">Bio: ${gallery.artist_bio || ''}</span>
                    <span class="feature-tag">Time Remaining: ${gallery.time_remaining_minutes} min</span>
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
                <div class="gallery-price">$${gallery.price}</div>
                <div class="gallery-actions">
                    <button class="enter-gallery-btn" onclick="enterVirtualGallery(${gallery.id})">
                        Book Visit
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
    const gallery = virtualGalleries.find(g => g.id === galleryId);
    if (gallery) {
        const enterBtn = event.target;
        const originalText = enterBtn.innerHTML;
        enterBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Booking...';
        enterBtn.disabled = true;
        setTimeout(() => {
            alert(
                `🎨 Welcome to "${gallery.title}"!\n\n` +
                `Artist: ${gallery.artist_full}\n` +
                `Duration: ${gallery.duration} minutes\n` +
                `Rating: ${gallery.rating}/5.0\n` +
                `Price: $${gallery.price}\n\n` +
                `Features: ${gallery.features.join(", ")}\n\n` +
                `${gallery.description}\n\n` +
                `🚀 Booking confirmed!\n` +
                `Thank you for choosing Yadawity Virtual Galleries!`
            );
            enterBtn.innerHTML = '<i class="fas fa-check"></i> Booked!';
            enterBtn.style.background = "linear-gradient(45deg, #22c55e, #16a34a)";
            setTimeout(() => {
                enterBtn.innerHTML = originalText;
                enterBtn.style.background = "";
                enterBtn.disabled = false;
            }, 3000);
        }, 2000);
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

// Fetch galleries from API
async function fetchVirtualGalleries() {
    try {
        const response = await fetch('./API/getAllVirtualGallery.php');
        let result;
        try {
            result = await response.json();
        } catch (jsonErr) {
            console.error('JSON parse error:', jsonErr);
            const text = await response.text();
            console.error('Raw response:', text);
            document.getElementById('galleriesContainer').innerHTML = `<div class="no-results">API returned invalid JSON.<br>${jsonErr.message}</div>`;
            return;
        }
        if (result.success && Array.isArray(result.data)) {
            virtualGalleries = result.data.map(gallery => ({
                id: gallery.gallery_id,
                title: gallery.title,
                artist: gallery.artist.first_name ? gallery.artist.first_name.toLowerCase() : '',
                artist_full: gallery.artist.name,
                price: gallery.price || 0,
                duration: gallery.duration,
                rating: 5.0, // Default or you can add rating in DB/API
                image: gallery.artist.profile_picture ? `./image/${gallery.artist.profile_picture}` : './image/Logo.png',
                description: gallery.description,
                features: [gallery.artist.specialty || 'Art', gallery.artist.location || '', gallery.artist.bio || ''],
                type: gallery.status && gallery.status.is_premium ? 'premium' : 'standard',
                available: gallery.is_currently_active
            }));
            // Dynamically populate artist filter
            updateArtistFilter(virtualGalleries);
            filteredGalleries = [...virtualGalleries];
            renderGalleries(filteredGalleries);
            updateResultsCount();
        } else {
            const errorMsg = result && result.error ? result.error : 'No virtual galleries found.';
            document.getElementById('galleriesContainer').innerHTML = `<div class=\"no-results\">${errorMsg}</div>`;
            if (result && result.error) console.error('API error:', result.error);
        }
// Dynamically update artist filter dropdown
function updateArtistFilter(galleries) {
    const artistSelect = document.getElementById('artistSelect');
    if (!artistSelect) return;
    // Get unique artists (by full name)
    const uniqueArtists = Array.from(new Set(galleries.map(g => g.artist_full)));
    let options = '<option value="all">All Artists</option>';
    uniqueArtists.forEach(artist => {
        if (artist && artist.trim() !== '') {
            options += `<option value="${artist.toLowerCase()}">${artist}</option>`;
        }
    });
    artistSelect.innerHTML = options;
}
    } catch (error) {
        console.error('Fetch error:', error);
        document.getElementById('galleriesContainer').innerHTML = `<div class="no-results">Error loading galleries.<br>${error.message}</div>`;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    fetchVirtualGalleries();
    // Set up filter event listeners
    const artistSelect = document.getElementById('artistSelect');
    const priceSelect = document.getElementById('priceSelect');
    const durationSelect = document.getElementById('durationSelect');
    if (artistSelect) artistSelect.addEventListener('change', applyFilters);
    if (priceSelect) priceSelect.addEventListener('change', applyFilters);
    if (durationSelect) durationSelect.addEventListener('change', applyFilters);
});

// Additional functions for the new card design
function viewGalleryDetails(galleryId) {
    const gallery = virtualGalleries.find(g => g.id === galleryId);
    if (gallery) {
        alert(`Gallery Details:\n\nTitle: ${gallery.title}\nArtist: ${gallery.artist_full}\nPrice: $${gallery.price}\nDuration: ${gallery.duration} minutes\nRating: ${gallery.rating}\n\nDescription: ${gallery.description}`);
    }
}

function addToWishlist(galleryId) {
    const gallery = virtualGalleries.find(g => g.id === galleryId);
    if (gallery) {
        alert(`"${gallery.title}" has been added to your wishlist!`);
        // Example: Add visual feedback
        const wishlistBtn = event.target.closest('.wishlistBtn');
        if (wishlistBtn) {
            wishlistBtn.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(() => {
                wishlistBtn.innerHTML = '<i class="fas fa-heart"></i>';
            }, 2000);
        }
    }
}