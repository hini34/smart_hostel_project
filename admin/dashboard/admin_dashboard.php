<?php
session_start();

// Optional login check
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$admin_name = $_SESSION['admin_name'] ?? "Admin";
$page_title = "Admin Dashboard";
$active_page = "dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../../includes/admin/admin_header.php'; ?>
    <link rel="stylesheet" href="../../assets/css/admin_dashboard.css">
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

    <?php include __DIR__ . '/../../includes/admin/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="page-title">Dashboard Overview</h1>
                    <p class="page-subtitle">
                        Welcome <?php echo htmlspecialchars($admin_name); ?> • Monitor hostel activity, attendance, complaints, and daily updates in real time.
                    </p>
                </div>
                <div class="col-md-4">
                    <div class="status-box">
                        <span class="badge text-bg-success live-badge" id="liveStatus">LIVE</span>
                        <div class="last-updated" id="lastUpdated"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="row g-4 mt-1">
            <div class="col-sm-6 col-xl-3">
                <div class="dashboard-card">
                    <div class="card-top">
                        <h6 class="card-title">Total Students</h6>
                        <div class="card-icon"><i class="fa-solid fa-user-graduate"></i></div>
                    </div>
                    <h2 class="card-value" id="totalStudents">0</h2>
                    <div class="card-text">Registered students in hostel system</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="dashboard-card">
                    <div class="card-top">
                        <h6 class="card-title">Total Rooms</h6>
                        <div class="card-icon"><i class="fa-solid fa-bed"></i></div>
                    </div>
                    <h2 class="card-value" id="totalRooms">0</h2>
                    <div class="card-text">Rooms available in the hostel</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="dashboard-card">
                    <div class="card-top">
                        <h6 class="card-title">Available Beds</h6>
                        <div class="card-icon"><i class="fa-solid fa-door-open"></i></div>
                    </div>
                    <h2 class="card-value" id="availableRooms">0</h2>
                    <div class="card-text">Beds currently available for allocation</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="dashboard-card">
                    <div class="card-top">
                        <h6 class="card-title">Students on Leave</h6>
                        <div class="card-icon"><i class="fa-solid fa-person-walking-luggage"></i></div>
                    </div>
                    <h2 class="card-value" id="leaveStudents">0</h2>
                    <div class="card-text">Approved leave requests for today</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-top">
                        <h6 class="card-title">Attendance Today</h6>
                        <div class="card-icon"><i class="fa-solid fa-clipboard-check"></i></div>
                    </div>
                    <h2 class="card-value" id="attendanceToday">0</h2>
                    <div class="card-text">Total students marked present today</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-top">
                        <h6 class="card-title">Complaints Pending</h6>
                        <div class="card-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    </div>
                    <h2 class="card-value" id="pendingComplaints">0</h2>
                    <div class="card-text">Complaints currently waiting for action</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-top">
                        <h6 class="card-title">Complaints Completed</h6>
                        <div class="card-icon"><i class="fa-solid fa-circle-check"></i></div>
                    </div>
                    <h2 class="card-value" id="completedComplaints">0</h2>
                    <div class="card-text">Successfully resolved complaints</div>
                </div>
            </div>
        </div>

        <!-- Chart + Activity -->
        <div class="row g-4 mt-1">
            <div class="col-lg-8">
                <div class="section-card">
                    <h5 class="section-title">Attendance Analytics</h5>
                    <div class="section-subtitle">Present students in the last 7 days</div>
                    <canvas id="attendanceChart" height="120"></canvas>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="section-card">
                    <h5 class="section-title">Today Activity</h5>
                    <div class="section-subtitle">Quick summary of today’s hostel operations</div>

                    <ul class="activity-list">
                        <li><div>📝 New Registrations</div><span id="todayRegs">0</span></li>
                        <li><div>✅ Attendance Marked</div><span id="todayAttendance">0</span></li>
                        <li><div>📩 Complaints Received</div><span id="todayComplaints">0</span></li>
                        <li><div>🛠 Complaints Resolved</div><span id="todayResolved">0</span></li>
                        <li><div>🏠 Leave Requests</div><span id="todayLeaves">0</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../../includes/admin/admin_footer.php'; ?>
</body>
<script src="../../assets/js/admin_dashboard.js"></script>
</html>