<?php
session_start();

// Log the logout time and activity
require_once '../../includes/config.php';
require_once '../../includes/database.php';

// Record logout activity if admin is logged in
if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    $logout_time = date('Y-m-d H:i:s');
    
    $stmt = $conn->prepare("UPDATE admin_sessions SET logout_time = ? WHERE admin_id = ? AND logout_time IS NULL");
    $stmt->bind_param("si", $logout_time, $admin_id);
    $stmt->execute();
}

// Clear all session data
$_SESSION = array();

// Destroy session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
