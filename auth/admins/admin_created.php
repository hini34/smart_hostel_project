<?php
session_start();
require_once(__DIR__ . "/../../config/db.php");

$username = "admin";
$password = "71780747";

// password hash
$hash = password_hash($password, PASSWORD_DEFAULT);
$sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $username, $hash);
mysqli_stmt_execute($stmt);

echo "Admin Created Successfully";
?>