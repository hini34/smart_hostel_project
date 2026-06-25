<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

if (
    !isset($_SESSION['student_id']) ||
    empty($_SESSION['hostel_name'])
) {
    header("Location: ../../auth/students/student_login.php");
    exit();
}


$page_title   = "Room Details ";
$active_page  = "room";
$student_name = $_SESSION['student_name'] ?? $_SESSION['student'] ?? "Student";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../../includes/student/student_header.php'; ?>
    <link rel="stylesheet" href="../../assets/css/student/student_sidebar.css">
    <link rel="stylesheet" href="../../assets/css/student/student_room.css">
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

<?php include __DIR__ . '/../../includes/student/student_sidebar.php'; ?>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="card hero-card mb-3">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="mb-1 fw-bold">My Room</h4>
                <p class="small-muted mb-0">
                    View your assigned room, roommates, allotment history, and hostel contact details.
                </p>
            </div>
            <div class="text-md-end">
                <div class="data-label">Room Status</div>
                <div id="room-status-text">
                    <span class="status-badge status-info">Checking room status...</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">

        <!-- Assigned Room Details -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="section-title">Assigned Room Details</span>

                    <div id="room-details-error" class="error-box mt-3 d-none"></div>

                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <div class="info-box">
                                <div class="data-label">Room Number</div>
                                <div class="data-value" id="room-no">
                                    <span class="field-error">Room details are loading...</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="info-box">
                                <div class="data-label">Floor Number</div>
                                <div class="data-value" id="floor-no">
                                    <span class="field-error">Room details are loading...</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="info-box">
                                <div class="data-label">Block</div>
                                <div class="data-value" id="block-name">
                                    <span class="field-error">Room details are loading...</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="info-box">
                                <div class="data-label">Occupancy Type</div>
                                <div class="data-value" id="occupancy-text">
                                    <span class="field-error">Room details are loading...</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="info-box">
                                <div class="data-label">Capacity</div>
                                <div class="data-value" id="capacity">
                                    <span class="field-error">Room details are loading...</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="info-box">
                                <div class="data-label">Currently Occupied</div>
                                <div class="data-value" id="occupied">
                                    <span class="field-error">Room details are loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roommates -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="section-title">Roommates</span>
                    <p class="small-muted mb-2">Students sharing this room with you.</p>

                    <div id="mates-empty" class="empty-box">
                        Roommate information is loading...
                    </div>

                    <div class="table-responsive d-none mt-2" id="mates-table-wrapper">
                        <table class="table table-sm align-middle roommates-table">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Enrollment</th>
                                    <th>Mobile</th>
                                </tr>
                            </thead>
                            <tbody id="mates-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Allotment History -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="section-title">Room Allotment History</span>
                    <ul class="small-muted mt-3 mb-0 history-list" id="history-list">
                        <li>Room allotment history is loading...</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Warden Contact -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="section-title">Warden / Security Contact</span>

                    <div id="warden-error" class="error-box mt-3 d-none"></div>

                    <div class="row g-2 mt-2">
                        <div class="col-sm-6 col-lg-4">
                            <div class="info-box h-100">
                                <div class="data-label">Warden Name</div>
                                <div class="data-value" id="warden-name">
                                    <span class="field-error">Contact details are loading...</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-4">
                            <div class="info-box h-100">
                                <div class="data-label">Mobile Number</div>
                                <div class="data-value" id="warden-mobile">
                                    <span class="field-error">Contact details are loading...</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-4">
                            <div class="info-box h-100">
                                <div class="data-label">Email</div>
                                <div class="data-value" id="warden-email">
                                    <span class="field-error">Contact details are loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complaint Section -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="section-title">Room Complaint Section</span>
                    <p class="small-muted mb-2">
                        If you face any repair or maintenance issue in your room, raise a complaint from here.
                    </p>

                    <div class="d-grid gap-2">
                        <a href="/smart_hostel/student/complaints/student_complaints.php" class="btn btn-outline-warning btn-sm">
                            <i class="fa-solid fa-screwdriver-wrench me-1"></i> Room Repair / Maintenance
                        </a>
                        <a href="/smart_hostel/student/complaints/student_complaints.php" class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-broom me-1"></i> Cleaning Request
                        </a>
                        <a href="/smart_hostel/student/complaints/student_complaints.php" class="btn btn-outline-danger btn-sm">
                            <i class="fa-solid fa-plug-circle-exclamation me-1"></i> Electrical / Other Issue
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guidelines -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="section-title">Hostel Room Guidelines</span>
                    <ul class="small-muted mt-3 mb-0 guideline-list">
                        <li><b>Hostel timings:</b> Hostel gate opens at 07:00 AM and closes at 10:00 PM.</li>
                        <li><b>Restricted items:</b> Do not keep electric heaters, induction stoves, iron, candles, or illegal items.</li>
                        <li><b>Visitors:</b> Allowed only during visiting hours (09:00 AM to 06:00 PM) with proper entry.</li>
                        <li><b>Noise discipline:</b> Maintain silence during study hours and respect your roommates.</li>
                        <li><b>Cleanliness:</b> Keep the room clean, use dustbins, and avoid damaging walls or furniture.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/student/student_room.js"></script>
</body>
</html>