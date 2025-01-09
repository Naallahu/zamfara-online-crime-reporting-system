// Advanced Activity Feed Filter
class ActivityFeedManager {
    constructor() {
        this.filterSelect = document.getElementById('activity-filter');
        this.activityFeed = document.getElementById('activity-feed');
        this.activityCount = document.getElementById('activity-count');
        this.initializeFilters();
    }

    initializeFilters() {
        this.filterSelect.addEventListener('change', (e) => {
            const selectedFilter = e.target.value;
            this.filterActivities(selectedFilter);
            this.updateActivityCount(selectedFilter);
        });
    }

    filterActivities(filterValue) {
        const activities = this.activityFeed.querySelectorAll('.feed-item');
        activities.forEach(item => {
            const type = item.dataset.activityType;
            const shouldShow = filterValue === 'all' || type === filterValue;
            item.style.display = shouldShow ? 'flex' : 'none';
            item.classList.toggle('animate__fadeIn', shouldShow);
        });
    }

    updateActivityCount(filterValue) {
        const visibleActivities = this.activityFeed.querySelectorAll(
            filterValue === 'all' 
            ? '.feed-item' 
            : `.feed-item[data-activity-type="${filterValue}"]`
        ).length;
        this.activityCount.textContent = visibleActivities;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    new ActivityFeedManager();
});