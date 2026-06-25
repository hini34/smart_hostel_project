<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? "Admin";
$page_title = "Manage Student Leaves";
$active_page = "leave";

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}

date_default_timezone_set('Asia/Kolkata');

function e($str)
{
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

$status_filter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');

/* =========================
   LEAVES QUERY
========================= */

$sql = "
SELECT
    sl.id,
    sl.student_id,
    sl.leave_type,
    sl.reason,
    sl.from_date,
    sl.to_date,
    sl.going_address,
    sl.contact_number,
    sl.emergency_contact,
    sl.status,
    sl.admin_remark,
    sl.approved_at,
    sl.applied_on,

    s.fullname,
    s.enrollment,
    s.course,
    s.department,
    s.semester,

    r.room_no,
    r.block_name,

    a.username AS approved_by_name

FROM student_leaves sl

INNER JOIN students s
    ON sl.student_id = s.id

LEFT JOIN rooms r
    ON s.room_id = r.id

LEFT JOIN admins a
    ON sl.approved_by = a.id

WHERE s.hostel_name = ?
";

$params = [$hostel_name];
$types = "s";

if ($status_filter != '') {

    $sql .= " AND sl.status = ?";

    $params[] = $status_filter;
    $types .= "s";
}

if ($search != '') {

    $sql .= "
    AND (
        s.fullname LIKE ?
        OR s.enrollment LIKE ?
        OR s.department LIKE ?
        OR r.room_no LIKE ?
    )
    ";

    $like = "%{$search}%";

    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;

    $types .= "ssss";
}

$sql .= "
ORDER BY
CASE
    WHEN sl.status='Pending' THEN 1
    WHEN sl.status='Approved' THEN 2
    WHEN sl.status='Rejected' THEN 3
    ELSE 4
END,
sl.id DESC
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Prepare Failed : " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

/* =========================
   STATS
========================= */

$stats = [
    'total' => 0,
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0
];

$statsSql = "
SELECT
    COUNT(*) AS total,

    SUM(
        CASE
            WHEN sl.status='Pending'
            THEN 1 ELSE 0
        END
    ) AS pending,

    SUM(
        CASE
            WHEN sl.status='Approved'
            THEN 1 ELSE 0
        END
    ) AS approved,

    SUM(
        CASE
            WHEN sl.status='Rejected'
            THEN 1 ELSE 0
        END
    ) AS rejected

FROM student_leaves sl

INNER JOIN students s
    ON sl.student_id = s.id

WHERE s.hostel_name = '$hostel_name'
";

$statsRes = mysqli_query($conn, $statsSql);

if ($statsRes && mysqli_num_rows($statsRes) > 0) {
    $stats = mysqli_fetch_assoc($statsRes);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include(__DIR__ . "/../../includes/admin/admin_header.php"); ?>
    <link rel="stylesheet" href="../../assets/css/manage_leaves.css">
</head>
<body>
<div class="admin-layout">
    <!-- Mobile Navbar -->
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
    
    <?php include(__DIR__ . "/../../includes/admin/admin_sidebar.php"); ?>

    <main class="admin-content">
        <div class="page-header">
            <div>
                <h1>Student Leave Management</h1>
                <p>Approve or reject student leave requests professionally.</p>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card total">
                <h3>Total Leaves</h3>
                <h2><?php echo (int)$stats['total']; ?></h2>
            </div>
            <div class="stat-card pending">
                <h3>Pending</h3>
                <h2><?php echo (int)$stats['pending']; ?></h2>
            </div>
            <div class="stat-card approved">
                <h3>Approved</h3>
                <h2><?php echo (int)$stats['approved']; ?></h2>
            </div>
            <div class="stat-card rejected">
                <h3>Rejected</h3>
                <h2><?php echo (int)$stats['rejected']; ?></h2>
            </div>
        </div>

        <div class="filter-card">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, enrollment, room..." value="<?php echo e($search); ?>">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Approved" <?php echo $status_filter === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="Rejected" <?php echo $status_filter === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-5 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="manage_leaves.php" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-card">
            <div class="table-responsive">
                <table class="table align-middle custom-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Enrollment</th>
                            <th>Room</th>
                            <th>Department</th>
                            <th>Leave Dates</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Remark</th>
                            <th width="240">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php $i = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td>
                                    <strong><?php echo e($row['fullname']); ?></strong><br>
                                    <small>Sem: <?php echo e($row['semester']); ?></small>
                                </td>
                                <td><?php echo e($row['enrollment']); ?></td>
                                <td>
                                    <?php echo e($row['room_no'] ?? 'N/A'); ?><br>
                                    <small><?php echo e($row['block_name'] ?? ''); ?></small>
                                </td>
                                <td><?php echo e($row['department'] ?? $row['course'] ?? 'N/A'); ?></td>
                                <td>
                                    <strong><?php echo e($row['from_date']); ?></strong><br>
                                    to<br>
                                    <strong><?php echo e($row['to_date']); ?></strong>
                                </td>
                                <td><?php echo e($row['leave_type']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                        <?php echo e($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo e($row['admin_remark'] ?: '-'); ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'Pending'): ?>
                                        <form action="leave_action.php" method="POST" class="action-form mb-2">
                                            <input type="hidden" name="leave_id" value="<?php echo (int)$row['id']; ?>">
                                            <input type="hidden" name="action" value="Approved">
                                            <textarea name="admin_remark" class="form-control form-control-sm mb-2" placeholder="Approval remark"></textarea>
                                            <button type="submit" class="btn btn-success btn-sm w-100">Approve</button>
                                        </form>

                                        <form action="leave_action.php" method="POST" class="action-form">
                                            <input type="hidden" name="leave_id" value="<?php echo (int)$row['id']; ?>">
                                            <input type="hidden" name="action" value="Rejected">
                                            <textarea name="admin_remark" class="form-control form-control-sm mb-2" placeholder="Reject reason"></textarea>
                                            <button type="submit" class="btn btn-danger btn-sm w-100">Reject</button>
                                        </form>
                                    <?php else: ?>
                                        <div class="small text-muted">
                                            <?php if (!empty($row['approved_by_name'])): ?>
                                                By: <strong><?php echo e($row['approved_by_name']); ?></strong><br>
                                            <?php endif; ?>
                                            <?php if (!empty($row['approved_at'])): ?>
                                                On: <?php echo e($row['approved_at']); ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-4">No leave records found.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include(__DIR__ . "/../../includes/admin/admin_footer.php"); ?>
</body>
</html>