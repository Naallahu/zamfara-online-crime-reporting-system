// Initialize all charts
function initAnalytics(crimeData, lgaData, trendsData) {
    // Crime Types Chart
    const crimeCtx = document.getElementById('crimeTypesChart').getContext('2d');
    new Chart(crimeCtx, {
        type: 'pie',
        data: {
            labels: crimeData.labels,
            datasets: [{
                data: crimeData.values,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc',
                    '#f6c23e', '#e74a3b', '#858796'
                ]
            }]
        }
    });

    // LGA Distribution Chart
    const lgaCtx = document.getElementById('lgaChart').getContext('2d');
    new Chart(lgaCtx, {
        type: 'bar',
        data: {
            labels: lgaData.labels,
            datasets: [{
                label: 'Reports by LGA',
                data: lgaData.values,
                backgroundColor: '#4e73df'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Monthly Trends Chart
    const trendsCtx = document.getElementById('trendsChart').getContext('2d');
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: trendsData.labels,
            datasets: [{
                label: 'Number of Reports',
                data: trendsData.values,
                borderColor: '#4e73df',
                tension: 0.3
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Print Analytics Report
function printAnalytics() {
    window.print();
}

// Export Analytics Data
function exportAnalytics(format) {
    window.location.href = `export_analytics.php?format=${format}`;
}

// Advanced Analytics Charts
function initAdvancedAnalytics(statusData, hourlyData, riskData, efficiencyData) {
    // Status Trends Chart
    const statusCtx = document.getElementById('statusTrendsChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'stacked-bar',
        data: {
            labels: statusData.labels,
            datasets: statusData.datasets
        },
        options: {
            scales: {
                y: {
                    stacked: true,
                    beginAtZero: true
                },
                x: {
                    stacked: true
                }
            }
        }
    });

    // Hourly Distribution Heat Map
    const hourlyCtx = document.getElementById('hourlyDistributionChart').getContext('2d');
    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: hourlyData.labels,
            datasets: [{
                label: 'Reports by Hour',
                data: hourlyData.values,
                backgroundColor: hourlyData.colors
            }]
        }
    });

    // High Risk Areas Map
    initRiskMap(riskData);

    // Response Efficiency Gauge
    const efficiencyCtx = document.getElementById('efficiencyGauge').getContext('2d');
    new Chart(efficiencyCtx, {
        type: 'doughnut',
        data: {
            labels: efficiencyData.labels,
            datasets: [{
                data: efficiencyData.values,
                backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b']
            }]
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Initialize Risk Areas Map
function initRiskMap(riskData) {
    const map = L.map('riskAreasMap').setView([12.1667, 6.2167], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    riskData.forEach(area => {
        L.circle([area.lat, area.lng], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: area.incident_count * 100
        }).addTo(map);
    });
}
