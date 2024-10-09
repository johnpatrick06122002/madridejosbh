<?php
session_start(); // Start session

// Check if the admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit;
}

include('header.php'); // Include header

// Handle owner deletion (if still needed)
if (isset($_POST['delete'])) {
    $owner_id = $_POST['rowid'];
    $delete_sql = "DELETE FROM landlords WHERE id='$owner_id'";
    
    if ($dbconnection->query($delete_sql) === TRUE) {
        echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "Owner successfully deleted",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = "owner.php";
                });
              </script>';
    } else {
        echo "Error deleting record: " . $dbconnection->error;
    }
}

// Fetch user and subscription data
$query = "
    SELECT r1.email, r2.firstname, r2.middlename, r2.lastname, r2.address, r2.contact_number, r2.profile_photo, 
           s.plan, s.status, s.start_date
    FROM register1 r1
    INNER JOIN register2 r2 ON r1.id = r2.register1_id
    LEFT JOIN subscriptions s ON r1.id = s.register1_id
    WHERE s.status = 'active'"; // Modify this condition if you want to show inactive subscriptions too

$result = mysqli_query($dbconnection, $query);
?>
<div class="row">
    <div class="col-sm-2">
        <?php include('sidebar.php'); ?>
    </div>

    <div class="col-sm-9">
        <h3>User Subscription and Info</h3>
        <br />
        
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
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
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
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['contact_number']; ?></td>
                    <td><?php echo $row['plan']; ?></td>
                    <td><?php echo ucfirst($row['status']); ?></td>
                    <td><?php echo date('F d, Y', strtotime($row['start_date'])); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>
</div>

<?php include('footer.php'); ?>
