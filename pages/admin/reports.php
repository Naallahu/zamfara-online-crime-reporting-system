<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
include '../../includes/admin_auth.php';
include '../../includes/admin_header.php';

// Fetch all reports with pagination
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Add search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = '';
if ($search) {
    $where = " WHERE reference_number LIKE ? OR crime_type LIKE ? OR location LIKE ?";
}

$sql = "SELECT * FROM reports" . $where . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

if ($search) {
    $searchParam = "%$search%";
    $stmt->bind_param('sssii', $searchParam, $searchParam, $searchParam, $limit, $offset);
} else {
    $stmt->bind_param('ii', $limit, $offset);
}

$stmt->execute();
$reports = $stmt->get_result();
?>

<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Reports</h5>
            <div class="d-flex gap-2">
                <!-- Search Form -->
                <form class="d-flex me-2">
                    <input type="search" name="search" class="form-control" 
                           placeholder="Search reports..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary ms-2">Search</button>
                </form>
                <button class="btn btn-success" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i> Export
                </button>
                <button class="btn btn-danger" onclick="generatePDF()">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Crime Type</th>
                            <th>Location</th>
                            <th>Date Reported</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($report = $reports->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $report['reference_number']; ?></td>
                            <td><?php echo ucfirst($report['crime_type']); ?></td>
                            <td><?php echo $report['location']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($report['created_at'])); ?></td>
                            <td>
                                <select class="form-select form-select-sm status-select" 
                                        onchange="updateStatus(<?php echo $report['id']; ?>, this.value)">
                                    <option value="pending" <?php echo $report['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="investigating" <?php echo $report['status'] == 'investigating' ? 'selected' : ''; ?>>Investigating</option>
                                    <option value="resolved" <?php echo $report['status'] == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                    <option value="closed" <?php echo $report['status'] == 'closed' ? 'selected' : ''; ?>>Closed</option>
                                </select>
                            </td>
                            <td>
                                <a href="view_report.php?id=<?php echo $report['id']; ?>" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
function updateStatus(reportId) {
    const statuses = ['pending', 'investigating', 'resolved', 'closed'];
    const currentStatus = event.target.closest('tr').querySelector('.badge').textContent.toLowerCase();
    const currentIndex = statuses.indexOf(currentStatus);
    const newStatus = statuses[(currentIndex + 1) % statuses.length];
    
    fetch('update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            report_id: reportId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        }
    });
}

// Your existing JavaScript functions

function exportExcel() {
    window.location.href = 'export_reports_excel.php';
}

function exportPDF() {
    window.location.href = 'export_reports_pdf.php';
}
</script>




<?php include '../../includes/admin_footer.php'; ?>
