<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    header("Location: ../../auth/securitys/security_login.php");
    exit();
}

// if (!isset($_SESSION['security_id'])) {
//     header("Location: ../security_login.php");
//     exit;
// }

$staff_name  = $_SESSION['security'] ?? "Security Guard";
$page_title  = "Incidents";
$active_page = "incident list";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

if (!isset($_SESSION['security_id'])) {
    header("Location: ../../auth/securitys/security_login.php");
    exit();
}

$staff_id = (int)$_SESSION['security_id'];

$sql = "SELECT *
        FROM incidents
        WHERE reported_by = ?
        AND hostel_name = ?
        ORDER BY id DESC";


$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {

    error_log("Incident List Query Error: " . mysqli_error($conn));

    $result = false;

} else {

    mysqli_stmt_bind_param(
        $stmt,
        "is",
        $staff_id,
        $hostel_name
    );

    if (!mysqli_stmt_execute($stmt)) {

    error_log("Incident Execute Error: " . mysqli_stmt_error($stmt));

    $result = false;

} else {

    $result = mysqli_stmt_get_result($stmt);
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../../includes/security/security_header.php'; ?>
    <link rel="stylesheet" href="../../assets/css/security/security_sidebar.css">
    <link rel="stylesheet" href="../../assets/css/security/incident_list.css">
</head>

<body class="incident-page">

<!-- Mobile Topbar -->
<nav class="mobile-topbar d-lg-none fixed-top">
    <div class="topbar-wrap">

        <!-- Left -->
        <div class="topbar-left">
            <button class="menu-btn" data-bs-toggle="offcanvas" data-bs-target="#securitySidebar">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <!-- Center -->
        <div class="topbar-center">
            <i class="fa-solid fa-shield-halved me-1"></i>
            <span>Security Panel</span>
        </div>

        <!-- Right -->
        <div class="topbar-right">
            <span class="user-badge">
                <?php echo htmlspecialchars($staff_name); ?>
            </span>
        </div>

    </div>
</nav>

<?php include __DIR__ . '/../../includes/security/security_sidebar.php'; ?>

<main class="main-content">
    <div class="incident-content-wrap">

        <div class="incident-header-card">
            <div>
                <h1><i class="fa-solid fa-list me-2"></i>My Reported Incidents</h1>
                <p>Yahan security guard ke saare reported incidents dikhenge.</p>
            </div>

            <div class="header-action-group">
                <a href="report_incident.php" class="btn header-outline-btn">
                    <i class="fa-solid fa-plus me-2"></i>Report New Incident
                </a>
            </div>
        </div>

        <div class="card incident-form-card shadow-sm">
            <div class="card-body">
                <div class="section-title-wrap">
                    <h4 class="section-title">Incident Records</h4>
                    <p class="section-subtitle mb-0">
                        Aapke dwara report kiye gaye sabhi incidents yahan available hain.
                    </p>
                </div>

                <div class="table-responsive">
                    <table class="table incident-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 70px;">#</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Severity</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                <?php $i = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td>
                                            <div class="fw-semibold text-dark">
                                                <?php echo e($row['title']); ?>
                                            </div>
                                            <?php if (!empty($row['description'])): ?>
                                                <small class="text-muted">
                                                    <?php echo e(mb_strimwidth($row['description'], 0, 70, '...')); ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($row['incident_type']); ?></td>
                                        <td><?php echo e($row['location']); ?></td>
                                        <td>
                                            <?php
                                            $severity = $row['severity'] ?? 'Low';
                                            $severityClass = 'severity-low';
                                            if ($severity === 'High') {
                                                $severityClass = 'severity-high';
                                            } elseif ($severity === 'Medium') {
                                                $severityClass = 'severity-medium';
                                            }
                                            ?>
                                            <span class="custom-badge <?php echo $severityClass; ?>">
                                                <?php echo e($severity); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $status = $row['status'] ?? 'Pending';
                                            $statusClass = 'status-pending';
                                            if ($status === 'Resolved') {
                                                $statusClass = 'status-resolved';
                                            } elseif ($status === 'In Review') {
                                                $statusClass = 'status-review';
                                            } elseif ($status === 'Rejected') {
                                                $statusClass = 'status-rejected';
                                            }
                                            ?>
                                            <span class="custom-badge <?php echo $statusClass; ?>">
                                                <?php echo e($status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            echo !empty($row['reported_at'])
                                                ? date("d M Y, h:i A", strtotime($row['reported_at']))
                                                : '-';
                                            ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <i class="fa-solid fa-circle-info me-2"></i>
                                            No incidents found.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</main>

<?php include __DIR__ . '/../../includes/security/security_footer.php'; ?>

<?php
if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
?>

</body>
</html>