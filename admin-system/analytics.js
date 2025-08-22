let dailyOrdersChart = null;
let topArtworksChart = null;

document.addEventListener('DOMContentLoaded', function() {
    // Check authentication
    if (!localStorage.getItem('csrf_token')) {
        window.location.href = 'login.php';
        return;
    }

    // Display user info
    const userInfo = document.getElementById('userInfo');
    const userName = localStorage.getItem('user_name') || 'Admin';
    userInfo.textContent = `Welcome, ${userName}`;

    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-open');
        });
    }

    // Set default date range (last 30 days)
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    document.getElementById('fromDate').value = thirtyDaysAgo.toISOString().split('T')[0];
    document.getElementById('toDate').value = today.toISOString().split('T')[0];

    // Load analytics
    loadAnalytics();
});

async function loadAnalytics() {
    const fromDate = document.getElementById('fromDate').value;
    const toDate = document.getElementById('toDate').value;

    if (!fromDate || !toDate) {
        return;
    }

    try {
        const response = await fetch(`/admin-system/API/analytics.php?from_date=${fromDate}&to_date=${toDate}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.status === 401 || response.status === 403) {
            localStorage.clear();
            window.location.href = 'login.php';
            return;
        }

        const data = await response.json();
        
        if (response.ok && data.data) {
            updateAnalyticsCards(data.data);
            updateCharts(data.data);
            updateAdditionalAnalytics(data.data);
        } else {
            console.error('Failed to load analytics:', data.error);
        }
    } catch (error) {
        console.error('Error loading analytics:', error);
    }
}

function updateAnalyticsCards(data) {
    // Update summary cards
    document.getElementById('totalOrders').textContent = data.total_orders || 0;
    document.getElementById('totalRevenue').textContent = `$${(data.total_revenue || 0).toLocaleString()}`;
    document.getElementById('activeAuctions').textContent = data.active_auctions || 0;
    document.getElementById('artworksByType').textContent = data.artworks_by_type_count || 0;
}

function updateCharts(data) {
    // Daily Orders & Revenue Chart
    if (dailyOrdersChart) {
        dailyOrdersChart.destroy();
    }

    const dailyOrdersCtx = document.getElementById('dailyOrdersChart').getContext('2d');
    dailyOrdersChart = new Chart(dailyOrdersCtx, {
        type: 'line',
        data: {
            labels: data.daily_orders?.map(item => item.date) || [],
            datasets: [
                {
                    label: 'Orders',
                    data: data.daily_orders?.map(item => item.count) || [],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    yAxisID: 'y'
                },
                {
                    label: 'Revenue',
                    data: data.daily_orders?.map(item => item.revenue) || [],
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Orders'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Revenue ($)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });

    // Top Artworks Chart
    if (topArtworksChart) {
        topArtworksChart.destroy();
    }

    const topArtworksCtx = document.getElementById('topArtworksChart').getContext('2d');
    topArtworksChart = new Chart(topArtworksCtx, {
        type: 'doughnut',
        data: {
            labels: data.top_artworks?.map(item => item.title) || [],
            datasets: [{
                data: data.top_artworks?.map(item => item.sales_count) || [],
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: 'Top Selling Artworks'
                }
            }
        }
    });
}

function updateAdditionalAnalytics(data) {
    // Auctions Status
    const auctionsStatusDiv = document.getElementById('auctionsStatus');
    if (data.auctions_status) {
        let html = '';
        Object.entries(data.auctions_status).forEach(([status, count]) => {
            const statusClass = getStatusClass(status);
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge ${statusClass}">${status}</span>
                    <span class="fw-bold">${count}</span>
                </div>
            `;
        });
        auctionsStatusDiv.innerHTML = html;
    }

    // Artworks by Type Details
    const artworksByTypeDiv = document.getElementById('artworksByTypeDetails');
    if (data.artworks_by_type) {
        let html = '';
        Object.entries(data.artworks_by_type).forEach(([type, count]) => {
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-capitalize">${type}</span>
                    <span class="badge bg-secondary">${count}</span>
                </div>
            `;
        });
        artworksByTypeDiv.innerHTML = html;
    }
}

function getStatusClass(status) {
    const statusClasses = {
        'upcoming': 'bg-secondary',
        'starting_soon': 'bg-info',
        'active': 'bg-success',
        'sold': 'bg-primary',
        'cancelled': 'bg-danger'
    };
    return statusClasses[status] || 'bg-secondary';
}

function logout() {
    fetch('/admin-system/API/logout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': localStorage.getItem('csrf_token')
        }
    }).finally(() => {
        localStorage.clear();
        window.location.href = 'login.php';
    });
}
