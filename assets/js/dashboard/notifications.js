class DashboardNotifications {
    constructor() {
        this.ws = new WebSocket('ws://localhost:8080');
        this.initializeWebSocket();
        this.setupEventListeners();
    }

    initializeWebSocket() {
        this.ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.handleNotification(data);
        };
    }

    handleNotification(data) {
        switch(data.type) {
            case 'new_report':
                this.updateReportCount(data);
                this.showToast('New Report', data.message);
                break;
            case 'status_update':
                this.updateStatusCounts(data);
                this.showToast('Status Update', data.message);
                break;
            case 'alert':
                this.showAlert(data.message);
                break;
        }
    }

    showToast(title, message) {
        const toast = new bootstrap.Toast(document.getElementById('notification-toast'));
        document.getElementById('toast-title').textContent = title;
        document.getElementById('toast-message').textContent = message;
        toast.show();
    }

    updateReportCount(data) {
        animateValue('pendingCount', 
            document.getElementById('pendingCount').textContent, 
            data.counts.pending, 
            1000
        );
    }

    updateStatusCounts(data) {
        Object.keys(data.counts).forEach(status => {
            const element = document.getElementById(`${status}Count`);
            if (element) {
                animateValue(element.id, element.textContent, data.counts[status], 1000);
            }
        });
    }
}

// Initialize notifications
const dashboardNotifications = new DashboardNotifications();