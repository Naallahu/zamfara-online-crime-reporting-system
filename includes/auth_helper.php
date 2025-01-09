<?php
function isAdmin() {
    return isset($_SESSION['admin_role']) && in_array($_SESSION['admin_role'], ['super_admin', 'admin']);
}

function isSuperAdmin() {
    return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin';
}

function checkSessionTimeout() {
    $timeout = 1800; // 30 minutes
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        session_destroy();
        header("Location: ../../login.php?timeout=1");
        exit();
    }
}

function getAdminInfo() {
    return [
        'id' => $_SESSION['admin_id'],
        'name' => $_SESSION['admin_name'],
        'email' => $_SESSION['admin_email'],
        'role' => $_SESSION['admin_role']
    ];
}
