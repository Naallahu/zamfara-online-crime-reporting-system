<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/Report.php';
require_once '../../classes/ActivityLogger.php';

$db = new Database();
$conn = $db->connect();
$report = new Report($conn);
$logger = new ActivityLogger($conn);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $reportId = $_POST['report_id'] ?? null;
    
    switch($action) {
        case 'update_status':
            $status = $_POST['status'];
            if ($report->updateReportStatus($reportId, $status, $_SESSION['admin_id'])) {
                $logger->log($_SESSION['admin_id'], 'update_report', "Updated report #$reportId status to $status");
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            break;
            
        case 'delete':
            if ($report->deleteReport($reportId)) {
                $logger->log($_SESSION['admin_id'], 'delete_report', "Deleted report #$reportId");
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            break;
    }
}
