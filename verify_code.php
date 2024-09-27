<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredCode = $_POST['verification_code'];

    if ($enteredCode == $_SESSION['verification_code']) {
        echo 'Verification successful!';
        // Proceed with the next steps, like registering the user or logging them in
    } else {
        echo 'Invalid verification code. Please try again.';
    }
}
?>
