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
    $input = json_decode($input, true);
    
    if (!isset($input['subscription_id']) || !isset($input['amount']) || !isset($input['customer_info'])) {
        throw new Exception("Missing required parameters");
    }

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://dev.khalti.com/api/v2/epayment/initiate/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode([
            "return_url" => SITE_URL . "/payment_verify.php",
            "website_url" => SITE_URL,
            "amount" => strval($input['amount']),
            "purchase_order_id" => strval($input['subscription_id']),
            "purchase_order_name" => "Gym Subscription #" . $input['subscription_id'],
            "customer_info" => [
                "name" => $input['customer_info']['name'],
                "email" => $input['customer_info']['email'],
                "phone" => $input['customer_info']['phone']
            ]
        ]),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Key ' . KHALTI_SECRET_KEY,
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);
    logError("Khalti Response: " . $response);

    if (curl_errno($curl)) {
        logError("Curl Error: " . curl_error($curl));
        throw new Exception("Payment request failed");
    }

    curl_close($curl);
    
    $result = json_decode($response, true);
    
    if (isset($result['payment_url'])) {
        echo json_encode(['success' => true, 'payment_url' => $result['payment_url']]);
    } else {
        logError("Invalid Response: " . print_r($result, true));
        throw new Exception(isset($result['detail']) ? $result['detail'] : "Payment initialization failed");
    }

} catch (Exception $e) {
    logError("Exception: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 