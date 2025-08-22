let currentReportData = null;

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
});

async function loadReport() {
    const reportType = document.getElementById('reportType').value;
    const fromDate = document.getElementById('fromDate').value;
    const toDate = document.getElementById('toDate').value;

    if (!reportType || !fromDate || !toDate) {
        return;
    }

    try {
        const response = await fetch(`/admin-system/API/reports.php?type=${reportType}&from_date=${fromDate}&to_date=${toDate}`, {
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
            currentReportData = data.data;
            displayReport(reportType, data.data);
            updateReportSummary(data.data);
            document.getElementById('reportSummary').style.display = 'flex';
        } else {
            console.error('Failed to load report:', data.error);
            showErrorMessage('Failed to load report: ' + (data.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error loading report:', error);
        showErrorMessage('Error loading report. Please try again.');
    }
}

function displayReport(reportType, data) {
    const reportTitle = document.getElementById('reportTitle');
    const reportContent = document.getElementById('reportContent');

    // Set report title
    const titleMap = {
        'sales_summary': 'Sales Summary Report',
        'user_activity': 'User Activity Report',
        'artwork_performance': 'Artwork Performance Report',
        'auction_results': 'Auction Results Report',
        'revenue_analysis': 'Revenue Analysis Report',
        'inventory_status': 'Inventory Status Report'
    };
    reportTitle.textContent = titleMap[reportType] || 'Report';

    // Generate report content based on type
    let html = '';
    
    switch (reportType) {
        case 'sales_summary':
            html = generateSalesSummaryReport(data);
            break;
        case 'user_activity':
            html = generateUserActivityReport(data);
            break;
        case 'artwork_performance':
            html = generateArtworkPerformanceReport(data);
            break;
        case 'auction_results':
            html = generateAuctionResultsReport(data);
            break;
        case 'revenue_analysis':
            html = generateRevenueAnalysisReport(data);
            break;
        case 'inventory_status':
            html = generateInventoryStatusReport(data);
            break;
        default:
            html = '<div class="text-center text-muted py-5">Unknown report type</div>';
    }

    reportContent.innerHTML = html;
}

function generateSalesSummaryReport(data) {
    if (!data.sales_data || data.sales_data.length === 0) {
        return '<div class="text-center text-muted py-5">No sales data available for the selected period</div>';
    }

    let html = `
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Orders</th>
                        <th>Revenue</th>
                        <th>Average Order Value</th>
                    </tr>
                </thead>
                <tbody>
    `;

    data.sales_data.forEach(item => {
        html += `
            <tr>
                <td>${new Date(item.date).toLocaleDateString()}</td>
                <td>${item.orders}</td>
                <td>$${item.revenue.toLocaleString()}</td>
                <td>$${item.average_order_value.toLocaleString()}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    return html;
}

function generateUserActivityReport(data) {
    if (!data.user_activity || data.user_activity.length === 0) {
        return '<div class="text-center text-muted py-5">No user activity data available for the selected period</div>';
    }

    let html = `
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Login Count</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    `;

    data.user_activity.forEach(item => {
        html += `
            <tr>
                <td>${item.user_id}</td>
                <td>${item.name}</td>
                <td>${item.login_count}</td>
                <td>${new Date(item.last_login).toLocaleString()}</td>
                <td>${item.actions}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    return html;
}

function generateArtworkPerformanceReport(data) {
    if (!data.artwork_performance || data.artwork_performance.length === 0) {
        return '<div class="text-center text-muted py-5">No artwork performance data available for the selected period</div>';
    }

    let html = `
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Artwork ID</th>
                        <th>Title</th>
                        <th>Views</th>
                        <th>Sales</th>
                        <th>Revenue</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody>
    `;

    data.artwork_performance.forEach(item => {
        html += `
            <tr>
                <td>${item.artwork_id}</td>
                <td>${item.title}</td>
                <td>${item.views}</td>
                <td>${item.sales}</td>
                <td>$${item.revenue.toLocaleString()}</td>
                <td>${item.rating ? item.rating.toFixed(1) : 'N/A'}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    return html;
}

function generateAuctionResultsReport(data) {
    if (!data.auction_results || data.auction_results.length === 0) {
        return '<div class="text-center text-muted py-5">No auction results data available for the selected period</div>';
    }

    let html = `
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Auction ID</th>
                        <th>Product</th>
                        <th>Starting Bid</th>
                        <th>Final Bid</th>
                        <th>Status</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
    `;

    data.auction_results.forEach(item => {
        const statusClass = getStatusClass(item.status);
        html += `
            <tr>
                <td>${item.auction_id}</td>
                <td>${item.product_name}</td>
                <td>$${item.starting_bid.toLocaleString()}</td>
                <td>$${item.final_bid.toLocaleString()}</td>
                <td><span class="badge ${statusClass}">${item.status}</span></td>
                <td>${new Date(item.end_date).toLocaleDateString()}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    return html;
}

function generateRevenueAnalysisReport(data) {
    if (!data.revenue_analysis || data.revenue_analysis.length === 0) {
        return '<div class="text-center text-muted py-5">No revenue analysis data available for the selected period</div>';
    }

    let html = `
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Revenue</th>
                        <th>Percentage</th>
                        <th>Growth</th>
                    </tr>
                </thead>
                <tbody>
    `;

    data.revenue_analysis.forEach(item => {
        const growthClass = item.growth >= 0 ? 'text-success' : 'text-danger';
        const growthIcon = item.growth >= 0 ? '↗' : '↘';
        
        html += `
            <tr>
                <td>${item.category}</td>
                <td>$${item.revenue.toLocaleString()}</td>
                <td>${item.percentage.toFixed(1)}%</td>
                <td class="${growthClass}">${growthIcon} ${Math.abs(item.growth).toFixed(1)}%</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    return html;
}

function generateInventoryStatusReport(data) {
    if (!data.inventory_status || data.inventory_status.length === 0) {
        return '<div class="text-center text-muted py-5">No inventory status data available for the selected period</div>';
    }

    let html = `
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Artwork ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Stock</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
    `;

    data.inventory_status.forEach(item => {
        const statusClass = getInventoryStatusClass(item.status);
        html += `
            <tr>
                <td>${item.artwork_id}</td>
                <td>${item.title}</td>
                <td><span class="badge ${statusClass}">${item.status}</span></td>
                <td>${item.stock}</td>
                <td>${new Date(item.last_updated).toLocaleDateString()}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    return html;
}

function updateReportSummary(data) {
    document.getElementById('totalRecords').textContent = data.total_records || 0;
    document.getElementById('totalValue').textContent = `$${(data.total_value || 0).toLocaleString()}`;
    document.getElementById('averageValue').textContent = `$${(data.average_value || 0).toLocaleString()}`;
    document.getElementById('reportStatus').textContent = data.status || 'Complete';
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

function getInventoryStatusClass(status) {
    const statusClasses = {
        'in_stock': 'bg-success',
        'low_stock': 'bg-warning',
        'out_of_stock': 'bg-danger',
        'discontinued': 'bg-secondary'
    };
    return statusClasses[status] || 'bg-secondary';
}

function showErrorMessage(message) {
    const reportContent = document.getElementById('reportContent');
    reportContent.innerHTML = `
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle"></i>
            ${message}
        </div>
    `;
}

function exportReport() {
    if (!currentReportData) {
        alert('Please generate a report first');
        return;
    }

    // Create CSV content
    const reportType = document.getElementById('reportType').value;
    const fromDate = document.getElementById('fromDate').value;
    const toDate = document.getElementById('toDate').value;
    
    let csvContent = `Report: ${reportType}\nFrom: ${fromDate}\nTo: ${toDate}\n\n`;
    
    // Add data based on report type
    // This is a simplified export - you might want to enhance this
    csvContent += JSON.stringify(currentReportData, null, 2);
    
    // Create and download file
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${reportType}_${fromDate}_${toDate}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
}

function printReport() {
    if (!currentReportData) {
        alert('Please generate a report first');
        return;
    }
    
    window.print();
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
