<?php
session_start();
include('connection.php');
$msg = "";

if (isset($_POST['verify_otp'])) {
    $otp_input = mysqli_real_escape_string($dbconnection, $_POST['otp_code']);
    $email = $_SESSION['Email_Session']; // Assuming the email is stored in session after registration

    $result = mysqli_query($dbconnection, "SELECT * FROM register WHERE email='$email' AND CodeV='$otp_input'");
    
    if (mysqli_num_rows($result) > 0) {
        // OTP is correct, mark the user as verified
        mysqli_query($dbconnection, "UPDATE register SET verification = 1 WHERE email='$email'");
        
        // Fetch the user ID and store it in the session
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['ID']; // Store the user ID in session
        
        $msg = "<div class='alert alert-success'>Your account has been verified!</div>";
        
        // Redirect to subscription.php
        header("Location: subscription.php");
        exit();
    } else {
        $msg = "<div class='alert alert-danger'>Invalid OTP code. Please try again.</div>";
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
            <div class="signin-signup" style="left: 50%; z-index: 99;">
                <form action="" method="POST" class="sign-in-form">
                    <h2 class="title">Verify OTP</h2>
                    <?php echo $msg ?>
                    <div class="input-field">
                        <i class="fas fa-key"></i>
                        <input type="text" name="otp_code" placeholder="Enter 6-digit code" required />
                    </div>
                    <input type="submit" name="verify_otp" value="Verify" class="btn solid" />
                    <p class="social-text">Or Sign in with social platforms</p>
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
    </div>

    <script src="app.js"></script>
</body>

</html>
