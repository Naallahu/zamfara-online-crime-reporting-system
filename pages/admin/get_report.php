<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/Report.php';

$db = new Database();
$conn = $db->connect();
$report = new Report($conn);

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $reportId = (int)$_GET['id'];
    $reportDetails = $report->getReportDetails($reportId);
    
    if ($reportDetails) {
        // Log view activity
        $logger = new ActivityLogger($conn);
        $logger->log($_SESSION['admin_id'], 'view_report', "Viewed report #$reportId");
        
        echo json_encode([
            'success' => true,
            'data' => $reportDetails
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Report not found'
        ]);
    }
}
