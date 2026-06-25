<?php
// admin/rooms/assign_room.php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if(empty($hostel_name)){
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}

// FORM SUBMIT (POST)
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = intval($_POST['student_id']);
    $room_id    = intval($_POST['room_id']);

    // 1. Room capacity check
    $roomRes = mysqli_query(
    $conn,
    "SELECT capacity, occupied, room_no
     FROM rooms
     WHERE id=$room_id
     AND hostel_name='$hostel_name'"
);
    $room    = mysqli_fetch_assoc($roomRes);

    if (!$room) {
        $message = "Invalid room selected.";
    } elseif ($room['occupied'] >= $room['capacity']) {
        $message = "Room {$room['room_no']} is already FULL.";
    } else {
        // 2. Student ko room assign karo
        mysqli_query($conn, "UPDATE students
                                    SET room_id=$room_id
                                    WHERE id=$student_id
                                    AND hostel_name='$hostel_name'");

        // 3. Room ki occupied count badhao
        mysqli_query($conn, "UPDATE rooms SET occupied = occupied + 1 WHERE id=$room_id");

        $message = "Room {$room['room_no']} assigned successfully!";
    }
}

// DROPDOWN DATA
// Students jinka room abhi NULL hai
        $students = mysqli_query(
            $conn,
            "SELECT id, fullname, enrollment
            FROM students
            WHERE room_id IS NULL
            AND hostel_name='$hostel_name'
            ORDER BY fullname"
        );

// Rooms jisme abhi space hai
        $rooms = mysqli_query(
            $conn,
            "SELECT id, room_no, capacity, occupied
            FROM rooms
            WHERE capacity > occupied
            AND hostel_name='$hostel_name'
            ORDER BY room_no"
        );
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Room</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6fb;
            font-family: "Segoe UI", sans-serif;
        }

        h3 {
            font-weight: 600;
            color: #0f172a;
        }

        /* Card */
        .card {
            border: none;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        }

        /* Inputs */
        .form-select, .form-control {
            border-radius: 999px;
            padding: 10px;
        }

        label {
            font-weight: 500;
            color: #1e293b;
        }

        /* Buttons */
        .btn-primary {
            border-radius: 999px;
            padding: 8px 22px;
        }
        .btn-secondary {
            border-radius: 999px;
            padding: 8px 22px;
        }

        /* Alert styling */
        .alert-info {
            border-radius: 12px;
        }

        /* Hover effect */
        select.form-select:hover {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .15);
        }

        /* Responsive */
        @media (max-width: 576px) {
            h3 {
                font-size: 1.35rem;
            }
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <h3 class="mb-3">Assign Room to Student</h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="assign_room.php" method="POST" class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Select Student</label>
                    <select name="student_id" class="form-select" required>
                        <option value="">-- Choose Student --</option>
                        <?php while($s = mysqli_fetch_assoc($students)): ?>
                            <option value="<?php echo $s['id']; ?>">
                                <?php echo $s['fullname'] . " (" . $s['enrollment'] . ")"; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Select Room</label>
                    <select name="room_id" class="form-select" required>
                        <option value="">-- Choose Room --</option>
                        <?php while($r = mysqli_fetch_assoc($rooms)): 
                            $available = $r['capacity'] - $r['occupied'];
                        ?>
                            <option value="<?php echo $r['id']; ?>">
                                Room <?php echo $r['room_no']; ?> (Available: <?php echo $available; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-12 text-end">
                    <a href="rooms.php" class="btn btn-secondary">Back to Rooms</a>
                    <button type="submit" class="btn btn-primary">Assign Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>