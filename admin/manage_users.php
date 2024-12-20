<?php
$page_title = 'Manage Users';
require_once '../includes/functions.php';
require_once '../includes/header.php';
checkAdminRole();

$users_sql = "SELECT * FROM users WHERE role = 'user'";
$users_result = mysqli_query($con, $users_sql);

if (isset($_GET['delete_id'])) {
    $user_id = intval($_GET['delete_id']);
    $delete_sql = "DELETE FROM users WHERE user_id = ? AND role = 'user'";
    $stmt = mysqli_prepare($con, $delete_sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        redirectTo(SITE_URL . '/admin/manage_users.php');
    }
}
?>

<div class="container">
    <h1>Manage Users</h1>
    
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Registration Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                <td><?php echo date('Y-m-d', strtotime($user['registration_date'])); ?></td>
                <td>
                    <a href="?delete_id=<?php echo $user['user_id']; ?>" 
                       class="btn btn-danger"
                       onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?> 