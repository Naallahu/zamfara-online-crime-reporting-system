<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/ActivityLogger.php';

class ActivitySearch {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function searchActivities($filters = []) {
        $conditions = [];
        $params = [];
        $types = '';
        
        if (!empty($filters['action'])) {
            $conditions[] = "l.action = ?";
            $params[] = $filters['action'];
            $types .= 's';
        }
        
        if (!empty($filters['admin'])) {
            $conditions[] = "a.username LIKE ?";
            $params[] = "%{$filters['admin']}%";
            $types .= 's';
        }
        
        if (!empty($filters['date_from'])) {
            $conditions[] = "l.created_at >= ?";
            $params[] = $filters['date_from'];
            $types .= 's';
        }
        
        if (!empty($filters['date_to'])) {
            $conditions[] = "l.created_at <= ?";
            $params[] = $filters['date_to'];
            $types .= 's';
        }
        
        $query = "SELECT l.*, a.username, a.name 
                 FROM admin_activity_log l 
                 LEFT JOIN admins a ON l.admin_id = a.id";
                 
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $query .= " ORDER BY l.created_at DESC LIMIT 100";
        
        $stmt = $this->conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }
    
    public function getActionTypes() {
        return $this->conn->query("SELECT DISTINCT action FROM admin_activity_log ORDER BY action");
    }
}

$searcher = new ActivitySearch($conn);
$actionTypes = $searcher->getActionTypes();

// Handle search
$filters = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filters = [
        'action' => $_POST['action'] ?? '',
        'admin' => $_POST['admin'] ?? '',
        'date_from' => $_POST['date_from'] ?? '',
        'date_to' => $_POST['date_to'] ?? ''
    ];
}

$results = $searcher->searchActivities($filters);

include '../../includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <!-- Search Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Search Activity Log</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label>Action Type</label>
                            <select name="action" class="form-select">
                                <option value="">All Actions</option>
                                <?php while($action = $actionTypes->fetch_assoc()): ?>
                                    <option value="<?php echo $action['action']; ?>">
                                        <?php echo ucfirst($action['action']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label>Admin Username</label>
                            <input type="text" name="admin" class="form-control" placeholder="Search by admin">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-control">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>Admin</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($activity = $results->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($activity['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($activity['name'] ?? $activity['username']); ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo htmlspecialchars($activity['action']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($activity['details']); ?></td>
                                <td><?php echo htmlspecialchars($activity['ip_address']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>
