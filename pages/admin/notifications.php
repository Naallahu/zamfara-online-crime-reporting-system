<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
include '../../includes/admin_auth.php';
include '../../includes/admin_header.php';

// Fetch notifications from database
$notifications_query = "SELECT * FROM notifications ORDER BY created_at DESC";
$notifications = $conn->query($notifications_query);
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Notifications</h5>
                    <button class="btn btn-primary btn-sm" onclick="markAllAsRead()">
                        <i class="fas fa-check-double me-2"></i>Mark All as Read
                    </button>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php while($notification = $notifications->fetch_assoc()): ?>
                            <div class="list-group-item list-group-item-action <?php echo !$notification['read_status'] ? 'bg-light' : ''; ?>">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1"><?php echo $notification['title']; ?></h6>
                                    <small class="text-muted">
                                        <?php echo date('M d, Y h:i A', strtotime($notification['created_at'])); ?>
                                    </small>
                                </div>
                                <p class="mb-1"><?php echo $notification['message']; ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">
                                        <i class="fas <?php echo getNotificationIcon($notification['type']); ?> me-2"></i>
                                        <?php echo ucfirst($notification['type']); ?>
                                    </small>
                                    <?php if(!$notification['read_status']): ?>
                                        <button class="btn btn-sm btn-light" 
                                                onclick="markAsRead(<?php echo $notification['id']; ?>)">
                                            <i class="fas fa-check me-1"></i>Mark as Read
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function getNotificationIcon($type) {
    switch($type) {
        case 'report':
            return 'fa-file-alt';
        case 'status':
            return 'fa-info-circle';
        case 'system':
            return 'fa-cog';
        default:
            return 'fa-bell';
    }
}

include '../../includes/admin_footer.php';
?>
