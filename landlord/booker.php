<?php
// Include database connection
include('../connection.php');
require '../vendor_copy/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Start session only if it hasn't started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and has register1_id in session
if (!isset($_SESSION['register1_id'])) {
    header("Location: ../login.php");
    exit();
}

$current_user_id = $_SESSION['register1_id'];
// Function to update confirm_date to the next month's date if 30 days have passed
function updateConfirmDate($bookingId) {
    global $dbconnection;

    // Fetch the current confirm_date for the booking
    $query = "SELECT confirm_date FROM booking WHERE id = ?";
    $stmt = $dbconnection->prepare($query);
    if ($stmt === false) {
        die("MySQL error: " . $dbconnection->error);
    }

    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $confirmDate = $row['confirm_date'];

        // If confirm_date is not null, calculate if it needs to be updated
        if ($confirmDate) {
            $currentDate = new DateTime();
            $confirmDateObj = new DateTime($confirmDate);

            // Calculate the difference in days
            $daysDifference = $confirmDateObj->diff($currentDate)->days;

            // Check if 30 days have passed
            if ($daysDifference >= 30) {
                // Add 30 days to the confirm_date
                $confirmDateObj->modify('+30 days');
                $newConfirmDate = $confirmDateObj->format('Y-m-d');

                // Update the booking table with the new confirm_date
                $updateQuery = "UPDATE booking SET confirm_date = ? WHERE id = ?";
                $updateStmt = $dbconnection->prepare($updateQuery);
                if ($updateStmt === false) {
                    die("MySQL error: " . $dbconnection->error);
                }

                $updateStmt->bind_param("si", $newConfirmDate, $bookingId);
                if (!$updateStmt->execute()) {
                    die("Update Query Error: " . $updateStmt->error);
                }

                return true; // Confirm date updated
            }
        }
    }
    return false; // No update needed
}

// Function to fetch the monthly rental rate for a specific rental
function getMonthlyRateForRental($bhouseId) {
    global $dbconnection;

    $query = "SELECT monthly FROM rental WHERE rental_id = ?";
    $stmt = $dbconnection->prepare($query);

    if ($stmt === false) {
        die("MySQL error: " . $dbconnection->error);
    }

    $stmt->bind_param("i", $bhouseId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['monthly'];
    }
    return 0;
}

 
// Function to calculate balance
function calculateBalance($bookingId, $monthlyRate) {
    global $dbconnection;
    
    // Get the total amount paid and last payment date
    $query = "SELECT p.amount, p.last_date_pay, b.date_posted 
              FROM payment p 
              INNER JOIN booking b ON b.payment_id = p.payment_id 
              WHERE b.id = ?";
    
    $stmt = $dbconnection->prepare($query);
    if ($stmt === false) {
        die("MySQL error: " . $dbconnection->error);
    }
    
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if (!$row) {
        return $monthlyRate; // Return full monthly rate if no payment record found
    }
    
    $totalPaid = $row['amount'];
    $startDate = new DateTime($row['date_posted']);
    $currentDate = new DateTime();
    
    // Calculate months passed
    $monthsPassed = $startDate->diff($currentDate)->m + ($startDate->diff($currentDate)->y * 12) + 1;
    
    // Calculate total amount due
    $totalDue = $monthlyRate * $monthsPassed;
    
    // Calculate remaining balance
    return max(0, $totalDue - $totalPaid);
}

