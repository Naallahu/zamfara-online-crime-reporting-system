<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../classes/Report.php';
require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

$db = new Database();
$conn = $db->connect();
$report = new Report($conn);

$format = $_GET['format'] ?? 'excel';
$reports = $report->getRecentReports(1000);

if ($format === 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set headers
    $sheet->setCellValue('A1', 'Report ID');
    $sheet->setCellValue('B1', 'Crime Type');
    $sheet->setCellValue('C1', 'Location');
    $sheet->setCellValue('D1', 'Status');
    $sheet->setCellValue('E1', 'Date Reported');
    
    $row = 2;
    while ($reportData = $reports->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $reportData['id']);
        $sheet->setCellValue('B' . $row, $reportData['crime_type']);
        $sheet->setCellValue('C' . $row, $reportData['location']);
        $sheet->setCellValue('D' . $row, $reportData['status']);
        $sheet->setCellValue('E' . $row, $reportData['created_at']);
        $row++;
    }
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="crime_reports.xlsx"');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    
} else if ($format === 'pdf') {
    $html = '<h2>Crime Reports</h2>';
    $html .= '<table border="1" cellpadding="5">';
    $html .= '<tr><th>ID</th><th>Type</th><th>Location</th><th>Status</th><th>Date</th></tr>';
    
    while ($reportData = $reports->fetch_assoc()) {
        $html .= '<tr>';
        $html .= '<td>' . $reportData['id'] . '</td>';
        $html .= '<td>' . htmlspecialchars($reportData['crime_type']) . '</td>';
        $html .= '<td>' . htmlspecialchars($reportData['location']) . '</td>';
        $html .= '<td>' . $reportData['status'] . '</td>';
        $html .= '<td>' . date('Y-m-d', strtotime($reportData['created_at'])) . '</td>';
        $html .= '</tr>';
    }
    $html .= '</table>';
    
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("crime_reports.pdf");
}
