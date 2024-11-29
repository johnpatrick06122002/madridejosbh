<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor_copy/autoload.php'; // Ensure the correct path
include('../connection.php'); // Database connection

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $dbconnection->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a 6-digit OTP
        $otp = random_int(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes")); // OTP expires in 15 minutes

        // Insert the OTP and expiry into the database
        $stmt = $dbconnection->prepare("UPDATE admins SET otp = ?, reset_token_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $otp, $expiry, $email);
        $stmt->execute();

        // Send OTP via email
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'lucklucky2100@gmail.com'; // Your email
            $mail->Password = 'kjxf ptjv erqn yygv'; // Your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('lucklucky2100@gmail.com', 'Your Website');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = "Your OTP for password reset is: <b>$otp</b>. This OTP is valid for 15 minutes.";

            if ($mail->send()) {
                $_SESSION['success'] = "An OTP has been sent to your email address.";
                $_SESSION['email'] = $email; // Save email to session for OTP verification
                header("Location: verify_otp.php"); // Redirect to verify_otp.php
                exit(); // Ensure the script stops here
            } else {
                $_SESSION['error'] = "Unable to send OTP email. Please try again.";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        $_SESSION['error'] = "The email address you entered is not registered.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="shortcut icon" type="x-icon" href="../b.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* General Body Styling */
       
       body {
    background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('../b.png') no-repeat center center fixed;
    background-size: 70%;
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh; /* Ensures full screen coverage */
    margin: 0;
    padding: 0;
}

        /* Centered Form Container */
        .form-container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        /* Form Heading */
        .form-container h2 {
            margin-bottom: 20px;
            color: #555;
        }

        /* Input Field Styling */
        .form-container input {
            width: 100%;
            padding: 10px 15px;
            margin: 10px 0 20px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s ease-in-out;
        }

        .form-container input:focus {
            outline: none;
            border-color: #6e8efb;
        }

        /* Button Styling */
        .form-container button {
            background: #6e8efb;
            color: #fff;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .form-container button:hover {
            background: #5a7ddb;
        }

        /* Responsive Design */
        @media screen and (max-width: 480px) {
            .form-container {
                padding: 20px 30px;
            }
        }
    </style>
</head>
<body>
    
    <div class="form-container">
        <h2>Forgot Password</h2>
        <form action="" method="POST">
            <label for="email">Enter your email address:</label>
            <input type="email" name="email" id="email" placeholder="Your email address" required>
            <button type="submit" name="reset_password">Send OTP</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php
    // Display SweetAlert for success or error
    if (isset($_SESSION['success'])) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '" . $_SESSION['success'] . "',
                confirmButtonText: 'Ok'
            });
        </script>";
        unset($_SESSION['success']); // Clear session after showing
    } elseif (isset($_SESSION['error'])) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '" . $_SESSION['error'] . "',
                confirmButtonText: 'Ok'
            });
        </script>";
        unset($_SESSION['error']); // Clear session after showing
    }
    ?>

</body>
</html>
