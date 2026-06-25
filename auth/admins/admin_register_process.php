<?php

require_once(__DIR__ . "/../../config/db.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin_register.php");
    exit();
}

/* =========================
   GET FORM DATA
========================= */

$username         = trim($_POST['username'] ?? '');
$hostel_name      = trim($_POST['hostel_name'] ?? '');
$password         = trim($_POST['password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');

/* =========================
   VALIDATION
========================= */

if (
    $username === '' ||
    $hostel_name === '' ||
    $password === '' ||
    $confirm_password === ''
) {

    echo "<script>
            alert('Please fill all fields');
            window.location='admin_register.php';
          </script>";
    exit();
}

/* =========================
   PASSWORD MATCH CHECK
========================= */

if ($password !== $confirm_password) {

    echo "<script>
            alert('Passwords do not match');
            window.location='admin_register.php';
          </script>";
    exit();
}

/* =========================
   USERNAME CHECK
========================= */

$checkUsername = mysqli_prepare(
    $conn,
    "SELECT id FROM admins WHERE username = ?"
);

mysqli_stmt_bind_param(
    $checkUsername,
    "s",
    $username
);

mysqli_stmt_execute($checkUsername);

$resultUsername =
    mysqli_stmt_get_result($checkUsername);

if (mysqli_num_rows($resultUsername) > 0) {

    echo "<script>
            alert('Username already exists');
            window.location='admin_register.php';
          </script>";
    exit();
}

/* =========================
   HOSTEL CHECK
========================= */

$checkHostel = mysqli_prepare(
    $conn,
    "SELECT id FROM admins WHERE hostel_name = ?"
);

mysqli_stmt_bind_param(
    $checkHostel,
    "s",
    $hostel_name
);

mysqli_stmt_execute($checkHostel);

$resultHostel =
    mysqli_stmt_get_result($checkHostel);

if (mysqli_num_rows($resultHostel) > 0) {

    echo "<script>
            alert('Hostel already assigned to another admin');
            window.location='admin_register.php';
          </script>";
    exit();
}

/* =========================
   HASH PASSWORD
========================= */

$hashed_password =
    password_hash(
        $password,
        PASSWORD_DEFAULT
    );

/* =========================
   INSERT ADMIN
========================= */

$sql = "INSERT INTO admins
        (
            username,
            hostel_name,
            password
        )
        VALUES
        (
            ?, ?, ?
        )";

$stmt = mysqli_prepare(
    $conn,
    $sql
);

if (!$stmt) {

    die(
        "Prepare Failed : " .
        mysqli_error($conn)
    );
}

mysqli_stmt_bind_param(
    $stmt,
    "sss",
    $username,
    $hostel_name,
    $hashed_password
);

/* =========================
   EXECUTE
========================= */

if (mysqli_stmt_execute($stmt)) {

    echo "<script>
            alert('Admin Registered Successfully');
            window.location='admin_login.php';
          </script>";

} else {

    echo "<script>
            alert('Registration Failed');
            window.location='admin_register.php';
          </script>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>