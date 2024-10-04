<?php
session_start();

// Check if login_user is not set or empty, redirect to index.php
if (!isset($_SESSION['login_user']) || empty($_SESSION['login_user'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$dbconnection = mysqli_connect("localhost", "username", "password", "database_name");

if (!$dbconnection) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
