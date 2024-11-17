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
                            text: "Username or Password is Incorrect"
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
                        text: "Username or Password is Incorrect"
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
        

        @import url('https://fonts.googleapis.com/css?family=Roboto:300,400,500,700');

        body {
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('../bh.jpg') no-repeat center center fixed;
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

        .login-page {
            width: 360px;
            padding: 8% 0 0;
            margin: auto;
        }

        .form {
            position: relative;
            z-index: 1;
            background:white;
            max-width: 360px;
            margin: 0 auto 100px;
            padding: 45px;
            text-align: center;
            box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
            border-radius: 10px;
        }

        .form input {
            font-family: "Roboto", sans-serif;
            outline: 0;
            background: none;
            width: 100%;
            border: 0;
            margin: 0 0 15px;
            padding: 15px;
            box-sizing: border-box;
            font-size: 14px;
            border-radius: 50px;
            text-color: black;
        }

        .form button {
            font-family: "Roboto", sans-serif;
            text-transform: uppercase;
            outline: 0;
            background:#f9f5f4;
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

        .form .message {
            margin: 15px 0 0;
            color: black!important;
            font-size: 12px;
        }

        .form .message a {
            color: blue;
            text-decoration: none;
        }

        .form .message a:hover {
            color: #43A047;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 300px;
            margin: 0 auto;
        }

        .container:before, .container:after {
            content: "";
            display: block;
            clear: both;
        }

        .container .info {
            margin: 50px auto;
            text-align: center;
        }

        .container .info h1 {
            margin: 0 0 15px;
            padding: 0;
            font-size: 36px;
            font-weight: 300;
            color: #1a1a1a;
        }

        .container .info span {
            color: #4d4d4d;
            font-size: 12px;
        }

        .container .info span a {
            color: #000000;
            text-decoration: none;
        }

        .container .info span .fa {
            color: #EF3B3A;
        }
        .input-container {
  position: relative;
  margin-bottom: 15px;
}

.input-container .icon {
  position: absolute;
  left: 15px;
  top: 35%;
  transform: translateY(-50%);
  color: #888;
}

.input-container input {
  width: 100%;
  height: 50px;
  padding: 10px 10px 10px 40px; /* Adjust padding to make space for the icon */
  box-sizing: border-box;
  border: 1px solid #ccc;
  border-radius: 50px;
  color: black;
}

.input-container input::placeholder {
    color: black; /* Set placeholder text color to white */
}
.input-container input:focus {
  border-color: #007bff;
  outline: none;
}
h2{
color:black;

}
 .input-container .toggle-password {
            position: absolute;
            right: 15px;
            top: 35%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }
    </style>
</head>
<body>

<div class="login-page">
    <div class="form">
        <h2>Login</h2>
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
             <p style="text-align: left; margin-top: -15px; margin-bottom: 15px; font-size: 12px;">
                <a href="forgot_password.php" style="color: blue; text-decoration: none;">Forgot Password?</a>
            </p>
            <button type="submit" name="login">Login</button>
            <p class="message" style="color: #f9f5f4;"> Return to <a href="../index.php">WebPage</a></p>
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