<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
include '../includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Track Your Report</h4>
                </div>
                <div class="card-body">
                    <form id="trackForm" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Reference Number</label>
                            <input type="text" class="form-control" name="reference" required 
                                   placeholder="Enter your reference number (e.g., ZCRS-20230815-1234)">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Track Report
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
