<?php
session_start();
include('connection.php');
require 'vendor_copy/autoload.php';

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($dbconnection, $_POST['email']);

    // Check if email exists in the database
    $stmt = $dbconnection->prepare("SELECT * FROM register1 WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a secure 6-digit OTP and verification token
        $otp = random_int(100000, 999999);
        $verification_token = bin2hex(random_bytes(32)); // Generate a secure random token
        $otp_hash = password_hash($otp, PASSWORD_DEFAULT);
        $otp_expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        // Update the database with OTP, token, and expiry
        $update_stmt = $dbconnection->prepare("UPDATE register1 SET otp = ?, otp_expiry = ?, verification_token = ? WHERE email = ?");
        $update_stmt->bind_param("ssss", $otp_hash, $otp_expiry, $verification_token, $email);
        
        if ($update_stmt->execute()) {
            // Create verification link - Updated to verify_otp.php
           $verification_link = "https://madridejosbh2.com/verify_otp2.php?token=" . $verification_token;
            
            // Create a new PHPMailer instance
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'madridejosbh2@gmail.com';
                $mail->Password = 'ougf gwaw ezwh jmng';
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('madridejosbh2@gmail.com', 'Madridejos Bh finder');
                $mail->addAddress($email);

                // Create HTML email content with both OTP and verification link
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset OTP and Verification Link';
                $mail->Body = '
                    <html>
                    <body style="font-family: Arial, sans-serif; line-height: 1.6;">
                        <h2>Password Reset Request</h2>
                        <p>Your OTP for password reset is: <strong>' . $otp . '</strong></p>
                        <p>This OTP will expire in 15 minutes.</p>
                        <p>Alternatively, you can click the button below to verify and reset your password:</p>
                        <p style="margin: 25px 0;">
                            <a href="' . $verification_link . '" 
                               style="background-color: #4CAF50;
                                      color: white;
                                      padding: 12px 25px;
                                      text-decoration: none;
                                      border-radius: 5px;
                                      display: inline-block;">
                                Reset Password
                            </a>
                        </p>
                        <p>If the button doesn\'t work, copy and paste this link into your browser:</p>
                        <p>' . $verification_link . '</p>
                        <p>If you didn\'t request this password reset, please ignore this email.</p>
                    </body>
                    </html>';

                if ($mail->send()) {
                    $_SESSION['email'] = $email;
                    $_SESSION['success'] = "OTP and verification link sent successfully! Please check your email.";
                    header("Location: forgot_pass.php");
                    exit;
                } else {
                    $_SESSION['error'] = "Unable to send email. Please try again.";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Mailer Error: " . $mail->ErrorInfo;
            }
        } else {
            $_SESSION['error'] = "Failed to update verification data. Please try again.";
        }
    } else {
        $_SESSION['error'] = "If your email is registered, you will receive an OTP and verification link.";
    }

    if (isset($_SESSION['error'])) {
        header("Location: forgot_pass.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="shortcut icon" type="x-icon" href="b.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
           background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('b.png') no-repeat center center fixed;
           background-size: 70%;
           font-family: Arial, sans-serif;
           display: flex;
           justify-content: center;
           align-items: center;
           min-height: 100vh;
           margin: 0;
           padding: 0;
       }
        .container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .container h2 {
            color: #333;
            margin-bottom: 25px;
            font-weight: 600;
        }
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        .input-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .input-group input:focus {
            outline: none;
            border-color: #2575fc;
            box-shadow: 0 0 0 3px rgba(37, 117, 252, 0.1);
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #6a11cb 0%, #2575fc 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        @media (max-width: 480px) {
            .container {
                width: 95%;
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['success'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo $_SESSION['success']; ?>'
            }).then(() => {
                window.location.href = 'forgot_pass.php';
            });
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $_SESSION['error']; ?>'
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="container">
        <form method="POST" action="forgot_pass.php">
            <h2>Forgot Password</h2>
            <div class="input-group">
                <label for="email">Enter your email</label>
                <input type="email" id="email" name="email" required placeholder="example@email.com">
            </div>
            <button type="submit" name="submit">Send OTP</button>
<br><br>
             <p class="message"><a href="index.php">WebPage</a></p>
        </form>
    </div>
</body>
</html>