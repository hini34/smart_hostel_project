<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");
$hostel_name = $_SESSION['hostel_name'] ?? '';

if(empty($hostel_name)){
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}


require_once("../../PHPMailer/src/PHPMailer.php");
require_once("../../PHPMailer/src/SMTP.php");
require_once("../../PHPMailer/src/Exception.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// =======================================
// GET STUDENT ID
// =======================================

$id = $_GET['id'] ?? '';

if($id == ''){

    echo "
    <script>
        alert('Invalid Student ID');
        window.location='pending_students.php';
    </script>
    ";
    exit;
}

// =======================================
// GET STUDENT DATA
// =======================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM students
     WHERE id = ?
     AND hostel_name = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "is",
    $id,
    $hostel_name
);

mysqli_stmt_execute($stmt);

$getStudent = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($getStudent) == 0){

    echo "
    <script>
        alert('Student not found');
        window.location='pending_students.php';
    </script>
    ";
    exit;
}

$student = mysqli_fetch_assoc($getStudent);

// =======================================
// GENERATE LOGIN
// =======================================

$username = $student['enrollment'];

$plain_password = substr(

    str_shuffle(
        "123456789"
    ),

    0,

    8
);

$hash_password = password_hash(
    $plain_password,
    PASSWORD_DEFAULT
);

// =======================================
// UPDATE STUDENT STATUS
// =======================================

// CHECK
$stmtCheck = mysqli_prepare(
    $conn,
    "SELECT id
     FROM logins
     WHERE username = ?"
);

mysqli_stmt_bind_param(
    $stmtCheck,
    "s",
    $username
);

mysqli_stmt_execute($stmtCheck);

$check = mysqli_stmt_get_result($stmtCheck);

if (mysqli_num_rows($check) > 0) {
     echo "
    <script>
        alert('Student is already approved!');
        history.back();
    </script>
    ";

    exit;
}

// INSERT LOGIN
$stmtLogin = mysqli_prepare(
    $conn,
    "INSERT INTO logins
    (username, password)
    VALUES (?, ?)"
);

mysqli_stmt_bind_param(
    $stmtLogin,
    "ss",
    $username,
    $hash_password
);

mysqli_stmt_execute($stmtLogin);

// UPDATE STUDENT
$stmtUpdate = mysqli_prepare(
    $conn,
    "UPDATE students
     SET username = ?,
         password = ?,
         status = 'approved'
     WHERE id = ?
     AND hostel_name = ?"
);

mysqli_stmt_bind_param(
    $stmtUpdate,
    "ssis",
    $username,
    $hash_password,
    $id,
    $hostel_name
);

mysqli_stmt_execute($stmtUpdate);

// =======================================
// SEND EMAIL
// =======================================

$mail = new PHPMailer(true);

try{

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;

    $mail->Username =
    'hostelmangements@gmail.com';

    $mail->Password =
    'jovs dvrs xhia ltai';

    $mail->SMTPSecure =
    PHPMailer::ENCRYPTION_STARTTLS;

    $mail->Port = 587;

    $mail->setFrom(
        'hostelmangements@gmail.com',
        'Hostel Management'
    );

    $mail->addAddress(
        $student['email']
    );

    $mail->isHTML(true);

    $mail->Subject =
    "Hostel Account Approved";

    $mail->Body = "

    <div style='font-family:Arial'>

        <h2>
            Your Hostel Account Has Been Approved
        </h2>

        <p>

            <b>Username :</b>
            {$username}

        </p>

        <p>

            <b>Password :</b>
            {$plain_password}

        </p>

        <br>

        <p>

            You can now login into
            Hostel Management System.

        </p>

    </div>

    ";

    $mail->send();

    echo "

    <script>

        alert(
        'Student Approved Successfully'
        );

        window.location='pending_students.php';

    </script>

    ";

}catch(Exception $e){

    echo "

    <script>

        alert(
        'Student Approved But Email Failed'
        );

        window.location='pending_students.php';

    </script>

    ";
}
?>