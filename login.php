<?php
session_start();
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; script-src 'self' https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com;");
header("Referrer-Policy: no-referrer");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

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
                     $alert_message = [
                        'icon' => 'success',
                        'title' => 'Login Successful',
                        'text' => 'Please create your boarding house.'
                    ];
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
      <link rel="shortcut icon" type="x-icon" href="b.png">
    <title>Login - MADRIE-BH</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style type="text/css">
    :root {
        --primary-color: #007bff;
        --secondary-color: #43A047;
        --background-color: rgba(0, 0, 0, 0.3);
    }

    body {
        background: linear-gradient(var(--background-color), var(--background-color)), url('b.png') no-repeat center center fixed;
        background-size: 70%;
        font-family: 'Poppins', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        padding: 0;
        color: #333;
    }

    @media (max-width: 768px) {
        body {
            min-height: 100vh;
            background-size: cover; 
        }
    }

    .login-page {
        width: 380px;
        padding: 8% 0 0;
        margin: auto;
        perspective: 1000px;
    }

    .form {
        position: relative;
        z-index: 1;
        background: rgba(255, 255, 255, 0.9);
        margin: 0 auto 100px;
        padding: 45px;
        text-align: center;
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
        border-radius: 15px;
        transform-style: preserve-3d;
        transform: rotateX(10deg) scale(0.95);
        transition: all 0.5s ease;
    }

    .form:hover {
        transform: rotateX(0) scale(1);
        box-shadow: 0 20px 35px rgba(0, 0, 0, 0.3);
    }

    .form h2 {
        color: var(--primary-color);
        margin-bottom: 30px;
        font-weight: 600;
        position: relative;
    }

    .form h2::after {
        content: '';
        position: absolute;
        width: 60px;
        height: 3px;
        background: var(--secondary-color);
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
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
        border: 2px solid #e0e0e0;
        border-radius: 25px;
        font-family: 'Poppins', sans-serif;
        transition: all 0.3s ease;
    }

    .input-container input:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .input-container input:focus + .icon {
        color: var(--secondary-color);
    }

    #togglePassword {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary-color);
        cursor: pointer;
        transition: color 0.3s ease;
    }

    #togglePassword:hover {
        color: var(--secondary-color);
    }

    .form button {
        font-family: 'Poppins', sans-serif;
        text-transform: uppercase;
        background: var(--primary-color);
        width: 100%;
        border: none;
        padding: 15px;
        color: white;
        font-size: 14px;
        border-radius: 25px;
        transition: all 0.3s ease;
        letter-spacing: 1px;
    }

    .form button:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(67, 160, 71, 0.4);
    }

    .form .message {
        margin: 15px 0 0;
        color: #666;
        font-size: 12px;
    }

    .form .message a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
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
        <h2>Logins</h2>
        <form class="login-form" action="" method="POST">
            <div class="input-container">
                <i class="fa fa-envelope icon"></i>
                <input type="text" name="myemail" placeholder="Email" required/>
            </div>
            <div class="input-container">
                <i class="fa fa-lock icon"></i>
                <input type="password" name="mypassword" id="mypassword" placeholder="Password" required/>
                <i class="fa fa-eye" id="togglePassword"></i>
            </div>
            <p style="text-align: left; margin-top: -15px; margin-bottom: 15px; font-size: 12px;">
                <a href="forgot_pass.php">Forgot Password?</a>
            </p>
            <button type="submit" name="login">Login</button>
            <p class="message">Don't have an account? <a href="register_step1.php">Sign up</a></p>
            <p class="message"><a href="index.php">WebPage</a></p>
        </form>
    </div>
</div>

<!-- Existing PHP and JavaScript code remains unchanged -->
<?php if ($alert_message): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: '<?php echo $alert_message['icon']; ?>',
                title: '<?php echo $alert_message['title']; ?>',
                text: '<?php echo $alert_message['text']; ?>',
                <?php if ($alert_message['icon'] === 'success'): ?>
                showConfirmButton: false,
                timer: 1500
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
    // Existing JavaScript code remains unchanged
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('mypassword');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
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
                document.querySelector("button[name='login']").disabled = false;
            } else {
                countdownDisplay.textContent = "Time remaining: " + Math.ceil(remainingTime / 60) + " minute(s)";
                remainingTime -= 1;
            }
        }, 1000);
    }
</script>
</body>
</html>