<?php
session_start();
require_once(__DIR__ . "/../../../config/db.php");

$msg = $_SESSION['msg'] ?? '';
$msg_type = $_SESSION['msg_type'] ?? '';

unset($_SESSION['msg'], $_SESSION['msg_type']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/student/student_forget.css">
</head>
<body>

<div class="forgot-wrapper">
    <div class="forgot-card">

        <div class="forgot-icon">
            <i class="fa-solid fa-lock"></i>
        </div>

        <h2 class="forgot-title">Forgot Password</h2>

        <?php if (!empty($msg)): ?>
            <div class="<?php echo ($msg_type == 'success') ? 'success-message' : 'error-message'; ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <p class="forgot-subtitle">
            Write Your Registered Username to Reset Password
        </p>

        <form action="security_forgot_password_process.php" method="POST">

            <input
                type="text"
                name="user_input"
                class="form-control"
                placeholder="Enter Username"
                required
            >

            <button type="submit" class="forgot-btn">
                Continue
            </button>

        </form>

        <div class="back-login">
            <a href="../../securitys/security_login.php">
                Back to Login
            </a>
        </div>

    </div>
</div>

</body>
</html>