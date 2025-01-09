<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';

class ActivityAlerts {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function checkSuspiciousActivity() {
        $query = "SELECT COUNT(*) as attempt_count, ip_address 
                 FROM admin_activity_log 
                 WHERE action = 'login' 
                 AND created_at >= NOW() - INTERVAL 1 HOUR 
                 GROUP BY ip_address 
                 HAVING attempt_count > 5";
        
        return $this->conn->query($query);
    }
    
    public function getRecentFailedLogins() {
        return $this->conn->query("
            SELECT * FROM admin_activity_log 
            WHERE action = 'failed_login' 
            AND created_at >= NOW() - INTERVAL 24 HOUR 
            ORDER BY created_at DESC
        ");
    }
    
    public function createAlert($type, $message) {
        $stmt = $this->conn->prepare("
            INSERT INTO admin_alerts (type, message, created_at) 
            VALUES (?, ?, NOW())
        ");
        $stmt->bind_param("ss", $type, $message);
        return $stmt->execute();
    }
}

$alerts = new ActivityAlerts($conn);
$suspicious = $alerts->checkSuspiciousActivity();
$failedLogins = $alerts->getRecentFailedLogins();

include '../../includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <!-- Alert Summary -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Suspicious Activity</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php while($activity = $suspicious->fetch_assoc()): ?>
                            <div class="list-group-item">
                                <h6 class="mb-1">Multiple Login Attempts</h6>
                                <p class="mb-1">IP Address: <?php echo htmlspecialchars($activity['ip_address']); ?></p>
                                <small>Attempts: <?php echo $activity['attempt_count']; ?></small>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Failed Login Attempts</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php while($login = $failedLogins->fetch_assoc()): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">Failed Login</h6>
                                    <small><?php echo date('H:i:s', strtotime($login['created_at'])); ?></small>
                                </div>
                                <p class="mb-1">IP: <?php echo htmlspecialchars($login['ip_address']); ?></p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Real-time alert checking
function checkAlerts() {
    fetch('check_alerts.php')
        .then(response => response.json())
        .then(data => {
            if (data.alerts.length > 0) {
                data.alerts.forEach(alert => {
                    showNotification(alert.message, alert.type);
                });
            }
        });
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Add to notification area
    document.getElementById('notifications').appendChild(notification);
}

// Check for new alerts every 30 seconds
setInterval(checkAlerts, 30000);
</script>

<?php include '../../includes/admin_footer.php'; ?>
