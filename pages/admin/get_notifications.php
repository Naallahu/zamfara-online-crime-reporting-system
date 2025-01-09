<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../includes/notifications.php';

header('Content-Type: application/json');

$db = new Database();
$conn = $db->connect();
$notifications = new ActivityNotifications($conn);

$userId = $_SESSION['admin_id'];

// Get unread count
$unreadCount = $notifications->getUnreadCount($userId);

// Get recent notifications
$result = $notifications->getUserNotifications($userId, 5);
$notificationsList = [];

while ($row = $result->fetch_assoc()) {
    $notificationsList[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'message' => $row['message'],
        'type' => $row['type'],
        'read_status' => $row['read_status'],
        'created_at' => date('M d, Y H:i', strtotime($row['created_at']))
    ];
}

echo json_encode([
    'unreadCount' => $unreadCount,
    'notifications' => $notificationsList
]);
