<?php
session_start();
include('connection.php'); // Replace with your connection file
require 'vendor/autoload.php'; // PHPMailer autoload

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate email and password
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>Swal.fire('Invalid Email', 'Please enter a valid email address.', 'error');</script>";
    } elseif ($password !== $confirm_password) {
        echo "<script>Swal.fire('Password Mismatch', 'Passwords do not match. Please try again.', 'error');</script>";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
        echo "<script>Swal.fire('Weak Password', 'Password must be at least 8 characters long, include numbers, letters, and at least one capital letter.', 'error');</script>";
    } else {
        // Check if the email is already registered
        $stmt = $dbconnection->prepare("SELECT * FROM register1 WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>Swal.fire('Email Exists', 'This email is already registered.', 'error');</script>";
        } else {
            // Generate an OTP
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $email;
            $_SESSION['password'] = password_hash($password, PASSWORD_DEFAULT);

            // Insert the user data into the register1 table along with OTP
            $stmt = $dbconnection->prepare("INSERT INTO register1 (email, password, otp) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $_SESSION['password'], $otp);

            if ($stmt->execute()) {
                // Proceed to send OTP using PHPMailer
                $mail = new PHPMailer\PHPMailer\PHPMailer();
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';  // Set the SMTP server to send through
                    $mail->SMTPAuth = true;
                    $mail->Username = 'lucklucky2100@gmail.com'; // Your SMTP username
                    $mail->Password = 'kjxf ptjv erqn yygv'; // Your SMTP password
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('lucklucky2100@gmail.com', 'Your Name');
                    $mail->addAddress($email);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Your OTP Code';
                    $mail->Body    = "Your OTP code is <b>$otp</b>";

                    $mail->send();
                    // Redirect to OTP page after sending the OTP
                    header("Location: register_otp.php");
                    exit();
                } catch (Exception $e) {
                    echo "<script>Swal.fire('OTP Failed', 'Error sending OTP: {$mail->ErrorInfo}', 'error');</script>";
                }
            } else {
                echo "<script>Swal.fire('Database Error', 'Failed to register. Please try again.', 'error');</script>";
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
     <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        
        .register-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 300px;
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

        .password-container {
        position: relative; /* Make the container relative for absolute positioning of the icon */
        }

        .password-container input[type="password"] {
        padding-right: 110px; /* Add padding to the right to make space for the icon */
        box-sizing: border-box; /* Include padding in width calculation */
        }

        .password-container i {
            position: absolute; /* Position the icon absolutely */
            right: 15px; /* Align the icon to the right */ 
            top: 35%; /* Center the icon vertically */
            transform: translateY(-50%); /* Adjust the position to be exactly in the middle */
            color: #555; /* Change icon color if needed */
        }

        
    </style>	
</head>
<body>
    <div class="register-form">
        <h2>Register - Step 1</h2>
        <form action="register_step1.php" method="POST">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" name="password" id="password" required>
                <i class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></i>
            </div>

            <label for="confirm_password">Confirm Password</label>
            <div class="password-container">
                <input type="password" name="confirm_password" id="confirm_password" required>
                <i class="fas fa-eye" id="toggleConfirmPassword" style="cursor: pointer;"></i>
            </div>

            <button type="submit" name="submit">Send OTP</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
   
   <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');
        togglePassword.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        // Toggle confirm password visibility
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordField = document.getElementById('confirm_password');
        toggleConfirmPassword.addEventListener('click', function () {
            const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordField.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>