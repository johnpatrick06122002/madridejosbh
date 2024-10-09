<?php require '../connection.php'; ?>
<?php $landlordid = $_POST['landlordid'] ?>

<?php if(isset($_POST["query"])) { ?>


<?php


$search = mysqli_real_escape_string($dbconnection, $_POST["query"]);
$sql = "SELECT * FROM book where landlord_id='$landlordid' AND name LIKE '%".$search."%'";
$result = $dbconnection->query($sql);

  while($row = $result->fetch_assoc()) {
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
            
            <form action="" method="POST">
              <input type="hidden" name="rowid" value="<?php echo $row['id']; ?>">
              <button type="submit" name="approve" class="btn btn-primary">APPROVE</button>
            </form>
            
          <?php } else { ?>
            <button class="btn btn-secondary" disabled>APPROVED</button>
          <?php } ?>

          
        </td>
      </tr>
  <?php } ?>




<?php } ?>

