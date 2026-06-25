<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    $_SESSION['flash_message'] = "Hostel not assigned.";
    $_SESSION['flash_type'] = "danger";

    header("Location: visitor_entry.php");
    exit();
}

date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: visitor_entry.php");
    exit();
}

$name       = trim($_POST['name'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$relation   = trim($_POST['relation'] ?? '');
$purpose    = trim($_POST['purpose'] ?? '');
$address    = trim($_POST['address'] ?? '');
$student_id = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;

if ($name === '' || $phone === '' || $student_id <= 0) {
    $_SESSION['flash_message'] = "Please fill all required fields.";
    $_SESSION['flash_type'] = "danger";
    header("Location: visitor_entry.php");
    exit();
}
$checkStudent = mysqli_prepare(
    $conn,
    "SELECT id
     FROM students
     WHERE id = ?
     AND hostel_name = ?"
);

mysqli_stmt_bind_param(
    $checkStudent,
    "is",
    $student_id,
    $hostel_name
);

mysqli_stmt_execute($checkStudent);

$studentResult = mysqli_stmt_get_result($checkStudent);

if (mysqli_num_rows($studentResult) === 0) {

    $_SESSION['flash_message'] = "Invalid student selected.";
    $_SESSION['flash_type'] = "danger";

    header("Location: visitor_entry.php");
    exit();
}

$sql = "INSERT INTO visitors
(
    student_id,
    name,
    phone,
    purpose,
    address,
    relation,
    hostel_name,
    status,
    checkin_time,
    created_at
)
VALUES
(
    ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW()
)";

$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param(
    $stmt,
    "issssss",
    $student_id,
    $name,
    $phone,
    $purpose,
    $address,
    $relation,
    $hostel_name
);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['flash_message'] = "Visitor entry saved successfully. Waiting for admin approval.";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Failed to save visitor entry.";
        $_SESSION['flash_type'] = "danger";
    }
} else {
    $_SESSION['flash_message'] = "Something went wrong. Please contact admin.";
    $_SESSION['flash_type'] = "danger";
}

header("Location: visitor_entry.php");
exit();