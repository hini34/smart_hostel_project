<?php

require_once(__DIR__ . "/../../config/db.php");

// ======================================================
// ONLY POST REQUEST ALLOWED
// ======================================================

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    header("Location: student_registration.php");
    exit;
}

// ======================================================
// FORM DATA
// ======================================================

$fullname          = trim($_POST['fullname'] ?? '');
$enrollment        = trim($_POST['enrollment'] ?? '');
$gender            = trim($_POST['gender'] ?? '');
$dob               = trim($_POST['dob'] ?? '');
$blood             = trim($_POST['blood'] ?? '');
$mobile            = trim($_POST['mobile'] ?? '');
$email             = trim($_POST['email'] ?? '');
$address           = trim($_POST['address'] ?? '');

$college           = trim($_POST['college'] ?? '');
$course            = trim($_POST['course'] ?? '');
$department        = trim($_POST['department'] ?? '');
$semester          = trim($_POST['semester'] ?? '');

$father            = trim($_POST['father'] ?? '');
$mother            = trim($_POST['mother'] ?? '');
$parent_mobile     = trim($_POST['parent_mobile'] ?? '');
$occupation        = trim($_POST['occupation'] ?? '');
$parent_address    = trim($_POST['parent_address'] ?? '');
$hostel_name       = trim($_POST['hostel_name'] ?? '');

$guardian_name     = trim($_POST['guardian_name'] ?? '');
$guardian_address  = trim($_POST['guardian_address'] ?? '');
$guardian_contact  = trim($_POST['guardian_contact'] ?? '');

$emergency_name    = trim($_POST['emergency_name'] ?? '');
$relation          = trim($_POST['relation'] ?? '');
$emergency_contact = trim($_POST['emergency_contact'] ?? '');

$status = "pending";

// ======================================================
// REQUIRED FIELD VALIDATION
// ======================================================

if (
    $fullname == '' ||
    $enrollment == '' ||
    $email == '' ||
    $mobile == '' ||
    $hostel_name == ''
) {

    echo "<script>

            alert('Please Fill All Required Fields');

            history.back();

          </script>";

    exit;
}

// ======================================================
// CHECK ENROLLMENT EXISTS
// ======================================================

$check = mysqli_prepare(
    $conn,
    "SELECT id FROM students WHERE enrollment=?"
);

mysqli_stmt_bind_param(
    $check,
    "s",
    $enrollment
);

mysqli_stmt_execute($check);

mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {

    echo "<script>

            alert('Enrollment Number Already Exists');

            history.back();

          </script>";

    exit;
}

// ======================================================
// PHOTO UPLOAD
// ======================================================

$photo = null;

if (
    isset($_FILES['photo']) &&
    $_FILES['photo']['error'] == 0
) {

    // FILE SIZE CHECK → 5MB

    if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {

        echo "<script>

                alert('Photo Size Must Be Less Than 5MB');

                history.back();

              </script>";

        exit;
    }

    $photoFolder =
        __DIR__ . "/../../uploads/students/";

    if (!is_dir($photoFolder)) {

        mkdir($photoFolder, 0777, true);
    }

    $photoTmpName =
        $_FILES['photo']['tmp_name'];

    $photoOriginalName =
        $_FILES['photo']['name'];

    $photoExtension = strtolower(

        pathinfo(
            $photoOriginalName,
            PATHINFO_EXTENSION
        )
    );

    $allowedPhotoExtensions = [

        'jpg',
        'jpeg',
        'png',
        'webp'
    ];

    if (
        !in_array(
            $photoExtension,
            $allowedPhotoExtensions
        )
    ) {

        echo "<script>

                alert('Only JPG, JPEG, PNG, WEBP Files Allowed In Photo');

                history.back();

              </script>";

        exit;
    }

    $newPhotoName =

        "student_" .
        time() .
        "_" .
        rand(1000,9999) .
        "." .
        $photoExtension;

    $photoDestination =
        $photoFolder . $newPhotoName;

    if (
        move_uploaded_file(
            $photoTmpName,
            $photoDestination
        )
    ) {

        $photo =
            "uploads/students/" .
            $newPhotoName;
    }
}

// ======================================================
// ID CARD UPLOAD
// ======================================================

$idcard = null;

if (
    isset($_FILES['idcard']) &&
    $_FILES['idcard']['error'] == 0
) {

    // FILE SIZE CHECK → 10MB

    if ($_FILES['idcard']['size'] > 10 * 1024 * 1024) {

        echo "<script>

                alert('ID Card Size Must Be Less Than 10MB');

                history.back();

              </script>";

        exit;
    }

    $idcardFolder =
        __DIR__ . "/../../uploads/idcards/";

    if (!is_dir($idcardFolder)) {

        mkdir($idcardFolder, 0777, true);
    }

    $idcardTmpName =
        $_FILES['idcard']['tmp_name'];

    $idcardOriginalName =
        $_FILES['idcard']['name'];

    $idcardExtension = strtolower(

        pathinfo(
            $idcardOriginalName,
            PATHINFO_EXTENSION
        )
    );

    $allowedIdcardExtensions = [

        'jpg',
        'jpeg',
        'png',
        'pdf'
    ];

    if (
        !in_array(
            $idcardExtension,
            $allowedIdcardExtensions
        )
    ) {

        echo "<script>

                alert('Only JPG, JPEG, PNG, PDF Files Allowed In ID Card');

                history.back();

              </script>";

        exit;
    }

    $newIdcardName =

        "idcard_" .
        time() .
        "_" .
        rand(1000,9999) .
        "." .
        $idcardExtension;

    $idcardDestination =
        $idcardFolder . $newIdcardName;

    if (
        move_uploaded_file(
            $idcardTmpName,
            $idcardDestination
        )
    ) {

        $idcard =
            "uploads/idcards/" .
            $newIdcardName;
    }
}

// ======================================================
// INSERT STUDENT DATA
// ======================================================

$sql = "

INSERT INTO students(

fullname,
enrollment,
gender,
dob,
blood,
mobile,
email,
photo,
address,
college,
course,
department,
semester,
father,
mother,
parent_mobile,
occupation,
parent_address,
hostel_name,
emergency_contact,
status

)

VALUES(

?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?

)

";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {

    die(

        "Prepare Failed : " .

        mysqli_error($conn)
    );
}

mysqli_stmt_bind_param(

    $stmt,

    "sssssssssssssssssssss",

    $fullname,
    $enrollment,
    $gender,
    $dob,
    $blood,
    $mobile,
    $email,
    $photo,
    $address,
    $college,
    $course,
    $department,
    $semester,
    $father,
    $mother,
    $parent_mobile,
    $occupation,
    $parent_address,
    $hostel_name,
    $emergency_contact,
    $status
);

// ======================================================
// EXECUTE QUERY
// ======================================================

if (mysqli_stmt_execute($stmt)) {

    echo "<script>

            alert('Registration Request Submitted Successfully. Wait For Admin Approval.');

            window.location='../../auth/students/student_login.php';
            
          </script>";

} else {

    echo "<script>

            alert('Something Went Wrong');

            history.back();

          </script>";
}
?>