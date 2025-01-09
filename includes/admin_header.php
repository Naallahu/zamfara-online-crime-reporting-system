<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Zamfara Crime Reporting System</title>
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/admin.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/activity-feed.css">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo BASE_PATH; ?>assets/images/favicon.svg">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="p-3">
                <h4 class="text-white">Admin Panel</h4>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reports.php">
                        <i class="fas fa-file-alt me-2"></i> Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_users.php">
                        <i class="fas fa-users me-2"></i> Manage Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="activity_log.php">
                        <i class="fas fa-history me-2"></i> Activity Log
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="settings.php">
                        <i class="fas fa-cog me-2"></i> Settings
                    </a>
                </li>
            </ul>
        </div>

         <!-- Main Content -->
        <div class="content">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container-fluid">
                    <button class="btn btn-dark" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <a href="../../index.php" class="btn btn-light ms-3">
                        <i class="fas fa-home"></i> Main Home
                    </a>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['admin_name']; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>


    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const wrapper = document.querySelector('.wrapper');
        
        sidebarToggle.addEventListener('click', function() {
            wrapper.classList.toggle('sidebar-collapsed');
            // Store the state in localStorage for persistence
            localStorage.setItem('sidebarState', wrapper.classList.contains('sidebar-collapsed'));
        });
        
        // Restore sidebar state on page load
        if (localStorage.getItem('sidebarState') === 'true') {
            wrapper.classList.add('sidebar-collapsed');
        }
    });
    </script>
</body>
</html>