// Update the payment processing section
// Update the payment processing section
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['amount'])) {
    $bookingId = $_POST['id'];
    $paymentAmount = $_POST['amount'];
    
    // Start transaction
    $dbconnection->begin_transaction();
    
    try {
        // Get payment_id for the booking
        $query = "SELECT p.payment_id, p.amount as current_amount 
                 FROM booking b 
                 INNER JOIN payment p ON b.payment_id = p.payment_id 
                 WHERE b.id = ?";
        
        $stmt = $dbconnection->prepare($query);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $dbconnection->error);
        }
        
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $paymentData = $result->fetch_assoc();
        
        if (!$paymentData) {
            throw new Exception("Payment record not found");
        }
        
        // Update payment amount and last payment date
        $updateQuery = "UPDATE payment 
                       SET amount = amount + ?, 
                           last_date_pay = CURRENT_TIMESTAMP 
                       WHERE payment_id = ?";
        
        $stmt = $dbconnection->prepare($updateQuery);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $dbconnection->error);
        }
        
        $stmt->bind_param("di", $paymentAmount, $paymentData['payment_id']);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        // Insert a record into the paid table
        $insertPaidQuery = "INSERT INTO paid (payment_id, amount, last_date_pay) 
                            VALUES (?, ?, CURRENT_TIMESTAMP)";
        
        $stmt = $dbconnection->prepare($insertPaidQuery);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $dbconnection->error);
        }
        
        $stmt->bind_param("id", $paymentData['payment_id'], $paymentAmount);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        // Commit transaction
        $dbconnection->commit();
        $_SESSION['success_message'] = "Payment updated and recorded successfully";
        
    } catch (Exception $e) {
        $dbconnection->rollback();
        $_SESSION['error_message'] = "Error processing payment: " . $e->getMessage();
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
} elseif (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Verify that this booking belongs to the current user's rental
    $verifyQuery = "SELECT b.id 
                   FROM booking b 
                   INNER JOIN payment p ON b.payment_id = p.payment_id 
                INNER  JOIN rental r ON p.rental_id = r.rental_id 
                WHERE b.id = ? AND r.register1_id = ?"; 
    $verifyStmt = $dbconnection->prepare($verifyQuery);
    $verifyStmt->bind_param("is", $delete_id, $current_user_id);
    $verifyStmt->execute();
    $verifyResult = $verifyStmt->get_result();

    if ($verifyResult->num_rows > 0) {
        // Delete the booking record
        $deleteQuery = "DELETE FROM booking WHERE id = ?";
        $stmt = $dbconnection->prepare($deleteQuery);
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email_booking_id'])) {
    $bookingId = $_POST['email_booking_id'];
$query = "
    SELECT b.book_ref_no, b.firstname, b.lastname, b.email, b.confirm_date, 
           p.amount, p.last_date_pay, r.monthly
    FROM booking b
    INNER JOIN payment p ON b.payment_id = p.payment_id
    INNER JOIN rental r ON p.rental_id = r.rental_id
    WHERE b.id = ?
";
$stmt = $dbconnection->prepare($query);
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$result = $stmt->get_result();
$bookingData = $result->fetch_assoc();


    if ($bookingData) {
    $confirmDate = new DateTime($bookingData['confirm_date']);
    $currentDate = new DateTime();
    $daysLeft = max(0, 30 - $confirmDate->diff($currentDate)->days);
    $balance = max(0, $bookingData['monthly'] - $bookingData['amount']); // Calculate balance
    $email = $bookingData['email'];
        // Create email content
        $emailSubject = "Booking Details: " . $bookingData['book_ref_no'];
        $emailBody = "
            Hello {$bookingData['firstname']} {$bookingData['lastname']},
            
            Here are your booking details:
            - Booking Reference: {$bookingData['book_ref_no']}
           - Balance: " . number_format($balance, 2) . "
            - Last Payment Date: {$bookingData['last_date_pay']}
            - Days Left in 30-Day Period: $daysLeft
            
            Please ensure your payments are up to date.

            Thank you.
        ";

        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
          $mail->Username = 'madridejosbh2@gmail.com';
        $mail->Password = 'ougf gwaw ezwh jmng';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('madridejosbh2@gmail.com', 'Landlord');
            $mail->addAddress($email);

            $mail->Subject = $emailSubject;
            $mail->Body = $emailBody;

            $mail->send();
            $_SESSION['success_message'] = "Email sent successfully to $email.";
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Email sending failed: " . $mail->ErrorInfo;
        }
    } else {
        $_SESSION['error_message'] = "Booking not found.";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
// Include the header
include('header.php');

// Define the number of records per page
$results_per_page = 5;

// Determine the current page number
$pageno = isset($_GET['pageno']) ? (int)$_GET['pageno'] : 1;

// Calculate the offset for the SQL query
$offset = ($pageno - 1) * $results_per_page;

// Get the total number of records for confirmed bookings
$total_pages_sql = "
    SELECT COUNT(*) 
    FROM booking b
    INNER JOIN payment p ON b.payment_id = p.payment_id 
    INNER JOIN rental r ON p.rental_id = r.rental_id 
    WHERE r.register1_id = ? 
    AND b.status != 'Confirm'
    AND p.rental_id = ?";

$stmt_count = $dbconnection->prepare($total_pages_sql);

// Bind two parameters: $current_user_id and $rental_id
$stmt_count->bind_param("ss", $current_user_id, $rental_id);

// Execute the statement
$stmt_count->execute();

// Fetch the result
$total_rows = $stmt_count->get_result()->fetch_array()[0];

// Calculate total pages
$total_pages = ceil($total_rows / $results_per_page);

// Fetch the records for the current page
 

$query = "
    SELECT b.id, b.book_ref_no, b.firstname, b.middlename, b.lastname, 
           b.email, b.age, b.gender, b.contact_number, b.Address, 
           p.gcash_picture, b.status, p.amount, p.gcash_reference,
           p.rental_id, p.created_at
    FROM booking b
    INNER JOIN payment p ON b.payment_id = p.payment_id
    INNER JOIN rental r ON p.rental_id = r.rental_id
    WHERE r.register1_id = ? 
      AND b.status != 'Confirm'
    ORDER BY p.created_at DESC 
    LIMIT ?, ?";

$stmt = $dbconnection->prepare($query);

// Check if the statement preparation failed
if ($stmt === false) {
    die("Error preparing statement: " . $dbconnection->error);
}

// Bind parameters and execute
$stmt->bind_param("sii", $current_user_id, $offset, $results_per_page);
$stmt->execute();
$result = $stmt->get_result();

?>

<style>
       
/* Table styles */
.table {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    font-size: 14px;
}

.table thead th {
    background: #007bff;
    color: #fff;
    text-align: center;
}

.table tbody td {
    vertical-align: middle;
    text-align: center;
    padding: 10px;
}
/* Main layout container */
.dashboard-container {
    display: flex;
    min-height: 100vh;
    width: 100%;
}

/* Sidebar styles */
.sidebar-container {
    width: 250px;
    background: #fff;
    border-right: 1px solid #e3e6f0;
    flex-shrink: 0;
}

/* Main content area */
.main-content {
    flex-grow: 1;
    padding: 20px;
    background: #f8f9fc;
    overflow-x: hidden;
}

/* Table container */
.table-container {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,.15);
    margin-bottom: 20px;
    overflow: hidden;
}

/* Table styles */
.table-responsive {
    margin: 0;
    padding: 0;
    width: 100%;
}

.table {
    margin-bottom: 0;
    width: 100%;
}

.table th {
    background: #f8f9fc;
    font-weight: 600;
    padding: 12px 15px;
    white-space: nowrap;
}

.table td {
    padding: 12px 15px;
    vertical-align: middle;
}

/* Button styles */
.action-buttons {
    display: flex;
    gap: 5px;
}

.btn {
    padding: 6px 12px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
}

.btn i {
    font-size: 14px;
}

 

/* Header styles */
h3 {
    margin: 0 0 20px 0;
    color: #5a5c69;
    font-weight: 500;
    font-size: 1.75rem;
}

/* Responsive styles */
@media (max-width: 768px) {
    .dashboard-container {
        flex-direction: column;
    }
    
    .sidebar-container {
        width: 100%;
        position: static;
        height: auto;
    }
    
    .main-content {
        padding: 15px;
    }

    .table th, .table td {
        padding: 8px;
        font-size: 14px;
    }

    .btn {
        padding: 4px 8px;
        min-width: 30px;
    }

     

    h3 {
        font-size: 1.5rem;
        margin-bottom: 15px;
    }

    .action-buttons {
        flex-direction: column;
        gap: 3px;
    }
}
    /* General table styles */
    .table {
        width: 100%;
        table-layout: auto;
    }

    /* Make sure table scrolls horizontally on smaller screens */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch; /* For smooth scrolling on iOS */
    }

    /* Card layout for mobile */
    .card {
        border: 1px solid #ddd;
        margin-bottom: 20px;
        padding: 10px;
        border-radius: 5px;
    }

    /* For screens larger than 700px (tablets, desktops) */
    @media screen and (min-width: 700px) {
        th, td {
            font-size: 14px;
            padding: 8px;
        }
    }

    /* For small screens (700px or less) */
    @media screen and (max-width: 700px) {
        .table {
            display: none; /* Hide the table */
        }

        /* Ensure content wraps inside cards */
        .card th, .card td {
            white-space: normal; /* Allow text wrapping */
            word-wrap: break-word;
        }

        /* Style for headers in card layout */
        .card-header {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 16px; /* Increased size for better visibility */
        }

        .card p {
            font-size: 14px; /* Increased size for better visibility */
            margin: 5px 0; /* Added margin for spacing */
        }
    }

    /* For very small screens (400px or less) */
    @media screen and (max-width: 400px) {
        .card {
            font-size: 10px; /* Even smaller font */
        }
    }

    @media screen and (max-width: 700px) {
        .sidebar a {
            float: revert-layer !important;  
        }
    }
    @media (min-width: 576px) {
    .col-sm-9 {
        -ms-flex: 0 0 75%;
        flex: 0 0 75%;
        max-width: 100% !important;
    }
}
 .btn-danger {
    margin-right: 15px !important;
}
.btn-info {
    margin-left: 10px !important;
}
h3{
    margin-left: 15px;
}

