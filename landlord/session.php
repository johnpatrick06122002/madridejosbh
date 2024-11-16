<?php
// Start session only if it hasn't started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_check = $_SESSION['login_user'];

$ses_sql = mysqli_query($dbconnection, "SELECT * FROM register1 WHERE email = '$user_check'");

$row = mysqli_fetch_array($ses_sql, MYSQLI_ASSOC);

$login_session = $row['id'];

   
   // if(!isset($_SESSION['login_user'])){
   //    header("location:index.php");
   //    die();
   // }
?>
