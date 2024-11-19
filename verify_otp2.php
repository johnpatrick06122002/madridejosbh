<?php
session_start();
include('connection.php'); // Include your database connection
require 'vendor/autoload.php'; // PHPMailer autoload

if (!isset($_SESSION['email'])) {
    header('Location: forgot_pass.php');
    exit();
}

$msg = ""; // Initialize message

// Handle OTP verification
if (isset($_POST['verify'])) {
    $otp_input = $_POST['otp'];
    $email = $_SESSION['email'];

    $stmt = $dbconnection->prepare("SELECT otp, otp_expiry FROM register1 WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    $hashed_otp = $row['otp'];
    $otp_expiry = $row['otp_expiry'];

    if (new DateTime() > new DateTime($otp_expiry)) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'OTP Expired',
                text: 'The OTP has expired. Please request a new one.'
            });
        </script>";
    } elseif (password_verify($otp_input, $hashed_otp)) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Invalid OTP',
                text: 'The OTP you entered is incorrect. Please try again.'
            });
        </script>";
    }
}
}
// Handle OTP resend
if (isset($_POST['resend_otp'])) {
    $email = $_SESSION['email'];
    $new_otp = random_int(100000, 999999); // Generate a secure OTP
    $otp_hash = password_hash($new_otp, PASSWORD_DEFAULT); // Hash the OTP
    $otp_expiry = date("Y-m-d H:i:s", strtotime("+15 minutes")); // Set OTP expiry

    // Update OTP and expiry in the database
    $stmt = $dbconnection->prepare("UPDATE register1 SET otp = ?, otp_expiry = ? WHERE email = ?");
    $stmt->bind_param("sss", $otp_hash, $otp_expiry, $email);
    if ($stmt->execute()) {
        // Send OTP via email
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'lucklucky2100@gmail.com'; // Your SMTP username
            $mail->Password = 'kjxf ptjv erqn yygv'; // Your SMTP password
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('lucklucky2100@gmail.com', 'Your Name');
            $mail->addAddress($email);
            $mail->Subject = 'Your OTP Code';
            $mail->Body = "Your new OTP code is: $new_otp";

            if ($mail->send()) {
                $msg = "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'OTP Sent',
                        text: 'A new OTP has been sent to your email!'
                    });
                </script>";
            } else {
                $msg = "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to send OTP. Please try again later.'
                    });
                </script>";
            }
        } catch (Exception $e) {
            $msg = "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Mailer Error',
                    text: 'Error: {$mail->ErrorInfo}'
                });
            </script>";
        }
    } else {
        $msg = "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to update OTP. Please try again later.'
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .otp-container input {
            width: 45px;
            height: 45px;
            font-size: 18px;
            text-align: center;
        }
        .btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        #resendOtpButton:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <h2>Verify OTP</h2>
    <?php echo $msg; ?>

    <!-- OTP Input Form -->
    <form action="" method="POST" id="otpForm">
        <div class="otp-container">
            <input type="text" maxlength="6" name="otp" id="otp" placeholder="Enter OTP" required>
        </div>
        <button type="submit" name="verify" class="btn">Verify</button>
    </form>

    <!-- Resend OTP -->
    <form action="" method="POST">
        <button type="submit" name="resend_otp" id="resendOtpButton" class="btn">Resend OTP</button>
        <span id="countdown" style="margin-left: 10px; display:none;"> (60)</span>
    </form>

    <script>
        // Resend OTP Countdown
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
    </script>
</body>

</html>
