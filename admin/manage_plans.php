<?php
require_once '../includes/functions.php';
require_once '../includes/header.php';
checkAdminRole();

// Move existing code from manage_membership.php here
// Update paths and queries

$sql = "SELECT * FROM gym_plans";
$result = mysqli_query($con, $sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_name = sanitizeInput($_POST['plan_name']);
    $description = sanitizeInput($_POST['description']);
    $price = floatval($_POST['price']);
    $duration = intval($_POST['duration']);

    $add_plan_sql = "INSERT INTO gym_plans (plan_name, plan_description, plan_price, plan_duration) 
                     VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $add_plan_sql);
    mysqli_stmt_bind_param($stmt, "ssdi", $plan_name, $description, $price, $duration);
    
    if (mysqli_stmt_execute($stmt)) {
        redirectTo(SITE_URL . '/admin/manage_plans.php');
    }
}

if (isset($_GET['delete_id'])) {
    $plan_id = intval($_GET['delete_id']);
    $delete_sql = "DELETE FROM gym_plans WHERE plan_id = ?";
    $stmt = mysqli_prepare($con, $delete_sql);
    mysqli_stmt_bind_param($stmt, "i", $plan_id);
    
    if (mysqli_stmt_execute($stmt)) {
        redirectTo(SITE_URL . '/admin/manage_plans.php');
    }
}
?>

<!-- Rest of your HTML with updated paths --> 