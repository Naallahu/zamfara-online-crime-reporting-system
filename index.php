<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';
include 'includes/header.php';

// Create database connection
$db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>
<!-- Hero Section with Search -->
<div class="hero-section">
    <div class="overlay"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center text-white">
                <h1 class="display-4 fw-bold mb-4">Welcome to Zamfara Crime Reporting System</h1>
                <div class="search-box mb-5">
                   <form class="d-flex" action="search_results.php" method="GET">
    <input type="search" name="query" class="form-control form-control-lg" placeholder="Search for reports, updates...">
    <button class="btn btn-primary btn-lg" type="submit"><i class="fas fa-search"></i></button>
</form>

                </div>
                <div class="hero-buttons">
                    <a href="pages/reporter/report.php" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-exclamation-circle"></i> Report Crime
                    </a>
                    <a href="pages/track.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-search"></i> Track Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Crime Categories -->
<div class="categories-section py-5">
    <div class="container">
        <h2 class="text-center mb-4">Report by Category</h2>
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="category-card">
                    <i class="fas fa-user-ninja fa-2x mb-3"></i>
                    <h5>Kidnapping</h5>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="category-card">
                    <i class="fas fa-skull-crossbones fa-2x mb-3"></i>
                    <h5>Murder</h5>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="category-card">
                    <i class="fas fa-mask fa-2x mb-3"></i>
                    <h5>Robbery</h5>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="category-card">
                    <i class="fas fa-bomb fa-2x mb-3"></i>
                    <h5>Terrorism</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Counter -->
<div class="counter-section py-5">
    <div class="container">
        <div class="row text-center">
            <?php
            // Get total reports
            $total_reports = $db->query("SELECT COUNT(*) as total FROM reports")->fetch_object()->total;
            
            // Get solved cases
            $solved_cases = $db->query("SELECT COUNT(*) as total FROM reports WHERE status = 'solved'")->fetch_object()->total;
            
            // Get active cases
            $active_cases = $db->query("SELECT COUNT(*) as total FROM reports WHERE status = 'active'")->fetch_object()->total;
            
            // Get daily reports
            $daily_reports = $db->query("SELECT COUNT(*) as total FROM reports WHERE DATE(created_at) = CURDATE()")->fetch_object()->total;
            ?>
            <div class="col-md-3 col-6 mb-4">
                <div class="counter-item">
                    <h2 class="counter" data-target="<?php echo $total_reports; ?>">0</h2>
                    <p>Total Reports</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="counter-item">
                    <h2 class="counter" data-target="<?php echo $solved_cases; ?>">0</h2>
                    <p>Cases Solved</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="counter-item">
                    <h2 class="counter" data-target="<?php echo $active_cases; ?>">0</h2>
                    <p>Active Cases</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="counter-item">
                    <h2 class="counter" data-target="<?php echo $daily_reports; ?>">0</h2>
                    <p>Daily Reports</p>
                </div>
            </div>
        </div>
    </div>
</div>


<?php include 'includes/footer.php'; ?>
