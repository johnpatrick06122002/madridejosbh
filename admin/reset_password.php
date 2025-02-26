<?php
include('../connection.php');
session_start();

// Check if the OTP has been verified
if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
    header('Location: verify_otp.php');
    exit();
}

// Handle the password reset form submission
if (isset($_POST['reset'])) {
    $password = mysqli_real_escape_string($dbconnection, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($dbconnection, $_POST['confirm_password']);
    // Password validation regex
    $passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

    if (!preg_match($passwordRegex, $password)) {
        header('Location: reset_password.php?error=invalid_password');
        exit();
    }
    if ($password === $confirm_password) {
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_ARGON2I);
        $email = $_SESSION['email'];

        // Update the password in the database
        $update_password = mysqli_query($dbconnection, "UPDATE admins SET password = '$hashed_password', otp = '' WHERE email = '$email'");

        if ($update_password) {
            // Clear session data
            unset($_SESSION['email']);
            unset($_SESSION['otp_verified']);
            
            // Set a success flag in the session
            $_SESSION['password_reset_success'] = true;
            header('Location: reset_password.php?status=success');
            exit();
        } else {
            header('Location: reset_password.php?error=database');
            exit();
        }
    } else {
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
    <!-- Load SweetAlert2 CSS and JS in the head -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Your existing styles remain the same -->
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #3a7bd5;
            --background-color: #f4f7f6;
            --text-color: #2c3e50;
            --border-radius: 12px;
        }

        * {
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', Arial, sans-serif;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(99, 102, 241, 0.1)), url('../b.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: var(--text-color);
        }

        .reset-form {
            background-color: white;
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            width: 450px;
            text-align: center;
        }

        h2 {
            margin-bottom: 30px;
            color: var(--primary-color);
            font-weight: 600;
        }

        .input-field {
            position: relative;
            margin: 20px 0;
        }

        input {
            width: 100%;
            padding: 15px 50px 15px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f9f9f9;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: var(--primary-color);
        }

        button {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 15px;
            border-radius: var(--border-radius);
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
            transform: translateY(0);
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
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

    <script>
        // Handle success and error messages
        document.addEventListener('DOMContentLoaded', function() {
            <?php
            if (isset($_GET['status']) && $_GET['status'] === 'success') {
                echo "
                Swal.fire({
                    icon: 'success',
                    title: 'Password Reset Successful',
                    text: 'Your password has been reset successfully! Redirecting to the login page...',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                }).then(() => {
                    window.location = 'index.php';
                });
                ";
            }
            
            if (isset($_GET['error'])) {
                $error = $_GET['error'];
                if ($error === 'invalid_password') {
                    echo "
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Password',
                        text: 'Password must be at least 8 characters long, include at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.',
                    });
                    ";
                } elseif ($error === 'mismatch') {
                    echo "
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Mismatch',
                        text: 'Passwords do not match. Please try again.',
                    });
                    ";
                } elseif ($error === 'database') {
                    echo "
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update password. Please try again later.',
                    });
                    ";
                }
            }
            ?>
        });

        // Your existing password toggle and form validation code
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

        // Form validation
        document.querySelector('form').addEventListener('submit', function (e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            // Password validation regex
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (!passwordRegex.test(password)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Password',
                    text: 'Password must be at least 8 characters long, include at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.',
                });
                return;
            }

            if (password !== confirmPassword) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'Passwords do not match. Please try again.',
                });
            }
        });
    </script>
</body>
</html>