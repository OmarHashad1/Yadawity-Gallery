 // Course data
        const courses = [
            {
                id: 1,
                title: "Digital Painting Masterclass",
                instructor: "Sarah Martinez",
                description: "Master advanced digital painting techniques with industry-standard tools.",
                price: 89,
                originalPrice: 129,
                duration: "12 weeks",
                students: 2847,
                rating: 4.9,
                category: "Digital Art",
                difficulty: "intermediate",
                tags: ["Photoshop", "Procreate", "Color Theory"],
                image: "https://images.pexels.com/photos/1053687/pexels-photo-1053687.jpeg?auto=compress&cs=tinysrgb&w=600"
            },
            {
                id: 2,
                title: "Watercolor Fundamentals",
                instructor: "Emma Thompson",
                description: "Explore traditional watercolor techniques with modern applications.",
                price: 65,
                originalPrice: 95,
                duration: "8 weeks",
                students: 1923,
                rating: 4.8,
                category: "Traditional Art",
                difficulty: "beginner",
                tags: ["Watercolor", "Landscapes", "Color Mixing"],
                image: "https://images.pexels.com/photos/1047540/pexels-photo-1047540.jpeg?auto=compress&cs=tinysrgb&w=600"
            },
            {
                id: 3,
                title: "Character Design Workshop",
                instructor: "Alex Rivera",
                description: "Design compelling characters for games and animation.",
                price: 120,
                originalPrice: 180,
                duration: "16 weeks",
                students: 3156,
                rating: 4.9,
                category: "Concept Art",
                difficulty: "advanced",
                tags: ["Character Design", "Anatomy", "Storytelling"],
                image: "https://images.pexels.com/photos/1646953/pexels-photo-1646953.jpeg?auto=compress&cs=tinysrgb&w=600"
            },
            {
                id: 4,
                title: "Abstract Art Exploration",
                instructor: "Marina Kowalski",
                description: "Discover your unique artistic voice through abstract expression.",
                price: 75,
                originalPrice: 110,
                duration: "10 weeks",
                students: 1567,
                rating: 4.7,
                category: "Fine Art",
                difficulty: "beginner",
                tags: ["Abstract", "Mixed Media", "Expression"],
                image: "https://images.pexels.com/photos/1183992/pexels-photo-1183992.jpeg?auto=compress&cs=tinysrgb&w=600"
            },
            {
                id: 5,
                title: "3D Sculpture Digital Art",
                instructor: "David Chen",
                description: "Create stunning 3D sculptures using industry-leading software.",
                price: 150,
                originalPrice: 220,
                duration: "20 weeks",  
                students: 2234,
                rating: 4.8,
                category: "3D Art",
                difficulty: "advanced",
                tags: ["ZBrush", "Blender", "3D Modeling"],
                image: "https://images.pexels.com/photos/1194420/pexels-photo-1194420.jpeg?auto=compress&cs=tinysrgb&w=600"
            },
            {
                id: 6,
                title: "Oil Painting Techniques",
                instructor: "Leonardo Rossi",
                description: "Master traditional oil painting with classical techniques.",
                price: 95,
                originalPrice: 140,
                duration: "14 weeks",
                students: 1789,
                rating: 4.9,
                category: "Traditional Art",
                difficulty: "intermediate",
                tags: ["Oil Painting", "Classical", "Portraits"],
                image: "https://images.pexels.com/photos/1266808/pexels-photo-1266808.jpeg?auto=compress&cs=tinysrgb&w=600"
            },
            {
                id: 7,
                title: "Portrait Drawing Mastery",
                instructor: "Isabella Rodriguez",
                description: "Create lifelike portraits with advanced drawing techniques.",
                price: 78,
                originalPrice: 115,
                duration: "11 weeks",
                students: 2156,
                rating: 4.8,
                category: "Drawing",
                difficulty: "intermediate",
                tags: ["Portraits", "Graphite", "Anatomy"],
                image: "https://images.pexels.com/photos/1646953/pexels-photo-1646953.jpeg?auto=compress&cs=tinysrgb&w=600"
            },
            {
                id: 8,
                title: "Ceramic Pottery Workshop",
                instructor: "James Wilson",
                description: "Learn the timeless craft of pottery and ceramics.",
                price: 110,
                originalPrice: 160,
                duration: "15 weeks",
                students: 1432,
                rating: 4.7,
                category: "Ceramics",
                difficulty: "beginner",
                tags: ["Pottery", "Ceramics", "Wheel Throwing"],
                image: "https://images.pexels.com/photos/1094767/pexels-photo-1094767.jpeg?auto=compress&cs=tinysrgb&w=600"
            },
            {
                id: 9,
                title: "Street Art & Murals",
                instructor: "Carlos Mendez",
                description: "Master urban art techniques and large-scale mural creation.",
                price: 85,
                originalPrice: 125,
                duration: "9 weeks",
                students: 1876,
                rating: 4.6,
                category: "Street Art",
                difficulty: "intermediate",
                tags: ["Murals", "Spray Paint", "Urban Art"],
                image: "https://images.pexels.com/photos/1646953/pexels-photo-1646953.jpeg?auto=compress&cs=tinysrgb&w=600"
            },
            {
                id: 10,
                title: "Fashion Illustration",
                instructor: "Sophie Laurent",
                description: "Develop your fashion illustration style with professional techniques.",
                price: 92,
                originalPrice: 135,
                duration: "13 weeks",
                students: 2089,
                rating: 4.8,
                category: "Fashion Art",
                difficulty: "intermediate",
                tags: ["Fashion", "Illustration", "Design"],
                image: "https://images.pexels.com/photos/1183992/pexels-photo-1183992.jpeg?auto=compress&cs=tinysrgb&w=600"
            },
            {
                id: 11,
                title: "Calligraphy & Hand Lettering",
                instructor: "Yuki Tanaka",
                description: "Perfect the art of beautiful lettering and calligraphy.",
                price: 68,
                originalPrice: 98,
                duration: "7 weeks",
                students: 1654,
                rating: 4.9,
                category: "Typography",
                difficulty: "beginner",
                tags: ["Calligraphy", "Lettering", "Typography"],
                image: "https://images.pexels.com/photos/1053687/pexels-photo-1053687.jpeg?auto=compress&cs=tinysrgb&w=600"
            },
            {
                id: 12,
                title: "Landscape Photography Art",
                instructor: "Michael Anderson",
                description: "Transform landscape photography into fine art.",
                price: 105,
                originalPrice: 155,
                duration: "12 weeks",
                students: 2345,
                rating: 4.7,
                category: "Photography",
                difficulty: "intermediate",
                tags: ["Photography", "Landscapes", "Composition"],
                image: "https://images.pexels.com/photos/1047540/pexels-photo-1047540.jpeg?auto=compress&cs=tinysrgb&w=600"
            }
        ];

        let filteredCourses = [...courses];
        let activeFilters = {};

        // Create course card HTML - Matching Gallery Style
        function createCourseCard(course) {
            return `
                <div class="course-card" onclick="enrollCourse(${course.id})">
                    <div class="course-image">
                        <img src="${course.image}" alt="${course.title}" loading="lazy">
                        
                        <div class="course-rating">
                            <span class="star">★</span>
                            ${course.rating}
                        </div>
                        
                        <div class="difficulty-badge difficulty-${course.difficulty}">
                            ${course.difficulty}
                        </div>
                        
                        <div class="course-partner">
                            YADAWITY PARTNER
                        </div>
                    </div>
                    
                    <div class="course-content">
                        <h3 class="course-title">${course.title}</h3>
                        <p class="course-instructor">${course.instructor}</p>
                        <p class="course-category">${course.category}</p>
                        
                        <div class="course-meta">
                            <div class="course-duration">
                                <i class="fas fa-calendar-alt"></i>
                                ${course.duration}
                            </div>
                            <div class="course-students">
                                <i class="fas fa-users"></i>
                                ${course.students.toLocaleString()} students
                            </div>
                        </div>
                        
                        <div class="course-price-info">
                            <div class="course-price">
                                <span class="price">$${course.price}</span>
                                ${course.originalPrice ? `<span class="original-price">$${course.originalPrice}</span>` : ''}
                            </div>
                        </div>
                        
                        <button class="enroll-btn" onclick="event.stopPropagation(); enrollCourse(${course.id})">
                            Enroll Now
                        </button>
                    </div>
                </div>
            `;
        }

        // Enhanced filtering function
        function applyFilters() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const category = document.getElementById('categoryFilter').value;
            const difficulty = document.getElementById('difficultyFilter').value;
            const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
            const maxPrice = parseFloat(document.getElementById('maxPrice').value) || Infinity;
            const duration = document.getElementById('durationFilter').value;

            // Update active filters
            activeFilters = {};
            if (searchTerm) activeFilters.search = searchTerm;
            if (category) activeFilters.category = category;
            if (difficulty) activeFilters.difficulty = difficulty;
            if (minPrice > 0) activeFilters.minPrice = minPrice;
            if (maxPrice < Infinity) activeFilters.maxPrice = maxPrice;
            if (duration) activeFilters.duration = duration;

            filteredCourses = courses.filter(course => {
                // Name/Search filter
                const matchesSearch = !searchTerm || 
                    course.title.toLowerCase().includes(searchTerm) ||
                    course.instructor.toLowerCase().includes(searchTerm) ||
                    course.description.toLowerCase().includes(searchTerm) ||
                    course.tags.some(tag => tag.toLowerCase().includes(searchTerm));

                // Category filter
                const matchesCategory = !category || course.category === category;

                // Difficulty filter
                const matchesDifficulty = !difficulty || course.difficulty === difficulty;

                // Price filter
                const matchesPrice = course.price >= minPrice && course.price <= maxPrice;

                // Duration filter
                const matchesDuration = !duration || 
                    (duration === 'short' && parseInt(course.duration) <= 8) ||
                    (duration === 'medium' && parseInt(course.duration) >= 9 && parseInt(course.duration) <= 15) ||
                    (duration === 'long' && parseInt(course.duration) >= 16);

                return matchesSearch && matchesCategory && matchesDifficulty && matchesPrice && matchesDuration;
            });

            updateActiveFiltersDisplay();
            updateSearchResults();
            renderCourses();
        }

        // Update active filters display
        function updateActiveFiltersDisplay() {
            const activeFiltersContainer = document.getElementById('activeFilters');
            activeFiltersContainer.innerHTML = '';

            Object.entries(activeFilters).forEach(([key, value]) => {
                const filterTag = document.createElement('div');
                filterTag.className = 'filter-tag';
                
                let displayText = '';
                switch(key) {
                    case 'search':
                        displayText = `Search: "${value}"`;
                        break;
                    case 'category':
                        displayText = `Category: ${value}`;
                        break;
                    case 'difficulty':
                        displayText = `Level: ${value}`;
                        break;
                    case 'minPrice':
                        displayText = `Min: $${value}`;
                        break;
                    case 'maxPrice':
                        displayText = `Max: $${value}`;
                        break;
                    case 'duration':
                        displayText = `Duration: ${value}`;
                        break;
                }

                filterTag.innerHTML = `
                    ${displayText}
                    <span class="remove-filter" onclick="removeFilter('${key}')">×</span>
                `;
                
                activeFiltersContainer.appendChild(filterTag);
            });
        }

        // Remove individual filter
        function removeFilter(filterKey) {
            switch(filterKey) {
                case 'search':
                    document.getElementById('searchInput').value = '';
                    break;
                case 'category':
                    document.getElementById('categoryFilter').value = '';
                    break;
                case 'difficulty':
                    document.getElementById('difficultyFilter').value = '';
                    break;
                case 'minPrice':
                    document.getElementById('minPrice').value = '';
                    break;
                case 'maxPrice':
                    document.getElementById('maxPrice').value = '';
                    break;
                case 'duration':
                    document.getElementById('durationFilter').value = '';
                    break;
            }
            applyFilters();
        }

        // Clear all filters
        function clearAllFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('categoryFilter').value = '';
            document.getElementById('difficultyFilter').value = '';
            document.getElementById('minPrice').value = '';
            document.getElementById('maxPrice').value = '';
            document.getElementById('durationFilter').value = '';
            applyFilters();
        }

        // Update search results display
        function updateSearchResults() {
            const searchResults = document.getElementById('searchResults');
            const count = filteredCourses.length;
            const total = courses.length;
            
            if (Object.keys(activeFilters).length > 0) {
                const plural = count !== 1 ? 's' : '';
                searchResults.textContent = `Showing ${count} of ${total} course${plural}`;
            } else {
                searchResults.textContent = `Showing all ${total} courses`;
            }
        }

        // Render courses
        function renderCourses(coursesToRender = filteredCourses) {
            const coursesGrid = document.getElementById('coursesGrid');
            const noResults = document.getElementById('noResults');
            
            if (coursesToRender.length === 0) {
                coursesGrid.innerHTML = '';
                noResults.classList.add('show');
            } else {
                coursesGrid.innerHTML = coursesToRender.map(course => createCourseCard(course)).join('');
                noResults.classList.remove('show');
            }
        }

        // Enroll in course
        function enrollCourse(courseId) {
            const course = courses.find(c => c.id === courseId);
            if (course) {
                alert(`🎨 Enrolling in "${course.title}" by ${course.instructor}\n\n💰 Price: $${course.price}\n⏰ Duration: ${course.duration}\n👥 ${course.students.toLocaleString()} students already enrolled\n\n✨ Redirecting to secure payment...`);
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Initial render
            renderCourses();
            updateSearchResults();
            
            // Search input event listener
            document.getElementById('searchInput').addEventListener('input', applyFilters);
            
            // Filter event listeners
            document.getElementById('categoryFilter').addEventListener('change', applyFilters);
            document.getElementById('difficultyFilter').addEventListener('change', applyFilters);
            document.getElementById('minPrice').addEventListener('input', applyFilters);
            document.getElementById('maxPrice').addEventListener('input', applyFilters);
            document.getElementById('durationFilter').addEventListener('change', applyFilters);
            
            // Clear search on escape key
            document.getElementById('searchInput').addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    clearAllFilters();
                }
            });
        });