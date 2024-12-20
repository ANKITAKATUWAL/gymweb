<?php
require_once 'config/config.php';
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$token = sanitizeInput($_POST['token']);
$amount = floatval($_POST['amount']);
$subscription_id = intval($_POST['subscription_id']);

// Verify with Khalti API
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://khalti.com/api/v2/payment/verify/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'token' => $token,
        'amount' => $amount
    ]),
    CURLOPT_HTTPHEADER => [
        "Authorization: Key " . KHALTI_SECRET_KEY,
        "Content-Type: application/json"
    ]
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    http_response_code(500);
    exit(json_encode(['error' => $err]));
}

$result = json_decode($response, true);

if (isset($result['idx'])) {
    // Payment successful
    $payment_sql = "INSERT INTO payments (subscription_id, amount, khalti_token) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $payment_sql);
    mysqli_stmt_bind_param($stmt, "ids", $subscription_id, $amount, $token);
    
    if (mysqli_stmt_execute($stmt)) {
        // Update subscription status
        $update_sql = "UPDATE subscriptions SET payment_status = 'Paid' WHERE subscription_id = ?";
        $stmt = mysqli_prepare($con, $update_sql);
        mysqli_stmt_bind_param($stmt, "i", $subscription_id);
        mysqli_stmt_execute($stmt);
        
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Payment verification failed']);
}
?> 