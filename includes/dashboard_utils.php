<?php
function getStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'investigating' => 'info',
        'resolved' => 'success',
        'closed' => 'secondary'
    ];
    return $colors[$status] ?? 'primary';
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

function formatReferenceNumber($ref) {
    return strtoupper($ref);
}

function calculatePercentageChange($current, $previous) {
    if ($previous == 0) return 100;
    return round((($current - $previous) / $previous) * 100, 1);
}

function sanitizeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function getChartColors() {
    return [
        '#4e73df', // Primary
        '#1cc88a', // Success
        '#36b9cc', // Info
        '#f6c23e', // Warning
        '#e74a3b'  // Danger
    ];
}
