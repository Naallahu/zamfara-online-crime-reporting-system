// Mark single notification as read
function markAsRead(notificationId) {
    fetch('process_notification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'mark_read',
            notification_id: notificationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            document.querySelector(`[data-notification="${notificationId}"]`)
                .classList.remove('bg-light');
            updateNotificationCount();
        }
    });
}

// Mark all notifications as read
function markAllAsRead() {
    fetch('process_notification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'mark_all_read'
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            document.querySelectorAll('.bg-light')
                .forEach(el => el.classList.remove('bg-light'));
            updateNotificationCount();
        }
    });
}

// Update notification count in header
function updateNotificationCount() {
    fetch('get_notification_count.php')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('#notificationBadge');
            if(data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'inline';
            } else {
                badge.style.display = 'none';
            }
        });
}

// Real-time notifications using WebSocket
const ws = new WebSocket('ws://your-websocket-server');
ws.onmessage = function(event) {
    const notification = JSON.parse(event.data);
    addNewNotification(notification);
    updateNotificationCount();
};

// Add new notification to the list
function addNewNotification(notification) {
    const template = `
        <div class="list-group-item list-group-item-action bg-light" data-notification="${notification.id}">
            <div class="d-flex w-100 justify-content-between align-items-center">
                <h6 class="mb-1">${notification.title}</h6>
                <small class="text-muted">Just now</small>
            </div>
            <p class="mb-1">${notification.message}</p>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <small class="text-muted">
                    <i class="fas ${getNotificationIcon(notification.type)} me-2"></i>
                    ${notification.type}
                </small>
                <button class="btn btn-sm btn-light" onclick="markAsRead(${notification.id})">
                    <i class="fas fa-check me-1"></i>Mark as Read
                </button>
            </div>
        </div>
    `;
    
    document.querySelector('.list-group').insertAdjacentHTML('afterbegin', template);
}

// Initialize WebSocket connection
const socket = new WebSocket('ws://localhost:8080');

// Handle incoming notifications
socket.onmessage = function(event) {
    const notification = JSON.parse(event.data);
    showNotification(notification);
    updateNotificationBadge();
    addNotificationToList(notification);
};

// Display notification toast
function showNotification(data) {
    const toast = `
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div class="toast" role="alert">
                <div class="toast-header bg-primary text-white">
                    <strong class="me-auto">Activity Alert</strong>
                    <small>${formatTimestamp(data.timestamp)}</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${data.details}
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', toast);
    const toastElement = document.querySelector('.toast:last-child');
    new bootstrap.Toast(toastElement).show();
}

// Update notification counter
function updateNotificationBadge() {
    fetch('get_unread_notifications.php')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notificationBadge');
            badge.textContent = data.count;
            badge.style.display = data.count > 0 ? 'block' : 'none';
        });
}

// Add notification to dropdown list
function addNotificationToList(notification) {
    const list = document.getElementById('notificationsList');
    const item = `
        <a class="dropdown-item d-flex align-items-center" href="#">
            <div class="me-3">
                <div class="icon-circle bg-primary">
                    <i class="fas fa-bell text-white"></i>
                </div>
            </div>
            <div>
                <div class="small text-gray-500">${formatTimestamp(notification.timestamp)}</div>
                <span>${notification.details}</span>
            </div>
        </a>
    `;
    list.insertAdjacentHTML('afterbegin', item);
}

// Format timestamp
function formatTimestamp(timestamp) {
    return new Date(timestamp).toLocaleString();
}

// Mark notifications as read
function markAsRead() {
    fetch('mark_notifications_read.php', {
        method: 'POST'
    })
    .then(() => {
        updateNotificationBadge();
    });
}

// Initialize notifications on page load
document.addEventListener('DOMContentLoaded', () => {
    updateNotificationBadge();
});
