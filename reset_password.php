<?php
include('connection.php');
session_start();

// Check if the OTP has been verified
if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
    header('Location: verify_otp2.php');
    exit();
}

// Handle the password reset form submission
if (isset($_POST['reset'])) {
    $password = mysqli_real_escape_string($dbconnection, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($dbconnection, $_POST['confirm_password']);

    if ($password === $confirm_password) {
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $email = $_SESSION['email'];

        // Update the password in the database
        $update_password = mysqli_query($dbconnection, "UPDATE register1 SET password = '$hashed_password', otp = '' WHERE email = '$email'");

        if ($update_password) {
            // Clear session data
            unset($_SESSION['email']);
            unset($_SESSION['otp_verified']);

            // Redirect with a success message
            header('Location: login.php?reset=success');
            exit();
        } else {
            // Redirect with an error message
            header('Location: reset_password.php?error=database');
            exit();
        }
    } else {
        // Redirect with a password mismatch error
        header('Location: reset_password.php?error=mismatch');
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
      <link rel="shortcut icon" type="x-icon" href="b.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .reset-form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        .input-field {
            position: relative;
            margin: 10px 0;
        }

        input {
            width: 80%;
            padding: 12px 40px 12px 12px; /* Added left padding for icon */
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .toggle-password {
            position: absolute;
            right: 30px;
            top: 12px;
            cursor: pointer;
            color: #888;
        }

        button {
            background-color: #43A047;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #388E3C;
        }
    </style>
</head>
<body>
    <div class="reset-form">
        <h2>Reset Password</h2>
        <form action="reset_password.php" method="POST">
            <div class="input-field">
                <input type="password" name="password" placeholder="Enter new password" required id="password">
                <i class="toggle-password fas fa-eye" id="togglePassword1"></i>
            </div>
            <div class="input-field">
                <input type="password" name="confirm_password" placeholder="Confirm new password" required id="confirm_password">
                <i class="toggle-password fas fa-eye" id="togglePassword2"></i>
            </div>
            <button type="submit" name="reset">Reset Password</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toggle password visibility
        const togglePassword1 = document.getElementById('togglePassword1');
        const passwordField1 = document.getElementById('password');

        const togglePassword2 = document.getElementById('togglePassword2');
        const passwordField2 = document.getElementById('confirm_password');

        togglePassword1.addEventListener('click', function () {
            const type = passwordField1.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField1.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        togglePassword2.addEventListener('click', function () {
            const type = passwordField2.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField2.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
