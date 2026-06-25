<?php
require_once(__DIR__ . "/../../config/db.php");

// Session messages
$msg   = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';

unset($_SESSION['msg'], $_SESSION['error']);

// =========================
// TOTAL COLLECTED + PENDING
// =========================
$total_collected = 0;
$total_pending   = 0;
$month_collected = 0;

$query = "SELECT 
            COALESCE(SUM(paid_amount), 0) AS collected,
            COALESCE(SUM(amount - paid_amount), 0) AS pending
          FROM student_fees";

$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $total_collected = floatval($row['collected'] ?? 0);
    $total_pending   = floatval($row['pending'] ?? 0);
}

// =========================
// THIS MONTH COLLECTION
// =========================
$current_month = date('Y-m');

$query = "SELECT COALESCE(SUM(paid_amount), 0) AS collected
          FROM student_fees
          WHERE payment_date IS NOT NULL
          AND DATE_FORMAT(payment_date, '%Y-%m') = '$current_month'";

$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $month_collected = floatval($row['collected'] ?? 0);
}

// =========================
// RECENT FEE RECORDS
// =========================
$fees_list = [];

$query = "SELECT * FROM student_fees ORDER BY id DESC LIMIT 50";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $fees_list[] = $row;
    }
}