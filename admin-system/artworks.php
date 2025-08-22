<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artwork Management - Yadawity Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="artworks.css">
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
            <h1>Artwork Management</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#artworkModal" onclick="openAddArtworkModal()">
                Add New Artwork
            </button>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-3">
                <select class="form-select" id="typeFilter" onchange="filterArtworks()">
                    <option value="">All Types</option>
                    <option value="painting">Painting</option>
                    <option value="sculpture">Sculpture</option>
                    <option value="photography">Photography</option>
                    <option value="digital">Digital</option>
                    <option value="mixed_media">Mixed Media</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>

        <!-- Artworks Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Artist ID</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Auction</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="artworksTableBody">
                            <tr>
                                <td colspan="9" class="text-center">Loading artworks...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Artworks pagination">
                    <ul class="pagination justify-content-center" id="pagination">
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Artwork Modal -->
    <div class="modal fade" id="artworkModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="artworkModalTitle">Add New Artwork</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="artworkForm">
                        <input type="hidden" id="artworkId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="artistId" class="form-label">Artist ID</label>
                                    <input type="number" class="form-control" id="artistId" required>
                                </div>
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <select class="form-select" id="type" required>
                                        <option value="painting">Painting</option>
                                        <option value="sculpture">Sculpture</option>
                                        <option value="photography">Photography</option>
                                        <option value="digital">Digital</option>
                                        <option value="mixed_media">Mixed Media</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="number" class="form-control" id="price" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="dimensions" class="form-label">Dimensions</label>
                                    <input type="text" class="form-control" id="dimensions" placeholder="e.g., 24x36 inches">
                                </div>
                                <div class="mb-3">
                                    <label for="year" class="form-label">Year</label>
                                    <input type="text" class="form-control" id="year" placeholder="e.g., 2023">
                                </div>
                                <div class="mb-3">
                                    <label for="material" class="form-label">Material</label>
                                    <input type="text" class="form-control" id="material" placeholder="e.g., Oil on canvas">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="artworkImage" class="form-label">Image URL</label>
                                    <input type="url" class="form-control" id="artworkImage">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isAvailable" checked>
                                        <label class="form-check-label" for="isAvailable">
                                            Available
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="onAuction">
                                        <label class="form-check-label" for="onAuction">
                                            On Auction
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveArtwork()">Save Artwork</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="artworks.js"></script>
</body>
</html>
