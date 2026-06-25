<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if(empty($hostel_name)){
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}


// Optional admin login check
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: ../adminLogin.php");
//     exit;
// }

date_default_timezone_set('Asia/Kolkata');
$admin_name = $_SESSION['admin_name'] ?? "Admin";
$page_title = "Admin Notice";
$active_page = 'notices';

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}


$today = date('Y-m-d');
$notices = [];

$sql = "
SELECT *,
CASE
    WHEN start_date <= ?
    AND (
        end_date IS NULL
        OR end_date = ''
        OR end_date >= ?
    )
    THEN 'Active'
    ELSE 'Inactive'
END AS notice_status

FROM notices

WHERE hostel_name = ?

ORDER BY
is_pinned DESC,
created_at DESC,
id DESC
";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param(
    $stmt,
    "sss",
    $today,
    $today,
    $hostel_name
);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $notices[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
} else {
    echo "
    <script>
        alert('Failed to fetch notices');
        window.location='dashboard.php';
    </script>
    ";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include (__DIR__ . "/../../includes/admin/admin_header.php"); ?>
    <link rel="stylesheet" href="../../assets/css/admin_notices.css">
</head>
<body>

<?php include(__DIR__ . "/../../includes/admin/admin_sidebar.php"); ?>

<!-- Mobile Topbar -->
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

<main class="page-wrapper">
    <div class="container-fluid p-3 p-md-4">
        <div class="page-card p-4 p-md-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h1 class="section-title mb-1">Notice Management</h1>
                    <p class="section-subtitle mb-0">
                        Add, pin, view and manage all hostel notices from one place.
                    </p>
                </div>

                <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#addNoticeModal">
                    <i class="fa-solid fa-plus me-2"></i>Add Notice
                </button>
            </div>

            <?php if (!empty($notices)): ?>
                <div class="row g-3">
                    <?php foreach ($notices as $row): ?>
                        <div class="col-12">
                            <div class="card notice-card shadow-sm border-0">
                                <div class="card-body p-4">
                                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">

                                        <div class="flex-grow-1">
                                            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                                <?php if ((int)$row['is_pinned'] === 1): ?>
                                                    <span class="pinned-strip">
                                                        <i class="fa-solid fa-thumbtack me-1"></i>Pinned Notice
                                                    </span>
                                                <?php endif; ?>

                                                <span class="badge badge-soft-primary px-3 py-2">
                                                    <?php echo e(ucfirst($row['audience'])); ?>
                                                </span>

                                                <?php if ($row['notice_status'] === 'Active'): ?>
                                                    <span class="badge badge-soft-success px-3 py-2">Active</span>
                                                <?php else: ?>
                                                    <span class="badge badge-soft-secondary px-3 py-2">Inactive</span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="notice-title mb-2">
                                                <?php echo e($row['title']); ?>
                                            </div>

                                            <div class="notice-body mb-3">
                                                <?php echo nl2br(e($row['body'])); ?>
                                            </div>

                                            <div class="meta-text">
                                                <strong>Start:</strong>
                                                <?php echo !empty($row['start_date']) ? e(date('d M Y', strtotime($row['start_date']))) : 'N/A'; ?>
                                                &nbsp; | &nbsp;
                                                <strong>End:</strong>
                                                <?php echo !empty($row['end_date']) ? e(date('d M Y', strtotime($row['end_date']))) : 'N/A'; ?>
                                                &nbsp; | &nbsp;
                                                <strong>Posted:</strong>
                                                <?php echo !empty($row['created_at']) ? e(date('d M Y, h:i A', strtotime($row['created_at']))) : 'N/A'; ?>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-row flex-lg-column gap-2">
                                            <?php if ((int)$row['is_pinned'] === 1): ?>
                                                <a href="notice_pin.php?action=unpin&id=<?php echo (int)$row['id']; ?>"
                                                   class="btn btn-warning btn-sm btn-action">
                                                    <i class="fa-solid fa-thumbtack me-1"></i>Unpin
                                                </a>
                                            <?php else: ?>
                                                <a href="notice_pin.php?action=pin&id=<?php echo (int)$row['id']; ?>"
                                                   class="btn btn-outline-warning btn-sm btn-action">
                                                    <i class="fa-solid fa-thumbtack me-1"></i>Pin
                                                </a>
                                            <?php endif; ?>

                                            <a href="notice_delete.php?id=<?php echo (int)$row['id']; ?>"
                                               class="btn btn-outline-danger btn-sm btn-action"
                                               onclick="return confirm('Are you sure you want to delete this notice?');">
                                                <i class="fa-solid fa-trash me-1"></i>Delete
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">
                    No notices found.
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Add Notice Modal -->
<div class="modal fade" id="addNoticeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Add New Notice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="notice_add.php">
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notice Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter notice title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notice Description</label>
                        <textarea name="body" class="form-control" rows="5" placeholder="Write notice details here..." required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Audience</label>
                            <select name="audience" class="form-select" required>
                                <option value="student">Student</option>
                                <option value="security">Security</option>
                                <option value="admin">Admin</option>
                                <option value="all">All</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3 d-flex align-items-center">
                            <div class="form-check form-switch mt-md-4">
                                <input class="form-check-input" type="checkbox" name="is_pinned" id="is_pinned" value="1">
                                <label class="form-check-label ms-2" for="is_pinned">Pin this notice</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_notice" class="btn btn-primary px-4">Publish Notice</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>