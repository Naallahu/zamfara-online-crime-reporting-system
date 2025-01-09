<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/security_helper.php';
require_once '../../includes/login_security.php';
require_once '../../classes/ActivityLogger.php';

$loginSecurity = new LoginSecurity($conn);
$logger = new ActivityLogger($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $_SESSION['login_error'] = "Invalid request";
        header("Location: login.php");
        exit();
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Check for account lockout
    if ($loginSecurity->isAccountLocked($username)) {
        $_SESSION['login_error'] = "Account temporarily locked. Please try again later.";
        $logger->log(0, 'login_attempt', "Locked account attempt: $username");
        header("Location: login.php");
        exit();
    }

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($admin = $result->fetch_assoc()) {
        if (password_verify($password, $admin['password'])) {
            // Successful login
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['last_activity'] = time();
            
            $loginSecurity->logLoginAttempt($username, 1);
            $logger->log($admin['id'], 'login', "Admin logged in successfully: $username");
            header("Location: dashboard.php");
            exit();
        }
    }
    
    // Failed login
    $loginSecurity->logLoginAttempt($username, 0);
    $logger->log(0, 'failed_login', "Failed login attempt: $username");
    $_SESSION['login_error'] = "Invalid username or password";
    header("Location: login.php");
    exit();
}
