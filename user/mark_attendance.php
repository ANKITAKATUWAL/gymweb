<?php
session_start();
require_once '../includes/functions.php';
require_once '../includes/db_connection.php';
checkLoggedIn();

// Set proper headers for JSON response
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // First check for active subscription
    $subscription_check = "SELECT s.* FROM subscriptions s 
                          JOIN gym_plans g ON s.plan_id = g.plan_id 
                          WHERE s.user_id = ? 
                          AND s.payment_status = 'Approved'
                          AND s.subscription_date <= NOW() 
                          AND DATE_ADD(s.subscription_date, INTERVAL g.plan_duration MONTH) > NOW()";
    
    $stmt = mysqli_prepare($con, $subscription_check);
    if (!$stmt) {
        throw new Exception("Failed to prepare subscription check: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => false, 'message' => 'No active subscription found']);
        exit();
    }

    // Check if already marked attendance today
    $check_sql = "SELECT * FROM attendance WHERE user_id = ? AND DATE(check_in_time) = CURRENT_DATE()";
    $stmt = mysqli_prepare($con, $check_sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare attendance check: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => false, 'message' => 'Attendance already marked for today']);
        exit();
    }

    // Insert new attendance record
    $insert_sql = "INSERT INTO attendance (user_id, check_in_time) VALUES (?, NOW())";
    $stmt = mysqli_prepare($con, $insert_sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare insert: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to insert attendance: " . mysqli_stmt_error($stmt));
    }
    
    echo json_encode(['success' => true, 'message' => 'Attendance marked successfully']);
    
} catch (Exception $e) {
    error_log("Attendance marking error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to mark attendance. Please try again.']);
} 