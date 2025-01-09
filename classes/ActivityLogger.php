<?php
class ActivityLogger {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function log($admin_id, $action, $details = '') {
        $stmt = $this->conn->prepare("INSERT INTO admin_activity_log (admin_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $stmt->bind_param("issss", $admin_id, $action, $details, $ip, $user_agent);
        return $stmt->execute();
    }
    
    public function getActivityLog($page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT l.*, a.username, a.name 
                 FROM admin_activity_log l 
                 LEFT JOIN admins a ON l.admin_id = a.id 
                 ORDER BY l.created_at DESC 
                 LIMIT ?, ?";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        
        return $stmt->get_result();
    }
    
    public function getActivityStats() {
        $query = "SELECT 
                    action, 
                    COUNT(*) as count,
                    DATE(created_at) as date
                 FROM admin_activity_log 
                 GROUP BY action, DATE(created_at)
                 ORDER BY date DESC";
        return $this->conn->query($query);
    }
    
    public function clearOldLogs($days = 90) {
        $query = "DELETE FROM admin_activity_log 
                 WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $days);
        return $stmt->execute();
    }
}
