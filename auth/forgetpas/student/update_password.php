<?php
session_start();
require_once(__DIR__ . "/../../../config/db.php");

if(!isset($_SESSION['reset_user_id'])){
    header("Location: forgot_password.php");
    exit();
}

$new_password     = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

if($new_password !== $confirm_password){

    echo "Passwords do not match";
    exit();
}

$hashed_password = password_hash(
    $new_password,
    PASSWORD_DEFAULT
);

$user_id = $_SESSION['reset_user_id'];

$sql = "UPDATE logins
        SET password = ?
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param(
    $stmt,
    "si",
    $hashed_password,
    $user_id
);

if(mysqli_stmt_execute($stmt)){

    unset($_SESSION['reset_user_id']);

    header("Location: ../../students/student_login.php?reset=success");
    exit();

}else{

    echo "Password update failed";
}
?>