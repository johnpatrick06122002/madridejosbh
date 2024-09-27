<?php
session_start();
if (isset($_SESSION['Email_Session'])) {
    header("Location: welcome.php");
    die();
}

include('config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

$msg = "";
$step = 1;

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conx, $_POST['email']);
    $CodeReset = sprintf("%06d", mt_rand(100000, 999999)); // Generate 6-digit OTP code
    if (mysqli_num_rows(mysqli_query($conx, "SELECT * FROM register WHERE email='{$email}'")) > 0) {
        $query = mysqli_query($conx, "UPDATE register SET CodeV='{$CodeReset}' WHERE email='{$email}'");
        if ($query) {
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'lucklucky2100@gmail.com';  // Your Gmail email
                $mail->Password   = 'kjxf ptjv erqn yygv';     // Your Gmail app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                //Recipients
                $mail->setFrom('lucklucky2100@gmail.com', 'Madribh2');
                $mail->addAddress($email);
                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = 'Welcome To My Website';
                $mail->Body    = '<p>Your verification code is: <b>' . $CodeReset . '</b></p>';

                $mail->send();
                $step = 2; // Move to step 2
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
            $msg = "<div class='alert alert-info'>We've sent a verification code to your email address</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>This email '{$email}' is not found</div>";
    }
}

if (isset($_POST['verify_code'])) {
    $email = mysqli_real_escape_string($conx, $_POST['email']);
    $entered_code = mysqli_real_escape_string($conx, $_POST['code']);

    $result = mysqli_query($conx, "SELECT * FROM register WHERE email='{$email}' AND CodeV='{$entered_code}'");
    if (mysqli_num_rows($result) > 0) {
        $step = 3; // Move to step 3
        $msg = "<div class='alert alert-success'>Code verified! You can now reset your password.</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Invalid verification code</div>";
        $step = 2; // Stay on step 2
    }
}

if (isset($_POST['reset_password'])) {
    $email = mysqli_real_escape_string($conx, $_POST['email']);
    $new_password = mysqli_real_escape_string($conx, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conx, $_POST['confirm_password']);

    if ($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        mysqli_query($conx, "UPDATE register SET password='{$hashed_password}' WHERE email='{$email}'");
        $msg = "<div class='alert alert-success'>Password has been successfully updated!</div>";
        $step = 1;
    } else {
        $msg = "<div class='alert alert-danger'>Passwords do not match</div>";
        $step = 3;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css" />
    <title>Forget Password</title>
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

        .alert-info {
            background-color: #2E9AFE;
        }

        .alert-warning {
            background-color: #ff9966;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup" style="left: 50%; z-index:99;">
                <form action="" method="POST" class="sign-in-form">
                    <h2 class="title">Forget Password</h2>
                    <?php echo $msg ?>

                    <!-- Step 1: Email input -->
                    <?php if ($step == 1) { ?>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="email" name="email" placeholder="Email" required />
                    </div>
                    <input type="submit" name="submit" value="Send Code" class="btn solid" />
                    <?php } ?>

                    <!-- Step 2: Verification code input -->
                    <?php if ($step == 2) { ?>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="code" placeholder="Enter verification code" required />
                    </div>
                    <input type="hidden" name="email" value="<?php echo $email; ?>" />
                    <input type="submit" name="verify_code" value="Verify Code" class="btn solid" />
                    <?php } ?>

                    <!-- Step 3: Password reset -->
                    <?php if ($step == 3) { ?>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="new_password" placeholder="New Password" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required />
                    </div>
                    <input type="hidden" name="email" value="<?php echo $email; ?>" />
                    <input type="submit" name="reset_password" value="Reset Password" class="btn solid" />
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
</body>

</html>
