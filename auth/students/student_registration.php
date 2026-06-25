<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/student_registration.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Student Registration</h2>
        <form action="student_registration_process.php" method="POST" enctype="multipart/form-data">
            <!-- Personal Information -->
           <h5 class="section-title">1. Personal Information</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Full Name</label>
                    <input type="text"
                        class="form-control"
                        name="fullname"
                        pattern="[A-Za-z ]+"
                        title="Only alphabets and spaces allowed"
                        required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Enrollment Number</label>
                    <input type="text" name="enrollment" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Gender</label>
                    <select class="form-control" name="gender">
                        <option>Female</option>
                        <option>Male</option>
                        <option>Other</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Date of Birth</label>
                    <input type="date" class="form-control" name="dob" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Blood Group</label>
                    <input type="text" class="form-control" name="blood" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Mobile No</label>
                    <input type="tel"
                        class="form-control"
                        name="mobile"
                        pattern="[0-9]{10}"
                        maxlength="10"
                        minlength="10"
                        title="Enter 10 digit mobile number"
                        required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>Email Id</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Address</label>
                    <textarea class="form-control" name="address" required></textarea>
                </div>

                <!-- <div class="col-md-6 mb-3">
                    <label>Student Photo</label>
                    <input type="file" class="form-control" name="photo" accept="image/*" required>
                </div> -->
                <div class="col-md-6 mb-3">

                    <label>Student Photo</label>

                    <input type="file"
                        class="form-control"
                        id="photo"
                        name="photo"
                        accept=".jpg,.jpeg,.png,.webp"
                        required>

                    <small class="text-muted">
                        Allowed Formats: JPG, JPEG, PNG, WEBP |
                        Maximum Size: 5MB
                    </small>

                    <div id="photoSize"
                        class="text-primary mt-1">
                    </div>

                </div>
            </div>
            <!-- Academic Information -->
            <h5 class="section-title">2. Academic Information</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>College/University Name</label>
                      <input type="text" class="form-control" name="college" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>Course</label>
                      <input type="text" class="form-control" name="course" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Department</label>
                        <input type="text"
                        class="form-control"
                        name="department"
                        pattern="[A-Za-z ]+"
                        title="Only alphabets and spaces allowed"
                        required>
                   
                </div>

                <div class="col-md-6 mb-3">
                    <label>Year / Semester</label>
                    <input type="text" class="form-control" name="semester" required>
                </div>

                <!-- <div class="col-md-6 mb-3">
                    <label>CollegeId Card</label>
                    <input type="file" class="form-control" name="idcard" required>
                </div> -->
                <div class="col-md-6 mb-3">

                    <label>College ID Card</label>

                    <input type="file"
                        class="form-control"
                        id="idcard"
                        name="idcard"
                        accept=".jpg,.jpeg,.png,.pdf"
                        required>

                    <small class="text-muted">
                        Allowed Formats: JPG, PNG, PDF |
                        Maximum Size: 10MB
                    </small>

                    <div id="idcardSize"
                        class="text-primary mt-1">
                    </div>

                </div>
            </div>
            <!-- Perants Information -->
            <h5 class="section-title">3. Perants Information</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Father Name</label>
                    <input type="text"
                        class="form-control"
                        name="father"
                        pattern="[A-Za-z ]+"
                        title="Only alphabets and spaces allowed"
                        required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Mother Name</label>
                    <input type="text"
                        class="form-control"
                        name="mother"
                        pattern="[A-Za-z ]+"
                        title="Only alphabets and spaces allowed"
                        required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Parents Contact No</label>
                    <input type="tel"
                        class="form-control"
                        name="parent_mobile"
                        pattern="[0-9]{10}"
                        maxlength="10"
                        minlength="10"
                        title="Enter 10 digit mobile number"
                        required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Parent Occupation</label>
                    <input type="text"
                        class="form-control"
                        name="occupation"
                        pattern="[A-Za-z ]+"
                        title="Only alphabets and spaces allowed"
                        required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Parent Address</label>
                    <textarea class="form-control" name="parent_address" required></textarea>
                </div>
                <div class="col-md-6 mb-3">
                <label>Select Hostel</label>

                <select
                    name="hostel_name"
                    class="form-control"
                    required
                >
                    <option value="">
                        Select Hostel
                    </option>

                    <?php
                    require_once(__DIR__ . "/../../config/db.php");

                    $hostels = mysqli_query(
                        $conn,
                        "SELECT DISTINCT hostel_name
                        FROM admins
                        WHERE hostel_name IS NOT NULL
                        AND hostel_name != ''
                        ORDER BY hostel_name ASC"
                    );

                    while($hostel = mysqli_fetch_assoc($hostels)){
                    ?>
                        <option value="<?= htmlspecialchars($hostel['hostel_name']); ?>">
                            <?= htmlspecialchars($hostel['hostel_name']); ?>
                        </option>
                    <?php } ?>

                </select>
            </div>

                <div class="col-md-6 mb-3">
                    <label>Gaurdian Name</label>
                    <input type="text"
                        class="form-control"
                        name="guardian_name"
                        pattern="[A-Za-z ]+"
                        title="Only alphabets and spaces allowed">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Gaurdian Address</label>
                    <textarea class="form-control"  name="guardian_address"></textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Gaurdian Contact No</label>
                    <input type="tel"
                        class="form-control"
                        name="guardian_contact"
                        pattern="[0-9]{10}"
                        maxlength="10"
                        minlength="10"
                        title="Enter 10 digit mobile number">
                </div>
            </div>

            <!-- Emergency Contact -->
             <h5 class="section-title">4. Emergency Contact Information</h5>

             <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Emergency Person Name</label>
                    <input type="text"
                        class="form-control"
                        name="emergency_name"
                        pattern="[A-Za-z ]+"
                        title="Only alphabets and spaces allowed"
                        required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Relationship</label>
                    <input type="text"
                        class="form-control"
                        name="relation"
                        pattern="[A-Za-z ]+"
                        title="Only alphabets and spaces allowed"
                        required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Contact No</label>
                    <input type="tel"
                        class="form-control"
                        name="emergency_contact"
                        pattern="[0-9]{10}"
                        maxlength="10"
                        minlength="10"
                        title="Enter 10 digit mobile number"
                        required>
                </div>

                <div class="text-center mt-4">
                   <button class="btn btn-primary px-5">Register Student</button>  
                </div>
             </div>
             
        </form>
    </div>
