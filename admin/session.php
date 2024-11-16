<?php
session_start(); // Start session to check login status

// Check if the admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

// If logged in, you can access admin session variables, such as the username.
$admin_username = $_SESSION['admin_username'];
?>
