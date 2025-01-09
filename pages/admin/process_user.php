<?php
require_once '../../includes/config.php';
require_once '../../includes/Database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/ActivityLogger.php';

$db = new Database();
$conn = $db->connect();
$logger = new ActivityLogger($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    
    switch($action) {
        case 'add':
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = 'user';
            $status = 'active';
            
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $password, $role, $status);
            
            if ($stmt->execute()) {
                $logger->log($_SESSION['admin_id'], 'add_user', "Created new user: $name");
                echo json_encode(['success' => true, 'message' => 'User added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add user']);
            }
            break;
            
        case 'update':
            $user_id = $_POST['user_id'];
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $status = $_POST['status'];
            
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, status = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $email, $status, $user_id);
            
            if ($stmt->execute()) {
                $logger->log($_SESSION['admin_id'], 'update_user', "Updated user ID #$user_id");
                echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update user']);
            }
            break;
            
        case 'delete':
            $user_id = $_POST['user_id'];
            
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            
            if ($stmt->execute()) {
                $logger->log($_SESSION['admin_id'], 'delete_user', "Deleted user ID #$user_id");
                echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
            }
            break;
    }
}
