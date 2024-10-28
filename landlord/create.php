<?php include('header.php'); ?>
<head>
  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
  <!-- Your existing HTML content -->
  
  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

<?php 
if(isset($_POST["create"])) {
  // Escape all input values
  $rental_id = mysqli_real_escape_string($dbconnection, $_POST['rental_id']);
  $title = mysqli_real_escape_string($dbconnection, $_POST['title']);
  $address = mysqli_real_escape_string($dbconnection, $_POST['address']);
  $slots = mysqli_real_escape_string($dbconnection, $_POST['slots']);
  $monthly = mysqli_real_escape_string($dbconnection, $_POST['monthly']);
  $map = "https://maps.google.com/maps?q=".mysqli_real_escape_string($dbconnection, $_POST['latitude']).",".mysqli_real_escape_string($dbconnection, $_POST['longitude'])."&t=&z=15&ie=UTF8&iwloc=&output=embed";
  $description = mysqli_real_escape_string($dbconnection, $_POST['description']);
  $payment_policy = mysqli_real_escape_string($dbconnection, $_POST['payment_policy']);
 
  $downpayment_amount = isset($_POST['downpayment_amount']) ? mysqli_real_escape_string($dbconnection, $_POST['downpayment_amount']) : null;
  $installment_months = isset($_POST['installment_months']) ? mysqli_real_escape_string($dbconnection, $_POST['installment_months']) : null;
  $installment_amount = isset($_POST['installment_amount']) ? mysqli_real_escape_string($dbconnection, $_POST['installment_amount']) : null;
  
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

  // Insert data into rental table
  $sql = "INSERT INTO rental (rental_id, title, address, slots, map, photo, description, register1_id, monthly, wifi, water, kuryente, downpayment_amount, installment_months, installment_amount) 
          VALUES ('$rental_id', '$title', '$address', '$slots', '$map', '$photo', '$description', '$login_session', '$monthly', '$freewifi', '$freewater', '$freekuryente', '$downpayment_amount', '$installment_months', '$installment_amount')";

  if ($dbconnection->query($sql) === TRUE) {
    move_uploaded_file($_FILES['photo']['tmp_name'], $target);

    // Gallery handling
    $totalfiles = count($_FILES['gallery']['name']);
    for($i = 0; $i < $totalfiles; $i++){
      $filename = $_FILES['gallery']['name'][$i];
      if(move_uploaded_file($_FILES["gallery"]["tmp_name"][$i], '../uploads/'.$filename)){
        $insert = "INSERT INTO gallery (file_name, rental_id) VALUES ('$filename', '$rental_id')";
        mysqli_query($dbconnection, $insert);
      }
    }

    // Success alert and redirect
    echo '<script type="text/javascript">
      Swal.fire({
        title: "Success!",
        text: "Successfully Added",
        icon: "success",
        confirmButtonText: "OK"
      }).then(function() {
        window.location.href = "dashboard.php"; // Redirect to dashboard after OK click
      });
    </script>';
  } else {
    // Error alert
    echo '<script type="text/javascript">
      Swal.fire({
        title: "Error!",
        text: "There was an error adding the rental.",
        icon: "error",
        confirmButtonText: "OK"
      });
    </script>';
  }
}
?>

<div class="row">
  <div class="col-sm-2">
    <!-- Sidebar can be included here -->
  </div>

  <div class="col-sm-9">
 <?php 
    // Check if the user has an active subscription
    $sql_check = "SELECT status FROM subscriptions WHERE register1_id='$login_session' AND status='active'";
    $result_check = mysqli_query($dbconnection, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
?>
    <br />
    <h3>POST NEW BOARDING HOUSE</h3>  
    <br />
    <form action="" method="POST" enctype="multipart/form-data">
      <?php $number = random_int(100, 100000); ?>
      
      <div class="form-group" hidden>
        <label>ID</label>
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
          <div class="form-group">
            <label>Address</label>
            <input name="address" type="text" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Number of Bedspacers</label>
            <input name="slots" type="number" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Price Monthly (₱<span id="pricechanger">500</span>)</label>
            <input type="hidden" id="price" name="monthly" value="500">
            <input type="range" class="form-control" min="300" max="5000" value="500" step="50">
          </div>

          <div class="form-group">
            <input type="checkbox" name="free_wifi">
            <label>Free Wifi</label><br>
            <input type="checkbox" name="free_water">
            <label>Free Water</label><br>
            <input type="checkbox" name="free_kuryente">
            <label>Free Kuryente</label><br>
          </div>

          <div class="form-group">
            <label>Photo</label>
            <input type="file" name="photo">
          </div>

          <div class="form-group">
            <label>Gallery</label>
            <input type="file" name="gallery[]" multiple>
          </div>

          <div class="form-group">
            <label>Payment Policy</label>
            <select class="form-control" name="payment_policy" id="payment_policy" required>
              <option value="other">Choose your policy</option>
              <option value="downpayment">Downpayment</option>
              <option value="installment">Installment</option>
              
            </select>
          </div>

          <div class="form-group" id="downpayment-section" style="display: none;">
            <label>Downpayment Amount (₱)</label>
            <input type="number" class="form-control" name="downpayment_amount">
          </div>

          <div class="form-group" id="installment-section" style="display: none;">
            <label>Installment Plan (Months)</label>
            <select class="form-control" name="installment_months">
              <option value="3">3 months</option>
              <option value="6">6 months</option>
              <option value="12">12 months</option>
            </select>
            <label>Monthly Installment Amount (₱)</label>
            <input type="number" class="form-control" name="installment_amount">
          </div>

          
        </div>

        <div class="col">
          <center>
            <?php include('map.php'); ?>
          </center>
        </div>
      </div>

      <button type="submit" name="create" class="btn btn-primary">
        <i class="fa fa-plus-circle" aria-hidden="true"></i> CREATE
      </button>
    </form>

    <?php
      } else {
        // If no active subscription, show alert
        echo '<div class="alert alert-danger">Please subscribe to add a new boarding house.</div>';
      }
    ?>

    <br /><br />
  </div>
</div>

<!-- JavaScript to toggle downpayment and installment fields -->
<script>
document.getElementById('payment_policy').addEventListener('change', function () {
  var policy = this.value;
  document.getElementById('downpayment-section').style.display = policy === 'downpayment' ? 'block' : 'none';
  document.getElementById('installment-section').style.display = policy === 'installment' ? 'block' : 'none';
});

// Update price label based on range slider
document.querySelector('input[type="range"]').addEventListener('input', function() {
  document.getElementById('pricechanger').textContent = this.value;
  document.getElementById('price').value = this.value;
});
</script>

<?php include('footer.php'); ?>
