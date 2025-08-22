<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Yadawity Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="reports.css">
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
            <h1>Reports</h1>
            <div class="d-flex gap-2">
                <button class="btn btn-success" onclick="exportReport()">
                    <i class="bi bi-download"></i> Export
                </button>
                <button class="btn btn-primary" onclick="printReport()">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
        </div>

        <!-- Report Controls -->
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="reportType" class="form-label">Report Type</label>
                <select class="form-select" id="reportType" onchange="loadReport()">
                    <option value="">Select Report Type</option>
                    <option value="sales_summary">Sales Summary</option>
                    <option value="user_activity">User Activity</option>
                    <option value="artwork_performance">Artwork Performance</option>
                    <option value="auction_results">Auction Results</option>
                    <option value="revenue_analysis">Revenue Analysis</option>
                    <option value="inventory_status">Inventory Status</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="fromDate" class="form-label">From Date</label>
                <input type="date" class="form-control" id="fromDate" onchange="loadReport()">
            </div>
            <div class="col-md-4">
                <label for="toDate" class="form-label">To Date</label>
                <input type="date" class="form-control" id="toDate" onchange="loadReport()">
            </div>
        </div>

        <!-- Report Content -->
        <div class="card">
            <div class="card-header">
                <h5 id="reportTitle">Select a report type to begin</h5>
            </div>
            <div class="card-body">
                <div id="reportContent">
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-file-earmark-text" style="font-size: 3rem;"></i>
                        <p class="mt-3">Choose a report type and date range to generate your report</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Summary -->
        <div class="row mt-4" id="reportSummary" style="display: none;">
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h6 class="card-title">Total Records</h6>
                        <h4 id="totalRecords">-</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h6 class="card-title">Total Value</h6>
                        <h4 id="totalValue">$0</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h6 class="card-title">Average</h6>
                        <h4 id="averageValue">$0</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h6 class="card-title">Status</h6>
                        <h4 id="reportStatus">-</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="reports.js"></script>
</body>
</html>
