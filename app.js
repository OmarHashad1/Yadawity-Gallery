// Yadawity Navbar Component JavaScript

// Initialize navbar functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all navbar functions
    initializeNavbar();
});

function initializeNavbar() {
    // Mobile menu toggle
    setupMobileMenu();
    
    // Search functionality
    setupSearch();
    
    // User dropdown
    setupUserDropdown();
    
    // Navbar scroll effects
    
    // Cart and wishlist counters
    setupCounters();
    
    // Active page detection
    setActivePage();
}

// Mobile Menu Toggle
function setupMobileMenu() {
    const navToggle = document.querySelector('.navToggle');
    const navMenu = document.querySelector('.navMenu');

    if (navToggle) {
        navToggle.addEventListener('click', function() {
            // Open the burger menu overlay instead of toggling navbar
            if (window.toggleBurgerMenu) {
                window.toggleBurgerMenu();
            }
        });

        // Close mobile menu when clicking on links (legacy support)
        const navLinks = document.querySelectorAll('.navLink');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (navMenu) {
                    navMenu.classList.remove('active');
                }
                navToggle.classList.remove('active');
            });
        });

        // Close mobile menu when clicking outside (legacy support)
        document.addEventListener('click', function(e) {
            if (!navToggle.contains(e.target) && navMenu && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
            }
        });
    }
}

// Search Functionality
function setupSearch() {
    const searchInput = document.querySelector('.searchInput');
    const mobileSearchInput = document.querySelector('.mobileSearchInput');
    const searchBtn = document.querySelector('.searchBtn');
    const mobileSearchOverlay = document.querySelector('.mobileSearchOverlay');
    const mobileSearchClose = document.querySelector('.mobileSearchClose');
    const mobileSearchIcon = document.querySelector('.navIconLink.mobileSearch');

    // Desktop search
    if (searchBtn && searchInput) {
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            performSearch(searchInput.value);
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch(this.value);
            }
        });

        // Search suggestions (placeholder functionality)
        searchInput.addEventListener('input', function() {
            // You can implement search suggestions here
            console.log('Searching for:', this.value);
        });
    }

    // Mobile search overlay
    if (mobileSearchIcon && mobileSearchOverlay) {
        mobileSearchIcon.addEventListener('click', function(e) {
            e.preventDefault();
            openMobileSearch();
        });
    }

    if (mobileSearchClose) {
        mobileSearchClose.addEventListener('click', function() {
            closeMobileSearch();
        });
    }

    if (mobileSearchOverlay) {
        mobileSearchOverlay.addEventListener('click', function(e) {
            if (e.target === mobileSearchOverlay) {
                closeMobileSearch();
            }
        });
    }

    if (mobileSearchInput) {
        mobileSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch(this.value);
                closeMobileSearch();
            }
        });
    }
}

function performSearch(query) {
    if (query.trim()) {
        console.log('Performing search for:', query);
        // Implement your search logic here
        // For example: window.location.href = `/search?q=${encodeURIComponent(query)}`;
        
        // Placeholder alert - replace with actual search functionality
        alert(`Searching for: "${query}"`);
    }
}

function openMobileSearch() {
    const overlay = document.querySelector('.mobileSearchOverlay');
    const input = document.querySelector('.mobileSearchInput');
    
    if (overlay) {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Focus on input after animation
        setTimeout(() => {
            if (input) input.focus();
        }, 300);
    }
}

function closeMobileSearch() {
    const overlay = document.querySelector('.mobileSearchOverlay');
    
    if (overlay) {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// User Dropdown
function setupUserDropdown() {
    const userDropdown = document.querySelector('.userDropdown');
    const dropdownMenu = document.querySelector('.userDropdownMenu');

    if (userDropdown && dropdownMenu) {
        // Handle dropdown for touch devices
        userDropdown.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Toggle dropdown on mobile/touch devices
            if (window.innerWidth <= 768) {
                dropdownMenu.style.opacity = dropdownMenu.style.opacity === '1' ? '0' : '1';
                dropdownMenu.style.visibility = dropdownMenu.style.visibility === 'visible' ? 'hidden' : 'visible';
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target)) {
                dropdownMenu.style.opacity = '0';
                dropdownMenu.style.visibility = 'hidden';
            }
        });
    }
}

