<?php
class Staff {
    private $conn;
    private $table = 'staff';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = 'INSERT INTO ' . $this->table . '
            SET
                name = :name,
                username = :username,
                password = :password,
                email = :email,
                department = :department,
                phone = :phone';

        $stmt = $this->conn->prepare($query);
        
        // Hash password before saving
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        return $stmt->execute($data);
    }

    public function authenticate($username, $password) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE username = :username';
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['username' => $username]);
        
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $staff && password_verify($password, $staff['password']) ? $staff : false;
    }

    // Method to get all staff
    public function getAllStaff() {
        $query = 'SELECT * FROM ' . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
