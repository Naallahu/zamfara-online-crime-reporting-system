<?php
require_once '../../includes/config.php';
require_once '../../classes/Database.php';
require_once '../../includes/admin_auth.php';

$db = new Database();
$conn = $db->connect();

// Fetch all users
$query = "SELECT id, name, email, status, created_at FROM users ORDER BY created_at DESC";
$users = $conn->query($query);

include '../../includes/admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-6">
            <h2>Manage Users</h2>
        </div>
        <div class="col-6 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus"></i> Add New User
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $users->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editUser(<?php echo $user['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'user_forms.php'; ?>

<script src="../../assets/js/user-management.js"></script>
<script src="../../assets/js/user-validation.js"></script>

<?php include '../../includes/admin_footer.php'; ?>
