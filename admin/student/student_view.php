<?php
// student_view.php
session_start();
require_once(__DIR__ . "/../../config/db.php");
$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id <= 0){
    header("Location: student.php?error=Invalid Student ID");
    exit;
}

$sql = "
SELECT *
FROM students
WHERE id = ?
AND hostel_name = ?
LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {

    echo "<script>
            alert('Unable to load student details');
            window.location='student.php';
          </script>";
    exit();
}

mysqli_stmt_bind_param(
    $stmt,
    "is",
    $id,
    $hostel_name
);

mysqli_stmt_execute($stmt);

$res = mysqli_stmt_get_result($stmt);

$student = (
    $res &&
    mysqli_num_rows($res) === 1
)
? mysqli_fetch_assoc($res)
: null;

$photo = trim($student['photo'] ?? '');

if (!empty($photo) && file_exists("../../" . $photo)) {
    $img = "../../" . $photo;
} else {
    $img = "../../assets/images/default.png";
}

if(!$student){

    echo "
    <div style='padding:20px;
                font-family:Arial'>
        Student not found
        or access denied.
    </div>";

    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Student Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-0">Student Profile</h3>
      <small class="text-muted">Full details of student</small>
    </div>
    <div class="d-flex gap-2">
      <a href="student.php" class="btn btn-outline-secondary">← Back</a>
      <!-- <a href="student_edit.php?id=<?php echo (int)$student['id']; ?>" class="btn btn-warning">Edit</a> -->
    </div>
  </div>

  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <h5 class="mb-3">Basic Details</h5>
      <div class="row g-3">
        <div class="col-md-4">
          <strong>Full Name</strong><br>
          <?php echo htmlspecialchars($student['fullname']); ?>
        </div>
        <div class="col-md-4">
          <strong>Enrollment</strong><br>
          <?php echo htmlspecialchars($student['enrollment']); ?>
        </div>
        <div class="col-md-4">
          <strong>Gender</strong><br>
          <?php echo htmlspecialchars($student['gender']); ?>
        </div>
        <div class="col-md-4">
          <strong>Date of Birth</strong><br>
          <?php echo htmlspecialchars($student['dob']); ?>
        </div>
        <div class="col-md-4">
          <strong>Blood Group</strong><br>
          <?php echo htmlspecialchars($student['blood']); ?>
        </div>
        <div class="col-md-4">
          <strong>Mobile</strong><br>
          <?php echo htmlspecialchars($student['mobile']); ?>
        </div>
        <div class="col-md-4">
          <strong>Email</strong><br>
          <?php echo htmlspecialchars($student['email']); ?>
        </div>
        <div class="col-md-8">
          <strong>Address</strong><br>
          <?php echo nl2br(htmlspecialchars($student['address'])); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <h5 class="mb-3">Academic Information</h5>
      <div class="row g-3">
        <div class="col-md-4">
          <strong>College</strong><br>
          <?php echo htmlspecialchars($student['college']); ?>
        </div>
        <div class="col-md-4">
          <strong>Course</strong><br>
          <?php echo htmlspecialchars($student['course']); ?>
        </div>
        <div class="col-md-4">
          <strong>Department</strong><br>
          <?php echo htmlspecialchars($student['department']); ?>
        </div>
        <div class="col-md-4">
          <strong>Semester</strong><br>
          <?php echo htmlspecialchars($student['semester']); ?>
        </div>
        <div class="col-md-4">
          <strong>Registered On</strong><br>
          <?php echo htmlspecialchars($student['created_at']); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="mb-3">Parent / Emergency Details</h5>
      <div class="row g-3">
        <div class="col-md-4">
          <strong>Father's Name</strong><br>
          <?php echo htmlspecialchars($student['father']); ?>
        </div>
        <div class="col-md-4">
          <strong>Mother's Name</strong><br>
          <?php echo htmlspecialchars($student['mother']); ?>
        </div>
        <div class="col-md-4">
          <strong>Parent Mobile</strong><br>
          <?php echo htmlspecialchars($student['parent_mobile']); ?>
        </div>
        <div class="col-md-4">
          <strong>Occupation</strong><br>
          <?php echo htmlspecialchars($student['occupation']); ?>
        </div>
        <div class="col-md-4">
          <strong>Parent Address</strong><br>
          <?php echo nl2br(htmlspecialchars($student['parent_address'])); ?>
        </div>
        <div class="col-md-4">
          <strong>Emergency Contact</strong><br>
          <?php echo htmlspecialchars($student['emergency_contact']); ?>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
