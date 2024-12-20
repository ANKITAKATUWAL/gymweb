<?php
$page_title = 'Membership Plans';
require_once '../includes/functions.php';
require_once '../includes/header.php';
checkLoggedIn();

$user_id = $_SESSION['user_id'];

// Get all plans
$plans_sql = "SELECT * FROM gym_plans ORDER BY plan_price ASC";
$plans_result = mysqli_query($con, $plans_sql);

// Get current subscription
$subscription_sql = "SELECT s.*, g.plan_name, g.plan_duration 
                    FROM subscriptions s 
                    JOIN gym_plans g ON s.plan_id = g.plan_id 
                    WHERE s.user_id = ? AND s.payment_status = 'Approved'
                    AND s.subscription_date <= NOW() 
                    AND DATE_ADD(s.subscription_date, INTERVAL g.plan_duration MONTH) > NOW()";
$stmt = mysqli_prepare($con, $subscription_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$current_subscription = mysqli_stmt_get_result($stmt)->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plan_id'])) {
    $plan_id = intval($_POST['plan_id']);
    
    // Create subscription
    $subscribe_sql = "INSERT INTO subscriptions (user_id, plan_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $subscribe_sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $plan_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $subscription_id = mysqli_insert_id($con);
        redirectTo(SITE_URL . '/user/payment.php?subscription_id=' . $subscription_id);
    }
}
?>

<div class="container">
    <div class="membership-header">
        <h1>Choose Your Membership Plan</h1>
        <p class="subtitle">Select the plan that best fits your fitness goals</p>
    </div>

    <?php if ($current_subscription): ?>
    <div class="current-plan-card">
        <div class="plan-status">
            <i class="fas fa-check-circle"></i>
            <h3>Current Active Plan</h3>
        </div>
        <div class="plan-details">
            <h4><?php echo htmlspecialchars($current_subscription['plan_name']); ?></h4>
            <p>Valid until: <?php echo date('F j, Y', strtotime($current_subscription['subscription_date'] . ' + ' . $current_subscription['plan_duration'] . ' months')); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <div class="membership-grid">
        <?php while ($plan = mysqli_fetch_assoc($plans_result)): ?>
        <div class="plan-card <?php echo $plan['plan_name'] === 'Premium Plan' ? 'featured' : ''; ?>">
            <?php if ($plan['plan_name'] === 'Premium Plan'): ?>
            <div class="featured-badge">Most Popular</div>
            <?php endif; ?>
            
            <div class="plan-header">
                <h3><?php echo htmlspecialchars($plan['plan_name']); ?></h3>
                <div class="plan-price">
                    <span class="currency">Rs.</span>
                    <span class="amount"><?php echo number_format($plan['plan_price']); ?></span>
                    <span class="duration">/ <?php echo $plan['plan_duration']; ?> months</span>
                </div>
            </div>

            <div class="plan-features">
                <?php 
                $features = explode("\n", $plan['plan_description']);
                foreach ($features as $feature): 
                ?>
                <div class="feature-item">
                    <i class="fas fa-check"></i>
                    <span><?php echo htmlspecialchars(trim($feature)); ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <form method="POST" class="plan-action">
                <input type="hidden" name="plan_id" value="<?php echo $plan['plan_id']; ?>">
                <button type="submit" class="btn btn-primary <?php echo $current_subscription ? 'disabled' : ''; ?>"
                        <?php echo $current_subscription ? 'disabled' : ''; ?>>
                    <?php echo $current_subscription ? 'Already Subscribed' : 'Choose Plan'; ?>
                </button>
            </form>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Add custom CSS for membership page -->
<style>
.membership-header {
    text-align: center;
    margin-bottom: 3rem;
}

.membership-header h1 {
    font-size: 2.5rem;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.subtitle {
    color: #7f8c8d;
    font-size: 1.1rem;
}

.membership-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    padding: 1rem;
}

.plan-card {
    background: white;
    border-radius: 10px;
    padding: 2rem;
    position: relative;
    transition: transform 0.3s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.plan-card:hover {
    transform: translateY(-5px);
}

.plan-card.featured {
    border: 2px solid #3498db;
}

.featured-badge {
    position: absolute;
    top: -12px;
    right: 20px;
    background: #3498db;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: bold;
}

.plan-header {
    text-align: center;
    margin-bottom: 2rem;
}

.plan-header h3 {
    color: #2c3e50;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.plan-price {
    font-size: 2.5rem;
    color: #2c3e50;
    font-weight: bold;
}

.currency {
    font-size: 1rem;
    vertical-align: super;
}

.duration {
    font-size: 1rem;
    color: #7f8c8d;
}

.plan-features {
    margin: 2rem 0;
}

.feature-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.feature-item i {
    color: #2ecc71;
    margin-right: 10px;
}

.plan-action {
    text-align: center;
}

.plan-action .btn {
    width: 100%;
    padding: 12px;
    font-size: 1.1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.current-plan-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.plan-status {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.plan-status i {
    color: #2ecc71;
    font-size: 1.5rem;
}

.btn.disabled {
    background-color: #95a5a6;
    cursor: not-allowed;
}
</style>

<?php require_once '../includes/footer.php'; ?> 