<?php
// admin/staff/staff.php
session_start();
require_once(__DIR__ . "/../../config/db.php");


$hostel_name = $_SESSION['hostel_name'] ?? '';

if(empty($hostel_name)){
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}

// OPTIONAL: admin auth check
// if (!isset($_SESSION['admin'])) { header("Location: ../admin_login.php"); exit; }

$admin_name = $_SESSION['admin_name'] ?? "Admin";
$page_title = "Staff Management";
$active_page = "staff";

function e($str){
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

$errors = [];
$success = "";

function generate_employee_code($conn)
{
    $res = mysqli_query(
        $conn,
        "SELECT MAX(id) AS max_id FROM staff"
    );

    $row = mysqli_fetch_assoc($res);

    $next = (int)($row['max_id'] ?? 0) + 1;

    return "STF" . str_pad($next, 4, "0", STR_PAD_LEFT);
}

$employee_code = generate_employee_code(
    $conn,
);

/* ---------------- ADD / EDIT STAFF ---------------- */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) &&
    in_array($_POST['action'], ['add_staff', 'edit_staff'], true)
) {
    $id           = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name         = trim($_POST['name'] ?? '');
    $mobile       = trim($_POST['mobile'] ?? '');
    $alt_mobile   = trim($_POST['alt_mobile'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $gender       = $_POST['gender'] ?? 'Male';
    $dob          = $_POST['dob'] ?? null;
    $address      = trim($_POST['address'] ?? '');
    $role         = trim($_POST['role'] ?? '');
    $shift        = $_POST['shift'] ?? 'Full Day';
    $salary       = (float)($_POST['salary'] ?? 0);
    $joining_date = $_POST['joining_date'] ?? null;
    $username     = trim($_POST['username'] ?? '');
    $password     = $_POST['password'] ?? '';
    $status       = $_POST['status'] ?? 'Active';

    if ($name === '')   $errors[] = "Name is required.";
    if ($mobile === '') $errors[] = "Mobile is required.";
    if ($role === '')   $errors[] = "Role is required.";

    $photo_file = null;

    if (!empty($_FILES['photo']['name'])) {
        $upload_dir = __DIR__ . "/../../uploads/staff/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed_ext, true)) {
            $errors[] = "Only JPG, JPEG, PNG, WEBP files are allowed.";
        } else {
            $photo_file = "staff_" . time() . "_" . rand(100,999) . "." . $ext;
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo_file)) {
                $errors[] = "Photo upload failed.";
            }
        }
    }

    if (empty($errors)) {
        if ($_POST['action'] === 'add_staff') {
         $employee_code = generate_employee_code(
                            $conn,
                        );
            $hash = $password !== '' ? password_hash($password, PASSWORD_BCRYPT) : null;

            $checkUser = mysqli_query(
                $conn,
                "SELECT id
                FROM staff
                WHERE username='" .
                mysqli_real_escape_string($conn,$username) .
                "'
                AND hostel_name='$hostel_name'"
            );

            if(mysqli_num_rows($checkUser) > 0){

                echo "
                <script>
                    alert('Username already exists');
                    window.location='staff.php';
                </script>
                ";

                exit;
            }

            $stmt = mysqli_prepare($conn, "INSERT INTO staff
                (employee_code, name, mobile, alt_mobile, email, gender, dob, address, role, shift, salary, joining_date, username, password, photo, status, hostel_name)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            mysqli_stmt_bind_param(
                $stmt,
                "ssssssssssdssssss",
                $employee_code,
                $name,
                $mobile,
                $alt_mobile,
                $email,
                $gender,
                $dob,
                $address,
                $role,
                $shift,
                $salary,
                $joining_date,
                $username,
                $hash,
                $photo_file,
                $status,
                $hostel_name
            );

            if (mysqli_stmt_execute($stmt)) {

                    echo "
                    <script>
                        alert('Staff added successfully');
                        window.location='staff.php';
                    </script>
                    ";
                    exit;

                } else {

                    echo "
                    <script>
                        alert('Unable to add staff');
                        window.location='staff.php';
                    </script>
                    ";
                    exit;
                }
               // mysqli_stmt_close($stmt);

        } else {
            if (!$photo_file) {
                $res = mysqli_query($conn, "SELECT photo
                FROM staff
                WHERE id = $id
                AND hostel_name='$hostel_name'");
                $row = mysqli_fetch_assoc($res);
                $photo_file = $row['photo'] ?? null;
            }

            $password_sql = "";
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $password_sql = ", password='" . mysqli_real_escape_string($conn, $hash) . "'";
            }

            $sql = "UPDATE staff SET 
                        name='" . mysqli_real_escape_string($conn, $name) . "',
                        mobile='" . mysqli_real_escape_string($conn, $mobile) . "',
                        alt_mobile='" . mysqli_real_escape_string($conn, $alt_mobile) . "',
                        email='" . mysqli_real_escape_string($conn, $email) . "',
                        gender='" . mysqli_real_escape_string($conn, $gender) . "',
                        dob=" . ($dob ? "'" . mysqli_real_escape_string($conn, $dob) . "'" : "NULL") . ",
                        address='" . mysqli_real_escape_string($conn, $address) . "',
                        role='" . mysqli_real_escape_string($conn, $role) . "',
                        shift='" . mysqli_real_escape_string($conn, $shift) . "',
                        salary=" . $salary . ",
                        joining_date=" . ($joining_date ? "'" . mysqli_real_escape_string($conn, $joining_date) . "'" : "NULL") . ",
                        username='" . mysqli_real_escape_string($conn, $username) . "',
                        photo='" . mysqli_real_escape_string($conn, $photo_file) . "',
                        status='" . mysqli_real_escape_string($conn, $status) . "'
                        $password_sql
                    WHERE id = $id
                    AND hostel_name='$hostel_name'";

            if (mysqli_query($conn, $sql)) {
                echo "
                    <script>
                        alert('staff updated successfully');
                        window.location='staff.php';
                    </script>
                    ";
                $success = "Staff updated successfully.";
            } else {
                echo "
                    <script>
                        alert('Unable to update staff');
                        window.location='staff.php';
                    </script>
                    ";
                    exit;
            }
        }
    }
}

