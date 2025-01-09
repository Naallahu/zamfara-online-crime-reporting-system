<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
include '../../includes/admin_auth.php';

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'];
$status = $data['status'];

$sql = "UPDATE users SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $status, $user_id);

$response = ['success' => $stmt->execute()];
echo json_encode($response);
