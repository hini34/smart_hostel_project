<?php

$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "smart_hostel_management";
$port = 3309;

$conn = mysqli_connect($host, $user, $password, $database, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>