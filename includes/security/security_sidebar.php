<?php
$current = $active_page ?? '';
?>

<!-- Desktop Sidebar -->
<aside class="sidebar d-none d-lg-flex">
    <div class="sidebar-inner w-100">
        <div class="brand-box">
            <div class="brand-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h4>Security Panel</h4>
            <p>Smart Hostel Management</p>
        </div>

        <nav class="nav flex-column menu-links">
            <a href="../../security/dashboard/security_dashboard.php" class="nav-link <?php echo ($current === 'dashboard') ? 'active' : ''; ?>">
                <i class="fa-solid fa-gauge-high me-2"></i>Dashboard
            </a>

            <a href="../../security/attendence/security_attendence.php" class="nav-link <?php echo ($current === 'attendance') ? 'active' : ''; ?>">
                <i class="fa-solid fa-user-check me-2"></i>Attendance
            </a>

            <a href="../../security/visitors/visitor_entry.php" class="nav-link <?php echo ($current === 'visitors') ? 'active' : ''; ?>">
                <i class="fa-solid fa-id-badge me-2"></i>Visitors
            </a>

            <a href="../../security/leave/leave_verification.php" class="nav-link <?php echo ($current === 'leave') ? 'active' : ''; ?>">
                <i class="fa-solid fa-person-walking-luggage me-2"></i>Leave Verification
            </a>

            <a href="../../security/incidents/incident_list.php" class="nav-link <?php echo ($current === 'incidents') ? 'active' : ''; ?>">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>Incidents
            </a>

            <a href="../../security/logout/logout.php" class="nav-link text-danger mt-3">
                <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
            </a>
        </nav>
    </div>
</aside>

<!-- Mobile Sidebar -->
<div class="offcanvas offcanvas-start sidebar-offcanvas text-white" tabindex="-1" id="securitySidebar">
    <div class="offcanvas-header border-bottom border-secondary-subtle">
        <h5 class="mb-0"><i class="fa-solid fa-shield-halved me-2"></i>Security Panel</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
        <nav class="nav flex-column menu-links">
            <a href="../../security/dashboard/security_dashboard.php" class="nav-link <?php echo ($current === 'dashboard') ? 'active' : ''; ?>">
                <i class="fa-solid fa-gauge-high me-2"></i>Dashboard
            </a>

            <a href="../../security/attendence/security_attendence.php" class="nav-link <?php echo ($current === 'attendance') ? 'active' : ''; ?>">
                <i class="fa-solid fa-user-check me-2"></i>Attendance
            </a>

            <a href="../../security/visitors/visitor_entry.php" class="nav-link <?php echo ($current === 'visitors') ? 'active' : ''; ?>">
                <i class="fa-solid fa-id-badge me-2"></i>Visitors
            </a>

            <a href="../../security/leave/leave_verification.php" class="nav-link <?php echo ($current === 'leave') ? 'active' : ''; ?>">
                <i class="fa-solid fa-person-walking-luggage me-2"></i>Leave Verification
            </a>

            <a href="../../security/incidents/incident_list.php" class="nav-link <?php echo ($current === 'incidents') ? 'active' : ''; ?>">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>Incidents
            </a>

            <a href="../../security/logout/logout.php" class="nav-link text-danger mt-3">
                <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
            </a>
        </nav>
    </div>
</div>