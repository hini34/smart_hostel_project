<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}

$student_id = (int)($_GET['id'] ?? 0);

if ($student_id <= 0) {
    header("Location: student.php?error=Invalid Student ID");
    exit();
}

/* =========================
   CHECK STUDENT BELONGS TO
   CURRENT ADMIN HOSTEL
========================= */

$sql = "
SELECT enrollment, photo
FROM students
WHERE id = ?
AND hostel_name = ?
LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    header("Location: student.php?error=Database Error");
    exit();
}

mysqli_stmt_bind_param(
    $stmt,
    "is",
    $student_id,
    $hostel_name
);

mysqli_stmt_execute($stmt);

$res = mysqli_stmt_get_result($stmt);

if (!$res || mysqli_num_rows($res) !== 1) {
    header("Location: student.php?error=Student Not Found Or Access Denied");
    exit();
}

$row = mysqli_fetch_assoc($res);

$enrollment = $row['enrollment'];
$photo      = $row['photo'];

/* =========================
   DELETE LOGIN ACCOUNT
========================= */

$stmtLogin = mysqli_prepare(
    $conn,
    "DELETE FROM logins WHERE username = ?"
);

mysqli_stmt_bind_param(
    $stmtLogin,
    "s",
    $enrollment
);

mysqli_stmt_execute($stmtLogin);

/* =========================
   DELETE COMPLAINTS
========================= */

$stmtComplaint = mysqli_prepare(
    $conn,
    "DELETE FROM complaints WHERE student_id = ?"
);

mysqli_stmt_bind_param(
    $stmtComplaint,
    "i",
    $student_id
);

mysqli_stmt_execute($stmtComplaint);

/* =========================
   DELETE LEAVE RECORDS
========================= */

$stmtLeave = mysqli_prepare(
    $conn,
    "DELETE FROM student_leaves WHERE student_id = ?"
);

mysqli_stmt_bind_param(
    $stmtLeave,
    "i",
    $student_id
);

mysqli_stmt_execute($stmtLeave);

/* =========================
   DELETE STUDENT
========================= */

$stmtStudent = mysqli_prepare(
    $conn,
    "DELETE FROM students
     WHERE id = ?
     AND hostel_name = ?"
);

mysqli_stmt_bind_param(
    $stmtStudent,
    "is",
    $student_id,
    $hostel_name
);

mysqli_stmt_execute($stmtStudent);

/* =========================
   DELETE PHOTO
========================= */

if (!empty($photo)) {

    $path = __DIR__ . "/../../" . $photo;

    if (file_exists($path)) {
        unlink($path);
    }
}

/* =========================
   REDIRECT
========================= */

header("Location: student.php?success=Student Deleted Successfully");
exit();
?>