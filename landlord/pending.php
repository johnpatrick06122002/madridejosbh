<?php
// Include database connection
include('../connection.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer classes
require '../vendor_copy/autoload.php'; // Adjust the path according to your structure

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
// Function to send an email notification to multiple recipients
function sendEmail($recipients, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'lucklucky2100@gmail.com';
        $mail->Password   = 'kjxf ptjv erqn yygv'; // Ensure proper security practices
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('lucklucky2100@gmail.com', 'Your Name');

        // Add each recipient email address from the provided array
        foreach ($recipients as $email) {
            $mail->addAddress($email); // Add the actual recipient email here
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        echo 'Message has been sent to all recipients';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
// Check for status update submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_status']) && isset($_POST['id'])) {
    $id = $_POST['id'];  // Get the specific ID of the booking being updated
    $newStatus = $_POST['new_status'];

    // Update the status in the book table
    $updateQuery = "UPDATE book SET status = ? WHERE id = ?";
    $stmt = $dbconnection->prepare($updateQuery);
    $stmt->bind_param("si", $newStatus, $id);
    $stmt->execute();

    // Fetch email address for the current booking
    $emailQuery = "SELECT email FROM book WHERE id = ?";  // Fetch the email for the current booking
    $stmtEmail = $dbconnection->prepare($emailQuery);
    $stmtEmail->bind_param("i", $id);  // Bind the specific booking ID
    $stmtEmail->execute();
    $resultEmail = $stmtEmail->get_result();

    if ($emailRow = $resultEmail->fetch_assoc()) {
        $recipientEmail = $emailRow['email'];  // Fetch the email of the current recipient

        // Check if the email is valid before sending
        if (!empty($recipientEmail)) {
            $subject = "Status Update Notification";
            $body = "Your booking status has been updated to: $newStatus.";

            // Send email to the recipient
            sendEmail([$recipientEmail], $subject, $body);  // Send email to the specific recipient
        }
    }

    // Redirect back to the same page to reflect changes
    header("Location: " . $_SERVER['PHP_SELF'] . "?removed_id=" . $id);  // Pass the updated id
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
$query = "SELECT id, firstname, middlename, lastname, email, age, gender, contact_number, Address, gcash_picture, status 
          FROM book 
          WHERE id != ? AND status != 'Confirm' 
          LIMIT ?, ?";
$stmt = $dbconnection->prepare($query);
$removed_id = isset($_GET['removed_id']) ? (int)$_GET['removed_id'] : 0; // Get the removed id from the query parameter
$stmt->bind_param("iii", $removed_id, $offset, $results_per_page); // Bind the parameters
$stmt->execute();
$result = $stmt->get_result();
?>
<style>
    @media screen and (max-width: 768px) {
        table {
            display: block;
            width: 100%;
        }
        thead {
            display: none; /* Hide header on small screens */
        }
        
        tbody tr {
            display: block; /* Block display for each row */
            margin-bottom: 15px; /* Space between rows */
            border: 1px solid #ccc; /* Border around each row */
            padding: 10px; /* Padding for better spacing */
        }
        tbody td {
            display: flex; /* Flex display for content */
            justify-content: space-between; /* Space between label and value */
            padding: 10px; /* Padding for cells */
            border-bottom: 1px solid #ddd; /* Border below each cell */
        }
        tbody td::before {
            content: attr(data-label); /* Use data-label for the cell label */
            font-weight: bold; /* Make label bold */
            color: #333; /* Label color */
            margin-right: 50px; /* Space between label and value */
        }
    }

    .table {
        width: 100%;
        border-collapse: collapse; /* Ensures borders are collapsed for better appearance */
    }

    .table th, .table td {
        padding: 12px; /* Added padding for better spacing */
        text-align: left; /* Align text to the left */
        border: 1px solid #ddd; /* Border for better visibility */
    }

    .table th {
        background-color: #f4f4f4; /* Optional: header background color */
        font-weight: bold; /* Optional: header font weight */
    }

    /* Optional: Hover effect for rows */
    .table tbody tr:hover {
        background-color: #f1f1f1; /* Highlight row on hover */
    }
     h3{
        margin-left: 10px;
    }
</style>

<div class="row">
    <div class="col-sm-2 col-md-2 col-lg-2 col-12">
        <?php include('sidebar.php'); ?>
    </div><br><br><br>
    <div class="col-sm-9 col-md-9 col-lg-9 col-12">
        <h3>Book Information</h3>
        <br />
        
        <div class="table-responsive">
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
                        <th>GCash Picture</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td data-label="Firstname"><?php echo htmlspecialchars($row['firstname']); ?></td>
                            <td data-label="Middlename"><?php echo htmlspecialchars($row['middlename']); ?></td>
                            <td data-label="Lastname"><?php echo htmlspecialchars($row['lastname']); ?></td>
                            <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td data-label="Age"><?php echo htmlspecialchars($row['age']); ?></td>
                            <td data-label="Gender"><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td data-label="Contact Number"><?php echo htmlspecialchars($row['contact_number']); ?></td>
                            <td data-label="Address"><?php echo htmlspecialchars($row['Address']); ?></td>
                            <td data-label="GCash Picture">
                                <?php
                                // GCash Picture logic...
                                if (!empty($row['gcash_picture'])) {
                                    $gcash_picture = preg_replace('/^uploads\/gcash_pictures\//', '', $row['gcash_picture']);
                                    $image_path = "../uploads/gcash_pictures/" . $gcash_picture;
                                    $full_path = realpath($image_path);
                                    
                                    if ($full_path && file_exists($full_path) && is_readable($full_path)) {
                                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                                        $mime_type = finfo_file($finfo, $full_path);
                                        finfo_close($finfo);
                                        
                                        if (strpos($mime_type, 'image') === 0) {
                                            echo '<a href="' . htmlspecialchars($image_path) . '" data-fancybox="gallery" data-caption="GCash Picture">';
                                            echo '<img src="' . htmlspecialchars($image_path) . '" alt="GCash Picture" style="width: 100px; height: 100px;">';
                                            echo '</a>';
                                        } else {
                                            echo 'Invalid file type';
                                        }
                                    } else {
                                        echo 'Image file not found or not readable';
                                    }
                                } else {
                                    echo 'No picture';
                                }
                                ?>
                            </td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <select name="new_status">
                                        <option value="Confirm">Confirm</option>
                                        <option value="Reject">Reject</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>

                                <!-- Delete form -->
                                <form method="POST" action="" style="margin-top: 5px;">
                                    <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



        <!-- Pagination -->
        <ul class="pagination">
            <li><a href="?pageno=1"><i class="fa fa-fast-backward"></i> First</a></li>
            <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
                <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>"><i class="fa fa-step-backward"></i> Prev</a>
            </li>
            <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
                <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>">Next <i class="fa fa-step-forward"></i></a>
            </li>
            <li><a href="?pageno=<?php echo $total_pages; ?>">Last <i class="fa fa-fast-forward"></i></a></li>
        </ul>
    </div>
</div>

<?php include('footer.php'); ?>
