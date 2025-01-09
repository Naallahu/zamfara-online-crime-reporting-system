<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
include '../../includes/admin_auth.php';

$user_id = $_GET['id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

echo json_encode($user);
