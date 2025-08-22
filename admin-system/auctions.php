<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auction Management - Yadawity Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="auctions.css">
</head>
<body class="bg-light">
    <!-- Navbar Placeholder -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <button class="btn btn-outline-light btn-sm me-2" id="sidebarToggle" type="button">Menu</button>
            <a class="navbar-brand" href="dashboard.php">Yadawity Admin</a>
            <div class="navbar-nav ms-auto align-items-center">
                <a class="btn btn-outline-light btn-sm me-2" href="/index.php">Home</a>
                <span class="navbar-text me-3" id="userInfo"></span>
                <button class="btn btn-outline-light btn-sm" onclick="logout()">Logout</button>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside id="sidebar" class="p-3">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
            <li class="nav-item"><a class="nav-link" href="artworks.php">Artworks</a></li>
            <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
            <li class="nav-item"><a class="nav-link" href="auctions.php">Auctions</a></li>
            <li class="nav-item"><a class="nav-link" href="courses.php">Courses</a></li>
            <li class="nav-item"><a class="nav-link" href="galleries.php">Galleries</a></li>
            <li class="nav-item"><a class="nav-link" href="analytics.php">Analytics</a></li>
            <li class="nav-item"><a class="nav-link" href="reports.php">Reports</a></li>
            <li class="nav-item"><a class="nav-link" href="settings.php">Settings</a></li>
        </ul>
    </aside>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Auction Management</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#auctionModal" onclick="openAddAuctionModal()">
                Add New Auction
            </button>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-3">
                <select class="form-select" id="statusFilter" onchange="filterAuctions()">
                    <option value="">All Statuses</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="starting_soon">Starting Soon</option>
                    <option value="active">Active</option>
                    <option value="sold">Sold</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        <!-- Auctions Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product ID</th>
                                <th>Artist ID</th>
                                <th>Starting Bid</th>
                                <th>Current Bid</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="auctionsTableBody">
                            <tr>
                                <td colspan="9" class="text-center">Loading auctions...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Auctions pagination">
                    <ul class="pagination justify-content-center" id="pagination">
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Auction Modal -->
    <div class="modal fade" id="auctionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="auctionModalTitle">Add New Auction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="auctionForm">
                        <input type="hidden" id="auctionId">
                        <div class="mb-3">
                            <label for="productId" class="form-label">Product ID</label>
                            <input type="number" class="form-control" id="productId" required>
                        </div>
                        <div class="mb-3">
                            <label for="artistId" class="form-label">Artist ID</label>
                            <input type="number" class="form-control" id="artistId" required>
                        </div>
                        <div class="mb-3">
                            <label for="startingBid" class="form-label">Starting Bid</label>
                            <input type="number" class="form-control" id="startingBid" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="currentBid" class="form-label">Current Bid</label>
                            <input type="number" class="form-control" id="currentBid" step="0.01" value="0.00">
                        </div>
                        <div class="mb-3">
                            <label for="startTime" class="form-label">Start Time</label>
                            <input type="datetime-local" class="form-control" id="startTime" required>
                        </div>
                        <div class="mb-3">
                            <label for="endTime" class="form-label">End Time</label>
                            <input type="datetime-local" class="form-control" id="endTime" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" required>
                                <option value="upcoming">Upcoming</option>
                                <option value="starting_soon">Starting Soon</option>
                                <option value="active">Active</option>
                                <option value="sold">Sold</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveAuction()">Save Auction</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="auctions.js"></script>
</body>
</html>
