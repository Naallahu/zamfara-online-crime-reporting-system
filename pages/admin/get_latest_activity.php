<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/ActivityLogger.php';

header('Content-Type: application/json');

$logger = new ActivityLogger($conn);
$lastId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;
$filter = isset($_GET['filter']) ? $_GET['filter'] : null;

$query = "SELECT l.*, a.username, a.name 
          FROM admin_activity_log l 
          LEFT JOIN admins a ON l.admin_id = a.id 
          WHERE l.id > ? ";

// Add filtering options
if ($filter) {
    switch($filter) {
        case 'login':
            $query .= "AND l.action IN ('login', 'logout', 'failed_login') ";
            break;
        case 'reports':
            $query .= "AND l.action IN ('view_report', 'update_report', 'delete_report') ";
            break;
        case 'users':
            $query .= "AND l.action IN ('add_user', 'update_user', 'delete_user') ";
            break;
    }
}

$query .= "ORDER BY l.created_at DESC LIMIT 10";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $lastId);
$stmt->execute();
$result = $stmt->get_result();

$activities = [];
while ($row = $result->fetch_assoc()) {
    $activities[] = [
        'id' => $row['id'],
        'action' => htmlspecialchars($row['action']),
        'details' => htmlspecialchars($row['details']),
        'admin_name' => htmlspecialchars($row['name'] ?? $row['username']),
        'time' => date('H:i:s', strtotime($row['created_at'])),
        'ip' => $row['ip_address'],
        'timestamp' => strtotime($row['created_at']),
        'date' => date('Y-m-d', strtotime($row['created_at']))
    ];
}

echo json_encode([
    'success' => true,
    'activities' => $activities,
    'last_id' => end($activities)['id'] ?? $lastId
]);
