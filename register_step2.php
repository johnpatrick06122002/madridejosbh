<?php
session_start();
// Security Headers
 
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

    // Flag to track registration status
    $registration_successful = false;

    // Handle successful registration
    if ($stmt->execute()) {
        $registration_successful = true;
    } else {
        $registration_error = "Registration failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --primary-color: #3b82f6;
            --background-color: #f3f4f6;
            --text-color: #1f2937;
            --input-border-color: #d1d5db;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            line-height: 1.6;
            color: var(--text-color);
        }

        .register-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 30px;
            transition: transform 0.3s ease;
        }

        .register-container:hover {
            transform: translateY(-5px);
        }

        .register-form h2 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 25px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1.5px solid var(--input-border-color);
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-group input[type="file"] {
            padding: 10px;
            border-style: dashed;
        }

        .submit-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .submit-btn:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        @media (max-width: 480px) {
            .register-container {
                width: 95%;
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-form">
            <h2>Complete Your Registration</h2>
            <form id="registrationForm" action="register_step2" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="firstname">First Name</label>
                    <input type="text" name="firstname" id="firstname" required>
                </div>

                <div class="form-group">
                    <label for="middlename">Middle Name (Optional)</label>
                    <input type="text" name="middlename" id="middlename">
                </div>

                <div class="form-group">
                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" id="lastname" required>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" required>
                </div>

                <div class="form-group">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" required>
                </div>

                <div class="form-group">
                    <label for="profile_photo">Profile Photo</label>
                    <input type="file" name="profile_photo" id="profile_photo">
                </div>

                <button type="submit" name="submit" class="submit-btn">Submit</button>
            </form>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <script>
        <?php if (isset($registration_successful) && $registration_successful): ?>
            Swal.fire({
                title: 'Registration Successful!',
                text: 'You will now be redirected to the subscription page.',
                icon: 'success',
                confirmButtonText: 'Proceed',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'subscription.php';
                }
            });
        <?php elseif (isset($registration_error)): ?>
            Swal.fire({
                title: 'Registration Failed',
                text: '<?php echo $registration_error; ?>',
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
        <?php endif; ?>
    </script>
</body>
</html>