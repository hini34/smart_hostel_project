<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: /smart_hostl/auth/students/student_login.php");
    exit;
}

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    die("Hostel not found.");
}

$page_title   = "Notice Board  ";
$active_page  = "notices";
$student_name = $_SESSION['student_name'] ?? $_SESSION['student'] ?? "Student";

$today = date('Y-m-d');

$sql = "SELECT *
        FROM notices
        WHERE hostel_name = ?
          AND audience IN ('student','all')
          AND start_date <= ?
          AND (
                end_date IS NULL
                OR end_date = ''
                OR end_date >= ?
              )
        ORDER BY is_pinned DESC, created_at DESC, id DESC";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    error_log("Query prepare failed: " . mysqli_error($conn));
    die("Something went wrong. Please try again later.");
}

mysqli_stmt_bind_param(
    $stmt,
    "sss",
    $hostel_name,
    $today,
    $today
);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../../includes/student/student_header.php'; ?>
    <link rel="stylesheet" href="../../assets/css/student/student_sidebar.css">
    <link rel="stylesheet" href="../../assets/css/student/student_notices.css">
</head>
<body>

<!-- MOBILE TOP NAVBAR -->
<nav class="navbar navbar-dark navbar-main d-md-none fixed-top">
    <div class="container-fluid">
        <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#studentSidebar" aria-controls="studentSidebar">
            <i class="fa-solid fa-bars"></i>
        </button>

        <a class="navbar-brand d-flex align-items-center" href="#">
            <i class="fa-solid fa-building-user me-2"></i>
            <span>Smart Hostel - Student Panel</span>
        </a>

        <span class="badge bg-primary rounded-pill">
            <?php echo htmlspecialchars($student_name); ?>
        </span>
    </div>
</nav>

<?php include __DIR__ . '/../../includes/student/student_sidebar.php'; ?>

<!-- MAIN CONTENT -->
<div class="main-content">
<div class="container-fluid">

    <div class="dashboard-header">
        <div>
            <h1>
                <i class="fa-solid fa-bullhorn me-2"></i>
                Notice Board
            </h1>

            <p>
                Welcome <?php echo htmlspecialchars($student_name); ?> • View hostel announcements and updates.
            </p>
        </div>

        <div class="live-box">
            <span class="live-badge">LIVE</span>

            <div class="update-time">
                Last updated:
                <?php echo date("g:i:s a"); ?>
            </div>
        </div>
    </div>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>

        <div class="row g-4">

            <?php while($row = mysqli_fetch_assoc($result)): ?>

                <div class="col-12">

                    <div class="notice-card">

                        <div class="notice-top">

                            <div>

                                <?php if((int)$row['is_pinned'] === 1): ?>
                                    <span class="important-badge">
                                        <i class="fa-solid fa-thumbtack me-1"></i>
                                        Important Notice
                                    </span>
                                <?php endif; ?>

                                <h4>
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </h4>

                            </div>

                            <div class="notice-date">
                                <?php echo date("d M Y", strtotime($row['created_at'])); ?>
                            </div>

                        </div>

                        <div class="notice-body">
                            <?php echo nl2br(htmlspecialchars($row['body'])); ?>
                        </div>

                        <div class="notice-footer">

                            <span class="audience-badge">
                                <?php echo ucfirst($row['audience']); ?>
                            </span>

                            <span>
                                Active :
                                <?php echo htmlspecialchars($row['start_date']); ?>
                                →
                                <?php echo htmlspecialchars($row['end_date']); ?>
                            </span>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="card p-4 border-0 shadow-sm rounded-4">
            <h5>No Notices Available</h5>
            <p class="text-danger mb-0">
                Currently there are no active notices for students.
            </p>
        </div>

    <?php endif; ?>

</div>
</div>
 <?php include __DIR__ . '/../../includes/student/student_footer.php'; ?>
</body>
</html>