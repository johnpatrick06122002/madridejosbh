<?php
session_start(); // Start session to maintain logged-in status

// Check if the admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit;
}

// Include header and other necessary files
include('header.php');

// Handle delete operation
if (isset($_POST["delete_id"])) {
    $id = $_POST['delete_id'];
    $sql = "DELETE FROM rental WHERE rental_id='$id'";

    if ($dbconnection->query($sql) === TRUE) {
        echo "<script>Swal.fire('Deleted!', 'Record has been deleted.', 'success');</script>";
    } else {
        echo "<script>Swal.fire('Error!', 'Error deleting record: " . addslashes($dbconnection->error) . "', 'error');</script>";
    }
}

// Pagination logic
$pageno = isset($_GET['pageno']) ? (int)$_GET['pageno'] : 1;
$no_of_records_per_page = 8;
$offset = ($pageno - 1) * $no_of_records_per_page;

$total_pages_sql = "SELECT COUNT(*) FROM rental";
$result_pages = mysqli_query($dbconnection, $total_pages_sql);
$total_rows = mysqli_fetch_array($result_pages)[0];
$total_pages = ceil($total_rows / $no_of_records_per_page);

$sql = "SELECT * FROM rental ORDER BY id DESC LIMIT $offset, $no_of_records_per_page";
$result = mysqli_query($dbconnection, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boarding House List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<div class="row">
    <div class="col-sm-2">
        <?php include('sidebar.php'); ?>
    </div>
    <div class="col-sm-8"><br><br>
        <h3>Boarding House List</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Owner</th>
                    <th>View</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) {
                    $rent_id = $row['rental_id'];
                    $landlord_id = $row['register1_id'];
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td>
                            <?php
                            $sql_ll = "SELECT * FROM register2 WHERE register1_id='$landlord_id'";
                            $result_ll = mysqli_query($dbconnection, $sql_ll);
                            while ($row_ll = $result_ll->fetch_assoc()) {
                                echo htmlspecialchars($row_ll['firstname']);
                                if (!empty($row_ll['middlename'])) {
                                    echo " " . htmlspecialchars($row_ll['middlename']);
                                }
                                echo " " . htmlspecialchars($row_ll['lastname']);
                            }
                            ?>
                        </td>
                        <td class="col-md-1">
                            <a href="../view.php?bh_id=<?php echo htmlspecialchars($rent_id); ?>" class="btn btn-success"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        </td>
                        <td class="col-md-1">
                            <button type="button" class="btn btn-danger" onclick="confirmDelete('<?php echo $rent_id; ?>')"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <ul class="pagination">
            <li><a href="?pageno=1"><i class="fa fa-fast-backward" aria-hidden="true"></i> First</a></li>
            <li class="<?php if ($pageno <= 1) { echo 'disabled'; } ?>">
                <a href="<?php if ($pageno > 1) { echo "?pageno=" . ($pageno - 1); } ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i> Prev</a>
            </li>
            <li class="<?php if ($pageno >= $total_pages) { echo 'disabled'; } ?>">
                <a href="<?php if ($pageno < $total_pages) { echo "?pageno=" . ($pageno + 1); } ?>">Next <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
            </li>
            <li><a href="?pageno=<?php echo $total_pages; ?>">Last <i class="fa fa-fast-forward" aria-hidden="true"></i></a></li>
        </ul>
    </div>
</div>

<form id="delete-form" action="" method="POST" style="display: none;">
    <input type="hidden" name="delete_id" id="delete_id">
</form>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete-form').submit();
        }
    })
}
</script>

<?php include('footer.php'); ?>
</body>
</html>
