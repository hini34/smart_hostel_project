<?php
// student/complaints/complaint_save.php
session_start();
require_once(__DIR__ . "/../../config/db.php");

// Login check
if (!isset($_SESSION['student_id'])) {
    header("Location: /smart_hostel/auth/students/student_login.php");
    exit;
}

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    header("Location: student_complaints.php?error=" . urlencode("Hostel not found."));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: student_complaints.php");
    exit;
}

// $student_id  = $_SESSION['student_id'];
// $title       = trim($_POST['title'] ?? '');
// $category    = trim($_POST['category'] ?? '');
// $description = trim($_POST['description'] ?? '');
// $room_no     = trim($_POST['room_no'] ?? '');
// $priority    = trim($_POST['priority'] ?? 'Low');

// if ($title === '' || $category === '' || $description === '') {
//     header("Location: student_complaints.php?error=" . urlencode("Please fill all required fields."));
//     exit;
// }

$student_id  = $_SESSION['student_id'] ?? 0;
$title       = trim($_POST['title'] ?? '');
$category    = trim($_POST['category'] ?? '');
$description = trim($_POST['description'] ?? '');
$room_no     = trim($_POST['room_no'] ?? '');
$priority    = trim($_POST['priority'] ?? 'Low');


if ($student_id <= 0) {
    header("Location: login.php");
    exit;
}

if ($title === '' || $category === '' || $description === '' || $room_no === '') {
    header("Location: student_complaints.php?error=" . urlencode("Please fill all required fields."));
    exit;
}

$image_path = null;

if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
    $upload_dir = __DIR__ . "/../../uploads/complaints";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (in_array($ext, $allowed)) {
        $new_name = "complaint_" . $student_id . "_" . time() . "." . $ext;
        $target   = $upload_dir . "/" . $new_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image_path = "uploads/complaints/" . $new_name;
        }
    }
}

$sql = "INSERT INTO complaints
(
    student_id,
    hostel_name,
    title,
    category,
    description,
    room_no,
    priority,
    image,
    status,
    created_at
)
VALUES
(
    ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW()
)";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    header("Location: student_complaints.php?error=" . urlencode("Database error occurred."));
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "isssssss",
    $student_id,
    $hostel_name,
    $title,
    $category,
    $description,
    $room_no,
    $priority,
    $image_path
);

// if (mysqli_stmt_execute($stmt)) {
//     header("Location: student_complaints.php?success=1");
// } else {
//     $err = mysqli_error($conn);
//     header("Location: student_complaints.php?error=" . urlencode("DB Error: " . $err));
// }
// exit;

if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    header("Location: student_complaints.php?success=" . urlencode("Complaint submitted successfully."));
    exit;

} else {

    error_log("Complaint Insert Error: " . mysqli_stmt_error($stmt));

    mysqli_stmt_close($stmt);

    header("Location: student_complaints.php?error=" . urlencode("Failed to submit complaint. Please try again."));
    exit;
}
?>