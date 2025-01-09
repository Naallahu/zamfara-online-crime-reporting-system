// Status Management
function updateStatus(reportId, status) {
    fetch('update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            report_id: reportId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showAlert('Status updated successfully', 'success');
        }
    });
}

function deleteReport(reportId) {
    if(confirm('Are you sure you want to delete this report?')) {
        fetch('delete_report.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                report_id: reportId
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            }
        });
    }
}

// Export Functions
function exportToExcel() {
    window.location.href = 'export_excel.php';
}

function generatePDF() {
    window.location.href = 'generate_pdf.php';
}

// UI Functions
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    
    sidebarToggle?.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        document.querySelector('.content').classList.toggle('expanded');
    });

    // Dropdown Toggle
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    dropdownToggle?.addEventListener('click', function(e) {
        e.preventDefault();
        const dropdownMenu = this.nextElementSibling;
        dropdownMenu.classList.toggle('show');
    });

    // Active Link Highlighting
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });
});

// Quick Actions
function generateReport() {
    window.location.href = 'generate_report.php';
}

function exportData() {
    window.location.href = 'export_data.php';
}

function printSummary() {
    window.print();
}

// Notification System
function showNotifications() {
    alert('New notifications feature coming soon!');
}

function toggleNotifications() {
    const notificationCenter = document.getElementById('notificationCenter');
    notificationCenter?.classList.toggle('d-none');
}

function markAsRead(id) {
    console.log('Marking notification as read:', id);
}

// Charts Initialization
function initCharts(crimeTypesData, monthlyTrendsData) {
    const ctxCrimeTypes = document.getElementById('crimeTypesChart')?.getContext('2d');
    if (ctxCrimeTypes) {
        new Chart(ctxCrimeTypes, {
            type: 'pie',
            data: {
                labels: crimeTypesData.labels,
                datasets: [{
                    data: crimeTypesData.values,
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                    ]
                }]
            }
        });
    }

    const ctxMonthly = document.getElementById('monthlyTrendsChart')?.getContext('2d');
    if (ctxMonthly) {
        new Chart(ctxMonthly, {
            type: 'line',
            data: {
                labels: monthlyTrendsData.labels,
                datasets: [{
                    label: 'Number of Reports',
                    data: monthlyTrendsData.values,
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
}
// View User Details
function viewUser(userId) {
    fetch(`view_user.php?id=${userId}`)
        .then(response => response.json())
        .then(user => {
            // Create modal with user details
            const modal = new bootstrap.Modal(document.getElementById('userModal'));
            document.getElementById('userName').textContent = user.name;
            document.getElementById('userEmail').textContent = user.email;
            document.getElementById('userPhone').textContent = user.phone;
            document.getElementById('userRole').textContent = user.role;
            modal.show();
        });
}

// Toggle User Status
function toggleStatus(userId) {
    const currentStatus = event.target.closest('tr').querySelector('.badge').textContent.toLowerCase();
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    
    fetch('update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        }
    });
}
// User Management Functions
function editUser(userId) {
    fetch(`get_user.php?id=${userId}`)
        .then(response => response.json())
        .then(user => {
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_status').value = user.status;
            $('#editUserModal').modal('show');
        });
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch('process_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&user_id=${userId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

// Settings Functions
function createBackup() {
    fetch('create_backup.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Backup created successfully!');
            location.reload();
        }
    });
}

// Activity Monitoring Functions
function updateActivityFeed() {
    fetch('get_latest_activity.php')
        .then(response => response.json())
        .then(activities => {
            const feed = document.getElementById('activity-feed');
            if (feed) {
                activities.forEach(activity => {
                    const item = `
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1">${activity.action}</h6>
                                <small>${activity.time}</small>
                            </div>
                            <p class="mb-1">${activity.details}</p>
                            <small>By: ${activity.admin_name}</small>
                        </div>
                    `;
                    feed.insertAdjacentHTML('afterbegin', item);
                });
            }
        });
}

// Activity Analytics Functions
function initActivityCharts(actionData, adminData) {
    const actionChart = document.getElementById('actionChart')?.getContext('2d');
    if (actionChart) {
        new Chart(actionChart, {
            type: 'pie',
            data: {
                labels: actionData.labels,
                datasets: [{
                    data: actionData.values,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
                }]
            }
        });
    }

    const adminChart = document.getElementById('adminChart')?.getContext('2d');
    if (adminChart) {
        new Chart(adminChart, {
            type: 'bar',
            data: {
                labels: adminData.labels,
                datasets: [{
                    label: 'Activity Count',
                    data: adminData.values,
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
    }
}

// Activity Export Function
function exportActivity(format) {
    const startDate = document.getElementById('export_start_date').value;
    const endDate = document.getElementById('export_end_date').value;
    
    window.location.href = `export_activity.php?format=${format}&start_date=${startDate}&end_date=${endDate}`;
}

// Update activity feed every 30 seconds
if (document.getElementById('activity-feed')) {
    setInterval(updateActivityFeed, 30000);
    updateActivityFeed(); // Initial load
}


// Report Management Functions
function viewReport(reportId) {
    fetch(`get_report.php?id=${reportId}`)
        .then(response => response.json())
        .then(report => {
            const modal = new bootstrap.Modal(document.getElementById('reportModal'));
            document.getElementById('reportDetails').innerHTML = `
                <h5>Report #${report.id}</h5>
                <p><strong>Type:</strong> ${report.crime_type}</p>
                <p><strong>Location:</strong> ${report.location}</p>
                <p><strong>Description:</strong> ${report.description}</p>
                <p><strong>Status:</strong> ${report.status}</p>
                <p><strong>Reported:</strong> ${report.created_at}</p>
            `;
            modal.show();
        });
}

function updateStatus(reportId, status) {
    fetch('update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            report_id: reportId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showAlert('Status updated successfully', 'success');
        }
    });
}

function exportReports(format) {
    window.location.href = `export_reports.php?format=${format}`;
}

function deleteReport(reportId) {
    if(confirm('Are you sure you want to delete this report?')) {
        fetch('delete_report.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                report_id: reportId
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            }
        });
    }
}
