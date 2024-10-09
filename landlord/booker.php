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

// Check for payment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
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
$total_pages_sql = "SELECT COUNT(*) FROM book WHERE status = 'confirm'";
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

<div class="row">
    <div class="col-sm-2">
        <?php include('sidebar.php'); ?>
    </div>

    <div class="col-sm-9">
        <h3>Book Information</h3>
        <br />

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
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <ul class="pagination">
            <li><a href="?pageno=1"><i class="fa fa-fast-backward"></i> First</a></li>
            <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
                <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>"><i class="fa fa-chevron-left"></i> Prev</a>
            </li>
            <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
                <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>">Next <i class="fa fa-chevron-right"></i></a>
            </li>
            <li><a href="?pageno=<?php echo $total_pages; ?>">Last <i class="fa fa-fast-forward"></i></a></li>
        </ul>
    </div>
</div>

<?php include('footer.php'); ?>

<?php
// Close the prepared statement and database connection
$stmt->close();
$dbconnection->close();
?>
