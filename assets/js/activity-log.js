function filterActivities() {
    // Show the filter modal
    var filterModal = new bootstrap.Modal(document.getElementById('filterModal'));
    filterModal.show();
}

function applyFilter() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const actionType = document.getElementById('actionType').value;
    
    // Build the query string
    let queryParams = [];
    if (startDate) queryParams.push(`start_date=${startDate}`);
    if (endDate) queryParams.push(`end_date=${endDate}`);
    if (actionType) queryParams.push(`action_type=${actionType}`);
    
    // Redirect with filters
    const queryString = queryParams.length ? '?' + queryParams.join('&') : '';
    window.location.href = 'activity_log.php' + queryString;
}

function exportActivityLog() {
    // Get current filter parameters
    const urlParams = new URLSearchParams(window.location.search);
    const startDate = urlParams.get('start_date') || '';
    const endDate = urlParams.get('end_date') || '';
    const actionType = urlParams.get('action_type') || '';
    
    // Build export URL with filters
    let exportUrl = 'export_activity_log.php';
    let queryParams = [];
    if (startDate) queryParams.push(`start_date=${startDate}`);
    if (endDate) queryParams.push(`end_date=${endDate}`);
    if (actionType) queryParams.push(`action_type=${actionType}`);
    
    if (queryParams.length) {
        exportUrl += '?' + queryParams.join('&');
    }
    
    window.location.href = exportUrl;
}
