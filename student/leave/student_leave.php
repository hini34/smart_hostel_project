<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

// Login check
if (!isset($_SESSION['student_id'])) {
    header("Location: /smart_hostel/auth/student/student_login.php");
    exit;
}

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    die("Hostel not found");
}


$page_title   = "Leave Request | Smart Hostel";
$active_page  = "leave";
$student_id   = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'] ?? $_SESSION['student'] ?? "Student";

// Leave records
$leaveRows = [];
$totalLeaves = 0;
$pendingLeaves = 0;
$approvedLeaves = 0;
$rejectedLeaves = 0;
$dbError = "";

// Fetch leave history
    try {
    $stmt = $conn->prepare(
        "SELECT *
        FROM student_leaves
        WHERE student_id = ?
        AND hostel_name = ?
        ORDER BY id DESC"
    );

    $stmt->bind_param(
        "is",
        $student_id,
        $hostel_name
    );

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $leaveRows[] = $row;
    }
    $stmt->close();
    $totalLeaves = count($leaveRows);

    foreach ($leaveRows as $row) {
        $status = strtolower(trim($row['status'] ?? ''));
        if ($status === 'pending') {
            $pendingLeaves++;
        } elseif ($status === 'approved') {
            $approvedLeaves++;
        } elseif ($status === 'rejected') {
            $rejectedLeaves++;
        }
    }
} catch (mysqli_sql_exception $e) {
    $dbError = $e->getMessage();
}

