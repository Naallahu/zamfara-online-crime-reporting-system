<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/ActivityLogger.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$startDate = $data['startDate'];
$endDate = $data['endDate'];
$actionType = $data['actionType'];

$logger = new ActivityLogger($conn);
$filteredActivities = $logger->getFilteredActivities($startDate, $endDate, $actionType);

$result = [];
while ($activity = $filteredActivities->fetch_assoc()) {
    $result[] = [
        date('M d, Y H:i:s', strtotime($activity['created_at'])),
        htmlspecialchars($activity['name'] ?? $activity['username']),
        htmlspecialchars($activity['action']),
        htmlspecialchars($activity['details']),
        htmlspecialchars($activity['ip_address'])
    ];
}

echo json_encode($result);
