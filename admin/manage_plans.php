<?php
$page_title = 'Manage Plans';
require_once '../includes/functions.php';
require_once '../includes/header.php';
checkAdminRole();

// Get all plans
$sql = "SELECT * FROM gym_plans ORDER BY plan_price ASC";
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

<div class="container">
    <h1>Manage Gym Plans</h1>

    <!-- Add New Plan Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h2>Add New Plan</h2>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label for="plan_name">Plan Name</label>
                    <input type="text" class="form-control" id="plan_name" name="plan_name" required>
                </div>

                <div class="form-group">
                    <label for="description">Description (One feature per line)</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    <small class="text-muted">Enter each feature on a new line</small>
                </div>

                <div class="form-group">
                    <label for="price">Price (Rs.)</label>
                    <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="duration">Duration (Months)</label>
                    <input type="number" class="form-control" id="duration" name="duration" min="1" required>
                </div>

                <button type="submit" class="btn btn-primary">Add Plan</button>
            </form>
        </div>
    </div>

    <!-- Existing Plans Table -->
    <div class="card">
        <div class="card-header">
            <h2>Existing Plans</h2>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Plan Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($plan = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($plan['plan_name']); ?></td>
                        <td>
                            <?php 
                            $features = explode("\n", $plan['plan_description']);
                            foreach ($features as $feature) {
                                echo htmlspecialchars(trim($feature)) . "<br>";
                            }
                            ?>
                        </td>
                        <td>Rs. <?php echo number_format($plan['plan_price'], 2); ?></td>
                        <td><?php echo $plan['plan_duration']; ?> months</td>
                        <td>
                            <a href="?delete_id=<?php echo $plan['plan_id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirmDelete('Are you sure you want to delete this plan?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.card {
    margin-bottom: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 1rem;
}

.card-body {
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-control {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.table {
    width: 100%;
    margin-bottom: 0;
}

.table th,
.table td {
    padding: 1rem;
    vertical-align: top;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>

<?php require_once '../includes/footer.php'; ?> 