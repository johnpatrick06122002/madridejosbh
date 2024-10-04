<?php
include('connection.php');

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Verify the user with the matching verification code
    $stmt = $dbconnection->prepare("UPDATE users SET is_verified = 1 WHERE verification_code = ?");
    $stmt->bind_param("s", $code);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "<script>Swal.fire('Email Verified', 'Your email has been successfully verified.', 'success');</script>";
    } else {
        echo "<script>Swal.fire('Invalid Code', 'The verification link is invalid or expired.', 'error');</script>";
    }
}
?>
