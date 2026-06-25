<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

// Login check (jab ready ho uncomment kar dena)
// if (!isset($_SESSION['student_id'])) {
//     header("Location: stuLogin.php");
//     exit;
// }

$page_title   = "Student Dashboard";
$active_page  = "dashboard";
$student_name = $_SESSION['student_name'] ?? $_SESSION['student'] ?? "Student";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../../includes/student/student_header.php'; ?>
    <link rel="stylesheet" href="../../assets/css/student/student_sidebar.css">
    <link rel="stylesheet" href="../../assets/css/student/student_dashboard.css">
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

<div class="dashboard-wrapper">

    <?php include __DIR__ . '/../../includes/student/student_sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- PAGE HEADER -->
        <div class="page-header">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <h1 class="page-title">Dashboard Overview</h1>
                    <p class="page-subtitle mb-0">
                        Welcome <?php echo htmlspecialchars($student_name); ?> • Monitor your activity and notices.
                    </p>
                </div>
                <div class="col-md-4">
                    <div class="status-box">
                        <span class="badge text-bg-success live-badge">LIVE</span>
                        <div class="last-updated" id="lastUpdated">Last updated: --</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOP STATS ROW -->
        <div class="row g-3">

            <!-- My Room -->
            <div class="col-md-3 col-6">
                <div class="dashboard-card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="stat-label">My Room</span>
                            <i class="fa-solid fa-bed text-primary"></i>
                        </div>
                        <div class="stat-value" id="stat-room">--</div>
                        <p class="small-muted mb-0" id="stat-bed">Floor: --</p>
                    </div>
                </div>
            </div>

            <!-- Fees -->
            <!-- <div class="col-md-3 col-6">
                <div class="dashboard-card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="stat-label">Pending Fees</span>
                            <i class="fa-solid fa-indian-rupee-sign text-success"></i>
                        </div>
                        <div class="stat-value" id="stat-fees">₹ --</div>
                        <p class="small-muted mb-0" id="stat-fees-note">Last payment: --</p>
                    </div>
                </div>
            </div> -->

            <!-- Complaints -->
            <div class="col-md-3 col-6">
                <div class="dashboard-card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="stat-label">Complaints</span>
                            <i class="fa-solid fa-screwdriver-wrench text-warning"></i>
                        </div>
                        <div class="stat-value" id="stat-complaints-pending"></div>
                        <p class="small-muted mb-0" id="stat-complaints-total">Total: </p>
                    </div>
                </div>
            </div>

            <!-- Leave Status -->
            <div class="col-md-3 col-6">
                <div class="dashboard-card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="stat-label">Leave Status</span>
                            <i class="fa-solid fa-person-walking-luggage text-info"></i>
                        </div>
                        <div class="stat-value" id="stat-leave-status">--</div>
                        <p class="small-muted mb-0" id="stat-leave-dates">--</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- SECOND ROW -->
        <div class="row g-3 mt-1">
            <div class="col-lg-8">
                <div class="dashboard-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">
                                <i class="fa-solid fa-bullhorn me-1 text-primary"></i> Latest Notice
                            </h6>
                            <a href="/smart_hostel/student/notices/notice_board.php" class="small text-decoration-none">View all</a>
                        </div>
                        <h6 id="notice-title">No recent notice</h6>
                        <p class="small-muted mb-1" id="notice-date"></p>
                        <p class="mb-0 small" id="notice-text">
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-card mb-3">
                    <div class="card-body">
                        <h6 class="mb-2">
                            <i class="fa-solid fa-link me-1 text-primary"></i> Quick Actions
                        </h6>
                        <div class="d-grid gap-2">
                            <a href="/smart_hostel/student/leave/student_leave.php" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-person-walking-luggage me-1"></i> Apply Leave
                            </a>
                            <a href="/smart_hostel/student/complaints/student_complaints.php" class="btn btn-sm btn-outline-warning">
                                <i class="fa-solid fa-screwdriver-wrench me-1"></i> Register Complaint
                            </a>
                            <!-- <a href="/smart_hostel/student/student_fees.php" class="btn btn-sm btn-outline-success">
                                <i class="fa-solid fa-money-bill me-1"></i> Pay Fees / View History
                            </a> -->
                        </div>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="card-body">
                        <h6 class="mb-2">
                            <i class="fa-solid fa-circle-info me-1 text-secondary"></i> Hostel Info
                        </h6>
                        <p class="small-muted mb-0">
                            If you need help with any emergency or issue, contact the warden or the hostel office.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php include __DIR__ . '/../../includes/student/student_footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="../../assets/js/student/student_dashboard.js"></script>
</body>
</html>