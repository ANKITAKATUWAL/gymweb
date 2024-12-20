<?php
$page_title = 'Payment';
require_once '../includes/functions.php';
require_once '../includes/header.php';
checkLoggedIn();

if (!isset($_GET['subscription_id'])) {
    redirectTo(SITE_URL . '/user/membership.php');
}

$subscription_id = intval($_GET['subscription_id']);

// Get subscription details
$sql = "SELECT s.*, g.plan_name, g.plan_price 
        FROM subscriptions s 
        JOIN gym_plans g ON s.plan_id = g.plan_id 
        WHERE s.subscription_id = ? AND s.user_id = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ii", $subscription_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$subscription = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$subscription) {
    redirectTo(SITE_URL . '/user/membership.php');
}
?>

<div class="container">
    <div class="payment-section">
        <h1>Complete Payment</h1>
        
        <div class="payment-details">
            <h3>Plan: <?php echo htmlspecialchars($subscription['plan_name']); ?></h3>
            <p>Amount: Rs. <?php echo number_format($subscription['plan_price'], 2); ?></p>
        </div>
        
        <button id="payment-button" class="btn btn-primary">Pay with Khalti</button>
    </div>
</div>

<!-- Khalti SDK -->
<script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.22.0.0.0/khalti-checkout.iffe.js"></script>
<script>
    var config = {
        "publicKey": "<?php echo KHALTI_PUBLIC_KEY; ?>",
        "productIdentity": "<?php echo $subscription_id; ?>",
        "productName": "<?php echo $subscription['plan_name']; ?>",
        "productUrl": "<?php echo SITE_URL; ?>",
        "amount": <?php echo $subscription['plan_price'] * 100; ?>,
        "eventHandler": {
            onSuccess: handlePaymentSuccess,
            onError: handlePaymentError
        }
    };
</script>
<script src="<?php echo SITE_URL; ?>/assets/js/khalti.js"></script>
<script>initializeKhaltiPayment(config);</script>

<?php require_once '../includes/footer.php'; ?> 