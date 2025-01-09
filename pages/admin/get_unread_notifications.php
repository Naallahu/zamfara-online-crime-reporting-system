<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';

header('Content-Type: application/json');

$userId = $_SESSION['admin_id'];
$query = "SELECT COUNT(*) as count FROM notifications 
          WHERE user_id = ? AND read_status = 0";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->fetch_assoc()['count'];

echo json_encode(['count' => $count]);
