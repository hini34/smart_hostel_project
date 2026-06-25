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


// $baseUrl = "";

if (!isset($conn) || !$conn) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

$search = trim($_GET['search'] ?? '');
$today  = date('Y-m-d');

$response = [
    "success" => true,
    "last_updated" => date("d M Y h:i:s A"),
    "counts" => [
        "total_students"   => 0,
        "present_students" => 0,
        "absent_students"  => 0
    ],
    "students" => []
];

/* =========================
   COUNTS
========================= */
$countSql = "
SELECT 
(
    SELECT COUNT(*)
    FROM students
    WHERE hostel_name = ?
) AS total_students,

(
    SELECT COUNT(DISTINCT sa.student_id)
    FROM student_attendance sa
    INNER JOIN students s ON sa.student_id = s.id
    WHERE sa.attendance_date = CURDATE()
    AND LOWER(sa.status) = 'present'
    AND s.hostel_name = ?
) AS present_students
";

$countStmt = mysqli_prepare($conn, $countSql);
mysqli_stmt_bind_param(
    $countStmt,
    "ss",
    $hostel_name,
    $hostel_name
);
mysqli_stmt_execute($countStmt);

$countResult = mysqli_stmt_get_result($countStmt);

if (!$countResult) {
    echo json_encode([
        "success" => false,
        "message" => "Count query failed"
    ]);
    exit;
}

$countRow = mysqli_fetch_assoc($countResult);

$totalStudents   = (int)($countRow['total_students'] ?? 0);
$presentStudents = (int)($countRow['present_students'] ?? 0);
$absentStudents  = max(0, $totalStudents - $presentStudents);

$response['counts']['total_students']   = $totalStudents;
$response['counts']['present_students'] = $presentStudents;
$response['counts']['absent_students']  = $absentStudents;

/* =========================
   STUDENT LIST
========================= */
$sql = "
SELECT 
    s.id,
    s.fullname,
    s.enrollment,
    s.photo,
    COALESCE(r.room_no, '-') AS room_no,
    COALESCE(sa.status, 'Absent') AS attendance_status

FROM students s

LEFT JOIN student_attendance sa
    ON sa.student_id = s.id
    AND sa.attendance_date = ?
    AND sa.hostel_name = ?

LEFT JOIN rooms r
    ON r.id = s.room_id

WHERE s.hostel_name = ?
";

$params = [$today, $hostel_name, $hostel_name];
$types  = "sss";

if ($search !== '') {
    $sql .= " AND (s.fullname LIKE ? OR s.enrollment LIKE ?)";
    $searchLike = "%" . $search . "%";
    $params[] = $searchLike;
    $params[] = $searchLike;
    $types .= "ss";
}

$sql .= " ORDER BY s.fullname ASC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed: " . mysqli_error($conn)
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$sr = 1;

while ($row = mysqli_fetch_assoc($result)) {
            $photoPath = "assets/images/default-user.png";

            if (!empty($row['photo'])) {

                $dbPhoto = str_replace('\\', '/', trim($row['photo']));

                if (file_exists(__DIR__ . '/../../' . $dbPhoto)) {
                    $photoPath = $dbPhoto;
                }
            }
                $response['students'][] = [
                    "sr_no" => $sr++,
                    "id" => (int)$row['id'],
                    "fullname" => $row['fullname'],
                    "enrollment" => $row['enrollment'],
                    "photo" => $photoPath,
                    "room_no" => $row['room_no'],
                    "attendance_status" => ucfirst(strtolower($row['attendance_status']))
                ];
}

echo json_encode($response);
exit;
?>