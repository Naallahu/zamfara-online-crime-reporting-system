class DashboardCharts {
    constructor() {
        this.initializeCrimeChart();
        this.initializeTrendsChart();
    }

    initializeCrimeChart() {
        const ctx = document.getElementById('crimeTypesChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: crimeTypesData.labels,
                datasets: [{
                    data: crimeTypesData.values,
                    backgroundColor: this.generateColors(crimeTypesData.labels.length),
                    borderWidth: 2
                }]
            },
            options: {
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 2000,
                    easing: 'easeInOutQuart'
                },
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }

    initializeTrendsChart() {
        const ctx = document.getElementById('monthlyTrendsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyTrendsData.labels,
                datasets: [{
                    label: 'Monthly Reports',
                    data: monthlyTrendsData.values,
                    borderColor: '#4e73df',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    generateColors(count) {
        const colors = [];
        for (let i = 0; i < count; i++) {
            colors.push(`hsl(${(i * 360) / count}, 70%, 60%)`);
        }
        return colors;
    }
}

class ChartAnimations {
    static applyAnimations(chart) {
        const animations = {
            doughnut: {
                animateScale: true,
                animateRotate: true,
                duration: 2000,
                easing: 'easeInOutQuart'
            },
            line: {
                tension: {
                    duration: 2000,
                    easing: 'easeInOutQuart',
                    from: 0,
                    to: 0.4
                },
                scale: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                }
            }
        };

        return chart.type === 'doughnut' ? animations.doughnut : animations.line;
    }

    static addHoverEffects(chart) {
        chart.options.hover = {
            mode: 'nearest',
            intersect: true,
            animationDuration: 400
        };
    }

    static addTransitionEffects(chart) {
        chart.options.transitions = {
            active: {
                animation: {
                    duration: 400
                }
            }
        };
    }
}

// Initialize charts when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new DashboardCharts();
});
