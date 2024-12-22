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

// Prepare data for JavaScript
$paymentData = [
    'subscription_id' => $subscription_id,
    'amount' => $subscription['plan_price'] * 100,
    'customer_info' => [
        'name' => $_SESSION['full_name'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'phone' => $_SESSION['phone_number'] ?? ''
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

<script>
// Pass PHP data to JavaScript safely
var paymentData = <?php echo json_encode($paymentData); ?>;
var siteUrl = <?php echo json_encode(SITE_URL); ?>;

$(document).ready(function() {
    console.log('Document ready');
    
    $('#payment-button').on('click', function(e) {
        e.preventDefault();
        console.log('Payment button clicked');
        
        var $btn = $(this);
        var $status = $('#payment-status');
        
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');
        $status.html('Initializing payment...');
        
        console.log('Payment data:', paymentData);
        
        $.ajax({
            url: siteUrl + '/process_payment.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(paymentData),
            success: function(response) {
                console.log('Success response:', response);
                if (response.payment_url) {
                    $status.html('Redirecting to payment page...');
                    window.location.href = response.payment_url;
                } else {
                    throw new Error(response.message || 'Payment initialization failed');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', {xhr: xhr, status: status, error: error});
                $status.html('Payment failed: ' + error);
                $btn.prop('disabled', false).html('Pay with Khalti');
            }
        });
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>