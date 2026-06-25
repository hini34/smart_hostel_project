<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

$page_title   = "Student Profile";
$active_page  = "profile";
$student_id   = $_SESSION['student_id'] ?? 0;
$student_name = $_SESSION['student_name'] ?? $_SESSION['student'] ?? "Student";

function showValue($value, $fallback)
{
    $value = trim((string)($value ?? ''));
    return $value !== ''
        ? htmlspecialchars($value)
        : '<span class="field-error"><i class="fa-solid fa-circle-exclamation me-1"></i>' . htmlspecialchars($fallback) . '</span>';
}

function showValueNl2br($value, $fallback)
{
    $value = trim((string)($value ?? ''));
    return $value !== ''
        ? nl2br(htmlspecialchars($value))
        : '<span class="field-error"><i class="fa-solid fa-circle-exclamation me-1"></i>' . htmlspecialchars($fallback) . '</span>';
}

function showDateValue($value, $fallback)
{
    $value = trim((string)($value ?? ''));
    if ($value === '' || $value === '0000-00-00') {
        return '<span class="field-error"><i class="fa-solid fa-circle-exclamation me-1"></i>' . htmlspecialchars($fallback) . '</span>';
    }

    $timestamp = strtotime($value);
    return $timestamp
        ? date("d M Y", $timestamp)
        : '<span class="field-error"><i class="fa-solid fa-circle-exclamation me-1"></i>' . htmlspecialchars($fallback) . '</span>';
}


// Default values
$student = [
    'fullname'          => '',
    'enrollment'        => '',
    'gender'            => '',
    'dob'               => '',
    'blood'             => '',
    'mobile'            => '',
    'email'             => '',
    'address'           => '',
    'college'           => '',
    'course'            => '',
    'department'        => '',
    'semester'          => '',
    'father'            => '',
    'mother'            => '',
    'parent_mobile'     => '',
    'occupation'        => '',
    'parent_address'    => '',
    'emergency_contact' => '',
    'photo'             => '',
    'room_no'           => '',
    'block_name'        => '',
    'floor_no'          => '',
    'room_type'         => '',
    'hostel_name' => ''
];

$load_error = "";

// Student + Room info
if ($student_id > 0) {
    $sql = "SELECT 
                s.*,
                r.room_no,
                r.block_name,
                r.floor_no,
                r.room_type
            FROM students s
            LEFT JOIN rooms r ON s.room_id = r.id
            WHERE s.id = ?
            AND s.hostel_name = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param(
            $stmt,
            "is",
            $student_id,
            $hostel_name
        );
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $student = array_merge($student, $row);
        } else {
            $load_error = "Student profile could not be loaded. No record was found for this login.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $load_error = "Student profile could not be loaded because the profile query failed.";
    }
} else {
    $load_error = "Student session is missing. Please login again to view your profile.";
}

// Profile image
$photoFile = trim($student['photo'] ?? '');
$photoPath = "";

if ($photoFile !== '') {
    $photoPath = "../../" . $photoFile;
}

$hasPhoto = (!empty($photoFile) && file_exists($photoPath));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../../includes/student/student_header.php'; ?>
    <link rel="stylesheet" href="../../assets/css/student/student_sidebar.css">
    <link rel="stylesheet" href="../../assets/css/student/student_profile.css">
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


<!-- MAIN CONTENT -->
<div class="main-content">

