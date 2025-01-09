<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/ActivityLogger.php';

include '../../includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Real-time Activity Monitor</h5>
                </div>
                <div class="card-body">
                    <div id="activity-feed" class="list-group"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateActivityFeed() {
    fetch('get_latest_activity.php')
        .then(response => response.json())
        .then(activities => {
            const feed = document.getElementById('activity-feed');
            activities.forEach(activity => {
                const item = `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-1">${activity.action}</h6>
                            <small>${activity.time}</small>
                        </div>
                        <p class="mb-1">${activity.details}</p>
                        <small>By: ${activity.admin_name}</small>
                    </div>
                `;
                feed.insertAdjacentHTML('afterbegin', item);
            });
        });
}

// Update every 10 seconds
setInterval(updateActivityFeed, 10000);
updateActivityFeed();
</script>

<?php include '../../includes/admin_footer.php'; ?>
