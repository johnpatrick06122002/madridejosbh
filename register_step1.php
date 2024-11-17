<?php
session_start();
include('connection.php');
require 'vendor_copy/autoload.php'; // PHPMailer autoload

// Google reCAPTCHA secret key
$recaptcha_secret = '6LdEuIEqAAAAADNRqBLoTg11Lqx7yes1ieUsEOd4';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Verify reCAPTCHA
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_verify = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $recaptcha_result = json_decode($recaptcha_verify);

    if (!$recaptcha_result->success) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Captcha Failed',
                    text: 'Please complete the CAPTCHA before proceeding.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            });
        </script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Invalid Email',
                    text: 'Please enter a valid email address.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            });
        </script>";
    } elseif ($password !== $confirm_password) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Password Mismatch',
                    text: 'Passwords do not match. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            });
        </script>";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Weak Password',
                    text: 'Password must be at least 8 characters long, include numbers, letters, and at least one capital letter.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            });
        </script>";
    } else {
        // Check if email exists
        $stmt = $dbconnection->prepare("SELECT * FROM register1 WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Email Exists',
                        text: 'This email is already registered.',
                        icon: 'error',
                        confirmButtonColor: '#3085d6'
                    });
                });
            </script>";
        } else {
            // Generate OTP
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $email;
            $_SESSION['password'] = password_hash($password, PASSWORD_DEFAULT);

            // Insert into database
            $stmt = $dbconnection->prepare("INSERT INTO register1 (email, password, otp) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $_SESSION['password'], $otp);

            if ($stmt->execute()) {
                $mail = new PHPMailer\PHPMailer\PHPMailer();
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'lucklucky2100@gmail.com';
                    $mail->Password = 'kjxf ptjv erqn yygv';
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('lucklucky2100@gmail.com', 'Your Name');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Your OTP Code';
                    $mail->Body = "Your OTP code is <b>$otp</b>";

                    if($mail->send()) {
                        echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'OTP has been sent to your email address',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = 'register_otp.php';
                                    }
                                });
                            });
                        </script>";
                    }
                } catch (Exception $e) {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                title: 'OTP Failed',
                                text: 'Error sending OTP. Please try again.',
                                icon: 'error',
                                confirmButtonColor: '#3085d6'
                            });
                        });
                    </script>";
                }
            } else {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Database Error',
                            text: 'Failed to register. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#3085d6'
                        });
                    });
                </script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Step 1</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
     <style>
  body {
    background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('b.png') no-repeat center center fixed;
    background-size: 70%;
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh; /* Ensures full screen coverage */
    margin: 0;
    padding: 0;
}
@media (max-width: 768px) {
    body {
         
      
        min-height: 100vh;
         background-size:cover; 
    }
}
  
        .register-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 290px;
            margin-top: -80px;
        }
        
        .register-form h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        
        .register-form form {
            display: flex;
            flex-direction: column;
        }
        
        .register-form label {
            margin-bottom: 8px;
            color: #555;
        }
        
        .register-form input[type="email"],
        .register-form input[type="password"] {
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            position: relative; /* Keep relative for icon positioning */
            width: 100%; /* Ensure it takes full width */
            box-sizing: border-box; /* Include padding in the total width */
        }
        
        .register-form button {
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .register-form button:hover {
            background-color: #0056b3;
        }

        .input-container {
            position: relative; /* Relative positioning for absolute children */
            margin-bottom: 16px; /* Space between input fields */
        }

        .input-container i {
            position: absolute; /* Absolute positioning of the icon */
            right: 15px; /* Align icon to the right */
            top: 40%; /* Center icon vertically */
            transform: translateY(-50%); /* Adjust for exact vertical centering */
            color: #555; /* Change icon color if needed */
            cursor: pointer;
        }
        .form .message {
    margin: 15px 0 0;
    color: black !important;
    font-size: 12px;
}
    </style>	
</head>
<body>
    <div class="register-form">
        <h2>Register - Step 1</h2>
        <form id="registrationForm" method="POST">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

          <label for="password">Password</label>
            <div class="input-container">
                <input type="password" style=" width: 100%; padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            position: relative; 
            width: 100%;  
            box-sizing: border-box;" name="password" id="password" required>
                <i class="fas fa-eye" id="togglePassword"></i>
            </div>
           <label for="confirm_password">Confirm Password</label>
            <div class="input-container">
                <input type="password" style=" width: 100%; padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            position: relative; 
            width: 100%;  
            box-sizing: border-box;"name="confirm_password" id="confirm_password" required>
                <i class="fas fa-eye" id="toggleConfirmPassword"></i>
            </div>

           <!-- Google reCAPTCHA -->
<div class="g-recaptcha" 
     data-sitekey="6LdEuIEqAAAAAJp33EewtqMHDcVowUNiNrB0P51x" 
     data-callback="onCaptchaComplete">
</div>
<br>
<button type="submit" name="submit" id="submitBtn" disabled>Send OTP</button>
 <center> 
                <p class="message">Already have an account? <a href="login.php">Sign in</a></p>
                <p class="message"><a href="index.php">WebPage</a></p>
            </center>
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Captcha complete callback
    function onCaptchaComplete() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = false;
    }

    // Password visibility toggle
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordField = document.getElementById('password');
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const confirmPasswordField = document.getElementById('confirm_password');
        const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordField.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });

    // Form validation
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        // Email validation
        if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            e.preventDefault();
            Swal.fire({
                title: 'Invalid Email',
                text: 'Please enter a valid email address.',
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        // Password validation
        if (password !== confirmPassword) {
            e.preventDefault();
            Swal.fire({
                title: 'Password Mismatch',
                text: 'Passwords do not match. Please try again.',
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
    });
</script>

</body>
</html>
