<?php
require_once 'config/config.php';
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

if (isset($_GET['pidx'])) {
    $pidx = $_GET['pidx'];
    
    // Verify payment status
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => KHALTI_API_URL . 'lookup/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['pidx' => $pidx]),
        CURLOPT_HTTPHEADER => [
            'Authorization: Key ' . KHALTI_SECRET_KEY,
            'Content-Type: application/json'
        ]
    ]);

    $response = curl_exec($curl);
    $result = json_decode($response, true);

    if ($result['status'] === 'Completed') {
        // Update subscription status
        $subscription_id = $result['purchase_order_id'];
        $sql = "UPDATE subscriptions SET payment_status = 'Paid' WHERE subscription_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $subscription_id);
        mysqli_stmt_execute($stmt);

        redirectTo(SITE_URL . '/user/dashboard.php?payment=success');
    } else {
        redirectTo(SITE_URL . '/user/dashboard.php?payment=failed');
    }
} else {
    redirectTo(SITE_URL . '/user/dashboard.php?payment=failed');
} 