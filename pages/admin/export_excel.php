l<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
include '../../includes/admin_auth.php';

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="crime_reports_' . date('Y-m-d') . '.xls"');

// Fetch all reports
$sql = "SELECT r.*, u.name as reporter_name, ps.name as station_name 
        FROM reports r 
        LEFT JOIN users u ON r.user_id = u.id 
        LEFT JOIN police_stations ps ON r.station_id = ps.id 
        ORDER BY r.created_at DESC";
$result = $conn->query($sql);

// Create Excel header with styling
echo "<table border='1'>";
echo "<tr style='background-color: #f0f0f0;'>";
echo "<th>ID</th>";
echo "<th>Reporter</th>";
echo "<th>Crime Type</th>";
echo "<th>Location</th>";
echo "<th>Police Station</th>";
echo "<th>Status</th>";
echo "<th>Description</th>";
echo "<th>Date Reported</th>";
echo "</tr>";

while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['reporter_name'] . "</td>";
    echo "<td>" . ucfirst($row['crime_type']) . "</td>";
    echo "<td>" . $row['location'] . "</td>";
    echo "<td>" . $row['station_name'] . "</td>";
    echo "<td>" . ucfirst($row['status']) . "</td>";
    echo "<td>" . $row['description'] . "</td>";
    echo "<td>" . date('Y-m-d H:i', strtotime($row['created_at'])) . "</td>";
    echo "</tr>";
}
echo "</table>";
