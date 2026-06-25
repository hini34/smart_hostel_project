<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}


date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: visitors.php");
    exit;
}

$visitor_id = (int)($_POST['visitor_id'] ?? 0);
$action     = trim($_POST['action'] ?? '');

if ($visitor_id <= 0 || $action === '') {
    echo "
    <script>
        alert('Invalid Request');
        window.location='visitors.php';
    </script>
    ";
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, status, checkout_time
     FROM visitors
     WHERE id = ?
     AND hostel_name = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "is",
    $visitor_id,
    $hostel_name
);
mysqli_stmt_bind_param($stmt, "is", $visitor_id, $hostel_name);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$visitor = mysqli_fetch_assoc($result);

if (!$visitor) {
     echo "
    <script>
        alert('Visitor record not found');
        window.location='visitors.php';
    </script>
    ";
    exit;
}

$current_status = strtolower(trim($visitor['status'] ?? ''));

if ($action === 'approve') {
    $new_status = 'Approved';

    $update = mysqli_prepare($conn, "UPDATE visitors
        SET status = ?
        WHERE id = ?
        AND hostel_name = ?");
    mysqli_stmt_bind_param(
        $update,
        "sis",
        $new_status,
        $visitor_id,
        $hostel_name
    );

    if (mysqli_stmt_execute($update)) {
       echo "
            <script>
                alert('Visitor approved successfully');
                window.location='visitors.php';
            </script>
            ";
            exit;
    } else {
       echo "
    <script>
        alert('Failed to approve visitor');
        window.location='visitors.php';
    </script>
    ";
    }
    exit;

} elseif ($action === 'reject') {
    $new_status = 'Rejected';

    $update = mysqli_prepare($conn, "UPDATE visitors
        SET status = ?
        WHERE id = ?
        AND hostel_name = ?");
    mysqli_stmt_bind_param(
        $update,
        "sis",
        $new_status,
        $visitor_id,
        $hostel_name
    );

    if (mysqli_stmt_execute($update)) {

        echo "
            <script>
                alert('Visitor rejected successfully');
                window.location='visitors.php';
            </script>
            ";

        } else {

            echo "
            <script>
                alert('Failed to reject visitor');
                window.location='visitors.php';
            </script>
            ";
        }
        exit;

} elseif ($action === 'checkout') {

    if ($current_status !== 'approved') {

    echo "
    <script>
        alert('Only approved visitors can be checked out');
        window.location='visitors.php';
    </script>
    ";
    exit;
}

if (!empty($visitor['checkout_time'])) {

    echo "
    <script>
        alert('Visitor already checked out');
        window.location='visitors.php';
    </script>
    ";
    exit;
}

    $checkout_time = date("Y-m-d H:i:s");

    $update = mysqli_prepare($conn, "UPDATE visitors
    SET checkout_time = ?
    WHERE id = ?
    AND hostel_name = ?");
    mysqli_stmt_bind_param(
            $update,
            "sis",
            $checkout_time,
            $visitor_id,
            $hostel_name
        );
        if (mysqli_stmt_execute($update)) {

            echo "
            <script>
                alert('Visitor checked out successfully');
                window.location='visitors.php';
            </script>
            ";

        } else {

            echo "
            <script>
                alert('Failed to check out visitor');
                window.location='visitors.php';
            </script>
            ";
        }
        exit;

} else {
    echo "
    <script>
        alert('Unknown Action');
        window.location='visitors.php';
    </script>
    ";
    exit;
}
