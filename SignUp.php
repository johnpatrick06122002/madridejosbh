<?php
session_start();
if (isset($_SESSION['Email_Session'])) {
    header("Location: welcome.php");
    die();
}
include('connection.php');

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

$msg = "";
$Error_Pass = "";

if (isset($_POST['submit'])) {
    $first_name = mysqli_real_escape_string($dbconnection, $_POST['FirstName']);
    $middle_name = mysqli_real_escape_string($dbconnection, $_POST['MiddleName']);
    $last_name = mysqli_real_escape_string($dbconnection, $_POST['LastName']);
    $email = mysqli_real_escape_string($dbconnection, $_POST['Email']);
    $address = mysqli_real_escape_string($dbconnection, $_POST['Address']);
    $contact_number = mysqli_real_escape_string($dbconnection, $_POST['ContactNumber']);
    $facebook_account = mysqli_real_escape_string($dbconnection, $_POST['FacebookAccount']);
    $Password = mysqli_real_escape_string($dbconnection, md5($_POST['Password']));
    $Confirm_Password = mysqli_real_escape_string($dbconnection, md5($_POST['Conf-Password']));
    $Code = sprintf("%06d", mt_rand(100000, 999999)); // Generate 6-digit OTP code

    // Handle the profile photo upload
    $profile_photo = "";
    if (isset($_FILES['ProfilePhoto']) && $_FILES['ProfilePhoto']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $profile_photo = $target_dir . basename($_FILES["ProfilePhoto"]["name"]);
        move_uploaded_file($_FILES["ProfilePhoto"]["tmp_name"], $profile_photo);
    }

    // Check if email already exists
    if (mysqli_num_rows(mysqli_query($dbconnection, "SELECT * FROM register WHERE email='{$email}'")) > 0) {
        $msg = "<div class='alert alert-danger'>This Email: '{$email}' has already been registered.</div>";
    } else {
        // Check if passwords match
        if ($Password === $Confirm_Password) {
            // Insert user into the database
            $query = "INSERT INTO register(`FirstName`, `MiddleName`, `LastName`, `email`, `Password`, `CodeV`, `Address`, `ContactNumber`, `FacebookAccount`, `ProfilePhoto`) 
            VALUES('$first_name','$middle_name','$last_name','$email','$Password','$Code','$address','$contact_number','$facebook_account','$profile_photo')";
            $result = mysqli_query($dbconnection, $query);
            
            if ($result) {
                // Create an instance of PHPMailer
                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'lucklucky2100@gmail.com';  // Your Gmail email
                    $mail->Password   = 'kjxf ptjv erqn yygv';     // Your Gmail app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    // Recipients
                    $mail->setFrom('lucklucky2100@gmail.com', 'Madribh2');
                    $mail->addAddress($email, $first_name . ' ' . $last_name);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Welcome to My Website';
                    $mail->Body    = '<p>Your verification code is: <b>' . $Code . '</b></p>';

                    $mail->send();

                    // If the email was sent, store the email in session and redirect to the OTP page
                    $_SESSION['Email_Session'] = $email; // Store the email in session
                    header("Location: verify.php"); // Redirect to OTP verification page
                    exit(); // Ensure no further code is executed
                } catch (Exception $e) {
                    $msg = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }

                $msg = "<div class='alert alert-info'>We've sent a verification code to your email address.</div>";
            } else {
                $msg = "<div class='alert alert-danger'>Something went wrong.</div>";
            }
        } else {
            // If passwords do not match
            $msg = "<div class='alert alert-danger'>Password and Confirm Password do not match.</div>";
            $Error_Pass = 'style="border:1px Solid red;box-shadow:0px 1px 11px 0px red"';
        }
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
    <title>Sign in & Sign up Form</title>
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

        .password-hints {
            font-size: 0.85rem;
            color: #555;
            margin-top: 10px;
            list-style: none;
            padding-left: 0;
        }

        .password-hints li {
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="container sign-up-mode">
        <div class="forms-container">
            <div class="signin-signup">
                <form action="" method="POST" class="sign-up-form" enctype="multipart/form-data">
                    <h2 class="title">Sign up</h2>
                    <?php echo $msg ?>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="FirstName" placeholder="First Name" value="<?php if (isset($_POST['FirstName'])) {
                                                                                            echo $first_name;
                                                                                        } ?>" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="MiddleName" placeholder="Middle Name (optional)" value="<?php if (isset($_POST['MiddleName'])) {
                                                                                            echo $middle_name;
                                                                                        } ?>" />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="LastName" placeholder="Last Name" value="<?php if (isset($_POST['LastName'])) {
                                                                                            echo $last_name;
                                                                                        } ?>" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="Email" placeholder="Email" value="<?php if (isset($_POST['Email'])) {
                                                                                        echo $email;
                                                                                    } ?>" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-home"></i>
                        <input type="text" name="Address" placeholder="Address" value="<?php if (isset($_POST['Address'])) {
                                                                                            echo $address;
                                                                                        } ?>" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-phone"></i>
                        <input type="text" name="ContactNumber" placeholder="Contact Number" value="<?php if (isset($_POST['ContactNumber'])) {
                                                                                            echo $contact_number;
                                                                                        } ?>" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-facebook"></i>
                        <input type="text" name="FacebookAccount" placeholder="Facebook Account (optional)" value="<?php if (isset($_POST['FacebookAccount'])) {
                                                                                            echo $facebook_account;
                                                                                        } ?>" />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-image"></i>
                        <input type="file" name="ProfilePhoto" placeholder="Profile Photo" />
                    </div>
                    <div class="input-field" <?php echo $Error_Pass?>>
                        <i class="fas fa-lock"></i>
                        <input type="password" name="Password" placeholder="Password" onkeyup="checkPassword()" required />
                    </div>
                    <div class="input-field" <?php echo $Error_Pass?>>
                        <i class="fas fa-lock"></i>
                        <input type="password" name="Conf-Password" placeholder="Confirm Password" required />
                    </div>
                    <ul class="password-hints">
                        <li id="length">• Must be at least 8 characters long</li>
                        <li id="capital">• Must contain at least one uppercase letter</li>
                        <li id="small">• Must contain at least one lowercase letter</li>
                        <li id="special">• Must contain at least one special character</li>
                    </ul>
                    <input type="submit" name="submit" class="btn" value="Sign up" />
                    <p class="social-text">Or Sign up with social platforms</p>
                    <div class="social-media">
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-google"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <div class="panels-container">
            <div class="panel left-panel">
            </div>
            <div class="panel right-panel">
                <div class="content">
                    <h3>One of us ?</h3>
                    <p>
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Nostrum
                        laboriosam ad deleniti.
                    </p>
                    <a href="login.php" class="btn transparent" id="sign-in-btn" style="padding:10px 20px;text-decoration:none">
                        Sign in
                    </a>
                </div>
                <img src="img/register.svg" class="image" alt="" />
            </div>
        </div>
    </div>
    <script>
        function checkPassword() {
            const password = document.querySelector("input[name='Password']").value;
            const lengthCheck = document.getElementById("length");
            const capitalCheck = document.getElementById("capital");
            const smallCheck = document.getElementById("small");
            const specialCheck = document.getElementById("special");

            lengthCheck.style.color = password.length >= 8 ? "green" : "red";
            capitalCheck.style.color = /[A-Z]/.test(password) ? "green" : "red";
            smallCheck.style.color = /[a-z]/.test(password) ? "green" : "red";
            specialCheck.style.color = /[!@#$%^&*(),.?":{}|<>]/.test(password) ? "green" : "red";
        }
    </script>
</body>

</html>
