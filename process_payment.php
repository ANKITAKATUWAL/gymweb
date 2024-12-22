<?php
require_once 'config/config.php';
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log function
function logError($message) {
    error_log(date('Y-m-d H:i:s') . " - Payment Error: " . $message . "\n", 3, "payment_errors.log");
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logError("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    exit(json_encode(['success' => false, 'error' => 'Method Not Allowed']));
}

try {
    $input = file_get_contents('php://input');
    logError("Raw input: " . $input); // Log raw input
    
    $input = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON decode error: " . json_last_error_msg());
    }
    
    logError("Decoded input: " . print_r($input, true)); // Log decoded input
    
    $subscription_id = intval($input['subscription_id']);
    $amount = intval($input['amount']); // Amount in paisa

    // Validate amount
    if ($amount < 1000 || $amount > 100000) {
        throw new Exception("Invalid amount. Must be between Rs. 10 and Rs. 1000");
    }

    // Initialize Khalti payment
    $curl = curl_init();
    $postData = [
        'return_url' => SITE_URL . '/payment_verify.php',
        'website_url' => SITE_URL,
        'amount' => $amount,
        'purchase_order_id' => $subscription_id,
        'purchase_order_name' => "Gym Subscription #" . $subscription_id,
        'customer_info' => $input['customer_info']
    ];
    
    logError("Khalti request data: " . print_r($postData, true)); // Log Khalti request
    
    curl_setopt_array($curl, [
        CURLOPT_URL => KHALTI_API_URL . 'initiate/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => [
            'Authorization: Key ' . KHALTI_SECRET_KEY,
            'Content-Type: application/json'
        ]
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    logError("Khalti response: " . $response); // Log Khalti response
    if ($err) {
        logError("Curl error: " . $err);
    }
    
    curl_close($curl);

    if ($err) {
        throw new Exception("Curl Error: " . $err);
    }

    $result = json_decode($response, true);
    
    if (isset($result['payment_url'])) {
        echo json_encode(['success' => true, 'payment_url' => $result['payment_url']]);
    } else {
        throw new Exception($result['message'] ?? "Payment initialization failed");
    }

} catch (Exception $e) {
    logError("Exception: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 