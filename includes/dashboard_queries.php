<?php
function getDashboardStats($conn) {
    return [
        'total_reports' => $conn->query("SELECT COUNT(*) FROM reports")->fetch_row()[0],
        'pending' => $conn->query("SELECT COUNT(*) FROM reports WHERE status='pending'")->fetch_row()[0],
        'investigating' => $conn->query("SELECT COUNT(*) FROM reports WHERE status='investigating'")->fetch_row()[0],
        'resolved' => $conn->query("SELECT COUNT(*) FROM reports WHERE status='resolved'")->fetch_row()[0]
    ];
}

function getStatusCount($conn, $status) {
    $query = "SELECT COUNT(*) as count FROM reports WHERE status = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $status);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result)['count'];
}

function getRecentActivities($conn) {
    return mysqli_query($conn, "SELECT * FROM admin_activity_log ORDER BY created_at DESC LIMIT 5");
}

function getRecentReports($conn) {
    return $conn->query("SELECT * FROM reports ORDER BY created_at DESC LIMIT 5");
}

function getCrimeTypesData($conn) {
    return $conn->query("SELECT crime_type, COUNT(*) as count 
                        FROM reports 
                        GROUP BY crime_type");
}

function getMonthlyTrends($conn) {
    return $conn->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                        COUNT(*) as count 
                        FROM reports 
                        GROUP BY month 
                        ORDER BY month DESC 
                        LIMIT 6");
}

function getLGAStats($conn) {
    return $conn->query("SELECT lga, COUNT(*) as count 
                        FROM reports 
                        GROUP BY lga 
                        ORDER BY count DESC");
}

function getStatusDistribution($conn) {
    return $conn->query("SELECT status, COUNT(*) as count 
                        FROM reports 
                        GROUP BY status");
}
