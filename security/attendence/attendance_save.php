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

if (!isset($conn) || !$conn) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
    exit;
}

$action = $_POST['action'] ?? 'single';
$today  = date('Y-m-d');

/* =========================
   MARK ALL PRESENT
========================= */
if ($action === 'mark_all_present') {

    $stmtStudents = mysqli_prepare(
    $conn,
    "SELECT id FROM students WHERE hostel_name = ?"
);

    mysqli_stmt_bind_param(
        $stmtStudents,
        "s",
        $hostel_name
    );

    mysqli_stmt_execute($stmtStudents);

$studentQuery = mysqli_stmt_get_result($stmtStudents);

    if (!$studentQuery) {
        echo json_encode([
            "success" => false,
            "message" => "Unable to fetch students: " . mysqli_error($conn)
        ]);
        exit;
    }

    while ($student = mysqli_fetch_assoc($studentQuery)) {
        $studentId = (int)$student['id'];

        $checkStmt = mysqli_prepare(
            $conn,
            "SELECT id
            FROM student_attendance
            WHERE student_id = ?
            AND attendance_date = ?
            AND hostel_name = ?"
        );

        if (!$checkStmt) {
            error_log("Student Query Error: " . mysqli_error($conn));

            echo json_encode([
                "success" => false,
                "message" => "Unable to fetch students."
            ]);
            exit;
        }

       mysqli_stmt_bind_param(
            $checkStmt,
            "iss",
            $studentId,
            $today,
            $hostel_name
        );
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if ($existing = mysqli_fetch_assoc($checkResult)) {
            $attendanceId = (int)$existing['id'];
            $status = 'Present';

            $updateStmt = mysqli_prepare(
                $conn,
                "UPDATE student_attendance 
                SET status = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
                AND hostel_name = ?"
            );

             if (!$updateStmt) {
                error_log("Update prepare failed: " . mysqli_error($conn));

                echo json_encode([
                    "success" => false,
                    "message" => "Unable to process request."
                ]);
    exit;
}

           mysqli_stmt_bind_param(
                $updateStmt,
                "sis",
                $status,
                $attendanceId,
                $hostel_name
            );

           if (!mysqli_stmt_execute($updateStmt)) {
                error_log("Update failed: " . mysqli_stmt_error($updateStmt));

                echo json_encode([
                    "success" => false,
                    "message" => "Unable to update record."
                ]);
                exit;
            }
        } else {
            $status = 'Present';

            $insertStmt = mysqli_prepare(
                $conn,
                "INSERT INTO student_attendance
                (
                    student_id,
                    attendance_date,
                    status,
                    hostel_name
                )
                VALUES (?, ?, ?, ?)"
            );

            if (!$insertStmt) {
                echo json_encode([
                    "success" => false,
                    "message" => "Insert prepare failed: " . mysqli_error($conn)
                ]);
                exit;
            }

            mysqli_stmt_bind_param(
                $insertStmt,
                "isss",
                $studentId,
                $today,
                $status,
                $hostel_name
            );

            

            if (!mysqli_stmt_execute($insertStmt)) {

                error_log(
                    "Attendance insert failed: "
                    . mysqli_stmt_error($insertStmt)
                );

                echo json_encode([
                    "success" => false,
                    "message" => "Unable to save attendance."
                ]);
                exit;
            }
        }
    }

    echo json_encode([
        "success" => true,
        "message" => "All students marked present successfully"
    ]);
    exit;
}

/* =========================
   SINGLE SAVE
========================= */
$studentId = intval($_POST['student_id'] ?? 0);
$status    = trim($_POST['status'] ?? '');

if ($studentId <= 0 || !in_array(strtolower($status), ['present', 'absent'])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid attendance data"
    ]);
    exit;
}

$normalizedStatus = ucfirst(strtolower($status));

$verifyStmt = mysqli_prepare(
    $conn,
    "SELECT id
     FROM students
     WHERE id = ?
     AND hostel_name = ?"
);

mysqli_stmt_bind_param(
    $verifyStmt,
    "is",
    $studentId,
    $hostel_name
);

mysqli_stmt_execute($verifyStmt);

$verifyResult = mysqli_stmt_get_result($verifyStmt);

if (mysqli_num_rows($verifyResult) === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized student"
    ]);
    exit;
}

$checkStmt = mysqli_prepare(
    $conn,
    "SELECT id
     FROM student_attendance
     WHERE student_id = ?
     AND attendance_date = ?
     AND hostel_name = ?"
);

if (!$checkStmt) {
    error_log("Check prepare failed: " . mysqli_error($conn));

    echo json_encode([
        "success" => false,
        "message" => "Unable to process request."
    ]);
    exit;
}

mysqli_stmt_bind_param(
    $checkStmt,
    "iss",
    $studentId,
    $today,
    $hostel_name
);

mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if ($existing = mysqli_fetch_assoc($checkResult)) {
    $attendanceId = (int)$existing['id'];

    $updateStmt = mysqli_prepare(
        $conn,
        "UPDATE student_attendance
        SET status = ?, updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
        AND hostel_name = ?"
    );

    if (!$updateStmt) {
        error_log("Update prepare failed: " . mysqli_error($conn));

        echo json_encode([
            "success" => false,
            "message" => "Internal Server Error."
        ]);
        exit;
    }

    mysqli_stmt_bind_param(
    $updateStmt,
    "sis",
    $normalizedStatus,
    $attendanceId,
    $hostel_name
);

    if (mysqli_stmt_execute($updateStmt)) {
        echo json_encode([
            "success" => true,
            "message" => "Attendance updated successfully"
        ]);
    } else {
        error_log("Attendance update failed: " . mysqli_stmt_error($updateStmt));

        echo json_encode([
            "success" => false,
            "message" => "Unable to update attendance."
        ]);
    }
} else {
    $insertStmt = mysqli_prepare(
        $conn,
        "INSERT INTO student_attendance
        (
            student_id,
            attendance_date,
            status,
            hostel_name
        )
        VALUES (?, ?, ?, ?)"
    );

    if (!$insertStmt) {
        error_log("Insert prepare failed: " . mysqli_error($conn));

        echo json_encode([
            "success" => false,
            "message" => "Unable to process request."
        ]);
        exit;
    }

    mysqli_stmt_bind_param(
    $insertStmt,
    "isss",
    $studentId,
    $today,
    $normalizedStatus,
    $hostel_name
);

    if (mysqli_stmt_execute($insertStmt)) {
        echo json_encode([
            "success" => true,
            "message" => "Attendance saved successfully"
        ]);
    } else {
        error_log("Attendance insert failed: " . mysqli_stmt_error($insertStmt));

        echo json_encode([
            "success" => false,
            "message" => "Unable to save attendance."
        ]);
    }
}

exit;
?>