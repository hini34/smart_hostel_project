<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if(empty($hostel_name)){
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}

$id = (int)($_GET['id'] ?? 0);

if($id <= 0){

    echo "
    <script>
        alert('Invalid Student ID');
        window.location='pending_students.php';
    </script>
    ";
    exit();
}

/* ==========================
   CHECK STUDENT BELONGS TO
   CURRENT HOSTEL
========================== */

$stmt = mysqli_prepare(
    $conn,
    "SELECT id
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

$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) === 0){

    echo "
    <script>
        alert('Student Not Found Or Access Denied');
        window.location='pending_students.php';
    </script>
    ";
    exit();
}

/* ==========================
   REJECT STUDENT
========================== */

$stmtUpdate = mysqli_prepare(
    $conn,
    "UPDATE students
     SET status = 'rejected'
     WHERE id = ?
     AND hostel_name = ?"
);

mysqli_stmt_bind_param(
    $stmtUpdate,
    "is",
    $id,
    $hostel_name
);

mysqli_stmt_execute($stmtUpdate);

echo "
<script>

alert('Student Rejected Successfully');

window.location='pending_students.php';

</script>
";
?>