<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");
$hostel_name = $_SESSION['hostel_name'] ?? '';

if (empty($hostel_name)) {
    die("Hostel not assigned");
}


$page_title  = "Visitor History";
$active_page = "visitors";

function e($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

$search = trim($_GET['search'] ?? '');

$sql = "SELECT
            v.*,
            s.fullname,
            s.enrollment,
            s.department,
            r.room_no
        FROM visitors v
        INNER JOIN students s
            ON v.student_id = s.id
        LEFT JOIN rooms r
            ON s.room_id = r.id
        WHERE s.hostel_name = ?";

$params = [$hostel_name];
$types  = "s";

if ($search !== '') {

    $sql .= " AND (
                v.name LIKE ?
                OR v.phone LIKE ?
                OR s.fullname LIKE ?
                OR s.enrollment LIKE ?
                OR r.room_no LIKE ?
             )";

    $like = "%{$search}%";

    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;

    $types .= "sssss";
}

$sql .= " ORDER BY v.id DESC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {

    error_log("Visitor History Query Error: " . mysqli_error($conn));

    $_SESSION['flash_message'] = "Unable to load visitor records.";
    $_SESSION['flash_type'] = "danger";

    header("Location: visitor_history.php");
    exit();
}

mysqli_stmt_bind_param(
    $stmt,
    $types,
    ...$params
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include(__DIR__ . "/../../includes/security/security_header.php"); ?>
    <link rel="stylesheet" href="../../assets/css/security/visitor_entry.css">
</head>
<body>
<div class="security-layout">
    <?php include(__DIR__ . "/../../includes/security/security_sidebar.php"); ?>

    <main class="security-content">
        <div class="page-header">
            <div>
                <h1>Visitor History</h1>
                <p>Security panel se visitor status aur checkout manage karo.</p>
            </div>
            <a href="visitor_entry.php" class="btn btn-primary">
            + New Visitor Entry
             </a>
            <div class="live-badge">HISTORY</div>
        </div>

        <div class="table-card">
            <form method="GET" class="row g-3 mb-3">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Search visitor, student, phone, room..." value="<?php echo e($search); ?>">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary">Search</button>
                    <a href="visitor_history.php" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle custom-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Visitor</th>
                            <th>Phone</th>
                            <th>Student</th>
                            <th>Room</th>
                            <th>Purpose</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                   <?php if (isset($result) && $result && mysqli_num_rows($result) > 0): ?>
                        <?php $i = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td>
                                    <strong><?php echo e($row['name']); ?></strong><br>
                                    <small><?php echo e($row['relation'] ?: 'N/A'); ?></small>
                                </td>
                                <td><?php echo e($row['phone']); ?></td>
                                <td>
                                    <?php echo e($row['fullname']); ?><br>
                                    <small><?php echo e($row['enrollment']); ?></small>
                                </td>
                                <td><?php echo e($row['room_no'] ?? 'N/A'); ?></td>
                                <td><?php echo e($row['purpose'] ?: '-'); ?></td>
                                <td><?php echo e($row['checkin_time']); ?></td>
                                <td><?php echo e($row['checkout_time'] ?: '---'); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                        <?php echo ucfirst(e($row['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (strtolower($row['status']) === 'approved' && empty($row['checkout_time'])): ?>
                                        <form action="visitor_checkout.php" method="POST">
                                            <input type="hidden" name="visitor_id" value="<?php echo (int)$row['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-dark">Mark Exit</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small">No Action</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-4">No visitor records found.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include(__DIR__ . "/../../includes/security/security_footer.php"); ?>
</body>
</html>