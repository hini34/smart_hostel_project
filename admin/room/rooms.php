<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if(empty($hostel_name)){
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? "Admin";
$page_title  = "Rooms Management";
$active_page = "rooms";

/* ====================== ADD / UPDATE ROOM ====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id     = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
    $room_no = mysqli_real_escape_string($conn, trim($_POST['room_no'] ?? ''));
    $room_type   = mysqli_real_escape_string($conn, trim($_POST['room_type'] ?? ''));
    $floor_no    = mysqli_real_escape_string($conn, trim($_POST['floor_no'] ?? ''));
    $block_name  = mysqli_real_escape_string($conn, trim($_POST['block_name'] ?? ''));
    $capacity    = intval($_POST['capacity'] ?? 0);
    $status      = mysqli_real_escape_string($conn, trim($_POST['status'] ?? 'AVAILABLE'));

    if ($room_no === '' || $room_type === '' || $floor_no === '' || $capacity <= 0) {
        die("Please fill all required room fields correctly.");
    }

    if ($room_id > 0) {
        $sql =  "UPDATE rooms SET
            room_no='$room_no',
            room_type='$room_type',
            floor_no='$floor_no',
            block_name='$block_name',
            capacity=$capacity,
            status='$status'
        WHERE id=$room_id
        AND hostel_name='$hostel_name'";
    } else {
        $occupied = 0;
        $sql = "INSERT INTO rooms
                (
                room_no,
                room_type,
                floor_no,
                block_name,
                capacity,
                occupied,
                status,
                hostel_name
                )
                VALUES
                    ('$room_no','$room_type','$floor_no','$block_name',$capacity,0,'$status','$hostel_name')";
    }
    

    /* CHECK DUPLICATE ROOM NO */
        $checkRoom = mysqli_query(
            $conn,
            "SELECT id FROM rooms WHERE room_no='$room_no'AND hostel_name='$hostel_name'"
        );

        if ($room_id > 0) {

            $checkRoom = mysqli_query(
                $conn,
                "SELECT id
                FROM rooms
                WHERE room_no='$room_no'
                AND hostel_name='$hostel_name'
                AND id != $room_id"
            );
        }

        if (mysqli_num_rows($checkRoom) > 0) {

            echo "
            <script>
                alert('Room Number already exists. Please use a different room number.');
                window.location='rooms.php';
            </script>
            ";
            exit;
        }

        if(mysqli_query($conn, $sql)){

            echo "
            <script>
                alert('Room Saved Successfully');
                window.location='rooms.php';
            </script>
            ";

        }else{

            echo "
            <script>
                alert('Unable to save room');
                window.location='rooms.php';
            </script>
            ";
        }
        exit;
}

/* ====================== DELETE ROOM ====================== */
if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);

    $checkRoom = mysqli_query(
        $conn,
        "SELECT id FROM rooms WHERE id=$id AND hostel_name='$hostel_name'"
    );

    if (mysqli_num_rows($checkRoom) == 0) {

        echo "
        <script>
            alert('Room not found');
            window.location='rooms.php';
        </script>
        ";
        exit;
    }

    mysqli_query(
        $conn,
        "UPDATE students
        SET room_id = NULL
        WHERE room_id = $id
        AND hostel_name='$hostel_name'"
    );

    if (mysqli_query($conn, "DELETE FROM rooms
                            WHERE id = $id
                            AND hostel_name='$hostel_name'")) {

        echo "
        <script>
            alert('Room deleted successfully');
            window.location='rooms.php';
        </script>
        ";

    } else {

        echo "
        <script>
            alert('Unable to delete room');
            window.location='rooms.php';
        </script>
        ";
    }

    exit;
}

/* ====================== EDIT ROOM ====================== */
$editRoom = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM rooms WHERE id = $id AND hostel_name='$hostel_name'");
    if ($res && mysqli_num_rows($res) > 0) {
        $editRoom = mysqli_fetch_assoc($res);
    }
}

