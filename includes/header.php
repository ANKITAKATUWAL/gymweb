<?php
ob_start();
session_start();
require_once dirname(__FILE__) . '/../config/config.php';
require_once dirname(__FILE__) . '/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) && !in_array(basename($_SERVER['PHP_SELF']), ['login.php', 'register.php'])) {
    header("Location: " . SITE_URL . "/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Gym Management System</title>
    
    <!-- Base CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    
    <!-- Dashboard specific CSS -->
    <?php if (strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false): ?>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/dashboard.css">
    <?php endif; ?>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Custom styles for specific pages -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css_file): ?>
        <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/<?php echo $css_file; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <header class="main-header <?php echo isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? 'admin-header' : 'user-header'; ?>">
        <div class="header-container">
            <div class="logo">
                <a href="<?php echo SITE_URL; ?>">
                    <i class="fas fa-dumbbell"></i>
                    <span><?php echo SITE_NAME; ?></span>
                </a>
            </div>
            
            <nav class="main-nav">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/manage_users.php">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/manage_plans.php">
                        <i class="fas fa-clipboard-list"></i> Plans
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/manage_subscriptions.php">
                        <i class="fas fa-credit-card"></i> Subscriptions
                    </a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/user/dashboard.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="<?php echo SITE_URL; ?>/user/membership.php">
                        <i class="fas fa-dumbbell"></i> Membership
                    </a>
                    <a href="<?php echo SITE_URL; ?>/user/profile.php">
                        <i class="fas fa-user"></i> Profile
                    </a>
                <?php endif; ?>
            </nav>
            
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-menu">
                <div class="user-info">
                    <span class="user-name">
                        <i class="fas fa-user-circle"></i>
                        <?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : ''; ?>
                    </span>
                    <a href="<?php echo SITE_URL; ?>/auth/logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </header>
    <div class="main-content">
</body>
</html> 