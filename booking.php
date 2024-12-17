<?php
include('connection.php');
session_start();
require 'vendor_copy/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function sanitizeInput($data, $type) {
    switch ($type) {
        case 'string':
            return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
        case 'int':
            return filter_var($data, FILTER_VALIDATE_INT);
        default:
            return $data;
    }
}

$payment_id = isset($_GET['payment_id']) ? sanitizeInput($_GET['payment_id'], 'int') : null;

// Verify payment_id
if ($payment_id) {
    $sql_check = "SELECT payment_id FROM payment WHERE payment_id = ?";
    $stmt_check = $dbconnection->prepare($sql_check);
    if ($stmt_check) {
        $stmt_check->bind_param('i', $payment_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        if ($result->num_rows === 0) {
            die('<script>Swal.fire("Error", "Invalid or already processed payment.", "error");</script>');
        }
        $stmt_check->close();
    }
} else {
    die('<script>Swal.fire("Error", "Payment ID is required.", "error").then(() => {window.location.href = "payment.php";});</script>');
}

// Handle OTP sending
if (isset($_POST['send_otp'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if ($email) {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
    
        $stmt_otp = $dbconnection->prepare("INSERT INTO otp (email, otp) VALUES (?, ?)");
        $stmt_otp->bind_param('ss', $email, $otp);
        if ($stmt_otp->execute() && sendOtpBtn ($email, $otp)) {
            echo json_encode(['status' => 'success', 'message' => 'OTP sent to your email.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send OTP.']);
        }
        $stmt_otp->close();
        exit();
    }             
}

// Handle OTP verification
if (isset($_POST['verify_otp'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $otp = sanitizeInput($_POST['otp'], 'string');

    $stmt_verify = $dbconnection->prepare("SELECT otp_id FROM otp WHERE email = ? AND otp = ? AND created_at >= NOW() - INTERVAL 10 MINUTE");
    $stmt_verify->bind_param('ss', $email, $otp);
    $stmt_verify->execute();
    $result = $stmt_verify->get_result();

    if ($result->num_rows === 1) {
        echo json_encode(['status' => 'success', 'message' => 'OTP verified successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired OTP.']);
    }
    $stmt_verify->close();
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $otp = sanitizeInput($_POST['otp'], 'string');

    // Verify OTP again for security
    $stmt_verify = $dbconnection->prepare("SELECT otp_id FROM otp WHERE email = ? AND otp = ? AND created_at >= NOW() - INTERVAL 10 MINUTE");
    $stmt_verify->bind_param('ss', $email, $otp);
    $stmt_verify->execute();
    $result = $stmt_verify->get_result();

    if ($result->num_rows === 1) {
        $otp_row = $result->fetch_assoc();
        $otp_id = $otp_row['otp_id'];

        // Process booking
        $firstname = sanitizeInput($_POST['firstname'], 'string');
        $middlename = sanitizeInput($_POST['middlename'], 'string');
        $lastname = sanitizeInput($_POST['lastname'], 'string');
        $age = sanitizeInput($_POST['age'], 'int');
        $gender = sanitizeInput($_POST['gender'], 'string');
        $contact_number = sanitizeInput($_POST['contact_number'], 'string');
        $address = sanitizeInput($_POST['address'], 'string');
        
        if (!$firstname || !$lastname || !$age || !$gender || !$contact_number || !$address) {
            echo json_encode(['status' => 'error', 'message' => 'All required fields must be filled.']);
            exit();
        }

        $book_ref_no = 'BRN-' . strtoupper(bin2hex(random_bytes(4)));
        
        $sql_booking = "INSERT INTO booking (book_ref_no, payment_id, firstname, middlename, lastname, email, age, gender, 
                        contact_number, Address, status, otp_id) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?)";

        $stmt_booking = $dbconnection->prepare($sql_booking);
        $stmt_booking->bind_param('sissssssssi', $book_ref_no, $payment_id, $firstname, $middlename, $lastname, 
                                $email, $age, $gender, $contact_number, $address, $otp_id);

       if ($stmt_booking->execute()) {
    if (sendConfirmationEmail($email, $book_ref_no, $firstname, $lastname)) {
        echo json_encode(['status' => 'success', 'message' => 'Booking Confirmed!']);
    } else {
        echo json_encode(['status' => 'warning', 'message' => 'Booking saved, but confirmation email failed to send.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save the booking.']);
}


        $stmt_booking->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired OTP.']);
    }
    exit();
}

function sendOtpBtn($email, $otp) {
 $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  
        $mail->SMTPAuth = true;
        $mail->Username = 'madridejosbh2@gmail.com';  
        $mail->Password = 'ougf gwaw ezwh jmng';  
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;  

        $mail->setFrom('madridejosbh2@gmail.com', 'Madridejos Bh finder');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your Booking OTP Code';
        $mail->Body    = "Your OTP code for booking verification is: <strong>$otp</strong><br>
                          This code will expire in 10 minutes.";
        // Send the email
        if ($mail->send()) {
            return true;
        } else {
            // Log error if email fails to send
            error_log("Mailer Error: " . $mail->ErrorInfo);
            return false;
        }
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
function sendConfirmationEmail($email, $book_ref_no, $firstname, $lastname) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'madridejosbh2@gmail.com';
        $mail->Password = 'ougf gwaw ezwh jmng';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('madridejosbh2@gmail.com', 'Madridejos Bh Finder');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Booking Confirmation';
        $mail->Body = "
            <h1>Booking Confirmation</h1>
            <p>Dear $firstname $lastname,</p>
            <p>Thank you for your booking. Your reference number is: <strong>$book_ref_no</strong></p>
            <p>We look forward to serving you. For any inquiries, feel free to contact us.</p>
            <p>Best regards,<br>Madridejos Bh Finder Team</p>
        ";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #2563EB;
            --secondary-color: #3B82F6;
            --success-color: #10B981;
            --background-color: #F3F4F6;
            --border-radius: 12px;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #1F2937;
            line-height: 1.5;
        }

        .booking-container {
            max-width: 800px;
            background: white;
            padding: 2.5rem;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin: 2rem auto;
        }

        .page-title {
            color: #111827;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .form-section {
            background: #FFFFFF;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            border: 1px solid #E5E7EB;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid #E5E7EB;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3E%3Cpath d='M4.646 6.646a.5.5 0 0 1 .708 0L8 9.293l2.646-2.647a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #6B7280;
            border: none;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #4B5563;
            transform: translateY(-1px);
        }

        #otpSection {
            position: relative;
            padding: 1.5rem;
            background: #F9FAFB;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
        }

        .otp-verified {
            border-color: var(--success-color) !important;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .booking-container {
                padding: 1.5rem;
                margin: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .form-section {
                padding: 1rem;
            }

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        #bookingSection {
            animation: fadeIn 0.5s ease-out;
        }

        /* Grid Layout for Form Fields */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="booking-container">
            <h2 class="page-title">Book Your Reservation</h2>
            <form id="bookingForm" method="POST">
                <div id="otpSection" class="form-section">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="d-flex gap-2">
                            <input type="email" class="form-control" id="email" name="email" required>
                            <button type="button" id="sendOtpBtn" class="btn btn-secondary">Send OTP</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="otp" class="form-label">Verification Code (OTP)</label>
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control" id="otp" name="otp" required>
                            <button type="button" id="verifyOtpBtn" class="btn btn-secondary">Verify</button>
                        </div>
                    </div>
                </div>

                <div id="bookingSection" style="display: none;">
                    <div class="form-section">
                        <h3 class="mb-4">Personal Information</h3>
                        <div class="form-grid">
                            <div class="mb-3">
                                <label for="firstname" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstname" name="firstname" required>
                            </div>
                            <div class="mb-3">
                                <label for="middlename" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middlename" name="middlename">
                            </div>
                            <div class="mb-3">
                                <label for="lastname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastname" name="lastname" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="mb-4">Additional Details</h3>
                        <div class="form-grid">
                            <div class="mb-3">
                                <label for="age" class="form-label">Age</label>
                                <input type="number" class="form-control" id="age" name="age" required>
                            </div>
                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-control" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="tel" class="form-control" id="contact_number" name="contact_number" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Complete Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="button" id="confirmBookingBtn" class="btn btn-primary btn-lg">
                            Confirm Booking
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<script>    
document.addEventListener('DOMContentLoaded', function () {
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const confirmBookingBtn = document.getElementById('confirmBookingBtn');
    const otpSection = document.getElementById('otpSection');
    const bookingSection = document.getElementById('bookingSection');

    sendOtpBtn.addEventListener('click', function () {
        const email = document.getElementById('email').value;

        if (!email) {
            Swal.fire('Error', 'Please enter your email address.', 'error');
            return;
        }

        // Disable the button immediately after clicking
        sendOtpBtn.disabled = true;
        sendOtpBtn.innerText = 'Sending...';

        // Submit form without redirection
        const formData = new FormData();
        formData.append('send_otp', 'true');
        formData.append('email', email);

        fetch('', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Success', data.message, 'success');
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Failed to send OTP. Please try again.', 'error');
            })
            .finally(() => {
                // Re-enable the button after the response
                sendOtpBtn.disabled = false;
                sendOtpBtn.innerText = 'Send OTP';
            });
    });

    verifyOtpBtn.addEventListener('click', function () {
        const email = document.getElementById('email').value;
        const otp = document.getElementById('otp').value;

        if (!email || !otp) {
            Swal.fire('Error', 'Please enter your email and OTP.', 'error');
            return;
        }

        // Submit form without redirection
        const formData = new FormData();
        formData.append('verify_otp', 'true');
        formData.append('email', email);
        formData.append('otp', otp);

        fetch('', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Success', data.message, 'success').then(() => {
                        otpSection.style.display = 'none';
                        bookingSection.style.display = 'block';
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Failed to verify OTP. Please try again.', 'error');
            });
    });

    confirmBookingBtn.addEventListener('click', function () {
    const bookingForm = document.getElementById('bookingForm');

    if (!bookingForm.checkValidity()) {
        Swal.fire('Error', 'Please complete all required fields.', 'error');
        return;
    }
   confirmBookingBtn.disabled = true;
         confirmBookingBtn.innerText = 'Sending...';
    // Add hidden field for booking confirmation
    const confirmInput = document.createElement('input');
    confirmInput.type = 'hidden';
    confirmInput.name = 'confirm_booking';
    confirmInput.value = 'true';
    bookingForm.appendChild(confirmInput);

    // Prepare form data
    const formData = new FormData(bookingForm);

    fetch('', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Booking Confirmed!',
                    text: 'Redirecting to home page...',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'index.php';
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Failed to process the booking. Please try again.', 'error');
        });
});
});

</script>

</body>
</html>