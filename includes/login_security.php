<?php
class LoginSecurity {
    private $conn;
    private $max_attempts = 5;
    private $lockout_time = 900; // 15 minutes

    public function __construct($db) {
        $this->conn = $db;
    }

    public function checkLoginAttempts($username) {
        $stmt = $this->conn->prepare("SELECT * FROM login_attempts WHERE username = ? AND timestamp > (NOW() - INTERVAL 15 MINUTE)");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows;
    }

    public function logLoginAttempt($username, $success) {
        $stmt = $this->conn->prepare("INSERT INTO login_attempts (username, success, ip_address) VALUES (?, ?, ?)");
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt->bind_param("sis", $username, $success, $ip);
        $stmt->execute();
    }

    public function isAccountLocked($username) {
        $attempts = $this->checkLoginAttempts($username);
        return $attempts >= $this->max_attempts;
    }
}
