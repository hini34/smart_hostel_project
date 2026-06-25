<?php
session_start();
require_once(__DIR__ . "/../../../config/db.php");

if(!isset($_SESSION['reset_user_id'])){
    header("Location: forgot_password.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<link rel="stylesheet"
href="../../../assets/css/student/reset_password.css">
</head>

<body>

<div class="reset-card">

    <div class="reset-icon">
        <i class="fa-solid fa-key"></i>
    </div>

    <h2 class="reset-title">Reset Password</h2>

    <p class="reset-subtitle">
        Enter your new password and confirm it.
    </p>

    <form action="update_password.php" method="POST">

        <input type="password"
               name="new_password"
               class="form-control"
               placeholder="New Password"
               required>

        <input type="password"
               name="confirm_password"
               class="form-control"
               placeholder="Confirm Password"
               required>

        <button type="submit" class="reset-btn">
            Reset Password
        </button>

    </form>

</div>

</body>
</html>