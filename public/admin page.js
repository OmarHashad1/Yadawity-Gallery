
        // Mock data
        const recentActivities = [
            {
                id: 1,
                user: "Sarah Chen",
                action: "purchased",
                item: "Digital Landscape #42",
                amount: "$299",
                time: "2 minutes ago",
                initials: "SC"
            },
            {
                id: 2,
                user: "Mike Johnson",
                action: "listed",
                item: "Abstract Portrait Series",
                amount: "$1,200",
                time: "15 minutes ago",
                initials: "MJ"
            },
            {
                id: 3,
                user: "Emma Davis",
                action: "bid on",
                item: "Sunset Memories",
                amount: "$450",
                time: "1 hour ago",
                initials: "ED"
            },
            {
                id: 4,
                user: "Alex Rivera",
                action: "withdrew",
                item: "earnings",
                amount: "$2,340",
                time: "3 hours ago",
                initials: "AR"
            }
        ];

        const popularArtworks = [
            { name: "Digital Dreams", sales: 45, revenue: "$13,500", trend: "+12%" },
            { name: "Ocean Waves", sales: 38, revenue: "$11,400", trend: "+8%" },
            { name: "City Lights", sales: 32, revenue: "$9,600", trend: "+15%" },
            { name: "Forest Path", sales: 28, revenue: "$8,400", trend: "+5%" },
            { name: "Mountain View", sales: 24, revenue: "$7,200", trend: "-2%" }
        ];

        const users = [
            {
                name: "Sarah Chen",
                email: "sarah@example.com",
                type: "Artist",
                status: "Active",
                joined: "2023-12-15",
                initials: "SC"
            },
            {
                name: "Mike Johnson",
                email: "mike@example.com",
                type: "Buyer",
                status: "Active",
                joined: "2023-11-20",
                initials: "MJ"
            },
            {
                name: "Emma Davis",
                email: "emma@example.com",
                type: "Collector",
                status: "Inactive",
                joined: "2023-10-05",
                initials: "ED"
            },
            {
                name: "Alex Rivera",
                email: "alex@example.com",
                type: "Artist",
                status: "Pending",
                joined: "2024-01-10",
                initials: "AR"
            }
        ];

        // DOM Elements
        const sidebar = document.getElementById('sidebar');
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const pageTitle = document.getElementById('pageTitle');
        const breadcrumbCurrent = document.getElementById('breadcrumbCurrent');
        const navLinks = document.querySelectorAll('.nav-link');

        // Initialize the dashboard
        document.addEventListener('DOMContentLoaded', function() {
            populateActivityFeed();
            populatePopularArtworks();
            populateUsers();
            setupEventListeners();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Navigation
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const section = this.getAttribute('data-section');
                    switchSection(section);
                });
            });

            // Mobile menu toggle
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('open');
                });
            }

            // Tab functionality
            setupTabs();

            // Close sidebar on mobile when clicking outside
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
                        sidebar.classList.remove('open');
                    }
                }
            });
        }

        // Setup tabs functionality
        function setupTabs() {
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    const parentSection = this.closest('.dashboard-section');
                    
                    // Remove active class from all tabs in this section
                    parentSection.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                    parentSection.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    this.classList.add('active');
                    const tabContent = parentSection.querySelector(`#${tabId}`);
                    if (tabContent) {
                        tabContent.classList.add('active');
                    }
                });
            });
        }

        // Switch between dashboard sections
        function switchSection(sectionName) {
            // Update active nav link
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('data-section') === sectionName) {
                    link.classList.add('active');
                }
            });

            // Update page title and breadcrumb
            const titles = {
                'overview': { title: 'Dashboard Overview', breadcrumb: 'Dashboard' },
                'analytics': { title: 'Analytics Dashboard', breadcrumb: 'Analytics' },
                'financial': { title: 'Financial Reports', breadcrumb: 'Financial' },
                'users': { title: 'User Management', breadcrumb: 'Users' },
                'content': { title: 'Content Management', breadcrumb: 'Content' },
                'auctions': { title: 'Auction Management', breadcrumb: 'Auctions' },
                'courses': { title: 'Course Management', breadcrumb: 'Courses' },
                'orders': { title: 'Orders & Transactions', breadcrumb: 'Orders' },
                'inventory': { title: 'Inventory & Catalog', breadcrumb: 'Inventory' },
                'support': { title: 'Support & Communication', breadcrumb: 'Support' },
                'messages': { title: 'Messages Management', breadcrumb: 'Messages' },
                'announcements': { title: 'Announcements', breadcrumb: 'Announcements' },
                'galleries': { title: 'Gallery & Exhibition Management', breadcrumb: 'Galleries' },
                'exhibitions': { title: 'Exhibition Management', breadcrumb: 'Exhibitions' },
                'campaigns': { title: 'Marketing & Promotions', breadcrumb: 'Campaigns' },
                'promotions': { title: 'Promotions Management', breadcrumb: 'Promotions' },
                'newsletter': { title: 'Newsletter Management', breadcrumb: 'Newsletter' },
                'administration': { title: 'System Administration', breadcrumb: 'Administration' },
                'security': { title: 'Security Management', breadcrumb: 'Security' },
                'legal': { title: 'Legal & Compliance', breadcrumb: 'Legal' },
                'settings': { title: 'Settings', breadcrumb: 'Settings' }
            };
            
            const sectionInfo = titles[sectionName] || { title: 'Dashboard', breadcrumb: 'Dashboard' };
            pageTitle.textContent = sectionInfo.title;
            breadcrumbCurrent.textContent = sectionInfo.breadcrumb;

            // Show/hide sections
            const sections = document.querySelectorAll('.dashboard-section');
            sections.forEach(section => {
                section.classList.remove('active');
                if (section.id === sectionName) {
                    section.classList.add('active');
                }
            });

            // Close mobile sidebar
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('open');
            }
        }

        // Populate activity feed
        function populateActivityFeed() {
            const activityFeed = document.getElementById('activityFeed');
            if (!activityFeed) return;

            activityFeed.innerHTML = recentActivities.map(activity => `
                <div class="activity-item hover-lift">
                    <div class="activity-avatar">${activity.initials}</div>
                    <div class="activity-content">
                        <div class="activity-text">
                            <strong>${activity.user}</strong> ${activity.action} <strong>${activity.item}</strong>
                        </div>
                        <div class="activity-time">${activity.time}</div>
                    </div>
                    <div class="activity-amount">${activity.amount}</div>
                </div>
            `).join('');
        }

        // Populate popular artworks table
        function populatePopularArtworks() {
            const table = document.getElementById('popularArtworksTable');
            if (!table) return;

            table.innerHTML = popularArtworks.map(artwork => `
                <tr>
                    <td><strong>${artwork.name}</strong></td>
                    <td>${artwork.sales}</td>
                    <td>${artwork.revenue}</td>
                    <td>
                        <span class="badge ${artwork.trend.startsWith('+') ? 'badge-success' : 'badge-error'}">
                            ${artwork.trend}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-secondary">View</button>
                    </td>
                </tr>
            `).join('');
        }

        // Populate users table
        function populateUsers() {
            const table = document.getElementById('usersTable');
            if (!table) return;

            table.innerHTML = users.map(user => `
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div class="activity-avatar" style="width: 40px; height: 40px;">${user.initials}</div>
                            <div>
                                <div style="font-weight: 600;">${user.name}</div>
                            </div>
                        </div>
                    </td>
                    <td>${user.email}</td>
                    <td><span class="badge badge-info">${user.type}</span></td>
                    <td><span class="badge ${getStatusBadgeClass(user.status)}">${user.status}</span></td>
                    <td>${user.joined}</td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <button class="btn btn-sm btn-secondary">Edit</button>
                            <button class="btn btn-sm btn-warning">Suspend</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Get badge class for status
        function getStatusBadgeClass(status) {
            switch (status.toLowerCase()) {
                case 'active':
                    return 'badge-success';
                case 'pending':
                    return 'badge-warning';
                case 'inactive':
                    return 'badge-secondary';
                case 'banned':
                    return 'badge-error';
                default:
                    return 'badge-secondary';
            }
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('open');
            }
        });

        // Simulate real-time updates
        setInterval(function() {
            // Update some random metrics (simulation)
            const metricValues = document.querySelectorAll('.metric-value');
            metricValues.forEach(metric => {
                if (Math.random() > 0.98) { // 2% chance to update
                    const currentValue = parseFloat(metric.textContent.replace(/[$,]/g, ''));
                    const change = (Math.random() - 0.5) * 50;
                    const newValue = Math.max(0, currentValue + change);
                    
                    if (metric.textContent.includes('$')) {
                        metric.textContent = '$' + Math.round(newValue).toLocaleString();
                    } else {
                        metric.textContent = Math.round(newValue).toLocaleString();
                    }
                }
            });
        }, 3000); // Update every 3 seconds

        // Add smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
