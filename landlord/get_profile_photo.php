<?php
session_start();
require 'connection.php'; // Ensure this file contains your database connection setup

$login_session = $_SESSION['login_id']; // Or however you get the logged-in user ID

$query = "SELECT profile_photo FROM landlords WHERE id = ?";
$stmt = $dbconnection->prepare($query);
$stmt->bind_param("i", $login_session);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo $row['profile_photo'];
} else {
    // Default profile photo path or handle error as needed
    echo 'default_profile_photo.jpg';
}
$stmt->close();
$dbconnection->close();
?>
