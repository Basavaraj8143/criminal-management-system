<?php
$host = "localhost";
$user = "root";
$password = ""; // No password by default in XAMPP
$dbname = "criminal_db";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
