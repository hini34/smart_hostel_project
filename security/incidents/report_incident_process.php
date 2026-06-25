<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    header("Location: ../../auth/security/security_login.php");
    exit();
}

if (!isset($conn) || !$conn) {
    error_log("Database connection failed");
    die("Something went wrong.");
}

if (!isset($_SESSION['security_id'])) {
    header("Location: ../../auth/security/security_login.php");
    exit();
}

$reported_by = (int) $_SESSION['security_id'];

$title = trim($_POST['title'] ?? '');
$desc  = trim($_POST['description'] ?? '');
$type  = trim($_POST['incident_type'] ?? '');
$loc   = trim($_POST['location'] ?? '');
$sev   = trim($_POST['severity'] ?? '');

// Validation
if ($title === '' || $desc === '' || $type === '' || $loc === '' || $sev === '') {
    header("Location: report_incident.php?error=" . urlencode("All fields are required."));
    exit();
}

$allowed_types = ['Fight', 'Noise', 'Damage', 'Theft', 'Suspicious'];
$allowed_severity = ['Low', 'Medium', 'High'];

if (!in_array($type, $allowed_types, true)) {
    header("Location: report_incident.php?error=" . urlencode("Invalid incident type selected."));
    exit();
}

if (!in_array($sev, $allowed_severity, true)) {
    header("Location: report_incident.php?error=" . urlencode("Invalid severity selected."));
    exit();
}

// Check if guard exists in staff table
$checkSql = "SELECT id
             FROM staff
             WHERE id = ?
             AND hostel_name = ?
             AND status = 'Active'
             LIMIT 1";
$checkStmt = mysqli_prepare($conn, $checkSql);

if (!$checkStmt) {
    error_log("Staff check prepare failed: " . mysqli_error($conn));
    die("Something went wrong.");
}

mysqli_stmt_bind_param(
    $checkStmt,
    "is",
    $reported_by,
    $hostel_name
);

if (!mysqli_stmt_execute($checkStmt)) {

    error_log("Staff check execute failed: " . mysqli_stmt_error($checkStmt));

    mysqli_stmt_close($checkStmt);

    die("Something went wrong.");
}

$checkResult = mysqli_stmt_get_result($checkStmt);

if (!$checkResult || mysqli_num_rows($checkResult) === 0) {
    session_destroy();
    header("Location: ../../auth/security/security_login.php");
    exit();
}

mysqli_stmt_close($checkStmt);

// Insert incident
$sql = "INSERT INTO incidents
        (
            title,
            description,
            incident_type,
            location,
            severity,
            reported_by,
            hostel_name
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    error_log("Incident insert prepare failed: " . mysqli_error($conn));
    die("Something went wrong.");
}

mysqli_stmt_bind_param(
    $stmt,
    "sssssis",
    $title,
    $desc,
    $type,
    $loc,
    $sev,
    $reported_by,
    $hostel_name
);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: incident_list.php?success=1");
    exit();
} else {
    error_log("Incident insert failed: " . mysqli_stmt_error($stmt));
     mysqli_stmt_close($stmt);

    header("Location: report_incident.php?error=" . urlencode("Unable to save incident."));
    exit();
}
?>