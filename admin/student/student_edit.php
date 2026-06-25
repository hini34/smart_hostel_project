<?php
// student_edit.php
require_once(__DIR__ . "/../../config/db.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("Invalid student id");
}

// current student
$res = mysqli_query($conn, "SELECT * FROM students WHERE id=$id LIMIT 1");
$student = ($res && mysqli_num_rows($res) === 1) ? mysqli_fetch_assoc($res) : null;
if (!$student) {
    die("Student not found");
}

$success = "";
$error   = "";

// submit update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname         = $_POST['fullname'] ?? '';
    $enrollment       = $_POST['enrollment'] ?? '';
    $gender           = $_POST['gender'] ?? '';
    $dob              = $_POST['dob'] ?? null;
    $blood            = $_POST['blood'] ?? '';
    $mobile           = $_POST['mobile'] ?? '';
    $email            = $_POST['email'] ?? '';
    $address          = $_POST['address'] ?? '';
    $college          = $_POST['college'] ?? '';
    $course           = $_POST['course'] ?? '';
    $department       = $_POST['department'] ?? '';
    $semester         = $_POST['semester'] ?? '';
    $father           = $_POST['father'] ?? '';
    $mother           = $_POST['mother'] ?? '';
    $parent_mobile    = $_POST['parent_mobile'] ?? '';
    $occupation       = $_POST['occupation'] ?? '';
    $parent_address   = $_POST['parent_address'] ?? '';
    $emergency_contact= $_POST['emergency_contact'] ?? '';

    if ($fullname === '' || $enrollment === '' || $mobile === '') {
        $error = "Full name, enrollment and mobile are required.";
    } else {

        // photo (optional replace)
        // $photoPath = $student['photo']; // old

        // if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {

        //     $uploadDir = "uploads/students/";
        //     if (!is_dir($uploadDir)) {
        //         mkdir($uploadDir, 0777, true);
        //     }

        //     $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        //     $fileName = "stu_" . time() . "_" . rand(1000,9999) . "." . $ext;
        //     $target = $uploadDir . $fileName;

        //     if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
        //         // delete old photo
        //         if ($photoPath && file_exists($photoPath)) {
        //             @unlink($photoPath);
        //         }
        //         $photoPath = $target;
        //     }
        // }

        $sql = "UPDATE students SET
                fullname=?, enrollment=?, gender=?, dob=?, blood=?, mobile=?, email=?, address=?,
                college=?, course=?, department=?, semester=?,
                father=?, mother=?, parent_mobile=?, occupation=?, parent_address=?,
                emergency_contact=?, photo=?
                WHERE id=?";

        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "sssssssssssssssssssi",
                $fullname, $enrollment, $gender, $dob, $blood,
                $mobile, $email, $address,
                $college, $course, $department, $semester,
                $father, $mother, $parent_mobile, $occupation, $parent_address,
                $emergency_contact, $photoPath, $id
            );

            if (mysqli_stmt_execute($stmt)) {
                $success = "Student updated successfully.";
                // refresh current data
                $res = mysqli_query($conn, "SELECT * FROM students WHERE id=$id LIMIT 1");
                $student = mysqli_fetch_assoc($res);
            } else {
                $error = "Database error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = "Failed to prepare statement: " . mysqli_error($conn);
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Student</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Edit Student</h3>
    <a href="student.php" class="btn btn-outline-secondary">← Back to Students</a>
  </div>

  <?php if($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
  <?php endif; ?>
  <?php if($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="post" enctype="multipart/form-data">

        <h5 class="mb-3">Personal Information</h5>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Full Name *</label>
            <input type="text" name="fullname" class="form-control"
                   value="<?php echo htmlspecialchars($student['fullname']); ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Enrollment *</label>
            <input type="text" name="enrollment" class="form-control"
                   value="<?php echo htmlspecialchars($student['enrollment']); ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
              <option value="">Select</option>
              <option <?php if($student['gender']=='Male') echo 'selected'; ?>>Male</option>
              <option <?php if($student['gender']=='Female') echo 'selected'; ?>>Female</option>
              <option <?php if($student['gender']=='Other') echo 'selected'; ?>>Other</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="dob" class="form-control"
                   value="<?php echo htmlspecialchars($student['dob']); ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">Blood Group</label>
            <input type="text" name="blood" class="form-control"
                   value="<?php echo htmlspecialchars($student['blood']); ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">Mobile *</label>
            <input type="text" name="mobile" class="form-control"
                   value="<?php echo htmlspecialchars($student['mobile']); ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?php echo htmlspecialchars($student['email']); ?>">
          </div>

          <!-- <div class="col-md-4">
            <label class="form-label">Photo</label><br>
            <?php if($student['photo']): ?>
              <img src="<?php echo htmlspecialchars($student['photo']); ?>"
                   style="width:60px;height:60px;object-fit:cover;border-radius:50%;margin-bottom:5px;">
            <?php endif; ?>
            <input type="file" name="photo" class="form-control mt-1">
          </div> -->

          <div class="col-12">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2"><?php
              echo htmlspecialchars($student['address']);
            ?></textarea>
          </div>
        </div>

        <hr class="my-4">

        <h5 class="mb-3">Academic Details</h5>
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">College</label>
            <input type="text" name="college" class="form-control"
                   value="<?php echo htmlspecialchars($student['college']); ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Course</label>
            <input type="text" name="course" class="form-control"
                   value="<?php echo htmlspecialchars($student['course']); ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Department</label>
            <input type="text" name="department" class="form-control"
                   value="<?php echo htmlspecialchars($student['department']); ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Semester</label>
            <input type="text" name="semester" class="form-control"
                   value="<?php echo htmlspecialchars($student['semester']); ?>">
          </div>
        </div>

        <hr class="my-4">

        <h5 class="mb-3">Parent / Emergency Details</h5>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Father Name</label>
            <input type="text" name="father" class="form-control"
                   value="<?php echo htmlspecialchars($student['father']); ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Mother Name</label>
            <input type="text" name="mother" class="form-control"
                   value="<?php echo htmlspecialchars($student['mother']); ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Parent Mobile</label>
            <input type="text" name="parent_mobile" class="form-control"
                   value="<?php echo htmlspecialchars($student['parent_mobile']); ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Occupation</label>
            <input type="text" name="occupation" class="form-control"
                   value="<?php echo htmlspecialchars($student['occupation']); ?>">
          </div>
          <div class="col-md-8">
            <label class="form-label">Parent Address</label>
            <textarea name="parent_address" class="form-control" rows="2"><?php
              echo htmlspecialchars($student['parent_address']);
            ?></textarea>
          </div>
          <div class="col-md-4">
            <label class="form-label">Emergency Contact</label>
            <input type="text" name="emergency_contact" class="form-control"
                   value="<?php echo htmlspecialchars($student['emergency_contact']); ?>">
          </div>
        </div>

        <div class="mt-4 text-end">
          <button type="submit" class="btn btn-primary px-4">Update Student</button>
        </div>

      </form>
    </div>
  </div>
</div>
</body>
</html>
