<?php
session_start(); // Start session at the beginning of the script

if (isset($_POST["login"])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded credentials for simplicity (replace with secure authentication)
    if ($username == "admin" && $password == "admin123") {
        // Set session variable indicating user is logged in
        $_SESSION['admin_loggedin'] = true;
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="../bhh.jpg">
    <title>Admin Login</title>
    <!-- Include SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css?family=Roboto:300);

        .login-page {
          width: 360px;
          padding: 8% 0 0;
          margin: auto;
        }
        .form {
          position: relative;
          z-index: 1;
          background: #FFFFFF;
          max-width: 360px;
          margin: 0 auto 100px;
          padding: 45px;
          text-align: center;
          box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
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
        }
        .form button {
          font-family: "Roboto", sans-serif;
          text-transform: uppercase;
          outline: 0;
          background: #4CAF50;
          width: 100%;
          border: 0;
          padding: 15px;
          color: #FFFFFF;
          font-size: 14px;
          -webkit-transition: all 0.3 ease;
          transition: all 0.3 ease;
          cursor: pointer;
        }
        .form button:hover,.form button:active,.form button:focus {
          background: #43A047;
        }
        .form .message {
          margin: 15px 0 0;
          color: #b3b3b3;
          font-size: 12px;
        }
        .form .message a {
          color: #4CAF50;
          text-decoration: none;
        }
        .form .register-form {
          display: none;
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
        body {
          background: #76b852; /* fallback for old browsers */
          background: rgb(141,194,111);
          background: linear-gradient(90deg, rgba(141,194,111,1) 0%, rgba(118,184,82,1) 50%);
          font-family: "Roboto", sans-serif;
          -webkit-font-smoothing: antialiased;
          -moz-osx-font-smoothing: grayscale;      
        }
    </style>
</head>
<body>

<div class="login-page">
  <div class="form">
    <form class="login-form" action="" method="POST">
      <input type="text" name="username" placeholder="username"/>
      <input type="password" name="password" placeholder="password"/>
      <button type="submit" name="login">login</button>
      <!-- <p class="message">Not registered? <a href="#">Create an account</a></p> -->
      <p class="message">Return to <a href="../index.php">WebPage</a></p>
    </form>
  </div>
</div>

<!-- Include SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include('footer.php'); ?>
</body>
</html>
