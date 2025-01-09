<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
include '../../includes/admin_auth.php';
require_once '../../vendor/autoload.php'; // Make sure you have TCPDF installed

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Zamfara Crime Reporting System');
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Crime Reports');

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Crime Reports', date('Y-m-d'));

// Add a page
$pdf->AddPage();

// Fetch reports
$sql = "SELECT r.*, u.name as reporter_name, ps.name as station_name 
        FROM reports r 
        LEFT JOIN users u ON r.user_id = u.id 
        LEFT JOIN police_stations ps ON r.station_id = ps.id 
        ORDER BY r.created_at DESC";
$result = $conn->query($sql);

// Create the table content
$html = '<table border="1" cellpadding="4">
    <tr style="background-color: #f0f0f0;">
        <th>ID</th>
        <th>Reporter</th>
        <th>Crime Type</th>
        <th>Location</th>
        <th>Status</th>
        <th>Date</th>
    </tr>';

while($row = $result->fetch_assoc()) {
    $html .= '<tr>
        <td>'.$row['id'].'</td>
        <td>'.$row['reporter_name'].'</td>
        <td>'.ucfirst($row['crime_type']).'</td>
        <td>'.$row['location'].'</td>
        <td>'.ucfirst($row['status']).'</td>
        <td>'.date('Y-m-d', strtotime($row['created_at'])).'</td>
    </tr>';
}

$html .= '</table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('crime_reports_'.date('Y-m-d').'.pdf', 'D');
