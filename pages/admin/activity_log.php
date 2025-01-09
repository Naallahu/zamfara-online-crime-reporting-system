<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/ActivityLogger.php';
include '../../includes/admin_header.php';

// Initialize pagination variables
// At the top after your includes
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 20;
$offset = ($page - 1) * $records_per_page;

// Get total records
$total_records_query = "SELECT COUNT(*) as total FROM admin_activity_log";
$result = $conn->query($total_records_query);
$total_records = $result->fetch_assoc()['total'];

// Calculate total pages
$total_pages = ceil($total_records / $records_per_page);

// Get the activities
$logger = new ActivityLogger($conn);
$activities = $logger->getActivityLog($page, $records_per_page);

?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">Activity Log</h2>
            <p class="text-muted">Track all admin activities</p>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-primary" onclick="exportActivityLog()">
                <i class="fas fa-download me-2"></i>Export Log
            </button>
            <button class="btn btn-secondary" onclick="filterActivities()">
                <i class="fas fa-filter me-2"></i>Filter
            </button>
        </div>
    </div>

  <div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="activityTable">
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
                        <?php while($activity = $activities->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php echo date('M d, Y H:i:s', strtotime($activity['created_at'])); ?>
                                </td>

                                <td>
                                    <span class="badge bg-success text-white"> 
                                     <i class="fas fa-user mr-1"></i>
                                    <?php echo htmlspecialchars($activity['name'] ?? $activity['username']); ?>
                                    </span>
                                </td>
                               
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        <?php echo htmlspecialchars($activity['action']); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($activity['details']); ?>
                                    
                                </td>
                                <td> <span class="badge bg-info text-white">
                                    <i class="fas fa-network-wired mr-1"></i>
                                    <?php echo htmlspecialchars($activity['ip_address']); ?>
                                    </span>
                                    
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add pagination here -->
  <div class="card-footer">
    <nav aria-label="Activity log navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=1">&laquo; First</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page-1 ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);

            for ($i = $start_page; $i <= $end_page; $i++) {
                $active = $page == $i ? 'active' : '';
                echo "<li class='page-item {$active}'><a class='page-link' href='?page={$i}'>{$i}</a></li>";
            }
            ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page+1 ?>">Next</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $total_pages ?>">Last &raquo;</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>


<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Activities</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Date Range</label>
                    <input type="date" class="form-control mb-2" id="startDate" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
                    <input type="date" class="form-control" id="endDate" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label>Action Type</label>
                    <select class="form-control" id="actionType">
    <option value="">All Actions</option>
    <option value="login" <?= isset($_GET['action_type']) && $_GET['action_type'] === 'login' ? 'selected' : '' ?>>Login</option>
    <option value="add_user" <?= isset($_GET['action_type']) && $_GET['action_type'] === 'add_user' ? 'selected' : '' ?>>Add User</option>
    <option value="update_user" <?= isset($_GET['action_type']) && $_GET['action_type'] === 'update_user' ? 'selected' : '' ?>>Update User</option>
    <option value="delete_user" <?= isset($_GET['action_type']) && $_GET['action_type'] === 'delete_user' ? 'selected' : '' ?>>Delete User</option>
</select>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="applyFilter()">Apply Filter</button>
            </div>
        </div>
    </div>
</div>
<style>
    .pagination {
        margin-bottom: 0;
    }

    .page-link {
        color: #2c3e50;
        border-color: #dee2e6;
    }

    .page-item.active .page-link {
        background-color: #2c3e50;
        border-color: #2c3e50;
    }

    .page-link:hover {
        color: #1a252f;
        background-color: #e9ecef;
    }
</style>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="../../assets/js/activity-log.js"></script>

<?php include '../../includes/admin_footer.php'; ?>
