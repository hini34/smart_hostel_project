<?php
require_once(__DIR__ . "/../../config/db.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . "/complaint_helpers.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

$msg   = '';
$error = '';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    ($_POST['action'] ?? '') === 'update_complaint'
) {

    $complaint_id   = (int)($_POST['complaint_id'] ?? 0);
    $status         = trim($_POST['status'] ?? '');
    $assigned_staff = !empty($_POST['assigned_staff_id'])
                        ? (int)$_POST['assigned_staff_id']
                        : null;

    $admin_response = trim($_POST['admin_response'] ?? '');

    if (
        empty($hostel_name) ||
        $complaint_id <= 0 ||
        !in_array(
            $status,
            allowedComplaintStatuses(),
            true
        )
    ) {

        $error = "Please select a valid complaint and status.";

    } else {

        $checkStmt = mysqli_prepare(
            $conn,
            "
            SELECT id
            FROM complaints
            WHERE id = ?
            AND hostel_name = ?
            LIMIT 1
            "
        );

        mysqli_stmt_bind_param(
            $checkStmt,
            "is",
            $complaint_id,
            $hostel_name
        );

        mysqli_stmt_execute($checkStmt);

        $checkResult =
            mysqli_stmt_get_result($checkStmt);

        if (
            mysqli_num_rows($checkResult) === 0
        ) {

            $error =
                "Complaint not found or access denied.";

        } else {

            if ($assigned_staff === null) {

                $stmt = mysqli_prepare(
                    $conn,
                    "
                    UPDATE complaints
                    SET
                        status = ?,
                        assigned_staff_id = NULL,
                        admin_response = ?,
                        updated_at = NOW()
                    WHERE id = ?
                    "
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "ssi",
                    $status,
                    $admin_response,
                    $complaint_id
                );

            } else {

                $stmt = mysqli_prepare(
                    $conn,
                    "
                    UPDATE complaints
                    SET
                        status = ?,
                        assigned_staff_id = ?,
                        admin_response = ?,
                        updated_at = NOW()
                    WHERE id = ?
                    "
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "sisi",
                    $status,
                    $assigned_staff,
                    $admin_response,
                    $complaint_id
                );
            }

            if (
                $stmt &&
                mysqli_stmt_execute($stmt)
            ) {

                $msg =
                    "Complaint updated successfully.";

            } else {

                $error =
                    "Unable to update complaint.";
            }

            if ($stmt) {
                mysqli_stmt_close($stmt);
            }
        }

        mysqli_stmt_close($checkStmt);
    }
}
?>