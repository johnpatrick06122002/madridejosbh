<?php require '../connection.php'; ?>


<?php if(isset($_POST["query"])) { ?>


<?php

$search_query = "";
        $query_param = "";
$search = mysqli_real_escape_string($dbconnection, $_POST["query"]);
$sql = "SELECT * FROM landlords WHERE status='Approved' AND name LIKE '%".$search."%'";
$result = $dbconnection->query($sql);

  while($row = $result->fetch_assoc()) {
    $status = $row['status'];
?>

      <tr>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td><?php echo $row['Address']; ?></td>
        <td><?php echo $row['contact_number']; ?></td>
        <td><?php echo $row['facebook']; ?></td>
       <td class="col-md-1">
                <a href="edit_owner.php?owner_id=<?php echo $row['id']; ?><?php echo $query_param; ?>" class="btn btn-warning">
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




<?php } ?>

