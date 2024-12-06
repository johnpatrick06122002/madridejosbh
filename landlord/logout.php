<?php
session_start();
include('../connection.php');

// Clear session token in the database (optional)
$login_user = $_SESSION['login_user'] ?? null;

if ($login_user) {
    $stmt = $dbconnection->prepare("UPDATE register1 SET session_token = NULL WHERE email = ?");
    $stmt->bind_param("s", $login_user);
    $stmt->execute();
}

// Destroy the session
$_SESSION = [];
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <!-- Include SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>

<!-- Include SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Logged out!',
        text: 'You have been logged out successfully.',
        showConfirmButton: false,
        timer: 1500
    }).then(() => {
        window.location.href = '../index.php';
    });
</script>

<noscript>
    <p>JavaScript is required to use this page. You have been logged out. Please <a href="../index.php">click here</a> to go back to the home page.</p>
</noscript>

</body>
</html>
