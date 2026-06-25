<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: security_login.php");
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    echo "<script>
            alert('Please fill all fields');
            window.location='security_login.php';
          </script>";
    exit();
}

$sql = "SELECT * FROM staff WHERE username = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Statement preparation failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);

    // 🔒 Status check
    if ($row['status'] !== 'Active') {
        echo "<script>
                alert('Your account is inactive');
                window.location='security_login.php';
              </script>";
        exit();
    }

    // 🔒 Role check
    if (strtolower(trim($row['role'])) !== 'security') {
        echo "<script>
                alert('Access denied! Only security staff allowed');
                window.location='security_login.php';
              </script>";
        exit();
    }

    if (empty($row['hostel_name'])) {

    echo "<script>
            alert('Hostel not assigned to this security account');
            window.location='security_login.php';
          </script>";
    exit();
    }


    // 🔑 Password check
    if (password_verify($password, $row['password'])) {

        // ✅ IMPORTANT (dashboard ke liye)
        $_SESSION['security']      = $row['name'];
        $_SESSION['security_id']   = $row['id'];
        $_SESSION['security_name'] = $row['name'];
        $_SESSION['security_user'] = $row['username'];
        $_SESSION['hostel_name']   = $row['hostel_name'];
        $_SESSION['role']          = $row['role'];
        // $_SESSION['role']          = $row['role'];   // optional but useful

        header("Location: ../../security/dashboard/security_dashboard.php");
        exit();

    } else {
        echo "<script>
                alert('Invalid Password');
                window.location='security_login.php';
              </script>";
        exit();
    }

} else {
    echo "<script>
            alert('Invalid Username');
            window.location='security_login.php';
          </script>";
    exit();
}
?>