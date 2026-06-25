<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");


$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

/* Validation */

if($username == '' || $password == ''){

    echo "<script>
            alert('Please fill all fields');
            window.location='student_login.php';
          </script>";
    exit();
}

$sql = "SELECT * FROM logins WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Database Error");
}

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 1) {

    $row = mysqli_fetch_assoc($result);

    if (password_verify($password, $row['password'])) {

       $stuSql = "SELECT
            id,
            fullname,
            room_id,
            hostel_name,
            status
        FROM students
        WHERE enrollment = ?
        LIMIT 1";
        $stuStmt = mysqli_prepare($conn, $stuSql);

        if (!$stuStmt) {
            die("Database Error");
        }

        mysqli_stmt_bind_param($stuStmt, "s", $username);
        mysqli_stmt_execute($stuStmt);
        $stuRes = mysqli_stmt_get_result($stuStmt);

        if ($stuRow = mysqli_fetch_assoc($stuRes)) {

                if ($stuRow['status'] === 'pending') {

                    echo "<script>
                            alert('Your account is waiting for admin approval.');
                            window.location='student_login.php';
                        </script>";
                    exit();
                }

                if ($stuRow['status'] === 'rejected') {

                    echo "<script>
                            alert('Your registration request was rejected.');
                            window.location='student_login.php';
                        </script>";
                    exit();
                }

                $_SESSION['student_id']   = $stuRow['id'];
                $_SESSION['student_name'] = $stuRow['fullname'];
                $_SESSION['hostel_name']  = $stuRow['hostel_name'];
            }
            else {

                echo "<script>
                        alert('Student record not found');
                        window.location='student_login.php';
                    </script>";
                exit();
            }
    
        $_SESSION['student'] = $username;

        header("Location: ../../student/dashboard/student_dashboard.php");
        exit();

    } else {
        echo "<script>
                alert('Invalid Password');
                window.location='student_login.php';
              </script>";
    }

} else {
    echo "<script>
            alert('Invalid Username');
            window.location='student_login.php';
          </script>";
}