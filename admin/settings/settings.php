<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$hostel_name = $_SESSION['hostel_name'] ?? '';

// Optional admin login check
// if (!isset($_SESSION['admin'])) {
//     header("Location: ../adLogin.php");
//     exit;
// }

date_default_timezone_set('Asia/Kolkata');

$page_title  = "Settings | Smart Hostel";
$active_page = "settings";
$currentSessionUsername = $_SESSION['admin'] ?? '';
$admin_name = $_SESSION['admin_name'] ?? $currentSessionUsername ?? 'Admin';

$message = "";
$message_type = "";

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// ---------- ADMIN DATA LOAD ---------- //
$admin = null;

if ($currentSessionUsername !== '') {
        $sql = "
        SELECT id, username, password, hostel_name
        FROM admins
        WHERE username = ?
        AND hostel_name = ?
        LIMIT 1
        ";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "ss",
            $currentSessionUsername,
            $hostel_name
        );

    if ($stmt) {
        // mysqli_stmt_bind_param($stmt, "s", $currentSessionUsername);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $admin = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    } else {
        $message = "Database error while loading admin data.";
        $message_type = "danger";
    }
}

if (!$admin && $message === "") {
    $message = "Admin record not found in database.";
    $message_type = "danger";
}

// ---------- FORM SUBMIT ---------- //
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $admin) {
    $new_username     = trim($_POST['new_username'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_username === "" || $current_password === "" || $new_password === "" || $confirm_password === "") {
        $message = "Please fill all fields.";
        $message_type = "danger";
    } elseif (strlen($new_username) < 3) {
        $message = "Username must be at least 3 characters long.";
        $message_type = "danger";
    } elseif (strlen($new_password) < 6) {
        $message = "New password must be at least 6 characters long.";
        $message_type = "danger";
    } elseif ($new_password !== $confirm_password) {
        $message = "New password and confirm password do not match.";
        $message_type = "danger";
    } else {
        if (!password_verify($current_password, $admin['password'])) {
            $message = "Current password is incorrect.";
            $message_type = "danger";
        } else {
            $checkSql = "SELECT id FROM admins WHERE username = ? AND hostel_name = ?  AND id != ?";
            $checkStmt = mysqli_prepare($conn, $checkSql);

            if ($checkStmt) {
               mysqli_stmt_bind_param(
                $checkStmt,
                "ssi",
                $new_username,
                $hostel_name,
                $admin['id']
            );
                mysqli_stmt_execute($checkStmt);
                $checkResult = mysqli_stmt_get_result($checkStmt);
                $exists = mysqli_num_rows($checkResult) > 0;
                mysqli_stmt_close($checkStmt);

                if ($exists) {
                    $message = "This username is already taken. Please choose another.";
                    $message_type = "danger";
                } else {
                    $hash = password_hash($new_password, PASSWORD_DEFAULT);

                    $updateSql = "UPDATE admins SET username = ?, password = ? WHERE id = ?";
                    $updateStmt = mysqli_prepare($conn, $updateSql);

                    if ($updateStmt) {
                        mysqli_stmt_bind_param($updateStmt, "ssi", $new_username, $hash, $admin['id']);

                        if (mysqli_stmt_execute($updateStmt)) {
                            $message = "Username and password updated successfully.";
                            $message_type = "success";

                            $_SESSION['admin'] = $new_username;
                            $_SESSION['admin_name'] = $new_username;

                            $admin['username'] = $new_username;
                            $admin['password'] = $hash;
                            $admin_name = $new_username;
                            $currentSessionUsername = $new_username;
                        } else {
                            $message = "Unable to update account. Please try again.";
                            $message_type = "danger";
                        }

                        mysqli_stmt_close($updateStmt);
                    } else {
                        $message = "Database error while preparing update query.";
                        $message_type = "danger";
                    }
                }
            } else {
                $message = "Database error while checking username.";
                $message_type = "danger";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include(__DIR__ . "/../../includes/admin/admin_header.php"); ?>
    <link rel="stylesheet" href="../../assets/css/admin_settings.css">
</head>
<body>

<?php include(__DIR__ . "/../../includes/admin/admin_sidebar.php"); ?>

<!-- Mobile Top Navbar -->
<nav class="navbar navbar-dark navbar-main d-lg-none fixed-top">
    <div class="container-fluid">
        <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar">
            <i class="fa-solid fa-bars"></i>
        </button>

        <a class="navbar-brand d-flex align-items-center" href="#">
            <i class="fa-solid fa-building-user me-2"></i>
            <span>Smart Hostel - Admin</span>
        </a>

        <span class="badge bg-primary rounded-pill">
            <?php echo e($admin_name); ?>
        </span>
    </div>
</nav>

<main class="page-wrapper">
    <div class="container-fluid p-3 p-md-4">
        <div class="settings-header mb-4">
            <div>
                <h1 class="page-title">
                    <i class="fa-solid fa-gear me-2"></i>Account Settings
                </h1>
                <p class="page-subtitle mb-0">
                    Update your admin username and password securely.
                </p>
            </div>
        </div>

        <?php if ($message !== ""): ?>
            <div class="alert alert-<?php echo e($message_type); ?> alert-dismissible fade show" role="alert">
                <?php echo e($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-12 col-lg-4">
                <div class="card profile-card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="profile-avatar mx-auto mb-3">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <h4 class="profile-name mb-1">
                            <?php echo e($admin['username'] ?? $currentSessionUsername ?: 'Admin'); ?>
                        </h4>
                        <p class="profile-role mb-3">Administrator Account</p>

                        <div class="profile-meta">
                            <div class="meta-box">
                                <span class="meta-label">Current Username</span>
                                <span class="meta-value"><?php echo e($admin['username'] ?? $currentSessionUsername ?: 'N/A'); ?></span>
                            </div>
                            <div class="meta-box">
                                <span class="meta-label">Security</span>
                                <span class="meta-value text-success">Password Protected</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <div class="card settings-card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="mb-1 fw-bold">Update Login Credentials</h5>
                        <p class="text-muted mb-0 small">
                            Make sure your new details are secure and easy to remember.
                        </p>
                    </div>

                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Current Username</label>
                                <input
                                    type="text"
                                    class="form-control form-control-custom"
                                    value="<?php echo e($admin['username'] ?? $currentSessionUsername); ?>"
                                    disabled
                                >
                                <div class="form-text">This is your existing admin username.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">New Username</label>
                                <input
                                    type="text"
                                    name="new_username"
                                    class="form-control form-control-custom"
                                    value="<?php echo e($admin['username'] ?? ''); ?>"
                                    placeholder="Enter new username"
                                    required
                                >
                            </div>

                            <div class="section-divider">
                                <span>Password Update</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Current Password</label>
                                    <input
                                        type="password"
                                        name="current_password"
                                        class="form-control form-control-custom"
                                        placeholder="Enter current password"
                                        required
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">New Password</label>
                                    <input
                                        type="password"
                                        name="new_password"
                                        class="form-control form-control-custom"
                                        placeholder="Enter new password"
                                        required
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Confirm New Password</label>
                                    <input
                                        type="password"
                                        name="confirm_password"
                                        class="form-control form-control-custom"
                                        placeholder="Confirm new password"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="mt-4 d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary btn-save px-4">
                                    <i class="fa-solid fa-floppy-disk me-2"></i>Save Changes
                                </button>

                                <button type="reset" class="btn btn-light btn-reset px-4">
                                    <i class="fa-solid fa-rotate-left me-2"></i>Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>