<?php
session_start(); // Start session at the beginning of the script

// Check if the admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit; // Stop further execution
}

include('header.php'); // Include header.php which contains necessary HTML and PHP code
?>
<?php
$approve_success = false;
$delete_success = false;

if (isset($_POST["approve"])) {
    $id = $_POST['rowid'];
    $sql_approve = "UPDATE landlords SET status='Approved' WHERE id=?";
    $stmt = $dbconnection->prepare($sql_approve);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $approve_success = true;
    }
    
    $stmt->close();
}

if (isset($_POST["delete"])) {
    $id = $_POST['rowid'];
    $sql_delete = "DELETE FROM landlords WHERE id=?";
    $stmt = $dbconnection->prepare($sql_delete);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $delete_success = true;
    }
    
    $stmt->close();
}
?>

<div class="row">
    <div class="col-sm-2">
        <?php include('sidebar.php'); ?>
    </div>
    <div class="col-sm-8"><br><br>
        <h3>Pending for Approval</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Contact Number</th>
                    <th>Facebook</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $pageno = isset($_GET['pageno']) ? $_GET['pageno'] : 1;
                $no_of_records_per_page = 8;
                $offset = ($pageno - 1) * $no_of_records_per_page;

                $sql_count = "SELECT COUNT(*) FROM landlords WHERE status=''";
                $result_count = mysqli_query($dbconnection, $sql_count);
                $total_rows = mysqli_fetch_array($result_count)[0];
                $total_pages = ceil($total_rows / $no_of_records_per_page);

                $sql_fetch = "SELECT * FROM landlords WHERE status='' LIMIT ?, ?";
                $stmt = $dbconnection->prepare($sql_fetch);
                $stmt->bind_param("ii", $offset, $no_of_records_per_page);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                ?>
                    <tr>
                        <td><img src="../uploads/<?php echo $row['profile_photo']; ?>" alt="Profile Photo" width="50" height="50"></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['Address']; ?></td>
                        <td><?php echo $row['contact_number']; ?></td>
                        <td><?php echo $row['facebook']; ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <form action="" method="POST" style="display:inline;">
                                    <input type="hidden" name="rowid" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="approve" class="btn btn-primary">APPROVE</button>
                                </form>
                                <form action="" method="POST" style="display:inline; margin-left: 5px;">
                                    <input type="hidden" name="rowid" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete" class="btn btn-danger">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php } 
                $stmt->close();
                ?>
            </tbody>
        </table>
        <ul class="pagination">
            <li><a href="?pageno=1"><i class="fa fa-fast-backward" aria-hidden="true"></i> First</a></li>
            <li class="<?php if ($pageno <= 1) { echo 'disabled'; } ?>">
                <a href="<?php if ($pageno <= 1) { echo '#'; } else { echo "?pageno=" . ($pageno - 1); } ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i> Prev</a>
            </li>
            <li class="<?php if ($pageno >= $total_pages) { echo 'disabled'; } ?>">
                <a href="<?php if ($pageno >= $total_pages) { echo '#'; } else { echo "?pageno=" . ($pageno + 1); } ?>">Next <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
            </li>
            <li><a href="?pageno=<?php echo $total_pages; ?>">Last <i class="fa fa-fast-forward" aria-hidden="true"></i></a></li>
        </ul>
    </div>
</div>

<?php include('footer.php'); ?>

<!-- Include SweetAlert2 library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<!-- JavaScript for SweetAlert2 dialogs -->
<script>
    // Function to show SweetAlert2 message for approve
    <?php if ($approve_success): ?>
        Swal.fire({
            icon: 'success',
            title: 'Successfully Approved',
            showConfirmButton: false,
            timer: 1500
        
        });
    <?php elseif (isset($_POST["approve"])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error in database.',
            showConfirmButton: false,
            timer: 1500
        });
    <?php endif; ?>

    // Function to show SweetAlert2 confirmation dialog for delete
    function confirmDelete() {
        return Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Action confirmed, continue with form submission
                return true;
            } else {
                // Action canceled, do nothing
                return false;
            }
        });
    }

    // Function to show SweetAlert2 message for delete success
    <?php if ($delete_success): ?>
        Swal.fire({
            icon: 'success',
            title: 'Successfully Deleted',
            showConfirmButton: false,
            timer: 1500
        
        });
    <?php elseif (isset($_POST["delete"])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error in database.',
            showConfirmButton: false,
            timer: 1500
        });
    <?php endif; ?>
</script>
