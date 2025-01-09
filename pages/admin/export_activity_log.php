<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ActivityLogExporter {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getActivityData($filters = []) {
        $query = "SELECT al.*, a.username 
                 FROM admin_activity_log al 
                 LEFT JOIN admins a ON al.admin_id = a.id 
                 WHERE 1=1";
        
        if (!empty($filters['start_date'])) {
            $query .= " AND DATE(al.created_at) >= '{$filters['start_date']}'";
        }
        if (!empty($filters['end_date'])) {
            $query .= " AND DATE(al.created_at) <= '{$filters['end_date']}'";
        }
        
        $query .= " ORDER BY al.created_at DESC";
        
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function exportToExcel($data) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', 'ACTIVITY LOG REPORT');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        // Set headers
        $headers = ['ID', 'Admin', 'Action', 'Details', 'Severity', 'IP Address', 'User Agent', 'Date/Time'];
        $col = 'A';
        $row = 3;
        
        foreach($headers as $header) {
            $sheet->setCellValue($col.$row, $header);
            $sheet->getStyle($col.$row)->getFont()->setBold(true);
            $col++;
        }
        
        // Add data
        $row = 4;
        foreach($data as $record) {
            $sheet->setCellValue('A'.$row, $record['id']);
            $sheet->setCellValue('B'.$row, $record['username']);
            $sheet->setCellValue('C'.$row, $record['action']);
            $sheet->setCellValue('D'.$row, $record['details']);
            $sheet->setCellValue('E'.$row, $record['severity']);
            $sheet->setCellValue('F'.$row, $record['ip_address']);
            $sheet->setCellValue('G'.$row, $record['user_agent']);
            $sheet->setCellValue('H'.$row, date('d/m/Y H:i', strtotime($record['created_at'])));
            $row++;
        }
        
        // Auto-size columns
        foreach(range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="activity_log_'.date('Y-m-d').'.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}

// Handle the export
$exporter = new ActivityLogExporter($conn);

$filters = [
    'start_date' => $_POST['start_date'] ?? null,
    'end_date' => $_POST['end_date'] ?? null
];

$data = $exporter->getActivityData($filters);
$exporter->exportToExcel($data);
