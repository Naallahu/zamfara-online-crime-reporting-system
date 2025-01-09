<?php
$page_title = "Export Activity Log";
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/ActivityLogger.php';

class ActivityExporter {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getPreviewData($start_date = null, $end_date = null, $action_type = null, $admin_id = null, $limit = 25) {
        $query = "SELECT l.created_at, a.username, l.action, l.details, l.ip_address 
                 FROM admin_activity_log l 
                 LEFT JOIN admins a ON l.admin_id = a.id 
                 WHERE 1=1";
        
        $params = array();
        $types = "";
        
        if ($start_date && $end_date) {
            $query .= " AND DATE(l.created_at) BETWEEN ? AND ?";
            $params[] = $start_date;
            $params[] = $end_date;
            $types .= "ss";
        }
        
        if ($action_type) {
            $query .= " AND l.action = ?";
            $params[] = $action_type;
            $types .= "s";
        }
        
        if ($admin_id) {
            $query .= " AND l.admin_id = ?";
            $params[] = $admin_id;
            $types .= "i";
        }
        
        $query .= " ORDER BY l.created_at DESC LIMIT ?";
        $params[] = $limit;
        $types .= "i";
        
        $stmt = $this->conn->prepare($query);
        if (!empty($params)) {
            $bindParams = array();
            $bindParams[] = &$types;
            foreach ($params as $key => $value) {
                $bindParams[] = &$params[$key];
            }
            call_user_func_array(array($stmt, 'bind_param'), $bindParams);
        }
        
        $stmt->execute();
        return $stmt->get_result();
    }


    public function getAdmins() {
        $query = "SELECT id, username FROM admins ORDER BY username";
        return $this->conn->query($query);
    }
    
    public function exportCSV($start_date = null, $end_date = null, $action_type = null, $admin_id = null) {
        $result = $this->getPreviewData($start_date, $end_date, $action_type, $admin_id);
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="activity_log_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, ['Date/Time', 'Admin', 'Action', 'Details', 'IP Address']);
        
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                date('d/m/Y H:i', strtotime($row['created_at'])),
                $row['username'],
                $row['action'],
                $row['details'],
                $row['ip_address']
            ]);
        }
        
        fclose($output);
        exit();
    }
}

// Initialize exporter and get filter values
$exporter = new ActivityExporter($conn);
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;
$action_type = $_GET['action_type'] ?? null;
$admin_id = $_GET['admin_id'] ?? null;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 25;

// Handle export
if (isset($_POST['export'])) {
    $exporter->exportCSV($_POST['start_date'], $_POST['end_date'], $_POST['action_type'], $_POST['admin_id']);
}

// Get data for display
$preview_data = $exporter->getPreviewData($start_date, $end_date, $action_type, $admin_id, $limit);
$admins = $exporter->getAdmins();

require_once '../../includes/admin_header.php';
?>

<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <!-- Filter Form -->
                <div class="card card-primary card-outline elevation-3">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title">
                            <i class="fas fa-file-export mr-2"></i>
                            Export Activity Log
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Filter Form -->
                       <form method="GET" action="" id="filterForm">

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="text-bold">
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            Start Date
                                        </label>
                                        <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="text-bold">
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            End Date
                                        </label>
                                        <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="text-bold">
                                            <i class="fas fa-filter mr-1"></i>
                                            Action Type
                                        </label>
                                        <select class="form-control select2" name="action_type">
                                            <option value="">All Actions</option>
                                            <option value="login" <?= $action_type === 'login' ? 'selected' : '' ?>>Login</option>
                                            <option value="logout" <?= $action_type === 'logout' ? 'selected' : '' ?>>Logout</option>
                                            <option value="create" <?= $action_type === 'create' ? 'selected' : '' ?>>Create</option>
                                            <option value="update" <?= $action_type === 'update' ? 'selected' : '' ?>>Update</option>
                                            <option value="delete" <?= $action_type === 'delete' ? 'selected' : '' ?>>Delete</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="text-bold">
                                            <i class="fas fa-user-shield mr-1"></i>
                                            Admin
                                        </label>
                                        <select class="form-control select2" name="admin_id">
                                            <option value="">All Admins</option>
                                            <?php while($admin = $admins->fetch_assoc()): ?>
                                                <option value="<?= $admin['id'] ?>" <?= $admin_id == $admin['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($admin['username']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                       
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-info btn-block text-white">
                                        <i class="fas fa-filter mr-2"></i>
                                        Apply Filters
                                    </button>
                                </div>

                                <div class="col-md-6">
                                    <button type="submit" formmethod="POST" formaction="export_handler.php" name="export" class="btn btn-primary btn-block">
                                        <i class="fas fa-download mr-2"></i>
                                        Export CSV
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

  <!-- Export Preview -->
                <div class="card mt-4">
    <div class="card-header bg-gradient-info d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <i class="fas fa-table mr-2"></i>
            Preview Data
        </h3>
        <div class="form-inline">
            <label class="mr-2 text-white">Show:</label>
            <select class="form-control form-control-sm" onchange="this.form.submit()" name="limit" form="filterForm">
                <option value="10" <?= ($limit == 10) ? 'selected' : '' ?>>10 records</option>
                <option value="25" <?= ($limit == 25) ? 'selected' : '' ?>>25 records</option>
                <option value="50" <?= ($limit == 50) ? 'selected' : '' ?>>50 records</option>
                <option value="100" <?= ($limit == 100) ? 'selected' : '' ?>>100 records</option>
            </select>
        </div>
    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
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
                               <?php if ($preview_data && $preview_data->num_rows > 0): ?>
        <?php while($row = $preview_data->fetch_assoc()): ?>
            <tr>
                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
        <td>
    <span class="badge bg-success text-white">  <!-- Changed from badge-info -->
        <i class="fas fa-user mr-1"></i>
        <?= htmlspecialchars($row['username']) ?>
    </span>
</td>
<td>
    <span class="badge bg-warning text-dark">  <!-- Changed from badge-primary -->
        <?= htmlspecialchars($row['action']) ?>
    </span>
</td>
<td><?= htmlspecialchars($row['details']) ?></td>
<td>
    <span class="badge bg-info text-white">  <!-- Changed from badge-secondary -->
        <i class="fas fa-network-wired mr-1"></i>
        <?= htmlspecialchars($row['ip_address']) ?>
    </span>
</td>

            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5" class="text-center">No records found</td>
        </tr>
    <?php endif; ?>
</tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/admin_footer.php'; ?>
