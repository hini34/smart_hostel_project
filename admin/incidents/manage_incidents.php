<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

// Admin login check
// if (!isset($_SESSION['admin'])) {
//     header("Location: /smart_hostel/auth/admin/admin_login.php");
//     exit();
// }

$search = trim($_GET['search'] ?? '');
$role   = trim($_GET['role'] ?? '');   // dummy for UI match
$shift  = trim($_GET['shift'] ?? '');  // dummy for UI match
$status = trim($_GET['status'] ?? '');

/* =========================
   FILTER QUERY
========================= */
$sql = "
    SELECT 
        i.*,
        s.name AS guard_name,
        s.employee_code,
        s.mobile AS guard_mobile
    FROM incidents i
    LEFT JOIN staff s ON i.reported_by = s.id
    WHERE 1=1
";

$params = [];
$types  = "";

if ($search !== '') {
    $sql .= " AND (
        i.title LIKE ? OR
        i.location LIKE ? OR
        i.incident_type LIKE ? OR
        s.name LIKE ? OR
        s.employee_code LIKE ?
    )";
    $searchLike = "%{$search}%";
    array_push($params, $searchLike, $searchLike, $searchLike, $searchLike, $searchLike);
    $types .= "sssss";
}

if ($status !== '') {
    $sql .= " AND i.status = ?";
    $params[] = $status;
    $types .= "s";
}

