<?php 
 
include('header.php'); 

if (isset($_POST["approve"])) {
    $id = $_POST['rowid'];
    $sql_book = "UPDATE book SET status='Approved' WHERE id='$id'";
    if ($dbconnection->query($sql_book) === TRUE) {
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Successfully Booked'
        ];
    } else {
        $_SESSION['message'] = [
            'type' => 'error',
            'text' => 'Error updating database.'
        ];
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST["delete"])) {
    $id = $_POST['rowid'];
    $sql_delete = "DELETE FROM book WHERE id='$id'";
    if ($dbconnection->query($sql_delete) === TRUE) {
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Record Deleted'
        ];
    } else {
        $_SESSION['message'] = [
            'type' => 'error',
            'text' => 'Error deleting record.'
        ];
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST["update"])) {
    $id = $_POST['rowid'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];

    $sql_update = "UPDATE book SET name='$name', Address='$address', contact_number='$contact_number', age='$age', gender='$gender' WHERE id='$id'";
    if ($dbconnection->query($sql_update) === TRUE) {
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Record Updated'
        ];
    } else {
        $_SESSION['message'] = [
            'type' => 'error',
            'text' => 'Error updating record.'
        ];
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$query = "";
if (isset($_POST["search"])) {
    $query = $_POST['query'];
}

?>

<?php if (isset($_SESSION['message'])): ?>
    <script type="text/javascript">
        Swal.fire({
            icon: "<?php echo $_SESSION['message']['type']; ?>",
            title: "<?php echo $_SESSION['message']['text']; ?>",
            showConfirmButton: false,
            timer: 2000
        }).then(function() {
            window.location.reload();
        });
    </script>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>
<style>
    .fa {
    display: inline-block;
    font: normal normal normal 14px / 1 FontAwesome;
    font-size: inherit;
    text-rendering: auto;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    float: right; /* or */
    text-align: right; /* or */
    margin-left: 70px; /* or */

    /* any other method to position right */
}
</style>
<div class="row">
    <div class="col-sm-2">
        <?php include('sidebar.php'); ?>
    </div>

    <div class="col-sm-10">
        <br />
        <h3>Requesting Reservation</h3>
        <br />
        <br />

        <form action="" method="POST">
            <div class="input-group col-6 float-right">
                <input name="query" type="text" class="form-control" placeholder="Input name" value="<?php echo $query; ?>">
                <div class="input-group-append">
                    <button name="search" class="btn btn-secondary" type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <br />
        <br />

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Contact Number</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="bookerslist">
                <?php
                if (isset($_GET['pageno'])) {
                    $pageno = $_GET['pageno'];
                } else {
                    $pageno = 1;
                }

                $no_of_records_per_page = 10;
                $offset = ($pageno - 1) * $no_of_records_per_page;

                if ($query != "") {
                    $sql = "SELECT * FROM book WHERE landlord_id='$login_session' AND name LIKE '%$query%' LIMIT $offset, $no_of_records_per_page";
                    $total_pages_sql = "SELECT COUNT(*) FROM book WHERE landlord_id='$login_session' AND name LIKE '%$query%'";
                } else {
                    $sql = "SELECT * FROM book WHERE landlord_id='$login_session' LIMIT $offset, $no_of_records_per_page";
                    $total_pages_sql = "SELECT COUNT(*) FROM book WHERE landlord_id='$login_session'";
                }

                $result_pages = mysqli_query($dbconnection, $total_pages_sql);
                $total_rows = mysqli_fetch_array($result_pages)[0];
                $total_pages = ceil($total_rows / $no_of_records_per_page);

                $result = mysqli_query($dbconnection, $sql);

                while ($row = $result->fetch_assoc()) {
                    $status = $row['status'];
                ?>
                <tr>
                    <td><a href="../view.php?bh_id=<?php echo $row['bhouse_id']; ?>"><?php echo $row['bhouse_id']; ?></a></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['Address']; ?></td>
                    <td><?php echo $row['contact_number']; ?></td>
                    <td><?php echo $row['age']; ?></td>
                    <td><?php echo $row['gender']; ?></td>
                    <td>
                        <?php if ($status == '') { ?>
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="rowid" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="approve" class="btn btn-primary">APPROVE</button>
                        </form>
                        <?php } else { ?>
                        <button class="btn btn-secondary" disabled>APPROVED</button>
                        <?php } ?>

                        <!-- Delete button always displayed -->
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="rowid" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete" class="btn btn-danger delete">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </form>

                        <!-- Edit button conditionally displayed -->
                        <?php if ($status == 'Approved') { ?>
                        <button class="btn btn-warning edit" data-toggle="modal" data-target="#editModal<?php echo $row['id']; ?>">
                            <i class="fa fa-pencil" aria-hidden="true"></i>
                        </button>
                        <?php } ?>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel<?php echo $row['id']; ?>">Edit Record</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="" method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="rowid" value="<?php echo $row['id']; ?>">
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" class="form-control" name="name" value="<?php echo $row['name']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="address">Address</label>
                                                <input type="text" class="form-control" name="address" value="<?php echo $row['Address']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="contact_number">Contact Number</label>
                                                <input type="text" class="form-control" name="contact_number" value="<?php echo $row['contact_number']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="age">Age</label>
                                                <input type="number" class="form-control" name="age" value="<?php echo $row['age']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="gender">Gender</label>
                                                <select class="form-control" name="gender" required>
                                                    <option value="Male" <?php if ($row['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                                    <option value="Female" <?php if ($row['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" name="update" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <?php } ?>
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
