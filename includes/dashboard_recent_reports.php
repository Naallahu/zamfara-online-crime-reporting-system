<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Reports</h5>
                <a href="reports.php" class="btn btn-primary btn-sm">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Crime Type</th>
                                <th>Location</th>
                                <th>Date Reported</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($report = $recent_reports->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo formatReferenceNumber($report['reference_number']); ?></td>
                                <td><?php echo ucfirst($report['crime_type']); ?></td>
                                <td><?php echo sanitizeOutput($report['location']); ?></td>
                                <td><?php echo formatDate($report['created_at']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getStatusColor($report['status']); ?>">
                                        <?php echo ucfirst($report['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="view_report.php?id=<?php echo $report['id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
