class ActivityHandler {
    constructor() {
        this.activityFeed = document.querySelector('.activity-feed');
        this.activityCount = document.getElementById('activity-count');
        this.initializeFilters();
        this.startRealtimeUpdates();
    }

    initializeFilters() {
        const filterSelect = document.getElementById('activity-filter');
        filterSelect.addEventListener('change', (e) => {
            this.filterActivities(e.target.value);
        });
    }

    filterActivities(type) {
        const items = this.activityFeed.querySelectorAll('.feed-item');
        items.forEach(item => {
            const shouldShow = type === 'all' || item.dataset.type === type;
            item.style.display = shouldShow ? 'flex' : 'none';
        });
        this.updateCount();
    }

    updateCount() {
        const visibleItems = this.activityFeed.querySelectorAll('.feed-item:not([style*="none"])').length;
        this.activityCount.textContent = visibleItems;
    }

    startRealtimeUpdates() {
        setInterval(() => this.fetchLatestActivities(), 30000);
    }

    async fetchLatestActivities() {
        const response = await fetch('get_latest_activities.php');
        const activities = await response.json();
        this.updateActivityFeed(activities);
    }
}