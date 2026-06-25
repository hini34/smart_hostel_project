<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    header("Location: ../../auth/securitys/security_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: visitor_history.php");
    exit();
}

$visitor_id = (int)($_POST['visitor_id'] ?? 0);

if ($visitor_id <= 0) {
    header("Location: visitor_history.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Verify Visitor belongs to same hostel
|--------------------------------------------------------------------------
*/

$checkSql = "
    SELECT v.id
    FROM visitors v
    INNER JOIN students s
        ON v.student_id = s.id
    WHERE v.id = ?
    AND s.hostel_name = ?
";

$checkStmt = mysqli_prepare($conn, $checkSql);

if (!$checkStmt) {
    error_log("Checkout Verify Error: " . mysqli_error($conn));
    header("Location: visitor_history.php");
    exit();
}

mysqli_stmt_bind_param(
    $checkStmt,
    "is",
    $visitor_id,
    $hostel_name
);

mysqli_stmt_execute($checkStmt);

$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) === 0) {

    error_log("Unauthorized checkout attempt");

    header("Location: visitor_history.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Update Checkout Time
|--------------------------------------------------------------------------
*/

$updateSql = "
    UPDATE visitors
    SET checkout_time = NOW()
    WHERE id = ?
    AND status = 'approved'
    AND checkout_time IS NULL
";

$updateStmt = mysqli_prepare($conn, $updateSql);

if (!$updateStmt) {

    error_log("Checkout Update Error: " . mysqli_error($conn));

    header("Location: visitor_history.php");
    exit();
}

mysqli_stmt_bind_param(
    $updateStmt,
    "i",
    $visitor_id
);

mysqli_stmt_execute($updateStmt);

mysqli_stmt_close($checkStmt);
mysqli_stmt_close($updateStmt);

header("Location: visitor_history.php");
exit();
?>