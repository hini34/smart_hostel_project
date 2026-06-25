<?php
require_once(__DIR__ . "/../../config/db.php");
session_start();
$hostel_name = $_SESSION['hostel_name'] ?? '';

$admin_name = $_SESSION['admin_name'] ?? "Admin";
$page_title  = "Students Management | Admin Panel";
$active_page = "students";

$search = trim($_GET['search'] ?? '');

/* =========================
   MAIN QUERY (FIXED + JOIN)
========================= */
$sql = "
SELECT s.*, r.room_no
FROM students s
LEFT JOIN rooms r ON s.room_id = r.id
WHERE s.status = 'approved'
AND s.hostel_name = ?
";

$params = [$hostel_name];
$types  = "s";

if ($search !== '') {

    $sql .= " AND (
        s.fullname LIKE ? OR
        s.enrollment LIKE ? OR
        s.mobile LIKE ? OR
        s.course LIKE ? OR
        s.department LIKE ? OR
        s.email LIKE ? OR
        s.gender LIKE ? OR
        r.room_no LIKE ?
    )";

    $like = "%{$search}%";

    $params = [
        $hostel_name,
        $like,
        $like,
        $like,
        $like,
        $like,
        $like,
        $like,
        $like
    ];

    $types = "s" . str_repeat("s", 8);
}

$sql .= " ORDER BY s.id DESC";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt && !empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

/* =========================
   DATA STORE
========================= */
$students = [];
while ($row = mysqli_fetch_assoc($res)) {
    $students[] = $row;
}

/* =========================
   PHOTO FUNCTION
========================= */
function getStudentPhotoPath($photo)
{
    $photo = trim((string)$photo);

    $default = "../../assets/images/default.png";

    if ($photo === '') return $default;

    $file = __DIR__ . "/../../uploads/students/" . basename($photo);

    return file_exists($file) ? "../../uploads/students/" . basename($photo) : $default;
}
?>

<!doctype html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../../includes/admin/admin_header.php'; ?>
    <link rel="stylesheet" href="../../assets/css/admin_student.css">
</head>

<body>

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

<div class="content-wrapper">

    <!-- HEADER -->
    <div class="page-header">
        <h1>Students Management</h1>
        <p>Only approved students are shown</p>
    </div>

    <!-- SEARCH -->
    <div class="search-card mb-3">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control"
                placeholder="Search student..."
                value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary">Search</button>
            <a href="student.php" class="btn btn-secondary">Reset</a>
        </form>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Enrollment</th>
                    <th>Course</th>
                    <th>Department</th>
                    <th>Mobile</th>
                    <th>Room</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php if (count($students) > 0): ?>
                <?php $i = 1; ?>
                <?php foreach ($students as $row): ?>

                    <tr>
                        <td><?php echo $i++; ?></td>

                        <td data-label="Photo">
                            <img src="<?php echo getStudentPhotoPath($row['photo']); ?>"
                                width="50" height="50" style="border-radius:50%">
                        </td>
                        <td data-label="Name">
                        <?php echo htmlspecialchars($row['fullname']); ?><br>
                        <small><?php echo htmlspecialchars($row['email']); ?></small>
                    </td>

                    <td data-label="Enrollment"><?php echo htmlspecialchars($row['enrollment']); ?></td>

                    <td data-label="Course"><?php echo htmlspecialchars($row['course']); ?></td>

                    <td data-label="Department"><?php echo htmlspecialchars($row['department']); ?></td>

                    <td data-label="Mobile"><?php echo htmlspecialchars($row['mobile']); ?></td>

                    <td data-label="Room">
                        <?php echo $row['room_no'] ?? 'Not Assigned'; ?>
                    </td>

                    <td data-label="Status">
                        <span class="badge bg-success">Approved</span>
                    </td>

                    <td data-label="Action">
                        <a href="student_view.php?id=<?php echo $row['id']; ?>"
                        class="btn btn-warning btn-sm">View</a>

                        <a href="student_delete.php?id=<?php echo $row['id']; ?>"
                        onclick="return confirm('Delete student?')"
                        class="btn btn-danger btn-sm">Delete</a>
                    </td>
                    </tr>

                <?php endforeach; ?>
            <?php else: ?>

                <tr>
                    <td colspan="10" class="text-center">
                        No approved students found
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>

<?php include __DIR__ . '/../../includes/admin/admin_footer.php'; ?>
</body>
</html>