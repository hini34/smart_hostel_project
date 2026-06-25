<?php
session_start();
require_once(__DIR__ . "/fees_data.php");

$page_title  = "Fees Management | Admin Panel";
$active_page = "fees";
$extra_css   = "/smart_hostel/assets/css/admin_fees.css";

function e($str){
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

include __DIR__ . "/../../includes/admin/admin_header.php";
include __DIR__ . "/../../includes/admin/admin_sidebar.php";
?>

<div class="main-content">
    <div class="topbar">
        <h2><i class="fa-solid fa-money-check-dollar me-2"></i>Fees Management</h2>
        <p>Manage fee records and student payments</p>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e($msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card-box">
                <div class="stat-title">Total Collected</div>
                <h3 class="stat-value">₹ <?php echo number_format($total_collected, 2); ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-box">
                <div class="stat-title">Total Pending</div>
                <h3 class="stat-value">₹ <?php echo number_format($total_pending, 2); ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-box">
                <div class="stat-title">This Month Collected</div>
                <h3 class="stat-value">₹ <?php echo number_format($month_collected, 2); ?></h3>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="feesTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="assign-fee-tab" data-bs-toggle="tab" data-bs-target="#assignFee" type="button" role="tab" aria-controls="assignFee" aria-selected="true">
                Assign Fee
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="record-payment-tab" data-bs-toggle="tab" data-bs-target="#recordPayment" type="button" role="tab" aria-controls="recordPayment" aria-selected="false">
                Record Payment
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="recent-fees-tab" data-bs-toggle="tab" data-bs-target="#recentFees" type="button" role="tab" aria-controls="recentFees" aria-selected="false">
                Recent Fees
            </button>
        </li>
    </ul>

    <div class="tab-content" id="feesTabContent">

        <!-- Assign Fee -->
        <div class="tab-pane fade show active" id="assignFee" role="tabpanel" aria-labelledby="assign-fee-tab">
            <div class="card-box">
                <h5 class="section-title">Assign New Fee</h5>

                <form method="POST" action="fees_action.php" class="row g-3">
                    <input type="hidden" name="action" value="assign_fee">

                    <div class="col-md-3">
                        <label class="form-label">Student ID</label>
                        <input type="number" name="student_id" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Category ID</label>
                        <input type="number" name="category_id" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" required>
                    </div>

                    <div class="col-12 text-end">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa-solid fa-plus me-1"></i> Assign Fee
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Record Payment -->
        <div class="tab-pane fade" id="recordPayment" role="tabpanel" aria-labelledby="record-payment-tab">
            <div class="card-box">
                <h5 class="section-title">Record Payment</h5>

                <form method="POST" action="fees_action.php" class="row g-3">
                    <input type="hidden" name="action" value="record_payment">

                    <div class="col-md-3">
                        <label class="form-label">Fee ID</label>
                        <input type="number" name="fee_id" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Payment Amount</label>
                        <input type="number" step="0.01" name="pay_amount" class="form-control" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Payment Mode</label>
                        <select name="payment_mode" class="form-select">
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI</option>
                            <option value="Card">Card</option>
                            <option value="Net Banking">Net Banking</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Transaction No</label>
                        <input type="text" name="transaction_no" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" class="form-control">
                    </div>

                    <div class="col-12 text-end">
                        <button class="btn btn-success" type="submit">
                            <i class="fa-solid fa-check me-1"></i> Save Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Recent Fees -->
        <div class="tab-pane fade" id="recentFees" role="tabpanel" aria-labelledby="recent-fees-tab">
            <div class="card-box">
                <h5 class="section-title">Recent Fee Records</h5>

                <div class="table-responsive table-wrap">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student ID</th>
                                <th>Category ID</th>
                                <th>Amount</th>
                                <th>Paid Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                                <th>Payment Mode</th>
                                <th>Transaction No</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($fees_list)): ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted">No fee records found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($fees_list as $fee): ?>
                                    <?php
                                        $statusClass = 'unpaid';
                                        if (($fee['status'] ?? '') === 'Paid') {
                                            $statusClass = 'paid';
                                        } elseif (($fee['status'] ?? '') === 'Partially Paid') {
                                            $statusClass = 'partial';
                                        }
                                    ?>
                                    <tr>
                                        <td><?php echo e($fee['id']); ?></td>
                                        <td><?php echo e($fee['student_id']); ?></td>
                                        <td><?php echo e($fee['category_id']); ?></td>
                                        <td>₹ <?php echo number_format((float)$fee['amount'], 2); ?></td>
                                        <td>₹ <?php echo number_format((float)$fee['paid_amount'], 2); ?></td>
                                        <td><?php echo e($fee['due_date']); ?></td>
                                        <td>
                                            <span class="badge-status <?php echo $statusClass; ?>">
                                                <?php echo e($fee['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo e($fee['payment_date']); ?></td>
                                        <td><?php echo e($fee['payment_mode']); ?></td>
                                        <td><?php echo e($fee['transaction_no']); ?></td>
                                        <td><?php echo e($fee['remarks']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include __DIR__ . "/../../includes/admin/admin_footer.php"; ?>