<?php
$page_title = 'Payment Success';
require_once 'includes/functions.php';
require_once 'includes/header.php';

if (!isset($_GET['subscription_id'])) {
    redirectTo(SITE_URL . '/user/dashboard.php');
}

$subscription_id = intval($_GET['subscription_id']);

// Updated SQL query to include payment information
$sql = "SELECT s.*, g.plan_name, g.plan_price, g.plan_duration, 
               u.full_name, u.email, s.payment_token, s.payment_date
        FROM subscriptions s
        JOIN gym_plans g ON s.plan_id = g.plan_id
        JOIN users u ON s.user_id = u.user_id
        WHERE s.subscription_id = ?";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $subscription_id);
mysqli_stmt_execute($stmt);
$subscription = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$subscription) {
    redirectTo(SITE_URL . '/user/dashboard.php');
}
?>

<div class="receipt-container">
    <div class="receipt">
        <div class="receipt-header">
            <i class="fas fa-check-circle success-icon"></i>
            <h1>Payment Successful!</h1>
            <p>Thank you for your subscription</p>
        </div>

        <div class="receipt-details">
            <div class="receipt-row">
                <span>Transaction ID:</span>
                <span><?php echo !empty($subscription['payment_token']) ? 
                    htmlspecialchars($subscription['payment_token']) : 'Pending'; ?></span>
            </div>
            <div class="receipt-row">
                <span>Plan:</span>
                <span><?php echo htmlspecialchars($subscription['plan_name']); ?></span>
            </div>
            <div class="receipt-row">
                <span>Amount:</span>
                <span>Rs. <?php echo number_format($subscription['plan_price'], 2); ?></span>
            </div>
            <div class="receipt-row">
                <span>Duration:</span>
                <span><?php echo $subscription['plan_duration']; ?> months</span>
            </div>
            <div class="receipt-row">
                <span>Payment Date:</span>
                <span><?php echo !empty($subscription['payment_date']) ? 
                    date('F j, Y g:i A', strtotime($subscription['payment_date'])) : 
                    date('F j, Y g:i A'); ?></span>
            </div>
        </div>

        <div class="receipt-footer">
            <a href="<?php echo SITE_URL; ?>/user/dashboard.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Go to Dashboard
            </a>
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print"></i> Print Receipt
            </button>
        </div>
    </div>
</div> 