<?php
if (isset($_POST["register"])) {
    $name = $_POST['name'];
    $address = $_POST['Address'];
    $contact_number = "+63" . $_POST['contact_number'];
    $facebook = $_POST['facebook'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $profile_photo = $_FILES['profile_photo']['name'];
    $target = "uploads/" . basename($profile_photo);

    // Database connection (assuming $dbconnection is your database connection variable)
    include('connection.php');

    // Password validation on the server side
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
    } else {
        $sql = "INSERT INTO landlords (name, email, password, Address, contact_number, facebook, profile_photo) 
                VALUES ('$name', '$email', '$password', '$address', '$contact_number', '$facebook', '$profile_photo')";

        if ($dbconnection->query($sql) === TRUE) {
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: "success",
                            title: "Registration Successful",
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location.href = "login.php";
                        });
                    });
                  </script>';
            move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target);
        } else {
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: "error",
                            title: "Registration Failed",
                            text: "Please try again",
                        });
                    });
                  </script>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="bhh.jpg">
    <title>Register - MADRIE-BH</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css?family=Roboto:300);

        .register-page {
            width: 390px;
            padding:  4% 0 0;
            margin: auto;
        }
        .form {
            position: relative;
            z-index: 1;
            background: #FFFFFF;
            max-width: 360px;
            padding: 45px;
            text-align: left;
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
            font-size:14px;
        }
        .message a {
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="register-page">
        <div class="form">
            <form class="register-form" action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="profile_photo">Profile Picture</label>
                    <input type="file" name="profile_photo" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter name" required>
                </div>
                <div class="form-group">
                    <label for="Address">Address:</label>
                    <input type="text" name="Address" class="form-control" placeholder="Enter Address" required>
                </div>
               <div class="form-group">
        <label for="contact_number">Contact Number:</label>
        <div class="input-group-text">+63
            <input onkeypress="phnumber(event)" type="text" maxlength="10" minlength="10" name="contact_number" class="form-control" placeholder="Contact Number" required>
        </div>
    </div> 

    <script>
        function phnumber(event) {
            // Allow only numeric input
            var charCode = event.which ? event.which : event.keyCode;
            if (charCode < 48 || charCode > 57) {
                event.preventDefault();
            }
        }
    </script>
</body>
</html>
                <div class="form-group">
                    <label for="facebook">Facebook Account:</label>
                    <input type="url" name="facebook" class="form-control" placeholder="Enter Facebook Account URL" required>
                </div>
                <div class="form-group">
                    <label for="email">Email address:</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                </div>
                 <div class="form-group">
        <label for="password">Password:</label>
        <input id="password" type="password" name="password" class="form-control" placeholder="Enter password" required>
        <div id="password-error" class="error">Password must be at least 8 characters long and include numbers, letters, and at least one capital letter.</div>
    </div>

    <script>
        document.getElementById('password').addEventListener('input', function () {
            const password = this.value;
            const errorElement = document.getElementById('password-error');

            // Regular expression to check password
            const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/;

            if (!passwordRegex.test(password)) {
                errorElement.style.display = 'block';
            } else {
                errorElement.style.display = 'none';
            }
        });
    </script>
                <button type="submit" name="register" class="btn btn-primary">Register</button>
                <p class="message">Already have an account? <a href="login.php">Login</a></p>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
