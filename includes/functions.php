<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function checkLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: " . SITE_URL . "/auth/login.php");
        exit();
    }
}

function checkAdminRole() {
    if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
        header("Location: " . SITE_URL . "/auth/login.php");
        exit();
    }
}

function redirectTo($url) {
    header("Location: " . $url);
    exit();
}

function sanitizeInput($data) {
    global $con;
    return mysqli_real_escape_string($con, trim($data));
}

function showAlert($message, $type = 'success') {
    $_SESSION['alert'] = [
        'message' => $message,
        'type' => $type
    ];
}

function generatePasswordHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}
?> 