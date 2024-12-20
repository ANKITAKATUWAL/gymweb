<?php
$page_title = 'Login';
require_once '../includes/functions.php';
require_once '../config/config.php';
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: " . SITE_URL . "/admin/dashboard.php");
    } else {
        header("Location: " . SITE_URL . "/user/dashboard.php");
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/db_connection.php';
    
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            if ($user['role'] === 'admin') {
                redirectTo(SITE_URL . '/admin/dashboard.php');
            } else {
                redirectTo(SITE_URL . '/user/dashboard.php');
            }
        }
    }
    
    $error_message = 'Invalid email or password';
}
?>

<div class="container">
    <div class="auth-form">
        <h1>Login</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        
        <p class="auth-links">
            Don't have an account? <a href="<?php echo SITE_URL; ?>/auth/register.php">Register</a>
        </p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 