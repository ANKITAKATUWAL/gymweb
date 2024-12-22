<?php
$page_title = 'My Profile';
require_once '../includes/functions.php';
require_once '../includes/header.php';
checkLoggedIn();

$user_id = $_SESSION['user_id'];
$success_message = $error_message = '';

// Get current user data
$stmt = mysqli_prepare($con, "SELECT * FROM users WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_stmt_get_result($stmt)->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $full_name = sanitizeInput($_POST['full_name']);
        $email = sanitizeInput($_POST['email']);
        $phone = sanitizeInput($_POST['phone']);
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        
        // Start transaction
        mysqli_begin_transaction($con);
        
        // Check if email is changed and not already taken
        if ($email !== $user['email']) {
            $check_email = mysqli_prepare($con, "SELECT user_id FROM users WHERE email = ? AND user_id != ?");
            mysqli_stmt_bind_param($check_email, "si", $email, $user_id);
            mysqli_stmt_execute($check_email);
            if (mysqli_stmt_fetch($check_email)) {
                throw new Exception("Email already in use");
            }
        }
        
        // Update basic info
        $update_sql = "UPDATE users SET full_name = ?, email = ?, phone_number = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($con, $update_sql);
        mysqli_stmt_bind_param($stmt, "sssi", $full_name, $email, $phone, $user_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to update profile");
        }
        
        // Update password if provided
        if (!empty($current_password) && !empty($new_password)) {
            if (!password_verify($current_password, $user['password'])) {
                throw new Exception("Current password is incorrect");
            }
            
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $password_sql = "UPDATE users SET password = ? WHERE user_id = ?";
            $stmt = mysqli_prepare($con, $password_sql);
            mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to update password");
            }
        }
        
        mysqli_commit($con);
        $_SESSION['full_name'] = $full_name; // Update session
        $success_message = "Profile updated successfully";
        
        // Refresh user data
        $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $user = mysqli_stmt_get_result($stmt)->fetch_assoc();
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        $error_message = $e->getMessage();
    }
}
?>

<div class="profile-container">
    <div class="profile-header">
        <h1><i class="fas fa-user-circle"></i> My Profile</h1>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <div class="profile-form-container">
        <form method="POST" class="profile-form">
            <div class="form-group">
                <label for="full_name">
                    <i class="fas fa-user"></i> Full Name
                </label>
                <input type="text" id="full_name" name="full_name" 
                       value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">
                    <i class="fas fa-phone"></i> Phone Number
                </label>
                <input type="tel" id="phone" name="phone" 
                       value="<?php echo htmlspecialchars($user['phone_number']); ?>">
            </div>

            <div class="password-section">
                <h3><i class="fas fa-lock"></i> Change Password</h3>
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password">
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.profile-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.profile-header {
    text-align: center;
    margin-bottom: 2rem;
}

.profile-header h1 {
    color: #2c3e50;
    font-size: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.profile-form-container {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.profile-form {
    display: grid;
    gap: 1.5rem;
}

.form-group {
    display: grid;
    gap: 0.5rem;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #2c3e50;
    font-weight: 500;
}

.form-group input {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 0 2px rgba(52,152,219,0.2);
}

.password-section {
    border-top: 1px solid #eee;
    padding-top: 1.5rem;
    margin-top: 1.5rem;
}

.password-section h3 {
    color: #2c3e50;
    font-size: 1.25rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 1.5rem;
}

.btn-primary {
    background: #3498db;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

.alert {
    padding: 1rem;
    border-radius: 5px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@media (max-width: 768px) {
    .profile-form-container {
        padding: 1.5rem;
    }
    
    .form-actions {
        justify-content: stretch;
    }
    
    .btn-primary {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?> 