<?php
session_start();
include('connection.php'); // Replace with your connection file
require 'vendor_copy/autoload.php'; // PHPMailer autoload

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($dbconnection, $_POST['email']);

    // Check if email exists in the database
    $result = mysqli_query($dbconnection, "SELECT * FROM register1 WHERE email = '$email'");
    
    if (mysqli_num_rows($result) > 0) {
        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);

        // Update the OTP in the database for this email
        $update_otp = mysqli_query($dbconnection, "UPDATE register1 SET otp = '$otp' WHERE email = '$email'");

        // Create a new PHPMailer instance
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
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Password Reset OTP';
            $mail->Body    = 'Your OTP for password reset is: <b>' . $otp . '</b>';

            // Send the email
            if ($mail->send()) {
                $_SESSION['email'] = $email;

                // Set a success message
                $_SESSION['otp_sent'] = true;

                // Redirect to verify OTP page
                header("Location: verify_otp2.php");
                exit; // Ensure no further script execution
            } else {
                // Email sending failed
                $_SESSION['error'] = "Unable to send OTP email. Please try again.";
            }
        } catch (Exception $e) {
            // PHPMailer error
            $_SESSION['error'] = "Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        // Email not found in the database
        $_SESSION['error'] = "The email address you entered is not registered.";
    }

    // If there was an error, redirect back to the form
    if (isset($_SESSION['error'])) {
        header("Location: forgot_pass.php");
        exit; // Ensure no further script execution
    }
}
?>

<!-- HTML Form for OTP Request -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        label {
            margin-bottom: 10px;
            display: block;
            font-weight: bold;
            color: #555;
        }

        input[type="email"] {
            width: 93%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <form method="POST" action="forgot_pass.php">
        <h2>Forgot Password</h2>
        <label for="email">Enter your email:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit" name="submit">Send OTP</button>
    </form>
</body>
</html>
