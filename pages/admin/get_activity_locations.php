<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';

header('Content-Type: application/json');

$query = "SELECT 
            ip_address,
            COUNT(*) as count,
            MAX(created_at) as last_activity
          FROM activity_logs 
          WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
          GROUP BY ip_address";

$result = $conn->query($query);
$locations = [];

while ($row = $result->fetch_assoc()) {
    // Get geolocation data for IP
    $geoData = json_decode(file_get_contents("http://ip-api.com/json/{$row['ip_address']}"));
    if ($geoData && $geoData->status === 'success') {
        $locations[] = [
            'lat' => $geoData->lat,
            'lng' => $geoData->lon,
            'count' => $row['count']
        ];
    }
}

echo json_encode($locations);
