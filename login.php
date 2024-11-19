<?php
session_start();

// Initialize login attempts if not set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lock_time'] = null;
}

$now = time();
$alert_message = '';

// Check for lock time
if ($_SESSION['lock_time'] && $now < $_SESSION['lock_time']) {
    $remaining = $_SESSION['lock_time'] - $now;
    $remaining_minutes = ceil($remaining / 60);
    $alert_message = [
        'icon' => 'error',
        'title' => 'Login Locked',
        'text' => "Please wait {$remaining_minutes} minutes before trying again."
    ];
}

if (isset($_POST["login"])) {
    include('connection.php');

    $myusername = mysqli_real_escape_string($dbconnection, $_POST['myemail']);
    $mypassword = mysqli_real_escape_string($dbconnection, $_POST['mypassword']);

    $sql = "SELECT id, password, confirmation, verification FROM register1 WHERE email = '$myusername'";
    $result = mysqli_query($dbconnection, $sql);

    if ($result) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if ($row) {
            // Check verification
            if ($row['verification'] != 1) {
                $alert_message = [
                    'icon' => 'warning',
                    'title' => 'Account Not Verified',
                    'text' => 'Please verify your email first.'
                ];
            }
            // Check approval
            elseif ($row['confirmation'] != 'approved') {
                $alert_message = [
                    'icon' => 'info',
                    'title' => 'Account Pending Approval',
                    'text' => 'Please wait for your account to be approved by the administrator.'
                ];
            }
            // Check password
            elseif (password_verify($mypassword, $row['password'])) {
                $_SESSION['login_attempts'] = 0;
                $_SESSION['lock_time'] = null;
                $_SESSION['login_user'] = $myusername;
                $_SESSION['register1_id'] = $row['id'];

                $register1_id = $row['id'];
    $rental_check_sql = "SELECT id FROM rental WHERE register1_id = '$register1_id'";
    $rental_check_result = mysqli_query($dbconnection, $rental_check_sql);

    if (mysqli_num_rows($rental_check_result) > 0) {
                    $alert_message = [
                        'icon' => 'success',
                        'title' => 'Login Successful',
                        'text' => 'Welcome back! Redirecting to your dashboard.'
                    ];
                    echo '<script>setTimeout(function(){ window.location.href = "landlord/dashboard.php"; }, 1500);</script>';
                } else {
                    echo '<script>setTimeout(function(){ window.location.href = "landlord/create.php"; }, 1500);</script>';
                }
} else {
                handle_failed_login();
            }
        } else {
            handle_failed_login();
        }
    } else {
        $alert_message = [
            'icon' => 'error',
            'title' => 'Error',
            'text' => mysqli_error($dbconnection)
        ];
    }
}

function handle_failed_login() {
    global $now, $alert_message;
    $_SESSION['login_attempts']++;

    if ($_SESSION['login_attempts'] >= 3) {
        if ($_SESSION['lock_time'] === null || $now > $_SESSION['lock_time']) {
            if ($_SESSION['login_attempts'] > 6) {
                $_SESSION['lock_time'] = $now + 3600; // 1 hour
            } else {
                $_SESSION['lock_time'] = $now + 180; // 3 minutes
            }
        }
    }

    $remaining_attempts = max(0, 3 - ($_SESSION['login_attempts'] % 3));
    $lock_message = $_SESSION['login_attempts'] >= 3 ? 
        'Your account is temporarily locked. Please try again later.' : 
        "You have $remaining_attempts attempts left.";

    $alert_message = [
        'icon' => 'error',
        'title' => 'Login Failed',
        'text' => $lock_message
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="bhh.jpg">
    <title>Login - MADRIE-BH</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style type="text/css">
        @import url('https://fonts.googleapis.com/css?family=Roboto:300,400,500,700');

       body {
    background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('b.png') no-repeat center center fixed;
    background-size: 70%;
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh; /* Ensures full screen coverage */
    margin: 0;
    padding: 0;
    }
    @media (max-width: 768px) {
    body {
         
      
        min-height: 100vh;
         background-size:cover; 
    }
    }

        .login-page {
            width: 330px;
            padding: 8% 0 0;
            margin: auto;
            height: 500px;
        }

        .form {
            position: relative;
            z-index: 1;
            background:white;
            max-width: 500px;
            margin: 0 auto 100px;
            padding: 45px;
            text-align: center;
            box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
            border-radius: 20px;
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
            color: black !important;
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
</style>
</head>
<body>
<div class="login-page">
    <div class="form">
        <h2>Login</h2>
        <form class="login-form" action="" method="POST">
            <div class="input-container">
                <i class="fa fa-envelope icon"></i>
                <input type="text" name="myemail" placeholder="Email" required/>
            </div>
            <div class="input-container">
                <i class="fa fa-lock icon"></i>
                <input type="password" name="mypassword" id="mypassword" placeholder="Password" required/>
                <i class="fa fa-eye" id="togglePassword" style="cursor: pointer; position: absolute; right: 15px; top: 35%; transform: translateY(-50%); color: #888;"></i>
            </div>
            <!-- Forgot Password link positioned to the right under the password field -->
            <p style="text-align: left; margin-top: -15px; margin-bottom: 15px; font-size: 12px;">
                <a href="forgot_pass.php" style="color: blue; text-decoration: none;">Forgot Password?</a>
            </p>
            <button type="submit" name="login">login</button>
            <p class="message" style="color: white;">Don't have an account? <a href="register_step1.php">Sign up</a></p>
            <p class="message"><a href="index.php">WebPage</a></p>
        </form>
    </div>
</div>
<?php if ($alert_message): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: '<?php echo $alert_message['icon']; ?>',
                title: '<?php echo $alert_message['title']; ?>',
                text: '<?php echo $alert_message['text']; ?>',
                <?php if ($alert_message['icon'] === 'success'): ?>
                showConfirmButton: false,
                timer: 1500 // Auto-hide the success message after 1.5 seconds
                <?php endif; ?>
            });
            <?php if (isset($_SESSION['lock_time']) && $_SESSION['lock_time'] > $now): ?>
            document.querySelector("button[name='login']").disabled = true;
            startCountdown(<?php echo $_SESSION['lock_time'] - $now; ?>);
            <?php endif; ?>
        });
    </script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('mypassword');

    togglePassword.addEventListener('click', function () {
        // Toggle the type attribute
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle the eye icon
        this.classList.toggle('fa-eye-slash');
    });
    
    function startCountdown(remainingTime) {
    const countdownDisplay = document.createElement('div');
    countdownDisplay.style.color = 'red';
    countdownDisplay.style.marginTop = '10px';
    document.querySelector('.form').appendChild(countdownDisplay);

    const countdownInterval = setInterval(function () {
        if (remainingTime <= 0) {
            clearInterval(countdownInterval);
            countdownDisplay.textContent = "You can now try logging in again.";
            document.querySelector("button[name='login']").disabled = false; // Re-enable the login button
        } else {
            countdownDisplay.textContent = "Time remaining: " + Math.ceil(remainingTime / 60) + " minute(s)";
            remainingTime -= 1;
        }
    }, 1000);
}

</script>
</body>
</html>
