<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$student_id  = $_SESSION['student_id'] ?? 0;
$hostel_name = $_SESSION['hostel_name'] ?? '';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0 || $student_id <= 0 || empty($hostel_name)) {
    header("Location: student_leave.php?error=Invalid Request");
    exit;
}

$sql = "DELETE FROM student_leaves
        WHERE id = ?
        AND student_id = ?
        AND hostel_name = ?
        AND status = 'Pending'";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param(
    $stmt,
    "iis",
    $id,
    $student_id,
    $hostel_name
);

mysqli_stmt_execute($stmt);

header("Location: student_leave.php?cancelled=1");
exit;
?>