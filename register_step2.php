<?php
session_start();

// Redirect to step 1 if email session is not set
if (!isset($_SESSION['email'])) {
    header("Location: register_step1.php");
    exit();
}

// Check if form is submitted
if (isset($_POST['submit'])) {
    include('connection.php'); // Replace with your connection file

    // Get form data
    $firstname = $_POST['firstname'];
    $middlename = isset($_POST['middlename']) ? $_POST['middlename'] : '';
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $profile_photo = isset($_FILES['profile_photo']['name']) ? $_FILES['profile_photo']['name'] : '';

    // Handle file upload
    if ($profile_photo) {
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], "uploads/" . basename($profile_photo));
    }

    // Get the ID from the `register1` table using the session email
    $stmt1 = $dbconnection->prepare("SELECT id FROM register1 WHERE email = ?");
    $stmt1->bind_param("s", $_SESSION['email']);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $row1 = $result1->fetch_assoc();
    $register1_id = $row1['id']; // Fetch the register1 ID

    // Insert user data into `register2` table
    $stmt = $dbconnection->prepare("INSERT INTO register2 (register1_id, firstname, middlename, lastname, address, contact_number, profile_photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $register1_id, $firstname, $middlename, $lastname, $address, $contact_number, $profile_photo);

    // Handle successful registration
    if ($stmt->execute()) {
        // Redirect to subscription page upon success
        header("Location: subscription.php");
    } else {
        // Display error message
        echo "<p>Registration failed. Please try again.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Step 2</title>
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

        .register-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            margin-bottom: 5px;
            display: block;
            color: #666;
        }

        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
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
    <div class="register-form">
        <h2>Complete Your Registration</h2>
        <form action="register_step2.php" method="POST" enctype="multipart/form-data">
            <label for="firstname">First Name</label>
            <input type="text" name="firstname" id="firstname" required>

            <label for="middlename">Middle Name (Optional)</label>
            <input type="text" name="middlename" id="middlename">

            <label for="lastname">Last Name</label>
            <input type="text" name="lastname" id="lastname" required>

            <label for="address">Address</label>
            <input type="text" name="address" id="address" required>

            <label for="contact_number">Contact Number</label>
            <input type="text" name="contact_number" id="contact_number" required>

            <label for="profile_photo">Profile Photo</label>
            <input type="file" name="profile_photo" id="profile_photo">

            <button type="submit" name="submit">Submit</button>
        </form>
    </div>
</body>
</html>
