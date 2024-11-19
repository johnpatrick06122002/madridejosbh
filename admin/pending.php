<?php
session_start();

// Ensure only logged-in admins can access this page
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Include necessary files
include('../connection.php'); // Update with your actual DB connection file
include('header.php');

$approve_success = false;
$delete_success = false;

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor_copy/autoload.php';

// Handle Approve Request
if (isset($_POST['approve'])) {
    $id = filter_var($_POST['rowid'], FILTER_SANITIZE_NUMBER_INT);

    // Update database to mark the account as approved
    $sql_approve = "UPDATE register1 SET confirmation = 'approved' WHERE id = ?";
    $stmt = $dbconnection->prepare($sql_approve);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $approve_success = true;

        // Fetch email of the approved user
        $sql_fetch_email = "SELECT r1.email FROM register1 r1 WHERE r1.id = ?";
        $stmt_email = $dbconnection->prepare($sql_fetch_email);
        $stmt_email->bind_param("i", $id);
        $stmt_email->execute();
        $stmt_email->bind_result($email);
        $stmt_email->fetch();
        $stmt_email->close();

        // Send email notification to the user about the approval
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Set the SMTP server to Gmail
            $mail->SMTPAuth = true;
            $mail->Username = 'lucklucky2100@gmail.com'; // Your SMTP username
            $mail->Password = 'kjxf ptjv erqn yygv'; // Your SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('your-email@gmail.com', 'Admin');  // Sender's email
            $mail->addAddress($email);  // Recipient's email (the user being approved)

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Account Approved';
            $mail->Body    = 'Hello, your account has been approved! You can post your boarding house.';

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    $stmt->close();
}

// Handle Delete Request
if (isset($_POST['delete'])) {
    $id = filter_var($_POST['rowid'], FILTER_SANITIZE_NUMBER_INT);

    $sql_delete = "DELETE FROM register1 WHERE id = ?";
    $stmt = $dbconnection->prepare($sql_delete);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $delete_success = true;
    }

    $stmt->close();
}

// Pagination variables
$records_per_page = 5; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$page = max($page, 1); // Ensure the page is at least 1
$offset = ($page - 1) * $records_per_page; // Calculate offset

// Count total records for pagination
$sql_count = "
    SELECT COUNT(*) AS total_records 
    FROM register1 r1
    INNER JOIN register2 r2 ON r1.id = r2.register1_id
    WHERE r1.confirmation IS NULL OR r1.confirmation != 'approved'
";
$result_count = $dbconnection->query($sql_count);
$total_records = $result_count->fetch_assoc()['total_records'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch paginated records
$sql_fetch = "
    SELECT r1.id, r1.email, r1.created_at, 
           r2.firstname, r2.middlename, r2.lastname, r2.address, 
           r2.contact_number, r2.profile_photo 
    FROM register1 r1
    INNER JOIN register2 r2 ON r1.id = r2.register1_id
    WHERE r1.confirmation IS NULL OR r1.confirmation != 'approved'
    LIMIT $records_per_page OFFSET $offset
";
$result = $dbconnection->query($sql_fetch);

// Check if there is any new pending account, and notify via email
if ($total_records > 0) {
    // Send email to the admin (kenethducay12@gmail.com) if there is a new pending account
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Set the SMTP server to Gmail
        $mail->SMTPAuth = true;
        $mail->Username = 'lucklucky2100@gmail.com'; // Your SMTP username
        $mail->Password = 'kjxf ptjv erqn yygv'; // Your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('your-email@gmail.com', 'Admin');  // Sender's email
        $mail->addAddress('kenethducay12@gmail.com');  // Admin email (recipient)

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Pending Accounts';
        $mail->Body    = 'Hello, there are new pending accounts that require approval. Please check the admin panel for details.';

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
<style>
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
</style>
<div class="dashboard-container">
    <div class="sidebar-container">
        <?php include('sidebar.php'); ?>
    </div>

    <div class="main-content">
        <h3>Pending Landlord Accounts</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Profile Photo</th>
                        <th>Email</th>
                        <th>Full Name</th>
                        <th>Address</th>
                        <th>Contact Number</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        $photo_path = '../uploads/' . $row['profile_photo'];
                    ?>
                        <tr>
                            <td>
                                <?php if (!empty($row['profile_photo']) && file_exists($photo_path)) { ?>
                                    <img src="<?php echo $photo_path; ?>" alt="Profile Photo" class="img-thumbnail" width="100">
                                <?php } else { ?>
                                    No Photo
                                <?php } ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                            <td><?php echo date('F d, Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <div class="btn-group">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="rowid" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="approve" class="btn btn-success">Approve</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="rowid" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_records == 0) { ?>
            <p>No pending accounts found.</p>
        <?php } else { ?>
            <!-- Pagination Links -->
            <nav>
                <ul class="pagination">
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php } ?>
                    <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php } ?>
    </div>
</div>

<?php include('footer.php'); ?>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
<?php if ($approve_success): ?>
    Swal.fire({
        icon: 'success',
        title: 'Account Approved Successfully',
        showConfirmButton: false,
        timer: 1500
    });
<?php elseif ($delete_success): ?>
    Swal.fire({
        icon: 'success',
        title: 'Account Deleted Successfully',
        showConfirmButton: false,
        timer: 1500
    });
<?php elseif (isset($_POST['approve']) || isset($_POST['delete'])): ?>
    Swal.fire({
        icon: 'error',
        title: 'Something went wrong. Please try again!',
        showConfirmButton: true
    });
<?php endif; ?>
</script>

</body>
</html>
