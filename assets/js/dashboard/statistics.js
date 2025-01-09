class DashboardStatistics {
    constructor() {
        this.initializeCounters();
        this.startRealTimeUpdates();
    }

    initializeCounters() {
        this.counters = {
            pending: document.getElementById('pendingCount'),
            inProgress: document.getElementById('inProgressCount'),
            resolved: document.getElementById('resolvedCount'),
            responseRate: document.getElementById('responseRate')
        };
    }

    startRealTimeUpdates() {
        this.updateStatistics();
        setInterval(() => this.updateStatistics(), 30000);
    }

    async updateStatistics() {
        const response = await fetch('get_dashboard_stats.php');
        const data = await response.json();
        this.updateCounters(data);
        this.updateTrends(data.trends);
    }

    updateCounters(data) {
        Object.keys(this.counters).forEach(key => {
            if (data[key] !== undefined) {
                this.animateCounter(this.counters[key], data[key]);
            }
        });
    }

    animateCounter(element, newValue) {
        const startValue = parseInt(element.textContent);
        const duration = 1000;
        const start = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            
            const value = Math.floor(startValue + (newValue - startValue) * progress);
            element.textContent = element.id === 'responseRate' ? `${value}%` : value;
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    }
}

