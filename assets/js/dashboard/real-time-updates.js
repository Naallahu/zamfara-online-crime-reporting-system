class DashboardUpdates {
    constructor() {
        this.liveIndicator = document.getElementById('live-indicator');
        this.updateInterval = 30000;
        this.eventSource = null;
        this.initialize();
    }

    initialize() {
        this.initializeEventListeners();
        this.startPulseAnimation();
        this.initializeSSE();
        this.startPeriodicUpdates();
    }

    initializeEventListeners() {
        // Activity filter
        document.getElementById('activity-filter')?.addEventListener('change', (e) => {
            this.filterActivities(e.target.value);
        });

        // Export controls
        document.querySelector('[name="date_range"]')?.addEventListener('change', (e) => {
            this.toggleCustomDates(e.target.value);
        });
    }

    startPulseAnimation() {
        this.liveIndicator?.classList.add('pulse-animation');
    }

    initializeSSE() {
        this.eventSource = new EventSource('dashboard-updates.php');
        
        this.eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.updateDashboard(data);
        };

        this.eventSource.onerror = () => {
            this.handleSSEError();
        };
    }

    startPeriodicUpdates() {
        setInterval(() => {
            this.updateStatistics();
            this.updateActivityFeed();
        }, this.updateInterval);
    }

    async updateStatistics() {
        try {
            const response = await fetch('get_dashboard_stats.php');
            const data = await response.json();
            this.updateDashboardCounts(data);
            this.updateCharts(data.charts);
        } catch (error) {
            console.error('Error updating statistics:', error);
        }
    }

    updateDashboard(data) {
        if (data.stats) this.updateDashboardCounts(data.stats);
        if (data.charts) this.updateCharts(data.charts);
        if (data.activities) this.updateActivityFeed(data.activities);
    }

    updateDashboardCounts(data) {
        Object.keys(data.counts).forEach(key => {
            const element = document.getElementById(`${key}Count`);
            if (element) {
                this.animateValue(element.id, parseInt(element.textContent), data.counts[key], 1000);
            }
        });
    }

    animateValue(elementId, start, end, duration) {
        const element = document.getElementById(elementId);
        const range = end - start;
        const startTime = performance.now();

        const updateNumber = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const value = Math.floor(start + (range * progress));
            
            element.textContent = value.toLocaleString();
            
            if (progress < 1) {
                requestAnimationFrame(updateNumber);
            }
        };
        
        requestAnimationFrame(updateNumber);
    }

    filterActivities(filterValue) {
        const activities = document.querySelectorAll('.activity-item');
        activities.forEach(activity => {
            const shouldShow = filterValue === 'all' || activity.dataset.type === filterValue;
            activity.style.display = shouldShow ? 'flex' : 'none';
        });
    }

    toggleCustomDates(value) {
        const customDates = document.querySelector('.custom-dates');
        if (customDates) {
            customDates.style.display = value === 'custom' ? 'block' : 'none';
        }
    }

    handleSSEError() {
        this.liveIndicator?.classList.remove('pulse-animation');
        this.liveIndicator?.classList.add('error');
        setTimeout(() => this.initializeSSE(), 5000);
    }
}

// Initialize dashboard updates
document.addEventListener('DOMContentLoaded', () => {
    const dashboardUpdates = new DashboardUpdates();
});
