<?php
// admin/rooms/room_members.php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if(empty($hostel_name)){
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}


$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

if ($room_id <= 0) {
    die("Invalid room.");
}

/// Room info
$roomRes = mysqli_query($conn, "SELECT *
FROM rooms
WHERE id = $room_id
AND hostel_name='$hostel_name'");
$room = mysqli_fetch_assoc($roomRes);

if (!$room) {
    die("Room not found.");
}

// Live occupied count from students table
$countRes = mysqli_query($conn, "SELECT COUNT(*) AS total
FROM students
WHERE room_id = $room_id
AND hostel_name='$hostel_name'");
$countRow = mysqli_fetch_assoc($countRes);
$occupiedCount = (int)($countRow['total'] ?? 0);

// Students in this room
$studentsRes = mysqli_query(
    $conn,
    "SELECT id, fullname, enrollment, mobile
    FROM students
    WHERE room_id = $room_id
    AND hostel_name='$hostel_name'"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Room Members</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/room_member.css">
</head>
<body>

<div class>

<body class="bg-light">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">Room <?php echo htmlspecialchars($room['room_no']); ?> Members</h3>
            <small class="text-muted">
                Capacity: <?php echo (int)$room['capacity']; ?> |
                Occupied: <?php echo $occupiedCount; ?>
            </small>
        </div>
        <a href="rooms.php" class="btn btn-secondary btn-sm">Back to Rooms</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Enrollment</th>
                            <th>Mobile</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sn = 1;
                    while($stu = mysqli_fetch_assoc($studentsRes)): ?>
                        <tr>
                            <td><?php echo $sn++; ?></td>
                            <td><?php echo htmlspecialchars($stu['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($stu['enrollment']); ?></td>
                            <td><?php echo htmlspecialchars($stu['mobile']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($sn == 1): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                No students assigned to this room.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>