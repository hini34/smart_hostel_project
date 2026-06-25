<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: /smart_hostel/auth/students/student_login.php");
    exit;
}

$page_title   = "Complaints ";
$active_page  = "complaints";
$student_id   = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'] ?? $_SESSION['student'] ?? "Student";
$room_no      = $_SESSION['room_no'] ?? "";

$hostel_name = $_SESSION['hostel_name'] ?? '';

$sql = "SELECT *
        FROM complaints
        WHERE student_id = ?
        AND hostel_name = ?
        ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param(
    $stmt,
    "is",
    $student_id,
    $hostel_name
);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$complaints_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $complaints_data[] = $row;
}

$total_complaints   = count($complaints_data);
$pending_count      = 0;
$progress_count     = 0;
$resolved_count     = 0;
$cancelled_count    = 0;

foreach ($complaints_data as $row) {
    $status = strtolower(trim($row['status'] ?? ''));
    if ($status === 'pending') $pending_count++;
    elseif ($status === 'in progress') $progress_count++;
    elseif ($status === 'resolved') $resolved_count++;
    elseif ($status === 'cancelled') $cancelled_count++;
}

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function badgeClass($type, $value)
{
    $value = strtolower(trim($value));

    if ($type === 'status') {
        return match ($value) {
            'pending'     => 'badge-pending',
            'in progress' => 'badge-progress',
            'resolved'    => 'badge-resolved',
            'cancelled'   => 'badge-cancelled',
            'reopened'    => 'badge-reopened',
            default       => 'badge-default',
        };
    }

    if ($type === 'priority') {
        return match ($value) {
            'low'    => 'badge-low',
            'medium' => 'badge-medium',
            'high'   => 'badge-high',
            default  => 'badge-default',
        };
    }

    return 'badge-default';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../../includes/student/student_header.php'; ?>
    <link rel="stylesheet" href="../../assets/css/student/student_complaints.css">
</head>
<body>

<nav class="navbar navbar-dark navbar-main d-md-none fixed-top">
    <div class="container-fluid">
        <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#studentSidebar">
            <i class="fa-solid fa-bars"></i>
        </button>
        <a class="navbar-brand d-flex align-items-center" href="#">
            <i class="fa-solid fa-building-user me-2"></i>
            <span>Smart Hostel - Student</span>
        </a>
        <span class="badge bg-primary rounded-pill">
            <?php echo htmlspecialchars($student_name); ?>
        </span>
    </div>
</nav>

<?php include __DIR__ . '/../../includes/student/student_sidebar.php'; ?>

<div class="content-wrapper">
    <div class="container-fluid px-0">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <?php if (!empty($_GET['success'])): ?>
        <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Complaint submitted successfully!'
        });
        </script>
        <?php endif; ?>

        <?php if (!empty($_GET['cancelled'])): ?>
        <script>
        Swal.fire({
            icon: 'success',
            title: 'Cancelled',
            text: 'Complaint cancelled successfully!'
        });
        </script>
        <?php endif; ?>

        <?php if (!empty($_GET['reopened'])): ?>
        <script>
        Swal.fire({
            icon: 'info',
            title: 'Reopened',
            text: 'Complaint reopened successfully!'
        });
        </script>
        <?php endif; ?>

        <?php if (!empty($_GET['error'])): ?>
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo e($_GET['error']); ?>'
        });
        </script>
        <?php endif; ?>
        

        <!-- Page Header -->
        <div class="page-hero">

            <div>
                <h1 class="page-title">Complaint Status</h1>

                <p class="page-subtitle">
                    Raise Complaint and Show the Complaint Status.
                </p>
            </div>

            <div class="room-status">
                <div class="room-status-label">
                    COMPLAINT STATUS
                </div>

                <div class="room-status-badge">
                    Active
                </div>
            </div>

        </div>

        <div class="row g-4 mb-4">
            <div class="col-6 col-xl-3">
                <div class="summary-card">
                    <div class="summary-icon bg-soft-primary">
                        <i class="fa-solid fa-file-circle-exclamation"></i>
                    </div>
                    <div>
                        <div class="summary-title">Total Complaints</div>
                        <div class="summary-value"><?php echo $total_complaints; ?></div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="summary-card">
                    <div class="summary-icon bg-soft-warning">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <div class="summary-title">Pending</div>
                        <div class="summary-value"><?php echo $pending_count; ?></div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="summary-card">
                    <div class="summary-icon bg-soft-info">
                        <i class="fa-solid fa-spinner"></i>
                    </div>
                    <div>
                        <div class="summary-title">In Progress</div>
                        <div class="summary-value"><?php echo $progress_count; ?></div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="summary-card">
                    <div class="summary-icon bg-soft-success">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <div class="summary-title">Resolved</div>
                        <div class="summary-value"><?php echo $resolved_count; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-card">
            <div class="card-header-custom">
                <ul class="nav nav-pills custom-pills" id="complaintTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="raise-tab" data-bs-toggle="pill"
                                data-bs-target="#raise" type="button" role="tab">
                            <i class="fa-solid fa-plus-circle me-1"></i> Raise Complaint
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="status-tab" data-bs-toggle="pill"
                                data-bs-target="#status" type="button" role="tab">
                            <i class="fa-solid fa-list-check me-1"></i> Complaint Status / History
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body-custom">
                <div class="tab-content">

                    <div class="tab-pane fade show active" id="raise" role="tabpanel">
                        <div class="form-scroll">
                            <form action="complaint_save.php" method="POST" enctype="multipart/form-data" class="row g-3">
                                <input type="hidden" name="student_id" value="<?php echo (int)$student_id; ?>">

                                <div class="col-md-6">
                                    <label class="form-label">Complaint Title</label>
                                    <input type="text" name="title" class="form-control custom-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Complaint Category</label>
                                    <select name="category" class="form-select custom-control" required>
                                        <option value="">-- Select --</option>
                                        <option>Electricity</option>
                                        <option>Water</option>
                                        <option>Cleaning</option>
                                        <option>Room Maintenance</option>
                                        <option>WiFi / Security</option>
                                        <option>Other</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control custom-control" rows="4" required></textarea>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Room Number</label>
                                    <input type="text" name="room_no" class="form-control custom-control" value="<?php echo e($room_no); ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Priority</label>
                                    <select name="priority" class="form-select custom-control" required>
                                        <option value="Low">Low</option>
                                        <option value="Medium" selected>Medium</option>
                                        <option value="High">High</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Upload Image</label>
                                    <input type="file" name="image" class="form-control custom-control" accept="image/*">
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" name="submit" class="btn btn-primary submit-btn">
                                        <i class="fa-solid fa-paper-plane me-1"></i> Submit Complaint
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="status" role="tabpanel">
                        <div class="table-responsive complaint-table-wrap">
                            <table class="table complaint-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Resolved Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (count($complaints_data) === 0): ?>
                                    <tr>
                                        <td colspan="8">
                                            <div class="empty-state">
                                                <i class="fa-solid fa-inbox"></i>
                                                <h5>No complaints yet</h5>
                                                <p>Your submitted complaints will appear here.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $i = 1; foreach ($complaints_data as $row): ?>
                                        <?php
                                            $resolved_date = '-';
                                            if (!empty($row['resolved_at']) && $row['resolved_at'] !== '0000-00-00 00:00:00') {
                                                $resolved_date = date('d M Y', strtotime($row['resolved_at']));
                                            }
                                        ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo e($row['title']); ?></td>
                                            <td><?php echo e($row['category']); ?></td>
                                            <td>
                                                <span class="custom-badge <?php echo badgeClass('priority', $row['priority'] ?? ''); ?>">
                                                    <?php echo e($row['priority']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="custom-badge <?php echo badgeClass('status', $row['status'] ?? ''); ?>">
                                                    <?php echo e($row['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '-'; ?></td>
                                            <td><?php echo $resolved_date; ?></td>
                                            <td>
                                                <div class="action-group">
                                                    <button class="btn btn-sm btn-outline-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#viewModal<?php echo (int)$row['id']; ?>">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </button>

                                                    <?php if (in_array($row['status'], ['Pending', 'In Progress'])): ?>
                                                        <button class="btn btn-sm btn-outline-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#cancelModal<?php echo (int)$row['id']; ?>">
                                                            Cancel
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if (($row['status'] ?? '') === 'Resolved'): ?>
                                                        <button class="btn btn-sm btn-outline-warning"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#reopenModal<?php echo (int)$row['id']; ?>">
                                                            Reopen
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
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

    </div>
</div>

<?php foreach ($complaints_data as $row): ?>
    <div class="modal fade" id="viewModal<?php echo (int)$row['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content custom-modal">
                <div class="modal-header">
                    <h5 class="modal-title">Complaint Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-item"><strong>Title:</strong> <?php echo e($row['title']); ?></div>
                    <div class="detail-item"><strong>Category:</strong> <?php echo e($row['category']); ?></div>
                    <div class="detail-item"><strong>Priority:</strong> <?php echo e($row['priority']); ?></div>
                    <div class="detail-item"><strong>Room:</strong> <?php echo e($row['room_no']); ?></div>
                    <div class="detail-item"><strong>Description:</strong><br><?php echo nl2br(e($row['description'])); ?></div>

                    <?php if (!empty($row['image_path'])): ?>
                        <div class="detail-item">
                            <strong>Image:</strong><br>
                            <img src="../../<?php echo e($row['image_path']); ?>" class="img-fluid rounded complaint-image" alt="Complaint Image">
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($row['cancel_reason'])): ?>
                        <div class="detail-item"><strong>Cancel Reason:</strong><br><?php echo nl2br(e($row['cancel_reason'])); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($row['resolution_note'])): ?>
                        <div class="detail-item"><strong>Resolution Note:</strong><br><?php echo nl2br(e($row['resolution_note'])); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($row['reopen_reason'])): ?>
                        <div class="detail-item"><strong>Reopen Reason:</strong><br><?php echo nl2br(e($row['reopen_reason'])); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancelModal<?php echo (int)$row['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content custom-modal" method="POST" action="complaint_cancel.php">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Complaint</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                    <input type="hidden" name="action" value="cancel">

                    <label class="form-label">Reason for cancellation</label>
                    <textarea name="reason" class="form-control custom-control" rows="4" required placeholder="Enter cancellation reason..."></textarea>

                    <small class="text-muted d-block mt-2">Once cancelled, this complaint will move to history.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Complaint</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="reopenModal<?php echo (int)$row['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content custom-modal" method="POST" action="complaint_cancel.php">
                <div class="modal-header">
                    <h5 class="modal-title">Reopen Complaint</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                    <input type="hidden" name="action" value="reopen">

                    <label class="form-label">Why do you want to reopen?</label>
                    <textarea name="reason" class="form-control custom-control" rows="4" required placeholder="Enter reopen reason..."></textarea>

                    <small class="text-muted d-block mt-2">Use this if the issue is not actually fixed.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">Reopen</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<?php include __DIR__ . '/../../includes/student/student_footer.php'; ?>
<script src="../../assets/js/student/student_complaint.js"></script>
</body>
</html>