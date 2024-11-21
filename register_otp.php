<?php
session_start();
include('connection.php');
require 'vendor_copy/autoload.php'; // PHPMailer autoload
$msg = "";

// Check if the session variable 'email' is set
if (!isset($_SESSION['email'])) {
    header('Location: register_step1.php'); // Redirect if no email is found in session
    exit();
}

// Handle OTP verification
if (isset($_POST['verify'])) {
    $otp_input = mysqli_real_escape_string($dbconnection, $_POST['otp']);
    $email = $_SESSION['email'];

    // Query to verify OTP
    $result = mysqli_query($dbconnection, "SELECT * FROM register1 WHERE email='$email' AND otp='$otp_input'");
    
    if (mysqli_num_rows($result) > 0) {
        // OTP is correct, mark the user as verified
        mysqli_query($dbconnection, "UPDATE register1 SET verification = 1 WHERE email='$email'");
        
        // Fetch user ID and store it in the session
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['ID'];
        
        // Set a session variable to trigger the SweetAlert
        $_SESSION['otp_verified'] = true;

        // Redirect to the same page
        header("Location: register_step2.php");
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
    <title>Verify OTP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 15px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .verify-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 30px;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .otp-input-container {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        .otp-input {
            width: 45px;
            height: 45px;
            text-align: center;
            font-size: 18px;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .otp-input:focus {
            border-color: #2575fc;
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 117, 252, 0.1);
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #6a11cb 0%, #2575fc 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        #countdown {
            color: #666;
            font-size: 14px;
        }

        /* Mobile Responsiveness */
        @media screen and (max-width: 480px) {
            .verify-container {
                padding: 20px;
                max-width: 100%;
                margin: 0;
            }
            .otp-input-container {
                gap: 5px;
            }
            .otp-input {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
            h2 {
                font-size: 1.5rem;
                margin-bottom: 15px;
            }
            .btn {
                padding: 10px;
                font-size: 15px;
            }
        }

        @media screen and (max-width: 320px) {
            .otp-input-container {
                gap: 3px;
            }
            .otp-input {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <h2>Verify OTP</h2>
        <?php echo $msg; ?>

        <form action="" method="POST" id="otpForm">
            <div class="otp-input-container">
                <input type="text" class="otp-input" maxlength="1" pattern="\d*" inputmode="numeric">
                <input type="text" class="otp-input" maxlength="1" pattern="\d*" inputmode="numeric">
                <input type="text" class="otp-input" maxlength="1" pattern="\d*" inputmode="numeric">
                <input type="text" class="otp-input" maxlength="1" pattern="\d*" inputmode="numeric">
                <input type="text" class="otp-input" maxlength="1" pattern="\d*" inputmode="numeric">
                <input type="text" class="otp-input" maxlength="1" pattern="\d*" inputmode="numeric">
                <input type="hidden" name="otp" id="hiddenOtpInput">
            </div>
            <button type="submit" name="verify" class="btn">Verify</button>
        </form>

        <form action="" method="POST">
            <button type="submit" name="resend_otp" id="resendOtpButton" class="btn">Resend OTP</button>
            <span id="countdown"></span>
        </form>
    </div>

    <script>
        // Previous countdown script
        let countdown = 60;
        let resendButton = document.getElementById('resendOtpButton');
        let countdownDisplay = document.getElementById('countdown');
        
        resendButton.addEventListener('click', function (e) {
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
        });

        // New OTP input handling
        const otpInputs = document.querySelectorAll('.otp-input');
        const hiddenOtpInput = document.getElementById('hiddenOtpInput');

        otpInputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                // Ensure only numbers
                input.value = input.value.replace(/[^0-9]/g, '');

                // Auto move to next input
                if (input.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }

                // Combine all inputs
                const otpValue = Array.from(otpInputs).map(inp => inp.value).join('');
                hiddenOtpInput.value = otpValue;
            });

            // Allow backspace to move back
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && input.value.length === 0 && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });
        });

        // Trigger SweetAlert if OTP is verified
        <?php if (isset($_SESSION['otp_verified']) && $_SESSION['otp_verified']): ?>
        Swal.fire({
            icon: 'success',
            title: 'Verification Successful',
            text: 'Your OTP has been verified. Redirecting...'
        }).then(() => {
            window.location.href = "register_step2.php";
        });
        <?php unset($_SESSION['otp_verified']); endif; ?>
    </script>
</body>
</html>