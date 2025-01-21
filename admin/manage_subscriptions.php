<?php
$page_title = 'Manage Subscriptions';
require_once '../includes/functions.php';
require_once '../includes/header.php';
checkAdminRole();

// Handle POST request for approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['subscription_id']) && isset($_POST['action'])) {
        $subscription_id = intval($_POST['subscription_id']);
        $action = $_POST['action'];
        $note = isset($_POST['note']) ? trim($_POST['note']) : '';
        
        // Update subscription status
        $new_status = ($action === 'approve') ? 'Approved' : 'Rejected';
        
        $update_sql = "UPDATE subscriptions SET 
                      payment_status = ?,
                      approval_date = NOW(),
                      admin_note = ?
                      WHERE subscription_id = ?";
                      
        $stmt = mysqli_prepare($con, $update_sql);
        mysqli_stmt_bind_param($stmt, "ssi", $new_status, $note, $subscription_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Subscription has been " . strtolower($new_status);
        } else {
            $error_message = "Failed to update subscription status";
        }
    }
}

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
<div class="modal fade" id="approvalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modalMessage">Are you sure you want to process this subscription?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmApproval">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Bootstrap JS and jQuery -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let selectedSubscriptionId = null;
let selectedAction = null;

function showApprovalModal(subscriptionId, action) {
    selectedSubscriptionId = subscriptionId;
    selectedAction = action;
    
    const message = action === 'approve' 
        ? 'Are you sure you want to approve this subscription?' 
        : 'Are you sure you want to reject this subscription?';
    
    $('#modalMessage').text(message);
    $('#confirmApproval').removeClass('btn-success btn-danger')
        .addClass(action === 'approve' ? 'btn-success' : 'btn-danger')
        .text(action === 'approve' ? 'Approve' : 'Reject');
    
    $('#approvalModal').modal('show');
}

$('#confirmApproval').click(function() {
    if (!selectedSubscriptionId || !selectedAction) return;
    
    $.ajax({
        url: '<?php echo SITE_URL; ?>/admin/process_subscription.php',
        type: 'POST',
        data: {
            subscription_id: selectedSubscriptionId,
            action: selectedAction
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while processing the request');
        }
    });
    
    $('#approvalModal').modal('hide');
});

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