</body>

<script>

// PHOTO SIZE

document.getElementById('photo')
.addEventListener('change', function(){

    let file = this.files[0];

    if(file){

        let size =
            (file.size / 1024 / 1024)
            .toFixed(2);

        document.getElementById('photoSize')
        .innerHTML =
        "Selected File Size : " +
        size + " MB";

        // MAX 5MB

        if(file.size > 5 * 1024 * 1024){

            alert(
            'Photo size must be less than 5MB'
            );

            this.value = '';

            document.getElementById(
            'photoSize'
            ).innerHTML = '';
        }
    }
});

// Only numbers for mobile fields

document.querySelectorAll(
    'input[name="mobile"], input[name="parent_mobile"], input[name="guardian_contact"], input[name="emergency_contact"]'
).forEach(function(input){

    input.addEventListener('input', function(){

        this.value = this.value
            .replace(/\D/g, '')
            .slice(0, 10);

    });

});


// Only alphabets for name fields

document.querySelectorAll(
    'input[name="fullname"], input[name="father"], input[name="mother"], input[name="guardian_name"], input[name="emergency_name"], input[name="relation"], input[name="occupation"]'
).forEach(function(input){

    input.addEventListener('input', function(){

        this.value = this.value.replace(
            /[^A-Za-z ]/g,
            ''
        );

    });

});


// ID CARD SIZE

document.getElementById('idcard')
.addEventListener('change', function(){

    let file = this.files[0];

    if(file){

        let size =
            (file.size / 1024 / 1024)
            .toFixed(2);

        document.getElementById('idcardSize')
        .innerHTML =
        "Selected File Size : " +
        size + " MB";

        // MAX 10MB

        if(file.size > 10 * 1024 * 1024){

            alert(
            'ID Card size must be less than 10MB'
            );

            this.value = '';

            document.getElementById(
            'idcardSize'
            ).innerHTML = '';
        }
    }
});

</script>
</html>