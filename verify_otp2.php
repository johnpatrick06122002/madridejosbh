<?php
session_start();
include('connection.php');
require 'vendor_copy/autoload.php'; // PHPMailer autoload
$msg = "";

// Check if the session variable 'email' is set
if (!isset($_SESSION['email'])) {
    header('Location: forgot_pass.php'); // Redirect if no email is found in session
    exit();
}

if (isset($_POST['verify'])) {
    // Sanitize user input
    $otp_input = mysqli_real_escape_string($dbconnection, $_POST['otp']);
    $email = $_SESSION['email'];

    // Query to verify OTP
    $result = mysqli_query($dbconnection, "SELECT * FROM register1 WHERE email='$email' AND otp='$otp_input'");

    if (mysqli_num_rows($result) > 0) {
        // OTP is correct, fetch user ID and store it in the session
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['ID']; // Store the user ID in session

        // Set a session variable to trigger the SweetAlert
        $_SESSION['otp_verified'] = true;

        // Redirect to the reset password page
        header("Location: reset_password.php");
        exit();
    } else {
        $msg = "<div class='alert alert-danger'>Invalid OTP code. Please try again.</div>";
    }
}

if (isset($_POST['resend_otp'])) {
    $email = $_SESSION['email'];
    $new_otp = rand(100000, 999999); // Generate a new OTP

    // Update the new OTP in the database
    $update_otp = mysqli_query($dbconnection, "UPDATE register1 SET otp = '$new_otp' WHERE email = '$email'");

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
        $mail->Subject = 'Your OTP Code';
        $mail->Body = "Your new OTP code is: $new_otp";

        if ($mail->send()) {
            $msg = "<div class='alert alert-success'>New OTP sent to your email!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Failed to send OTP. Please try again later.</div>";
        }
    } catch (Exception $e) {
        $msg = "<div class='alert alert-danger'>Mailer Error: {$mail->ErrorInfo}</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Verify OTP</title>
    <style>
        .alert {
            padding: 1rem;
            border-radius: 5px;
            color: white;
            margin: 1rem 0;
            font-weight: 500;
            width: 65%;
        }

        .alert-success {
            background-color: #42ba96;
        }

        .alert-danger {
            background-color: #fc5555;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .input-field {
            position: relative;
            margin-bottom: 20px;
        }

        .input-field input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 93%;
        }

        .btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 12px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .resend-btn {
            display: inline-block;
            margin-top: 10px;
            font-size: 14px;
            color: #007bff;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="" method="POST">
            <h2 class="title">Verify OTP</h2>
            <?php echo $msg; ?>
            <div class="input-field">
                <input type="text" name="otp" placeholder="Enter 6-digit code" required />
            </div>
            <input type="submit" name="verify" value="Verify" class="btn" />
            <div class="resend-btn">
                <button type="button" id="resendOtpButton" onclick="resendOtp()">Resend OTP</button>
                <span id="countdown" style="display:none;"> (60)</span>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let countdown = 60; // 60 seconds countdown
        let resendButton = document.getElementById('resendOtpButton');
        let countdownDisplay = document.getElementById('countdown');

        function resendOtp() {
            resendButton.disabled = true; // Disable the button
            countdownDisplay.style.display = "inline"; // Show the countdown display
            countdownDisplay.innerText = ` (${countdown})`; // Show initial countdown value

            let interval = setInterval(function() {
                countdown--;
                countdownDisplay.innerText = ` (${countdown})`; // Update countdown display
                if (countdown <= 0) {
                    clearInterval(interval);
                    resendButton.disabled = false; // Re-enable the button
                    countdownDisplay.style.display = "none"; // Hide the countdown display
                    countdown = 60; // Reset countdown
                }
            }, 1000);

            // AJAX request to resend OTP
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Handle response
                    alert("New OTP sent to your email!"); // Show success message
                }
            };
            xhr.send("resend_otp=1"); // Send request to PHP script
        }
    </script>
</body>
</html>
