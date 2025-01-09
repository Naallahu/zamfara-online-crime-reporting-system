<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/ActivityLogger.php';

$logger = new ActivityLogger($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $system_name = trim($_POST['system_name']);
    $admin_email = trim($_POST['admin_email']);
    
    // Update settings in database
    $stmt = $conn->prepare("UPDATE settings SET value = ? WHERE name = 'system_name'");
    $stmt->bind_param("s", $system_name);
    $stmt->execute();
    
    $stmt = $conn->prepare("UPDATE settings SET value = ? WHERE name = 'admin_email'");
    $stmt->bind_param("s", $admin_email);
    
    if ($stmt->execute()) {
        $logger->log($_SESSION['admin_id'], 'update_settings', "Updated system settings");
        $_SESSION['success'] = "Settings updated successfully";
    }
    
    header("Location: settings.php");
    exit();
}
