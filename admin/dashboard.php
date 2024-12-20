<?php
$page_title = 'Admin Dashboard';
require_once '../includes/functions.php';
require_once '../includes/header.php';
checkAdminRole();

// Get statistics
$stats = [
    'total_users' => mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM users WHERE role = 'user'"))['count'],
    'active_subscriptions' => mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM subscriptions WHERE payment_status = 'Approved'"))['count'],
    'today_attendance' => mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(DISTINCT user_id) as count FROM attendance WHERE check_in_date = CURDATE()"))['count'],
    'total_revenue' => mysqli_fetch_assoc(mysqli_query($con, "SELECT COALESCE(SUM(amount), 0) as total FROM payments"))['total']
];

// Recent activities
$recent_subscriptions = mysqli_query($con, "
    SELECT s.*, u.full_name, g.plan_name 
    FROM subscriptions s
    JOIN users u ON s.user_id = u.user_id
    JOIN gym_plans g ON s.plan_id = g.plan_id
    ORDER BY s.subscription_date DESC LIMIT 5
");
?>

<div class="container">
    <h1>Admin Dashboard</h1>
    
    <div class="stats-cards">
        <div class="stat-card">
            <h3>Total Users</h3>
            <p><?php echo $stats['total_users']; ?></p>
        </div>
        <div class="stat-card">
            <h3>Active Subscriptions</h3>
            <p><?php echo $stats['active_subscriptions']; ?></p>
        </div>
        <div class="stat-card">
            <h3>Today's Attendance</h3>
            <p><?php echo $stats['today_attendance']; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Revenue</h3>
            <p>Rs. <?php echo number_format($stats['total_revenue'], 2); ?></p>
        </div>
    </div>

    <div class="recent-activities">
        <h2>Recent Subscriptions</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Plan</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($sub = mysqli_fetch_assoc($recent_subscriptions)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($sub['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($sub['plan_name']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($sub['subscription_date'])); ?></td>
                    <td><?php echo htmlspecialchars($sub['payment_status']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 