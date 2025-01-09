<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';
include 'includes/header.php';

// Create database connection
$db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if (isset($_GET['query'])) {
    $search = $db->real_escape_string($_GET['query']);
    $query = "SELECT * FROM reports 
            WHERE reference_number LIKE '%$search%' 
            OR crime_type LIKE '%$search%'
            OR description LIKE '%$search%'
            OR lga LIKE '%$search%'
            OR location LIKE '%$search%'
            OR status LIKE '%$search%'
            ORDER BY created_at DESC";
    
    $result = $db->query($query);

}
// Add this function at the top with your other PHP code
function getStatusBadgeColor($status) {
    switch($status) {
        case 'resolved':
            return 'success';
        case 'investigating':
            return 'info';
        case 'closed':
            return 'secondary';
        default:
            return 'warning';
    }
}
?>
<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    main {
        flex: 1;
        width: 100%;
    }

    .container {
        height: 100%;
    }

    footer {
        margin-top: auto;
        position: relative;
        bottom: 0;
        width: 100%;
    }
</style>

<main>
    <div class="container py-5">
        <h2>Search Results</h2>
        
        <?php 
        if (isset($result) && $result->num_rows > 0) {
            echo '<div class="row">';
            while ($row = $result->fetch_assoc()) { ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Report #<?php echo htmlspecialchars($row['reference_number']); ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($row['crime_type']); ?></h6>
                        <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                        <p class="card-text"><small>Location: <?php echo htmlspecialchars($row['lga'] . ' - ' . $row['location']); ?></small></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-<?php echo getStatusBadgeColor($row['status']); ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                            <small class="text-muted"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                        </div>
                    </div>
                </div>
            </div>
           <?php }
            echo '</div>';
        } else {
            echo '<div class="alert alert-info">No results found for your search query.</div>';
        }
        ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>