/* Ensure the search form is aligned to the right */
.search-form-container {
    display: flex;
    justify-content: flex-end; /* Push to the right */
    margin-bottom: 20px; /* Add spacing below */
}

/* Add styling for the search input and button */
.search-form-container input[type="text"] {
    width: 300px; /* Adjust width */
    border-radius: 5px;
    border: 1px solid #ddd;
    padding: 8px 12px;
    margin-right: 10px;
    font-size: 14px;
}

.search-form-container button {
    padding: 8px 15px;
    font-size: 14px;
    border: none;
    border-radius: 5px;
    background-color: #007bff;
    color: #fff;
    cursor: pointer;
}

.search-form-container button:hover {
    background-color: #0056b3;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .search-form-container {
        flex-direction: column; /* Stack vertically on smaller screens */
        align-items: flex-start;
    }

    .search-form-container input[type="text"] {
        margin-right: 0; /* Reset margin for stacking */
        margin-bottom: 10px;
        width: 100%; /* Use full width */
    }

    .search-form-container button {
        width: 100%; /* Use full width */
    }
}
</style>
<div class="dashboard-container">
    <div class="sidebar-container">
        <?php include('sidebar.php'); ?>
    </div>

    <div class="main-content"> <br><br>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Book Information</h3>
        </div>

        <?php
        $soaDetails = [];
        $soaBookRef = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['soa_book_ref'])) {
            $soaBookRef = $_POST['soa_book_ref'];

            $query = "
                SELECT p.payment_id 
                FROM booking b
                INNER JOIN payment p ON b.payment_id = p.payment_id
                WHERE b.book_ref_no = ?";
            $stmt = $dbconnection->prepare($query);
            $stmt->bind_param('s', $soaBookRef);
            $stmt->execute();
            $result = $stmt->get_result();
            $booking = $result->fetch_assoc();

            if ($booking) {
                $paymentId = $booking['payment_id'];

                $paidQuery = "SELECT amount, last_date_pay FROM paid WHERE payment_id = ?";
                $paidStmt = $dbconnection->prepare($paidQuery);
                $paidStmt->bind_param('i', $paymentId);
                $paidStmt->execute();
                $paidResult = $paidStmt->get_result();

                while ($row = $paidResult->fetch_assoc()) {
                    $soaDetails[] = $row;
                }
            } else {
                $soaDetails = null;
            }
        }
        ?>

        <!-- Display SOA -->
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['soa_book_ref'])): ?>
            <div class="mt-4">
                <h4>Statement of Account for Booking Reference: <?php echo htmlspecialchars($soaBookRef); ?></h4>
                <?php if ($soaDetails): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($soaDetails as $detail): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(number_format($detail['amount'], 2)); ?></td>
                                    <td><?php echo htmlspecialchars($detail['last_date_pay']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No payments found for this booking reference.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <?php
                // Get unique rental IDs
                $unique_rental_ids = array();
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($rental_row = mysqli_fetch_assoc($result)) {
                        // Only add rental ID if it's not already in the array
                        if (!in_array($rental_row['rental_id'], $unique_rental_ids)) {
                            $unique_rental_ids[] = $rental_row['rental_id'];
                            echo '<a href="add_walkin.php?rental_id=' . $rental_row['rental_id'] . '" class="btn btn-success me-2">Add Walk-In</a>';
                        }
                    }
                    // Reset the result pointer for the main data display
                    mysqli_data_seek($result, 0);
                }
                ?>
            </div>
            <form method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="d-flex">
                <input type="text" name="search_query" class="form-control me-2" placeholder="Search by firstname" value="<?php echo isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : ''; ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        <!-- Responsive Table -->
        <div class="table-responsive d-none d-md-block">  
              
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Book Ref</th>
                        <th>Firstname</th>
                        <th>Middlename</th>
                        <th>Lastname</th>
                        <th>Email</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Contact Number</th>
                        <th>Address</th>
                        <th>Date Posted</th>
                        <th>Start Date</th>
                        <th>Due Date</th>
                        <th>Balance</th>
                        <th>Paid Amount</th>
                        <th>Actions</th>
                        <th>SOA</th>
                    </tr>
                </thead>
                <tbody>
                  <?php
                  // Handle search query
                  $search_query = isset($_GET['search_query']) ? '%' . $_GET['search_query'] . '%' : null;

                 $query = "
    SELECT b.id, b.book_ref_no, b.firstname, b.middlename, b.lastname, b.email, 
           b.age, b.gender, b.contact_number, b.Address, 
           b.date_posted, b.confirm_date, p.amount AS amount, p.rental_id, p.last_date_pay,
           DATE_ADD(b.confirm_date, INTERVAL 30 DAY) AS due_date
    FROM booking b
    INNER JOIN payment p ON b.payment_id = p.payment_id
    WHERE b.status = 'Confirm' 
    AND p.rental_id IN (
        SELECT r.rental_id 
        FROM rental r 
        WHERE r.register1_id = ?
    )
    " . ($search_query ? "AND b.firstname LIKE ?" : "") . "
    LIMIT ?, ?";


                  // Prepare the query
                  $stmt = $dbconnection->prepare($query);

                  if ($stmt === false) {
                      die("Error preparing statement: " . $dbconnection->error);
                  }

                  // Bind parameters
                  if ($search_query) {
                      $stmt->bind_param("ssii", $current_user_id, $search_query, $offset, $results_per_page);
                  } else {
                      $stmt->bind_param("sii", $current_user_id, $offset, $results_per_page);
                  }

                  // Execute the query
                  if (!$stmt->execute()) {
                      die("Execution failed: " . $stmt->error);
                  }

                  // Fetch the results
                  $result = $stmt->get_result();

                  // Reset URL if search_query is present
                  if (isset($_GET['search_query'])) {
                      echo '<script>
                          window.history.replaceState({}, document.title, "' . htmlspecialchars($_SERVER['PHP_SELF']) . '");
                      </script>';
                  }

                  // Loop through the results
                  while ($row = $result->fetch_assoc()) {
                      $monthly_rental = getMonthlyRateForRental($row['rental_id']); 
                      $balance = calculateBalance($row['id'], $monthly_rental, $row['amount']); 
                  ?>
                    <tr>
                         <td><?php echo htmlspecialchars($row['book_ref_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($row['middlename']); ?></td>
                        <td><?php echo htmlspecialchars($row['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['age']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['Address']); ?></td>
                        <td><?php echo date('F d, Y', strtotime($row['date_posted'])); ?></td>
                            <td><?php echo $row['confirm_date'] ? date('F d, Y', strtotime($row['confirm_date'])) : 'N/A'; ?></td>
            <td><?php echo $row['due_date'] ? date('F d, Y', strtotime($row['due_date'])) : 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars(number_format($balance, 2)); ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="number" name="amount" min="0" step="0.01" placeholder="Enter Amount" required>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                            
                        </td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                            <form method="post" action="">
                            <input type="hidden" name="email_booking_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn btn-secondary">Send Email</button>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                            <input type="hidden" name="soa_book_ref" value="<?php echo htmlspecialchars($row['book_ref_no']); ?>">
                            <button type="submit" class="btn btn-info">View SOA</button>
                            </form>
                        </td>

                    </tr>
                  <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Card layout for mobile view -->
        <div class="d-md-none">
            <?php
            mysqli_data_seek($result, 0); // Reset result pointer for mobile view
            while ($row = $result->fetch_assoc()) {
                $monthly_rental = getMonthlyRateForRental($row['rental_id']);
                $balance = calculateBalance($row['id'], $monthly_rental, $row['amount']);
            ?>
            <div class="card">
                <div class="card-header">
                    <h5><?php echo htmlspecialchars($row['firstname']) . ' ' . htmlspecialchars($row['lastname']); ?></h5>
                </div>
                <div class="card-body">
                    <p><strong>Book Ref:</strong> <?php echo htmlspecialchars($row['book_ref_no']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                    <p><strong>Age:</strong> <?php echo htmlspecialchars($row['age']); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($row['gender']); ?></p>
                    <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($row['contact_number']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($row['Address']); ?></p>
                    <p><strong>Date Posted:</strong> <?php echo date('F d, Y', strtotime($row['date_posted'])); ?></p>
                     <p><strong>Start Date:</strong> <?php echo $row['confirm_date'] ? date('F d, Y', strtotime($row['confirm_date'])) : 'N/A'; ?></p>
             <p><strong>Due Date:</strong><?php echo $row['due_date'] ? date('F d, Y', strtotime($row['due_date'])) : 'N/A'; ?></p>
                    <p><strong>Balance:</strong> <?php echo htmlspecialchars(number_format($balance, 2)); ?></p>
                       <td>
                            <form method="post" action="">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="number" name="amount" min="0" step="0.01" placeholder="Enter Amount" required>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                            
                        </td>
                         <td>
                            <form method="post" action="">
                                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                            <form method="post" action="">
        <input type="hidden" name="email_booking_id" value="<?php echo $row['id']; ?>">
        <button type="submit" class="btn btn-secondary">Send Email</button>
    </form>
                        </td>
                </div>
            </div>
            <?php } ?>
        </div>
   

<br>
        <div class="pagination">
            <a href="?pageno=1" class="btn btn-info">First</a>
            <a href="?pageno=<?php echo max(1, $pageno - 1); ?>" class="btn btn-info">Previous</a>
            <span>Page <?php echo $pageno; ?> of <?php echo $total_pages; ?></span>
            <a href="?pageno=<?php echo min($total_pages, $pageno + 1); ?>" class="btn btn-info">Next</a>
            <a href="?pageno=<?php echo $total_pages; ?>" class="btn btn-info">Last</a>
        </div>
    </div>
</div></div> </div>

<?php include('footer.php'); ?>

<?php
// Close the prepared statement and database connection
$stmt->close();
$dbconnection->close();
?>
