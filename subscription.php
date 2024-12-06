<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: register_step1.php");
    exit();
}

include('connection.php');

// Check if the user's subscription has expired
$user_email = $_SESSION['email'];

// Get the user's active subscription
$stmt = $dbconnection->prepare("SELECT id, plan, start_date, status FROM subscriptions WHERE register1_id = (SELECT id FROM register1 WHERE email = ?) AND status = 'active'");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$subscription = $result->fetch_assoc();

if ($subscription) {
    $current_date = new DateTime();
    $start_date = new DateTime($subscription['start_date']);

    // Determine expiration date based on the plan
    if ($subscription['plan'] === 'monthly') {
        $expiration_date = (clone $start_date)->modify('+30 days');
    } elseif ($subscription['plan'] === 'trial') {
        $expiration_date = (clone $start_date)->modify('+30 days');
    } elseif ($subscription['plan'] === 'yearly') {
        $expiration_date = (clone $start_date)->modify('+1 year');
    }

    // Mark the subscription as expired if the current date is beyond the expiration date
    if ($current_date > $expiration_date) {
        $stmt2 = $dbconnection->prepare("UPDATE subscriptions SET status = 'inactive' WHERE id = ?");
        $stmt2->bind_param("i", $subscription['id']);
        $stmt2->execute();
    }
}

// Subscription processing logic
if (isset($_POST['subscribe'])) {
    $subscription_plan = $_POST['plan'];
    $user_email = $_SESSION['email'];

    // Get the register1_id of the user based on their email
    $stmt = $dbconnection->prepare("SELECT id FROM register1 WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $register1_id = $row['id'];

    // Insert the subscription plan into the subscriptions table
    $stmt2 = $dbconnection->prepare("INSERT INTO subscriptions (register1_id, plan, status, start_date) VALUES (?, ?, 'active', NOW())");
    $stmt2->bind_param("is", $register1_id, $subscription_plan);

    if ($stmt2->execute()) {
        if ($subscription_plan === 'trial') {
            // Set a session flag for SweetAlert
            $_SESSION['alert_type'] = 'success';
            $_SESSION['alert_message'] = 'Free Trial Activated! Enjoy your free 1-month trial.';
            $_SESSION['redirect'] = 'login.php';
        } else {
            // Set a session flag for SweetAlert for other plans
            $_SESSION['alert_type'] = 'success';
            $_SESSION['alert_message'] = 'Subscription Successful!';
            $_SESSION['redirect'] = 'login.php';
        }
    } else {
        // Set an error alert
        $_SESSION['alert_type'] = 'error';
        $_SESSION['alert_message'] = 'Subscription failed. Please try again.';
    }

    header("Location: subscription.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Subscription Plan</title>
      <link rel="shortcut icon" type="x-icon" href="b.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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
            max-width: 800px;
            width: 100%;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .plans {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .plan-card {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .plan-card input {
            margin-top: 10px;
        }

        .plan-card.free-trial {
            background-color: #e7f9f0;
            border-color: #42ba96;
        }

        button {
            margin-top: 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Select Your Subscription Plan</h2>
        <form action="subscription" method="POST">
            <div class="plans">
                <!-- Free Trial Plan -->
                <div class="plan-card free-trial">
                    <h3>Free Trial</h3>
                    <p>1 Month - Free</p>
                    <input type="radio" name="plan" id="plan-trial" value="trial" required>
                    <label for="plan-trial">Select</label>
                </div>

                <!-- Monthly Plan -->
                <div class="plan-card">
                    <h3>Monthly Plan</h3>
                    <p>$10/month</p>
                    <input type="radio" name="plan" id="plan1" value="monthly" required>
                    <label for="plan1">Select</label>
                </div>

                <!-- 3 Months Plan -->
                <div class="plan-card">
                    <h3>3 Months Plan</h3>
                    <p>$25/3 months</p>
                    <input type="radio" name="plan" id="plan3" value="3months" required>
                    <label for="plan3">Select</label>
                </div>

                <!-- 6 Months Plan -->
                <div class="plan-card">
                    <h3>6 Months Plan</h3>
                    <p>$50/6 months</p>
                    <input type="radio" name="plan" id="plan6" value="6months" required>
                    <label for="plan6">Select</label>
                </div>

                <!-- Yearly Plan -->
                <div class="plan-card">
                    <h3>Yearly Plan</h3>
                    <p>$100/year</p>
                    <input type="radio" name="plan" id="plan12" value="yearly" required>
                    <label for="plan12">Select</label>
                </div>
            </div>

            <button type="submit" name="subscribe">Subscribe</button>
        </form>
    </div>

    <!-- SweetAlert Trigger -->
    <script>
        <?php if (isset($_SESSION['alert_type'])): ?>
            Swal.fire({
                icon: '<?php echo $_SESSION['alert_type']; ?>',
                title: '<?php echo $_SESSION['alert_message']; ?>',
                confirmButtonText: 'Continue'
            }).then(() => {
                <?php if (isset($_SESSION['redirect'])): ?>
                window.location.href = '<?php echo $_SESSION['redirect']; ?>';
                <?php endif; ?>
            });
            <?php 
            unset($_SESSION['alert_type'], $_SESSION['alert_message'], $_SESSION['redirect']);
            ?>
        <?php endif; ?>
    </script>
</body>
</html>
