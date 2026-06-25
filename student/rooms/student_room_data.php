<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

$response = [
    'success' => false,
    'room' => [
        'room_no'        => null,
        'floor_no'       => null,
        'block_name'     => null,
        'room_type'      => null,
        'capacity'       => null,
        'occupied'       => null,
        'occupancy_text' => 'Not assigned',
        'status_text'    => 'Room not assigned'
    ],
    'roommates' => [],
    'warden' => [
        'name'          => null,
        'mobile'        => null,
        'email'         => null,
        'helper_mobile' => null
    ],
    'history' => []
];

if (empty($hostel_name)) {
    echo json_encode($response);
    exit;
}


try {
    $student_id = null;

    if (!empty($_SESSION['student_id'])) {
        $student_id = (int)$_SESSION['student_id'];
    }

    if (!$student_id && !empty($_SESSION['student_enrollment'])) {
        $enrollment = trim($_SESSION['student_enrollment']);

        $tmp = mysqli_prepare($conn, "SELECT id FROM students WHERE enrollment = ? AND hostel_name = ? LIMIT 1");
        if ($tmp) {
            mysqli_stmt_bind_param(
                $tmp,
                "ss",
                $enrollment,
                $hostel_name
            );
            mysqli_stmt_execute($tmp);
            $tmpRes = mysqli_stmt_get_result($tmp);

            if ($tmpRow = mysqli_fetch_assoc($tmpRes)) {
                $student_id = (int)$tmpRow['id'];
                $_SESSION['student_id'] = $student_id;
            }
            mysqli_stmt_close($tmp);
        }
    }

    if (!$student_id) {
        echo json_encode($response);
        exit;
    }

    $room_id = null;

    $sql = "SELECT 
                s.id,
                s.fullname,
                s.enrollment,
                s.mobile,
                s.photo,
                s.room_id,
                r.room_no,
                r.room_type,
                r.floor_no,
                r.block_name,
                r.capacity,
                r.occupied,
                r.status
            FROM students s
            LEFT JOIN rooms r ON s.room_id = r.id
            WHERE s.id = ?
            AND s.hostel_name = ?
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception(mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
    $stmt,
    "is",
    $student_id,
    $hostel_name
    );
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $room_id = !empty($row['room_id']) ? (int)$row['room_id'] : null;

        $cap = (int)($row['capacity'] ?? 0);
        $occ = (int)($row['occupied'] ?? 0);
        $remaining = ($cap > 0) ? max($cap - $occ, 0) : 0;
        $dbStatus = strtoupper(trim($row['status'] ?? ''));

        $occupancy_text = !empty($row['room_type']) ? $row['room_type'] : ($cap > 0 ? $cap . ' Seater' : 'Not assigned');

        $status_text = 'Room not assigned';
        if ($room_id && !empty($row['room_no'])) {
            if ($dbStatus === 'AVAILABLE') {
                if ($occ == 0) {
                    $status_text = 'Available';
                } elseif ($remaining > 0) {
                    $status_text = "Sharing ({$remaining} bed(s) available)";
                } else {
                    $status_text = 'Occupied';
                }
            } elseif ($dbStatus === 'OCCUPIED') {
                $status_text = 'Occupied';
            } else {
                $status_text = 'Assigned';
            }
        }

        $response['room'] = [
            'room_no'        => $row['room_no'] ?? null,
            'floor_no'       => $row['floor_no'] ?? null,
            'block_name'     => $row['block_name'] ?? null,
            'room_type'      => $row['room_type'] ?? null,
            'capacity'       => $row['capacity'] ?? null,
            'occupied'       => $row['occupied'] ?? null,
            'occupancy_text' => $occupancy_text,
            'status_text'    => $status_text
        ];

        $response['success'] = true;
    }
    mysqli_stmt_close($stmt);

    if (!empty($room_id)) {
        $mate_sql ="SELECT fullname, enrollment, mobile, photo
             FROM students
             WHERE room_id = ?
             AND id != ?
             AND hostel_name = ?";

        $mstmt = mysqli_prepare($conn, $mate_sql);
        if ($mstmt) {
            mysqli_stmt_bind_param(
            $mstmt,
            "iis",
            $room_id,
            $student_id,
            $hostel_name
        );
            mysqli_stmt_execute($mstmt);
            $mres = mysqli_stmt_get_result($mstmt);

            while ($m = mysqli_fetch_assoc($mres)) {
                $response['roommates'][] = [
                    'fullname'   => $m['fullname'] ?? '',
                    'enrollment' => $m['enrollment'] ?? '',
                    'mobile'     => $m['mobile'] ?? '',
                    'photo'      => $m['photo'] ?? ''
                ];
            }
            mysqli_stmt_close($mstmt);
        }
    }

    $wsql = "SELECT name, mobile, alt_mobile, email
         FROM staff
         WHERE role = 'Warden'
         AND hostel_name = ?
         LIMIT 1";

    $wstmt = mysqli_prepare($conn, $wsql);
            if ($wstmt) {
                mysqli_stmt_bind_param(
            $wstmt,
            "s",
            $hostel_name
        );

        mysqli_stmt_execute($wstmt);
        $wres = mysqli_stmt_get_result($wstmt);

        if ($wardenRow = mysqli_fetch_assoc($wres)) {
            $response['warden'] = [
                'name'          => $wardenRow['name'] ?? null,
                'mobile'        => $wardenRow['mobile'] ?? null,
                'email'         => $wardenRow['email'] ?? null,
                'helper_mobile' => $wardenRow['alt_mobile'] ?? null
            ];
        }
        mysqli_stmt_close($wstmt);
    }

    echo json_encode($response);

} catch (Throwable $e) {

    error_log($e->getMessage());

    echo json_encode([
        'success' => false,
        'room' => $response['room'],
        'roommates' => [],
        'warden' => $response['warden'],
        'history' => []
    ]);
}
?>