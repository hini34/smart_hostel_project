<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$page_title = "Staff Profile";
$active_page = "staff";

function e($str){
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("Invalid staff ID");

// GET STAFF
$staffRes = mysqli_query($conn, "SELECT * FROM staff WHERE id=$id");
$staff = mysqli_fetch_assoc($staffRes);
if (!$staff) die("Staff not found");

/* ================= ATTENDANCE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance_action'])) {

    $date   = $_POST['attendance_date'] ?? date('Y-m-d');
    $status = $_POST['status'] ?? 'Present';
    $in     = !empty($_POST['check_in']) ? $_POST['check_in'] : null;
    $out    = !empty($_POST['check_out']) ? $_POST['check_out'] : null;
    $remarks= trim($_POST['remarks'] ?? '');

    $dateEsc = mysqli_real_escape_string($conn,$date);

    $check = mysqli_query($conn,"SELECT id FROM staff_attendance WHERE staff_id=$id AND attendance_date='$dateEsc'");

    if (mysqli_num_rows($check)>0){
        $row = mysqli_fetch_assoc($check);
        $aid = $row['id'];

        $sql = "UPDATE staff_attendance SET 
                    status='".mysqli_real_escape_string($conn,$status)."',
                    check_in=".($in?"'".mysqli_real_escape_string($conn,$in)."'":"NULL").",
                    check_out=".($out?"'".mysqli_real_escape_string($conn,$out)."'":"NULL").",
                    remarks='".mysqli_real_escape_string($conn,$remarks)."'
                WHERE id=$aid";
        mysqli_query($conn,$sql);
        $att_msg = "Attendance updated.";
    }else{
        $stmt = mysqli_prepare($conn,"INSERT INTO staff_attendance 
            (staff_id, attendance_date, status, check_in, check_out, remarks)
            VALUES (?,?,?,?,?,?)");

        mysqli_stmt_bind_param($stmt,"isssss",$id,$date,$status,$in,$out,$remarks);
        mysqli_stmt_execute($stmt);
        $att_msg = "Attendance marked.";
    }
}

/* ================= LEAVE APPLY ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leave_action']) && $_POST['leave_action']=='apply') {

    $type   = $_POST['leave_type'] ?? 'Other';
    $start  = $_POST['start_date'] ?? '';
    $end    = $_POST['end_date'] ?? '';
    $reason = trim($_POST['reason'] ?? '');

    if ($start && $end) {
        $stmt = mysqli_prepare($conn,"INSERT INTO staff_leaves 
            (staff_id, leave_type, start_date, end_date, reason)
            VALUES (?,?,?,?,?)");

        mysqli_stmt_bind_param($stmt,"issss",$id,$type,$start,$end,$reason);
        mysqli_stmt_execute($stmt);

        $leave_msg = "Leave request submitted.";
    } else {
        $leave_msg = "Please select start and end date.";
    }
}

/* ================= LEAVE APPROVE / REJECT ================= */
if (isset($_GET['leave_id']) && isset($_GET['set_status'])) {

    $lid = (int)$_GET['leave_id'];
    $st  = $_GET['set_status'] === 'Approved' ? 'Approved' : 'Rejected';

    mysqli_query($conn,"UPDATE staff_leaves 
        SET status='$st', decided_at=NOW()
        WHERE id=$lid AND staff_id=$id");

    header("Location: staff_profile.php?id=".$id."#leaves");
    exit;
}

/* ================= FETCH DATA ================= */

// Attendance
$attRes = mysqli_query($conn, "
    SELECT * FROM staff_attendance 
    WHERE staff_id=$id 
    ORDER BY attendance_date DESC 
    LIMIT 30
");

// Leaves
$leaveRes = mysqli_query($conn, "
    SELECT * FROM staff_leaves 
    WHERE staff_id=$id 
    ORDER BY id DESC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . "/../../includes/admin/admin_header.php"; ?>
    <link rel="stylesheet" href="../../assets/css/admin_staff_profile.css">
</head>

<body>


<div class="content-wrapper">
<div class="container-fluid p-3">

<a href="staff.php" class="btn btn-light mb-3">
    <i class="fa-solid fa-arrow-left"></i> Back
</a>

<div class="row g-3">

<!-- PROFILE -->
<div class="col-lg-4">
<div class="card text-center p-3">

<?php
$photo = $staff['photo'] 
    ? "../../uploads/staff/".e($staff['photo']) 
    : "https://via.placeholder.com/100?text=S";
?>

<img src="<?php echo $photo; ?>" class="avatar-lg mb-2">

<h4><?php echo e($staff['name']); ?></h4>
<div class="badge bg-info mb-2"><?php echo e($staff['role']); ?></div>

<p class="small text-muted"><?php echo e($staff['employee_code']); ?></p>
<p class="small">Shift: <?php echo e($staff['shift']); ?></p>

<hr>

<p><i class="fa fa-phone"></i> <?php echo e($staff['mobile']); ?></p>
<p><i class="fa fa-envelope"></i> <?php echo e($staff['email']); ?></p>
<p>Salary: ₹ <?php echo number_format($staff['salary'],2); ?></p>

</div>
</div>

<!-- RIGHT -->
<div class="col-lg-8">

<!-- TABS -->
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#att">Attendance</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#leave">Leaves</button>
    </li>
</ul>

<div class="tab-content">

<!-- ATTENDANCE -->
<div class="tab-pane fade show active" id="att">

<?php if(isset($att_msg)): ?>
<div class="alert alert-info"><?php echo e($att_msg); ?></div>
<?php endif; ?>

<form method="POST" class="row g-2 mb-3">
<input type="hidden" name="attendance_action" value="1">

<div class="col-md-3">
<input type="date" name="attendance_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
</div>

<div class="col-md-3">
<select name="status" class="form-select">
<option>Present</option>
<option>Absent</option>
<option>Leave</option>
</select>
</div>

<div class="col-md-2">
<input type="time" name="check_in" class="form-control">
</div>

<div class="col-md-2">
<input type="time" name="check_out" class="form-control">
</div>

<div class="col-md-2">
<button class="btn btn-primary w-100">Save</button>
</div>
</form>

<table class="table table-sm">
<thead>
<tr>
<th>Date</th>
<th>Status</th>
<th>In</th>
<th>Out</th>
</tr>
</thead>

<tbody>

<?php if(mysqli_num_rows($attRes)==0): ?>
<tr><td colspan="4">No data</td></tr>
<?php endif; ?>

<?php while($a=mysqli_fetch_assoc($attRes)): ?>
<tr>
<td><?php echo e($a['attendance_date']); ?></td>
<td><?php echo e($a['status']); ?></td>
<td><?php echo e($a['check_in']); ?></td>
<td><?php echo e($a['check_out']); ?></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>

</div>

<!-- LEAVES -->
<div class="tab-pane fade" id="leave">

<?php if(isset($leave_msg)): ?>
<div class="alert alert-info"><?php echo e($leave_msg); ?></div>
<?php endif; ?>

<form method="POST" class="row g-2 mb-3">
<input type="hidden" name="leave_action" value="apply">

<div class="col-md-3">
<select name="leave_type" class="form-select">
<option>Sick</option>
<option>Casual</option>
<option>Other</option>
</select>
</div>

<div class="col-md-3">
<input type="date" name="start_date" class="form-control">
</div>

<div class="col-md-3">
<input type="date" name="end_date" class="form-control">
</div>

<div class="col-md-3">
<button class="btn btn-primary w-100">Apply</button>
</div>

</form>

<table class="table table-sm">
<thead>
<tr>
<th>Type</th>
<th>From</th>
<th>To</th>
<th>Status</th>
</tr>
</thead>

<tbody>

<?php if(mysqli_num_rows($leaveRes)==0): ?>
<tr><td colspan="4">No leave</td></tr>
<?php endif; ?>

<?php while($l=mysqli_fetch_assoc($leaveRes)): ?>
<tr>
<td><?php echo e($l['leave_type']); ?></td>
<td><?php echo e($l['start_date']); ?></td>
<td><?php echo e($l['end_date']); ?></td>
<td><?php echo e($l['status']); ?></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>

</div>

</div>
</div>

</div>
</div>

<?php include __DIR__ . "/../../includes/admin/admin_footer.php"; ?>

</body>
</html>