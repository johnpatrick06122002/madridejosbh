<?php
// Set Content Security Policy
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net/npm/sweetalert2@11; style-src 'self' 'unsafe-inline';");

// Include database connection
include('connection.php');

// PHPMailer dependencies
 require 'vendor_copy/autoload.php'; // PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to sanitize input
function sanitizeInput($input, $type) {
    $input = trim($input);
    $input = stripslashes($input);
    
    switch ($type) {
        case 'name':
            return preg_replace("/[^a-zA-Z\s'-]/", "", $input);
        case 'email':
            return filter_var($input, FILTER_SANITIZE_EMAIL);
        case 'number':
            return preg_replace("/[^0-9]/", "", $input);
        case 'address':
            return preg_replace("/[^a-zA-Z0-9\s,.'-]/", "", $input);
        default:
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}

// Get and validate the rental ID from the URL
$rental_id = filter_input(INPUT_GET, 'bh_id', FILTER_VALIDATE_INT);
if ($rental_id === false || $rental_id <= 0) {
    echo '<script>Swal.fire("Error", "Invalid rental ID.", "error");</script>';
    exit();
}

// Fetch rental and landlord details securely
$sql = "
    SELECT rental.*, register1.email, register1.id AS register1_id 
    FROM rental 
    JOIN register1 ON rental.register1_id = register1.id 
    WHERE rental.rental_id = ?
";
$stmt = $dbconnection->prepare($sql);
$stmt->bind_param('i', $rental_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo '<script>Swal.fire("Error", "Rental not found.", "error");</script>';
    exit();
}

// Landlord's email
$landlord_email = $row['email'];

if (isset($_POST["booknow"])) {
    // Sanitize and validate user inputs
    $firstname = sanitizeInput($_POST['firstname'], 'name');
    $middlename = sanitizeInput($_POST['middlename'], 'name');
    $lastname = sanitizeInput($_POST['lastname'], 'name');
    $age = filter_var($_POST['age'], FILTER_VALIDATE_INT);
    $gender = sanitizeInput($_POST['gender'], 'name');
    $gcash_number = sanitizeInput($_POST['gcash_number'], 'number');
    $email = sanitizeInput($_POST['email'], 'email');
    $address = sanitizeInput($_POST['Address'], 'address');
    $paid_amount = filter_var($_POST['paid_amount'], FILTER_VALIDATE_FLOAT);

    // Validate required fields
    if (!$firstname || !$lastname || !$age || !$gender || !$gcash_number || !$email || !$address || $paid_amount === false) {
        echo '<script>Swal.fire("Error", "All fields are required and must be valid.", "error");</script>';
        exit();
    }

    // File upload validation
    $gcash_picture = $_FILES['gcash_picture'];
    $target_dir = "uploads/gcash_pictures/";
    $file_extension = strtolower(pathinfo($gcash_picture["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $allowed_mime_types = ['image/jpeg', 'image/png'];

    if (!in_array($file_extension, $allowed_extensions) || 
        !in_array($gcash_picture['type'], $allowed_mime_types)) {
        echo '<script>Swal.fire("Error", "Only JPG, JPEG, and PNG files are allowed.", "error");</script>';
        exit();
    }

    if ($gcash_picture['size'] > 5000000) { // 5MB limit
        echo '<script>Swal.fire("Error", "File is too large. Maximum size is 5MB.", "error");</script>';
        exit();
    }

    if (move_uploaded_file($gcash_picture["tmp_name"], $target_file)) {
        // Insert booking into the database
        $sql_book = "
            INSERT INTO book (firstname, middlename, lastname, age, gender, contact_number, email, register1_id, bhouse_id, Address, gcash_picture, paid_amount)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt_book = $dbconnection->prepare($sql_book);
        $stmt_book->bind_param('sssisississd', 
            $firstname, 
            $middlename, 
            $lastname, 
            $age, 
            $gender, 
            $gcash_number, 
            $email, 
            $row['register1_id'], 
            $rental_id, 
            $address, 
            $target_file, 
            $paid_amount
        );

        if ($stmt_book->execute()) {
            echo '<script>Swal.fire("Success", "Successfully Booked. Please complete your payment.", "success");</script>';

            // Send email to the landlord using PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'lucklucky2100@gmail.com';
                $mail->Password = 'kjxf ptjv erqn yygv';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('lucklucky2100@gmail.com', 'Your Site Name');
                $mail->addAddress($landlord_email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'New Booking Alert';
                $mail->Body = "
                    <h3>New Booking for Your Rental Property</h3>
                    <p><strong>First Name:</strong> " . htmlspecialchars($firstname, ENT_QUOTES, 'UTF-8') . "</p>
                    <p><strong>Last Name:</strong> " . htmlspecialchars($lastname, ENT_QUOTES, 'UTF-8') . "</p>
                    <p><strong>Email:</strong> " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</p>
                    <p><strong>Paid Amount:</strong> " . htmlspecialchars($paid_amount, ENT_QUOTES, 'UTF-8') . "</p>
                    <p><strong>GCash Picture:</strong> <img src='" . htmlspecialchars($target_file, ENT_QUOTES, 'UTF-8') . "' alt='GCash Payment' width='150'></p>
                    <p>Kindly log in to your account to view more details.</p>
                ";

                $mail->send();
                echo '<script>Swal.fire("Success", "Notification sent to the landlord.", "success");</script>';
            } catch (Exception $e) {
                error_log("Mailer Error: " . $mail->ErrorInfo);
                echo "<script>Swal.fire('Error', 'There was an error sending the notification. Please contact support.', 'error');</script>";
            }
        } else {
            error_log("Database Error: " . $dbconnection->error);
            echo '<script>Swal.fire("Error", "There was an error processing your booking. Please try again later.", "error");</script>';
        }
    } else {
        echo '<script>Swal.fire("Error", "Error uploading file.", "error");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Now</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Your existing CSS */
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-primary:hover {
            background-color: #0069d9;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .container {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Book Now</h2>
        <form id="bookingForm" method="POST" action="book.php?bh_id=<?php echo htmlspecialchars($rental_id, ENT_QUOTES, 'UTF-8'); ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="firstname">First Name:</label>
                <input type="text" class="form-control" id="firstname" name="firstname" required>
            </div>
            <div class="form-group">
                <label for="middlename">Middle Name:</label>
                <input type="text" class="form-control" id="middlename" name="middlename">
            </div>
            <div class="form-group">
                <label for="lastname">Last Name:</label>
                <input type="text" class="form-control" id="lastname" name="lastname" required>
            </div>
            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" class="form-control" id="age" name="age" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="gcash_number">GCash Number:</label>
                <input type="text" class="form-control" id="gcash_number" name="gcash_number" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="Address">Address:</label>
                <input type="text" class="form-control" id="Address" name="Address" required>
            </div>
            <div class="form-group">
                <label for="gcash_picture">GCash Payment Proof (JPG/PNG only):</label>
                <input type="file" class="form-control-file" id="gcash_picture" name="gcash_picture" accept=".jpg,.jpeg,.png" required>
            </div>
            <div class="form-group">
                <label for="paid_amount">Paid Amount:</label>
                <input type="number" class="form-control" id="paid_amount" name="paid_amount" step="0.01" required>
            </div>
            <button type="submit" class="btn btn-primary" name="booknow">Book Now</button>
        </form>
    </div>

    <script>
    // JavaScript for client-side validation
    document.getElementById('bookingForm').addEventListener('submit', function(event) {
        var isValid = true;

        // Validate name fields (letters, hyphens, apostrophes, spaces only)
        ['firstname', 'middlename', 'lastname'].forEach(function(field) {
            var value = document.getElementById(field).value;
            if (!/^[A-Za-z\s'-]*$/.test(value)) {
                isValid = false;
                Swal.fire('Error', 'Please enter a valid ' + field + '.', 'error');
            }
        });

        // Validate address field (letters, numbers, commas, periods, dashes, and spaces)
        var address = document.getElementById('Address').value;
        if (!/^[A-Za-z0-9\s,.'-]*$/.test(address)) {
            isValid = false;
            Swal.fire('Error', 'Please enter a valid address.', 'error');
        }

        // Validate email
        var email = document.getElementById('email').value;
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            isValid = false;
            Swal.fire('Error', 'Please enter a valid email address.', 'error');
        }

        // Validate GCash number (numbers only)
        var gcashNumber = document.getElementById('gcash_number').value;
        if (!/^\d+$/.test(gcashNumber)) {
            isValid = false;
            Swal.fire('Error', 'Please enter a valid GCash number (numbers only).', 'error');
        }

        if (!isValid) {
            event.preventDefault();
        }
    });
    </script>
</body>
</html>