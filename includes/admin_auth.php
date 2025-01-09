<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check for admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Add role-based access control
$admin_role = $_SESSION['admin_role'] ?? '';
$allowed_roles = ['super_admin', 'admin'];

if (!in_array($admin_role, $allowed_roles)) {
    header("Location: ../../unauthorized.php");
    exit();
}

// Set admin data for use in pages
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? 'Administrator';
$admin_email = $_SESSION['admin_email'] ?? '';

// Track last activity
$_SESSION['last_activity'] = time();
