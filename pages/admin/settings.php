<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/ActivityLogger.php';

$logger = new ActivityLogger($conn);

// Fetch current settings
$query = "SELECT * FROM settings WHERE id = 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$settings = $result->fetch_assoc();

// Fetch analytics data
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$activeUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE status = 'active'"))['total'];

// Get system uptime (if on Linux)
$systemUptime = shell_exec('uptime -p');
$lastRestart = date('Y-m-d H:i:s', strtotime(shell_exec('uptime -s')));

// Database statistics
$dbStats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
    SUM(data_length + index_length) as size,
    COUNT(*) as tables 
    FROM information_schema.tables 
    WHERE table_schema = '" . DB_NAME . "'"));
$dbSize = $dbStats['size'];
$totalTables = $dbStats['tables'];

// Activity statistics
$todayActions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM admin_activity_log WHERE DATE(created_at) = CURDATE()"))['total'];
$totalLogs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM admin_activity_log"))['total'];

// Chart data (last 7 days)
$chartQuery = "SELECT DATE(created_at) as date, COUNT(*) as count 
               FROM admin_activity_log 
               WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
               GROUP BY DATE(created_at)
               ORDER BY date";
$chartResult = mysqli_query($conn, $chartQuery);
$chartLabels = [];
$chartData = [];
while($row = mysqli_fetch_assoc($chartResult)) {
    $chartLabels[] = date('M d', strtotime($row['date']));
    $chartData[] = $row['count'];
}

// Helper function for formatting bytes
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
}

