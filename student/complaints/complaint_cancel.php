<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

// Login check
if (!isset($_SESSION['student_id'])) {
    header("Location: /smart_hostel/auth/students/student_login.php");
    exit;
}

$hostel_name = $_SESSION['hostel_name'] ?? '';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: student_complaints.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$id         = (int)($_POST['id'] ?? 0);
$action     = $_POST['action'] ?? '';
$reason     = trim($_POST['reason'] ?? '');

// if ($id <= 0 || $action === '' || $reason === '') {
//     header("Location: student_complaints.php?error=" . urlencode("Invalid request."));
//     exit;
// }

$allowed_actions = ['cancel', 'reopen'];

if (
    $id <= 0 ||
    !in_array($action, $allowed_actions) ||
    trim($reason) === ''
) {
    header("Location: student_complaints.php?error=" . urlencode("Invalid request."));
    exit;
}

// Ensure complaint belongs to this student (security)
// $chk = mysqli_prepare($conn, "SELECT id FROM complaints WHERE id=? AND student_id=?");
// mysqli_stmt_bind_param($chk, "ii", $id, $student_id);
// mysqli_stmt_execute($chk);
// $res = mysqli_stmt_get_result($chk);
// if (mysqli_num_rows($res) === 0) {
//     header("Location: student_complaints.php?error=" . urlencode("Complaint not found."));
//     exit;
// }

$chk = mysqli_prepare(
    $conn,
    "SELECT id
    FROM complaints
    WHERE id = ?
    AND student_id = ?
    AND hostel_name = ?
    LIMIT 1"
);

mysqli_stmt_bind_param(
    $chk,
    "iis",
    $id,
    $student_id,
    $hostel_name
);
mysqli_stmt_execute($chk);

$res = mysqli_stmt_get_result($chk);

if (!$res || mysqli_num_rows($res) === 0) {
    header("Location: student_complaints.php?error=" . urlencode("Complaint not found."));
    exit;
}


if ($action === 'cancel') {
    $sql = "UPDATE complaints
            SET status='Cancelled', cancel_reason=?, updated_at=NOW()
            WHERE id=? AND student_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $reason, $id, $student_id);
    mysqli_stmt_execute($stmt);

    header("Location: student_complaints.php?cancelled=1");
    exit;
}

if ($action === 'reopen') {
    $sql = "UPDATE complaints
            SET status='Reopened', reopen_reason=?, updated_at=NOW()
            WHERE id=? AND student_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $reason, $id, $student_id);
    mysqli_stmt_execute($stmt);

    header("Location: student_complaints.php?reopened=1");
    exit;
}

// Agar koi aur action ho to:
header("Location: student_complaints.php?error=" . urlencode("Unknown action."));
exit;