<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    die("Hostel not found");
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// 1) Sirf POST request allow
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: student_leave.php");
    exit;
}

// 2) Student login check
if (!isset($_SESSION['student_id'])) {
    die("Student not logged in");
}

$student_id = (int)$_SESSION['student_id'];

// 3) Form se values lo (names same jaise form me)
$from_date        = $_POST['from_date']        ?? null;
$to_date          = $_POST['to_date']          ?? null;
$leave_type       = $_POST['leave_type']       ?? null;
$reason           = $_POST['reason']           ?? null;
$going_address    = $_POST['going_address']    ?? null;
$contact_number   = $_POST['contact_number']   ?? null;
$parent_number    = $_POST['parent_number']    ?? null;      
$emergency_number = $_POST['emergency_number'] ?? null;

// 4) Basic validation 
if (
    empty($from_date) ||
    empty($to_date) ||
    empty($leave_type) ||
    empty($reason) ||
    empty($going_address) ||
    empty($contact_number) ||
    empty($emergency_number)
) {
    die("Required fields missing");
}

// 5) Document upload
$document = null;

if (!empty($_FILES['document']['name'])) {
    $uploadDir = __DIR__ . "/uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $document = time() . "_" . basename($_FILES['document']['name']);
    move_uploaded_file($_FILES['document']['tmp_name'], $uploadDir . $document);
}

// 6) Insert query 
$stmt = $conn->prepare("
    INSERT INTO student_leaves
    (
        student_id,
        hostel_name,
        from_date,
        to_date,
        leave_type,
        reason,
        going_address,
        contact_number,
        emergency_contact,
        document,
        status
    )
    VALUES
    (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending'
    )
");

$stmt->bind_param(
    "isssssssss",
    $student_id,
    $hostel_name,
    $from_date,
    $to_date,
    $leave_type,
    $reason,
    $going_address,
    $contact_number,
    $emergency_number,
    $document
);

try {
    $stmt->execute();
    header("Location: student_leave.php?success=1");
    exit;
} catch (mysqli_sql_exception $e) {
    echo "DB Error: " . $e->getMessage();
    header("Location: student_leave.php?error=" . urlencode("Failed to submit leave request."));
    exit;
}