/* ---------------- DELETE ---------------- */
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    if (mysqli_query($conn, "DELETE FROM staff
        WHERE id = $id
        AND hostel_name='$hostel_name'")) {

        echo "
        <script>
            alert('Staff deleted successfully');
            window.location='staff.php';
        </script>
        ";

    } else {

        echo "
        <script>
            alert('Unable to delete staff');
            window.location='staff.php';
        </script>
        ";
    }

    exit;
}

/* ---------------- FILTERS ---------------- */
$where = "WHERE hostel_name='$hostel_name'";
$q        = trim($_GET['q'] ?? '');
$role_f   = trim($_GET['role'] ?? '');
$shift_f  = trim($_GET['shift'] ?? '');
$status_f = trim($_GET['status'] ?? '');

if ($q !== '') {
    $q_esc = mysqli_real_escape_string($conn, $q);
    $where .= " AND (name LIKE '%$q_esc%' OR mobile LIKE '%$q_esc%' OR employee_code LIKE '%$q_esc%')";
}
if ($role_f !== '') {
    $r_esc = mysqli_real_escape_string($conn, $role_f);
    $where .= " AND role = '$r_esc'";
}
if ($shift_f !== '') {
    $s_esc = mysqli_real_escape_string($conn, $shift_f);
    $where .= " AND shift = '$s_esc'";
}
if ($status_f !== '') {
    $st_esc = mysqli_real_escape_string($conn, $status_f);
    $where .= " AND status = '$st_esc'";
}

