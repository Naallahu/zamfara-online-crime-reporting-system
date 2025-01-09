class DashboardWebSocket {
    constructor() {
        this.socket = new WebSocket('ws://localhost:8080');
        this.initializeWebSocket();
    }

    initializeWebSocket() {
        this.socket.onopen = () => {
            console.log('Connected to WebSocket server');
            this.updateConnectionStatus(true);
        };

        this.socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.handleUpdate(data);
        };

        this.socket.onclose = () => {
            this.updateConnectionStatus(false);
            setTimeout(() => this.reconnect(), 5000);
        };
    }

    handleUpdate(data) {
        switch(data.type) {
            case 'stats_update':
                this.updateDashboardStats(data.stats);
                break;
            case 'new_report':
                this.handleNewReport(data.report);
                break;
            case 'status_change':
                this.handleStatusChange(data.update);
                break;
        }
    }

    updateDashboardStats(stats) {
        Object.keys(stats).forEach(key => {
            const element = document.getElementById(`${key}Count`);
            if (element) {
                animateValue(element.id, parseInt(element.textContent), stats[key], 1000);
            }
        });
    }

    handleNewReport(report) {
        // Update pending count
        const pendingCount = document.getElementById('pendingCount');
        animateValue('pendingCount', parseInt(pendingCount.textContent), 
                    parseInt(pendingCount.textContent) + 1, 500);
        
        // Show notification
        this.showNotification('New Report', 'A new report has been submitted');
    }

    showNotification(title, message) {
        const toast = new bootstrap.Toast(document.getElementById('notification-toast'));
        document.getElementById('toast-title').textContent = title;
        document.getElementById('toast-message').textContent = message;
        toast.show();
    }
}

// Initialize WebSocket connection
const dashboardWS = new DashboardWebSocket();