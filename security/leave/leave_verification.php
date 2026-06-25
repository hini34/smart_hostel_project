<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    header("Location: ../../auth/securitys/security_login.php");
    exit();
}


$staff_name  = $_SESSION['security'] ?? "Security Guard";
$page_title  = "Leave Verification";
$active_page = "leave";

// optional security session check
// if (!isset($_SESSION['security'])) {
//     header("Location: /smart_hostel/auth/security/security_login.php");
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include(__DIR__ . "/../../includes/security/security_header.php"); ?>
    <link rel="stylesheet" href="../../assets/css/security/leave_verification.css">
</head>
<body>
<div class="security-layout">
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

    <?php include(__DIR__ . "/../../includes/security/security_sidebar.php"); ?>

    <main class="security-content">
        <div class="page-header">
            <div>
                <h1>Leave Verification (Gate Pass)</h1>
                <p>Check whether the student has admin-approved leave before allowing exit.</p>
            </div>
            <div class="live-badge">SECURITY CHECK</div>
        </div>

        <div class="search-card">
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Search by Student Enrollment</label>
                    <input type="text" id="enrollmentInput" class="form-control" placeholder="Enter student Enrollment">
                </div>
                <div class="col-md-4">
                    <button id="searchBtn" class="btn btn-primary w-100">Verify Leave Status</button>
                </div>
            </div>
        </div>

        <div id="resultArea" class="result-area">
            <div class="empty-state">
                <h4>No Student Selected</h4>
                <p>Enter student Enrollmnet to verify leave approval status.</p>
            </div>
        </div>
    </main>
</div>

<?php include(__DIR__ . "/../../includes/security/security_footer.php"); ?>
<script src="../../assets/js/security/leave_verification.js"></script>
</body>
</html>