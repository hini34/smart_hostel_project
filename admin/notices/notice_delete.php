<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");
$hostel_name = $_SESSION['hostel_name'] ?? '';

if(empty($hostel_name)){
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}


$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {

    echo "
    <script>
        alert('Invalid Notice ID');
        window.location='notices.php';
    </script>
    ";
    exit;
}

$sql = "
DELETE FROM notices
WHERE id = ?
AND hostel_name = ?
LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {

    echo "
    <script>
        alert('Database Prepare Failed');
        window.location='notices.php';
    </script>
    ";
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "is",
    $id,
    $hostel_name
);

if (mysqli_stmt_execute($stmt)) {

    if (mysqli_stmt_affected_rows($stmt) > 0) {

        echo "
        <script>
            alert('Notice Deleted Successfully');
            window.location='notices.php';
        </script>
        ";

    } else {

        echo "
        <script>
            alert('Notice Not Found');
            window.location='notices.php';
        </script>
        ";
    }

} else {

    echo "
    <script>
        alert('Failed To Delete Notice');
        window.location='notices.php';
    </script>
    ";
}

mysqli_stmt_close($stmt);
exit;
?>