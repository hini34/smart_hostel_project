<?php
session_start();
require_once(__DIR__ . "/../../../config/db.php");

$user_input = trim($_POST['user_input'] ?? '');

if ($user_input === '') {

    $_SESSION['msg'] = "Please enter username.";
    $_SESSION['msg_type'] = "error";

    header("Location: security_forgot_password.php");
    exit();
}

$sql = "SELECT id
        FROM staff
        WHERE username = ?
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Database Error");
}

mysqli_stmt_bind_param($stmt, "s", $user_input);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {

    $_SESSION['reset_staff_id'] = $user['id'];

    header("Location: security_reset_password.php");
    exit();

} else {

    $_SESSION['msg'] = "Username not found.";
    $_SESSION['msg_type'] = "error";

    header("Location: security_forgot_password.php");
    exit();
}
?>