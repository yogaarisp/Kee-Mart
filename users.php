<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireAdmin();

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Prevent deleting self
    if ($id === $_SESSION['user_id']) {
        flashMessage('Cannot delete your own account', 'danger');
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id = $id");
        flashMessage('User deleted successfully');
    }
    redirect('users.php');
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $username = sanitize($_POST['username']);
    $role = $_POST['role'];
    
    // Check if username exists (for new users)
    if (!$id) {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
        if (mysqli_num_rows($check) > 0) {
            flashMessage('Username already exists', 'danger');
            redirect('users.php');
        }
    }
    
    if ($id) {
        // Update
        $query = "UPDATE users SET username = '$username', role = '$role'";
        
        // Update password if provided
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $query .= ", password = '$password'";
        }
        
        $query .= " WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            flashMessage('User updated successfully');
        } else {
            flashMessage('Error updating user', 'danger');
        }
    } else {
        // Insert new user
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
        
        if (mysqli_query($conn, $query)) {
            flashMessage('User added successfully');
        } else {
            flashMessage('Error adding user', 'danger');
        }
    }
    redirect('users.php');
}

// Get all users
$query = "SELECT * FROM users ORDER BY username";
$users = mysqli_query($conn, $query);
?>

<?php include 'views/common/header.php'; ?>

<div class="content-header">
    <h1>Users Management</h1>
    <button class="btn btn-primary" onclick="showAddForm()">Add New User</button>
</div>

<!-- Add/Edit User Form -->
<div id="userForm" class="card" style="display: none; margin-bottom: 20px;">
    <h3 id="formTitle">Add New User</h3>
    <form method="POST">
        <input type="hidden" name="id" id="user_id">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" id="password" class="form-control">
            <small class="form-text text-muted" id="passwordHelp">Leave empty to keep current password when editing</small>
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role" id="role" class="form-control" required>
                <option value="cashier">Cashier</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Save User</button>
        <button type="button" class="btn btn-secondary" onclick="hideForm()">Cancel</button>
    </form>
</div>

<!-- Users Table -->
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo ucfirst($user['role']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                    <td>
                        <button class="btn btn-primary" onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">Edit</button>
                        <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                            <a href="users.php?delete=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function showAddForm() {
    document.getElementById('formTitle').textContent = 'Add New User';
    document.getElementById('userForm').style.display = 'block';
    document.getElementById('user_id').value = '';
    document.getElementById('username').value = '';
    document.getElementById('password').value = '';
    document.getElementById('role').value = 'cashier';
    document.getElementById('password').required = true;
    document.getElementById('passwordHelp').style.display = 'none';
}

function hideForm() {
    document.getElementById('userForm').style.display = 'none';
}

function editUser(user) {
    document.getElementById('formTitle').textContent = 'Edit User';
    document.getElementById('userForm').style.display = 'block';
    document.getElementById('user_id').value = user.id;
    document.getElementById('username').value = user.username;
    document.getElementById('password').value = '';
    document.getElementById('role').value = user.role;
    document.getElementById('password').required = false;
    document.getElementById('passwordHelp').style.display = 'block';
}
</script>

<?php include 'views/common/footer.php'; ?>
