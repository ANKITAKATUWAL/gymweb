<?php
$page_title = 'Manage Subscriptions';
require_once '../includes/functions.php';
require_once '../includes/header.php';
checkAdminRole();

$subscriptions_sql = "SELECT s.*, u.full_name, u.email, g.plan_name, p.amount 
                     FROM subscriptions s
                     JOIN users u ON s.user_id = u.user_id
                     JOIN gym_plans g ON s.plan_id = g.plan_id
                     LEFT JOIN payments p ON s.subscription_id = p.subscription_id
                     ORDER BY s.subscription_date DESC";
$subscriptions_result = mysqli_query($con, $subscriptions_sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subscription_id = intval($_POST['subscription_id']);
    $action = $_POST['action'];
    
    if ($action === 'approve') {
        $update_sql = "UPDATE subscriptions SET payment_status = 'Approved' WHERE subscription_id = ?";
    } else if ($action === 'reject') {
        $update_sql = "UPDATE subscriptions SET payment_status = 'Rejected' WHERE subscription_id = ?";
    }
    
    if (isset($update_sql)) {
        $stmt = mysqli_prepare($con, $update_sql);
        mysqli_stmt_bind_param($stmt, "i", $subscription_id);
        
        if (mysqli_stmt_execute($stmt)) {
            redirectTo(SITE_URL . '/admin/manage_subscriptions.php');
        }
    }
}
?>

<div class="container">
    <h1>Manage Subscriptions</h1>
    
    <table class="table">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Plan</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($sub = mysqli_fetch_assoc($subscriptions_result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($sub['full_name']); ?></td>
                <td><?php echo htmlspecialchars($sub['email']); ?></td>
                <td><?php echo htmlspecialchars($sub['plan_name']); ?></td>
                <td>Rs. <?php echo htmlspecialchars($sub['amount']); ?></td>
                <td><?php echo date('Y-m-d', strtotime($sub['subscription_date'])); ?></td>
                <td><?php echo htmlspecialchars($sub['payment_status']); ?></td>
                <td>
                    <?php if ($sub['payment_status'] === 'Paid'): ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="subscription_id" value="<?php echo $sub['subscription_id']; ?>">
                        <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?> 