<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/ActivityLogger.php';

class ActivityAnalytics {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getActionStats() {
        return $this->conn->query("
            SELECT action, COUNT(*) as count 
            FROM admin_activity_log 
            GROUP BY action 
            ORDER BY count DESC
        ");
    }
    
    public function getAdminStats() {
        return $this->conn->query("
            SELECT a.username, a.name, COUNT(l.id) as activity_count 
            FROM admins a 
            LEFT JOIN admin_activity_log l ON a.id = l.admin_id 
            GROUP BY a.id 
            ORDER BY activity_count DESC
        ");
    }
    
    public function getHourlyActivity() {
        return $this->conn->query("
            SELECT HOUR(created_at) as hour, COUNT(*) as count 
            FROM admin_activity_log 
            GROUP BY HOUR(created_at) 
            ORDER BY hour
        ");
    }
}

$analytics = new ActivityAnalytics($conn);
$actionStats = $analytics->getActionStats();
$adminStats = $analytics->getAdminStats();
$hourlyActivity = $analytics->getHourlyActivity();

include '../../includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Action Type Distribution -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Activity Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="actionChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Admin Activity Levels -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Admin Activity Levels</h5>
                </div>
                <div class="card-body">
                    <canvas id="adminChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize charts with data from PHP
const actionData = {
    labels: <?php echo json_encode(array_column($actionStats->fetch_all(MYSQLI_ASSOC), 'action')); ?>,
    values: <?php echo json_encode(array_column($actionStats->fetch_all(MYSQLI_ASSOC), 'count')); ?>
};

const adminData = {
    labels: <?php echo json_encode(array_column($adminStats->fetch_all(MYSQLI_ASSOC), 'name')); ?>,
    values: <?php echo json_encode(array_column($adminStats->fetch_all(MYSQLI_ASSOC), 'activity_count')); ?>
};

// Create charts
new Chart(document.getElementById('actionChart'), {
    type: 'pie',
    data: {
        labels: actionData.labels,
        datasets: [{
            data: actionData.values,
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
        }]
    }
});

new Chart(document.getElementById('adminChart'), {
    type: 'bar',
    data: {
        labels: adminData.labels,
        datasets: [{
            label: 'Activity Count',
            data: adminData.values,
            backgroundColor: '#4e73df'
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include '../../includes/admin_footer.php'; ?>
