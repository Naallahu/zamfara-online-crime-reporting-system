<?php
require_once '../../includes/config.php';
include '../../includes/header.php';

$reference = isset($_GET['ref']) ? $_GET['ref'] : '';
?>

<div class="container my-5">
    <div class="card shadow-lg">
        <div class="card-body text-center py-5">
            <i class="fas fa-check-circle text-success fa-5x mb-3"></i>
            <h2 class="mb-4">Report Submitted Successfully!</h2>
            <p class="lead mb-4">Your reference number is: <strong><?php echo $reference; ?></strong></p>
            <p class="mb-4">Please save this number to track your report status.</p>
            <div class="d-flex justify-content-center gap-3">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Reference
                </button>
                <a href="<?php echo BASE_PATH; ?>pages/track.php" class="btn btn-secondary">
                    <i class="fas fa-search"></i> Track Report
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
