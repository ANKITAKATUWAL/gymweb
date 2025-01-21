<?php
require_once '../config/config.php';
require_once '../includes/db_connection.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');
checkAdminRole();

if (!isset($_POST['subscription_id']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$subscription_id = intval($_POST['subscription_id']);
$action = $_POST['action'];

if (!in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit();
}

try {
    // Start transaction
    mysqli_begin_transaction($con);

    // Update subscription status
    $status = $action === 'approve' ? 'Approved' : 'Rejected';
    $sql = "UPDATE subscriptions SET 
            payment_status = ?,
            approval_date = NOW(),
            approved_by = ?
            WHERE subscription_id = ? AND payment_status = 'Paid'";
            
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sis", $status, $_SESSION['user_id'], $subscription_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to update subscription status");
    }

    if (mysqli_affected_rows($con) === 0) {
        throw new Exception("Subscription not found or already processed");
    }

    // Commit transaction
    mysqli_commit($con);
    
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Rollback transaction
    mysqli_rollback($con);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 