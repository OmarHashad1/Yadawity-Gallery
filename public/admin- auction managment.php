<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard &Analytics - Yadawity Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="./components/Navbar/navbar.css" />`n    <link rel="stylesheet" href="./components/BurgerMenu/burger-menu.css">
    <link rel="stylesheet" href="./public/admin-auction managment.css">
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
                <h1>Auctions Managment</h1>
                <p>Monitor and manage all ongoing auctions in real-time.</p>
            </div>
            <div class="headerActions">
                <button class="btn btn-secondary" id="refreshBtn">
                    <i class="fas fa-refresh"></i>
                    Refresh
                </button>
                <button class="btn btn-primary" id="createAuctionBtn">
                    <i class="fas fa-plus"></i>
                    Create Auction
                </button>
            </div>
        </div>

        <!-- Auction Stats -->
        <div class="statsGrid">
            <div class="statCard">
                <div class="statIcon">
                    <i class="fas fa-gavel"></i>
                </div>
                <div class="statContent">
                    <h3>24</h3>
                    <p>Active Auctions</p>
                    <span class="statChange positive">+3</span>
                </div>
            </div>
            <div class="statCard">
                <div class="statIcon">
                    <i class="fas fa-hand-paper"></i>
                </div>
                <div class="statContent">
                    <h3>1,847</h3>
                    <p>Total Bids</p>
                    <span class="statChange positive">+127</span>
                </div>
            </div>
            <div class="statCard">
                <div class="statIcon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="statContent">
                    <h3>$45,230</h3>
                    <p>Current Value</p>
                    <span class="statChange positive">+$3,200</span>
                </div>
            </div>
            <div class="statCard">
                <div class="statIcon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="statContent">
                    <h3>6</h3>
                    <p>Ending Soon</p>
                    <span class="statChange negative">-2</span>
                </div>
            </div>
        </div>

        <!-- Active Auctions -->
        <div class="contentCard">
            <div class="cardHeader">
                <h2>Active Auctions</h2>
                <div class="cardActions">
                    <button class="btn btn-sm btn-outline" id="filterAuctionsBtn">Filter</button>
                </div>
            </div>
            <div class="tableContainer">
                <table class="dataTable">
                    <thead>
                        <tr>
                            <th>Artwork</th>
                            <th>Current Bid</th>
                            <th>Bidders</th>
                            <th>Time Left</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="auctionsTable">
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 40px; height: 40px; background: var(--light-gold); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                    <span>Modern Abstract #3</span>
                                </div>
                            </td>
                            <td>$2,400</td>
                            <td>12</td>
                            <td><span class="countdown" data-end="2024-12-31T23:59:59">2h 34m</span></td>
                            <td><span class="statusBadge status-open">Active</span></td>
                            <td>
                                <button class="actionBtn btn-view">Monitor</button>
                                <button class="actionBtn btn-reply">Manage</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 40px; height: 40px; background: var(--light-gold); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                    <span>Vintage Portrait</span>
                                </div>
                            </td>
                            <td>$1,800</td>
                            <td>8</td>
                            <td><span class="countdown" data-end="2024-12-31T23:59:59">45m</span></td>
                            <td><span class="statusBadge status-pending">Ending Soon</span></td>
                            <td>
                                <button class="actionBtn btn-view">Monitor</button>
                                <button class="actionBtn btn-reply">Manage</button>
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


    <script src="./public/admin-auction managment.js"></script>
</body>
</html>
