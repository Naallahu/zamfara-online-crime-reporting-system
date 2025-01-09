function initializeCharts(crimeData, trendData) {
    new Chart(document.getElementById('crimeTypesChart'), {
        type: 'doughnut',
        data: {
            labels: crimeData.labels,
            datasets: [{
                data: crimeData.values,
                backgroundColor: generateColors(crimeData.labels.length)
            }]
        },
        options: {
            animation: {
                animateScale: true,
                animateRotate: true
            },
            responsive: true
        }
    });
}