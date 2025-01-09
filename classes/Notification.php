<?php
class Notifications {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($userId, $title, $message, $type) {
        $query = "INSERT INTO notifications (user_id, title, message, type) 
                 VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("isss", $userId, $title, $message, $type);
        return $stmt->execute();
    }

    public function getUnreadCount($userId) {
        $query = "SELECT COUNT(*) as count FROM notifications 
                 WHERE user_id = ? AND read_status = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }

    public function markAsRead($userId) {
        $query = "UPDATE notifications 
                 SET read_status = 1 
                 WHERE user_id = ? AND read_status = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    public function getUserNotifications($userId, $limit = 10) {
        $query = "SELECT * FROM notifications 
                 WHERE user_id = ? 
                 ORDER BY created_at DESC 
                 LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        return $stmt->get_result();
    }
}
