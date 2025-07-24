 // Mobile Menu Toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('active');
        }

        // Search functionality
        document.getElementById('mainSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            console.log('Searching for:', searchTerm);
            // Add search logic here
        });

        // Filter functions
        function setQuickFilter(filterType) {
            const priceMin = document.getElementById('priceMin');
            const priceMax = document.getElementById('priceMax');
            
            switch(filterType) {
                case 'under-50':
                    priceMin.value = '';
                    priceMax.value = '50';
                    break;
                case '50-100':
                    priceMin.value = '50';
                    priceMax.value = '100';
                    break;
                case '100-150':
                    priceMin.value = '100';
                    priceMax.value = '150';
                    break;
                case 'premium':
                    priceMin.value = '150';
                    priceMax.value = '';
                    break;
                case 'available':
                    // Add logic for available today filter
                    console.log('Filtering for available today');
                    break;
            }
            
            // Highlight active quick filter
            document.querySelectorAll('.quick-filter-btn').forEach(btn => {
                btn.style.background = btn.classList.contains('available') ? 'linear-gradient(45deg, var(--gold), var(--gold-light))' : 'rgba(255, 255, 255, 0.2)';
                btn.style.color = btn.classList.contains('available') ? 'var(--dark-brown)' : 'white';
            });
            event.target.style.background = 'linear-gradient(45deg, var(--gold), var(--gold-light))';
            event.target.style.color = 'var(--dark-brown)';
        }

        function clearAllFilters() {
            document.getElementById('therapistFilter').value = '';
            document.getElementById('sessionTypeFilter').value = '';
            document.getElementById('priceMin').value = '';
            document.getElementById('priceMax').value = '';
            document.getElementById('durationFilter').value = '';
            document.getElementById('mainSearch').value = '';
            
            // Reset quick filter buttons
            document.querySelectorAll('.quick-filter-btn').forEach(btn => {
                btn.style.background = btn.classList.contains('available') ? 'linear-gradient(45deg, var(--gold), var(--gold-light))' : 'rgba(255, 255, 255, 0.2)';
                btn.style.color = btn.classList.contains('available') ? 'var(--dark-brown)' : 'white';
            });
        }

        // Filter change handlers
        document.getElementById('therapistFilter').addEventListener('change', function(e) {
            console.log('Therapist filter changed:', e.target.value);
            // Add filter logic here
        });

        document.getElementById('sessionTypeFilter').addEventListener('change', function(e) {
            console.log('Session type filter changed:', e.target.value);
            // Add filter logic here
        });

        document.getElementById('durationFilter').addEventListener('change', function(e) {
            console.log('Duration filter changed:', e.target.value);
            // Add filter logic here
        });

        document.getElementById('priceMin').addEventListener('input', function(e) {
            console.log('Price min changed:', e.target.value);
            // Add filter logic here
        });

        document.getElementById('priceMax').addEventListener('input', function(e) {
            console.log('Price max changed:', e.target.value);
            // Add filter logic here
        });

        // Service booking handlers
        document.querySelectorAll('.service-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const serviceTitle = this.closest('.service-card').querySelector('.service-title').textContent;
                alert(`Booking ${serviceTitle}. This would redirect to booking system.`);
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            
            if (!mobileMenuBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
                mobileMenu.classList.remove('active');
            }
        });

        // Add loading states for buttons
        document.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', function() {
                if (this.classList.contains('service-btn') || this.classList.contains('hero-search-btn')) {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    this.disabled = true;
                    
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }, 1500);
                }
            });
        });

        // Pre-screening functionality
        let uploadedFile = null;

        // File upload handling
        document.getElementById('artworkUpload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                handleFileUpload(file);
            }
        });

        // Drag and drop functionality
        const uploadArea = document.getElementById('uploadArea');

        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                handleFileUpload(file);
            }
        });

        function handleFileUpload(file) {
            uploadedFile = file;
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('previewImage').src = e.target.result;
                document.getElementById('uploadArea').style.display = 'none';
                document.getElementById('uploadedImage').style.display = 'block';
            };
            
            reader.readAsDataURL(file);
        }

        function removeImage() {
            uploadedFile = null;
            document.getElementById('uploadArea').style.display = 'block';
            document.getElementById('uploadedImage').style.display = 'none';
            document.getElementById('artworkUpload').value = '';
        }

        function analyzeArtwork() {
            if (!uploadedFile) {
                alert('Please upload an image first.');
                return;
            }
            
            // Simulate AI analysis
            const analysisBtn = document.querySelector('.analyze-btn');
            const originalText = analysisBtn.innerHTML;
            analysisBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';
            analysisBtn.disabled = true;
            
            setTimeout(() => {
                analysisBtn.innerHTML = originalText;
                analysisBtn.disabled = false;
                
                // Show analysis results
                alert('Analysis complete! Your artwork suggests you might benefit from expressive therapy techniques. This information will help match you with the right therapist.');
            }, 3000);
        }

        // Form submission
        document.getElementById('preScreeningForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const mood = formData.get('mood');
            const supportType = formData.get('support-type');
            const experience = formData.get('experience');
            const formats = formData.getAll('format');
            
            // Show loading state
            const submitBtn = document.querySelector('.submit-assessment-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Finding Your Perfect Match...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                // Generate recommendations based on responses
                generateRecommendations(mood, supportType, experience, formats);
                
                // Show results
                document.getElementById('assessmentResults').style.display = 'block';
                document.getElementById('assessmentResults').scrollIntoView({ behavior: 'smooth' });
            }, 2500);
        });

        function generateRecommendations(mood, supportType, experience, formats) {
            // Determine if user needs art therapy or professional therapy
            const needsProfessionalTherapy = assessNeedForProfessionalTherapy(mood, supportType);
            
            if (needsProfessionalTherapy) {
                generateProfessionalTherapistRecommendations(mood, supportType);
            } else {
                generateArtTherapyRecommendations(mood, supportType, experience, formats);
            }
        }

        function assessNeedForProfessionalTherapy(mood, supportType) {
            // Conditions that require professional therapy
            const criticalMoods = ['very-sad', 'anxious', 'angry'];
            const criticalSupport = ['trauma-recovery', 'anxiety-depression', 'grief-loss'];
            
            return criticalMoods.includes(mood) || criticalSupport.includes(supportType);
        }

        function generateProfessionalTherapistRecommendations(mood, supportType) {
            const professionalTherapists = [
                {
                    name: 'Dr. Sarah Mitchell, PhD',
                    specialty: 'Clinical Psychologist - Trauma Specialist',
                    location: 'Downtown Medical Center, 123 Health St, Cairo',
                    phone: '+20 1234567890',
                    email: 'dr.mitchell@healthcenter.com',
                    avatar: '👩‍⚕️',
                    urgency: 'high',
                    description: 'Specialized in PTSD, trauma recovery, and crisis intervention with 15+ years experience.',
                    availability: 'Emergency appointments available'
                },
                {
                    name: 'Dr. Ahmed Hassan, MD',
                    specialty: 'Psychiatrist - Anxiety & Depression',
                    location: 'Nile Medical Complex, 456 River Rd, Giza',
                    phone: '+20 1987654321',
                    email: 'dr.hassan@nilemedical.com',
                    avatar: '👨‍⚕️',
                    urgency: 'high',
                    description: 'Expert in anxiety disorders, depression, and medication management.',
                    availability: 'Same-day consultations available'
                },
                {
                    name: 'Dr. Fatima Al-Rashid, PhD',
                    specialty: 'Licensed Clinical Social Worker',
                    location: 'Community Health Center, 789 Care Ave, Alexandria',
                    phone: '+20 1122334455',
                    email: 'dr.alrashid@communityhc.org',
                    avatar: '👩‍⚕️',
                    urgency: 'medium',
                    description: 'Specializes in grief counseling, family therapy, and crisis support.',
                    availability: 'Walk-ins welcome Mon-Fri'
                }
            ];
            
            const recommendationsGrid = document.getElementById('recommendationsGrid');
            recommendationsGrid.innerHTML = '';
            
            // Add urgent care notice
            const urgentNotice = document.createElement('div');
            urgentNotice.style.cssText = `
                background: linear-gradient(135deg, #FEF3C7, #FDE68A);
                border: 2px solid #F59E0B;
                border-radius: 12px;
                padding: 1.5rem;
                margin-bottom: 2rem;
                text-align: center;
            `;
            urgentNotice.innerHTML = `
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">⚠️</div>
                <h4 style="color: #92400E; margin-bottom: 0.5rem; font-size: 1.25rem;">Professional Support Recommended</h4>
                <p style="color: #78350F; margin-bottom: 1rem;">
                    Based on your responses, we recommend connecting with a licensed mental health professional. 
                    They can provide the specialized care you need.
                </p>
                <div style="background: #FEE2E2; border: 1px solid #F87171; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                    <p style="color: #991B1B; font-weight: 600; margin: 0;">
                        🚨 If you're having thoughts of self-harm, please call 988 (Crisis Hotline) immediately or go to your nearest emergency room.
                    </p>
                </div>
            `;
            recommendationsGrid.appendChild(urgentNotice);
            
            professionalTherapists.forEach(therapist => {
                const card = document.createElement('div');
                card.className = 'recommendation-card professional-therapist';
                card.style.cssText = `
                    background: rgba(255, 255, 255, 0.95);
                    border: 2px solid #F87171;
                `;
                card.innerHTML = `
                    <div class="therapist-avatar" style="background: #DC2626;">${therapist.avatar}</div>
                    <div class="therapist-name" style="color: var(--text-primary);">${therapist.name}</div>
                    <div class="therapist-specialty" style="color: #F87171;">${therapist.specialty}</div>
                    <div class="contact-info" style="margin: 1rem 0; text-align: left;">
                        <div style="margin-bottom: 0.5rem; color: var(--text-primary);">
                            <i class="fas fa-map-marker-alt" style="color: #DC2626; margin-right: 0.5rem;"></i>
                            <strong>Location:</strong><br>
                            <span style="font-size: 0.875rem; margin-left: 1.25rem;">${therapist.location}</span>
                        </div>
                        <div style="margin-bottom: 0.5rem; color: var(--text-primary);">
                            <i class="fas fa-phone" style="color: #DC2626; margin-right: 0.5rem;"></i>
                            <strong>Phone:</strong> 
                            <a href="tel:${therapist.phone}" style="color: #DC2626; text-decoration: none; font-weight: 600;">${therapist.phone}</a>
                        </div>
                        <div style="margin-bottom: 0.5rem; color: var(--text-primary);">
                            <i class="fas fa-envelope" style="color: #DC2626; margin-right: 0.5rem;"></i>
                            <strong>Email:</strong> 
                            <a href="mailto:${therapist.email}" style="color: #DC2626; text-decoration: none;">${therapist.email}</a>
                        </div>
                        <div style="margin-bottom: 1rem; color: var(--text-primary);">
                            <i class="fas fa-clock" style="color: #DC2626; margin-right: 0.5rem;"></i>
                            <strong>Availability:</strong> ${therapist.availability}
                        </div>
                    </div>
                    <p style="color: var(--text-muted); margin-bottom: 1rem; font-size: 0.875rem; text-align: left;">
                        ${therapist.description}
                    </p>
                    <div style="display: flex; gap: 0.5rem;">
                        <button class="book-therapist-btn" onclick="callTherapist('${therapist.phone}')"
                                 style="background: #DC2626; flex: 1;">
                            <i class="fas fa-phone"></i> Call Now
                        </button>
                        <button class="book-therapist-btn" onclick="emailTherapist('${therapist.email}')"
                                 style="background: linear-gradient(45deg, var(--gold), var(--gold-light)); color: var(--dark-brown); flex: 1;">
                            <i class="fas fa-envelope"></i> Email
                        </button>
                    </div>
                `;
                recommendationsGrid.appendChild(card);
            });
        }

        function generateArtTherapyRecommendations(mood, supportType, experience, formats) {
            const artTherapyServices = [
                {
                    name: 'Creative Expression Sessions',
                    type: 'Art Therapy Service',
                    icon: '🎨',
                    match: 95,
                    description: 'Perfect for self-expression and emotional exploration through various art mediums.',
                    price: 'From $60/session',
                    duration: '60-90 minutes',
                    format: 'Individual or Group',
                    benefits: ['Stress relief', 'Emotional processing', 'Self-discovery']
                },
                {
                    name: 'Mindful Art Workshops',
                    type: 'Art Therapy Service',
                    icon: '🧘‍♀️',
                    match: 88,
                    description: 'Combine mindfulness techniques with artistic creation for inner peace.',
                    price: 'From $45/session',
                    duration: '75 minutes',
                    format: 'Group sessions',
                    benefits: ['Anxiety reduction', 'Mindfulness', 'Creative flow']
                },
                {
                    name: 'Therapeutic Drawing Classes',
                    type: 'Art Therapy Service',
                    icon: '✏️',
                    match: 82,
                    description: 'Use drawing as a tool for emotional healing and personal growth.',
                    price: 'From $50/session',
                    duration: '60 minutes',
                    format: 'Individual',
                    benefits: ['Emotional release', 'Self-reflection', 'Skill building']
                }
            ];
            
            const recommendationsGrid = document.getElementById('recommendationsGrid');
            recommendationsGrid.innerHTML = '';
            
            // Add art therapy notice
            const artTherapyNotice = document.createElement('div');
            artTherapyNotice.style.cssText = `
                background: rgba(255, 255, 255, 0.95);
                border: 2px solid var(--gold);
                border-radius: 12px;
                padding: 1.5rem;
                margin-bottom: 2rem;
                text-align: center;
                backdrop-filter: blur(10px);
            `;
            artTherapyNotice.innerHTML = `
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🎨</div>
                <h4 style="color: var(--text-primary); margin-bottom: 0.5rem; font-size: 1.25rem;">Art Therapy Recommended</h4>
                <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                    Great news! Based on your responses, art therapy sessions would be perfect for your current needs. 
                    These creative approaches can help you express yourself and find healing through art.
                </p>
                <div style="background: rgba(212, 175, 55, 0.1); border: 1px solid var(--gold); border-radius: 8px; padding: 1rem;">
                    <p style="color: var(--medium-brown); font-weight: 600; margin: 0;">
                        💡 Art therapy is a gentle, non-invasive approach that can complement other forms of self-care and wellness.
                    </p>
                </div>
            `;
            recommendationsGrid.appendChild(artTherapyNotice);
            
            artTherapyServices.forEach(service => {
                const card = document.createElement('div');
                card.className = 'recommendation-card art-therapy-service';
                card.innerHTML = `
                    <div class="service-icon-large" style="font-size: 3rem; margin-bottom: 1rem;">${service.icon}</div>
                    <div class="service-name" style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                        ${service.name}
                    </div>
                    <div class="service-type" style="color: var(--medium-brown); font-weight: 500; margin-bottom: 1rem;">
                        ${service.type}
                    </div>
                    <div class="match-score">${service.match}% Match</div>
                    <p style="color: var(--text-muted); margin-bottom: 1rem; font-size: 0.875rem; text-align: left;">
                        ${service.description}
                    </p>
                    <div class="service-details" style="text-align: left; margin-bottom: 1rem; font-size: 0.875rem;">
                        <div style="margin-bottom: 0.5rem; color: var(--text-primary);">
                            <i class="fas fa-dollar-sign" style="color: var(--gold); margin-right: 0.5rem;"></i>
                            <strong>Price:</strong> ${service.price}
                        </div>
                        <div style="margin-bottom: 0.5rem; color: var(--text-primary);">
                            <i class="fas fa-clock" style="color: var(--gold); margin-right: 0.5rem;"></i>
                            <strong>Duration:</strong> ${service.duration}
                        </div>
                        <div style="margin-bottom: 0.5rem; color: var(--text-primary);">
                            <i class="fas fa-users" style="color: var(--gold); margin-right: 0.5rem;"></i>
                            <strong>Format:</strong> ${service.format}
                        </div>
                    </div>
                    <div class="benefits" style="margin-bottom: 1rem;">
                        <strong style="color: var(--medium-brown); font-size: 0.875rem;">Benefits:</strong>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
                            ${service.benefits.map(benefit => 
                                `<span style="background: rgba(255, 255, 255, 0.2); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.3);">${benefit}</span>`
                            ).join('')}
                        </div>
                    </div>
                    <button class="book-therapist-btn" onclick="bookArtTherapy('${service.name}')">
                        <i class="fas fa-calendar-plus"></i> Book Session
                    </button>
                `;
                recommendationsGrid.appendChild(card);
            });
        }

        function callTherapist(phoneNumber) {
            // Remove any formatting and create tel link
            const cleanPhone = phoneNumber.replace(/\s+/g, '');
            window.location.href = `tel:${cleanPhone}`;
        }

        function emailTherapist(email) {
            const subject = encodeURIComponent('Art Therapy Assessment - Consultation Request');
            const body = encodeURIComponent(`Hello,

I recently completed an art therapy assessment on the Yadawity platform and received a recommendation to connect with a professional therapist.

I would like to schedule a consultation to discuss my needs and explore treatment options.

Please let me know your availability.

Thank you,
[Your Name]`);
            
            window.location.href = `mailto:${email}?subject=${subject}&body=${body}`;
        }

        function bookArtTherapy(serviceName) {
            alert(`Booking ${serviceName}. You'll be redirected to our art therapy booking system where you can choose your preferred time and instructor.`);
            // In a real application, this would redirect to the booking system
            // window.location.href = '/book-art-therapy?service=' + encodeURIComponent(serviceName);
        }