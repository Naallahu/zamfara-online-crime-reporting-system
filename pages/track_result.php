<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
include '../includes/header.php';

$reference = isset($_POST['reference']) ? $_POST['reference'] : '';
$report = null;

if ($reference) {
    $sql = "SELECT * FROM reports WHERE reference_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $reference);
    $stmt->execute();
    $result = $stmt->get_result();
    $report = $result->fetch_assoc();
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php if ($report): ?>
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Report Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5>Reference Number</h5>
                            <p class="lead"><?php echo $report['reference_number']; ?></p>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Status</h5>
                            <span class="badge bg-<?php echo getStatusColor($report['status']); ?> fs-6">
                                <?php echo ucfirst($report['status']); ?>
                            </span>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Crime Type</h5>
                            <p><?php echo ucfirst($report['crime_type']); ?></p>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Location</h5>
                            <p><?php echo $report['location']; ?></p>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Date Reported</h5>
                            <p><?php echo date('F j, Y g:i A', strtotime($report['created_at'])); ?></p>
                        </div>
                        
                        <?php if ($report['status'] !== 'pending'): ?>
                            <div class="mb-4">
                                <h5>Investigation Updates</h5>
                                <div class="timeline">
                                    <!-- Fetch and display updates here -->
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <h4>No Report Found</h4>
                    <p>The reference number you entered was not found in our system. Please check and try again.</p>
                    <a href="track.php" class="btn btn-primary mt-3">Try Again</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
function getStatusColor($status) {
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'investigating':
            return 'info';
        case 'resolved':
            return 'success';
        case 'closed':
            return 'secondary';
        default:
            return 'primary';
    }
}

include '../includes/footer.php';
?>