$sql .= " ORDER BY i.id DESC";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Query prepare failed: " . mysqli_error($conn));
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Incidents | Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        body{
            background:#eef2f6;
            font-family:"Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color:#0f172a;
        }

        .page-wrap{
            padding:32px 28px;
        }

        .top-bar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:16px;
            margin-bottom:22px;
            flex-wrap:wrap;
        }

        .page-title{
            margin:0;
            font-size:2.25rem;
            font-weight:700;
            color:#0f172a;
        }

        .btn-add{
            background:linear-gradient(135deg, #3b82f6, #2563eb);
            color:#fff;
            border:none;
            border-radius:16px;
            padding:12px 20px;
            font-weight:700;
            font-size:1rem;
            box-shadow:0 10px 24px rgba(37, 99, 235, 0.20);
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            gap:8px;
        }

        .btn-add:hover{
            color:#fff;
            transform:translateY(-1px);
        }

        .filter-card,
        .table-card{
            background:#ffffff;
            border-radius:26px;
            padding:22px;
            box-shadow:0 6px 18px rgba(15, 23, 42, 0.06);
            border:1px solid #e8edf3;
        }

        .filter-card{
            margin-bottom:24px;
        }

        .filter-grid{
            display:grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr auto auto;
            gap:14px;
            align-items:center;
        }

        .form-control,
        .form-select{
            height:62px;
            border-radius:18px;
            border:1px solid #d7dee7;
            font-size:1rem;
            padding:0 18px;
            box-shadow:none;
        }

        .form-control:focus,
        .form-select:focus{
            border-color:#94a3b8;
            box-shadow:none;
        }

        .btn-filter{
            height:62px;
            min-width:150px;
            border:none;
            border-radius:18px;
            background:#6b7280;
            color:#fff;
            font-size:1rem;
            font-weight:700;
            padding:0 22px;
        }

        .btn-reset{
            height:62px;
            min-width:138px;
            border:none;
            border-radius:18px;
            background:#f3f4f6;
            color:#111827;
            font-size:1rem;
            font-weight:700;
            display:flex;
            align-items:center;
            justify-content:center;
            text-decoration:none;
        }

        .btn-reset:hover{
            color:#111827;
            background:#e5e7eb;
        }

        .incident-table{
            width:100%;
            border-collapse:separate;
            border-spacing:0;
        }

        .incident-table thead th{
            background:#f1f5f9;
            color:#243b63;
            font-size:1rem;
            font-weight:700;
            padding:20px 20px;
            border-bottom:1px solid #dbe3ec;
            white-space:nowrap;
        }

        .incident-table thead th:first-child{
            border-top-left-radius:18px;
        }

        .incident-table thead th:last-child{
            border-top-right-radius:18px;
        }

        .incident-table tbody td{
            padding:24px 20px;
            vertical-align:middle;
            border-bottom:1px solid #e5eaf0;
            background:#fff;
        }

        .incident-main{
            display:flex;
            align-items:center;
            gap:16px;
            min-width:220px;
        }

        .incident-avatar{
            width:58px;
            height:58px;
            border-radius:50%;
            background:linear-gradient(135deg, #dbeafe, #bfdbfe);
            color:#1d4ed8;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:1.2rem;
            font-weight:700;
            flex-shrink:0;
            border:2px solid #e0ecff;
        }

        .incident-name{
            font-size:1.05rem;
            font-weight:700;
            color:#0f172a;
            margin-bottom:4px;
        }

        .incident-sub{
            color:#64748b;
            font-size:0.95rem;
            line-height:1.4;
        }

        .type-badge,
        .status-badge{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:8px 14px;
            border-radius:999px;
            font-size:0.9rem;
            font-weight:700;
            min-width:96px;
        }

        .type-fight{ background:#fee2e2; color:#b91c1c; }
        .type-noise{ background:#dbeafe; color:#1d4ed8; }
        .type-damage{ background:#fef3c7; color:#b45309; }
        .type-theft{ background:#ede9fe; color:#6d28d9; }
        .type-suspicious{ background:#e0f2fe; color:#0369a1; }

        .sev-low{ background:#dcfce7; color:#15803d; }
        .sev-medium{ background:#fef3c7; color:#b45309; }
        .sev-high{ background:#fee2e2; color:#b91c1c; }

        .status-pending{ background:#fef3c7; color:#a16207; }
        .status-review{ background:#dbeafe; color:#1d4ed8; }
        .status-resolved{ background:#dcfce7; color:#166534; }

        .contact-line{
            display:flex;
            align-items:center;
            gap:8px;
            color:#0f172a;
            margin-bottom:6px;
            font-size:0.98rem;
        }

        .contact-line:last-child{
            margin-bottom:0;
        }

        .salary-text{
            font-size:1.05rem;
            font-weight:700;
            color:#0f172a;
        }

        .date-text{
            font-size:0.98rem;
            font-weight:600;
            color:#0f172a;
        }

        .date-sub{
            color:#64748b;
            font-size:0.9rem;
            margin-top:4px;
        }

        .action-group{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .action-btn{
            width:46px;
            height:46px;
            border-radius:14px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            text-decoration:none;
            font-size:1rem;
            border:1.5px solid;
            background:#fff;
        }

        .view-btn{
            color:#2563eb;
            border-color:#2563eb;
        }

        .edit-btn{
            color:#6b7280;
            border-color:#9ca3af;
        }

        .delete-btn{
            color:#ef4444;
            border-color:#ef4444;
        }

        .action-btn:hover{
            transform:translateY(-1px);
        }

        .empty-state{
            text-align:center;
            color:#64748b;
            padding:40px 20px;
            font-size:1rem;
        }

        @media (max-width: 1200px){
            .filter-grid{
                grid-template-columns:1fr 1fr;
            }
        }

        @media (max-width: 768px){
            .page-wrap{
                padding:18px 14px;
            }

            .page-title{
                font-size:1.8rem;
            }

            .filter-grid{
                grid-template-columns:1fr;
            }

            .form-control,
            .form-select,
            .btn-filter,
            .btn-reset{
                width:100%;
            }

            .incident-table{
                min-width:1100px;
            }
        }
    </style>
</head>
<body>

<div class="page-wrap">

    <div class="top-bar">
        <h1 class="page-title">Incident Management</h1>
        <!-- <a href="../incidents/add_incident.php" class="btn-add">
            <i class="fa-solid fa-plus"></i> Add Incident
        </a> -->
    </div>

    <div class="filter-card">
        <form method="GET">
            <div class="filter-grid">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search by title, location, guard, code"
                    value="<?= htmlspecialchars($search) ?>"
                >

                <select name="role" class="form-select">
                    <option value="">All Types</option>
                    <option value="Fight">Fight</option>
                    <option value="Noise">Noise</option>
                    <option value="Damage">Damage</option>
                    <option value="Theft">Theft</option>
                    <option value="Suspicious">Suspicious</option>
                </select>

                <select name="shift" class="form-select">
                    <option value="">All Severity</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>

                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="In Review" <?= $status === 'In Review' ? 'selected' : '' ?>>In Review</option>
                    <option value="Resolved" <?= $status === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                </select>

                <button type="submit" class="btn-filter">
                    <i class="fa-solid fa-filter me-2"></i>Filter
                </button>

                <a href="manage_incidents.php" class="btn-reset">Reset</a>
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="incident-table">
                <thead>
                    <tr>
                        <th>Incident</th>
                        <th>Type / Severity</th>
                        <th>Reported By</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th style="min-width:150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <?php
                                $typeClass = 'type-noise';
                                if ($row['incident_type'] === 'Fight') $typeClass = 'type-fight';
                                elseif ($row['incident_type'] === 'Damage') $typeClass = 'type-damage';
                                elseif ($row['incident_type'] === 'Theft') $typeClass = 'type-theft';
                                elseif ($row['incident_type'] === 'Suspicious') $typeClass = 'type-suspicious';

                                $sevClass = 'sev-low';
                                if ($row['severity'] === 'Medium') $sevClass = 'sev-medium';
                                elseif ($row['severity'] === 'High') $sevClass = 'sev-high';

                                $statusClass = 'status-pending';
                                if ($row['status'] === 'In Review') $statusClass = 'status-review';
                                elseif ($row['status'] === 'Resolved') $statusClass = 'status-resolved';

                                $initial = strtoupper(substr($row['title'] ?? 'I', 0, 1));
                            ?>
                            <tr>
                                <td>
                                    <div class="incident-main">
                                        <div class="incident-avatar"><?= htmlspecialchars($initial) ?></div>
                                        <div>
                                            <div class="incident-name"><?= htmlspecialchars($row['title']) ?></div>
                                            <div class="incident-sub"><?= htmlspecialchars($row['location'] ?: 'No location') ?></div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="mb-2">
                                        <span class="type-badge <?= $typeClass ?>">
                                            <?= htmlspecialchars($row['incident_type']) ?>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="type-badge <?= $sevClass ?>">
                                            <?= htmlspecialchars($row['severity']) ?>
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <div class="contact-line">
                                        <i class="fa-solid fa-user"></i>
                                        <span><?= htmlspecialchars($row['guard_name'] ?? 'Unknown Guard') ?></span>
                                    </div>
                                    <div class="contact-line">
                                        <i class="fa-solid fa-id-badge"></i>
                                        <span><?= htmlspecialchars($row['employee_code'] ?? 'N/A') ?></span>
                                    </div>
                                </td>

                                <td>
                                    <div class="date-text">
                                        <?= !empty($row['reported_at']) ? date("d M Y", strtotime($row['reported_at'])) : 'N/A' ?>
                                    </div>
                                    <div class="date-sub">
                                        <?= !empty($row['reported_at']) ? date("h:i A", strtotime($row['reported_at'])) : '' ?>
                                    </div>
                                </td>

                                <td>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="action-group">
                                        <a href="update_incident.php?id=<?= (int)$row['id'] ?>" class="action-btn edit-btn" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="delete_incident.php?id=<?= (int)$row['id'] ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this incident?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty-state">
                                No incident records found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>