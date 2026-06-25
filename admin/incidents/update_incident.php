<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

// Admin login check
// if (!isset($_SESSION['admin'])) {
//     header("Location: /smart_hostel/auth/admin/admin_login.php");
//     exit();
// }

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die("Invalid incident ID");
}

$error = "";

// Update process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = trim($_POST['status'] ?? '');
    $admin_remark = trim($_POST['admin_remark'] ?? '');

    $allowed_status = ['Pending', 'In Review', 'Resolved'];

    if (!in_array($status, $allowed_status, true)) {
        die("Invalid status selected.");
    }

    $stmt = mysqli_prepare($conn, "
        UPDATE incidents
        SET status = ?, admin_remark = ?
        WHERE id = ?
    ");

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ssi", $status, $admin_remark, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: manage_incidents.php?updated=1");
        exit();
    } else {
        $error = "Failed to update incident: " . mysqli_stmt_error($stmt);
    }
}

// Fetch incident data
$stmt = mysqli_prepare($conn, "
    SELECT
        i.*,
        s.name AS guard_name,
        s.employee_code,
        s.mobile AS guard_mobile,
        s.role AS guard_role
    FROM incidents i
    LEFT JOIN staff s ON i.reported_by = s.id
    WHERE i.id = ?
");

if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$incident = mysqli_fetch_assoc($result);

if (!$incident) {
    die("Incident not found.");
}

$statusClass = 'status-pending';
if ($incident['status'] === 'In Review') {
    $statusClass = 'status-review';
} elseif ($incident['status'] === 'Resolved') {
    $statusClass = 'status-resolved';
}

$severityClass = 'severity-low';
if ($incident['severity'] === 'Medium') {
    $severityClass = 'severity-medium';
} elseif ($incident['severity'] === 'High') {
    $severityClass = 'severity-high';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Incident | Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        body{
            background: #eef3f8;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: #0f172a;
        }

        .wrapper{
            max-width: 1180px;
            margin: 30px auto;
            padding: 0 16px;
        }

        .top-header{
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            border-radius: 24px;
            padding: 28px 30px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .top-header::after{
            content: "";
            position: absolute;
            top: -30px;
            right: -30px;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }

        .top-header h2{
            margin: 0;
            font-weight: 700;
            font-size: 2rem;
        }

        .top-header p{
            margin: 8px 0 0;
            color: rgba(255,255,255,0.82);
            font-size: 0.98rem;
        }

        .info-card,
        .form-card{
            background: #fff;
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
            height: 100%;
        }

        .card-title{
            font-size: 1.2rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-grid{
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .detail-item{
            background: #f8fbff;
            border: 1px solid #e7eef7;
            border-radius: 16px;
            padding: 16px 18px;
        }

        .detail-item.full{
            grid-column: 1 / -1;
        }

        .detail-label{
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 8px;
        }

        .detail-value{
            font-size: 0.98rem;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.5;
            word-break: break-word;
        }

        .badge-soft{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 110px;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-pending{
            background: #fff3cd;
            color: #946200;
        }

        .status-review{
            background: #d9f2ff;
            color: #0c6d92;
        }

        .status-resolved{
            background: #d1fae5;
            color: #047857;
        }

        .severity-low{
            background: #dcfce7;
            color: #15803d;
        }

        .severity-medium{
            background: #fef3c7;
            color: #b45309;
        }

        .severity-high{
            background: #fee2e2;
            color: #b91c1c;
        }

        .form-label{
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
        }

        .form-select,
        .form-control{
            border-radius: 14px;
            border: 1px solid #dbe5f0;
            padding: 12px 14px;
            box-shadow: none;
        }

        .form-select:focus,
        .form-control:focus{
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.15rem rgba(59, 130, 246, 0.12);
        }

        textarea.form-control{
            resize: vertical;
            min-height: 140px;
        }

        .form-note{
            font-size: 0.88rem;
            color: #64748b;
            margin-top: 6px;
        }

        .btn-action{
            border-radius: 14px;
            font-weight: 600;
            padding: 10px 18px;
        }

        .btn-primary{
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.18);
        }

        .alert{
            border-radius: 14px;
        }

        @media (max-width: 991px){
            .detail-grid{
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px){
            .wrapper{
                margin: 18px auto;
            }

            .top-header{
                padding: 22px;
                border-radius: 20px;
            }

            .top-header h2{
                font-size: 1.5rem;
            }

            .info-card,
            .form-card{
                padding: 18px;
                border-radius: 18px;
            }
        }
    </style>
</head>
<body>

<div class="wrapper">

    <div class="top-header">
        <h2><i class="fa-solid fa-pen-to-square me-2 text-warning"></i>Update Incident</h2>
        <p>Review reported incident details and update its current handling status professionally.</p>
    </div>

    <div class="row g-4">
        <!-- Incident Details -->
        <div class="col-lg-7">
            <div class="info-card">
                <div class="card-title">
                    <i class="fa-solid fa-circle-info text-primary"></i>
                    Incident Details
                </div>

                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Title</div>
                        <div class="detail-value"><?= htmlspecialchars($incident['title']) ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Incident Type</div>
                        <div class="detail-value"><?= htmlspecialchars($incident['incident_type']) ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Current Status</div>
                        <div class="detail-value">
                            <span class="badge-soft <?= $statusClass ?>">
                                <?= htmlspecialchars($incident['status']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Severity</div>
                        <div class="detail-value">
                            <span class="badge-soft <?= $severityClass ?>">
                                <?= htmlspecialchars($incident['severity']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Reported By</div>
                        <div class="detail-value"><?= htmlspecialchars($incident['guard_name'] ?? 'Unknown Guard') ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Employee Code</div>
                        <div class="detail-value"><?= htmlspecialchars($incident['employee_code'] ?? 'N/A') ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Mobile Number</div>
                        <div class="detail-value"><?= htmlspecialchars($incident['guard_mobile'] ?? 'N/A') ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Role</div>
                        <div class="detail-value"><?= htmlspecialchars($incident['guard_role'] ?? 'N/A') ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Location</div>
                        <div class="detail-value"><?= htmlspecialchars($incident['location'] ?? 'N/A') ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Reported On</div>
                        <div class="detail-value">
                            <?= !empty($incident['reported_at']) ? date("d M Y, h:i A", strtotime($incident['reported_at'])) : 'N/A' ?>
                        </div>
                    </div>

                    <div class="detail-item full">
                        <div class="detail-label">Description</div>
                        <div class="detail-value"><?= nl2br(htmlspecialchars($incident['description'])) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Form -->
        <div class="col-lg-5">
            <div class="form-card">
                <div class="card-title">
                    <i class="fa-solid fa-sliders text-success"></i>
                    Update Status
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Select Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Pending" <?= ($incident['status'] === 'Pending') ? 'selected' : '' ?>>Pending</option>
                            <option value="In Review" <?= ($incident['status'] === 'In Review') ? 'selected' : '' ?>>In Review</option>
                            <option value="Resolved" <?= ($incident['status'] === 'Resolved') ? 'selected' : '' ?>>Resolved</option>
                        </select>
                        <div class="form-note">Choose the latest processing status for this incident.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Admin Remark</label>
                        <textarea 
                            name="admin_remark" 
                            class="form-control" 
                            rows="6" 
                            placeholder="Write incident handling remark here..."
                        ><?= htmlspecialchars($incident['admin_remark'] ?? '') ?></textarea>
                        <div class="form-note">Add a short note about review, action taken, or resolution details.</div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary btn-action">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Update Incident
                        </button>
                        <a href="manage_incidents.php" class="btn btn-outline-secondary btn-action">
                            <i class="fa-solid fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

</body>
</html>