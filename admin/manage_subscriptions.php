<?php
$page_title = 'Manage Subscriptions';
require_once '../includes/functions.php';
require_once '../includes/header.php';
checkAdminRole();

// Add this CSS at the top of the file after header inclusion
?>
<style>
.subscription-table {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.subscription-table th {
    background: #f8f9fa;
    padding: 15px;
    font-weight: 600;
    color: #2c3e50;
}

.member-info {
    display: flex;
    flex-direction: column;
}

.member-name {
    font-weight: 600;
    color: #2c3e50;
}

.member-email {
    font-size: 0.85rem;
    color: #666;
}

.date-info {
    display: flex;
    flex-direction: column;
}

.approval-date {
    font-size: 0.85rem;
    color: #666;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-paid { background: #cce5ff; color: #004085; }
.status-approved { background: #d4edda; color: #155724; }
.status-rejected { background: #f8d7da; color: #721c24; }

.controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    background: white;
    padding: 1rem;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.filter-buttons {
    display: flex;
    gap: 0.5rem;
}

.search-box {
    position: relative;
    width: 300px;
}

.search-box input {
    width: 100%;
    padding: 8px 35px 8px 15px;
    border: 1px solid #ddd;
    border-radius: 20px;
    outline: none;
}

.search-box i {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.action-buttons button {
    padding: 6px 12px;
    border-radius: 15px;
}
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Subscriptions</h1>
        <div class="search-box">
            <input type="text" id="searchSubscriptions" placeholder="Search subscriptions...">
            <i class="fas fa-search"></i>
        </div>
    </div>
    
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <div class="filter-buttons mb-3">
        <button class="btn btn-outline-primary active" data-filter="all">All</button>
        <button class="btn btn-outline-warning" data-filter="pending">Pending</button>
        <button class="btn btn-outline-info" data-filter="paid">Paid</button>
        <button class="btn btn-outline-success" data-filter="approved">Approved</button>
        <button class="btn btn-outline-danger" data-filter="rejected">Rejected</button>
    </div>

    <div class="table-responsive">
        <table class="table subscription-table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Plan</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Payment ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Get all subscriptions with user and plan details
                $subscriptions_sql = "SELECT s.*, 
                                            u.full_name, u.email, 
                                            g.plan_name, g.plan_price,
                                            p.payment_id, p.khalti_token,
                                            DATE_FORMAT(s.subscription_date, '%M %d, %Y') as formatted_date,
                                            CASE 
                                                WHEN s.approval_date IS NOT NULL 
                                                THEN DATE_FORMAT(s.approval_date, '%M %d, %Y') 
                                                ELSE NULL 
                                            END as formatted_approval_date
                                         FROM subscriptions s
                                         JOIN users u ON s.user_id = u.user_id
                                         JOIN gym_plans g ON s.plan_id = g.plan_id
                                         LEFT JOIN payments p ON s.subscription_id = p.subscription_id
                                         ORDER BY s.subscription_date DESC";

                // Change this line
                $subscriptions_result = mysqli_query($con, $subscriptions_sql);
                if (!$subscriptions_result) {
                    die("Query failed: " . mysqli_error($con));
                }

                // And update the while loop to use $subscriptions_result instead of $subscriptions
                while ($sub = mysqli_fetch_assoc($subscriptions_result)):
                    ?>
                    <tr class="subscription-row" data-status="<?php echo strtolower($sub['payment_status']); ?>">
                        <td>
                            <div class="member-info">
                                <span class="member-name"><?php echo htmlspecialchars($sub['full_name']); ?></span>
                                <span class="member-email"><?php echo htmlspecialchars($sub['email']); ?></span>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($sub['plan_name']); ?></td>
                        <td>Rs. <?php echo number_format($sub['plan_price'], 2); ?></td>
                        <td>
                            <div class="date-info">
                                <span class="subscription-date"><?php echo $sub['formatted_date']; ?></span>
                                <?php if ($sub['formatted_approval_date']): ?>
                                    <span class="approval-date">Processed: <?php echo $sub['formatted_approval_date']; ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($sub['payment_status']); ?>">
                                <?php echo $sub['payment_status']; ?>
                            </span>
                        </td>
                        <td><?php echo $sub['khalti_token'] ?? 'N/A'; ?></td>
                        <td>
                            <?php if ($sub['payment_status'] === 'Paid'): ?>
                                <div class="action-buttons">
                                    <button class="btn btn-success btn-sm" 
                                            onclick="showApprovalModal(<?php echo $sub['subscription_id']; ?>, 'approve')">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button class="btn btn-danger btn-sm"
                                            onclick="showApprovalModal(<?php echo $sub['subscription_id']; ?>, 'reject')">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="subscription_id" id="modalSubscriptionId">
                    <input type="hidden" name="action" id="modalAction">
                    <p id="modalMessage"></p>
                    <div class="form-group">
                        <label for="note">Note (optional):</label>
                        <textarea class="form-control" name="note" id="note" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirmButton">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showApprovalModal(subscriptionId, action) {
    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
    document.getElementById('modalSubscriptionId').value = subscriptionId;
    document.getElementById('modalAction').value = action;
    document.getElementById('modalMessage').textContent = 
        `Are you sure you want to ${action} this subscription?`;
    document.getElementById('confirmButton').className = 
        `btn btn-${action === 'approve' ? 'success' : 'danger'}`;
    modal.show();
}

// Filter functionality
document.querySelectorAll('.filter-buttons button').forEach(button => {
    button.addEventListener('click', function() {
        const filter = this.dataset.filter;
        const rows = document.querySelectorAll('.subscription-row');
        
        // Update active button
        document.querySelectorAll('.filter-buttons button').forEach(btn => 
            btn.classList.remove('active'));
        this.classList.add('active');
        
        // Filter rows
        rows.forEach(row => {
            if (filter === 'all' || row.dataset.status === filter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});

// Search functionality
document.getElementById('searchSubscriptions').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.subscription-row');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>

<?php require_once '../includes/footer.php'; ?> 