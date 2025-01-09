class DashboardUpdates {
    constructor() {
        this.initializeRealTimeUpdates();
    }

    initializeRealTimeUpdates() {
        setInterval(() => {
            this.updateStatistics();
            this.updateActivityFeed();
        }, 30000);
    }

    async updateStatistics() {
        const response = await fetch('get_dashboard_stats.php');
        const data = await response.json();
        this.updateDashboardUI(data);
    }

    updateDashboardUI(data) {
        // Update statistics cards
        document.getElementById('pendingCount').textContent = data.pending;
        document.getElementById('inProgressCount').textContent = data.in_progress;
        document.getElementById('resolvedCount').textContent = data.resolved;
        
        // Update response rate
        const responseRate = ((data.resolved / data.total) * 100).toFixed(1);
        document.getElementById('responseRate').textContent = `${responseRate}%`;
    }
}