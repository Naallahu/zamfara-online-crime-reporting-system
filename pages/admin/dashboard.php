<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/auth_helper.php';
require_once '../../includes/admin_auth.php';
require_once '../../includes/dashboard_queries.php';
require_once '../../includes/dashboard_utils.php';
require_once '../../classes/Database.php';
require_once '../../classes/Report.php';
require_once '../../classes/Notification.php';
require_once '../../includes/security_helper.php';
require_once '../../vendor/autoload.php';
require_once '../../includes/time_helper.php';
require_once '../../includes/browser_helper.php';
require_once '../../includes/ActivityLogger.php';

// Security checks
secureHeaders();
$csrf_token = generateCSRFToken();
checkSessionTimeout();
$admin = getAdminInfo();

// Database connection
$database = new Database();
$db = $database->connect();

// Get dashboard statistics
$stats = getDashboardStats($conn);
$recent_reports = getRecentReports($conn);
$crime_types_result = getCrimeTypesData($conn);
$monthly_trends_result = getMonthlyTrends($conn);
$lga_stats = getLGAStats($conn);

// Get status counts directly from database
$total_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reports WHERE status='pending'"))['count'];
$total_inprogress = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reports WHERE status='in_progress'"))['count'];
$total_resolved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reports WHERE status='resolved'"))['count'];
$recent_activities = mysqli_query($conn, "SELECT * FROM admin_activity_log ORDER BY created_at DESC LIMIT 5");

// Include header
include '../../includes/admin_header.php';
?>
<body>

 <!-- Real-time Status Indicator -->
    <div class="position-fixed bottom-0 end-0 p-3">
        <div class="connection-status">
            <span class="badge bg-success">
                <i class="fas fa-circle"></i> Connected
            </span>
        </div>
    </div>

<div class="container-fluid py-4">
    <!-- Welcome Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Dashboard Overview</h1>
            <p class="text-muted">Welcome to your admin dashboard</p>
        </div>
    </div>

<form method="POST" action="process_action.php">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <!-- Your form fields -->
</form>

   <!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Quick Actions</h5>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-primary" id="generateReportBtn">
                        <i class="fas fa-file-pdf me-2"></i>Generate Report
                    </a>
                    <a href="#" class="btn btn-success" id="exportDataBtn">
                        <i class="fas fa-file-excel me-2"></i>Export Data
                    </a>
                    <a href="#" class="btn btn-info text-white" id="printSummaryBtn">
                        <i class="fas fa-print me-2"></i>Print Summary
                    </a>
                    <a href="#" class="btn btn-warning text-white" id="showNotificationsBtn">
                        <i class="fas fa-bell me-2"></i>Notifications
                        <span class="badge bg-danger ms-2">3</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Real-time Statistics Cards -->
<div class="row mb-4">
    <!-- Pending Reports Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            <span>Pending Reports</span>
                            <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="Reports awaiting review"></i>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingCount">
                            <?php echo $total_pending; ?>
                        </div>
                        <div class="small text-success mt-2">
                            <i class="fas fa-arrow-up"></i>
                            <span id="pendingTrend">+5% from last week</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- In Progress Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            <span>In Progress</span>
                            <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="Cases under investigation"></i>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="inProgressCount">
                            <?php echo $total_inprogress; ?>
                        </div>
                        <div class="small text-warning mt-2">
                            <i class="fas fa-arrow-right"></i>
                            <span id="inProgressTrend">No change from last week</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-spinner fa-2x text-info opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resolved Cases Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            <span>Resolved Cases</span>
                            <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="Successfully resolved cases"></i>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="resolvedCount">
                            <?php echo $total_resolved; ?>
                        </div>
                        <div class="small text-success mt-2">
                            <i class="fas fa-arrow-up"></i>
                            <span id="resolvedTrend">+8% from last week</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Response Rate Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            <span>Response Rate</span>
                            <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="Overall case resolution rate"></i>
                        </div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800" id="responseRate">78%</div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 78%" aria-valuenow="78" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="small text-success mt-2">
                            <i class="fas fa-arrow-up"></i>
                            <span id="responseTrend">+3% from last month</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percent fa-2x text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add this right after your existing cards -->
<div class="notification-center">
    <!-- Toast Notification -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
        <div id="notification-toast" class="toast" role="alert">
            <div class="toast-header">
                <i class="fas fa-bell me-2"></i>
                <strong class="me-auto" id="toast-title">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="toast-message"></div>
        </div>
    </div>

    <!-- Live Updates Badge -->
    <div class="position-fixed top-0 end-0 top-3">
        <span class="badge bg-success" id="live-indicator">
            <i class="fas fa-circle text-white"></i> Live
        </span>
    </div>
</div>

