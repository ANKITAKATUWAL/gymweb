<?php
$page_title = 'My Profile';
require_once '../includes/functions.php';
require_once '../includes/header.php';
checkLoggedIn();

$user_id = $_SESSION['user_id'];

// Get user details
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_stmt_get_result($stmt)->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitizeInput($_POST['full_name']);
    $phone = sanitizeInput($_POST['phone']);
    
    $update_sql = "UPDATE users SET full_name = ?, phone_number = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($con, $update_sql);
    mysqli_stmt_bind_param($stmt, "ssi", $full_name, $phone, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['full_name'] = $full_name;
        showAlert('Profile updated successfully', 'success');
        redirectTo(SITE_URL . '/user/profile.php');
    }
}
?>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <h1>My Profile</h1>
    </div>

    <div class="profile-card">
        <form method="POST" class="profile-form">
            <div class="form-grid">
                <div class="form-group">
                    <label for="full_name">
                        <i class="fas fa-user"></i>
                        Full Name
                    </label>
                    <input type="text" 
                           id="full_name" 
                           name="full_name" 
                           value="<?php echo htmlspecialchars($user['full_name']); ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email
                    </label>
                    <input type="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" 
                           disabled>
                    <small class="form-text">Email cannot be changed</small>
                </div>
                
                <div class="form-group">
                    <label for="phone">
                        <i class="fas fa-phone"></i>
                        Phone Number
                    </label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="<?php echo htmlspecialchars($user['phone_number']); ?>">
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-calendar"></i>
                        Member Since
                    </label>
                    <input type="text" 
                           value="<?php echo date('F j, Y', strtotime($user['registration_date'])); ?>" 
                           disabled>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.profile-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.profile-header {
    text-align: center;
    margin-bottom: 2rem;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    margin: 0 auto 1rem;
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-avatar i {
    font-size: 3rem;
    color: white;
}

.profile-header h1 {
    color: #2c3e50;
    font-size: 2rem;
    margin: 0;
}

.profile-card {
    background: white;
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    color: #2c3e50;
    font-weight: 500;
}

.form-group label i {
    color: #3498db;
}

.form-group input {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #e1e1e1;
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #3498db;
}

.form-group input:disabled {
    background-color: #f8f9fa;
    cursor: not-allowed;
}

.form-text {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.form-actions {
    text-align: center;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.8rem 2rem;
    font-size: 1rem;
    font-weight: 500;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .profile-container {
        padding: 1rem;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?> 