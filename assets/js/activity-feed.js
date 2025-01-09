// Activity Feed Functionality
document.addEventListener('DOMContentLoaded', function() {
    const activityFeed = document.getElementById('activity-feed');
    const activityFilter = document.getElementById('activity-filter');
    const activityCount = document.getElementById('activity-count');
    
    // Update activity count
    function updateCount() {
        const visibleItems = activityFeed.querySelectorAll('.feed-item:not(.d-none)').length;
        activityCount.textContent = visibleItems;
    }
    
    // Filter activities
    activityFilter.addEventListener('change', function() {
        const selectedType = this.value;
        const items = activityFeed.querySelectorAll('.feed-item');
        
        items.forEach(item => {
            if (selectedType === 'all' || item.dataset.activityType === selectedType) {
                item.classList.remove('d-none');
            } else {
                item.classList.add('d-none');
            }
        });
        
        updateCount();
    });
    
    // Initialize count
    updateCount();
    
    // Real-time updates
    setInterval(fetchNewActivities, 30000);
});

function viewDetails(activityId) {
    // Implement activity details view
    const modal = new bootstrap.Modal(document.getElementById('activityModal'));
    // Fetch and display activity details
    modal.show();
}

function exportActivity(activityId) {
    // Implement activity export
    window.location.href = `export_activity.php?id=${activityId}`;
}

function getBrowserInfo(userAgent) {
    // Implement browser detection
    return userAgent.split(' ')[0];
}

function timeAgo(datetime) {
    // Implement relative time calculation
    const now = new Date();
    const past = new Date(datetime);
    const diff = Math.floor((now - past) / 1000);
    
    if (diff < 60) return 'just now';
    if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    return Math.floor(diff / 86400) + 'd ago';
}