<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    header("Location: ../../auth/security/security_login.php");
    exit();
}

if (!isset($_SESSION['security_id'])) {
    header("Location: /smart_hostel/auth/security/security_login.php");
    exit;
}

$staff_id   = (int) $_SESSION['security_id'];
$staff_name = $_SESSION['security_name'] ?? $_SESSION['security'] ?? "Security Guard";

$page_title  = "Incident Report";
$active_page = "incidents";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../../includes/security/security_header.php'; ?>
    <link rel="stylesheet" href="../../assets/css/security/security_sidebar.css">
    <link rel="stylesheet" href="../../assets/css/security/incident.css">
</head>

<body class="incident-page">

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

<main class="main-content incident-main">
    <div class="incident-wrapper">

        <?php if (isset($_GET['error']) && $_GET['error'] !== ''): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Incident reported successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="incident-header-card">
            <div>
                <h1><i class="fa-solid fa-triangle-exclamation me-2"></i>Report Incident</h1>
                <p>Guard yahan hostel ke important incidents report kar sakta hai. Report directly admin panel me visible hogi.</p>
            </div>

            <div class="header-action-group">
                <a href="incident_list.php" class="btn header-outline-btn">
                    <i class="fa-solid fa-list me-2"></i>View Incident List
                </a>
            </div>

            <div class="incident-header-badge">
                <i class="fa-solid fa-bell me-2"></i>Admin Notified
            </div>
        </div>

        <div class="incident-form-card">
            <div class="incident-form-top">
                <h3>Incident Details</h3>
                <p>Fight, noise, theft, suspicious activity ya damage jaisi incidents yahan submit karein.</p>
            </div>

            <form action="report_incident_process.php" method="POST" class="incident-form">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Incident Title</label>
                        <input type="text" id="title" name="title" class="form-control" placeholder="Enter incident title" required>
                    </div>

                    <div class="col-md-6">
                        <label for="incident_type" class="form-label">Incident Type</label>
                        <select id="incident_type" name="incident_type" class="form-select" required>
                            <option value="">Select incident type</option>
                            <option value="Fight">Fight</option>
                            <option value="Noise">Noise</option>
                            <option value="Damage">Damage</option>
                            <option value="Theft">Theft</option>
                            <option value="Suspicious">Suspicious</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="5" placeholder="Write complete incident details here..." required></textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" id="location" name="location" class="form-control" placeholder="Enter incident location" required>
                    </div>

                    <div class="col-md-6">
                        <label for="severity" class="form-label">Severity</label>
                        <select id="severity" name="severity" class="form-select" required>
                            <option value="">Select severity</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                </div>

                <div class="incident-info-box mt-4">
                    <h5><i class="fa-solid fa-circle-info me-2"></i>Examples of incidents</h5>
                    <ul>
                        <li>Fight / Noise issue</li>
                        <li>Damage / Theft case</li>
                        <li>Suspicious person or activity</li>
                    </ul>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn incident-submit-btn w-100">
                        <i class="fa-solid fa-paper-plane me-2"></i>Submit Incident
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../../includes/security/security_footer.php'; ?>
</body>
</html>