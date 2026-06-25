<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    echo json_encode([
        "success" => false,
        "message" => "Hostel not assigned"
    ]);
    exit;
}

date_default_timezone_set('Asia/Kolkata');

$response = [
    "success" => true,
    "total_students" => 0,
    "present_students" => 0,
    "absent_students" => 0,
    "visitors_today" => 0,
    "pending_approvals" => 0,
    "active_leaves" => 0,
    "last_updated" => date("d M Y h:i:s A"),
    "message" => "Data loaded successfully"
];

if (!isset($conn) || !$conn) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

if (!isset($_SESSION['security'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access"
    ]);
    exit;
}

// Optional role validation
// if (isset($_SESSION['role']) && strtolower($_SESSION['role']) !== 'security') {
//     echo json_encode([
//         "success" => false,
//         "message" => "Access denied"
//     ]);
//     exit;
// }

/* TOTAL STUDENTS */
$sql = "SELECT COUNT(*) AS total_students
        FROM students
        WHERE hostel_name = ?";
        
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $hostel_name);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $response['total_students'] = (int)$row['total_students'];
}

/* PRESENT STUDENTS */
$sql = "SELECT COUNT(DISTINCT student_id) AS present_students
        FROM student_attendance
        WHERE attendance_date = CURDATE()
        AND LOWER(status) = 'present'
        AND hostel_name = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $hostel_name);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $response['present_students'] = (int)$row['present_students'];
}

mysqli_stmt_close($stmt);

/* ABSENT STUDENTS */
$response['absent_students'] = max(
    0,
    $response['total_students'] - $response['present_students']
);

/* VISITORS TODAY */
$sql = "SELECT COUNT(*) AS visitors_today
        FROM visitors
        WHERE DATE(checkin_time) = CURDATE()
        AND hostel_name = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $hostel_name);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $response['visitors_today'] = (int)$row['visitors_today'];
}

mysqli_stmt_close($stmt);

/* PENDING APPROVALS */
$sql = "SELECT COUNT(*) AS pending_approvals
        FROM visitors
        WHERE LOWER(status) = 'pending'
        AND hostel_name = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $hostel_name);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $response['pending_approvals'] = (int)$row['pending_approvals'];
}

mysqli_stmt_close($stmt);

/* ACTIVE LEAVES */
$sql = "SELECT COUNT(*) AS active_leaves
        FROM student_leaves
        WHERE LOWER(status) = 'approved'
        AND CURDATE() BETWEEN from_date AND to_date
        AND hostel_name = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $hostel_name);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $response['active_leaves'] = (int)$row['active_leaves'];
}

mysqli_stmt_close($stmt);

echo json_encode($response);
exit;
?>