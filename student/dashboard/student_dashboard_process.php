<?php
session_start();

header('Content-Type: application/json');

//database connection
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

// Default response
$response = [
    "room_no"            => "Not assigned",
    "block_name"         => "",
    "floor_no"           => "",
    "pending_fees"       => 0,
    "last_payment_text"  => "Last payment: --",
    "complaints_total"   => 0,
    "complaints_pending" => 0,
    "leave_status"       => "",
    "leave_dates"        => "--",
    "notice_title"       => "No recent notice",
    "notice_date"        => "--",
    "notice_text"        => "summary of the notices will be shown here."
];

// Student login check
if (!isset($_SESSION['student_id'])) {
    echo json_encode($response);
    exit;
}

$student_id = (int)$_SESSION['student_id'];

$sql = "SELECT r.room_no, r.block_name, r.floor_no
        FROM students s
        LEFT JOIN rooms r ON s.room_id = r.id
        WHERE s.id = ?
        AND s.hostel_name = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {

    mysqli_stmt_bind_param(
        $stmt,
        "is",
        $student_id,
        $hostel_name
    );

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $response['room_no']    = $row['room_no'] ?? "Not assigned";
        $response['block_name'] = $row['block_name'] ?? "";
        $response['floor_no']   = $row['floor_no'] ?? "";
    }

    mysqli_stmt_close($stmt);
}


// COMPLAINTS (complaints)
  
$sql = "SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending
        FROM complaints
        WHERE student_id = ?
        AND hostel_name = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {

    mysqli_stmt_bind_param(
        $stmt,
        "is",
        $student_id,
        $hostel_name
    );

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $response['complaints_total']   = (int)$row['total'];
        $response['complaints_pending'] = (int)$row['pending'];
    }

    mysqli_stmt_close($stmt);
}

// LEAVE STATUS (student_leave) 

$sql = "SELECT from_date, to_date, status
        FROM student_leaves
        WHERE student_id = ?
        AND hostel_name = ?
        ORDER BY id DESC
        LIMIT 1";

if ($stmt = mysqli_prepare($conn, $sql)) {

    mysqli_stmt_bind_param(
        $stmt,
        "is",
        $student_id,
        $hostel_name
    );

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $status = $row['status'] ?? '';

        if ($status === 'Approved') {
            $response['leave_status'] = "Approved";
        } elseif ($status === 'Pending') {
            $response['leave_status'] = "Pending";
        } elseif ($status=== 'Rejected') {
            $response['leave_status'] = "Rejected";
        }

        if (!empty($row['from_date']) && !empty($row['to_date'])) {
            $from = date("d M", strtotime($row['from_date']));
            $to   = date("d M Y", strtotime($row['to_date']));
            $response['leave_dates'] = "$from - $to";
        }
    }
    mysqli_stmt_close($stmt);
}

// NOTICES (notices)

$today = date("Y-m-d");

$sql = "SELECT title, body, created_at, start_date, end_date, is_pinned
        FROM notices
        WHERE hostel_name = ?
        AND (audience = 'student' OR audience = 'all' OR audience = 'students')
        AND start_date <= ?
        AND (end_date IS NULL OR end_date >= ?)
        ORDER BY is_pinned DESC, created_at DESC
        LIMIT 1";

if ($stmt = mysqli_prepare($conn, $sql)) {

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $hostel_name,
        $today,
        $today
    );

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $response['notice_title'] = $row['title'] ?? "No recent notice";

        if (!empty($row['created_at'])) {
            $response['notice_date'] = date("d M Y", strtotime($row['created_at']));
        }

        $body = trim($row['body'] ?? '');

        if (!empty($body)) {
            // Remove extra spaces/new lines
            $body = preg_replace('/\s+/', ' ', $body);

            // Short summary max 100 chars
            if (strlen($body) > 100) {
                $body = substr($body, 0, 100) . '...';
            }

            $response['notice_text'] = $body;
        }
    }

    mysqli_stmt_close($stmt);
}

echo json_encode($response);
exit;
?>