$staffRes = mysqli_query($conn, "SELECT * FROM staff $where ORDER BY id DESC");
$rolesRes = mysqli_query($conn, "SELECT DISTINCT role
FROM staff
WHERE hostel_name='$hostel_name'
ORDER BY role ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . "/../../includes/admin/admin_header.php"; ?>
    <link rel="stylesheet" href="../../assets/css/admin_staff.css">
</head>
<body>

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

<div class="content-wrapper">
    <div class="container-fluid p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Staff Management</h3>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#staffModal">
                <i class="fa-solid fa-plus"></i> Add Staff
            </button>
        </div>

        <?php if($success): ?>
            <div class="alert alert-success py-2"><?php echo e($success); ?></div>
        <?php endif; ?>

        <?php if(!empty($errors)): ?>
            <div class="alert alert-danger py-2">
                <?php foreach($errors as $er): ?>
                    <div><?php echo e($er); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div class="alert alert-warning py-2">Staff deleted.</div>
        <?php endif; ?>

        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <form class="row g-2" method="GET">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="q" placeholder="Search by name, mobile, code" value="<?php echo e($q); ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="role" class="form-select">
                            <option value="">All Roles</option>
                            <?php while($r = mysqli_fetch_assoc($rolesRes)): ?>
                                <option value="<?php echo e($r['role']); ?>" <?php echo ($role_f === $r['role']) ? 'selected' : ''; ?>>
                                    <?php echo e($r['role']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="shift" class="form-select">
                            <option value="">All Shifts</option>
                            <option value="Morning" <?php echo ($shift_f === 'Morning') ? 'selected' : ''; ?>>Morning</option>
                            <option value="Evening" <?php echo ($shift_f === 'Evening') ? 'selected' : ''; ?>>Evening</option>
                            <option value="Night" <?php echo ($shift_f === 'Night') ? 'selected' : ''; ?>>Night</option>
                            <option value="Full Day" <?php echo ($shift_f === 'Full Day') ? 'selected' : ''; ?>>Full Day</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="Active" <?php echo ($status_f === 'Active') ? 'selected' : ''; ?>>Active</option>
                            <option value="Inactive" <?php echo ($status_f === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-secondary flex-fill">
                            <i class="fa-solid fa-filter"></i> Filter
                        </button>
                        <a href="staff.php" class="btn btn-light flex-fill">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Staff</th>
                                <th>Role / Shift</th>
                                <th>Contact</th>
                                <th>Salary</th>
                                <th>Status</th>
                                <th width="160">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($staffRes) == 0): ?>
                                <tr><td colspan="6" class="text-center text-muted">No staff found.</td></tr>
                            <?php endif; ?>

                            <?php while($s = mysqli_fetch_assoc($staffRes)): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php
                                            $photo = !empty($s['photo'])
                                                ? "../../uploads/staff/" . e($s['photo'])
                                                : "https://via.placeholder.com/40x40?text=S";
                                            ?>
                                            <img src="<?php echo $photo; ?>" class="avatar-sm me-2" alt="Staff">
                                            <div>
                                                <div class="fw-semibold"><?php echo e($s['name']); ?></div>
                                                <div class="text-muted small"><?php echo e($s['employee_code']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-role"><?php echo e($s['role']); ?></span><br>
                                        <small class="text-muted"><?php echo e($s['shift']); ?></small>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <i class="fa-solid fa-phone"></i> <?php echo e($s['mobile']); ?><br>
                                            <?php if(!empty($s['email'])): ?>
                                                <i class="fa-solid fa-envelope"></i> <?php echo e($s['email']); ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>₹ <?php echo number_format((float)$s['salary'], 2); ?></td>
                                    <td>
                                        <?php if($s['status'] === 'Active'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- <a href="staff_profile.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-id-card"></i>
                                        </a> -->

                                        <?php $staffJson = htmlspecialchars(json_encode($s), ENT_QUOTES, 'UTF-8'); ?>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary btn-edit-staff"
                                                data-staff="<?php echo $staffJson; ?>">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        <a href="staff.php?delete=<?php echo $s['id']; ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Delete this staff?');">
                                           <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ADD / EDIT MODAL -->
<div class="modal fade" id="staffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form class="modal-content" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_staff" id="form_action">
            <input type="hidden" name="id" id="staff_id">

            <div class="modal-header">
                <h5 class="modal-title" id="staffModalLabel">Add Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Mobile *</label>
                        <input type="text" name="mobile" id="mobile" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Alt. Mobile</label>
                        <input type="text" name="alt_mobile" id="alt_mobile" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gender</label>
                        <select name="gender" id="gender" class="form-select">
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" id="dob" class="form-control">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" id="address" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Role *</label>
                        <input type="text" name="role" id="role" class="form-control" placeholder="Warden / Security / Cleaner" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Shift</label>
                        <select name="shift" id="shift" class="form-select">
                            <option>Full Day</option>
                            <option>Morning</option>
                            <option>Evening</option>
                            <option>Night</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Monthly Salary</label>
                        <input type="number" step="0.01" name="salary" id="salary" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Joining Date</label>
                        <input type="date" name="joining_date" id="joining_date" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                        <div class="form-text">Leave blank to keep old password while editing.</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Photo</label>
                        <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Staff</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . "/../../includes/admin/admin_footer.php"; ?>
<script src="../../assets/js/admin_staff.js"></script>
</body>
</html>