<!-- Enhanced Activity Feed with Advanced Features -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <h5 class="mb-0 me-3">Recent Activities</h5>
                    <span class="badge bg-primary" id="activity-count">0</span>
                </div>
                <div class="header-actions">
                    <select class="form-select form-select-sm me-2 d-inline-block w-auto" id="activity-filter">
                        <option value="all">All Activities</option>
                        <option value="login">Logins</option>
                        <option value="report">Reports</option>
                        <option value="update">Updates</option>
                        <option value="delete">Deletions</option>
                    </select>
                    <a href="activity_log.php" class="btn btn-sm btn-primary">View All</a>
                </div>
            </div>
            <div class="card-body">
                <div class="activity-feed" id="activity-feed">
                    <?php while($activity = mysqli_fetch_assoc($recent_activities)): ?>
                        <div class="feed-item d-flex align-items-start mb-3 p-3 border-bottom" 
                             data-activity-type="<?php echo strtolower($activity['action']); ?>">
                            <!-- Activity Icon with Animation -->
                            <div class="activity-icon me-3 animate__animated animate__fadeIn">
                                <?php
                                $iconClass = 'text-primary';
                                switch(strtolower($activity['action'])) {
                                    case 'login':
                                        $icon = 'fa-sign-in-alt';
                                        $iconClass = 'text-success';
                                        break;
                                    case 'report':
                                        $icon = 'fa-file-alt';
                                        $iconClass = 'text-info';
                                        break;
                                    case 'update':
                                        $icon = 'fa-edit';
                                        $iconClass = 'text-warning';
                                        break;
                                    case 'delete':
                                        $icon = 'fa-trash-alt';
                                        $iconClass = 'text-danger';
                                        break;
                                    default:
                                        $icon = 'fa-info-circle';
                                }
                                ?>
                                <i class="fas <?php echo $icon; ?> fa-lg <?php echo $iconClass; ?>"></i>
                            </div>
                            
                            <!-- Enhanced Activity Content -->
                            <div class="activity-content flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="<?php echo $iconClass; ?>">
                                            <?php echo htmlspecialchars($activity['action']); ?>
                                        </strong>
                                        <span class="badge bg-light text-dark ms-2">
                                            <?php echo timeAgo($activity['created_at']); ?>
                                        </span>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-link btn-sm p-0" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="viewDetails(<?php echo $activity['id']; ?>)">
                                                <i class="fas fa-eye me-2"></i>View Details
                                            </a></li>
                                            <li><a class="dropdown-item" href="#" onclick="exportActivity(<?php echo $activity['id']; ?>)">
                                                <i class="fas fa-download me-2"></i>Export
                                            </a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <?php echo htmlspecialchars($activity['details']); ?>
                                </div>
                                <div class="activity-meta mt-2 small">
                                    <span class="me-3" title="Admin">
                                        <i class="fas fa-user-shield me-1"></i>
                                        <?php echo htmlspecialchars($activity['admin_id']); ?>
                                    </span>
                                    <span class="me-3" title="IP Address">
                                        <i class="fas fa-globe me-1"></i>
                                        <?php echo htmlspecialchars($activity['ip_address']); ?>
                                    </span>
                                     <span title="System Info">
                                     <i class="fas fa-desktop me-1"></i>
                                    <?php echo getBrowserInfo($activity['user_agent']); ?>
                                     </span>
                                  
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div id="activity-loader" class="text-center py-3 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Export Controls -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Export Analytics</h5>
                <form method="POST" action="export_analytics.php" class="row g-3">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="col-md-3">
                        <label class="form-label">Export Format</label>
                        <select name="export_type" class="form-select">
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Time Period</label>
                        <select name="date_range" class="form-select">
                            <option value="today">Today</option>
                            <option value="week">Last 7 Days</option>
                            <option value="month">Last 30 Days</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 custom-dates" style="display:none;">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Include Data</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="data_type[]" value="user_activity" checked>
                            <label class="form-check-label">User Activity</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="data_type[]" value="system_performance">
                            <label class="form-check-label">System Performance</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="data_type[]" value="security_logs">
                            <label class="form-check-label">Security Logs</label>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-download me-2"></i>Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- In your dashboard view -->
<?php if (isSuperAdmin()): ?>
    <div class="admin-controls">
        <button class="btn btn-primary" onclick="manageUsers()">Manage Users</button>
        <button class="btn btn-warning" onclick="systemSettings()">System Settings</button>
    </div>
<?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row">
        <?php include '../../includes/dashboard_stats.php'; ?>
    </div>

    <!-- Charts Row -->
    <div class="row mt-4">
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Crime Types Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="crimeTypesChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Monthly Report Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports Table -->
    <?php include '../../includes/dashboard_recent_reports.php'; ?>
</div>

