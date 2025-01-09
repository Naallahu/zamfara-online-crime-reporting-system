const socket = new WebSocket('ws://your-server/notifications');

socket.onmessage = function(event) {
    const notification = JSON.parse(event.data);
    updateDashboardCounts(notification);
    showNotificationToast(notification);
};

function updateDashboardCounts(data) {
    animateValue('pendingCount', document.getElementById('pendingCount').textContent, data.pending, 1000);
    animateValue('inProgressCount', document.getElementById('inProgressCount').textContent, data.inProgress, 1000);
    animateValue('resolvedCount', document.getElementById('resolvedCount').textContent, data.resolved, 1000);
    updateResponseRate(data.responseRate);
}