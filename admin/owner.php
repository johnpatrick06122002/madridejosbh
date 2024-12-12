
 <?php
session_start(); // Start session

// Check if the admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit;
}

include('header.php'); // Include header

if (isset($_POST['delete_user'])) {
    $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);

    // Step 1: Delete boarders associated with the user's boarding house
    $delete_boarders = "DELETE FROM book WHERE register1_id = ?";
    $stmt = $dbconnection->prepare($delete_boarders);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Step 2: Delete the boarding house from the rental table
    $delete_rental = "DELETE FROM rental WHERE register1_id = ?";
    $stmt = $dbconnection->prepare($delete_rental);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Step 3: Delete from subscriptions table
    $delete_subscription = "DELETE FROM subscriptions WHERE register1_id = ?";
    $stmt = $dbconnection->prepare($delete_subscription);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Step 4: Delete from register2 table
    $delete_register2 = "DELETE FROM register2 WHERE register1_id = ?";
    $stmt = $dbconnection->prepare($delete_register2);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Step 5: Finally delete from register1 table
    $delete_register1 = "DELETE FROM register1 WHERE id = ?";
    $stmt = $dbconnection->prepare($delete_register1);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "User and associated data successfully deleted",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = "user_subscriptions.php";
                });
              </script>';
    } else {
        echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Error deleting user",
                    text: "' . $stmt->error . '",
                });
              </script>';
    }
    $stmt->close();
}

// Pagination setup
$records_per_page = 5; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure the page is at least 1
$offset = ($page - 1) * $records_per_page; // Calculate offset for the current page

// Count total records for pagination
$sql_count = "
    SELECT COUNT(*) AS total_records
    FROM register1 r1
    INNER JOIN register2 r2 ON r1.id = r2.register1_id
    LEFT JOIN subscriptions s ON r1.id = s.register1_id
    WHERE r1.confirmation = 'approved'
";
$result_count = $dbconnection->query($sql_count);
$total_records = $result_count->fetch_assoc()['total_records'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch paginated results
$query = "
    SELECT r1.id, r1.email, r2.firstname, r2.middlename, r2.lastname, r2.address, 
           r2.contact_number, r2.profile_photo, s.plan, s.status, s.start_date
    FROM register1 r1
    INNER JOIN register2 r2 ON r1.id = r2.register1_id
    LEFT JOIN subscriptions s ON r1.id = s.register1_id
    WHERE r1.confirmation = 'approved'
    LIMIT ? OFFSET ?
";
$stmt = $dbconnection->prepare($query);
$stmt->bind_param("ii", $records_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
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
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
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
h3{
    margin-left:5px;
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
        <h3>Landlord's info</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Profile Photo</th>
                        <th>Email</th>
                        <th>Full Name</th>
                        <th>Address</th>
                        <th>Contact Number</th>
                        <th>Subscription Plan</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td data-label="Profile Photo">
                                <?php if (!empty($row['profile_photo'])) { ?>
                                    <div class="profile-photo-wrapper">
                                        <img src="<?php echo '../uploads/' . $row['profile_photo']; ?>" alt="Profile Photo" class="profile-photo" onclick="openModal('<?php echo '../uploads/' . $row['profile_photo']; ?>')">
                                    </div>
                                <?php } else { ?>
                                    No Photo
                                <?php } ?>
                            </td>
                            <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td data-label="Full Name"><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']); ?></td>
                            <td data-label="Address"><?php echo htmlspecialchars($row['address']); ?></td>
                            <td data-label="Contact Number"><?php echo htmlspecialchars($row['contact_number']); ?></td>
                            <td data-label="Subscription Plan"><?php echo htmlspecialchars($row['plan']); ?></td>
                            <td data-label="Status"><?php echo ucfirst(htmlspecialchars($row['status'])); ?></td>
                            <td data-label="Start Date"><?php echo date('F d, Y', strtotime($row['start_date'])); ?></td>
                            <td data-label="Actions">
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_user" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>   <!-- Enhanced Pagination -->
<?php if ($total_records == 0) { ?>
    <p>No approved users found.</p>
<?php } else { ?>
    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <!-- Previous Button -->
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <!-- Page Numbers -->
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php } ?>

            <!-- Next Button -->
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
