<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Zamfara Crime Reporting System - Anonymous Crime Reporting Platform">
    <meta name="keywords" content="crime reporting, zamfara, anonymous reporting, security">
    <title>Zamfara Crime Reporting System</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo BASE_PATH; ?>assets/images/favicon.svg">
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
    <!-- Add this in the head section -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    <!-- Custom CSS -->
    <link href="<?php echo BASE_PATH; ?>assets/css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Top Alert Bar -->
    <div class="alert alert-info alert-dismissible fade show m-0" role="alert">
        <div class="container">
            Emergency Hotline: <strong>112</strong> | Available 24/7
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo BASE_PATH; ?>index.php">
                <img src="<?php echo BASE_PATH; ?>assets/images/logo.svg" alt="ZCRS Logo" width="40" class="me-2">
                Zamfara CRS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_PATH; ?>index.php"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_PATH; ?>pages/reporter/report.php"><i class="fas fa-exclamation-circle"></i> Report Crime</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_PATH; ?>pages/track.php"><i class="fas fa-search"></i> Track Report</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_PATH; ?>pages/staff/login.php"><i class="fas fa-user-tie"></i> Staff Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_PATH; ?>pages/admin/login.php"><i class="fas fa-user-shield"></i> Admin Login</a>
                    </li>
                </ul>
                <!-- Emergency Button -->
                <button class="btn btn-danger ms-3" onclick="window.location.href='tel:112'">
                    <i class="fas fa-phone-alt"></i> Emergency
                </button>
            </div>
        </div>
    </nav>
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/counter.js"></script>
</body>


