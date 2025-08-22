<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Yadawity Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="analytics.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <h1>Analytics</h1>
            <div class="d-flex gap-2">
                <input type="date" class="form-control" id="fromDate" onchange="loadAnalytics()">
                <input type="date" class="form-control" id="toDate" onchange="loadAnalytics()">
            </div>
        </div>

        <!-- Analytics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Orders</h5>
                        <h2 id="totalOrders">-</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <h2 id="totalRevenue">$0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Active Auctions</h5>
                        <h2 id="activeAuctions">-</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Artworks by Type</h5>
                        <h2 id="artworksByType">-</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h6>Daily Orders & Revenue</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyOrdersChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h6>Top Artworks by Sales</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="topArtworksChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Analytics -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h6>Auctions Status</h6>
                    </div>
                    <div class="card-body">
                        <div id="auctionsStatus"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h6>Artworks by Type</h6>
                    </div>
                    <div class="card-body">
                        <div id="artworksByTypeDetails"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="analytics.js"></script>
</body>
</html>
