<?php
// Start output buffering
ob_start();

require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';
require_once '../../vendor/autoload.php';


$dataType = isset($_POST['data_type']) ? (array)$_POST['data_type'] : ['user_activity'];
generateReport($_POST['export_type'] ?? 'excel', $_POST['date_range'] ?? 'today', $dataType);


function generateReport($type, $dateRange, $dataType) {
    global $conn;
    
    // Set date range
    switch($dateRange) {
        case 'today':
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
            break;
        case 'week':
            $startDate = date('Y-m-d', strtotime('-7 days'));
            $endDate = date('Y-m-d');
            break;
        case 'month':
            $startDate = date('Y-m-d', strtotime('-30 days'));
            $endDate = date('Y-m-d');
            break;
        case 'custom':
            $startDate = $_POST['start_date'];
            $endDate = $_POST['end_date'];
            break;
    }
    
    // Fetch data based on type
    $data = [];
    foreach($dataType as $dt) {
        switch($dt) {
            case 'user_activity':
                $query = "SELECT * FROM admin_activity_log 
                         WHERE DATE(created_at) BETWEEN ? AND ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $startDate, $endDate);
                $stmt->execute();
                $result = $stmt->get_result();
                $data['user_activity'] = $result->fetch_all(MYSQLI_ASSOC);
                break;
                
            case 'system_performance':
                // Add system performance data
                break;
                
            case 'security_logs':
                // Add security logs data
                break;
        }
    }
    
    // Generate file based on type
    switch($type) {
        case 'pdf':
            generatePDF($data);
            break;
        case 'excel':
            generateExcel($data);
            break;
        case 'csv':
            generateCSV($data);
            break;
    }
}

function generatePDF($data) {
    require_once '../../vendor/autoload.php';
    
    // Create new PDF document
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
    
    // Set document information
    $pdf->SetCreator('Zamfara CRS');
    $pdf->SetAuthor('Admin');
    $pdf->SetTitle('Activity Log Report');
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(true);
    $pdf->setFooterData(array(0,64,0), array(0,64,128));
    
    // Set margins
    $pdf->SetMargins(10, 15, 10);
    
    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    // Add page
    $pdf->AddPage();
    
    // Set font styles
    $pdf->SetFont('helvetica', 'B', 18);
    
    // Title
    $pdf->Cell(0, 10, 'Zamfara Crime Reporting System', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 14);
    $pdf->Cell(0, 10, 'Analytics Report - ' . date('F d, Y'), 0, 1, 'C');
    $pdf->Ln(5);
    
    foreach($data as $type => $records) {
        if(!empty($records)) {
            // Section header
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->SetFillColor(40, 88, 145);
            $pdf->SetTextColor(255);
            $pdf->Cell(0, 10, strtoupper($type) . ' REPORT', 0, 1, 'L', true);
            $pdf->Ln(2);
            
            // Table header
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->SetTextColor(0);
            
            // Define column widths
            $widths = array(15, 25, 30, 60, 25, 35, 50, 40);
            $headers = array('ID', 'Admin ID', 'Action', 'Details', 'Severity', 'IP Address', 'User Agent', 'Created At');
            
            // Print headers
            foreach($headers as $key => $header) {
                $pdf->Cell($widths[$key], 8, $header, 1, 0, 'C', true);
            }
            $pdf->Ln();
            
            // Table data
            $pdf->SetFont('helvetica', '', 9);
            $fill = false;
            
            foreach($records as $row) {
                $pdf->Cell($widths[0], 6, $row['id'], 1, 0, 'C', $fill);
                $pdf->Cell($widths[1], 6, $row['admin_id'], 1, 0, 'C', $fill);
                $pdf->Cell($widths[2], 6, $row['action'], 1, 0, 'L', $fill);
                $pdf->Cell($widths[3], 6, substr($row['details'], 0, 40), 1, 0, 'L', $fill);
                $pdf->Cell($widths[4], 6, $row['severity'], 1, 0, 'C', $fill);
                $pdf->Cell($widths[5], 6, $row['ip_address'], 1, 0, 'C', $fill);
                $pdf->Cell($widths[6], 6, substr($row['user_agent'], 0, 30), 1, 0, 'L', $fill);
                $pdf->Cell($widths[7], 6, $row['created_at'], 1, 0, 'C', $fill);
                $pdf->Ln();
                $fill = !$fill;
            }
            $pdf->Ln(10);
        }
    }
    
    // Output PDF
    $pdf->Output('activity_log_report.pdf', 'D');
}


function generateCSV($data) {
    ob_clean();
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="analytics_report_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    foreach($data as $type => $records) {
        fputcsv($output, [strtoupper($type) . ' REPORT']);
        
        if(!empty($records)) {
            fputcsv($output, array_keys($records[0]));
            
            foreach($records as $record) {
                $formattedRecord = array_map(function($value) {
                    return trim(strip_tags($value));
                }, $record);
                fputcsv($output, $formattedRecord);
            }
            
            fputcsv($output, []);
        }
    }
    
    fclose($output);
    exit();
}

function generateExcel($data) {
    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set column widths
    $sheet->getColumnDimension('A')->setWidth(10);  // id
    $sheet->getColumnDimension('B')->setWidth(15);  // admin_id
    $sheet->getColumnDimension('C')->setWidth(20);  // action
    $sheet->getColumnDimension('D')->setWidth(50);  // details
    $sheet->getColumnDimension('E')->setWidth(15);  // severity
    $sheet->getColumnDimension('F')->setWidth(20);  // ip_address
    $sheet->getColumnDimension('G')->setWidth(80);  // user_agent
    $sheet->getColumnDimension('H')->setWidth(25);  // created_at
    
    $rowCount = 1;
    
    foreach($data as $type => $records) {
        // Add title
        $sheet->setCellValue('A'.$rowCount, strtoupper($type) . ' REPORT');
        $sheet->getStyle('A'.$rowCount)->getFont()->setBold(true)->setSize(14);
        $rowCount += 3;
        
        if(!empty($records)) {
            // Add headers
            $columns = array_keys($records[0]);
            foreach($columns as $index => $column) {
                $colLetter = chr(65 + $index); // Convert number to letter (A, B, C, etc.)
                $sheet->setCellValue($colLetter.$rowCount, $column);
                $sheet->getStyle($colLetter.$rowCount)->getFont()->setBold(true);
            }
            $rowCount++;
            
            // Add data
            foreach($records as $record) {
                $colIndex = 0;
                foreach($record as $value) {
                    $colLetter = chr(65 + $colIndex);
                    
                    // Format date for created_at column
                    if($colIndex == 7) { // created_at is the 8th column (index 7)
                        $date = new DateTime($value);
                        $value = $date->format('d/m/Y H:i');
                    }
                    
                    $sheet->setCellValue($colLetter.$rowCount, $value);
                    $colIndex++;
                }
                $rowCount++;
            }
        }
    }
    
    // Set title row style
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
    // Create Excel writer
    $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
    // Set headers for download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="analytics_report_'.date('Y-m-d').'.xlsx"');
    header('Cache-Control: max-age=0');
    
    // Output the file
    $writer->save('php://output');
    exit();
}


// Keep your POST handling code at the end
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['export_type'];
    $dateRange = $_POST['date_range'];
    $dataType = is_array($_POST['data_type']) ? $_POST['data_type'] : [$_POST['data_type']];
    
    generateReport($type, $dateRange, $dataType);
}