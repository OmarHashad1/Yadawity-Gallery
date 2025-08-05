<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard &Analytics - Yadawity Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="./components/Navbar/navbar.css" />`n    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css">
    <link rel="stylesheet" href="./public/admin-course managment.css">
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
<!-- Main Content -->
    <main class="adminMain">
        <div class="pageHeader">
            <div class="headerContent">
                <h1>Course Management</h1>
                <p>Review, approve, and manage all artwork submissions.</p>
            </div>
            <div class="headerActions">
                <button class="btn btn-secondary" id="filterArtworksBtn">
                    <i class="fas fa-filter"></i>
                    Filter Course
                </button>
                <button class="btn btn-primary" id="addArtworkBtn">
                    <i class="fas fa-plus"></i>
                    Add Course
                </button>
            </div>
        </div>

        <!-- Artwork Stats -->
        <div class="statsGrid">
            <div class="statCard">
                <div class="statIcon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="statContent">
                    <h3>47</h3>
                    <p>Pending Review</p>
                    <span class="statChange positive">+5</span>
                </div>
            </div>
            <div class="statCard">
                <div class="statIcon">
                    <i class="fas fa-check"></i>
                </div>
                <div class="statContent">
                    <h3>3,421</h3>
                    <p>Approved</p>
                    <span class="statChange positive">+23</span>
                </div>
            </div>
            <div class="statCard">
                <div class="statIcon">
                    <i class="fas fa-times"></i>
                </div>
                <div class="statContent">
                    <h3>89</h3>
                    <p>Rejected</p>
                    <span class="statChange negative">+3</span>
                </div>
            </div>
            <div class="statCard">
                <div class="statIcon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="statContent">
                    <h3>156</h3>
                    <p>Featured</p>
                    <span class="statChange positive">+8</span>
                </div>
            </div>
        </div>

        <!-- Controls Panel -->
        <div class="controlsPanel">
            <div class="filterGroup">
                <label>Category</label>
                <select class="filterSelect" id="categoryFilter">
                    <option>All Categories</option>
                    <option>Painting</option>
                    <option>Photography</option>
                    <option>Sculpture</option>
                    <option>Digital Art</option>
                </select>
            </div>
            <div class="filterGroup">
                <label>Status</label>
                <select class="filterSelect" id="statusFilter">
                    <option>All Status</option>
                    <option>Pending</option>
                    <option>Approved</option>
                    <option>Rejected</option>
                    <option>Featured</option>
                </select>
            </div>
            <div class="searchGroup">
                <input type="text" class="searchInput" id="artworkSearch" placeholder="Search artworks...">
                <button class="searchBtn" id="searchArtworksBtn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Recent Submissions -->
        <div class="contentCard">
            <div class="cardHeader">
                <h2>Recent Submissions</h2>
                <div class="cardActions">
                    <button class="btn btn-sm btn-outline" id="bulkActionsBtn">Bulk Actions</button>
                </div>
            </div>
            <div class="tableContainer">
                <table class="dataTable">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Artwork</th>
                            <th>Artist</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="artworksTable">
                        <tr>
                            <td>
                                <input type="checkbox" class="artwork-checkbox" data-id="1">
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 40px; height: 40px; background: var(--light-gold); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                    <span>Sunset Dreams</span>
                                </div>
                            </td>
                            <td>Elena Rodriguez</td>
                            <td>Painting</td>
                            <td>$1,200</td>
                            <td>2 hours ago</td>
                            <td><span class="statusBadge status-pending">Pending</span></td>
                            <td>
                                <button class="actionBtn btn-view">Review</button>
                                <button class="actionBtn btn-reply">Approve</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" class="artwork-checkbox" data-id="2">
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 40px; height: 40px; background: var(--light-gold); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                    <span>Urban Reflections</span>
                                </div>
                            </td>
                            <td>Marcus Chen</td>
                            <td>Photography</td>
                            <td>$800</td>
                            <td>1 day ago</td>
                            <td><span class="statusBadge status-open">Approved</span></td>
                            <td>
                                <button class="actionBtn btn-view">View</button>
                                <button class="actionBtn btn-reply">Edit</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" class="artwork-checkbox" data-id="3">
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 40px; height: 40px; background: var(--light-gold); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                    <span>Abstract Harmony</span>
                                </div>
                            </td>
                            <td>Isabella Thompson</td>
                            <td>Digital Art</td>
                            <td>$950</td>
                            <td>3 days ago</td>
                            <td><span class="statusBadge status-resolved">Featured</span></td>
                            <td>
                                <button class="actionBtn btn-view">View</button>
                                <button class="actionBtn btn-reply">Edit</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Mobile Sidebar Toggle -->
    <button class="sidebarToggle" id="mobileSidebarToggle" style="display: none; position: fixed; top: 90px; left: 20px; z-index: 1001; background: var(--primary-brown); color: white; border: none; padding: 10px; border-radius: 8px;">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Modal -->
    <div class="modal" id="actionModal">
        <div class="modalContent">
            <div class="modalHeader">
                <h2 id="modalTitle">Action</h2>
                <button class="modalClose" id="modalClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modalBody" id="modalBody">
                <!-- Modal content will be dynamically inserted here -->
            </div>
        </div>
    </div>

   
    <script src="./public/admin-course managment.js"></script>
</body>
</html>
