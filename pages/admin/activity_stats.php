<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/ActivityLogger.php';

$logger = new ActivityLogger($conn);
$stats = $logger->getActivityStats();

include '../../includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Activity Statistics</h2>
        </div>
    </div>

    <div class="row">
        <!-- Activity Trends Chart -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Activity Trends (Last 30 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="activityTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Action Distribution -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Action Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="actionDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Most Active Users -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Most Active Users</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Actions</th>
                                    <th>Last Active</th>
                                </tr>
                            </thead>
                            <tbody id="activeUsersList"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Map -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Activity Locations</h5>
                </div>
                <div class="card-body">
                    <div id="activityMap" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="../../assets/js/activity-stats.js"></script>

<script>
const activityData = <?php echo json_encode($stats->fetch_all(MYSQLI_ASSOC)); ?>;
initializeActivityStats(activityData);
</script>

<?php include '../../includes/admin_footer.php'; ?>