// Navbar Scroll Effects


// Cart and Wishlist Counters
function setupCounters() {
    // These functions would typically connect to your cart/wishlist data
    updateCartCount();
    updateWishlistCount();
}

function updateCartCount(count = 3) {
    const cartCount = document.querySelector('.cartCount');
    if (cartCount) {
        cartCount.textContent = count;
        cartCount.style.display = count > 0 ? 'flex' : 'none';
    }
}

function updateWishlistCount(count = 7) {
    const wishlistCount = document.querySelector('.wishlistCount');
    if (wishlistCount) {
        wishlistCount.textContent = count;
        wishlistCount.style.display = count > 0 ? 'flex' : 'none';
    }
}

// Active Page Detection
function setActivePage() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.navLink');
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        
        // Check if the link's href matches the current path
        const linkPath = new URL(link.href).pathname;
        if (linkPath === currentPath || 
            (linkPath !== '/' && currentPath.includes(linkPath))) {
            link.classList.add('active');
        }
    });
    
    // If no link matches, set home as active for root path
    if (currentPath === '/' || currentPath === '/index.html') {
        const homeLink = document.querySelector('.navLink[href*="index"], .navLink[href="/"], .navLink[href="#home"]');
        if (homeLink) {
            homeLink.classList.add('active');
        }
    }
}

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Smooth scrolling for anchor links
function setupSmoothScroll() {
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Initialize smooth scrolling
setupSmoothScroll();

// Export functions for external use
window.YadawityNavbar = {
    updateCartCount,
    updateWishlistCount,
    setActivePage,
    openMobileSearch,
    closeMobileSearch,
    performSearch
};

// Handle resize events
window.addEventListener('resize', debounce(function() {
    // Close mobile menu on resize to larger screen
    if (window.innerWidth > 1500) {
        const navMenu = document.querySelector('.navMenu');
        const navToggle = document.querySelector('.navToggle');
        
        if (navMenu) navMenu.classList.remove('active');
        if (navToggle) navToggle.classList.remove('active');
    }
}, 250));


const artisanFilterButtons = document.querySelectorAll('.artisanCategoryBtn');

artisanFilterButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Remove active class from all buttons
        artisanFilterButtons.forEach(btn => btn.classList.remove('active'));
        // Add active class to clicked button
        button.classList.add('active');

        const filterValue = button.dataset.filter;
        console.log('Filter selected:', filterValue);
    });
});

const slider = document.getElementById('cardSlider');

function slideRight() {
  slider.scrollBy({ left: 320, behavior: 'smooth' });
}

function slideLeft() {
  slider.scrollBy({ left: -320, behavior: 'smooth' });
}

