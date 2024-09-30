<?php
session_start();
include('connection.php');
$msg = "";

if (isset($_POST['verify'])) {
    // Use mysqli_real_escape_string to sanitize user input
    $otp_input = mysqli_real_escape_string($dbconnection, $_POST['otp']);
    $email = $_SESSION['email']; // Assuming the email is stored in session after registration

    // Query to verify OTP
    $result = mysqli_query($dbconnection, "SELECT * FROM register1 WHERE email='$email' AND otp='$otp_input'");
    
    if (mysqli_num_rows($result) > 0) {
        // OTP is correct, mark the user as verified
        mysqli_query($dbconnection, "UPDATE register1 SET verification = 1 WHERE email='$email'");
        
        // Fetch user ID and store it in the session
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['ID']; // Store the user ID in session
        
        // Set a session variable to trigger the SweetAlert
        $_SESSION['otp_verified'] = true;

        // Redirect to the same page
        header("Location: " . $_SERVER['PHP_SELF']);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .input-field {
            position: relative;
            margin-bottom: 20px;
        }

        .input-field input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 93%;
        }

        .input-field i {
            position: absolute;
            left: 10px;
            top: 10px;
            color: #aaa;
        }

        .btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 12px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <form action="" method="POST">
            <h2 class="title">Verify OTP</h2>
            <?php echo $msg; ?>
            <div class="input-field">
                <input type="text" name="otp" placeholder="Enter 6-digit code" required />
            </div>
            <input type="submit" name="verify" value="Verify" class="btn" />
        </form>
    </div>

    <script>
        <?php if (isset($_SESSION['otp_verified'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Your OTP has been verified!',
                showConfirmButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to the next step if needed
                    window.location.href = 'register_step2.php';
                }
            });
            <?php unset($_SESSION['otp_verified']); // Clear the session variable after displaying the alert ?>
        <?php endif; ?>
    </script>
</body>

</html>
