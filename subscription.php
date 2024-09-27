<?php
session_start();
include('connection.php');
$msg = "";

// Check if user_id is set in session
if (!isset($_SESSION['user_id'])) {
    $msg = "<div class='alert alert-danger'>You need to log in to select a subscription.</div>";
} else {
    if (isset($_POST['select_subscription'])) {
        $subscription = $_POST['subscription'];
        $user_id = $_SESSION['user_id']; // Get the user ID from the session
        $start_date = date('Y-m-d');
        $end_date = '';

        // Calculate the end date based on the selected subscription
        if ($subscription == 'free_trial') {
            $end_date = date('Y-m-d', strtotime('+1 month'));
        } elseif ($subscription == '6_months') {
            $end_date = date('Y-m-d', strtotime('+6 months'));
        } elseif ($subscription == '1_year') {
            $end_date = date('Y-m-d', strtotime('+1 year'));
        }

        // Insert the subscription details into the database
        $query = "INSERT INTO subscriptions (user_id, subscription_type, start_date, end_date, status) 
                  VALUES ('$user_id', '$subscription', '$start_date', '$end_date', 'active')";
        
        if (mysqli_query($dbconnection, $query)) {
            // Successful insertion, redirect to create.php
            header("Location: landlord/create.php"); // Adjust the path if necessary
            exit();
        } else {
            $msg = "<div class='alert alert-danger'>Failed to select subscription. Please try again.</div>";
        }
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
    <title>Select Subscription</title>
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

        .subscription-options {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .subscription-option {
            display: flex;
            align-items: center;
            gap: 1rem;
            width: 100%;
            max-width: 400px;
            padding: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .subscription-option:hover {
            background-color: #f0f0f0;
        }

        .subscription-option input {
            margin-right: 1rem;
        }

        .note {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #666;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup" style="left: 50%; z-index: 99;">
                <form action="create.php" method="POST" class="sign-in-form">
                    <h2 class="title">Select Your Subscription</h2>
                    <?php echo $msg ?>
                    <div class="subscription-options">
                        <label class="subscription-option">
                            <input type="radio" name="subscription" value="free_trial" required />
                            <span>One Month Free Trial</span>
                        </label>
                        <label class="subscription-option">
                            <input type="radio" name="subscription" value="6_months" required />
                            <span>6 Months - 400 pesos</span>
                        </label>
                        <label class="subscription-option">
                            <input type="radio" name="subscription" value="1_year" required />
                            <span>One Year - 7500 pesos</span>
                        </label>
                    </div>
                    <div class="note">
                        <p>* Monthly subscription of 80 pesos applies after the free trial expires.</p>
                    </div>
                    <input type="submit" name="select_subscription" value="Subscribe" class="btn solid" />
                </form>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
</body>

</html>
