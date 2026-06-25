<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");


$hostel_name = $_SESSION['hostel_name'] ?? '';

if(empty($hostel_name)){
    header("Location: ../../auth/admin/admin_login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? "Admin";
$page_title  = "Students Requests | Admin Panel";
$active_page = "Request";

$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM students
     WHERE status = 'pending'
     AND hostel_name = ?
     ORDER BY id DESC"
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $hostel_name
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include __DIR__ . "/../../includes/admin/admin_header.php"; ?>
    <link rel="stylesheet" href="../../assets/css/student_requests.css">
</head>

<!-- Mobile Navbar -->
<nav class="navbar navbar-dark navbar-main d-lg-none fixed-top">
        <div class="container-fluid">
            <button class="btn btn-outline-light" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar">
                <i class="fa-solid fa-bars"></i>
            </button>

            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fa-solid fa-building-user me-2"></i>
                <span>Smart Hostel - Admin</span>
            </a>

            <span class="badge bg-primary rounded-pill">
                <?php echo htmlspecialchars($admin_name); ?>
            </span>
        </div>
</nav>

<?php include __DIR__ . "/../../includes/admin/admin_sidebar.php"; ?>

<body class="bg-light">

<div class="main-content">

    <div class="container-fluid py-4">

        <!-- PAGE HEADER -->

        <div class="request-header">

            <div>

                <h2 class="page-title">
                    Pending Student Requests
                </h2>

                <p class="page-subtitle">
                    Approve or reject newly registered students
                </p>

            </div>

            <div class="request-count">

                <?php echo mysqli_num_rows($result); ?>

            </div>

        </div>

        <!-- TABLE CARD -->

        <div class="card custom-card">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table align-middle custom-table">

                        <thead>

                        <tr>

                            <th>ID</th>
                            <th>Photo</th>
                            <th>Student Name</th>
                            <th>Enrollment</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Action</th>

                        </tr>

                        </thead>

                        <tbody>

                        <?php

                        if(mysqli_num_rows($result) > 0){

                            while($row = mysqli_fetch_assoc($result)){

                        ?>

                        <tr>

                            <td>

                                <span class="student-id">

                                    #<?= $row['id']; ?>

                                </span>

                            </td>

                            <td>

                                <img
                                    src="../../<?= $row['photo']; ?>"
                                    class="student-photo"
                                >

                            </td>

                            <td>

                                <div class="student-name text-nowrap">

                                    <?= $row['fullname']; ?>

                                </div>

                            </td>

                            <td>

                                <span class="badge bg-primary">

                                    <?= $row['enrollment']; ?>

                                </span>

                            </td>

                            <td>

                                <?= $row['email']; ?>

                            </td>

                            <td>

                                <?= $row['mobile']; ?>

                            </td>

                            <td>

                                <div class="action-buttons">

                                    <a href="approve_student.php?id=<?= $row['id']; ?>"
                                       class="btn btn-success btn-sm custom-btn">

                                        <i class="fa-solid fa-check"></i>

                                        Approve

                                    </a>

                                    <a href="reject_student.php?id=<?= $row['id']; ?>"
                                       class="btn btn-danger btn-sm custom-btn"

                                       onclick="return confirm('Are you sure to reject this student?')">

                                        <i class="fa-solid fa-xmark"></i>

                                        Reject

                                    </a>

                                </div>

                            </td>

                        </tr>

                        <?php

                            }

                        } else {

                            echo "

                            <tr>

                                <td colspan='7'>

                                    <div class='empty-state'>

                                        <i class='fa-solid fa-user-check'></i>

                                        <h5>
                                            No Pending Requests
                                        </h5>

                                        <p>
                                            All student requests are processed.
                                        </p>

                                    </div>

                                </td>

                            </tr>

                            ";
                        }

                        ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>
<?php include __DIR__ . '/../../includes/admin/admin_footer.php'; ?>
</body>
</html>