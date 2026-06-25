<?php $active_page = $active_page ?? ''; ?>

<!-- SIDEBAR DESKTOP -->
<div class="bg-sidebar text-white sidebar-desktop">
    <div class="brand-box">
        <div class="icon-wrap">
            <i class="fa-solid fa-user-graduate"></i>
        </div>
        <h5>Student Panel</h5>
        <p>Smart Hostel Management</p>
    </div>

    <a href="../../student/dashboard/student_dashboard.php" class="nav-link <?php echo ($active_page === 'dashboard') ? 'active' : ''; ?>">
        <i class="fa-solid fa-gauge-high me-2"></i>Dashboard
    </a>

    <a href="../../student/profile/student_profile.php" class="nav-link <?php echo ($active_page === 'profile') ? 'active' : ''; ?>">
        <i class="fa-solid fa-id-card me-2"></i>My Profile
    </a>

    <a href="../../student/rooms/student_room.php" class="nav-link <?php echo ($active_page === 'room') ? 'active' : ''; ?>">
        <i class="fa-solid fa-bed me-2"></i>My Room
    </a>

    <a href="../../student/leave/student_leave.php" class="nav-link <?php echo ($active_page === 'leave') ? 'active' : ''; ?>">
        <i class="fa-solid fa-person-walking-luggage me-2"></i>Leave Requests
    </a>

    <a href="../../student/complaints/student_complaints.php" class="nav-link <?php echo ($active_page === 'complaints') ? 'active' : ''; ?>">
        <i class="fa-solid fa-screwdriver-wrench me-2"></i>Complaints
    </a>

    <a href="../../student/notices/notice_board.php" class="nav-link <?php echo ($active_page === 'notices') ? 'active' : ''; ?>">
        <i class="fa-solid fa-bullhorn me-2"></i>Notices
    </a>

    <a href="../../student/logout/logout.php" class="nav-link text-danger mt-3 <?php echo ($active_page === 'logout') ? 'active' : ''; ?>">
        <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
    </a>
</div>

<!-- MOBILE SIDEBAR (OFFCANVAS) -->
<div class="offcanvas offcanvas-start bg-sidebar text-white" id="studentSidebar" tabindex="-1" aria-labelledby="studentSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="studentSidebarLabel">
            <i class="fa-solid fa-user-graduate me-2"></i>Student Panel
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">
        <a href="../../student/dashboard/student_dashboard.php" class="nav-link <?php echo ($active_page === 'dashboard') ? 'active' : ''; ?>">
            <i class="fa-solid fa-gauge-high me-2"></i>Dashboard
        </a>

        <a href="../../student/profile/student_profile.php" class="nav-link <?php echo ($active_page === 'profile') ? 'active' : ''; ?>">
            <i class="fa-solid fa-id-card me-2"></i>My Profile
        </a>

        <a href="../../student/rooms/student_room.php" class="nav-link <?php echo ($active_page === 'room') ? 'active' : ''; ?>">
            <i class="fa-solid fa-bed me-2"></i>My Room
        </a>

        <a href="../../student/leave/student_leave.php" class="nav-link <?php echo ($active_page === 'leave') ? 'active' : ''; ?>">
            <i class="fa-solid fa-person-walking-luggage me-2"></i>Leave Requests
        </a>

        <a href="../../student/complaints/student_complaints.php" class="nav-link <?php echo ($active_page === 'complaints') ? 'active' : ''; ?>">
            <i class="fa-solid fa-screwdriver-wrench me-2"></i>Complaints
        </a>

        <a href="../../student/notices/notice_board.php" class="nav-link <?php echo ($active_page === 'notices') ? 'active' : ''; ?>">
            <i class="fa-solid fa-bullhorn me-2"></i>Notices
        </a>

        <a href="../../student/logout/logout.php" class="nav-link text-danger mt-3 <?php echo ($active_page === 'logout') ? 'active' : ''; ?>">
            <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
        </a>
    </div>
</div>