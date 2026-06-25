<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    header("Location: ../../auth/securitys/security_login.php");
    exit();
}


$staff_name  = $_SESSION['security'] ?? "Security Guard";
$page_title  = "Visitor Entry";
$active_page = "visitors";

// optional security session check
// if (!isset($_SESSION['security'])) {
//     header("Location: /smart_hostel/auth/security/security_login.php");
//     exit();
// }

function e($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

$students = [];


$sql = "SELECT
            s.id,
            s.fullname,
            s.enrollment,
            s.department,
            r.room_no
        FROM students s
        LEFT JOIN rooms r ON s.room_id = r.id
        WHERE s.hostel_name = ?
        ORDER BY s.fullname ASC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {

    error_log("Visitor Entry Query Error: " . mysqli_error($conn));

    $_SESSION['flash_message'] = "Something went wrong. Please contact administrator.";
    $_SESSION['flash_type'] = "danger";

    header("Location: visitor_entry.php");
    exit();
}

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $hostel_name
);



if (!mysqli_stmt_execute($stmt)) {

    error_log("Execute Error: " . mysqli_stmt_error($stmt));

    $_SESSION['flash_message'] = "Unable to load students.";
    $_SESSION['flash_type'] = "danger";

    $result = false;

} else {

    $result = mysqli_stmt_get_result($stmt);
}

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
}

mysqli_stmt_close($stmt);

$flash_message = $_SESSION['flash_message'] ?? '';
$flash_type    = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include(__DIR__ . "/../../includes/security/security_header.php"); ?>
    <link rel="stylesheet" href="../../assets/css/security/visitor_entry.css">
</head>
<body>
<div class="security-layout">

    <nav class="navbar navbar-dark mobile-topbar d-lg-none fixed-top">
        <div class="container-fluid">
            <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#securitySidebar">
                <i class="fa-solid fa-bars"></i>
            </button>

            <a class="navbar-brand fw-bold" href="#">
                <i class="fa-solid fa-shield-halved me-2"></i>Security Panel
            </a>

            <span class="user-badge"><?php echo htmlspecialchars($staff_name); ?></span>
        </div>
    </nav>
    <?php include(__DIR__ . "/../../includes/security/security_sidebar.php"); ?>

    <main class="security-content">
        <div class="page-header">
            <div>
                <h1>Visitor Management System</h1>
                <p>Security guard visitor entry karega, then admin approve ya reject karega.</p>
            </div>
            <div class="live-badge">VISITOR ENTRY</div>
        </div>

        <?php if ($flash_message): ?>
            <div class="alert alert-<?php echo e($flash_type); ?> alert-dismissible fade show" role="alert">
                <?php echo e($flash_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="visitor-grid">
            <div class="entry-card">
                <div class="card-title">Visitor Entry Form</div>

                <form action="visitor_entry_process.php" method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Visitor Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" name="phone" class="form-control" maxlength="15" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Relation</label>
                        <input type="text" name="relation" class="form-control" placeholder="Father / Mother / Friend / Relative">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Purpose</label>
                        <input type="text" name="purpose" class="form-control" placeholder="Meeting / Document / Personal">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Visitor address"></textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Student Name / Enrollment / Room</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">Select Student</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?php echo (int)$student['id']; ?>">
                                    <?php echo e($student['fullname']); ?>
                                    (<?php echo e($student['enrollment']); ?>)
                                    - Room: <?php echo e($student['room_no'] ?? 'N/A'); ?>
                                    - <?php echo e($student['department'] ?? 'N/A'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Entry Time</label>
                        <input type="text" class="form-control" value="<?php echo date('d M Y h:i A'); ?>" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Initial Status</label>
                        <input type="text" class="form-control" value="Pending" readonly>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100">Save Visitor Entry</button>
                    </div>
                </form>
            </div>

            <div class="info-card">
                <div class="card-title">Workflow</div>
                <div class="workflow-box">
                    <div class="workflow-step">
                        <span class="step-number">1</span>
                        <div>
                            <strong>Security Entry</strong>
                            <p>Visitor details fill hongi aur record save hoga.</p>
                        </div>
                    </div>

                    <div class="workflow-step">
                        <span class="step-number">2</span>
                        <div>
                            <strong>Admin Approval</strong>
                            <p>Admin pending entry ko approve ya reject karega.</p>
                        </div>
                    </div>

                    <div class="workflow-step">
                        <span class="step-number">3</span>
                        <div>
                            <strong>Exit Update</strong>
                            <p>Visitor ke jaane par checkout time update hoga.</p>
                        </div>
                    </div>
                </div>

                <a href="visitor_history.php" class="btn btn-outline-dark w-100 mt-3">View Visitor History</a>
            </div>
        </div>
    </main>
</div>

<?php include(__DIR__ . "/../../includes/security/security_footer.php"); ?>
</body>
</html>