<?php
session_start();
// Security Headers
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload"); // Enforces HTTPS
header("X-Frame-Options: SAMEORIGIN"); // Protects against clickjacking
header("X-Content-Type-Options: nosniff"); // Prevents MIME type sniffing

header("Referrer-Policy: no-referrer"); // Controls how referrer information is shared
header("Permissions-Policy: geolocation=(), microphone=(), camera=()"); // Limits access to features

include('connection.php');
require 'vendor_copy/autoload.php';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
   $recaptcha_response = $_POST['g-recaptcha-response'];

    $secret_key = "6LfqDZMqAAAAAHIZX2OriFHsibgr0XQUsqN3e85X"; // Replace with your secret key
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$recaptcha_response");
    $response_data = json_decode($response);

   if (!$response_data->success) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'reCAPTCHA Failed',
                    text: 'Please complete the reCAPTCHA.',
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
            $_SESSION['password'] = password_hash($password, PASSWORD_ARGON2I);

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
                        // Use JavaScript for redirect after showing alert
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
      <link rel="shortcut icon" type="x-icon" href="b.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --gradient-start: #6a11cb;
            --gradient-end: #2575fc;
            --text-color: #333;
            --input-bg: #f8f9fa;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('b.png') no-repeat center center fixed;
            background-size: 70%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .register-form {
            background: rgba(255, 255, 255, 0.9);
             color: black;
            padding: 40px;
            border-radius: 15px;
            width: 380px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
            margin-top: -80px;
        }

        .register-form h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .input-container {
            position: relative;
            margin-bottom: 20px;
        }

        .register-form label {
            display: block;
            margin-bottom: 8px;
            color: black;
        }

        .register-form input[type="email"],
        .register-form input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            background-color: var(--input-bg);
          
            border-radius: 8px;
            font-size: 15px;
            color: var(--text-color);
        }

        .input-container i {
            position: absolute;
            right: 15px;
            top: 70%;
            transform: translateY(-50%);
            color: black;
            cursor: pointer;
        }
  

    .register-form button:hover {
        background: #eba832;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(67, 160, 71, 0.4);
    }
        .register-form button {
            width: 100%;
            padding: 12px;
            background-color: #7272eb;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .form .message {
            margin: 15px 0 0;
            text-align: center;
            color: black;
        }

        .form .message a {
            color: black ;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            padding: 5px 10px;
            margin: 5px 0;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .form .message a:hover {
            background-color: #eba832;
        }
        .g-recaptcha {
    margin: 20px 0;
    display: flex;
    justify-content: center;
}
/* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            width: 90%;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .modal-content h2 {
            margin-bottom: 15px;
        }

        .modal-content p {
            text-align: justify;
            margin-bottom: 15px;
        }

        .modal-close {
            background-color: #7272eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="register-form">
        <h2>Register - Step 1</h2>
        <form id="registrationForm" method="POST">
            <div class="input-container">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            
            <div class="input-container">
                <label for="password">Password</label>
                <input type="password" style=" width: 100%; padding: 12px 15px; background-color: var(--input-bg);
          border-radius: 8px;
            font-size: 15px;
            color: var(--text-color);" name="password" id="password" required>
                <i class="fas fa-eye" id="togglePassword"></i>
            </div>

            <div class="input-container">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" style=" width: 100%; padding: 12px 15px; background-color: var(--input-bg);
          border-radius: 8px;
            font-size: 15px;
            color: var(--text-color);" name="confirm_password" id="confirm_password" required>
                <i class="fas fa-eye" id="toggleConfirmPassword"></i>
            </div>
             <div class="input-container">
                <label>
                    <input type="checkbox" id="termsCheckbox">
                    I agree to the <a href="#" id="openModal">Terms and Conditions</a>.
                </label>
            </div>
          
            <button type="submit" name="submit">Send OTP</button>
            
            <div class="form">
                <div class="message">
                    Already have an account? 
                    <a href="login.php" onclick="window.location.href='login.php'; return false;">Sign in</a>
                </div>
                <div class="message">
                    <a href="index.php" onclick="window.location.href='index.php'; return false;">WebPage</a>
                </div>
            </div>
        </form>
    </div>

      <!-- Modal -->
    <div class="modal" id="termsModal">
        <div class="modal-content">
            <h1>Terms and Conditions</h1>
        <p>
            Welcome to Madridejos Boarding House Finder! By using this platform, you agree to comply with and be bound by the following terms and conditions of use, which together with our privacy policy govern the relationship between you and our application.
        </p>
        
        <h2>1. Acceptance of Terms</h2>
        <p>
            By accessing or using our services, you agree to be bound by these terms. If you disagree with any part of these terms, please do not use our services.
        </p>

        <h2>2. Use of Services</h2>
        <p>
            Our platform allows you to search for and register with boarding houses in the Madridejos area. You agree to use the platform responsibly and not to engage in any behavior that could harm the platform, its users, or its reputation.
        </p>

        <h2>3. Account Registration</h2>
        <p>
            When registering an account, you must provide accurate information and maintain the security of your account. We are not responsible for unauthorized access due to your failure to secure your credentials.
        </p>

        <h2>4. Privacy Policy</h2>
        <p>
            Your use of our services is also governed by our Privacy Policy. Please review it to understand how we collect and handle your information.
        </p>

        <h2>5. Limitations of Liability</h2>
        <p>
            We are not responsible for any loss, damage, or inconvenience caused by the use of our platform, including issues arising from third-party boarding house services.
        </p>

        <h2>6. Changes to Terms</h2>
        <p>
            We reserve the right to update these terms at any time. It is your responsibility to review these terms periodically for changes.
        </p>

        <p>
            For more information, contact us at <a href="mailto:support@madridejosfinder.com">support@madridejosfinder.com</a>.
        </p>     
            <button class="modal-close" id="closeModal">Close</button>
        </div>
    </div>

<script src="https://www.google.com/recaptcha/api.js?render=6LfqDZMqAAAAAKD9P-4OFpmmraeL52jsWoIFs322"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
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

        // Terms and Conditions Modal
        const termsModal = document.getElementById('termsModal');
        const openModalBtn = document.getElementById('openModal');
        const closeModalBtn = document.getElementById('closeModal');

        openModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            termsModal.style.display = 'flex';
        });

        closeModalBtn.addEventListener('click', function() {
            termsModal.style.display = 'none';
        });

        // Form submission and validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Check terms acceptance
            if (!document.getElementById('termsCheckbox').checked) {
                Swal.fire({
                    title: 'Terms Required',
                    text: 'Please accept the terms and conditions to continue.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Get form values
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Email validation
            if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
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
                Swal.fire({
                    title: 'Password Mismatch',
                    text: 'Passwords do not match. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            if (!password.match(/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/)) {
                Swal.fire({
                    title: 'Weak Password',
                    text: 'Password must be at least 8 characters long, include numbers, letters, and at least one capital letter.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Show loading indicator
            Swal.fire({
                title: 'Verifying...',
                text: 'Please wait',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Execute reCAPTCHA
            grecaptcha.execute('6LfqDZMqAAAAAKD9P-4OFpmmraeL52jsWoIFs322', { action: 'submit' })
                .then(function(token) {
                    // Close loading indicator
                    Swal.close();
                    
                    // Add the token to form
                    const form = document.getElementById('registrationForm');
                    const recaptchaInput = document.createElement('input');
                    recaptchaInput.type = 'hidden';
                    recaptchaInput.name = 'g-recaptcha-response';
                    recaptchaInput.value = token;
                    form.appendChild(recaptchaInput);
                    
                    // Submit the form
                    form.submit();
                })
                .catch(function(error) {
                    console.error('reCAPTCHA error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'There was a problem verifying reCAPTCHA. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#3085d6'
                    });
                });
        });
    </script>
</body>
</html>