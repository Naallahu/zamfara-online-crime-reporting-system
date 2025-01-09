<?php
class Notification {
    private $conn;
    private $table = 'notifications';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($title, $message, $type) {
        $query = 'INSERT INTO ' . $this->table . '
            SET
                title = :title,
                message = :message,
                type = :type';

        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            'title' => $title,
            'message' => $message,
            'type' => $type
        ]);
    }

    public function getUnread() {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE read_status = 0 ORDER BY created_at DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
