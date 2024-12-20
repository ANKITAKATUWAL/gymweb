<?php
$page_title = 'Register';
require_once '../includes/functions.php';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitizeInput($_POST['full_name']);
    $email = sanitizeInput($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = sanitizeInput($_POST['phone']);
    
    // Check if email already exists
    $check_sql = "SELECT user_id FROM users WHERE email = ?";
    $stmt = mysqli_prepare($con, $check_sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    
    if (mysqli_stmt_fetch($stmt)) {
        $error_message = 'Email already registered';
    } else {
        $sql = "INSERT INTO users (full_name, email, password, phone_number) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $full_name, $email, $password, $phone);
        
        if (mysqli_stmt_execute($stmt)) {
            redirectTo(SITE_URL . '/auth/login.php');
        } else {
            $error_message = 'Registration failed. Please try again.';
        }
    }
}
?>

<div class="container">
    <div class="auth-form">
        <h1>Register</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone">
            </div>
            
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        
        <p class="auth-links">
            Already have an account? <a href="<?php echo SITE_URL; ?>/auth/login.php">Login</a>
        </p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
   