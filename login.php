<?php
session_start(); // Start session at the beginning of the script

if(isset($_POST["login"])) {
    $myusername = $_POST['myemail'];
    $mypassword = $_POST['mypassword'];

    // Database connection (assuming $dbconnection is your database connection variable)
    include('connection.php');

    $sql = "SELECT id FROM landlords WHERE email = '$myusername' and password = '$mypassword'";
    $result = mysqli_query($dbconnection, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $count = mysqli_num_rows($result);

    if($count == 1) {
        // Set session variable indicating user is logged in
        $_SESSION['login_user'] = $myusername;

        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "success",
                        title: "Login Successful",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = "landlord/dashboard.php";
                    });
                });
              </script>';
    } else {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "error",
                        title: "Login Failed",
                        text: "Username or Password is Incorrect",
                    });
                });
              </script>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - MADRIE-BH</title>
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
        .form button:hover, .form button:active, .form button:focus {
            background: #43A047;
        }
        body {
            background: #76b852; /* fallback for old browsers */
            background: rgb(141, 194, 111);
            background: linear-gradient(90deg, rgba(141, 194, 111, 1) 0%, rgba(118, 184, 82, 1) 50%);
            font-family: "Roboto", sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .message {
            margin: 15px 0 0;
            color: #b3b3b3;
            font-size: 14px;
        }
        .message a {
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="form">
            <form class="login-form" action="" method="POST">
                <input type="text" name="myemail" placeholder="Email" required/>
                <input type="password" name="mypassword" placeholder="Password" required/>
                <button type="submit" name="login">Login</button>
                <p class="message">Not registered? <a href="register.php">Create an account</a></p>
                 <p class="message"><a href="index.php">WebPage</a></p>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
