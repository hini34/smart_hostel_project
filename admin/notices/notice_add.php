<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if(empty($hostel_name)){
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}


date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['add_notice'])) {

    echo "
    <script>
        alert('Invalid Request');
        window.location='notices.php';
    </script>
    ";
    exit;
}

function clean($value)
{
    return trim((string)$value);
}

$title      = clean($_POST['title'] ?? '');
$body       = clean($_POST['body'] ?? '');
$audience   = clean($_POST['audience'] ?? '');
$start_date = clean($_POST['start_date'] ?? '');
$end_date   = clean($_POST['end_date'] ?? '');
$is_pinned  = isset($_POST['is_pinned']) ? 1 : 0;

$allowed_audience = ['student', 'security', 'admin', 'all'];

if ($title === '' || $body === '' || $audience === '' || $start_date === '') {

    echo "
    <script>
        alert('Please fill in all required fields');
        window.location='notices.php';
    </script>
    ";
    exit;
}

if (!in_array($audience, $allowed_audience, true)) {

    echo "
    <script>
        alert('Invalid audience selected');
        window.location='notices.php';
    </script>
    ";
    exit;
}

if (!strtotime($start_date)) {

    echo "
    <script>
        alert('Invalid start date');
        window.location='notices.php';
    </script>
    ";
    exit;
}

if (!empty($end_date) && $end_date < $start_date) {

    echo "
    <script>
        alert('End date cannot be earlier than start date');
        window.location='notices.php';
    </script>
    ";
    exit;
}

$sql = "INSERT INTO notices
(
    title,
    body,
    audience,
    is_pinned,
    start_date,
    end_date,
    hostel_name,
    created_at
)
VALUES
(
    ?, ?, ?, ?, ?, ?, ?, NOW()
)";

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
    "sssisss",
    $title,
    $body,
    $audience,
    $is_pinned,
    $start_date,
    $end_date,
    $hostel_name
);

if (mysqli_stmt_execute($stmt)) {

    echo "
    <script>
        alert('Notice Added Successfully');
        window.location='notices.php';
    </script>
    ";

} else {

    echo "
    <script>
        alert('Failed To Add Notice');
        window.location='notices.php';
    </script>
    ";
}

mysqli_stmt_close($stmt);
exit;
?>