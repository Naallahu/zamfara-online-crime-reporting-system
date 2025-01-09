<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
include '../../includes/admin_auth.php';
include '../../includes/admin_header.php';

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = '';
if ($search) {
    $where = " WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?";
}

// Pagination
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Count total users for pagination
$count_sql = "SELECT COUNT(*) as total FROM users" . $where;
$count_stmt = $conn->prepare($count_sql);
if ($search) {
    $searchParam = "%$search%";
    $count_stmt->bind_param('sss', $searchParam, $searchParam, $searchParam);
}
$count_stmt->execute();
$total_users = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_users / $limit);

// Fetch users
$sql = "SELECT * FROM users" . $where . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if ($search) {
    $searchParam = "%$search%";
    $stmt->bind_param('sssii', $searchParam, $searchParam, $searchParam, $limit, $offset);
} else {
    $stmt->bind_param('ii', $limit, $offset);
}
$stmt->execute();
$users = $stmt->get_result();
?>

<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">User Management</h5>
            <form class="d-flex gap-2" method="GET">
                <input type="search" name="search" class="form-control" 
                       placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['name']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['phone']; ?></td>
                            <td>
                                <select class="form-select form-select-sm" onchange="updateRole(<?php echo $user['id']; ?>, this.value)">
                                    <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                                    <option value="police" <?php echo $user['role'] == 'police' ? 'selected' : ''; ?>>Police</option>
                                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $user['status'] == 'active' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewUser(<?php echo $user['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="toggleStatus(<?php echo $user['id']; ?>)">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                    </li>
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Name:</strong> <span id="userName"></span>
                </div>
                <div class="mb-3">
                    <strong>Email:</strong> <span id="userEmail"></span>
                </div>
                <div class="mb-3">
                    <strong>Phone:</strong> <span id="userPhone"></span>
                </div>
                <div class="mb-3">
                    <strong>Role:</strong> <span id="userRole"></span>
                </div>
                <div class="mb-3">
                    <strong>Status:</strong> <span id="userStatus"></span>
                </div>
                <div class="mb-3">
                    <strong>Joined:</strong> <span id="userJoined"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewUser(userId) {
    fetch(`view_user.php?id=${userId}`)
        .then(response => response.json())
        .then(user => {
            document.getElementById('userName').textContent = user.name;
            document.getElementById('userEmail').textContent = user.email;
            document.getElementById('userPhone').textContent = user.phone;
            document.getElementById('userRole').textContent = user.role;
            document.getElementById('userStatus').textContent = user.status;
            document.getElementById('userJoined').textContent = new Date(user.created_at).toLocaleDateString();
            new bootstrap.Modal(document.getElementById('userModal')).show();
        });
}

function updateRole(userId, newRole) {
    fetch('update_role.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId,
            role: newRole
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        }
    });
}

function toggleStatus(userId) {
    fetch('update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        }
    });
}
</script>

<?php include '../../includes/admin_footer.php'; ?>
