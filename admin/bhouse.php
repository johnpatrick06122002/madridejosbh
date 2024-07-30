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
if(isset($_POST["delete"])) {

$id = $_POST['rowid'];


$sql = "DELETE FROM rental WHERE id='$id'";

if ($dbconnection->query($sql) === TRUE) {
  echo "<script>alert('Record Deleted Successfully');</script>";
} else {
  echo "Error Deleting record: " . $dbconnection->error;
}

    
}
?>


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

      <?php

if (isset($_GET['pageno'])) {
  $pageno = $_GET['pageno'];
} else {
  $pageno = 1;
}
  $no_of_records_per_page = 8;
  $offset = ($pageno-1) * $no_of_records_per_page;

  $total_pages_sql = "SELECT COUNT(*) FROM rental";
  $result_pages = mysqli_query($dbconnection,$total_pages_sql);
  $total_rows = mysqli_fetch_array($result_pages)[0];
  $total_pages = ceil($total_rows / $no_of_records_per_page);

  $sql = "SELECT * FROM rental ORDER BY id DESC LIMIT $offset, $no_of_records_per_page ";
  $result = mysqli_query($dbconnection,$sql);
  while($row = $result->fetch_assoc()) {
    $rent_id = $row['rental_id'];
    $landlord_id = $row['landlord_id'];
?>

      <tr>
        <td><?php echo $row['title']; ?></td>
        <td>
<?php   $sql_ll = "SELECT * FROM landlords WHERE id='$landlord_id'";
  $result_ll = mysqli_query($dbconnection,$sql_ll);

  while($row_ll = $result_ll->fetch_assoc()) {
    echo $row_ll['name'];

} ?>
          
        </td>
        <td class="col-md-1"><a href="../view.php?bh_id=<?php echo $rent_id; ?>" class="btn btn-success"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
        <td class="col-md-1">
          <form action="" method="POST">
              <input type="hidden" name="rowid" value="<?php echo $row['id']; ?>">
              <button type="submit" name="delete" id="<?php echo $rent_id; ?>" class="btn btn-danger delete"><i  aria-hidden="true">Delete</i></button>
            </form>
          
        </td>
      </tr>

<?php } ?>

    </tbody>
  </table>
<ul class="pagination">
        <li><a href="?pageno=1"><i class="fa fa-fast-backward" aria-hidden="true"></i> First</a></li>
        <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
            <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i> Prev</a>
        </li>
        <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
            <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>">Next <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
        </li>
        <li><a href="?pageno=<?php echo $total_pages; ?>">Last <i class="fa fa-fast-forward" aria-hidden="true"></i></a></li>
    </ul>

</div>
</div>

<?php include('footer.php'); ?>