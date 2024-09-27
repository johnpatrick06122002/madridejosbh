<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  // Include Composer's autoloader

$name = isset($_POST['name']) ? $_POST['name'] : '';
$address = isset($_POST['Address']) ? $_POST['Address'] : '';
$contact_number = isset($_POST['contact_number']) ? $_POST['contact_number'] : '';
$facebook = isset($_POST['facebook']) ? $_POST['facebook'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$profile_photo = isset($_FILES['profile_photo']['name']) ? $_FILES['profile_photo']['name'] : (isset($_POST['saved_profile_photo']) ? $_POST['saved_profile_photo'] : '');
$mname = isset($_POST['mname']) ? $_POST['mname'] : '';
$lname = isset($_POST['lname']) ? $_POST['lname'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
$entered_code = isset($_POST['verification_code']) ? $_POST['verification_code'] : '';

// Handle form submission
if (isset($_POST["register"])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Database connection (assuming $dbconnection is your database connection variable)
    include('connection.php');

    // Server-side validation
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "error",
                        title: "Invalid Password",
                        text: "Password must be at least 8 characters long and include numbers, letters, and at least one capital letter.",
                    });
                });
              </script>';
    } elseif ($password !== $confirm_password) {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "error",
                        title: "Password Mismatch",
                        text: "Passwords do not match. Please try again.",
                    });
                });
              </script>';
     } elseif ($entered_code !== $_SESSION['verification_code']) {
        // Verification code does not match
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "error",
                        title: "Invalid Verification Code",
                        text: "The verification code you entered is incorrect. Please try again.",
                    });
                });
              </script>';
    } else {
// Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $contact_number_full = "+63" . $contact_number;

        // Prepare and execute the SQL statement
        $stmt = $dbconnection->prepare("INSERT INTO landlords (name, email, password, Address, contact_number, facebook, profile_photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $email, $hashed_password, $address, $contact_number_full, $facebook, $profile_photo);

        if ($stmt->execute()) {
            move_uploaded_file($_FILES['profile_photo']['tmp_name'], "uploads/" . basename($profile_photo));

            // Send email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.example.com'; // Set the SMTP server to send through
                $mail->SMTPAuth   = true;
                $mail->Username   = 'your-email@example.com'; // SMTP username
                $mail->Password   = 'your-email-password'; // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Recipients
                $mail->setFrom('your-email@example.com', 'Your Name');
                $mail->addAddress($email, $name);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Registration Successful';
                $mail->Body    = 'Dear ' . $name . ',<br><br>Thank you for registering. Your registration was successful!<br><br>Best Regards,<br>Your Company';

                $mail->send();
                echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: "success",
                                title: "Registration Successful",
                                text: "A confirmation email has been sent to your address.",
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                window.location.href = "login.php";
                            });
                        });
                      </script>';
            } catch (Exception $e) {
                echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: "error",
                                title: "Email Sending Failed",
                                text: "Could not send confirmation email. Please try again.",
                            });
                        });
                      </script>';
            }

            $stmt->close();
            $dbconnection->close();
        } else {
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: "error",
                            title: "Registration Failed",
                            text: "Please try again.",
                        });
                    });
                  </script>';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MADRIE-BH</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css?family=Roboto:300,400,500,700');

        body {
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('bh.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Roboto', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .register-page {
            width: 390px;
            padding: 1% 0 0;
            margin: auto;
        }

        .form {
            position: relative;
            z-index: 1;
            background:white;
            max-width: 360px;
            padding: 45px;
            text-align: left;
            box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
            border-radius: 10px;
            color: black;
        }

        .form input {
            font-family: "Roboto", sans-serif;
            outline: 0;
            background: #f2f2f2;
            width: 100%;
            border: 0;
            margin: 0 0 15px;
            padding: 15px;
            box-sizing: border-box;
            font-size: 14px;
            border-radius: 5px;
            color: black ; 
        }

        .form button {
            font-family: "Roboto", sans-serif;
            text-transform: uppercase;
            outline: 0;
            background: red;
            width: 100%;
            border: 0;
            padding: 15px;
            color: black;
            font-size: 14px;
            border-radius: 50px;
            -webkit-transition: all 0.3 ease;
            transition: all 0.3 ease;
            cursor: pointer;
        }

        .form button:hover, .form button:active, .form button:focus {
            background: #43A047;
        }

        .form .form-group {
            margin-bottom: 15px;
        }

        .form .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: black;
        }

        .form .form-group input {
            font-family: "Roboto", sans-serif;
            outline: 0;
            background: none;
            border: 1px solid #ccc;
            border-radius: 50px;
            color: black;
        }

        .form .form-group .input-group-text {
            display: flex;
            align-items: center;
            background: none;
            border: 1px solid #ccc;
            border-radius: 50px;
            padding: 0 10px;
            color: black;
            height: 60px;

        }

        .form .form-group .input-group-text input {
            border: none;
            border-radius: 0;
            padding-left: 10px;
            color: black;
        }

        .form .form-group .error {
            color: #FF0000;
            font-size: 12px;
            display: none;
            margin-top: 5px;
            color: black;
        }

        .message {
            margin: 15px 0 0;
            color: black;
            font-size: 14px;

        }
           .form input::placeholder {
        color: black; /* Set placeholder text color to white */
    }

        .message a {
            color: blue;
            text-decoration: none;
            
        }
    </style>
</head>
<body>
    <div class="register-page">
    <div class="form">
        <form class="register-form" action="register.php" method="post" enctype="multipart/form-data">
            <!-- Section 1: Email and Password -->
            <div id="section1">
                <div class="form-group">
                    <label for="email">Email address:</label>
                    <div style="display: flex;">
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter email" required>
                        <button type="button" id="send-code" class="btn btn-secondary" style="margin-left: 10px;">Send Code</button>
                    </div>
                    <div id="email-error" class="error" style="display: none;">Please enter a valid email address.</div>
                </div>
                <div class="form-group">
                    <label for="verification_code">Enter Verification Code:</label>
                    <input type="text" id="verification_code" name="verification_code" class="form-control" placeholder="Enter verification code" required>
                    <div id="code-error" class="error" style="display: none;">Invalid verification code.</div>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input id="password" type="password" name="password" class="form-control" placeholder="Enter password" required>
                    <div id="password-error" class="error" style="display: none;">Password must be at least 8 characters long and include numbers, letters, and at least one capital letter.</div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Re-enter Password:</label>
                    <input id="confirm_password" type="password" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
                    <div id="confirm-password-error" class="error" style="display: none;">Passwords do not match.</div>
                </div>
                <button type="submit" id="register" class="btn btn-primary">Register</button>
            </div>
        </form>
    </div>
</div>


                <!-- Section 2: Additional Information -->
                <div id="section2" style="display: none;">
                    <!-- Your existing form fields for additional information -->
                    <div class="form-group">
                        <label for="profile_photo">Profile Picture:</label>
                        <input type="file" name="profile_photo" class="form-control" <?php if (!$profile_photo) echo 'required'; ?>>
                        <?php if ($profile_photo): ?>
                            <img src="uploads/<?php echo htmlspecialchars($profile_photo); ?>" alt="Profile Photo" width="100">
                            <input type="hidden" name="saved_profile_photo" value="<?php echo htmlspecialchars($profile_photo); ?>">
                        <?php endif; ?>
                    </div>
                    <!-- Add other fields here -->

                    <button type="submit" name="register" class="btn btn-primary">Register</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('submit-section1').addEventListener('click', function() {
                // Validate email and password
                const email = document.querySelector('input[name="email"]').value;
                const password = document.querySelector('input[name="password"]').value;
                const confirmPassword = document.querySelector('input[name="confirm_password"]').value;

                // Additional client-side validation here if needed

                // Example basic validation
                if (email.trim() === '' || password.trim() === '' || password !== confirmPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please enter valid email and passwords that match.',
                    });
                    return;
                }

                // Show Section 2
                document.getElementById('section1').style.display = 'none';
                document.getElementById('section2').style.display = 'block';
            });
        });
        document.getElementById('send-code').addEventListener('click', function() {
    var email = document.getElementById('email').value;

    if (validateEmail(email)) {
        // AJAX request to send verification code
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'send_verification_code.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert('Verification code sent to your email.');
                } else {
                    alert('Failed to send verification code. Please try again.');
                }
            }
        };
        xhr.send('email=' + encodeURIComponent(email));
    } else {
        document.getElementById('email-error').style.display = 'block';
    }
});

function validateEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}

    </script>
</body>
</html>