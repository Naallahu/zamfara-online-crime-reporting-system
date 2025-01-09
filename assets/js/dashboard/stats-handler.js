class DashboardStats {
    constructor() {
        this.stats = {
            pending: document.getElementById('pendingCount'),
            inProgress: document.getElementById('inProgressCount'),
            resolved: document.getElementById('resolvedCount'),
            responseRate: document.getElementById('responseRate')
        };
        this.initializeCharts();
        this.startRealtimeUpdates();
    }

    initializeCharts() {
        // Crime Types Chart
        const crimeTypesCtx = document.getElementById('crimeTypesChart').getContext('2d');
        this.crimeTypesChart = new Chart(crimeTypesCtx, {
            type: 'doughnut',
            data: crimeTypesData,
            options: {
                responsive: true,
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });

        // Monthly Trends Chart
        const trendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
        this.trendsChart = new Chart(trendsCtx, {
            type: 'line',
            data: monthlyTrendsData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    startRealtimeUpdates() {
        setInterval(() => this.updateStats(), 30000);
    }

    async updateStats() {
        const response = await fetch('get_dashboard_stats.php');
        const data = await response.json();
        this.updateCounters(data);
        this.updateCharts(data);
    }
}