<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/Report.php';

$db = new Database();
$conn = $db->connect();
$report = new Report($conn);

// Fetch all advanced analytics data
$statusTrends = $report->getStatusTrends();
$hourlyDistribution = $report->getHourlyDistribution();
$highRiskAreas = $report->getHighRiskAreas();
$responseEfficiency = $report->getResponseEfficiency();

include '../../includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Advanced Analytics Dashboard</h2>
        </div>
    </div>

    <div class="row">
        <!-- Status Trends -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Status Trends Over Time</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusTrendsChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Response Efficiency -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Response Efficiency</h5>
                </div>
                <div class="card-body">
                    <canvas id="efficiencyGauge"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Hourly Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>24-Hour Crime Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="hourlyDistributionChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- High Risk Areas -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>High Risk Areas</h5>
                </div>
                <div class="card-body">
                    <div id="riskAreasMap" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="../../assets/js/analytics.js"></script>
<script>
    // Initialize charts with PHP data
    const analyticsData = {
        statusTrends: <?php echo json_encode($statusTrends->fetch_all(MYSQLI_ASSOC)); ?>,
        hourlyDistribution: <?php echo json_encode($hourlyDistribution->fetch_all(MYSQLI_ASSOC)); ?>,
        highRiskAreas: <?php echo json_encode($highRiskAreas->fetch_all(MYSQLI_ASSOC)); ?>,
        responseEfficiency: <?php echo json_encode($responseEfficiency->fetch_all(MYSQLI_ASSOC)); ?>
    };
    
    initAdvancedAnalytics(analyticsData);
</script>

<?php include '../../includes/admin_footer.php'; ?>
