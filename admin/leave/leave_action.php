<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}

date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_leaves.php");
    exit();
}

$leave_id      = (int)($_POST['leave_id'] ?? 0);
$action        = trim($_POST['action'] ?? '');
$admin_remark  = trim($_POST['admin_remark'] ?? '');

$allowedStatus = ['Approved', 'Rejected'];

if (
    $leave_id <= 0 ||
    !in_array($action, $allowedStatus, true)
) {
    header("Location: manage_leaves.php");
    exit();
}

$admin_id = $_SESSION['admin_id'] ?? 0;

/* CHECK LEAVE BELONGS TO SAME HOSTEL */

$checkSql = "
SELECT sl.id
FROM student_leaves sl
INNER JOIN students s
    ON sl.student_id = s.id
WHERE sl.id = ?
AND s.hostel_name = ?
LIMIT 1
";

$checkStmt = mysqli_prepare($conn, $checkSql);

mysqli_stmt_bind_param(
    $checkStmt,
    "is",
    $leave_id,
    $hostel_name
);

mysqli_stmt_execute($checkStmt);

$checkRes = mysqli_stmt_get_result($checkStmt);

if (
    !$checkRes ||
    mysqli_num_rows($checkRes) === 0
) {
    header("Location: manage_leaves.php");
    exit();
}

/* UPDATE LEAVE */

$sql = "
UPDATE student_leaves
SET
    status = ?,
    admin_remark = ?,
    approved_by = ?,
    approved_at = NOW()
WHERE id = ?
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ssii",
    $action,
    $admin_remark,
    $admin_id,
    $leave_id
);

mysqli_stmt_execute($stmt);

header("Location: manage_leaves.php");
exit();
?>