// Flash messages
$successMsg = isset($_GET['success']) ? "Leave request submitted successfully." : "";
$errorMsg   = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : "";

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function getStatusClass($status)
{
    $status = strtolower(trim($status));
    return match ($status) {
        'approved' => 'status-approved',
        'pending'  => 'status-pending',
        'rejected' => 'status-rejected',
        default    => 'status-default',
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../../includes/student/student_header.php'; ?>
    <link rel="stylesheet" href="../../assets/css/student/student_sidebar.css">
    <link rel="stylesheet" href="../../assets/css/student/student_leave.css">
</head>
<body>

<!-- MOBILE NAVBAR -->
<nav class="navbar navbar-dark navbar-main d-md-none fixed-top">
    <div class="container-fluid">
        <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#studentSidebar">
            <i class="fa-solid fa-bars"></i>
        </button>

        <a class="navbar-brand d-flex align-items-center" href="#">
            <i class="fa-solid fa-building-user me-2"></i>
            <span>Smart Hostel</span>
        </a>

        <span class="badge bg-primary rounded-pill px-3 py-2">
            <?php echo e($student_name); ?>
        </span>
    </div>
</nav>

<?php include __DIR__ . '/../../includes/student/student_sidebar.php'; ?>

<div class="content-wrapper">
    <div class="container-fluid px-0">

        <!-- Page Header -->
        <div class="page-hero">

            <div>
                <h1 class="page-title">Leave Request</h1>

                <p class="page-subtitle">
                    Submit your leave request and track its approval status here.
                </p>
            </div>

            <div class="room-status">
                <div class="room-status-label">
                    REQUEST STATUS
                </div>

                <div class="room-status-badge">
                    Active
                </div>
            </div>

        </div>

        <!-- Alerts -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <?php if (isset($_GET['success'])): ?>
            <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Leave submitted successfully!'
            });
            </script>
            <?php endif; ?>

            <?php if (isset($_GET['cancelled'])): ?>
            <script>
            Swal.fire({
                icon: 'success',
                title: 'Cancelled',
                text: 'Leave cancelled successfully!'
            });
            </script>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
            <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo htmlspecialchars($_GET['error']); ?>'
            });
            </script>
            <?php endif; ?>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-6 col-xl-3">
                <div class="summary-card">
                    <div class="summary-icon bg-soft-primary">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-title">Total Requests</div>
                        <div class="summary-value"><?php echo $totalLeaves; ?></div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="summary-card">
                    <div class="summary-icon bg-soft-warning">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-title">Pending</div>
                        <div class="summary-value"><?php echo $pendingLeaves; ?></div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="summary-card">
                    <div class="summary-icon bg-soft-success">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-title">Approved</div>
                        <div class="summary-value"><?php echo $approvedLeaves; ?></div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="summary-card">
                    <div class="summary-icon bg-soft-danger">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-title">Rejected</div>
                        <div class="summary-value"><?php echo $rejectedLeaves; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Leave Form -->
            <div class="col-xl-5">
                <div class="main-card h-100">
                    <div class="card-header-custom">
                        <h2 class="section-title">
                            <i class="fa-solid fa-paper-plane me-2 text-primary"></i>
                            Apply for Leave
                        </h2>
                        <p class="section-subtitle mb-0">
                            Fill all required details carefully before submitting your request.
                        </p>
                    </div>

                    <div class="card-body-custom">
                        <div class="form-scroll">

                            <form action="submit_leave.php" method="POST" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">From Date</label>
                                        <input type="date" name="from_date" class="form-control custom-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">To Date</label>
                                        <input type="date" name="to_date" class="form-control custom-control" required>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Leave Type</label>
                                        <select name="leave_type" class="form-select custom-control" required>
                                            <option value="">Select Leave Type</option>
                                            <option value="Casual">Casual</option>
                                            <option value="Medical">Medical</option>
                                            <option value="Emergency">Emergency</option>
                                            <option value="Homevisit">Home Visit</option>
                                            <option value="Vacation">Vacation</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Reason</label>
                                        <textarea name="reason" class="form-control custom-control" rows="3" placeholder="Write the reason for your leave..." required></textarea>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Going Address</label>
                                        <textarea name="going_address" class="form-control custom-control" rows="3" placeholder="Enter your stay address during leave..." required></textarea>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" name="contact_number" class="form-control custom-control" placeholder="Your number" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Parent Contact</label>
                                        <input type="text" name="parent_number" class="form-control custom-control" placeholder="Parent number" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Emergency Contact</label>
                                        <input type="text" name="emergency_number" class="form-control custom-control" placeholder="Emergency number" required>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Upload Document</label>
                                        <input type="file" name="document" class="form-control custom-control">
                                        <div class="helper-text mt-2">
                                            Upload supporting proof like medical certificate if needed.
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary submit-btn">
                                            <i class="fa-solid fa-paper-plane me-2"></i>
                                            Submit Leave Request
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave History -->
            <div class="col-xl-7">
                <div class="main-card ">
                    <div class="card-header-custom">
                        <h2 class="section-title">
                            <i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>
                            Leave History
                        </h2>
                        <p class="section-subtitle mb-0">
                            Review your previously submitted leave requests and current status.
                        </p>
                    </div>

                    <div class="card-body-custom">
                        <div class="table-responsive leave-table-wrap">
                            <table class="table leave-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!$dbError && count($leaveRows) > 0): ?>
                                        <?php $sr = 1; ?>
                                        <?php foreach ($leaveRows as $row): ?>
                                            <?php
                                                $statusRaw = trim($row['status'] ?? 'Pending');
                                                $statusClass = getStatusClass($statusRaw);
                                            ?>
                                            <tr>
                                                <td><?php echo $sr++; ?></td>
                                                <td><?php echo e($row['from_date'] ?? ''); ?></td>
                                                <td><?php echo e($row['to_date'] ?? ''); ?></td>
                                                <td><?php echo e($row['leave_type'] ?? ''); ?></td>
                                                <td>
                                                    <span class="badge-status <?php echo $statusClass; ?>">
                                                        <?php echo e($statusRaw); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (strtolower($statusRaw) === 'pending'): ?>
                                                        <a href="cancel_leave.php?id=<?php echo (int)$row['id']; ?>"
                                                           class="btn btn-sm btn-outline-danger action-btn-sm"
                                                           onclick="return confirm('Are you sure you want to cancel this leave request?')">
                                                            <i class="fa-solid fa-ban me-1"></i>Cancel
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="no-action">No action</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php elseif (!$dbError): ?>
                                        <tr>
                                            <td colspan="6">
                                                <div class="empty-state">
                                                    <i class="fa-solid fa-calendar-xmark"></i>
                                                    <h5>No leave requests found</h5>
                                                    <p>Your submitted leave applications will appear here.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-danger fw-semibold py-4">
                                                Unable to load leave records.
                                            </td>
                                        </tr>
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

<?php include __DIR__ . '/../../includes/student/student_footer.php'; ?>
</body>
</html>