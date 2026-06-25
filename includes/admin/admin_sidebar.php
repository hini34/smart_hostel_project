<?php $active_page = $active_page ?? ''; ?>



<!-- Desktop Sidebar -->
<div class="text-white sidebar-desktop d-none d-lg-block">
    <div class="brand-box">
        <div class="icon-wrap">
            <i class="fa-solid fa-building-user"></i>
        </div>
        <h5>Admin Panel</h5>
        <p>Smart Hostel Management</p>
    </div>

    <a href="../../admin/dashboard/admin_dashboard.php" class="nav-link <?php echo ($active_page === 'dashboard') ? 'active' : ''; ?>">
        <i class="fa-solid fa-gauge-high me-2"></i>Dashboard
    </a>

    <a href="../../admin/student/student.php" class="nav-link <?php echo ($active_page === 'students') ? 'active' : ''; ?>">
        <i class="fa-solid fa-user-graduate me-2"></i>Students
    </a>

    <a href="../../admin/room/rooms.php" class="nav-link <?php echo ($active_page === 'rooms') ? 'active' : ''; ?>">
        <i class="fa-solid fa-bed me-2"></i>Rooms
    </a>

    <!-- <a href="/smart_hostel/admin/fees/fees.php" class="nav-link <?php echo ($active_page === 'fees') ? 'active' : ''; ?>">
        <i class="fa-solid fa-money-bill me-2"></i>Fees
    </a> -->

    <a href="../../admin/complaints/complaints.php" class="nav-link <?php echo ($active_page === 'complaints') ? 'active' : ''; ?>">
        <i class="fa-solid fa-screwdriver-wrench me-2"></i>Complaints
    </a>

    <a href="../../admin/staff/staff.php" class="nav-link <?php echo ($active_page === 'staff') ? 'active' : ''; ?>">
        <i class="fa-solid fa-user-shield me-2"></i>Staff
    </a>

    <a href="../../admin/visitors/visitors.php" class="nav-link <?php echo ($active_page === 'visitors') ? 'active' : ''; ?>">
        <i class="fa-solid fa-users me-2"></i>Visitors
    </a>

    <a href="../../admin/leave/manage_leaves.php" class="nav-link <?php echo ($active_page === 'leave') ? 'active' : ''; ?>">
        <i class="fa-solid fa-person-walking-luggage me-2"></i>Students Leave
    </a>

    <a href="../../admin/notices/notices.php" class="nav-link <?php echo ($active_page === 'notices') ? 'active' : ''; ?>">
        <i class="fa-solid fa-bullhorn me-2"></i>Notices
    </a>

    <a href="../../admin/settings/settings.php" class="nav-link <?php echo ($active_page === 'settings') ? 'active' : ''; ?>">
        <i class="fa-solid fa-gear me-2"></i>Settings
    </a>

    <a href="../../admin/request/pending_students.php" class="nav-link <?php echo ($active_page === 'Request') ? 'active' : ''; ?>">
        <i class="fa-solid fa-user-clock me-2"></i>Student Requests
    </a>

    <a href="../../admin/logout/logout.php" class="nav-link text-danger mt-3">
        <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
    </a>
</div>

<!-- Mobile Offcanvas -->
<div class="offcanvas offcanvas-start bg-sidebar text-white" id="adminSidebar">
    <div class="offcanvas-header">
        <h5><i class="fa-solid fa-building-user me-2"></i>Admin Panel</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
        <a href="../../admin/dashboard/admin_dashboard.php" class="nav-link <?php echo ($active_page === 'dashboard') ? 'active' : ''; ?>">
            <i class="fa-solid fa-gauge-high me-2"></i>Dashboard
        </a>

        <a href="../../admin/student/student.php" class="nav-link <?php echo ($active_page === 'students') ? 'active' : ''; ?>">
            <i class="fa-solid fa-user-graduate me-2"></i>Students
        </a>

        <a href="../../admin/room/rooms.php" class="nav-link <?php echo ($active_page === 'rooms') ? 'active' : ''; ?>">
            <i class="fa-solid fa-bed me-2"></i>Rooms
        </a>

        <!-- <a href="/smart_hostel/admin/fees/fees.php" class="nav-link <?php echo ($active_page === 'fees') ? 'active' : ''; ?>">
            <i class="fa-solid fa-money-bill me-2"></i>Fees
        </a> -->

        <a href="../../admin/complaints/complaints.php" class="nav-link <?php echo ($active_page === 'complaints') ? 'active' : ''; ?>">
            <i class="fa-solid fa-screwdriver-wrench me-2"></i>Complaints
        </a>

        <a href="../../admin/staff/staff.php" class="nav-link <?php echo ($active_page === 'staff') ? 'active' : ''; ?>">
            <i class="fa-solid fa-user-shield me-2"></i>Staff
        </a>

        <a href="../../admin/visitors/visitors.php" class="nav-link <?php echo ($active_page === 'visitors') ? 'active' : ''; ?>">
            <i class="fa-solid fa-users me-2"></i>Visitors
        </a>

        <a href="../../admin/leave/manage_leaves.php" class="nav-link <?php echo ($active_page === 'leave') ? 'active' : ''; ?>">
            <i class="fa-solid fa-person-walking-luggage me-2"></i>Students Leave
        </a>

        <a href="../../admin/notices/notices.php" class="nav-link <?php echo ($active_page === 'notices') ? 'active' : ''; ?>">
            <i class="fa-solid fa-bullhorn me-2"></i>Notices
        </a>

        <a href="../../admin/settings/settings.php" class="nav-link <?php echo ($active_page === 'settings') ? 'active' : ''; ?>">
            <i class="fa-solid fa-gear me-2"></i>Settings
        </a>


        <a href="../../admin/request/pending_students.php" class="nav-link <?php echo ($active_page === 'Request') ? 'active' : ''; ?>">
            <i class="fa-solid fa-user-clock me-2"></i></i>Student Requests
        </a>
    
        <a href="../../admin/logout/logout.php" class="nav-link text-danger mt-3">
            <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
        </a>
    </div>
</div>