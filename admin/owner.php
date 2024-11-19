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
</style>
<div class="dashboard-container">
    <div class="sidebar-container">
        <?php include('sidebar.php'); ?>
    </div>

    <div class="main-content">
        <h3>User Subscription and Info</h3>
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
                    <?php while ($row = $result->fetch_assoc()) { 
                        $photo = '../uploads/' . $row['profile_photo'];
                    ?>
                        <tr>
                            <td>
                                <?php if (!empty($row['profile_photo']) && file_exists($photo)) { ?>
                                    <img src="<?php echo $photo; ?>" alt="Profile Photo" class="img-thumbnail" width="100">
                                <?php } else { ?>
                                    No Photo
                                <?php } ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['plan']); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($row['status'])); ?></td>
                            <td><?php echo date('F d, Y', strtotime($row['start_date'])); ?></td>
                            <td>
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
            </table>
        </div>

        <?php if ($total_records == 0) { ?>
            <p>No approved users found.</p>
        <?php } else { ?>
            <!-- Pagination -->
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
