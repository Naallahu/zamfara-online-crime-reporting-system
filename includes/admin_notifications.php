<li class="nav-item dropdown no-arrow mx-1">
    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown">
        <i class="fas fa-bell fa-fw"></i>
        <span class="badge badge-danger badge-counter" id="notificationBadge"></span>
    </a>
    
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
        <h6 class="dropdown-header">
            Notifications Center
        </h6>
        <div id="notificationsList">
            <!-- Notifications will be dynamically inserted here -->
        </div>
        <a class="dropdown-item text-center small text-gray-500" href="#" onclick="markAsRead()">
            Mark All as Read
        </a>
    </div>
</li>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    setInterval(loadNotifications, 30000); // Refresh every 30 seconds
});

function loadNotifications() {
    fetch('../../pages/admin/get_notifications.php')
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.unreadCount);
            updateNotificationsList(data.notifications);
        });
}

function updateNotificationBadge(count) {
    const badge = document.getElementById('notificationBadge');
    badge.textContent = count;
    badge.style.display = count > 0 ? 'block' : 'none';
}

function updateNotificationsList(notifications) {
    const list = document.getElementById('notificationsList');
    list.innerHTML = notifications.map(notification => `
        <a class="dropdown-item d-flex align-items-center" href="#">
            <div class="mr-3">
                <div class="icon-circle bg-primary">
                    <i class="fas fa-file-alt text-white"></i>
                </div>
            </div>
            <div>
                <div class="small text-gray-500">${notification.created_at}</div>
                <span class="font-weight-bold">${notification.title}</span>
                <div class="small text-gray-500">${notification.message}</div>
            </div>
        </a>
    `).join('');
}
</script>
