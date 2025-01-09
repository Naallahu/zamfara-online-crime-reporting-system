// Quick Action Button Functions
document.addEventListener('DOMContentLoaded', function() {
    // Quick Action Button Event Listeners
    document.getElementById('generateReportBtn').addEventListener('click', function(e) {
        e.preventDefault();
        generateReport();
    });

    document.getElementById('exportDataBtn').addEventListener('click', function(e) {
        e.preventDefault();
        exportData();
    });

    document.getElementById('printSummaryBtn').addEventListener('click', function(e) {
        e.preventDefault();
        printSummary();
    });

    document.getElementById('showNotificationsBtn').addEventListener('click', function(e) {
        e.preventDefault();
        showNotifications();
    });
});

function generateReport() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'export_analytics.php';
    
    const inputs = {
        'export_type': 'pdf',
        'date_range': 'week',
        'data_type[]': 'user_activity',
        'csrf_token': document.querySelector('input[name="csrf_token"]').value
    };
    
    Object.entries(inputs).forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}


function exportData() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'export_analytics.php';
    
    const inputs = {
        'export_type': 'excel',
        'date_range': 'month',
        'data_type[]': ['user_activity', 'system_performance', 'security_logs'],
        'csrf_token': document.querySelector('input[name="csrf_token"]').value
    };
    
    Object.entries(inputs).forEach(([name, value]) => {
        if (Array.isArray(value)) {
            value.forEach(val => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = val;
                form.appendChild(input);
            });
        } else {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        }
    });
    
    document.body.appendChild(form);
    form.submit();
}

function printSummary() {
    window.print();
}

function showNotifications() {
    const toast = new bootstrap.Toast(document.getElementById('notification-toast'));
    document.getElementById('toast-title').textContent = 'System Notifications';
    document.getElementById('toast-message').textContent = 'Loading notifications...';
    toast.show();
    
    // Fetch notifications
    fetch('get_notifications.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('toast-message').innerHTML = data.notifications.map(
                notification => `<div class="notification-item">${notification.message}</div>`
            ).join('');
        })
        .catch(() => {
            document.getElementById('toast-message').textContent = 'Failed to load notifications';
        });
}

// Initialize tooltips and event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
    
    // Add click feedback
    const quickActionButtons = document.querySelectorAll('.btn');
    quickActionButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.add('active');
            setTimeout(() => this.classList.remove('active'), 200);
        });
    });
});