<?php include __DIR__ . '/../../includes/student/student_sidebar.php'; ?>

    <?php if ($load_error !== ''): ?>
        <div class="top-error-box">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <?php echo htmlspecialchars($load_error); ?>
        </div>
    <?php endif; ?>

    <!-- Top Profile Card -->
    <div class="card mb-3">
        <div class="card-body d-flex flex-wrap align-items-center gap-3">
            <div>
                <div class="profile-avatar">
                    <?php if ($hasPhoto): ?>
                        <img src="<?php echo htmlspecialchars($photoPath); ?>" alt="Profile Photo">
                    <?php else: ?>
                        <i class="fa-solid fa-user-graduate"></i>
                    <?php endif; ?>
                </div>

                <?php if (!$hasPhoto): ?>
                    <p class="profile-note field-error mb-0">
                        <i class="fa-solid fa-circle-exclamation me-1"></i>Profile photo not uploaded
                    </p>
                <?php endif; ?>
            </div>

            <div class="flex-grow-1">
                <h4 class="mb-1">
                    <?php echo showValue($student['fullname'], 'Student name not found in profile'); ?>
                </h4>
                <p class="small-muted mb-1">
                    Enrollment: <?php echo showValue($student['enrollment'], 'Enrollment number not available'); ?>
                </p>
                <span class="badge badge-soft">
                    <i class="fa-solid fa-building-user me-1"></i> Smart Hostel Student
                </span>
            </div>
        </div>
    </div>

    <div class="row g-3">

        <!-- Personal Information -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="section-title">Personal Information</span>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="data-label">Full Name</div>
                            <div class="data-value"><?php echo showValue($student['fullname'], 'Full name not available in profile'); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="data-label">Enrollment</div>
                            <div class="data-value"><?php echo showValue($student['enrollment'], 'Enrollment number not available'); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="data-label">Gender</div>
                            <div class="data-value"><?php echo showValue($student['gender'], 'Gender not added in profile'); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="data-label">DOB</div>
                            <div class="data-value"><?php echo showDateValue($student['dob'], 'Date of birth not available'); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="data-label">Blood Group</div>
                            <div class="data-value"><?php echo showValue($student['blood'], 'Blood group not added'); ?></div>
                        </div>
                    </div>

                    <hr>

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="data-label">Mobile</div>
                            <div class="data-value"><?php echo showValue($student['mobile'], 'Mobile number not added'); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="data-label">Email</div>
                            <div class="data-value"><?php echo showValue($student['email'], 'Email address not added'); ?></div>
                        </div>
                        <div class="col-12">
                            <div class="data-label">Address</div>
                            <div class="data-value"><?php echo showValueNl2br($student['address'], 'Address not available in profile'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic + Room Info -->
        <div class="col-lg-6">
            <div class="card h-100 mb-3">
                <div class="card-body">
                    <span class="section-title">Academic Information</span>
                    <div class="row g-2 mt-2">
                        <div class="col-12">
                            <div class="col-12">
                                <div class="data-label">Hostel</div>
                                <div class="data-value">
                                    <?php echo showValue(
                                        $student['hostel_name'],
                                        'Hostel not assigned'
                                    ); ?>
                                </div>
                            </div>
                            <div class="data-label">College</div>
                            <div class="data-value"><?php echo showValue($student['college'], 'College name not available'); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="data-label">Course</div>
                            <div class="data-value"><?php echo showValue($student['course'], 'Course details not available'); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="data-label">Department</div>
                            <div class="data-value"><?php echo showValue($student['department'], 'Department details not available'); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="data-label">Semester</div>
                            <div class="data-value"><?php echo showValue($student['semester'], 'Semester details not available'); ?></div>
                        </div>
                    </div>

                    <hr>

                    <span class="section-title">Hostel Room Details</span>
                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <div class="data-label">Room No</div>
                            <div class="data-value"><?php echo showValue($student['room_no'], 'Room not assigned yet'); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="data-label">Block</div>
                            <div class="data-value"><?php echo showValue($student['block_name'], 'Block details will appear after room allotment'); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="data-label">Floor</div>
                            <div class="data-value"><?php echo showValue($student['floor_no'], 'Floor details will appear after room allotment'); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="data-label">Room Type</div>
                            <div class="data-value"><?php echo showValue($student['room_type'], 'Room type will appear after room allotment'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parent / Guardian Details -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <span class="section-title">Parent / Guardian Details</span>
                    <div class="row g-2 mt-2">
                        <div class="col-md-4 col-6">
                            <div class="data-label">Father's Name</div>
                            <div class="data-value"><?php echo showValue($student['father'], 'Father name not added'); ?></div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="data-label">Mother's Name</div>
                            <div class="data-value"><?php echo showValue($student['mother'], 'Mother name not added'); ?></div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="data-label">Parent Mobile</div>
                            <div class="data-value"><?php echo showValue($student['parent_mobile'], 'Parent mobile number not added'); ?></div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="data-label">Occupation</div>
                            <div class="data-value"><?php echo showValue($student['occupation'], 'Occupation details not added'); ?></div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="data-label">Emergency Contact</div>
                            <div class="data-value"><?php echo showValue($student['emergency_contact'], 'Emergency contact not added'); ?></div>
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="data-label">Parent Address</div>
                            <div class="data-value"><?php echo showValueNl2br($student['parent_address'], 'Parent address not available'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>