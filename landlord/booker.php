<?php
// Include database connection
include('../connection.php');

// Function to fetch the monthly rental rate for a specific rental
function getMonthlyRateForRental($bhouseId) {
    global $dbconnection;

    $query = "SELECT monthly FROM rental WHERE rental_id = ?";
    $stmt = $dbconnection->prepare($query);
    $stmt->bind_param("i", $bhouseId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['monthly'];
    }
    return 0; // Default to 0 if no rental found
}

// Function to calculate the balance and check for overdue payments
function calculateBalance($id, $monthlyRate, $paidAmount) {
    global $dbconnection;

    // Get the last payment date and current balance
    $query = "SELECT last_payment_date FROM book WHERE id = ?";
    $stmt = $dbconnection->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $lastPaymentDate = $row['last_payment_date'];
        $currentDate = date('Y-m-d');

        // Check if 30 days have passed since the last payment
        $dateDifference = (strtotime($currentDate) - strtotime($lastPaymentDate)) / (60 * 60 * 24);

        if ($dateDifference >= 30) {
            // If balance is not zero, add the monthly rent to the remaining balance
            if ($paidAmount < $monthlyRate) {
                $balance = $monthlyRate - $paidAmount;
                // Add this month's rent to the outstanding balance
                return $balance + $monthlyRate;
            } else {
                // If balance is zero, reset the balance to the new month's rental rate
                return $monthlyRate;
            }
        } else {
            // If 30 days have not passed, return the current balance
            return $monthlyRate - $paidAmount;
        }
    }

    return $monthlyRate - $paidAmount; // Default calculation if no payment record is found
}

// Check for payment or delete submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id']) && isset($_POST['paid_amount'])) {
        $id = $_POST['id'];
        $paidAmount = $_POST['paid_amount'];

        // Update the paid amount in the book table
        $updateQuery = "UPDATE book SET paid_amount = paid_amount + ?, last_payment_date = CURRENT_DATE WHERE id = ?";
        $stmt = $dbconnection->prepare($updateQuery);
        $stmt->bind_param("di", $paidAmount, $id);
        $stmt->execute();

        // Redirect back to the same page to reflect changes
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['delete_id'])) {
        // Handle deletion of a record
        $delete_id = $_POST['delete_id'];

        // Delete the record from the database
        $deleteQuery = "DELETE FROM book WHERE id = ?";
        $stmt = $dbconnection->prepare($deleteQuery);
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();

        // Redirect after deletion
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Include the header
include('header.php');

// Define the number of records per page
$results_per_page = 8;

// Determine the current page number
$pageno = isset($_GET['pageno']) ? (int)$_GET['pageno'] : 1;

// Calculate the offset for the SQL query
$offset = ($pageno - 1) * $results_per_page;

// Get the total number of records with status 'confirm'
$total_pages_sql = "SELECT COUNT(*) FROM book WHERE status = 'Confirm'";
$result_pages = mysqli_query($dbconnection, $total_pages_sql);
$total_rows = mysqli_fetch_array($result_pages)[0];
$total_pages = ceil($total_rows / $results_per_page);

// Fetch the records for the current page with status 'confirm'
$query = "SELECT id, firstname, middlename, lastname, email, age, gender, contact_number, Address, date_posted, paid_amount, bhouse_id 
          FROM book 
          WHERE status = 'Confirm' 
          LIMIT ?, ?";
$stmt = $dbconnection->prepare($query);
$stmt->bind_param("ii", $offset, $results_per_page);
$stmt->execute();
$result = $stmt->get_result();

?>
<style>
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
</style>

<div class="row">
    <div class="col-sm-2">
        <?php include('sidebar.php'); ?>
    </div>
    <br><br><br>
    <div class="col-sm-9">
        <h3>Book Information</h3>
        <br />

        <!-- Responsive Table -->
        <div class="table-responsive d-none d-md-block"> <!-- Hide on small screens -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Firstname</th>
                        <th>Middlename</th>
                        <th>Lastname</th>
                        <th>Email</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Contact Number</th>
                        <th>Address</th>
                        <th>Date Started</th>
                        <th>Balance</th>
                        <th>Paid Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        $monthly_rental = getMonthlyRateForRental($row['bhouse_id']); 
                        $balance = calculateBalance($row['id'], $monthly_rental, $row['paid_amount']); 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($row['middlename']); ?></td>
                        <td><?php echo htmlspecialchars($row['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['age']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['Address']); ?></td>
                        <td><?php echo date('F d, Y', strtotime($row['date_posted'])); ?></td>
                        <td><?php echo htmlspecialchars(number_format($balance, 2)); ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="number" name="paid_amount" min="0" step="0.01" placeholder="Enter Amount" required>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
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
    while ($row = mysqli_fetch_assoc($result)) {
        $monthly_rental = getMonthlyRateForRental($row['bhouse_id']);
        $balance = calculateBalance($row['id'], $monthly_rental, $row['paid_amount']);
    ?>
    <div class="card">
        <div class="card-header">
            <h5><?php echo htmlspecialchars($row['firstname']) . ' ' . htmlspecialchars($row['lastname']); ?></h5>
        </div>
        <div class="card-body">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($row['age']); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($row['gender']); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($row['contact_number']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($row['Address']); ?></p>
            <p><strong>Date Started:</strong> <?php echo date('F d, Y', strtotime($row['date_posted'])); ?></p>
            <p><strong>Balance:</strong> <?php echo htmlspecialchars(number_format($balance, 2)); ?></p>

            <!-- Form to submit paid amount and delete record, side by side -->
            <div class="d-flex justify-content-between mt-2">
                <form method="post" action="">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="number" name="paid_amount" min="0" step="0.01" placeholder="Enter Amount" required>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                <form method="post" action="">
                    <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

        <div class="pagination">
            <a href="?pageno=1" class="btn btn-info">First</a>
            <a href="?pageno=<?php echo max(1, $pageno - 1); ?>" class="btn btn-info">Previous</a>
            <span>Page <?php echo $pageno; ?> of <?php echo $total_pages; ?></span>
            <a href="?pageno=<?php echo min($total_pages, $pageno + 1); ?>" class="btn btn-info">Next</a>
            <a href="?pageno=<?php echo $total_pages; ?>" class="btn btn-info">Last</a>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<?php
// Close the prepared statement and database connection
$stmt->close();
$dbconnection->close();
?>
