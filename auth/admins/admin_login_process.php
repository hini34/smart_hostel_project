<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin_login.php");
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    echo "<script>
            alert('Please fill all fields');
            window.location='admin_login.php';
          </script>";
    exit();
}

$sql = "SELECT * FROM admins WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Statement preparation failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);

    if (password_verify($password, $row['password'])) {
        $_SESSION['admin'] = $row['username'];
        $_SESSION['admin_username'] = $row['username'];
        $_SESSION['hostel_name'] = $row['hostel_name'];
        $_SESSION['admin_id'] = $row['id'] ?? null;

        header("Location: ../../admin/dashboard/admin_dashboard.php");
        exit();
    } else {
        echo "<script>
                alert('Invalid Password');
                window.location='admin_login.php';
              </script>";
        exit();
    }
} else {
    echo "<script>
            alert('Invalid Username');
            window.location='admin_login.php';
          </script>";
    exit();
}
?>