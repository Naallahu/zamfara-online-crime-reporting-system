<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';

class ActivityExporter {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function exportCSV($start_date = null, $end_date = null, $action_type = null, $admin_id = null) {
        $query = "SELECT l.created_at, a.username, l.action, l.details, l.ip_address 
                 FROM admin_activity_log l 
                 LEFT JOIN admins a ON l.admin_id = a.id 
                 WHERE 1=1";
        
        $params = [];
        $types = "";
        
        if ($start_date && $end_date) {
            $query .= " AND DATE(l.created_at) BETWEEN ? AND ?";
            $params[] = $start_date;
            $params[] = $end_date;
            $types .= "ss";
        }
        
        if ($action_type) {
            $query .= " AND l.action = ?";
            $params[] = $action_type;
            $types .= "s";
        }
        
        if ($admin_id) {
            $query .= " AND l.admin_id = ?";
            $params[] = $admin_id;
            $types .= "i";
        }
        
        $query .= " ORDER BY l.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="activity_log_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, ['Date/Time', 'Admin', 'Action', 'Details', 'IP Address']);
        
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                date('d/m/Y H:i', strtotime($row['created_at'])),
                $row['username'],
                $row['action'],
                $row['details'],
                $row['ip_address']
            ]);
        }
        
        fclose($output);
        exit();
    }
}

if (isset($_POST['export'])) {
    $exporter = new ActivityExporter($conn);
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $action_type = $_POST['action_type'] ?? null;
    $admin_id = $_POST['admin_id'] ?? null;
    
    $exporter->exportCSV($start_date, $end_date, $action_type, $admin_id);
}