<script>
// Enhanced statistics update function
function updateStatistics() {
    fetch('get_dashboard_stats.php')
        .then(response => response.json())
        .then(data => {
            // Update counts with animation
            animateValue('pendingCount', document.getElementById('pendingCount').textContent, data.pending, 1000);
            animateValue('inProgressCount', document.getElementById('inProgressCount').textContent, data.inProgress, 1000);
            animateValue('resolvedCount', document.getElementById('resolvedCount').textContent, data.resolved, 1000);
            
            // Update response rate with progress bar
            const responseRateElement = document.getElementById('responseRate');
            const progressBar = document.querySelector('.progress-bar');
            const newRate = data.responseRate;
            
            animateValue('responseRate', parseInt(responseRateElement.textContent), newRate, 1000);
            progressBar.style.width = newRate + '%';
            progressBar.setAttribute('aria-valuenow', newRate);
            
            // Update trends
            updateTrends(data.trends);
        });
}

// Value animation function
function animateValue(id, start, end, duration) {
    start = parseInt(start);
    end = parseInt(end);
    const element = document.getElementById(id);
    const range = end - start;
    const startTime = performance.now();
    
    function updateNumber(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const value = Math.floor(start + (range * progress));
        element.textContent = id === 'responseRate' ? value + '%' : value;
        
        if (progress < 1) {
            requestAnimationFrame(updateNumber);
        }
    }
    
    requestAnimationFrame(updateNumber);
}

// Update trend indicators
function updateTrends(trends) {
    const trendElements = {
        pending: document.getElementById('pendingTrend'),
        inProgress: document.getElementById('inProgressTrend'),
        resolved: document.getElementById('resolvedTrend'),
        response: document.getElementById('responseTrend')
    };
    
    Object.keys(trendElements).forEach(key => {
        if (trends[key]) {
            const element = trendElements[key];
            const trend = trends[key];
            const icon = trend > 0 ? 'up' : trend < 0 ? 'down' : 'right';
            const color = trend > 0 ? 'success' : trend < 0 ? 'danger' : 'warning';
            
            element.innerHTML = `
                <i class="fas fa-arrow-${icon}"></i>
                ${Math.abs(trend)}% from last week
            `;
            element.className = `small text-${color} mt-2`;
        }
    });
}

// Initialize tooltips and start updates
document.addEventListener('DOMContentLoaded', function() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
    
    // Start real-time updates
    updateStatistics();
    setInterval(updateStatistics, 30000);
});
</script>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Initialize charts -->
<script src="../../assets/js/dashboard-charts.js"></script>

<script src="../../assets/js/dashboard/activity-filter.js"></script><script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../../assets/js/dashboard/websocket-client.js"></script>
<script src="../../assets/js/dashboard/real-time-updates.js"></script>
<script src="../../assets/js/dashboard/chart-animations.js"></script>
<script src="../../assets/js/dashboard/notifications.js"></script>
<script src="../../assets/js/dashboard/stats-handler.js"></script>
<script src="../../assets/js/dashboard/charts.js"></script>
<script src="../../assets/js/dashboard/statistics.js"></script>
<script src="../../assets/js/dashboard/updates.js"></script>
<script src="../../assets/js/quick-actions.js"></script>

 
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard components
    const charts = new DashboardCharts(crimeTypesData, monthlyTrendsData);
    const statistics = new DashboardStatistics();
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
    
    // Handle date range selector
    document.querySelector('[name="date_range"]').addEventListener('change', function() {
        document.querySelector('.custom-dates').style.display = 
            this.value === 'custom' ? 'block' : 'none';
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dashboard = new DashboardUpdates();
    initializeCharts(crimeTypesData, monthlyTrendsData);
});
</script>

<script>
const dashboardActivity = new ActivityHandler();
const dashboardStats = new DashboardStats();
</script>

<script>
document.querySelector('[name="date_range"]').addEventListener('change', function() {
    const customDates = document.querySelector('.custom-dates');
    customDates.style.display = this.value === 'custom' ? 'block' : 'none';
});
</script>

<script>
    const crimeTypesData = {
        labels: <?php echo json_encode(array_column($crime_types_result->fetch_all(MYSQLI_ASSOC), 'crime_type')); ?>,
        values: <?php echo json_encode(array_column($crime_types_result->fetch_all(MYSQLI_ASSOC), 'count')); ?>
    };

    const monthlyTrendsData = {
        labels: <?php echo json_encode(array_column($monthly_trends_result->fetch_all(MYSQLI_ASSOC), 'month')); ?>,
        values: <?php echo json_encode(array_column($monthly_trends_result->fetch_all(MYSQLI_ASSOC), 'count')); ?>
    };

    initializeCharts(crimeTypesData, monthlyTrendsData);
</script>
<script>
// Ensure quick-actions.js loads after DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const script = document.createElement('script');
    script.src = '../../assets/js/quick-actions.js';
    document.body.appendChild(script);
});
</script>

</body>

<?php include '../../includes/admin_footer.php'; ?>
