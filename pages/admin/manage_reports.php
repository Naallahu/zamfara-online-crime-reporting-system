<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/Report.php';

$db = new Database();
$conn = $db->connect();
$report = new Report($conn);

include '../../includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Manage Reports</h2>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-success" onclick="exportReports('excel')">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
            <button class="btn btn-danger" onclick="exportReports('pdf')">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="reportsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Reported</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $reports = $report->getRecentReports(50);
                        while($row = $reports->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['crime_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td>
                                <select class="form-select form-select-sm" onchange="updateStatus(<?php echo $row['id']; ?>, this.value)">
                                    <option value="pending" <?php echo $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="investigating" <?php echo $row['status'] == 'investigating' ? 'selected' : ''; ?>>Investigating</option>
                                    <option value="resolved" <?php echo $row['status'] == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                </select>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="viewReport(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteReport(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>
