<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if(empty($hostel_name)){
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}


$admin_name = $_SESSION['admin_name'] ?? "Admin";
$page_title = "Complaints Management";
$active_page = "complaints";

// Optional admin auth check
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: ../admin_login.php");
//     exit;
// }

require_once(__DIR__ . "/complaint_helpers.php");
require_once(__DIR__ . "/complaint_actions.php");
require_once(__DIR__ . "/complaint_queries.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . "/../../includes/admin/admin_header.php"; ?>
    <link rel="stylesheet" href="../../assets/css/admin_complaints.css">
</head>
<body>

<nav class="navbar navbar-dark navbar-main d-lg-none fixed-top">
        <div class="container-fluid">
            <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar">
                <i class="fa-solid fa-bars"></i>
            </button>

            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fa-solid fa-building-user me-2"></i>
                <span>Smart Hostel - Admin</span>
            </a>

            <span class="badge bg-primary rounded-pill">
                <?php echo htmlspecialchars($admin_name); ?>
            </span>
        </div>
</nav>

<?php include __DIR__ . "/../../includes/admin/admin_sidebar.php"; ?>

<div class="page-wrapper">
    <div class="page-header">
        <h4 class="page-title">Complaints Management</h4>
        <p class="page-subtitle">Track, review, assign and update student complaints.</p>
    </div>

    
    
     <?php if (!empty($msg)): ?>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;">
    <div class="toast show text-bg-success border-0">
        <div class="d-flex">
            <div class="toast-body">
                <?= htmlspecialchars($msg) ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;">
    <div class="toast show text-bg-danger border-0">
        <div class="d-flex">
            <div class="toast-body">
                <?= htmlspecialchars($error) ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?> 
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Total Complaints</div>
                <h3 class="stat-value"><?php echo $stats['total']; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Open</div>
                <h3 class="stat-value"><?php echo $stats['open']; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">In-Progress</div>
                <h3 class="stat-value"><?php echo $stats['progress']; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Resolved</div>
                <h3 class="stat-value"><?php echo $stats['resolved']; ?></h3>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="panel-card">
                <div class="filter-toolbar">
                    <h4 class="panel-title mb-0">Complaint Records</h4>

                    <div class="btn-group btn-group-sm">
                        <a href="complaints.php" class="btn btn-outline-secondary <?php echo $status_filter === '' ? 'active' : ''; ?>">All</a>
                        <a href="complaints.php?status=Open" class="btn btn-outline-secondary <?php echo $status_filter === 'Open' ? 'active' : ''; ?>">Open</a>
                        <a href="complaints.php?status=In-Progress" class="btn btn-outline-secondary <?php echo $status_filter === 'In-Progress' ? 'active' : ''; ?>">In-Progress</a>
                        <a href="complaints.php?status=Resolved" class="btn btn-outline-secondary <?php echo $status_filter === 'Resolved' ? 'active' : ''; ?>">Resolved</a>
                        <a href="complaints.php?status=Rejected" class="btn btn-outline-secondary <?php echo $status_filter === 'Rejected' ? 'active' : ''; ?>">Rejected</a>
                    </div>
                </div>

                <div class="table-wrap">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Room</th>
                                <th>Title</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($complaints)): ?>
                            <tr>
                                <td colspan="7" class="empty-state">No complaints found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($complaints as $c): ?>
                                <tr class="complaint-row"
                                    onclick="window.location='complaints.php?id=<?php echo $c['id']; ?><?php echo $status_filter ? '&status=' . urlencode($status_filter) : ''; ?>'">
                                    <td><?php echo $c['id']; ?></td>
                                    <td>
                                        <div class="student-name"><?php echo e($c['fullname']); ?></div>
                                        <div class="student-meta"><?php echo e($c['enrollment']); ?></div>
                                    </td>
                                    <td><?php echo e($c['room_no']); ?></td>
                                    <td><?php echo e($c['title']); ?></td>
                                    <td>
                                        <span class="badge-soft <?php echo complaintPriorityClass($c['priority']); ?>">
                                            <?php echo e($c['priority']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-soft <?php echo complaintStatusClass($c['status']); ?>">
                                            <?php echo e($c['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y, h:i A', strtotime($c['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="panel-card">
                <h4 class="panel-title">Complaint Details & Update</h4>

                <?php if (!$selected_complaint): ?>
                    <div class="empty-state">Select any complaint to view its details and update status.</div>
                <?php else: ?>
                    <div class="details-box">
                        <div class="details-title">
                            #<?php echo $selected_complaint['id']; ?> - <?php echo e($selected_complaint['title']); ?>
                        </div>

                        <div class="details-meta">
                            <?php echo e($selected_complaint['fullname']); ?>
                            (<?php echo e($selected_complaint['enrollment']); ?>)
                            • Room: <?php echo e($selected_complaint['room_no']); ?>
                            • Category: <?php echo e($selected_complaint['category']); ?>
                        </div>

                        <div class="mb-3 d-flex flex-wrap gap-2">
                            <span class="badge-soft <?php echo complaintPriorityClass($selected_complaint['priority']); ?>">
                                Priority: <?php echo e($selected_complaint['priority']); ?>
                            </span>

                            <span class="badge-soft <?php echo complaintStatusClass($selected_complaint['status']); ?>">
                                Status: <?php echo e($selected_complaint['status']); ?>
                            </span>
                        </div>

                        <label class="form-label">Description</label>
                        <div class="description-box">
                            <?php echo nl2br(e($selected_complaint['description'])); ?>
                        </div>
                    </div>

                    <form method="POST">
                        <input type="hidden" name="action" value="update_complaint">
                        <input type="hidden" name="complaint_id" value="<?php echo $selected_complaint['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <?php foreach (allowedComplaintStatuses() as $status): ?>
                                    <option value="<?php echo $status; ?>" <?php echo $selected_complaint['status'] === $status ? 'selected' : ''; ?>>
                                        <?php echo $status; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Assign to Staff</label>
                            <select name="assigned_staff_id" class="form-select">
                                <option value="">-- Not Assigned --</option>
                                <?php foreach ($staff_list as $staff): ?>
                                    <option value="<?php echo $staff['id']; ?>"
                                        <?php echo ((int)$selected_complaint['assigned_staff_id'] === (int)$staff['id']) ? 'selected' : ''; ?>>
                                        <?php echo e($staff['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <?php if (empty($staff_list)): ?>
                                <small class="text-muted">No staff records found.</small>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Admin Response / Note</label>
                            <textarea name="admin_response" class="form-control" rows="4" placeholder="Write a professional admin response..."><?php echo e($selected_complaint['admin_response']); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-save me-2"></i>Update Complaint
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../../includes/admin/admin_footer.php"; ?>
</body>
<script src="../../assets/js/admin_complaints.js"></script>
</html>