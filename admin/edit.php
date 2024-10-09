<?php include('header.php'); ?>




<?php 

$rental_id = $_GET['bh_id'];
$sql_edit = "SELECT * FROM rental WHERE rental_id='$rental_id'";
$result_edit = mysqli_query($dbconnection,$sql_edit);

while($row_edit = $result_edit->fetch_assoc()) {
    $title = $row_edit['title'];
    $address = $row_edit['address'];
    $slots = $row_edit['slots'];
    $monthly = $row_edit['monthly'];
    $description = $row_edit['description'];
    $wifi = $row_edit['wifi'];
    $water = $row_edit['water'];
    $kuryente = $row_edit['kuryente'];
}

?>

<?php 

if(isset($_POST["create"])) {

$title = $_POST['title'];;
$address = $_POST['address'];
$slots = $_POST['slots'];
$monthly = $_POST['monthly'];
$map = "https://maps.google.com/maps?q=".$_POST['latitude'].",".$_POST['longitute']."&t=&z=15&ie=UTF8&iwloc=&output=embed";
$description = $_POST['description'];
$photo = $_FILES['photo']['name'];
$target = "../upload/".basename($photo);


if (isset($_POST['free_wifi'])) {
  $freewifi = 'yes';
} else {
  $freewifi = 'no';
}

if (isset($_POST['free_water'])) {
  $freewater = 'yes';
} else {
  $freewater = 'no';
}

if (isset($_POST['free_kuryente'])) {
  $freekuryente = 'yes';
} else {
  $freekuryente = 'no';
}



$sql = "UPDATE rental SET title='$title', address='$address', slots='$slots', map='$map', photo='$photo', description='$description', landlord_id='$login_session', monthly='$monthly', wifi='$freewifi', water='$freewater', kuryente='$freekuryente' WHERE rental_id='$rental_id'";


if ($dbconnection->query($sql) === TRUE) {
	echo '<script type="text/javascript">alert("Successfully Updated");</script>';
	move_uploaded_file($_FILES['photo']['tmp_name'], $target);

		//gallery 
	$totalfiles = count($_FILES['gallery']['name']);

	// Looping over all files
	for($i=0;$i<$totalfiles;$i++){
	$filename = $_FILES['gallery']['name'][$i];
	 
		// Upload files and store in database
		if(move_uploaded_file($_FILES["gallery"]["tmp_name"][$i],'../upload/'.$filename)){
		        // Image db insert sql
		        $insert = "INSERT into gallery (file_name,rental_id) values('$filename','$rental_id')";
		        mysqli_query($dbconnection, $insert);
		 }
    
	}

  echo "<script>window.location.href = 'bhouse.php';</script>";
    
}
    
}
 ?>






<div class="row">
<div class="col-sm-2">
<?php include('sidebar.php'); ?>
</div>

<div class="col-sm-9">
<br />
<h3>EDIT BOARDING HOUSE</h3>  
<br />
<br />
<form action="" method="POST" enctype="multipart/form-data">
	
<div class="form-group" hidden>
    <label >ID</label>
    <input class="form-control" type="text" name="rental_id" value="<?php echo $rental_id; ?>" readonly>
  </div>

  <div class="form-group">
    <label>Boarding House Name</label>
    <input name="title" type="text" class="form-control" value="<?php echo $title; ?>" required>
  </div>


	<div class="form-group">
    <label>Description</label>
    
    <div class="page-wrapper box-content">
	<textarea class="content" name="description" required><?php echo $description; ?></textarea>
	</div>
</div>


<div class="row">
	<div class="col">
 <!--  <div class="form-group">
    <label>Contact Number</label>
    <input name="number" type="number" class="form-control">
  </div> -->

  <div class="form-group">
    <label>Address</label>
    <input name="address" type="text" class="form-control" value="<?php echo $address; ?>" required>
  </div>
   <div class="form-group">
    <label>Number of Bedspacer</label>
    <input name="slots" type="number" class="form-control" value="<?php echo $slots; ?>" required>
  </div>
  <div class="form-group">
    <label>Price Monthly (₱<span id="pricechanger"><?php echo $monthly; ?></span>) </label>
    <input type="hidden" id="price" name="monthly" value="<?php echo $monthly; ?>">
    <input type="range" class="form-control" min="500" max="5000" value="<?php echo $monthly; ?>" step="100">
  </div>

<br />

  <div class="form-group">
    <div class="form-row">
      <div class="col">
        <?php if ($wifi == 'yes') { ?>
          <input type="checkbox" name="free_wifi" checked>
        <?php } else { ?>
          <input type="checkbox" name="free_wifi">
        <?php } ?>
        <label>Free Wifi</label><br>
      </div>
      <div class="col">
        <?php if ($water == 'yes') { ?>
          <input type="checkbox" name="free_water" checked>
        <?php } else { ?>
          <input type="checkbox" name="free_water">
        <?php } ?>
        <label>Free Water</label><br>
      </div>
      <div class="col">
        <?php if ($kuryente == 'yes') { ?>
          <input type="checkbox" name="free_kuryente" checked>
        <?php } else { ?>
          <input type="checkbox" name="free_kuryente">
        <?php } ?>
        <label>Free Kuryente</label><br>
      </div>
  </div>
</div>

<br />

  <div class="form-group">
    <label>Photo</label>
    <input type="file" name="photo" >
  </div>

  <div class="form-group">
    <label>Gallery</label>
    <input type="file" name="gallery[]" multiple >
  </div>


</div>
  <div class="col">
 <center>
<?php include('map.php'); ?>
</center>
</div>
 </div>




  

  <button type="submit" name="create" class="btn btn-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i> UPDATE</button>
</form>




</div>
</div>

<br />
<br />
<br />


<script type="text/javascript">
  $('input[type=range]').on('input', function () {
    var price = $(this).val();
    $('#price').val(parseInt(price).toLocaleString());
    $('#pricechanger').html(parseInt(price).toLocaleString());
});
</script>

<?php include('footer.php'); ?>