<?php
include('connection.php');
include('encryption_helper.php');
require 'vendor_copy/autoload.php';

session_start();

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

// Validate rental_id from GET parameter
$rental_id = isset($_GET['rental_id']) ? sanitizeInput($_GET['rental_id'], 'int') : null;
if (!$rental_id) {
    die('<script>Swal.fire("Error", "Rental ID is required.", "error");</script>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form inputs
    $gcash_reference = sanitizeInput($_POST['gcash_reference'], 'string');
    $amount = filter_var($_POST['amount'], FILTER_VALIDATE_FLOAT);
    $gcash_picture = $_FILES['gcash_picture'];

    if (!$gcash_reference || !$amount || !$gcash_picture['name']) {
        echo '<script>Swal.fire("Error", "All fields are required.", "error");</script>';
        exit();
    }

    // Fetch downpayment and installment amounts
    $sql_fetch_rental = "SELECT downpayment_amount, installment_amount FROM rental WHERE rental_id = ?";
    $stmt_fetch = $dbconnection->prepare($sql_fetch_rental);
    if (!$stmt_fetch) {
        die('<script>Swal.fire("Error", "Failed to fetch rental data: ' . $dbconnection->error . '", "error");</script>');
    }
    $stmt_fetch->bind_param('i', $rental_id);
    $stmt_fetch->execute();
    $rental_data = $stmt_fetch->get_result()->fetch_assoc();

    if (!$rental_data) {
        echo '<script>Swal.fire("Error", "Invalid rental ID.", "error");</script>';
        exit();
    }

    // Check minimum payment amount
    $min_payment = max($rental_data['downpayment_amount'] ?? 0, $rental_data['installment_amount'] ?? 0);
    if ($amount < $min_payment) {
        echo '<script>Swal.fire("Error", "Payment amount must be at least ₱' . number_format($min_payment, 2) . '.", "error");</script>';
        exit();
    }

    // File upload handling
    $target_dir = "uploads/gcash_pictures/";
    $file_extension = strtolower(pathinfo($gcash_picture["name"], PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png'];

    if (!in_array($file_extension, $allowed_types)) {
        echo '<script>Swal.fire("Error", "Invalid file type. Only JPG and PNG allowed.", "error");</script>';
        exit();
    }

    $new_filename = bin2hex(random_bytes(16)) . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    if (!move_uploaded_file($gcash_picture["tmp_name"], $target_file)) {
        echo '<script>Swal.fire("Error", "Failed to upload GCash proof.", "error");</script>';
        exit();
    }

    // Start transaction
    $dbconnection->begin_transaction();
    try {
        // Insert into payment table
        $sql_payment = "INSERT INTO payment (rental_id, gcash_reference, amount, gcash_picture, created_at) 
                        VALUES (?, ?, ?, ?, NOW())";
        $stmt = $dbconnection->prepare($sql_payment);
        if (!$stmt) {
            throw new Exception("Failed to prepare payment insertion: " . $dbconnection->error);
        }
        $stmt->bind_param('isds', $rental_id, $gcash_reference, $amount, $target_file);
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute payment insertion: " . $stmt->error);
        }

        // Get the last inserted payment ID
        $payment_id = $dbconnection->insert_id;

        // Insert into paid table
        $sql_paid = "INSERT INTO paid (payment_id, amount, last_date_pay) VALUES (?, ?, NOW())";
        $stmt_paid = $dbconnection->prepare($sql_paid);
        if (!$stmt_paid) {
            throw new Exception("Failed to prepare paid insertion: " . $dbconnection->error);
        }
        $stmt_paid->bind_param('id', $payment_id, $amount);
        if (!$stmt_paid->execute()) {
            throw new Exception("Failed to execute paid insertion: " . $stmt_paid->error);
        }

        // Commit transaction
        $dbconnection->commit();
        header("Location: booking.php?payment_id=" . $payment_id);
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $dbconnection->rollback();
        error_log("Payment processing error: " . $e->getMessage());
        echo '<script>Swal.fire("Error", "Failed to process payment. Please try again.", "error");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@2.1.1/dist/tesseract.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --primary-color: #4F46E5;
            --secondary-color: #818CF8;
            --background-color: #F3F4F6;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .payment-container {
            max-width: 600px;
            background: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .page-title {
            color: #111827;
            font-weight: 600;
            font-size: 1.875rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-control:disabled, .form-control[readonly] {
            background-color: #F9FAFB;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-1px);
        }

        .btn-primary:disabled {
            background-color: #9CA3AF;
            cursor: not-allowed;
        }

        .file-upload {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
            border: 2px dashed #E5E7EB;
            border-radius: 8px;
            background-color: #F9FAFB;
            transition: all 0.3s ease;
        }

        .file-upload:hover {
            border-color: var(--primary-color);
        }

        #gcash_picture {
            opacity: 0;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .upload-text {
            color: #6B7280;
            margin-top: 1rem;
            text-align: center;
        }
    </style>
</head>
<body class="py-5">
    <div class="container">
        <div class="payment-container mx-auto">
            <h2 class="page-title">Complete Your Payment</h2>
            <form id="paymentForm" method="POST" action="payment.php?rental_id=<?php echo htmlspecialchars($rental_id); ?>" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="gcash_picture" class="form-label">GCash Payment Proof</label>
                    <div class="file-upload">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <p class="upload-text">Drop your payment screenshot here or click to browse</p>
                        <input type="file" class="form-control" id="gcash_picture" name="gcash_picture" accept=".jpg,.jpeg,.png" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="gcash_reference" class="form-label">GCash Reference Number</label>
                    <input type="text" class="form-control" id="gcash_reference" name="gcash_reference" readonly required>
                </div>

                <div class="mb-4">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" class="form-control" id="amount" name="amount" readonly required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary" id="submitButton" disabled>Complete Payment</button>
                </div>
            </form>
        </div>
    </div>

<script>
    $(document).ready(function () {
    const gcashPictureInput = $('#gcash_picture');
    const gcashReferenceInput = $('#gcash_reference');
    const amountInput = $('#amount');
    const submitButton = $('#submitButton');
    const paymentForm = $('#paymentForm');

    let minPayment = 0; // To be fetched dynamically from the server

    // Fetch the minimum payment (downpayment/installment) for the rental_id
    $.ajax({
        url: 'fetch_min_payment.php', // Create a script to return min payment
        type: 'GET',
        data: { rental_id: '<?php echo $rental_id; ?>' },
        success: function (response) {
            minPayment = parseFloat(response.min_payment || 0);
        },
        error: function () {
            Swal.fire("Error", "Failed to fetch minimum payment amount.", "error");
        }
    });

    gcashPictureInput.change(function (event) {
        const file = event.target.files[0];
        const maxFileSize = 5 * 1024 * 1024; // 5MB

        // Clear previous values
        gcashReferenceInput.val('');
        amountInput.val('');
        submitButton.prop('disabled', true);

        if (!file) return;

        if (file.size > maxFileSize) {
            Swal.fire("Error", "File size must be less than 5MB.", "error");
            clearFields(); // Clear all fields on error
            return;
        }

        if (file.type === 'image/jpeg' || file.type === 'image/png') {
            const reader = new FileReader();
            reader.onload = function (e) {
                // Show loading indicator
                Swal.fire({
                    title: 'Scanning Receipt',
                    text: 'Please wait while we process the image...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Perform OCR
                Tesseract.recognize(e.target.result, 'eng', {
                    logger: info => console.log(info)
                }).then(({ data: { text } }) => {
                    console.log("Extracted Text:", text);
                    Swal.close();

                    // Extract Reference Number
                    const refPattern = /Ref\.?\s*No\.?\s*([\d\s]+)/i;
                    const refMatch = text.match(refPattern);
                    if (refMatch) {
                        gcashReferenceInput.val(refMatch[1].replace(/\s/g, ''));
                    } else {
                        Swal.fire("Warning", "Reference number not found in the image.", "warning");
                        clearFields(); // Clear all fields on error
                        return;
                    }

                    // Extract Amount
                    const amountPattern = /(\d{1,3}(?:,\d{3})*(?:\.\d{2}))/;
                    const amountMatch = text.match(amountPattern);
                    if (amountMatch) {
                        const scannedAmount = parseFloat(amountMatch[0].replace(/,/g, ''));
                        amountInput.val(scannedAmount);

                        if (scannedAmount < minPayment) {
                            Swal.fire("Error", `Amount must be at least ₱${minPayment.toFixed(2)}.`, "error");
                            clearFields(); // Clear all fields on error
                        } else {
                            submitButton.prop('disabled', false);
                        }
                    } else {
                        Swal.fire("Warning", "Amount not found in the image.", "warning");
                        clearFields(); // Clear all fields on error
                    }
                }).catch(err => {
                    console.error("OCR Error:", err);
                    Swal.fire("Error", "Failed to scan the image. Please try again.", "error");
                    clearFields(); // Clear all fields on error
                });
            };
            reader.readAsDataURL(file);
        } else {
            Swal.fire("Error", "Only JPG, JPEG, and PNG files are allowed.", "error");
            clearFields(); // Clear all fields on error
        }
    });

    // Clear all fields
    function clearFields() {
        paymentForm.trigger("reset");
        submitButton.prop('disabled', true);
    }
});

</script>
</body>
</html>