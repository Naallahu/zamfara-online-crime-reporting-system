<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';

header('Content-Type: application/json');

$query = "SELECT 
            u.name,
            COUNT(al.id) as action_count,
            MAX(al.created_at) as last_active
          FROM users u
          JOIN activity_logs al ON u.id = al.user_id
          WHERE al.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
          GROUP BY u.id
          ORDER BY action_count DESC
          LIMIT 10";

$result = $conn->query($query);
$users = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($users);
