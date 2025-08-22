<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Yadawity Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="settings.css">
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
            <h1>System Settings</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSettingModal">
                Add New Setting
            </button>
        </div>

        <!-- Settings Categories -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="settingCategory" id="allSettings" value="" checked>
                    <label class="btn btn-outline-primary" for="allSettings">All Settings</label>
                    
                    <input type="radio" class="btn-check" name="settingCategory" id="generalSettings" value="general">
                    <label class="btn btn-outline-primary" for="generalSettings">General</label>
                    
                    <input type="radio" class="btn-check" name="settingCategory" id="emailSettings" value="email">
                    <label class="btn btn-outline-primary" for="emailSettings">Email</label>
                    
                    <input type="radio" class="btn-check" name="settingCategory" id="paymentSettings" value="payment">
                    <label class="btn btn-outline-primary" for="paymentSettings">Payment</label>
                    
                    <input type="radio" class="btn-check" name="settingCategory" id="securitySettings" value="security">
                    <label class="btn btn-outline-primary" for="securitySettings">Security</label>
                </div>
            </div>
        </div>

        <!-- Settings Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Value</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="settingsTableBody">
                            <tr>
                                <td colspan="6" class="text-center">Loading settings...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Setting Modal -->
    <div class="modal fade" id="addSettingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="settingModalTitle">Add New Setting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="settingForm">
                        <input type="hidden" id="settingId">
                        <div class="mb-3">
                            <label for="settingKey" class="form-label">Setting Key</label>
                            <input type="text" class="form-control" id="settingKey" required>
                            <div class="form-text">Use lowercase with underscores (e.g., site_name, max_upload_size)</div>
                        </div>
                        <div class="mb-3">
                            <label for="settingValue" class="form-label">Setting Value</label>
                            <textarea class="form-control" id="settingValue" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="settingCategory" class="form-label">Category</label>
                            <select class="form-select" id="settingCategory" required>
                                <option value="general">General</option>
                                <option value="email">Email</option>
                                <option value="payment">Payment</option>
                                <option value="security">Security</option>
                                <option value="system">System</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="settingDescription" class="form-label">Description</label>
                            <input type="text" class="form-control" id="settingDescription">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveSetting()">Save Setting</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="settings.js"></script>
</body>
</html>
