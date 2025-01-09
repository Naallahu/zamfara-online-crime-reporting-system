const notificationSocket = new WebSocket('ws://localhost:8080');

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
        <div class="notification-item notification-${notification.type}">
            <div class="d-flex">
                <div class="notification-icon bg-${getNotificationColor(notification.type)}">
                    <i class="fas ${getNotificationIcon(notification.type)} text-white"></i>
                </div>
                <div>
                    <div class="small text-gray-500">${notification.created_at}</div>
                    <span>${notification.message}</span>
                </div>
            </div>
        </div>
    `).join('');
}

function getNotificationColor(type) {
    const colors = {
        'report': 'danger',
        'status': 'primary',
        'system': 'success',
        'alert': 'warning'
    };
    return colors[type] || 'primary';
}

function getNotificationIcon(type) {
    const icons = {
        'report': 'fa-file-alt',
        'status': 'fa-sync',
        'system': 'fa-cog',
        'alert': 'fa-exclamation-triangle'
    };
    return icons[type] || 'fa-bell';
}

function markAllAsRead() {
    fetch('../../pages/admin/mark_notifications_read.php', {
        method: 'POST'
    }).then(() => loadNotifications());
}

// Initialize notifications
document.addEventListener('DOMContentLoaded', () => {
    loadNotifications();
    setInterval(loadNotifications, 30000);
});
