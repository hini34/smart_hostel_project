<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: fees.php");
    exit;
}

$action = $_POST['action'] ?? '';

// =========================
// 1. ASSIGN NEW FEE
// =========================
if ($action === 'assign_fee') {
    $student_id  = intval($_POST['student_id'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $amount      = floatval($_POST['amount'] ?? 0);
    $due_date    = trim($_POST['due_date'] ?? '');

    if ($student_id > 0 && $category_id > 0 && $amount > 0 && $due_date !== '') {
        $paid_amount = 0.00;
        $status = 'Unpaid';
        $payment_date = null;
        $payment_mode = null;
        $transaction_no = null;
        $remarks = null;

        $stmt = mysqli_prepare($conn, "INSERT INTO student_fees 
            (student_id, category_id, amount, paid_amount, due_date, status, payment_date, payment_mode, transaction_no, remarks)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        mysqli_stmt_bind_param(
            $stmt,
            "iiddssssss",
            $student_id,
            $category_id,
            $amount,
            $paid_amount,
            $due_date,
            $status,
            $payment_date,
            $payment_mode,
            $transaction_no,
            $remarks
        );

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['msg'] = "Fee assigned successfully.";
        } else {
            $_SESSION['error'] = "Failed to assign fee.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error'] = "Please fill Student ID, Category ID, Amount and Due Date properly.";
    }

    header("Location: fees.php");
    exit;
}

// =========================
// 2. RECORD PAYMENT
// =========================
if ($action === 'record_payment') {
    $fee_id         = intval($_POST['fee_id'] ?? 0);
    $pay_amount     = floatval($_POST['pay_amount'] ?? 0);
    $payment_mode   = trim($_POST['payment_mode'] ?? '');
    $transaction_no = trim($_POST['transaction_no'] ?? '');
    $remarks        = trim($_POST['remarks'] ?? '');

    if ($fee_id <= 0 || $pay_amount <= 0) {
        $_SESSION['error'] = "Please enter valid fee ID and payment amount.";
        header("Location: fees.php");
        exit;
    }

    $stmt = mysqli_prepare($conn, "SELECT amount, paid_amount FROM student_fees WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $fee_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $fee = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$fee) {
        $_SESSION['error'] = "Fee record not found.";
        header("Location: fees.php");
        exit;
    }

    $current_paid = floatval($fee['paid_amount']);
    $total_amount = floatval($fee['amount']);
    $new_paid = $current_paid + $pay_amount;

    if ($new_paid >= $total_amount) {
        $new_paid = $total_amount;
        $status = 'Paid';
    } elseif ($new_paid > 0) {
        $status = 'Partially Paid';
    } else {
        $status = 'Unpaid';
    }

    $payment_date = date('Y-m-d');

    $stmt = mysqli_prepare($conn, "UPDATE student_fees 
        SET paid_amount = ?, status = ?, payment_date = ?, payment_mode = ?, transaction_no = ?, remarks = ?
        WHERE id = ?");

    mysqli_stmt_bind_param(
        $stmt,
        "dsssssi",
        $new_paid,
        $status,
        $payment_date,
        $payment_mode,
        $transaction_no,
        $remarks,
        $fee_id
    );

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['msg'] = "Payment recorded successfully.";
    } else {
        $_SESSION['error'] = "Failed to record payment.";
    }

    mysqli_stmt_close($stmt);

    header("Location: fees.php");
    exit;
}

// =========================
// INVALID ACTION
// =========================
$_SESSION['error'] = "Invalid action.";
header("Location: fees.php");
exit;