include '../../includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <h2 class="mb-4">System Settings</h2>

    <div class="row">
        <!-- General Settings -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">General Settings</h5>
                </div>
                <div class="card-body">
                    <form id="settingsForm" action="process_settings.php" method="POST">
                        <div class="mb-3">
                            <label>System Name</label>
                            <input type="text" name="system_name" class="form-control" value="<?php echo htmlspecialchars($settings['system_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Admin Email</label>
                            <input type="email" name="admin_email" class="form-control" value="<?php echo htmlspecialchars($settings['admin_email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Timezone</label>
                            <select name="timezone" class="form-control">
                                <?php foreach(timezone_identifiers_list() as $tz): ?>
                                    <option value="<?php echo $tz; ?>" <?php echo $settings['timezone'] == $tz ? 'selected' : ''; ?>><?php echo $tz; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Email Settings -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Email Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label>SMTP Host</label>
                        <input type="text" name="smtp_host" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_host'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label>SMTP Port</label>
                        <input type="text" name="smtp_port" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_port'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label>SMTP Username</label>
                        <input type="text" name="smtp_username" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_username'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label>SMTP Password</label>
                        <input type="password" name="smtp_password" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <!-- Theme Settings -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Theme Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label>System Theme</label>
                        <select name="system_theme" class="form-control">
                            <option value="light" <?php echo ($settings['system_theme'] ?? '') == 'light' ? 'selected' : ''; ?>>Light Mode</option>
                            <option value="dark" <?php echo ($settings['system_theme'] ?? '') == 'dark' ? 'selected' : ''; ?>>Dark Mode</option>
                            <option value="custom" <?php echo ($settings['system_theme'] ?? '') == 'custom' ? 'selected' : ''; ?>>Custom Theme</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Security Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="two_factor" class="form-check-input" <?php echo ($settings['two_factor'] ?? 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label">Enable Two-Factor Authentication</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="force_password_change" class="form-check-input" <?php echo ($settings['force_password_change'] ?? 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label">Force Password Change Every 90 Days</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Notification Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="email_notifications" class="form-check-input" <?php echo ($settings['email_notifications'] ?? 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label">Email Notifications</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="system_notifications" class="form-check-input" <?php echo ($settings['system_notifications'] ?? 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label">System Notifications</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="row">
        <div class="col-12">
            <button type="submit" form="settingsForm" class="btn btn-primary">
                <i class="fas fa-save"></i> Save All Settings
            </button>
        </div>
    </div>
</div>

<!-- System Analytics Dashboard -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">System Analytics Dashboard</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- User Statistics -->
                    <div class="col-md-3">
                        <div class="stats-card bg-primary text-white p-3 rounded">
                            <h6>Total Users</h6>
                            <h3><?php echo number_format($totalUsers); ?></h3>
                            <p class="mb-0">Active: <?php echo number_format($activeUsers); ?></p>
                        </div>
                    </div>

                    <!-- System Performance -->
                    <div class="col-md-3">
                        <div class="stats-card bg-success text-white p-3 rounded">
                            <h6>System Uptime</h6>
                            <h3><?php echo $systemUptime; ?></h3>
                            <p class="mb-0">Last Restart: <?php echo $lastRestart; ?></p>
                        </div>
                    </div>

                    <!-- Database Stats -->
                    <div class="col-md-3">
                        <div class="stats-card bg-info text-white p-3 rounded">
                            <h6>Database Size</h6>
                            <h3><?php echo formatBytes($dbSize); ?></h3>
                            <p class="mb-0">Tables: <?php echo $totalTables; ?></p>
                        </div>
                    </div>

                    <!-- Activity Overview -->
                    <div class="col-md-3">
                        <div class="stats-card bg-warning text-white p-3 rounded">
                            <h6>Today's Activity</h6>
                            <h3><?php echo number_format($todayActions); ?></h3>
                            <p class="mb-0">Logs: <?php echo number_format($totalLogs); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Real-time User Sessions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Real-time User Sessions</h5>
                                <span class="badge bg-success" id="online-counter">Updating...</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="active-sessions">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Role</th>
                                                <th>Last Activity</th>
                                                <th>IP Address</th>
                                                <th>Duration</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sessions-body">
                                            <!-- Data will be populated dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

<!-- Add this section after your Real-time User Sessions section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">System Resources Monitor</h5>
                <span class="badge bg-info" id="refresh-time">Last Updated: Just now</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- CPU Usage -->
                    <div class="col-md-4">
                        <div class="resource-card">
                            <h6>CPU Usage</h6>
                            <div class="progress mb-2">
                                <div class="progress-bar" id="cpu-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <span id="cpu-text">Loading...</span>
                        </div>
                    </div>

                    <!-- Memory Usage -->
                    <div class="col-md-4">
                        <div class="resource-card">
                            <h6>Memory Usage</h6>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-success" id="memory-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <span id="memory-text">Loading...</span>
                        </div>
                    </div>

                    <!-- Disk Usage -->
                    <div class="col-md-4">
                        <div class="resource-card">
                            <h6>Disk Usage</h6>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-warning" id="disk-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <span id="disk-text">Loading...</span>
                        </div>
                    </div>
                </div>

                <!-- Resource History Graph -->
                <div class="row mt-4">
                    <div class="col-12">
                        <canvas id="resourceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                <!-- Activity Graph -->
                <div class="row mt-4">
                    <div class="col-12">
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add this section after your System Resources Monitor -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Analytics Export</h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportAnalytics('pdf')">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="exportAnalytics('excel')">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info" onclick="exportAnalytics('csv')">
                        <i class="fas fa-file-csv"></i> CSV
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="export-options">
                    <form id="exportForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date Range</label>
                                    <select class="form-control" name="date_range">
                                        <option value="today">Today</option>
                                        <option value="week">Last 7 Days</option>
                                        <option value="month">Last 30 Days</option>
                                        <option value="custom">Custom Range</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6" id="customDateRange" style="display:none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="date" name="start_date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input type="date" name="end_date" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Data Type</label>
                                    <select class="form-control" name="data_type" multiple>
                                        <option value="user_activity">User Activity</option>
                                        <option value="system_performance">System Performance</option>
                                        <option value="security_logs">Security Logs</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function updateActiveSessions() {
    fetch('get_active_sessions.php')
        .then(response => response.json())
        .then(data => {
            const sessionsBody = document.getElementById('sessions-body');
            const onlineCounter = document.getElementById('online-counter');
            
            sessionsBody.innerHTML = '';
            onlineCounter.textContent = `${data.length} Active Users`;

            data.forEach(session => {
                const row = `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    ${session.user_name.charAt(0)}
                                </div>
                                ${session.user_name}
                            </div>
                        </td>
                        <td><span class="badge bg-info">${session.role}</span></td>
                        <td>${session.last_activity}</td>
                        <td>${session.ip_address}</td>
                        <td>${session.duration}</td>
                        <td>
                            <span class="badge bg-${session.is_active ? 'success' : 'warning'}">
                                ${session.is_active ? 'Active' : 'Idle'}
                            </span>
                        </td>
                    </tr>
                `;
                sessionsBody.innerHTML += row;
            });
        });
}

// Update every 30 seconds
setInterval(updateActiveSessions, 30000);
// Initial update
updateActiveSessions();

const ctx = document.getElementById('activityChart').getContext('2d');
const activityChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chartLabels); ?>,
        datasets: [{
            label: 'System Activity',
            data: <?php echo json_encode($chartData); ?>,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// System Resources Monitoring
function updateSystemResources() {
    fetch('get_system_resources.php')
        .then(response => response.json())
        .then(data => {
            // Update CPU
            document.getElementById('cpu-bar').style.width = data.cpu + '%';
            document.getElementById('cpu-text').textContent = `CPU Usage: ${data.cpu}%`;
            
            // Update Memory
            document.getElementById('memory-bar').style.width = data.memory.used + '%';
            document.getElementById('memory-text').textContent = 
                `Memory: ${data.memory.used}% (${data.memory.free}GB free of ${data.memory.total}GB)`;
            
            // Update Disk
            document.getElementById('disk-bar').style.width = data.disk.used + '%';
            document.getElementById('disk-text').textContent = 
                `Disk: ${data.disk.used}% (${data.disk.free}GB free of ${data.disk.total}GB)`;
            
            // Update refresh time
            document.getElementById('refresh-time').textContent = 
                `Last Updated: ${data.timestamp}`;
        });
}

// Update every 5 seconds
setInterval(updateSystemResources, 5000);
// Initial update
updateSystemResources();

// Analytics Export
document.querySelector('select[name="date_range"]').addEventListener('change', function() {
    const customRange = document.getElementById('customDateRange');
    if(this.value === 'custom') {
        customRange.style.display = 'block';
    } else {
        customRange.style.display = 'none';
    }
});

function exportAnalytics(type) {
    const formData = new FormData(document.getElementById('exportForm'));
    formData.append('export_type', type);
    
    // Set the correct file extension based on export type
    const fileExtensions = {
        'excel': 'xlsx',
        'pdf': 'pdf',
        'csv': 'csv'
    };
    
    fetch('export_analytics.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `analytics_report.${fileExtensions[type]}`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
    });
}


</script>


<script>
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('process_settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            toastr.success('Settings updated successfully');
            setTimeout(() => location.reload(), 1500);
        } else {
            toastr.error(data.message || 'Error updating settings');
        }
    });
});
</script>

<?php include '../../includes/admin_footer.php'; ?>
