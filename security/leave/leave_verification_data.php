<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    echo json_encode([
        'success' => false,
        'message' => 'Hostel not assigned.'
    ]);
    exit();
}

header('Content-Type: application/json');
date_default_timezone_set('Asia/Kolkata');

// optional session check
// if (!isset($_SESSION['security'])) {
//     echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
//     exit();
// }

$enrollment = trim($_GET['enrollment'] ?? '');


if ($enrollment === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Enrollment number is required.'
    ]);
    exit();
}
 
$sql = "SELECT 
            sl.id,
            sl.leave_type,
            sl.reason,
            sl.from_date,
            sl.to_date,
            sl.going_address,
            sl.contact_number,
            sl.emergency_contact,
            sl.status,
            sl.admin_remark,
            sl.approved_at,
            sl.applied_on,
            sl.exit_marked,
            sl.exit_time,
            sl.entry_marked,
            sl.entry_time,
            s.id AS student_id,
            s.fullname,
            s.enrollment,
            s.course,
            s.department,
            s.semester,
            r.room_no,
            r.block_name,
            r.floor_no,
            a.username AS approved_by_name
        FROM student_leaves sl
        INNER JOIN students s ON sl.student_id = s.id
        LEFT JOIN rooms r ON s.room_id = r.id
        LEFT JOIN admins a ON sl.approved_by = a.id
        WHERE s.enrollment = ?
        AND s.hostel_name = ?
        ORDER BY sl.id DESC
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Something went wrong. Please try again later.'
    ]);
    exit();
}

mysqli_stmt_bind_param(
    $stmt,
    "ss",
    $enrollment,
    $hostel_name
);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'No leave record found for this enrollment.'
    ]);
    exit();
}

$row = mysqli_fetch_assoc($result);

$today = date('Y-m-d');
$isValidDate = ($today >= $row['from_date'] && $today <= $row['to_date']);

$gate_pass_status = 'Not Allowed';
if ($row['status'] === 'Approved' && $isValidDate) {
    $gate_pass_status = 'Allowed';
} elseif ($row['status'] === 'Approved' && !$isValidDate) {
    $gate_pass_status = 'Expired';
} elseif ($row['status'] === 'Pending') {
    $gate_pass_status = 'Pending Approval';
} elseif ($row['status'] === 'Rejected') {
    $gate_pass_status = 'Rejected';
}

echo json_encode([
    'success' => true,
    'student' => [
        'name'       => $row['fullname'],
        'enrollment' => $row['enrollment'],
        'course'     => $row['course'],
        'department' => $row['department'],
        'semester'   => $row['semester'],
        'room_no'    => $row['room_no'],
        'block_name' => $row['block_name'],
        'floor_no'   => $row['floor_no']
    ],
    'leave' => [
        'leave_type'       => $row['leave_type'],
        'reason'           => $row['reason'],
        'from_date'        => $row['from_date'],
        'to_date'          => $row['to_date'],
        'going_address'    => $row['going_address'],
        'contact_number'   => $row['contact_number'],
        'emergency_contact'=> $row['emergency_contact'],
        'status'           => $row['status'],
        'admin_remark'     => $row['admin_remark'],
        'approved_at'      => $row['approved_at'],
        'applied_on'       => $row['applied_on'],
        'approved_by'      => $row['approved_by_name'],
        'exit_marked'      => $row['exit_marked'],
        'exit_time'        => $row['exit_time'],
        'entry_marked'     => $row['entry_marked'],
        'entry_time'       => $row['entry_time'],
        'gate_pass_status' => $gate_pass_status,
        'is_valid_today'   => $isValidDate
    ]
]);
exit();