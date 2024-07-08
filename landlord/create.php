<?php include('header.php'); ?>



<?php 

if(isset($_POST["create"])) {

$rental_id = $_POST['rental_id'];
$title = $_POST['title'];;
$address = $_POST['address'];
$slots = $_POST['slots'];
$monthly = $_POST['monthly'];
$map = "https://maps.google.com/maps?q=".$_POST['latitude'].",".$_POST['longitute']."&t=&z=15&ie=UTF8&iwloc=&output=embed";
$description = $_POST['description'];
$photo = $_FILES['photo']['name'];
$target = "../uploads/".basename($photo);


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



$sql = "INSERT INTO rental (rental_id, title, address, slots, map, photo, description, landlord_id, monthly, wifi, water, kuryente) VALUES ('$rental_id','$title', '$address', '$slots', '$map', '$photo', '$description', '$login_session', '$monthly', '$freewifi', '$freewater', '$freekuryente')";


if ($dbconnection->query($sql) === TRUE) {
	echo '<script type="text/javascript">alert("Successfully Added");</script>';
	move_uploaded_file($_FILES['photo']['tmp_name'], $target);

		//gallery 
	$totalfiles = count($_FILES['gallery']['name']);

	// Looping over all files
	for($i=0;$i<$totalfiles;$i++){
	$filename = $_FILES['gallery']['name'][$i];
	 
		// Upload files and store in database
		if(move_uploaded_file($_FILES["gallery"]["tmp_name"][$i],'../uploads/'.$filename)){
		        // Image db insert sql
		        $insert = "INSERT into gallery (file_name,rental_id) values('$filename','$rental_id')";
		        mysqli_query($dbconnection, $insert);
		 }
    
	}

    
}
    
}
 ?>

<div class="row">
<div class="col-sm-2">
<?php include('sidebar.php'); ?>
</div>

<div class="col-sm-9">

<?php 
//check if already approved
$sql_check = "SELECT status FROM landlords WHERE id='$login_session'";
$result_check = mysqli_query($dbconnection,$sql_check);
while($row_check = $result_check->fetch_assoc()) {
$status = $row_check['status'];
if ($status == 'Approved') {


?>

<br />
<h3>POST NEW BOARDING HOUSE</h3>  
<br />
<br />
<form action="" method="POST" enctype="multipart/form-data">
	<?php $number =  random_int(100, 100000); ?>
	
<div class="form-group" hidden>
    <label >ID</label>
    <input class="form-control" type="text" name="rental_id" value="<?php echo $number; ?>" readonly>
  </div>

  <div class="form-group">
    <label>Boarding House Name</label>
    <input name="title" type="text" class="form-control" required>
  </div>


	<div class="form-group">
    <label>Description</label>
    
    <div class="page-wrapper box-content">
	<textarea class="content" name="description" required></textarea>
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
    <input name="address" type="text" class="form-control" required>
  </div>
   <div class="form-group">
    <label>Number of Bedspacer</label>
    <input name="slots" type="number" class="form-control" required>
  </div>
  <div class="form-group">
    <label>Price Monthly (₱<span id="pricechanger">500</span>) </label>
    <input type="hidden" id="price" name="monthly" value="500">
    <input type="range" class="form-control" min="300" max="5000" value="500" step="50">
  </div>

<br />

  <div class="form-group">
    <div class="form-row">
      <div class="col">
        <input type="checkbox" name="free_wifi">
        <label>Free Wifi</label><br>
      </div>
      <div class="col">
        <input type="checkbox" name="free_water">
        <label>Free Water</label><br>
      </div>
      <div class="col">
        <input type="checkbox" name="free_kuryente">
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




  

  <button type="submit" name="create" class="btn btn-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i> CREATE</button>
</form>


<?php
//check if already approved
} else {
  echo "<h1 class='pending'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i> You account is pending for approval.</h1>";
}


}


?>

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