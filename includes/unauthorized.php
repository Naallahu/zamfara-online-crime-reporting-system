<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Unauthorized Access</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-danger">
            <h4>Unauthorized Access</h4>
            <p>You don't have permission to access this area.</p>
            <a href="login.php" class="btn btn-primary">Return to Login</a>
        </div>
    </div>
</body>
</html>
