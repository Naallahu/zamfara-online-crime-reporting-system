<?php
class Report {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getRecentReports($limit = 10) {
        $query = "SELECT * FROM reports ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getTotalReports() {
        $result = $this->db->query("SELECT COUNT(*) as total FROM reports");
        return $result->fetch_assoc()['total'];
    }

    public function getReportsByStatus($status) {
        $query = "SELECT * FROM reports WHERE status = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $status);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getReportsByLGA($lga) {
        $query = "SELECT * FROM reports WHERE lga = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $lga);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function updateReportStatus($reportId, $status, $adminId) {
        $query = "UPDATE reports SET status = ?, updated_by = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sii", $status, $adminId, $reportId);
        return $stmt->execute();
    }

    public function getReportDetails($reportId) {
        $query = "SELECT r.*, u.name as reporter_name 
                 FROM reports r 
                 LEFT JOIN users u ON r.user_id = u.id 
                 WHERE r.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getCrimeTypeStats() {
        $query = "SELECT 
            crime_type,
            COUNT(*) as count,
            ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM reports)), 1) as percentage
        FROM reports 
        GROUP BY crime_type 
        ORDER BY count DESC";
        
        return $this->db->query($query);
    }

    public function getLGAStats() {
        $query = "SELECT 
            lga,
            COUNT(*) as count
        FROM reports 
        GROUP BY lga 
        ORDER BY count DESC";
        
        return $this->db->query($query);
    }

    public function getMonthlyTrends() {
        $query = "SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as count
        FROM reports 
        GROUP BY month 
        ORDER BY month ASC 
        LIMIT 12";
        
        return $this->db->query($query);
    }

    public function getResponseTimeStats() {
        $query = "SELECT 
            AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_time,
            MIN(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as min_time,
            MAX(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as max_time
        FROM reports 
        WHERE status = 'resolved'";
        
        $result = $this->db->query($query);
        return $result->fetch_assoc();
    }

    public function getStatusTrends() {
        $query = "SELECT 
            status,
            COUNT(*) as count,
            DATE_FORMAT(created_at, '%Y-%m') as month
        FROM reports 
        GROUP BY status, month
        ORDER BY month ASC";
        
        return $this->db->query($query);
    }

    public function getHourlyDistribution() {
        $query = "SELECT 
            HOUR(created_at) as hour,
            COUNT(*) as count
        FROM reports 
        GROUP BY hour
        ORDER BY hour ASC";
        
        return $this->db->query($query);
    }

    public function getHighRiskAreas() {
        $query = "SELECT 
            location,
            COUNT(*) as incident_count,
            GROUP_CONCAT(DISTINCT crime_type) as crime_types
        FROM reports 
        GROUP BY location
        HAVING incident_count > 5
        ORDER BY incident_count DESC";
        
        return $this->db->query($query);
    }

    public function getResponseEfficiency() {
        $query = "SELECT 
            CASE 
                WHEN TIMESTAMPDIFF(HOUR, created_at, updated_at) < 24 THEN '< 24 hours'
                WHEN TIMESTAMPDIFF(HOUR, created_at, updated_at) < 48 THEN '24-48 hours'
                ELSE '> 48 hours'
            END as response_time,
            COUNT(*) as count
        FROM reports 
        WHERE status = 'resolved'
        GROUP BY response_time";
        
        return $this->db->query($query);
    }
}
