<?php
session_start();
include('../connection.php'); // Database connection
require '../vendor_copy/autoload.php'; // PHPMailer autoload

if (!isset($_SESSION['email'])) {
    header('Location: forgot_password.php');
    exit();
}

$msg = "";

// Handle OTP verification
if (isset($_POST['verify'])) {
    $otp_input = $_POST['otp'];
    $email = $_SESSION['email'];

    $stmt = $dbconnection->prepare("SELECT otp, otp_expiry FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $hashed_otp = $row['otp'];
        $otp_expiry = $row['otp_expiry'];

        if (new DateTime() > new DateTime($otp_expiry)) {
            $msg = "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'OTP Expired',
                    text: 'The OTP has expired. Please request a new one.'
                });
            </script>";
        } elseif (password_verify($otp_input, $hashed_otp)) {
            $_SESSION['otp_verified'] = true;

            // Success SweetAlert
            $msg = "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'OTP Verified',
                    text: 'The OTP has been verified successfully!',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location = 'reset_password.php'; // Redirect after user clicks 'OK'
                    }
                });
            </script>";
        } else {
            $msg = "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid OTP',
                    text: 'The OTP you entered is incorrect. Please try again.'
                });
            </script>";
        }
    } else {
        $msg = "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No OTP record found. Please try again.'
            });
        </script>";
    }
    $stmt->close();
}

// Handle OTP resend
if (isset($_POST['resend_otp'])) {
    $email = $_SESSION['email'];
    $new_otp = random_int(100000, 999999);
    $otp_hash = password_hash($new_otp, PASSWORD_DEFAULT);
    $otp_expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

    $stmt = $dbconnection->prepare("UPDATE admins SET otp = ?, otp_expiry = ? WHERE email = ?");
    $stmt->bind_param("sss", $otp_hash, $otp_expiry, $email);

    if ($stmt->execute()) {
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'madridejosbh2@gmail.com'; // Your SMTP username
            $mail->Password = 'ougf gwaw ezwh jmng'; // Your SMTP password
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('madridejosbh2@gmail.com', 'Madridejos Bh finder');
            $mail->addAddress($email);
            $mail->Subject = 'Your OTP Code';
            $mail->Body = "Your new OTP code is: $new_otp";

            if ($mail->send()) {
                echo json_encode(["status" => "success", "message" => "OTP sent successfully."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to send OTP."]);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Mailer Error: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update OTP."]);
    }
    $stmt->close();
    exit();
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
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .verify-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        .otp-input-container {
            display: flex;
            justify-content: center;
            gap: 10px;
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
   document.addEventListener('DOMContentLoaded', () => {
    const resendButton = document.getElementById('resendOtpButton');
    const countdownDisplay = document.getElementById('countdown');
    const otpInputs = document.querySelectorAll('.otp-input');
    const hiddenOtpInput = document.getElementById('hiddenOtpInput');
    let countdown = 60;

    // Resend OTP functionality
    resendButton.addEventListener('click', function (e) {
        e.preventDefault();

        resendButton.disabled = true; // Disable button to avoid multiple clicks

        fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'resend_otp=1',
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'OTP Sent',
                    text: data.message,
                });

                // Start countdown after a successful OTP send
                countdownDisplay.style.display = "inline";
                countdownDisplay.innerText = ` (${countdown}s)`;

                let interval = setInterval(function () {
                    countdown--;
                    countdownDisplay.innerText = ` (${countdown}s)`;
                    if (countdown <= 0) {
                        clearInterval(interval);
                        resendButton.disabled = false;
                        countdownDisplay.style.display = "none";
                        countdown = 60; // Reset countdown
                    }
                }, 1000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                });
                resendButton.disabled = false; // Re-enable button on failure
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An unexpected error occurred. Please try again later.',
            });
            resendButton.disabled = false; // Re-enable button on failure
        });
    });

    // OTP Input Handling
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', () => {
            // Ensure only numeric input
            input.value = input.value.replace(/[^0-9]/g, '');

            // Move focus to the next input if valid
            if (input.value.length === 1 && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }

            // Update hidden input with concatenated OTP
            hiddenOtpInput.value = Array.from(otpInputs).map(inp => inp.value).join('');
        });

        input.addEventListener('keydown', (e) => {
            // Handle Backspace to move to the previous input
            if (e.key === 'Backspace' && input.value.length === 0 && index > 0) {
                otpInputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', (e) => {
            // Handle paste events to allow for quick input
            e.preventDefault();
            const pasteData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, otpInputs.length);
            pasteData.split('').forEach((char, i) => {
                otpInputs[i].value = char;
            });
            hiddenOtpInput.value = pasteData; // Update hidden input
            otpInputs[Math.min(pasteData.length, otpInputs.length - 1)].focus(); // Focus next input
        });
    });
});

</script>

</body>
</html>