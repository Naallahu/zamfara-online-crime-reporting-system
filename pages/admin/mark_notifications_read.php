<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';

$userId = $_SESSION['admin_id'];
$query = "UPDATE notifications SET read_status = 1 
          WHERE user_id = ? AND read_status = 0";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();

echo json_encode(['success' => true]);
