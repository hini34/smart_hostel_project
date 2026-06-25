<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

// Optional login check
// if (!isset($_SESSION['security'])) {
//     header("Location: /smart_hostel/security/security_login.php");
//     exit;
// }
$staff_name  = $_SESSION['security'] ?? "Security Guard";
$page_title  = "Security Dashboard";
$active_page = "attendence";
$security_name = $_SESSION['security_name'] ?? $_SESSION['security'] ?? 'Security Staff';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . "/../../includes/security/security_header.php"; ?>
    <link rel="stylesheet" href="../../assets/css/security_sidebar.css">
    <link rel="stylesheet" href="../../assets/css/security/security_attendance.css">
</head>
<body>

<?php include __DIR__ . "/../../includes/security/security_sidebar.php"; ?>

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

<div class="main-content">
    <div class="attendance-page">

        <div class="attendance-header-card">
            <div>
                <h1><i class="fa-solid fa-user-check me-2"></i>Student Attendance Management</h1>
                <p class="mb-0">Welcome, <?php echo htmlspecialchars($security_name); ?>. Mark and manage today’s student attendance.</p>
            </div>
            <div class="header-time-box">
                <span class="small-label">Last Updated</span>
                <strong id="lastUpdated">--</strong>
            </div>
        </div>

        <div class="row g-4 stats-row">
            <div class="col-md-4">
                <div class="stat-card total-card">
                    <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                    <div>
                        <h6>Total Students</h6>
                        <h2 id="totalStudents">0</h2>
                        <p>Registered hostel students</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card present-card">
                    <div class="stat-icon"><i class="fa-solid fa-user-check"></i></div>
                    <div>
                        <h6>Present</h6>
                        <h2 id="presentStudents">0</h2>
                        <p>Marked present today</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card absent-card">
                    <div class="stat-icon"><i class="fa-solid fa-user-xmark"></i></div>
                    <div>
                        <h6>Absent</h6>
                        <h2 id="absentStudents">0</h2>
                        <p>Not present today</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="attendance-panel card shadow-sm border-0">
            <div class="card-body">
                <div class="panel-top">
                    <div>
                        <h4 class="panel-title mb-1">Today Attendance List</h4>
                        <p class="text-muted mb-0">Search student and mark attendance in real-time.</p>
                    </div>

                    <div class="panel-actions">
                        <div class="search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="searchInput" class="form-control" placeholder="Search by name or enrollment">
                        </div>

                        <button type="button" class="btn btn-success" id="markAllPresentBtn">
                            <i class="fa-solid fa-check-double me-2"></i>Mark All Present
                        </button>
                    </div>
                </div>

                <div id="alertBox" class="mt-3"></div>

                <div class="table-responsive mt-4">
                    <table class="table align-middle attendance-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">#</th>
                                <th style="width: 90px;">Photo</th>
                                <th>Student Name</th>
                                <th>Enrollment</th>
                                <th>Room No</th>
                                <th>Status</th>
                                <th style="width: 230px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                            <tr>
                                <td colspan="7" class="text-center py-4">Loading attendance data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/security/security_attendance.js"></script>
</body>
</html>