//review card scroll 
   // Review data
   const reviews = [
    {
        name: "Lord Edmund Blackwood",
        title: "Art Collector",
        text: "The therapeutic arts program has provided profound healing through the mastery of classical techniques.",
        image: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face"
    },
    {
        name: "Dr. Sarah Chen",
        title: "Art Therapist",
        text: "This program has revolutionized how I approach healing through creative expression. The results speak for themselves.",
        image: "https://images.unsplash.com/photo-1489424731084-a5d8b219a5bb?w=150&h=150&fit=crop&crop=face"
    },
    {
        name: "Marcus Rodriguez",
        title: "Creative Director",
        text: "The fusion of traditional techniques with modern therapeutic approaches created a transformative experience for our team.",
        image: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face"
    },
    {
        name: "Isabella Morrison",
        title: "Museum Curator",
        text: "An extraordinary journey through the healing power of art. The program's methodology is both innovative and deeply rooted in tradition.",
        image: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face"
    },
    {
        name: "Prof. James Wellington",
        title: "Fine Arts Professor",
        text: "Never before have I witnessed such a profound connection between artistic practice and personal transformation.",
        image: "https://images.unsplash.com/photo-1560250097-0b93528c311a?w=150&h=150&fit=crop&crop=face"
    },
    {
        name: "Maria Santos",
        title: "Wellness Coach",
        text: "The program's holistic approach to creativity and healing has opened new pathways for my clients' recovery and growth.",
        image: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150&h=150&fit=crop&crop=face"
    },
    {
        name: "David Kim",
        title: "Gallery Owner",
        text: "This therapeutic arts initiative has created a ripple effect of positive change throughout our entire artistic community.",
        image: "https://images.unsplash.com/photo-1507591064344-4c6ce005b128?w=150&h=150&fit=crop&crop=face"
    },
    {
        name: "Elena Vasquez",
        title: "Art Student",
        text: "The program helped me discover not just my artistic voice, but also my path to emotional healing and self-discovery.",
        image: "https://images.unsplash.com/photo-1489424731084-a5d8b219a5bb?w=150&h=150&fit=crop&crop=face"
    },
    {
        name: "Robert Thompson",
        title: "Philanthropist",
        text: "Investing in this program was one of the most meaningful decisions I've made. The impact on participants is truly remarkable.",
        image: "https://images.unsplash.com/photo-1463453091185-61582044d556?w=150&h=150&fit=crop&crop=face"
    }
];

class ReviewCarousel {
    constructor() {
        this.init();
    }

    init() {
        this.createCards();
    }

    createCards() {
        const track = document.getElementById('carouselTrack');
        track.innerHTML = '';

        // Create multiple sets for seamless infinite scroll
        const sets = 4; // Create 4 sets of cards for smooth looping
        for (let set = 0; set < sets; set++) {
            reviews.forEach((review, index) => {
                const card = document.createElement('div');
                card.className = 'reviewCardItem';
                card.setAttribute('tabindex', '0');
                card.innerHTML = `
                    <div class="reviewQuoteIcon">"</div>
                    <div class="reviewProfileSection">
                        <img src="${review.image}" alt="${review.name}" class="reviewProfileImage" 
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMzAiIGZpbGw9IiNGOEY2RjMiLz4KPHN2ZyB4PSIxNSIgeT0iMTUiIHdpZHRoPSIzMCIgaGVpZ2h0PSIzMCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSIjOTk5Ij4KPHA+dGggZD0iTTEyIDEyYzIuMjEgMCA0LTEuNzkgNC00cy0xLjc5LTQtNC00LTQgMS43OS00IDQgMS43OSA0IDQgNHptMCAyYy0yLjY3IDAtOCAxLjM0LTggNHYyaDE2di0yYzAtMi42Ni01LjMzLTQtOC00eiIvPgo8L3N2Zz4KPC9zdmc+'">
                        <div class="reviewProfileInfo">
                            <div class="reviewProfileName">${review.name}</div>
                            <div class="reviewProfileTitle">${review.title}</div>
                        </div>
                    </div>
                    <div class="reviewTextContent">${review.text}</div>
                    <div class="reviewStarRating">
                        <span class="reviewStar">★</span>
                        <span class="reviewStar">★</span>
                        <span class="reviewStar">★</span>
                        <span class="reviewStar">★</span>
                        <span class="reviewStar">★</span>
                    </div>
                `;
                track.appendChild(card);
            });
        }
    }
}

// Initialize carousel when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ReviewCarousel();
});

//login page
 
const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

if (registerBtn) {
    registerBtn.addEventListener('click', () => {
        container.classList.add("active");
    });
}

if (loginBtn) {
    loginBtn.addEventListener('click', () => {
        container.classList.remove("active");
    });
}