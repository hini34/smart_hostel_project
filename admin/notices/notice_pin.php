<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if(empty($hostel_name)){
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}


$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = trim($_GET['action'] ?? '');

if ($id <= 0 || !in_array($action, ['pin', 'unpin'])) {

    echo "
    <script>
        alert('Invalid Request');
        window.location='notices.php';
    </script>
    ";
    exit;
}

$is_pinned = ($action === 'pin') ? 1 : 0;

$sql = "
UPDATE notices
SET is_pinned = ?
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
    "iis",
    $is_pinned,
    $id,
    $hostel_name
);

if (mysqli_stmt_execute($stmt)) {

    if (mysqli_stmt_affected_rows($stmt) > 0) {

        if ($is_pinned == 1) {

            echo "
            <script>
                alert('Notice Pinned Successfully');
                window.location='notices.php';
            </script>
            ";

        } else {

            echo "
            <script>
                alert('Notice Unpinned Successfully');
                window.location='notices.php';
            </script>
            ";
        }

    } else {

        echo "
        <script>
            alert('Notice Not Found Or Already Updated');
            window.location='notices.php';
        </script>
        ";
    }

} else {

    echo "
    <script>
        alert('Failed To Update Notice');
        window.location='notices.php';
    </script>
    ";
}

mysqli_stmt_close($stmt);
exit;
?>