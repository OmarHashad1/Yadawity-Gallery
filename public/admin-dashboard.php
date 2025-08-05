<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard &Analytics - Yadawity Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="./components/Navbar/navbar.css" />`n    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css">
    <link rel="stylesheet" href="./public/admin-dashboard.css">
</head>
<body>
    <!-- Navigation -->
    <?php include './components/includes/navbar.php'; ?>

    <!-- Admin Sidebar -->
    <aside class="adminSidebar" id="adminSidebar">
        <div class="sidebarHeader">
            <h3>Admin Panel</h3>
            <button class="sidebarToggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <nav class="sidebarNav">
            <div class="navSection">
                <h4>OVERVIEW</h4>
                <a href="admin-dashboard.html" class="sidebarLink">
                   <i class="fas fa-chart-line"></i>
                    <span>Dashboard & Analytics</span>
                </a>
            </div>

            <div class="navSection">
                <h4>Communication</h4>
                <a href="admin-communication.html" class="sidebarLink">
                    <i class="fas fa-comments"></i>
                    <span>Support Tickets</span>
                </a>
                <a href="#" class="sidebarLink">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                </a>
                <a href="#" class="sidebarLink">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>
            </div>

            <div class="navSection">
                <h4>Management</h4>
                <a href="admin-user managment.html" class="sidebarLink">
                    <i class="fas fa-user"></i>
                    <span>User Management</span>
                </a>
                <a href="admin-content managment.html" class="sidebarLink">
                    <i class="fas fa-grip"></i>
                    <span>Content managment</span>
                </a>
                <a href="admin- auction managment.html" class="sidebarLink">
                    <i class="fas fa-gavel"></i>
                    <span>Auction managment</span>
                </a>
                <a href="admin-course managment.html" class="sidebarLink">
                    <i class="fas fa-book-open-reader"></i>
                    <span>Course managment</span>
                </a>
                <a href="admin-order transactions.html" class="sidebarLink">
                    <i class="fas fa-truck"></i>
                    <span>Order & transactions</span>
                </a>

                <a href="admin-system.html" class="sidebarLink">
                    <i class="fas fa-cogs"></i>
                    <span>System Admin</span>
                </a>
                <a href="admin-gallery.html" class="sidebarLink">
                    <i class="fas fa-images"></i>
                    <span>Gallery Management</span>
                </a>
                <a href="admin-marketing.html" class="sidebarLink active">
                    <i class="fas fa-chart-line"></i>
                    <span>Marketing</span>
                </a>
                <a href="admin-inventory.html" class="sidebarLink">
                    <i class="fas fa-boxes"></i>
                    <span>Inventory</span>
                </a>
                <a href="admin-legal.html" class="sidebarLink">
                    <i class="fas fa-gavel"></i>
                    <span>Legal & Compliance</span>
                </a>
            </div>
        </nav>
    </aside>

    <main class="adminMain">
        <!-- Dashboard Section -->
        <div id="dashboard-section" class="content-section active">
            <div class="pageHeader">
                <div class="headerContent">
                    <h1>Dashboard Overview</h1>
                    <p>Welcome back! Here's what's happening with your business today.</p>
                </div>
                <div class="headerActions">
                    <button class="btn btn-secondary btn-sm" id="refreshBtn">
                        <i class="fas fa-sync-alt"></i>
                        Refresh
                    </button>
                    <button class="btn btn-primary btn-sm" id="exportBtn">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="statsGrid">
                <div class="statCard">
                    <div class="statIcon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="statContent">
                        <h3 id="totalRevenue">$24,580</h3>
                        <p>Total Revenue</p>
                        <span class="statChange positive">+12.5%</span>
                    </div>
                </div>
                <div class="statCard">
                    <div class="statIcon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="statContent">
                        <h3 id="totalUsers">1,247</h3>
                        <p>Active Users</p>
                        <span class="statChange positive">+8.2%</span>
                    </div>
                </div>
                <div class="statCard">
                    <div class="statIcon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="statContent">
                        <h3 id="totalOrders">342</h3>
                        <p>Orders Today</p>
                        <span class="statChange negative">-3.1%</span>
                    </div>
                </div>
                <div class="statCard">
                    <div class="statIcon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="statContent">
                        <h3 id="conversionRate">3.24%</h3>
                        <p>Conversion Rate</p>
                        <span class="statChange positive">+0.8%</span>
                    </div>
                </div>
            </div>

            <!-- Controls Panel -->
            <div class="controlsPanel">
                <div class="filterGroup">
                    <label>Time Period</label>
                    <select class="filterSelect" id="timePeriod">
                        <option value="today">Today</option>
                        <option value="week" selected>This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
                <div class="filterGroup">
                    <label>Category</label>
                    <select class="filterSelect" id="category">
                        <option value="all">All Categories</option>
                        <option value="sales">Sales</option>
                        <option value="marketing">Marketing</option>
                        <option value="support">Support</option>
                    </select>
                </div>
                <div class="searchGroup">
                    <input type="text" class="searchInput" placeholder="Search..." id="searchInput">
                    <button class="searchBtn" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Charts Row -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 30px;">
                <div class="contentCard">
                    <div class="cardHeader">
                        <h2>Revenue Analytics</h2>
                        <div class="cardActions">
                            <button class="btn btn-outline btn-sm">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div style="padding: 30px;">
                        <canvas id="revenueChart" width="400" height="200"></canvas>
                    </div>
                </div>
                <div class="contentCard">
                    <div class="cardHeader">
                        <h2>Traffic Sources</h2>
                    </div>
                    <div style="padding: 30px;">
                        <canvas id="trafficChart" width="300" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="contentCard">
                <div class="cardHeader">
                    <h2>Recent Activity</h2>
                    <div class="cardActions">
                        <button class="btn btn-secondary btn-sm" id="viewAllBtn">
                            View All
                        </button>
                    </div>
                </div>
                <div class="tableContainer">
                    <table class="dataTable">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Status</th>
                                <th>Value</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="activityTableBody">
                            <tr>
                                <td>2 min ago</td>
                                <td>John Doe</td>
                                <td>Purchase completed</td>
                                <td><span class="statusBadge status-resolved">Success</span></td>
                                <td>$299.99</td>
                                <td>
                                    <button class="actionBtn btn-view">View</button>
                                    <button class="actionBtn btn-reply">Contact</button>
                                </td>
                            </tr>
                            <tr>
                                <td>5 min ago</td>
                                <td>Jane Smith</td>
                                <td>Support ticket created</td>
                                <td><span class="statusBadge status-pending">Pending</span></td>
                                <td>-</td>
                                <td>
                                    <button class="actionBtn btn-view">View</button>
                                    <button class="actionBtn btn-reply">Reply</button>
                                </td>
                            </tr>
                            <tr>
                                <td>12 min ago</td>
                                <td>Mike Johnson</td>
                                <td>Account registration</td>
                                <td><span class="statusBadge status-open">New</span></td>
                                <td>-</td>
                                <td>
                                    <button class="actionBtn btn-view">View</button>
                                </td>
                            </tr>
                            <tr>
                                <td>18 min ago</td>
                                <td>Sarah Wilson</td>
                                <td>Product review</td>
                                <td><span class="statusBadge status-resolved">Published</span></td>
                                <td>5 stars</td>
                                <td>
                                    <button class="actionBtn btn-view">View</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Analytics Section -->
        <div id="analytics-section" class="content-section">
            <div class="pageHeader">
                <div class="headerContent">
                    <h1>Advanced Analytics</h1>
                    <p>Deep dive into your business metrics and performance indicators.</p>
                </div>
                <div class="headerActions">
                    <button class="btn btn-outline btn-sm">
                        <i class="fas fa-calendar"></i>
                        Date Range
                    </button>
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-file-pdf"></i>
                        Generate Report
                    </button>
                </div>
            </div>

            <!-- Analytics Charts -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                <div class="contentCard">
                    <div class="cardHeader">
                        <h2>User Engagement</h2>
                    </div>
                    <div style="padding: 30px;">
                        <canvas id="engagementChart" width="400" height="300"></canvas>
                    </div>
                </div>
                <div class="contentCard">
                    <div class="cardHeader">
                        <h2>Sales Performance</h2>
                    </div>
                    <div style="padding: 30px;">
                        <canvas id="salesChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="contentCard">
                <div class="cardHeader">
                    <h2>Performance Metrics</h2>
                </div>
                <div style="padding: 30px;">
                    <div class="statsGrid">
                        <div class="statCard">
                            <div class="statIcon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="statContent">
                                <h3>45,678</h3>
                                <p>Page Views</p>
                                <span class="statChange positive">+15.3%</span>
                            </div>
                        </div>
                        <div class="statCard">
                            <div class="statIcon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="statContent">
                                <h3>2:34</h3>
                                <p>Avg. Session</p>
                                <span class="statChange positive">+0:23</span>
                            </div>
                        </div>
                        <div class="statCard">
                            <div class="statIcon">
                                <i class="fas fa-mouse-pointer"></i>
                            </div>
                            <div class="statContent">
                                <h3>68.5%</h3>
                                <p>Click Rate</p>
                                <span class="statChange negative">-2.1%</span>
                            </div>
                        </div>
                        <div class="statCard">
                            <div class="statIcon">
                                <i class="fas fa-undo"></i>
                            </div>
                            <div class="statContent">
                                <h3>23.4%</h3>
                                <p>Bounce Rate</p>
                                <span class="statChange positive">-5.2%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Other sections (hidden by default) -->
        <div id="reports-section" class="content-section">
            <div class="pageHeader">
                <div class="headerContent">
                    <h1>Reports</h1>
                    <p>Generate and view detailed reports.</p>
                </div>
            </div>
            <div class="contentCard">
                <div style="padding: 60px; text-align: center;">
                    <i class="fas fa-file-alt" style="font-size: 4rem; color: var(--text-light); margin-bottom: 20px;"></i>
                    <h3>Reports Section</h3>
                    <p>This section would contain detailed reports and analytics.</p>
                </div>
            </div>
        </div>

        <div id="users-section" class="content-section">
            <div class="pageHeader">
                <div class="headerContent">
                    <h1>User Management</h1>
                    <p>Manage user accounts and permissions.</p>
                </div>
            </div>
            <div class="contentCard">
                <div style="padding: 60px; text-align: center;">
                    <i class="fas fa-users" style="font-size: 4rem; color: var(--text-light); margin-bottom: 20px;"></i>
                    <h3>User Management</h3>
                    <p>This section would contain user management tools.</p>
                </div>
            </div>
        </div>

        <div id="products-section" class="content-section">
            <div class="pageHeader">
                <div class="headerContent">
                    <h1>Product Management</h1>
                    <p>Manage your product catalog.</p>
                </div>
            </div>
            <div class="contentCard">
                <div style="padding: 60px; text-align: center;">
                    <i class="fas fa-box" style="font-size: 4rem; color: var(--text-light); margin-bottom: 20px;"></i>
                    <h3>Product Management</h3>
                    <p>This section would contain product management tools.</p>
                </div>
            </div>
        </div>

        <div id="orders-section" class="content-section">
            <div class="pageHeader">
                <div class="headerContent">
                    <h1>Order Management</h1>
                    <p>Track and manage customer orders.</p>
                </div>
            </div>
            <div class="contentCard">
                <div style="padding: 60px; text-align: center;">
                    <i class="fas fa-shopping-cart" style="font-size: 4rem; color: var(--text-light); margin-bottom: 20px;"></i>
                    <h3>Order Management</h3>
                    <p>This section would contain order management tools.</p>
                </div>
            </div>
        </div>

        <div id="messages-section" class="content-section">
            <div class="pageHeader">
                <div class="headerContent">
                    <h1>Messages</h1>
                    <p>View and respond to customer messages.</p>
                </div>
            </div>
            <div class="contentCard">
                <div style="padding: 60px; text-align: center;">
                    <i class="fas fa-envelope" style="font-size: 4rem; color: var(--text-light); margin-bottom: 20px;"></i>
                    <h3>Messages</h3>
                    <p>This section would contain message management tools.</p>
                </div>
            </div>
        </div>

        <div id="notifications-section" class="content-section">
            <div class="pageHeader">
                <div class="headerContent">
                    <h1>Notifications</h1>
                    <p>Manage system notifications and alerts.</p>
                </div>
            </div>
            <div class="contentCard">
                <div style="padding: 60px; text-align: center;">
                    <i class="fas fa-bell" style="font-size: 4rem; color: var(--text-light); margin-bottom: 20px;"></i>
                    <h3>Notifications</h3>
                    <p>This section would contain notification management tools.</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal for detailed views -->
    <div class="modal" id="detailModal">
        <div class="modalContent">
            <div class="modalHeader">
                <h2 id="modalTitle">Details</h2>
                <button class="modalClose" id="modalClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modalBody" id="modalBody">
                <!-- Modal content will be inserted here -->
            </div>
        </div>
    </div>


    <script src="./public/admin-dashboard.js"></script>
</body>
</html>
