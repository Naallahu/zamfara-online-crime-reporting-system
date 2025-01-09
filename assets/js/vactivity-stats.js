function initializeActivityStats(data) {
    createTrendsChart(data);
    createDistributionChart(data);
    initializeActivityMap();
    updateActiveUsers();
}

function createTrendsChart(data) {
    const ctx = document.getElementById('activityTrendsChart').getContext('2d');
    const dates = [...new Set(data.map(item => item.date))];
    const actions = [...new Set(data.map(item => item.action))];
    
    const datasets = actions.map(action => ({
        label: action,
        data: dates.map(date => {
            const match = data.find(item => item.date === date && item.action === action);
            return match ? match.count : 0;
        }),
        borderColor: getRandomColor(),
        fill: false
    }));

    new Chart(ctx, {
        type: 'line',
        data: { labels: dates, datasets },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

function createDistributionChart(data) {
    const ctx = document.getElementById('actionDistributionChart').getContext('2d');
    const actionCounts = {};
    
    data.forEach(item => {
        actionCounts[item.action] = (actionCounts[item.action] || 0) + parseInt(item.count);
    });

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(actionCounts),
            datasets: [{
                data: Object.values(actionCounts),
                backgroundColor: Object.keys(actionCounts).map(() => getRandomColor())
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

function initializeActivityMap() {
    const map = L.map('activityMap').setView([11.8948, 6.9911], 7); // Zamfara coordinates
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    fetch('get_activity_locations.php')
        .then(response => response.json())
        .then(locations => {
            locations.forEach(location => {
                L.marker([location.lat, location.lng])
                    .bindPopup(`${location.count} activities`)
                    .addTo(map);
            });
        });
}

function updateActiveUsers() {
    fetch('get_active_users.php')
        .then(response => response.json())
        .then(users => {
            const tbody = document.getElementById('activeUsersList');
            tbody.innerHTML = users.map(user => `
                <tr>
                    <td>${user.name}</td>
                    <td>${user.action_count}</td>
                    <td>${formatDate(user.last_active)}</td>
                </tr>
            `).join('');
        });
}

function getRandomColor() {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleString();
}

// Update active users list every 5 minutes
setInterval(updateActiveUsers, 300000);
