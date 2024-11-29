<?php
// Set Content Security Policy


// Include database connection
include('connection.php');

// PHPMailer dependencies
require 'vendor_copy/autoload.php';

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

// Initialize payment type variables
$payment_type = '';
$paid_amount_value = 0;
$installment_months = 0;

if (!is_null($row['downpayment_amount']) && $row['downpayment_amount'] > 0) {
    $payment_type = 'downpayment';
    $paid_amount_value = $row['downpayment_amount'];
} elseif (!is_null($row['installment_amount']) && $row['installment_amount'] > 0 && !is_null($row['installment_months'])) {
    $payment_type = 'installment';
    $paid_amount_value = $row['installment_amount'];
    $installment_months = $row['installment_months'];
} else {
    echo '<script>Swal.fire("Error", "No valid payment option set for this rental.", "error");</script>';
    exit();
}

$landlord_email = $row['email'];

if (isset($_POST["booknow"])) {
    $firstname = sanitizeInput($_POST['firstname'], 'name');
    $middlename = sanitizeInput($_POST['middlename'], 'name');
    $lastname = sanitizeInput($_POST['lastname'], 'name');
    $age = filter_var($_POST['age'], FILTER_VALIDATE_INT);
    $gender = sanitizeInput($_POST['gender'], 'name');
    $gcash_number = sanitizeInput($_POST['gcash_number'], 'number');
    $email = sanitizeInput($_POST['email'], 'email');
    $address = sanitizeInput($_POST['Address'], 'address');
    $paid_amount_value = filter_var($_POST['paid_amount'], FILTER_VALIDATE_FLOAT);

    if (!$firstname || !$lastname || !$age || !$gender || !$gcash_number || !$email || !$address || $paid_amount_value === false) {
        echo '<script>Swal.fire("Error", "All fields are required and must be valid.", "error");</script>';
        exit();
    }

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

    if ($gcash_picture['size'] > 5000000) {
        echo '<script>Swal.fire("Error", "File is too large. Maximum size is 5MB.", "error");</script>';
        exit();
    }

    if (move_uploaded_file($gcash_picture["tmp_name"], $target_file)) {
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
            $paid_amount_value
        );

        if ($stmt_book->execute()) {
            echo '<script>Swal.fire("Success", "Successfully Booked. Please complete your payment.", "success");</script>';

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'lucklucky2100@gmail.com';
                $mail->Password = 'kjxf ptjv erqn yygv';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('lucklucky2100@gmail.com', 'Your Site Name');
                $mail->addAddress($landlord_email);

                $mail->isHTML(true);
                $mail->Subject = 'New Booking Alert';
                $mail->Body = "
                    <h3>New Booking for Your Rental Property</h3>
                    <p><strong>First Name:</strong> " . htmlspecialchars($firstname, ENT_QUOTES, 'UTF-8') . "</p>
                    <p><strong>Last Name:</strong> " . htmlspecialchars($lastname, ENT_QUOTES, 'UTF-8') . "</p>
                    <p><strong>Email:</strong> " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</p>
                    <p><strong>Paid Amount:</strong> " . htmlspecialchars($paid_amount_value, ENT_QUOTES, 'UTF-8') . "</p>
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
      <link rel="shortcut icon" type="x-icon" href="b.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #5f27cd;
            --background-color: #f4f6ff;
            --text-color: #333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--background-color) 0%, #e6e9f0 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 650px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 600;
            position: relative;
        }

        h2::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 4px;
            background: var(--secondary-color);
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-weight: 500;
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .btn-primary, .btn-back {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.4);
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .btn-back {
            background: #6c757d;
            color: white;
        }

        .btn-back:hover {
            background: #495057;
            transform: translateY(-2px);
        }

        #image-preview {
            max-width: 200px;
            max-height: 200px;
            border: 2px dashed #e0e0e0;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }

        #image-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .btn-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Book Your Rental</h2>

        <div class="btn-container">
            <a href="view.php?bh_id=<?php echo $rental_id; ?>" class="btn-back">Back to Details</a>
        </div>

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
            <h3 style="color: var(--primary-color); margin-bottom: 15px;">Payment Type: <?php echo ucfirst($payment_type); ?></h3>
            
            <div class="form-group">
                <label for="gcash_picture">GCash Payment Proof (JPG/PNG only):</label>
                <input type="file" class="form-control" id="gcash_picture" name="gcash_picture" accept=".jpg,.jpeg,.png" required>
            </div>

            <div class="form-group">
                <label>Preview:</label>
                <div id="image-preview"></div>
            </div>

            <div class="form-group">
                <label for="reference_number">Reference Number:</label>
                <input type="text" class="form-control" id="reference_number" name="reference_number" readonly>
            </div>

            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="text" class="form-control" id="amount" name="amount" readonly>
            </div>

            <div class="form-group">
                <label><?php echo ucfirst($payment_type); ?> Amount:</label>
                <p style="color: var(--secondary-color); font-weight: 600;"><?php echo htmlspecialchars($paid_amount_value, ENT_QUOTES, 'UTF-8'); ?></p>
                <input type="hidden" name="paid_amount" value="<?php echo htmlspecialchars($paid_amount_value, ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <?php if ($payment_type == 'installment'): ?>
            <div class="form-group">
                <label>Installment Months:</label>
                <p style="color: var(--secondary-color); font-weight: 600;"><?php echo htmlspecialchars($installment_months, ENT_QUOTES, 'UTF-8'); ?></p>
                <input type="hidden" name="installment_months" value="<?php echo htmlspecialchars($installment_months, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <?php endif; ?>

            <div class="btn-container">
                <button type="submit" class="btn-primary" name="booknow">Book Now</button>
            </div>
        </form>
    </div>
   
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@2.1.1/dist/tesseract.min.js"></script>
<!-- Add PHP variable for payment amount to JavaScript -->
<script>
    const requiredAmount = <?php echo json_encode($paid_amount_value); ?>;
</script>
<script>
$(document).ready(function () {
    $('#gcash_picture').change(function (event) {
        const file = event.target.files[0];
        const previewContainer = $('#image-preview');
        const refNoInput = $('#reference_number');
        const amountInput = $('#amount'); // Amount input for displaying scanned amount
        const fileInput = $('#gcash_picture'); // File input for clearing if needed

        // Clear previous preview, reference number, and amount
        previewContainer.empty();
        refNoInput.val('');
        amountInput.val('');

        if (file && (file.type === 'image/jpeg' || file.type === 'image/png')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = $('<img>').attr('src', e.target.result).css({
                    maxWidth: '100px',
                    maxHeight: '100px'
                });
                previewContainer.append(img); // Display image preview

                // Perform OCR to extract reference number and amount
                Tesseract.recognize(e.target.result, 'eng', {
                    logger: info => console.log(info) // Log OCR progress
                }).then(({ data: { text } }) => {
                    console.log("Extracted Text:", text);

                    // Regex pattern to extract reference number
                    const refNoPattern = /Ref\.?\s*No\.?\s*([\d\s]+)/i;
                    const matchRefNo = text.match(refNoPattern);
                    if (matchRefNo) {
                        refNoInput.val(matchRefNo[1].trim()); // Populate reference number
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Reference Number Not Found',
                            text: 'No reference number was detected in the uploaded image. Please try again with a clearer image.',
                            confirmButtonText: 'OK'
                        });
                    }

                    // Regex pattern to extract amount with decimal (e.g., 1234.56)
                    const amountPattern = /(\d{1,3}(?:,\d{3})*(?:\.\d{2}))/; // Matches a number with decimals
                    const matchAmount = text.match(amountPattern);
                    if (matchAmount) {
                        const scannedAmount = parseFloat(matchAmount[0].replace(/,/g, '')); // Convert to number
                        if (scannedAmount === requiredAmount) {
                            amountInput.val(scannedAmount); // Populate amount if it matches exactly
                        } else {
                            // Clear fields if scanned amount is not exactly equal to the required amount
                            amountInput.val('');
                            refNoInput.val(''); // Clear reference number
                            previewContainer.empty(); // Remove the image preview
                            fileInput.val(''); // Reset file input

                            Swal.fire({
                                icon: 'warning',
                                title: 'Invalid Amount',
                                text: `The scanned amount (${scannedAmount}) does not match the required amount of ${requiredAmount}. Please try again with the correct amount.`,
                                confirmButtonText: 'OK'
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Amount Not Found',
                            text: 'No amount was detected in the uploaded image. Please try again with a clearer image.',
                            confirmButtonText: 'OK'
                        });
                    }
                }).catch(err => {
                    console.error('OCR Error:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'OCR Error',
                        text: 'Failed to extract text from the image. Please try again.',
                        confirmButtonText: 'OK'
                    });
                });
            };
            reader.readAsDataURL(file);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Only JPG, JPEG, and PNG files are allowed.',
                confirmButtonText: 'OK'
            });
        }
    });
});
</script>


</body>
</html>
