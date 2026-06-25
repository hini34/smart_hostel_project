<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");


$staff_name  = $_SESSION['security'] ?? "Security Guard";
$page_title  = "Security Dashboard";
$active_page = "dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../../includes/security/security_header.php'; ?>
    <link rel="stylesheet" href="../../assets/css/security/security_dashboard.css">
</head>
<body>

<!-- Mobile Topbar -->
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

<?php include __DIR__ . '/../../includes/security/security_sidebar.php'; ?>

<main class="main-content">
    <div class="container-fluid">

        <div class="page-header-card">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h2><i class="fa-solid fa-shield-halved me-2"></i>Security Dashboard</h2>
                    <p class="mb-0">Welcome, <?php echo htmlspecialchars($staff_name); ?>. Live hostel summary is shown below.</p>
                </div>

                <div class="last-updated-box">
                    <span class="small-label">Last Updated</span>
                    <strong id="lastUpdated">--</strong>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="dashboard-card card-total">
                    <div class="card-icon"><i class="fa-solid fa-users"></i></div>
                    <div class="card-content">
                        <span class="card-label">Total Students</span>
                        <h3 id="totalStudents">0</h3>
                        <small>Total hostel students</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-4">
                <div class="dashboard-card card-present">
                    <div class="card-icon"><i class="fa-solid fa-user-check"></i></div>
                    <div class="card-content">
                        <span class="card-label">Present Students</span>
                        <h3 id="presentStudents">0</h3>
                        <small>Marked present today</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-4">
                <div class="dashboard-card card-absent">
                    <div class="card-icon"><i class="fa-solid fa-user-xmark"></i></div>
                    <div class="card-content">
                        <span class="card-label">Absent Students</span>
                        <h3 id="absentStudents">0</h3>
                        <small>Not present today</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-4">
                <div class="dashboard-card card-visitors">
                    <div class="card-icon"><i class="fa-solid fa-id-badge"></i></div>
                    <div class="card-content">
                        <span class="card-label">Visitors Today</span>
                        <h3 id="visitorsToday">0</h3>
                        <small>Total today visitor entries</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-4">
                <div class="dashboard-card card-pending">
                    <div class="card-icon"><i class="fa-solid fa-clock"></i></div>
                    <div class="card-content">
                        <span class="card-label">Pending Approvals</span>
                        <h3 id="pendingApprovals">0</h3>
                        <small>Pending visitor requests</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-4">
                <div class="dashboard-card card-leaves">
                    <div class="card-icon"><i class="fa-solid fa-person-walking-luggage"></i></div>
                    <div class="card-content">
                        <span class="card-label">Active Leaves</span>
                        <h3 id="activeLeaves">0</h3>
                        <small>Approved leaves active now</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-12 col-lg-6">
                <div class="info-panel">
                    <div class="panel-header">
                        <h5><i class="fa-solid fa-circle-info me-2"></i>System Status</h5>
                    </div>
                    <div class="panel-body">
                        <div class="status-line">
                            <span>Refresh Interval</span>
                            <strong>5 Seconds</strong>
                        </div>
                        <div class="status-line">
                            <span>Server Status</span>
                            <strong id="serverStatus">Waiting...</strong>
                        </div>
                        <div class="status-line">
                            <span>Current Date</span>
                            <strong><?php echo date("d M Y"); ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="info-panel">
                    <div class="panel-header">
                        <h5><i class="fa-solid fa-bolt me-2"></i>Quick Notes</h5>
                    </div>
                    <div class="panel-body">
                        <ul class="quick-notes mb-0">
                            <li>Attendance summary refreshes automatically.</li>
                            <li>Visitor count updates after new entries.</li>
                            <li>Pending approvals show visitor requests awaiting action.</li>
                            <li>Active leaves count only approved current leaves.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<?php include __DIR__ . '/../../includes/security/security_footer.php'; ?>
<script src="../../assets/js/security/security_dashboard.js"></script>
</body>
</html>