<?php
require_once 'config/config.php';
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

// Enable error logging
function logError($message) {
    error_log(date('Y-m-d H:i:s') . " - Verification Error: " . $message . "\n", 3, "payment_verify_errors.log");
}

try {
    logError("Received parameters: " . print_r($_GET, true));

    if (!isset($_GET['pidx']) || !isset($_GET['purchase_order_id']) || !isset($_GET['status'])) {
        throw new Exception("Missing required parameters");
    }

    $pidx = $_GET['pidx'];
    $subscription_id = intval($_GET['purchase_order_id']);
    $status = $_GET['status'];
    $amount = isset($_GET['amount']) ? intval($_GET['amount']) : 0;

    if ($status !== 'Completed') {
        throw new Exception("Payment not completed. Status: " . $status);
    }

    // Verify payment with Khalti
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => KHALTI_API_URL . 'epayment/lookup/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'pidx' => $pidx
        ]),
        CURLOPT_HTTPHEADER => [
            'Authorization: Key ' . KHALTI_SECRET_KEY,
            'Content-Type: application/json'
        ]
    ]);

    $response = curl_exec($curl);
    logError("Khalti Lookup Response: " . $response);
    
    if (curl_errno($curl)) {
        throw new Exception("Curl Error: " . curl_error($curl));
    }
    
    curl_close($curl);
    
    $result = json_decode($response, true);
    
    if (!isset($result['status']) || $result['status'] !== 'Completed') {
        throw new Exception("Payment verification failed");
    }

    // Update subscription status
    $sql = "UPDATE subscriptions SET 
            payment_status = 'Paid',
            payment_token = ?,
            payment_date = NOW() 
            WHERE subscription_id = ?";
            
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt === false) {
        throw new Exception("Database Error: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, "si", $pidx, $subscription_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to update subscription: " . mysqli_error($con));
    }

    // Redirect to success page
    redirectTo(SITE_URL . "/payment_success.php?subscription_id=" . $subscription_id);

} catch (Exception $e) {
    logError("Error: " . $e->getMessage());
    redirectTo(SITE_URL . "/user/dashboard.php?payment=failed&error=" . urlencode($e->getMessage()));
} 