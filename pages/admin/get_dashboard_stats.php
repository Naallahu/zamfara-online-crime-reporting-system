<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/admin_auth.php';

// Get updated statistics with optimized queries
$stats = [
    'pending' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reports WHERE status='pending'"))['count'],
    'inProgress' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reports WHERE status='in_progress'"))['count'],
    'resolved' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reports WHERE status='resolved'"))['count']
];

// Calculate additional metrics
$total = $stats['pending'] + $stats['inProgress'] + $stats['resolved'];
$stats['responseRate'] = $total > 0 ? round(($stats['resolved'] / $total) * 100) : 0;

// Add trend analysis
$lastMonth = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as count 
    FROM reports 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
"))['count'];

$stats['monthlyGrowth'] = $lastMonth;
$stats['averageResolutionTime'] = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_time 
    FROM reports 
    WHERE status = 'resolved'
"))['avg_time'];

// Return enhanced JSON response
header('Content-Type: application/json');
echo json_encode($stats);