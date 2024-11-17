<?php
session_start();
include('../connection.php');
require '../vendor_copy/autoload.php'; // PHPMailer autoload
$msg = "";

// Check if the session variable 'email' is set
if (!isset($_SESSION['email'])) {
    header('Location: forgot_pass.php'); // Redirect if no email is found in session
    exit();
}

// Handle OTP verification
if (isset($_POST['verify'])) {
    $otp_input = mysqli_real_escape_string($dbconnection, $_POST['otp']);
    $email = $_SESSION['email'];

    // Query to verify OTP
    $result = mysqli_query($dbconnection, "SELECT * FROM admins WHERE email='$email' AND otp='$otp_input'");
    
   
    if (mysqli_num_rows($result) > 0) {
        // OTP is correct, fetch user ID and store it in the session
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['ID']; // Store the user ID in session

        // Set a session variable to trigger the SweetAlert
        $_SESSION['otp_verified'] = true;

        // Redirect to the same page
        header("Location: reset_password.php");
        exit();
    } else {
        // Display an error using SweetAlert
        $msg = "<script>
            Swal.fire({
                icon: 'error',
                title: 'Invalid OTP',
                text: 'The OTP you entered is incorrect. Please try again.'
            });
        </script>";
    }
}

// Handle OTP resend
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
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'OTP Sent',
                    text: 'A new OTP has been sent to your email!'
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to send OTP. Please try again later.'
                });
            </script>";
        }
    } catch (Exception $e) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Mailer Error: {$mail->ErrorInfo}'
            });
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Verify OTP</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .alert {
            padding: 1rem;
            border-radius: 5px;
            color: white;
            margin: 1rem 0;
            font-weight: 500;
            width: 65%;
            text-align: center;
        }

        .alert-success {
            background-color: #42ba96;
        }

        .alert-danger {
            background-color: #fc5555;
        }

        /* OTP Boxes */
        .otp-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .otp-container input {
            width: 45px;
            height: 45px;
            font-size: 18px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
            color: #333;
        }

        /* Verify Button */
        .btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 12px 20px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 10px;
            width: 200px;
            text-align: center;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        /* Resend Button */
        .resend-btn {
            font-size: 14px;
            color: #007bff;
            cursor: pointer;
        }

        #resendOtpButton:disabled {
            color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <h2>Verify OTP</h2>
    <?php echo $msg; ?>

    <!-- OTP Input Boxes -->
    <div class="otp-container">
        <input type="text" maxlength="1" class="otp-box" oninput="moveToNext(this, 'otp2')" id="otp1">
        <input type="text" maxlength="1" class="otp-box" oninput="moveToNext(this, 'otp3')" id="otp2">
        <input type="text" maxlength="1" class="otp-box" oninput="moveToNext(this, 'otp4')" id="otp3">
        <input type="text" maxlength="1" class="otp-box" oninput="moveToNext(this, 'otp5')" id="otp4">
        <input type="text" maxlength="1" class="otp-box" oninput="moveToNext(this, 'otp6')" id="otp5">
        <input type="text" maxlength="1" class="otp-box" oninput="moveToNext(this, '')" id="otp6">
    </div>

    <!-- Hidden field to hold full OTP -->
    <form action="" method="POST" onsubmit="return combineOtp();">
        <input type="hidden" name="otp" id="otp" />
        <input type="submit" name="verify" value="Verify" class="btn" />
    </form>

    <!-- Resend OTP Button -->
    <div class="resend-btn">
        <button type="button" id="resendOtpButton" onclick="resendOtp()">Resend OTP</button>
        <span id="countdown" style="display:none;"> (60)</span>
    </div>

    <script>
        // Move focus to the next input field
        function moveToNext(current, nextFieldID) {
            if (current.value.length === 1 && nextFieldID) {
                document.getElementById(nextFieldID).focus();
            }
        }

        // Combine OTP boxes into one value for submission
        function combineOtp() {
            const otp = Array.from(document.querySelectorAll('.otp-box'))
                            .map(input => input.value)
                            .join('');
            document.getElementById('otp').value = otp;
            return otp.length === 6; // Only allow form submission if OTP is complete
        }

        // Resend OTP countdown timer
        let countdown = 60;
        let resendButton = document.getElementById('resendOtpButton');
        let countdownDisplay = document.getElementById('countdown');

        function resendOtp() {
            resendButton.disabled = true;
            countdownDisplay.style.display = "inline";
            countdownDisplay.innerText = ` (${countdown})`;

            let interval = setInterval(function () {
                countdown--;
                countdownDisplay.innerText = ` (${countdown})`;
                if (countdown <= 0) {
                    clearInterval(interval);
                    resendButton.disabled = false;
                    countdownDisplay.style.display = "none";
                    countdown = 60;
                }
            }, 1000);

            // AJAX request to resend OTP
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'OTP Sent',
                        text: 'A new OTP has been sent to your email!'
                    });
                }
            };
            xhr.send("resend_otp=1");
        }

        // Trigger SweetAlert if OTP is verified
        <?php if (isset($_SESSION['otp_verified']) && $_SESSION['otp_verified']): ?>
        Swal.fire({
            icon: 'success',
            title: 'Verification Successful',
            text: 'Your OTP has been verified. Redirecting...'
        }).then(() => {
            window.location.href = "../reset_password.php";
        });
        <?php unset($_SESSION['otp_verified']); endif; ?>
    </script>
</body>

</html>
