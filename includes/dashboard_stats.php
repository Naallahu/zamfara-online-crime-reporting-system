<?php
// Statistics Cards Component
$stat_cards = [
    [
        'id' => 'total-reports',
        'title' => 'Total Reports',
        'value' => $stats['total_reports'],
        'icon' => 'fa-file-alt',
        'color' => 'primary'
    ],
    [
        'id' => 'pending-cases',
        'title' => 'Pending Cases',
        'value' => $stats['pending'],
        'icon' => 'fa-clock',
        'color' => 'warning'
    ],
    [
        'id' => 'investigating-cases',
        'title' => 'Under Investigation',
        'value' => $stats['investigating'],
        'icon' => 'fa-search',
        'color' => 'info'
    ],
    [
        'id' => 'resolved-cases',
        'title' => 'Resolved Cases',
        'value' => $stats['resolved'],
        'icon' => 'fa-check-circle',
        'color' => 'success'
    ]
];

foreach ($stat_cards as $card): ?>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-<?php echo $card['color']; ?> text-white mb-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="display-4 mb-0" id="<?php echo $card['id']; ?>"><?php echo $card['value']; ?></h2>
                        <p class="mb-0"><?php echo $card['title']; ?></p>
                    </div>
                    <i class="fas <?php echo $card['icon']; ?> fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
function updateStats() {
    fetch('get_dashboard_stats.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-reports').textContent = data.total_reports;
            document.getElementById('pending-cases').textContent = data.pending;
            document.getElementById('investigating-cases').textContent = data.investigating;
            document.getElementById('resolved-cases').textContent = data.resolved;
        });
}

// Update stats every 30 seconds
setInterval(updateStats, 30000);
</script>
