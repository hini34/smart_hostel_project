<?php
session_start();
$hostel_name = $_SESSION['hostel_name'] ?? '';
require_once(__DIR__ . "/../../config/db.php");
header('Content-Type: application/json');

$response = [
    "success" => true,
    "total_students" => 0,
    "total_rooms" => 0,
    "available_rooms" => 0,
    "leave_students" => 0,
    "attendance_today" => 0,
    "pending_complaints" => 0,
    "completed_complaints" => 0,
    "attendance_chart" => [],
    "today_regs" => 0,
    "today_attendance" => 0,
    "today_complaints" => 0,
    "today_resolved" => 0,
    "today_leaves" => 0,
    "debug" => []
];

function runCount($conn, $sql, $field = 'total') {
    $q = mysqli_query($conn, $sql);
    if (!$q) {
        throw new Exception(mysqli_error($conn) . " | SQL: " . $sql);
    }
    $row = mysqli_fetch_assoc($q);
    return (int)($row[$field] ?? 0);
}

try {
    // Total students
    $response["total_students"] = runCount($conn, "
        SELECT COUNT(*) AS total 
        FROM students 
        WHERE hostel_name = '$hostel_name'
    ");

    // Total rooms
    $response["total_rooms"] = runCount($conn, "
        SELECT COUNT(*) AS total 
        FROM rooms
        WHERE hostel_name = '$hostel_name'
    ");

    // Available beds
    $response["available_rooms"] = runCount($conn, "
    SELECT COALESCE(SUM(capacity - occupied), 0) AS available
    FROM rooms
    WHERE hostel_name = '$hostel_name'
    ", "available");

    // Students on approved leave today
    $response["today_leaves"] = runCount($conn, "
        SELECT COUNT(*) AS total
        FROM student_leaves sl
        INNER JOIN students s
            ON sl.student_id = s.id
        WHERE s.hostel_name = '$hostel_name'
        AND DATE(sl.applied_on) = CURDATE()
    ");
    // Pending complaints
    $response["pending_complaints"] = runCount($conn, "
        SELECT COUNT(*) AS total
        FROM complaints
        WHERE hostel_name = '$hostel_name'
        AND status = 'Pending'
    ");

    // Completed/Resolved complaints
    $response["completed_complaints"] = runCount($conn, "
        SELECT COUNT(*) AS total
        FROM complaints
       WHERE hostel_name = '$hostel_name'
        AND status = 'Resolved'
    ");

    // Attendance today
    $response["attendance_today"] = runCount($conn, "
        SELECT COUNT(*) AS total
        FROM student_attendance sa
        INNER JOIN students s
            ON sa.student_id = s.id
        WHERE s.hostel_name = '$hostel_name'
        AND sa.attendance_date = CURDATE()
        AND sa.status = 'Present'
    ");

    // Today's registrations
    $response["today_regs"] = runCount($conn, "
        SELECT COUNT(*) AS total
        FROM students
        WHERE hostel_name = '$hostel_name'
        AND DATE(created_at) = CURDATE()
    ");

    // Today's attendance marked
    $response["today_attendance"] = runCount($conn, "
        SELECT COUNT(*) AS total
        FROM student_attendance sa
        INNER JOIN students s
        ON sa.student_id = s.id
        WHERE s.hostel_name = '$hostel_name'
        AND sa.attendance_date = CURDATE()
        AND sa.status = 'Present'
    ");

    // Today's complaints received
    $response["today_complaints"] = runCount($conn, "
        SELECT COUNT(*) AS total
        FROM complaints
        WHERE hostel_name = '$hostel_name'
        AND DATE(created_at) = CURDATE()
    ");

    // Today's resolved complaints
    $response["today_resolved"] = runCount($conn, "
        SELECT COUNT(*) AS total
        FROM complaints
        WHERE hostel_name = '$hostel_name'
        AND status = 'Resolved'
        AND DATE(created_at) = CURDATE()
    ");

    // Today's leave requests
        $response["leave_students"] = runCount($conn, "
            SELECT COUNT(*) AS total
            FROM student_leaves sl
            INNER JOIN students s
                ON sl.student_id = s.id
            WHERE s.hostel_name = '$hostel_name'
            AND sl.status = 'Approved'
            AND CURDATE() BETWEEN sl.from_date AND sl.to_date
        ");


    // Attendance chart (last 7 days)
    $chart_rows = [];

    $hostel_name = $_SESSION['hostel_name'];

    $q = mysqli_query($conn, "
        SELECT
            sa.attendance_date,
            COUNT(*) AS present_count
        FROM student_attendance sa
        INNER JOIN students s
            ON sa.student_id = s.id
        WHERE sa.status = 'Present'
        AND s.hostel_name = '$hostel_name'
        AND sa.attendance_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY sa.attendance_date
        ORDER BY sa.attendance_date
    ");

    if (!$q) {
        throw new Exception(mysqli_error($conn) . " | SQL: attendance chart");
    }

    while ($r = mysqli_fetch_assoc($q)) {
        $chart_rows[] = $r;
    }

    $response["attendance_chart"] = $chart_rows;

} catch (Exception $e) {
    $response["success"] = false;
    $response["error"] = $e->getMessage();
}

echo json_encode($response);
exit;
?>