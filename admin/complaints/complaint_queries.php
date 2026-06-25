<?php
require_once(__DIR__ . "/../../config/db.php");
require_once(__DIR__ . "/complaint_helpers.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

$stats = [
    'total'    => 0,
    'open'     => 0,
    'progress' => 0,
    'resolved' => 0,
    'rejected' => 0,
];

$statsQuery = "
    SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status='Open' THEN 1 ELSE 0 END) AS open_count,
        SUM(CASE WHEN status='In-Progress' THEN 1 ELSE 0 END) AS progress_count,
        SUM(CASE WHEN status='Resolved' THEN 1 ELSE 0 END) AS resolved_count,
        SUM(CASE WHEN status='Rejected' THEN 1 ELSE 0 END) AS rejected_count
    FROM complaints
    WHERE hostel_name = '$hostel_name'
";

$statsRes = mysqli_query($conn, $statsQuery);

if ($statsRes && $row = mysqli_fetch_assoc($statsRes)) {

    $stats['total']    = (int)$row['total'];
    $stats['open']     = (int)$row['open_count'];
    $stats['progress'] = (int)$row['progress_count'];
    $stats['resolved'] = (int)$row['resolved_count'];
    $stats['rejected'] = (int)$row['rejected_count'];
}

$status_filter = $_GET['status'] ?? '';

$where = "c.hostel_name = '$hostel_name'";

if (
    $status_filter &&
    in_array($status_filter, allowedComplaintStatuses(), true)
) {
    $status_esc = mysqli_real_escape_string(
        $conn,
        $status_filter
    );

    $where .= " AND c.status = '$status_esc'";
}

$complaints = [];

$complaintsQuery = "
SELECT
    c.*,
    s.fullname,
    s.enrollment,
    r.room_no
FROM complaints c
INNER JOIN students s
    ON s.id = c.student_id
LEFT JOIN rooms r
    ON r.id = s.room_id
WHERE $where
ORDER BY c.created_at DESC
LIMIT 100
";

$complaintsRes = mysqli_query(
    $conn,
    $complaintsQuery
);

if ($complaintsRes) {

    while ($row = mysqli_fetch_assoc($complaintsRes)) {

        $complaints[] = $row;
    }
}

$staff_list = [];

$staffRes = mysqli_query(
    $conn,
    "
    SELECT id,name
    FROM staff
    WHERE hostel_name='$hostel_name'
    ORDER BY name ASC
    "
);

if ($staffRes) {

    while ($row = mysqli_fetch_assoc($staffRes)) {

        $staff_list[] = $row;
    }
}

$selected_complaint = null;

if (!empty($_GET['id'])) {

    $selectedId = (int)$_GET['id'];

    foreach ($complaints as $complaint) {

        if ((int)$complaint['id'] === $selectedId) {

            $selected_complaint = $complaint;
            break;
        }
    }
}

if (!$selected_complaint && !empty($complaints)) {

    $selected_complaint = $complaints[0];
}