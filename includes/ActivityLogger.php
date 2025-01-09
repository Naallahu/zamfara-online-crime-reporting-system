<?php
class ActivityLogger {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Your existing methods remain the same

    // Add new method for admin activities
    public function logAdminActivity($adminId, $action, $details) {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        
        $query = "INSERT INTO admin_activity_log (admin_id, action, details, ip_address, user_agent) 
                 VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("issss", $adminId, $action, $details, $ipAddress, $userAgent);
        return $stmt->execute();
    }

    // Add method to get admin activities
    public function getAdminActivities($limit = 10) {
        $query = "SELECT aal.*, a.username 
                 FROM admin_activity_log aal 
                 LEFT JOIN admins a ON aal.admin_id = a.id 
                 ORDER BY aal.created_at DESC 
                 LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Add method for filtered admin activities
    public function getFilteredAdminActivities($startDate = null, $endDate = null, $actionType = null, $adminId = null) {
        $query = "SELECT aal.*, a.username 
                 FROM admin_activity_log aal 
                 LEFT JOIN admins a ON aal.admin_id = a.id 
                 WHERE 1=1";
        
        $params = [];
        $types = "";
        
        if ($startDate) {
            $query .= " AND DATE(aal.created_at) >= ?";
            $params[] = $startDate;
            $types .= "s";
        }
        
        if ($endDate) {
            $query .= " AND DATE(aal.created_at) <= ?";
            $params[] = $endDate;
            $types .= "s";
        }
        
        if ($actionType) {
            $query .= " AND aal.action = ?";
            $params[] = $actionType;
            $types .= "s";
        }
        
        if ($adminId) {
            $query .= " AND aal.admin_id = ?";
            $params[] = $adminId;
            $types .= "i";
        }
        
        $query .= " ORDER BY aal.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt->get_result();
    }

    // Add method for admin activity statistics
    public function getAdminActivityStats() {
        $query = "SELECT 
                    action,
                    COUNT(*) as count,
                    DATE(created_at) as date
                 FROM admin_activity_log 
                 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                 GROUP BY action, DATE(created_at)
                 ORDER BY date DESC";
        
        return $this->db->query($query);
    }
}