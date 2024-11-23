<?php
session_start(); // Start session at the beginning of the script

if (isset($_POST["login"])) {
    $email = $_POST['email']; // Use email for login
    $password = $_POST['password']; // Password field

    // Database connection (adjust parameters as needed)
  include('../connection.php');
    // Prepare and bind
    $stmt = $dbconnection->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $admin['password'])) {
            // Set session variable indicating user is logged in
            $_SESSION['admin_loggedin'] = true;
            $_SESSION['firstname'] = $admin['firstname'];
            $_SESSION['just_loggedin'] = true; // New session variable to indicate a fresh login
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        // Set the active link to dashboard in localStorage
                        localStorage.setItem("activeLink", "dashboard.php");
                        
                        Swal.fire({
                            icon: "success",
                            title: "Login Successful",
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location.href = "dashboard.php";
                        });
                    });
                  </script>';
        } else {
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Email or Password is Incorrect"
                        });
                    });
                  </script>';
        }
    } else {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Email or Password is Incorrect"
                    });
                });
              </script>';
    }

    // Close statement and connection
    $stmt->close();
    $dbconnection->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Include SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style type="text/css">
        /* Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #3b82f6;
            --secondary-color: #43A047;
            --background-color: #f3f4f6;
            --text-color: #1f2937;
            --input-border-color: #d1d5db;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(67, 160, 71, 0.1)), url('../b.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Inter', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            perspective: 1000px;
        }

        .login-page {
            width: 100%;
            max-width: 400px;
            padding: 8% 0 0;
            margin: auto;
        }

        .form {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.9);
            max-width: 400px;
            margin: 0 auto 100px;
            padding: 45px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease;
        }

        .form:hover {
            transform: translateY(-10px) rotateX(5deg);
        }

        h2 {
            color: var(--primary-color);
            margin-bottom: 25px;
            font-weight: 600;
        }

        .input-container {
            position: relative;
            margin-bottom: 20px;
        }

        .input-container .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            transition: color 0.3s ease;
        }

        .input-container input {
            width: 100%;
            height: 50px;
            padding: 10px 10px 10px 40px;
            box-sizing: border-box;
            border: 2px solid var(--input-border-color);
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .input-container input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .input-container input::placeholder {
            color: var(--text-color);
            opacity: 0.7;
        }

        .input-container .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--primary-color);
            transition: color 0.3s ease;
        }

        .input-container .toggle-password:hover {
            color: var(--secondary-color);
        }

        .forgot-password {
            text-align: left;
            margin-top: -15px;
            margin-bottom: 15px;
            font-size: 12px;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--secondary-color);
        }

        .form button {
            font-family: 'Inter', sans-serif;
            text-transform: uppercase;
            background: var(--primary-color);
            width: 100%;
            border: none;
            padding: 15px;
            color: white;
            font-size: 14px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .form .message {
            margin: 15px 0 0;
            color: var(--text-color);
            font-size: 12px;
        }

        .form .message a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .form .message a:hover {
            color: var(--secondary-color);
        }
    </style>
</head>
<body>

<div class="login-page">
    <div class="form">
        <h2>Admin Login</h2>
        <form class="login-form" action="" method="POST">
            <div class="input-container">
                <i class="fa fa-user icon"></i>
                <input type="email" name="email" placeholder="Email" required/>
            </div>
            <div class="input-container">
                <i class="fa fa-lock icon"></i>
                <input type="password" name="password" placeholder="Password" id="password" required/>
                <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility()"></i>
            </div>
            <p class="forgot-password">
                <a href="forgot_password.php">Forgot Password?</a>
            </p>
            <button type="submit" name="login">Login</button>
            <p class="message">Return to <a href="../index.php">WebPage</a></p>
        </form>
    </div>
</div>

<!-- Include SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.querySelector('.toggle-password');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
<?php include('footer.php'); ?>
</body>
</html>