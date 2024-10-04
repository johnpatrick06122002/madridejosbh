<?php
// Include database connection
include('../connection.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer classes
require '../vendor/autoload.php'; // Adjust the path according to your structure

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

// Function to send an email notification
function sendEmail($recipient, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.example.com';                   // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'lucklucky2100@gmail.com';             // SMTP username
        $mail->Password   = 'kjxf ptjv erqn yygv';                // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      // Enable TLS encryption
        $mail->Port       = 587;                                   // TCP port to connect to

        // Recipients
        $mail->setFrom('lucklucky2100@gmail.com', 'Your Name');
        $mail->addAddress($recipient);                             // Add a recipient

        // Content
        $mail->isHTML(true);                                      // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Check for status update submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_status']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $newStatus = $_POST['new_status'];

    // Update the status in the book table
    $updateQuery = "UPDATE book SET status = ? WHERE id = ?";
    $stmt = $dbconnection->prepare($updateQuery);
    $stmt->bind_param("si", $newStatus, $id);
    $stmt->execute();

    // Fetch email address for notification
    $emailQuery = "SELECT email FROM book WHERE id = ?";
    $stmtEmail = $dbconnection->prepare($emailQuery);
    $stmtEmail->bind_param("i", $id);
    $stmtEmail->execute();
    $resultEmail = $stmtEmail->get_result();
    $emailRow = $resultEmail->fetch_assoc();
    
    if ($emailRow) {
        $recipientEmail = $emailRow['email'];
        $subject = "Status Update Notification";
        $body = "Your booking status has been updated to: $newStatus";
        
        // Send email
        sendEmail($recipientEmail, $subject, $body);
    }

    // Redirect back to the same page to reflect changes
    header("Location: " . $_SERVER['PHP_SELF'] . "?removed_id=" . $id); // Pass the updated id
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

// Get the total number of records excluding the removed row and only those not active
$total_pages_sql = "SELECT COUNT(*) FROM book WHERE status != 'Confirm'";
$result_pages = mysqli_query($dbconnection, $total_pages_sql);
$total_rows = mysqli_fetch_array($result_pages)[0];
$total_pages = ceil($total_rows / $results_per_page);

// Fetch the records for the current page, excluding the removed row if present and only non-active statuses
$query = "SELECT id, firstname, middlename, lastname, email, age, gender, contact_number, Address, status 
          FROM book 
          WHERE id != ? AND status != 'Confirm' 
          LIMIT ?, ?";
$stmt = $dbconnection->prepare($query);
$removed_id = isset($_GET['removed_id']) ? (int)$_GET['removed_id'] : 0; // Get the removed id from the query parameter
$stmt->bind_param("iii", $removed_id, $offset, $results_per_page); // Bind the parameters
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
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $monthly_rental = getMonthlyRateForRental($row['id']); 
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
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <select name="new_status">
                                <option value="Confirm">Confirm</option> <!-- Updated option -->
                                <option value="inactive">Reject</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Send</button>
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
