<?php
session_start(); // Start session at the beginning of the script

// Check if the admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit; // Stop further execution
}

include('header.php'); // Include header.php which contains necessary HTML and PHP code

// Handle owner deletion
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

// Set the number of records per page based on the dropdown selection or default value
if (isset($_POST['records_per_page'])) {
    $no_of_records_per_page = $_POST['records_per_page'];
} elseif (isset($_GET['records_per_page'])) {
    $no_of_records_per_page = $_GET['records_per_page'];
} else {
    $no_of_records_per_page = 10;
}
?>
<div class="row">
    <div class="col-sm-2">
        <?php include('sidebar.php'); ?>
    </div>

    <div class="col-sm-9"><br>
        <h3>Owner List</h3>

        <form action="" method="POST">
            <div class="input-group col-6 float-right">
                <input name="query" type="text" class="form-control" placeholder="Input name" id="search" value="<?php echo isset($_POST['query']) ? $_POST['query'] : ''; ?>">
                <div class="input-group-append">
                    <button name="search" class="btn btn-secondary" type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
        <br />
        <br />

        <div class="row">
            <div class="col-2">
                <form action="" method="POST" id="recordsPerPageForm">
                    <select name="records_per_page" class="form-control" onchange="document.getElementById('recordsPerPageForm').submit();">
                        <option value="5" <?php if ($no_of_records_per_page == 5) echo 'selected'; ?>>5</option>
                        <option value="10" <?php if ($no_of_records_per_page == 10) echo 'selected'; ?>>10</option>
                        <option value="15" <?php if ($no_of_records_per_page == 15) echo 'selected'; ?>>15</option>
                        <option value="20" <?php if ($no_of_records_per_page == 20) echo 'selected'; ?>>20</option>
                    </select>
                </form>
            </div>
        </div>
        <br />

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Profile Photo</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Contact Number</th>
                    <th>Facebook</th>
                    <th colspan="2">Action</th> <!-- Updated colspan to cover both action buttons -->
                </tr>
            </thead>
            <tbody id="ownerslist">
                <?php
                // Query parameters for search and pagination
                $search_query = "";
                $query_param = "";

                if (isset($_POST['search'])) {
                    $query = mysqli_real_escape_string($dbconnection, $_POST['query']);
                    $search_query = " AND name LIKE '%$query%'";
                    $query_param = "&query=" . urlencode($query);
                } elseif (isset($_GET['query'])) {
                    $query = mysqli_real_escape_string($dbconnection, $_GET['query']);
                    $search_query = " AND name LIKE '%$query%'";
                    $query_param = "&query=" . urlencode($query);
                }

                // Pagination setup
                if (isset($_GET['pageno'])) {
                    $pageno = $_GET['pageno'];
                } else {
                    $pageno = 1;
                }
                $offset = ($pageno - 1) * $no_of_records_per_page;

                // Fetch total records for pagination
                $total_pages_sql = "SELECT COUNT(*) FROM landlords WHERE status='Approved' $search_query";
                $result_pages = mysqli_query($dbconnection, $total_pages_sql);
                $total_rows = mysqli_fetch_array($result_pages)[0];
                $total_pages = ceil($total_rows / $no_of_records_per_page);

                // Fetch records based on search and pagination
                $sql = "SELECT * FROM landlords WHERE status='Approved' $search_query LIMIT $offset, $no_of_records_per_page";
                $result = mysqli_query($dbconnection, $sql);

                while ($row = $result->fetch_assoc()) {
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
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['Address']; ?></td>
                    <td><?php echo $row['contact_number']; ?></td>
                    <td><?php echo $row['facebook']; ?></td>
                    <td class="col-md-1">
                        <a href="edit_owner.php?owner_id=<?php echo $row['id']; ?><?php echo $query_param; ?>&records_per_page=<?php echo $no_of_records_per_page; ?>" class="btn btn-warning">
                            <i class="fa fa-pencil-square" aria-hidden="true"></i> Edit
                        </a>
                    </td>
                    <td class="col-md-1">
                        <form action="" method="POST">
                            <input type="hidden" name="rowid" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete" class="btn btn-danger delete">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php if (!isset($_POST['search'])) { ?>
            <ul class="pagination">
                <li><a href="?pageno=1<?php echo $query_param; ?>&records_per_page=<?php echo $no_of_records_per_page; ?>"><i class="fa fa-fast-backward" aria-hidden="true"></i> First</a></li>
                <li class="<?php if ($pageno <= 1) { echo 'disabled'; } ?>">
                    <a href="<?php if ($pageno <= 1) { echo '#'; } else { echo "?pageno=" . ($pageno - 1) . $query_param . "&records_per_page=" . $no_of_records_per_page; } ?>">
                        <i class="fa fa-chevron-left" aria-hidden="true"></i> Prev
                    </a>
                </li>
                <li class="<?php if ($pageno >= $total_pages) { echo 'disabled'; } ?>">
                    <a href="<?php if ($pageno >= $total_pages) { echo '#'; } else { echo "?pageno=" . ($pageno + 1) . $query_param . "&records_per_page=" . $no_of_records_per_page; } ?>">
                        Next <i class="fa fa-chevron-right" aria-hidden="true"></i>
                    </a>
                </li>
                <li><a href="?pageno=<?php echo $total_pages . $query_param; ?>&records_per_page=<?php echo $no_of_records_per_page; ?>">Last <i class="fa fa-fast-forward" aria-hidden="true"></i></a></li>
            </ul>
        <?php } ?>

    </div>
</div>

<?php include('footer.php'); ?>
