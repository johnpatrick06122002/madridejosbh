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
            $mail->Username = 'madridejosbh2@gmail.com'; // Your SMTP username
            $mail->Password = 'ougf gwaw ezwh jmng'; // Your SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('madridejosbh2@gmail.com', 'Madridejos Bh finder');  // Sender's email
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
        $mail->Username = 'madridejosbh2@gmail.com'; // Your SMTP username
        $mail->Password = 'ougf gwaw ezwh jmng'; // Your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('madridejosbh2@gmail.com', 'Madridejos Bh finder');  // Sender's email
        $mail->addAddress('madridejosbh2@gmail.com');  // Admin email (recipient)

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

/* Profile photo display styling */
.profile-photo-wrapper {
    width: 100px;
    height: 100px;
    overflow: hidden;
    border-radius: 4px;
    display: inline-block;
    position: relative;
    border: 1px solid #ddd;
}

.profile-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s ease-in-out;
}

.profile-photo:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Modal styles for full-size photo */
.modal {
    display: none;
    position: fixed;
    z-index: 1050;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    justify-content: center;
    align-items: center;
}

.modal-content {
    max-width: 90%;
    max-height: 90%;
    border-radius: 8px;
    box-shadow: 0 0.15rem 1.75rem rgba(0, 0, 0, 0.5);
}

.close {
    position: absolute;
    top: 20px;
    right: 30px;
    color: #fff;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: #f1f1f1;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .dashboard-container {
        flex-direction: column;
    }

    .sidebar-container {
        width: 100%;
        position: static;
    }

    .main-content {
        padding: 10px;
    }

    .table thead {
        display: none;
    }

    .table tbody td {
        display: block;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .table tbody td:before {
        content: attr(data-label);
        font-weight: bold;
        color: #007bff;
        display: inline-block;
        width: 120px;
    }

    .profile-photo-wrapper {
        width: 80px;
        height: 80px;
    }
}

h3 {
    margin-left: 5px;
}

/* Center pagination and enhance styles */
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.pagination .page-item {
    margin: 0 5px;
}

.pagination .page-link {
    color: #007bff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 12px;
    transition: background-color 0.2s, color 0.2s;
}

.pagination .page-link:hover {
    background-color: #007bff;
    color: #fff;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    color: #fff;
    border-color: #007bff;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #e9ecef;
}
</style>

<div class="dashboard-container">
    <div class="sidebar-container">
        <?php include('sidebar.php'); ?>
    </div>

    <div class="main-content"><br><br>
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
                            <td data-label="Profile Photo">
                                <?php if (!empty($row['profile_photo']) && file_exists($photo_path)) { ?>
                                    <div class="profile-photo-wrapper">
                                        <img src="<?php echo $photo_path; ?>" alt="Profile Photo" class="profile-photo" onclick="openModal('<?php echo $photo_path; ?>')">
                                    </div>
                                <?php } else { ?>
                                    No Photo
                                <?php } ?>
                            </td>
                            <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td data-label="Full Name"><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']); ?></td>
                            <td data-label="Address"><?php echo htmlspecialchars($row['address']); ?></td>
                            <td data-label="Contact Number"><?php echo htmlspecialchars($row['contact_number']); ?></td>
                            <td data-label="Created At"><?php echo date('F d, Y', strtotime($row['created_at'])); ?></td>
                            <td data-label="Actions">
                                <div class="btn-group">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="rowid" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="approve" class="btn btn-success btn-sm">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="rowid" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
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
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
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

<!-- Modal for full-size photo -->
<div id="photoModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<script>
function openModal(imageSrc) {
    const modal = document.getElementById('photoModal');
    const modalImage = document.getElementById('modalImage');
    modal.style.display = 'flex';
    modalImage.src = imageSrc;
}

function closeModal() {
    document.getElementById('photoModal').style.display = 'none';
}
</script>

<?php include('footer.php'); ?>