/* ====================== FETCH ALL ROOMS ====================== */
$rooms = mysqli_query(
    $conn,
    "SELECT *
     FROM rooms
     WHERE hostel_name='$hostel_name'
     ORDER BY floor_no ASC, room_no ASC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . "/../../includes/admin/admin_header.php"; ?>
    <link rel="stylesheet" href="../../assets/css/admin_room.css">
</head>
<body>

<!-- Mobile Navbar -->
<nav class="navbar navbar-dark navbar-main d-lg-none fixed-top">
        <div class="container-fluid">
            <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar">
                <i class="fa-solid fa-bars"></i>
            </button>

            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fa-solid fa-building-user me-2"></i>
                <span>Smart Hostel - Admin</span>
            </a>

            <span class="badge bg-primary rounded-pill">
                <?php echo htmlspecialchars($admin_name); ?>
            </span>
        </div>
</nav>

<?php include __DIR__ . "/../../includes/admin/admin_sidebar.php"; ?>

<!-- Main Content -->
<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="page-title">Rooms Management</h1>
                <p class="page-subtitle">Add, edit, assign and monitor hostel rooms.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="assign_room.php" class="btn btn-outline-dark action-btn">
                    <i class="fa-solid fa-user-plus me-2"></i>Assign Room
                </a>
                <button class="btn btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#roomModal">
                    <i class="fa-solid fa-plus me-2"></i>Add Room
                </button>
            </div>
        </div>
    </div>

    <div class="card card-custom">
        <div class="card-header card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-1">Room Records</h5>
                <small class="text-light opacity-75">Manage room capacity, availability and student allocation</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Room No.</th>
                            <th>Type</th>
                            <th>Floor / Block</th>
                            <th>Capacity</th>
                            <th>Members</th>
                            <th>Available</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sn = 1;
                        if ($rooms && mysqli_num_rows($rooms) > 0):
                            while ($row = mysqli_fetch_assoc($rooms)):
                                $roomId = (int)$row['id'];

                                $memRes = mysqli_query($conn, "SELECT COUNT(*) AS total
                                                    FROM students
                                                    WHERE room_id = $roomId
                                                    AND hostel_name='$hostel_name'");
                                $memRow = $memRes ? mysqli_fetch_assoc($memRes) : ['total' => 0];
                                $members = (int)($memRow['total'] ?? 0);

                                $available = (int)$row['capacity'] - $members;
                                if ($available < 0) $available = 0;

                                if (strtoupper($row['status']) === 'MAINTENANCE') {
                                    $status = 'MAINTENANCE';
                                } elseif ($members >= (int)$row['capacity']) {
                                    $status = 'FULL';
                                } else {
                                    $status = 'AVAILABLE';
                                }
                        ?>
                        <tr>
                            <td><?php echo $sn++; ?></td>
                            <td class="room-no"><?php echo htmlspecialchars($row['room_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['floor_no'] . ' / ' . ($row['block_name'] ?: 'N/A')); ?></td>
                            <td><span class="mini-stat"><?php echo (int)$row['capacity']; ?> Beds</span></td>
                            <td><span class="mini-stat"><?php echo $members; ?> Students</span></td>
                            <td><span class="mini-stat"><?php echo $available; ?> Left</span></td>
                            <td>
                                <?php if ($status === 'AVAILABLE'): ?>
                                    <span class="soft-badge badge-available">Available</span>
                                <?php elseif ($status === 'FULL'): ?>
                                    <span class="soft-badge badge-full">Full</span>
                                <?php else: ?>
                                    <span class="soft-badge badge-maintenance">Maintenance</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="action-group d-inline-flex gap-2">
                                    <a href="rooms.php?edit=<?php echo $row['id']; ?>" class="btn btn-outline-primary" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="room_members.php?room_id=<?php echo $row['id']; ?>" class="btn btn-outline-secondary" title="View Members">
                                        <i class="fa-solid fa-users"></i>
                                    </a>
                                    <a href="rooms.php?delete=<?php echo $row['id']; ?>" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Delete this room and unassign its students?');">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="9" class="text-center empty-row">No rooms added yet.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Room Modal -->
<div class="modal fade" id="roomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="rooms.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <?php echo $editRoom ? 'Edit Room Details' : 'Add New Room'; ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="room_id" value="<?php echo $editRoom['id'] ?? ''; ?>">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Room Number</label>
                            <input type="text" name="room_no" class="form-control" required
                                   value="<?php echo htmlspecialchars($editRoom['room_no'] ?? ''); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Room Type</label>
                            <select name="room_type" class="form-select" required>
                                <?php
                                $types = ['Single','Double','Triple','Dormitory'];
                                $selectedType = $editRoom['room_type'] ?? '';
                                foreach ($types as $t) {
                                    $sel = ($t === $selectedType) ? 'selected' : '';
                                    echo "<option value=\"$t\" $sel>$t</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Floor No.</label>
                            <input type="text" name="floor_no" class="form-control" required
                                   value="<?php echo htmlspecialchars($editRoom['floor_no'] ?? ''); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Block / Wing</label>
                            <input type="text" name="block_name" class="form-control"
                                   value="<?php echo htmlspecialchars($editRoom['block_name'] ?? ''); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Capacity (Beds)</label>
                            <input type="number" name="capacity" class="form-control" min="1" required
                                   value="<?php echo htmlspecialchars($editRoom['capacity'] ?? 1); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <?php
                                $statuses = ['AVAILABLE','FULL','MAINTENANCE'];
                                $selectedStatus = $editRoom['status'] ?? 'AVAILABLE';
                                foreach ($statuses as $s) {
                                    $sel = ($s === $selectedStatus) ? 'selected' : '';
                                    echo "<option value=\"$s\" $sel>$s</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <?php echo $editRoom ? 'Update Room' : 'Save Room'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../../includes/admin/admin_footer.php"; ?>
<script src="assets/js/admin_rooms.js"></script>

<?php if ($editRoom): ?>
<script>
    window.addEventListener('load', function () {
        var roomModal = new bootstrap.Modal(document.getElementById('roomModal'));
        roomModal.show();
    });
</script>
<?php endif; ?>

</body>
</html>