<?php
// Disable error reporting for headers
error_reporting(0);

// Start session without including header
session_start();

require_once '../config/config.php';
require_once '../includes/db_connection.php';
require_once '../includes/functions.php';

// Ensure we're sending JSON response
header('Content-Type: application/json');

// Check admin role without header inclusion
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

function logError($message) {
    error_log(date('Y-m-d H:i:s') . " - Admin Process Error: " . $message . "\n", 3, "admin_process_errors.log");
}

try {
    if (!isset($_POST['subscription_id']) || !isset($_POST['action'])) {
        throw new Exception('Missing required parameters');
    }

    $subscription_id = intval($_POST['subscription_id']);
    $action = $_POST['action'];

    if (!in_array($action, ['approve', 'reject'])) {
        throw new Exception('Invalid action');
    }

    // Start transaction
    mysqli_begin_transaction($con);

    // First check if subscription exists and is in correct state
    $check_sql = "SELECT payment_status FROM subscriptions WHERE subscription_id = ?";
    $check_stmt = mysqli_prepare($con, $check_sql);
    
    if (!$check_stmt) {
        throw new Exception("Database Error: " . mysqli_error($con));
    }

    mysqli_stmt_bind_param($check_stmt, "i", $subscription_id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    $subscription = mysqli_fetch_assoc($result);

    if (!$subscription) {
        throw new Exception("Subscription not found");
    }

    if ($subscription['payment_status'] !== 'Paid') {
        throw new Exception("Subscription is not in paid status");
    }

    // Update subscription status
    $status = $action === 'approve' ? 'Approved' : 'Rejected';
    $sql = "UPDATE subscriptions SET 
            payment_status = ?,
            approval_date = NOW(),
            approved_by = ?
            WHERE subscription_id = ?";
            
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        throw new Exception("Database Error: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, "sis", $status, $_SESSION['user_id'], $subscription_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to update subscription: " . mysqli_error($con));
    }

    mysqli_commit($con);
    echo json_encode(['success' => true, 'message' => "Subscription has been " . strtolower($status)]);

} catch (Exception $e) {
    if (isset($con) && mysqli_ping($con)) {
        mysqli_rollback($con);
    }
    
    logError($e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 