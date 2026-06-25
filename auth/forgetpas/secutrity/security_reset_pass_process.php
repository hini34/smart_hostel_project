<?php
session_start();
require_once(__DIR__ . "/../../../config/db.php");

if (!isset($_SESSION['reset_staff_id'])) {
    header("Location: security_forgot_password.php");
    exit();
}

$new_password     = trim($_POST['new_password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');

if ($new_password === '' || $confirm_password === '') {

    $_SESSION['msg'] = "All fields are required.";
    $_SESSION['msg_type'] = "error";

    header("Location: security_reset_password.php");
    exit();
}

if ($new_password !== $confirm_password) {

    $_SESSION['msg'] = "Passwords do not match.";
    $_SESSION['msg_type'] = "error";

    header("Location: security_reset_password.php");
    exit();
}

$hashed_password = password_hash(
    $new_password,
    PASSWORD_DEFAULT
);

$staff_id = (int)$_SESSION['reset_staff_id'];

$sql = "UPDATE staff
        SET password = ?
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Database Error");
}

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $hashed_password,
    $staff_id
);

if (mysqli_stmt_execute($stmt)) {

    unset($_SESSION['reset_staff_id']);

    $_SESSION['msg'] = "Password updated successfully.";
    $_SESSION['msg_type'] = "success";

    header("Location: ../../securitys/security_login.php");
    exit();

} else {

    $_SESSION['msg'] = "Failed to update password.";
    $_SESSION['msg_type'] = "error";

    header("Location: security_reset_password.php");
    exit();
}
?>