<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

date_default_timezone_set('Asia/Kolkata');

$admin_name  = $_SESSION['admin_name'] ?? "Admin";
$page_title  = "Visitors Management";
$active_page = 'visitors';

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$search = trim($_GET['search'] ?? '');

$sql = "
    SELECT 
        v.*,
        s.fullname AS student_name,
        s.enrollment,
        r.room_no
    FROM visitors v
    INNER JOIN students s ON v.student_id = s.id
    LEFT JOIN rooms r ON s.room_id = r.id
    WHERE v.hostel_name = ?
";

$params = [$hostel_name];
$types  = "s";

if ($search !== '') {
    $sql .= " AND (
    v.name LIKE ?
    OR v.phone LIKE ?
    OR s.fullname LIKE ?
    OR s.enrollment LIKE ?
    OR v.purpose LIKE ?
    OR v.status LIKE ?
    OR r.room_no LIKE ?
    )";

    $like = "%{$search}%";

    $params = [
        $hostel_name,
        $like,
        $like,
        $like,
        $like,
        $like,
        $like,
        $like
    ];

    $types = "s" . str_repeat("s", 7);
}

$sql .= " ORDER BY v.id DESC";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
     echo "
    <script>
        alert('Unable to load visitor records');
        window.location='visitors.php';
    </script>
    ";
    exit;
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$flash_message = $_SESSION['flash_message'] ?? '';
$flash_type    = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include(__DIR__ . "/../../includes/admin/admin_header.php"); ?>
    <link rel="stylesheet" href="../../assets/css/admin_visitors.css">
</head>
<body>

<?php include(__DIR__ . "/../../includes/admin/admin_sidebar.php"); ?>

<nav class="navbar navbar-dark navbar-main d-lg-none fixed-top">
    <div class="container-fluid">
        <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar">
            <i class="fa-solid fa-bars"></i>
        </button>

        <a class="navbar-brand d-flex align-items-center" href="#">
            <i class="fa-solid fa-building-user me-2"></i>
            <span>Smart Hostel - Admin</span>
        </a>

        <span class="badge bg-primary rounded-pill">
            <?php echo e($admin_name); ?>
        </span>
    </div>
</nav>

<main class="page-wrapper">
    <div class="page-card">

        <div class="page-header-box mb-4">
            <div>
                <h2 class="page-heading mb-1">
                    <i class="fa-solid fa-user-check me-2"></i> Visitor Approval Management
                </h2>
                <p class="small-muted mb-0">
                    Admin pending visitors ko approve, reject aur approved visitor ko checkout mark kar sakta hai.
                </p>
            </div>
        </div>

        <?php if (!empty($flash_message)): ?>
            <div class="alert alert-<?php echo e($flash_type ?: 'info'); ?> alert-dismissible fade show" role="alert">
                <?php echo e($flash_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card search-card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-center">
                    <div class="col-md-9">
                        <input
                            type="text"
                            name="search"
                            class="form-control search-input"
                            placeholder="Search visitor, student, phone, room..."
                            value="<?php echo e($search); ?>"
                        >
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                        <a href="visitors.php" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card table-card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>All Visitor Records</strong>
                <span class="text-muted small">
                    Total Records: <?php echo ($result) ? mysqli_num_rows($result) : 0; ?>
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 visitor-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Visitor</th>
                                <th>Phone</th>
                                <th>Student</th>
                                <th>Enrollment</th>
                                <th>Room</th>
                                <th>Purpose</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                                <th style="min-width: 220px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                <?php $i = 1; ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <?php
                                        $status = strtolower(trim($row['status'] ?? 'pending'));

                                        if ($status === 'approved') {
                                            $statusClass = 'status-approved';
                                        } elseif ($status === 'rejected') {
                                            $statusClass = 'status-rejected';
                                        } else {
                                            $statusClass = 'status-pending';
                                        }
                                    ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>

                                        <td>
                                            <div class="fw-semibold"><?php echo e($row['name'] ?? '-'); ?></div>
                                            <div class="small text-muted"><?php echo e($row['relation'] ?? '-'); ?></div>
                                        </td>

                                        <td><?php echo e($row['phone'] ?? '-'); ?></td>
                                        <td><?php echo e($row['student_name'] ?? '-'); ?></td>
                                        <td><?php echo e($row['enrollment'] ?? '-'); ?></td>
                                        <td><?php echo e($row['room_no'] ?? 'Not Assigned'); ?></td>
                                        <td><?php echo e($row['purpose'] ?? '-'); ?></td>

                                        <td>
                                            <?php
                                                echo !empty($row['checkin_time'])
                                                    ? e(date('Y-m-d h:i:s A', strtotime($row['checkin_time'])))
                                                    : '-';
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                                if (!empty($row['checkout_time'])) {
                                                    echo e(date('Y-m-d h:i:s A', strtotime($row['checkout_time'])));
                                                } else {
                                                    echo '<span class="text-muted">---</span>';
                                                }
                                            ?>
                                        </td>

                                        <td>
                                            <span class="status-badge <?php echo e($statusClass); ?>">
                                                <?php echo e(ucfirst($row['status'] ?? 'Pending')); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <div class="d-flex flex-wrap gap-2">

                                                <?php if ($status === 'pending'): ?>
                                                    <form method="POST" action="update_visitor_status.php" class="d-inline">
                                                        <input type="hidden" name="visitor_id" value="<?php echo (int)$row['id']; ?>">
                                                        <input type="hidden" name="action" value="approve">
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fa-solid fa-check me-1"></i> Approve
                                                        </button>
                                                    </form>

                                                    <form method="POST" action="update_visitor_status.php" class="d-inline">
                                                        <input type="hidden" name="visitor_id" value="<?php echo (int)$row['id']; ?>">
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fa-solid fa-xmark me-1"></i> Reject
                                                        </button>
                                                    </form>

                                                <?php elseif ($status === 'approved' && empty($row['checkout_time'])): ?>
                                                    <form method="POST" action="update_visitor_status.php" class="d-inline">
                                                        <input type="hidden" name="visitor_id" value="<?php echo (int)$row['id']; ?>">
                                                        <input type="hidden" name="action" value="checkout">
                                                        <button type="submit" class="btn btn-sm btn-warning">
                                                            <i class="fa-solid fa-right-from-bracket me-1"></i> Check Out
                                                        </button>
                                                    </form>

                                                <?php elseif ($status === 'rejected'): ?>
                                                    <span class="text-muted small">Rejected</span>

                                                <?php else: ?>
                                                    <span class="text-muted small">No Action</span>
                                                <?php endif; ?>

                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center py-4 text-muted">
                                        No visitor records found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>