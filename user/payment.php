<?php
require_once '../config/config.php';
require_once '../includes/db_connection.php';
require_once '../includes/functions.php';

$page_title = 'Payment';
require_once '../includes/header.php';
checkLoggedIn();

if (!isset($_GET['subscription_id'])) {
    redirectTo(SITE_URL . '/user/membership.php');
}

$subscription_id = intval($_GET['subscription_id']);

// Get subscription details
$sql = "SELECT s.*, g.plan_name, g.plan_price, u.full_name, u.email, u.phone_number 
        FROM subscriptions s
        JOIN gym_plans g ON s.plan_id = g.plan_id
        JOIN users u ON s.user_id = u.user_id
        WHERE s.subscription_id = ?";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $subscription_id);
mysqli_stmt_execute($stmt);
$subscription = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$subscription) {
    redirectTo(SITE_URL . '/user/membership.php');
}

// Prepare payment data
$paymentData = [
    'subscription_id' => $subscription_id,
    'amount' => $subscription['plan_price'] * 100, // Convert to paisa
    'customer_info' => [
        'name' => $subscription['full_name'],
        'email' => $subscription['email'],
        'phone' => $subscription['phone_number']
    ]
];
?>

<div class="container mt-5">
    <div class="payment-section">
        <h1>Complete Payment</h1>
        
        <div class="payment-details card p-4 mb-4">
            <h3>Plan: <?php echo htmlspecialchars($subscription['plan_name']); ?></h3>
            <p class="mb-0">Amount: Rs. <?php echo number_format($subscription['plan_price'], 2); ?></p>
        </div>
        
        <button id="payment-button" class="btn btn-primary btn-lg">Pay with Khalti</button>
        <div id="payment-status"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.22.0.0.0/khalti-checkout.iffe.js"></script>

<script>
var paymentData = {
    subscription_id: <?php echo $subscription_id; ?>,
    amount: <?php echo $subscription['plan_price'] * 100; ?>,
    customer_info: {
        name: "<?php echo addslashes($subscription['full_name']); ?>",
        email: "<?php echo addslashes($subscription['email']); ?>",
        phone: "<?php echo addslashes($subscription['phone_number']); ?>"
    }
};

$('#payment-button').click(function() {
    $.ajax({
        url: '<?php echo SITE_URL; ?>/process_payment.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(paymentData),
        success: function(response) {
            if (response.success && response.payment_url) {
                window.location.href = response.payment_url;
            } else {
                $('#payment-status').html('<div class="alert alert-danger">Payment initialization failed</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            $('#payment-status').html('<div class="alert alert-danger">Payment initialization failed</div>');
        }
    });
});
</script>

<?php
require_once '../includes/footer.php';