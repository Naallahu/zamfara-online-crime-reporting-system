<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/Report.php';

$db = new Database();
$conn = $db->connect();
$report = new Report($conn);

// Get analytics data
$crimeStats = $report->getCrimeTypeStats();
$lgaStats = $report->getLGAStats();
$monthlyTrends = $report->getMonthlyTrends();
$responseStats = $report->getResponseTimeStats();

include '../../includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Report Analytics</h2>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-primary" onclick="printAnalytics()">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Crime Types Chart -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Crime Types Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="crimeTypesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- LGA Distribution Chart -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Reports by LGA</h5>
                </div>
                <div class="card-body">
                    <canvas id="lgaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Trends -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Monthly Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Response Time Stats -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Response Time Analysis</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <td>Average Response Time</td>
                                <td><?php echo $responseStats['avg_time']; ?> hours</td>
                            </tr>
                            <tr>
                                <td>Fastest Response</td>
                                <td><?php echo $responseStats['min_time']; ?> hours</td>
                            </tr>
                            <tr>
                                <td>Slowest Response</td>
                                <td><?php echo $responseStats['max_time']; ?> hours</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../../assets/js/analytics.js"></script>

<?php include '../../includes/admin_footer.php'; ?>
