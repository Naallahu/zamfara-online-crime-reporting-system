class DashboardCharts {
    constructor(crimeTypesData, monthlyTrendsData) {
        this.initializeCrimeTypesChart(crimeTypesData);
        this.initializeMonthlyTrendsChart(monthlyTrendsData);
    }

    initializeCrimeTypesChart(data) {
        const ctx = document.getElementById('crimeTypesChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    initializeMonthlyTrendsChart(data) {
        const ctx = document.getElementById('monthlyTrendsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Reports',
                    data: data.values,
                    borderColor: '#4e73df',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
}