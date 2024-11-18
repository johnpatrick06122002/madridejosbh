<?php
// Include database connection
include('../connection.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer classes
require '../vendor_copy/autoload.php'; // Adjust the path according to your structure
// Check if landlord is logged in and get their ID
session_start();
if (!isset($_SESSION['register1_id'])) {
    header("Location: login.php");
    exit();
}
$landlord_id = $_SESSION['register1_id'];

// Function to fetch the monthly rental rate
function getMonthlyRateForRental($bhouseId) {
    global $dbconnection;
    $query = "SELECT monthly FROM rental WHERE rental_id = ? AND register1_id = ?";
    $stmt = $dbconnection->prepare($query);
    $stmt->bind_param("ii", $bhouseId, $_SESSION['register1_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    return ($row = $result->fetch_assoc()) ? $row['monthly'] : 0;
}

// Email sending function remains the same
function sendEmail($recipients, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'lucklucky2100@gmail.com';
        $mail->Password   = 'kjxf ptjv erqn yygv';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom('lucklucky2100@gmail.com', 'Your Name');
        foreach ($recipients as $email) {
            $mail->addAddress($email);
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

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_status']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $newStatus = $_POST['new_status'];
    
    // Verify this booking belongs to the logged-in landlord
    $verifyQuery = "SELECT b.* FROM book b 
                   JOIN rental r ON b.bhouse_id = r.rental_id 
                   WHERE b.id = ? AND r.register1_id = ?";
    $stmt = $dbconnection->prepare($verifyQuery);
    $stmt->bind_param("ii", $id, $landlord_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update status
        $updateQuery = "UPDATE book SET status = ? WHERE id = ?";
        $stmt = $dbconnection->prepare($updateQuery);
        $stmt->bind_param("si", $newStatus, $id);
        $stmt->execute();

        // Send email notification
        $emailQuery = "SELECT email FROM book WHERE id = ?";
        $stmtEmail = $dbconnection->prepare($emailQuery);
        $stmtEmail->bind_param("i", $id);
        $stmtEmail->execute();
        $resultEmail = $stmtEmail->get_result();

        if ($emailRow = $resultEmail->fetch_assoc()) {
            $recipientEmail = $emailRow['email'];
            if (!empty($recipientEmail)) {
                $subject = "Booking Status Update";
                $body = "Your booking status has been updated to: $newStatus.";
                sendEmail([$recipientEmail], $subject, $body);
            }
        }
        
        header("Location: " . $_SERVER['PHP_SELF'] . "?updated_id=" . $id);
        exit();
    }
}

// Handle delete
if (isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];
    
    // Verify this booking belongs to the logged-in landlord
    $verifyQuery = "SELECT b.* FROM book b 
                   JOIN rental r ON b.bhouse_id = r.rental_id 
                   WHERE b.id = ? AND r.register1_id = ?";
    $stmt = $dbconnection->prepare($verifyQuery);
    $stmt->bind_param("ii", $deleteId, $landlord_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $deleteQuery = "DELETE FROM book WHERE id = ?";
        $stmt = $dbconnection->prepare($deleteQuery);
        $stmt->bind_param("i", $deleteId);
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?deleted_id=" . $deleteId);
            exit();
        }
    }
}

include('header.php');

// Pagination
$results_per_page = 8;
$pageno = isset($_GET['pageno']) ? (int)$_GET['pageno'] : 1;
$offset = ($pageno - 1) * $results_per_page;

// Get total pages for this landlord's bookings
$total_pages_sql = "SELECT COUNT(*) FROM book b 
                    JOIN rental r ON b.bhouse_id = r.rental_id 
                    WHERE r.register1_id = ? AND b.status != 'Confirm'";
$stmt = $dbconnection->prepare($total_pages_sql);
$stmt->bind_param("i", $landlord_id);
$stmt->execute();
$total_rows = $stmt->get_result()->fetch_row()[0];
$total_pages = ceil($total_rows / $results_per_page);

// Fetch bookings for this landlord
$query = "SELECT b.id, b.firstname, b.middlename, b.lastname, b.email, 
                 b.age, b.gender, b.contact_number, b.Address, b.gcash_picture, 
                 b.status, r.title as bhouse_title 
          FROM book b 
          JOIN rental r ON b.bhouse_id = r.rental_id 
          WHERE r.register1_id = ? AND b.status != 'Confirm'
          ORDER BY b.date_posted DESC 
          LIMIT ?, ?";
$stmt = $dbconnection->prepare($query);
$stmt->bind_param("iii", $landlord_id, $offset, $results_per_page);
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
    .pagination {
    display: -ms-flexbox;
    display: flex;
    
    list-style: none;
    border-radius: .25rem;
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

</style>

<div class="dashboard-container">
    <div class="sidebar-container">
        <?php include('sidebar.php'); ?>
    </div>
   
    <div class="main-content"> <br><br><br>
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
                                <!-- Delete form -->
<form method="POST" action="" style="margin-top: 5px;">
    <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?');">Delete</button>
</form>

                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>  <ul class="pagination">
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
</div>



        <!-- Pagination -->
      
    </div>
</div>

<?php include('footer.php'); ?>
