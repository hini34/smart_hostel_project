<?php
session_start();
require_once(__DIR__ . "/../../../config/db.php");

$username = trim($_POST['user_input']);

$sql = "SELECT id FROM logins WHERE username = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) > 0){

    $row = mysqli_fetch_assoc($result);

    $_SESSION['reset_user_id'] = $row['id'];

    header("Location: reset_password.php");
    exit();

}else{

    $_SESSION['msg'] = "Username not found";
    $_SESSION['msg_type'] = "danger";

    header("Location: forgot_password.php");
    exit();
}
?>