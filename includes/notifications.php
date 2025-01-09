<?php
class ActivityNotifications {
    private $db;
    private $websocket;

    public function __construct($db) {
        $this->db = $db;
        $this->initializeWebSocket();
    }

    public function sendNotification($userId, $action, $details) {
        $query = "INSERT INTO notifications (user_id, title, message, type) 
                 VALUES (?, ?, ?, 'activity')";
        
        $title = "New Activity Alert";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iss", $userId, $title, $details);
        
        if ($stmt->execute()) {
            $this->broadcastNotification([
                'type' => 'activity',
                'action' => $action,
                'details' => $details,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function getUnreadCount($userId) {
        $query = "SELECT COUNT(*) as count FROM notifications 
                 WHERE user_id = ? AND read_status = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
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

    public function markAsRead($userId) {
        $query = "UPDATE notifications 
                 SET read_status = 1 
                 WHERE user_id = ? AND read_status = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    private function initializeWebSocket() {
        $this->websocket = new WebSocket([
            'host' => 'localhost',
            'port' => 8080
        ]);
    }

    private function broadcastNotification($data) {
        $this->websocket->broadcast(json_encode($data));
    }
}
