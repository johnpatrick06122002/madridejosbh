<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: register_step1.php");
    exit();
}

include('connection.php');

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

    // Insert the subscription plan into a subscription table
    $stmt2 = $dbconnection->prepare("INSERT INTO subscriptions (register1_id, plan, status, start_date) VALUES (?, ?, 'active', NOW())");
    $stmt2->bind_param("is", $register1_id, $subscription_plan);

    if ($stmt2->execute()) {
        // Redirect to create.php upon successful subscription
        header("Location: landlord/createhouse.php");
        exit(); // Ensure no further code is executed
    } else {
        // Display simple error message in case of failure
        echo "<p>Subscription failed. Please try again.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Subscription Plan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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

        .subscription-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .plan {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .plan label {
            font-size: 16px;
            color: #666;
        }

        .plan input {
            width: auto;
        }

        button {
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
    <div class="subscription-form">
        <h2>Select Your Subscription Plan</h2>
        <form action="subscription.php" method="POST">
            <div class="plan">
                <label for="plan1">Monthly Plan - $10/month</label>
                <input type="radio" name="plan" id="plan1" value="monthly" required>
            </div>
            <div class="plan">
                <label for="plan2">Yearly Plan - $100/year</label>
                <input type="radio" name="plan" id="plan2" value="yearly" required>
            </div>

            <button type="submit" name="subscribe">Subscribe</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
