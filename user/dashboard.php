<?php
$page_title = 'Dashboard';
require_once '../includes/functions.php';
require_once '../includes/header.php';
checkLoggedIn();

$user_id = $_SESSION['user_id'];

// Get user's subscription and attendance data
$subscription_query = "SELECT s.*, g.plan_name, g.plan_duration 
                      FROM subscriptions s 
                      JOIN gym_plans g ON s.plan_id = g.plan_id 
                      WHERE s.user_id = ? AND s.payment_status = 'Approved'
                      AND s.subscription_date <= NOW() 
                      AND DATE_ADD(s.subscription_date, INTERVAL g.plan_duration MONTH) > NOW()";
$stmt = mysqli_prepare($con, $subscription_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$subscription = mysqli_stmt_get_result($stmt)->fetch_assoc();

// Get attendance stats
$attendance_stats = mysqli_fetch_assoc(mysqli_query($con, 
    "SELECT 
        COUNT(*) as total_visits,
        COUNT(DISTINCT MONTH(check_in_date)) as months_visited,
        MAX(check_in_date) as last_visit
    FROM attendance 
    WHERE user_id = $user_id"
));

// Get recent attendance
$attendance_query = "SELECT * FROM attendance 
                    WHERE user_id = ? 
                    ORDER BY check_in_date DESC, check_in_time DESC 
                    LIMIT 5";
$stmt = mysqli_prepare($con, $attendance_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$recent_attendance = mysqli_stmt_get_result($stmt);
?>

<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="welcome-content">
            <h1>Welcome Back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
            <p class="date"><?php echo date('l, F j, Y'); ?></p>
        </div>
        <div class="quick-actions">
            <?php if ($subscription): ?>
                <button class="btn btn-primary" onclick="markAttendance()">
                    <i class="fas fa-check-circle"></i> Mark Attendance
                </button>
            <?php else: ?>
                <a href="<?php echo SITE_URL; ?>/user/membership.php" class="btn btn-primary">
                    <i class="fas fa-dumbbell"></i> Get Membership
                </a>
            <?php endif; ?>
            <a href="<?php echo SITE_URL; ?>/user/profile.php" class="btn btn-secondary">
                <i class="fas fa-user"></i> View Profile
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-details">
                <h3>Total Visits</h3>
                <p class="stat-number"><?php echo $attendance_stats['total_visits'] ?? 0; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-details">
                <h3>Last Visit</h3>
                <p class="stat-number"><?php echo $attendance_stats['last_visit'] ? date('M d, Y', strtotime($attendance_stats['last_visit'])) : 'Never'; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-details">
                <h3>Active Months</h3>
                <p class="stat-number"><?php echo $attendance_stats['months_visited'] ?? 0; ?></p>
            </div>
        </div>
    </div>

    <!-- Membership Status -->
    <div class="content-grid">
        <div class="membership-status">
            <h2><i class="fas fa-id-card"></i> Membership Status</h2>
            <?php if ($subscription): ?>
                <div class="active-plan">
                    <div class="plan-badge">Active</div>
                    <h3><?php echo htmlspecialchars($subscription['plan_name']); ?></h3>
                    <div class="plan-details">
                        <p><i class="fas fa-calendar-alt"></i> Valid Until: 
                            <?php echo date('F j, Y', strtotime($subscription['subscription_date'] . ' + ' . $subscription['plan_duration'] . ' months')); ?>
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-plan">
                    <p>No active membership plan</p>
                    <a href="<?php echo SITE_URL; ?>/user/membership.php" class="btn btn-primary">View Plans</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Activity -->
        <div class="recent-activity">
            <h2><i class="fas fa-history"></i> Recent Activity</h2>
            <?php if (mysqli_num_rows($recent_attendance) > 0): ?>
                <div class="activity-timeline">
                    <?php while ($record = mysqli_fetch_assoc($recent_attendance)): ?>
                        <div class="timeline-item">
                            <div class="timeline-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h4><?php echo date('l', strtotime($record['check_in_date'])); ?></h4>
                                <p><?php echo date('F j, Y', strtotime($record['check_in_date'])); ?></p>
                                <span class="time"><?php echo date('g:i A', strtotime($record['check_in_time'])); ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-activity">No recent activity</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Add custom CSS -->
<style>
.dashboard-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.welcome-section {
    background: linear-gradient(135deg, #2193b0, #6dd5ed);
    border-radius: 15px;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.welcome-content h1 {
    font-size: 2rem;
    margin: 0;
}

.date {
    opacity: 0.9;
    margin-top: 0.5rem;
}

.quick-actions {
    display: flex;
    gap: 1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    background: #f8f9fa;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.stat-icon i {
    font-size: 1.5rem;
    color: #2193b0;
}

.stat-details h3 {
    margin: 0;
    font-size: 0.9rem;
    color: #6c757d;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0;
    color: #2c3e50;
}

.content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.membership-status, .recent-activity {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.membership-status h2, .recent-activity h2 {
    font-size: 1.2rem;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.active-plan {
    position: relative;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.plan-badge {
    position: absolute;
    top: -10px;
    right: 10px;
    background: #28a745;
    color: white;
    padding: 0.25rem 1rem;
    border-radius: 15px;
    font-size: 0.8rem;
}

.plan-details {
    margin-top: 1rem;
    color: #6c757d;
}

.plan-details i {
    margin-right: 0.5rem;
}

.activity-timeline {
    position: relative;
}

.timeline-item {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    position: relative;
}

.timeline-icon {
    background: #e3f2fd;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.timeline-icon i {
    color: #2193b0;
}

.timeline-content h4 {
    margin: 0;
    font-size: 1rem;
    color: #2c3e50;
}

.timeline-content p {
    margin: 0.25rem 0;
    color: #6c757d;
}

.time {
    font-size: 0.8rem;
    color: #6c757d;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #2193b0;
    color: white;
}

.btn-secondary {
    background: rgba(255,255,255,0.2);
    color: white;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .welcome-section {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .quick-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
function markAttendance() {
    fetch('<?php echo SITE_URL; ?>/user/mark_attendance.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to mark attendance');
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?> 