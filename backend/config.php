<?php
$host = 'localhost';
$db   = 'smartwastehub_db';
$user = 'root';
$pass = ''; // XAMPP default: empty password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

session_start();
?>
