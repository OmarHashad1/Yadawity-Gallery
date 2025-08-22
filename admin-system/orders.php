<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Yadawity Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="orders.css">
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
            <h1>Order Management</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#orderModal" onclick="openAddOrderModal()">
                Add New Order
            </button>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-3">
                <select class="form-select" id="statusFilter" onchange="filterOrders()">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control" id="dateFilter" onchange="filterOrders()">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="searchInput" placeholder="Search orders..." onkeyup="filterOrders()">
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order Number</th>
                                <th>Buyer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                            <tr>
                                <td colspan="7" class="text-center">Loading orders...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Orders pagination">
                    <ul class="pagination justify-content-center" id="pagination">
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Order Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalTitle">Add New Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="orderForm">
                        <input type="hidden" id="orderId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="orderNumber" class="form-label">Order Number</label>
                                    <input type="text" class="form-control" id="orderNumber" required>
                                </div>
                                <div class="mb-3">
                                    <label for="buyerId" class="form-label">Buyer ID</label>
                                    <input type="number" class="form-control" id="buyerId" required>
                                </div>
                                <div class="mb-3">
                                    <label for="buyerName" class="form-label">Buyer Name</label>
                                    <input type="text" class="form-control" id="buyerName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="totalAmount" class="form-label">Total Amount</label>
                                    <input type="number" class="form-control" id="totalAmount" step="0.01" required>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" required>
                                        <option value="pending">Pending</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="shipped">Shipped</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="orderDate" class="form-label">Order Date</label>
                                    <input type="date" class="form-control" id="orderDate" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="shippingAddress" class="form-label">Shipping Address</label>
                            <textarea class="form-control" id="shippingAddress" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveOrder()">Save Order</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="orders.js"></script>
</body>
</html>
