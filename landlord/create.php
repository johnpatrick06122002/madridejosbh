<?php include('header.php'); ?>



<?php 
if(!isset($_SESSION['login_user'])){
       header("location:../login.php");
       die();
     }
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
<head>
  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- Bootstrap CSS -->
  <link rel="shortcut icon" type="x-icon" href="../b.png">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-2">
        <!-- Sidebar can be included here -->
      </div>

      <div class="col-sm-9">
        <?php 
        $sql_check = "SELECT status FROM subscriptions WHERE register1_id='$login_session' AND status='active'";
        $result_check = mysqli_query($dbconnection, $sql_check);

        if (mysqli_num_rows($result_check) > 0) { ?>
        <br />
        <h3 class="text-center">POST NEW BOARDING HOUSE</h3>  
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
            <textarea class="form-control" name="description" rows="3" required></textarea>
          </div>
          
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
            <input type="range" class="form-control-range" min="300" max="5000" value="500" step="50">
          </div>

          <div class="form-group">
            <label>Features</label><br>
            <div class="form-check form-check-inline">
              <input type="checkbox" name="free_wifi" class="form-check-input">
              <label class="form-check-label">Free Wifi</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" name="free_water" class="form-check-input">
              <label class="form-check-label">Free Water</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" name="free_kuryente" class="form-check-input">
              <label class="form-check-label">Free Kuryente</label>
            </div>
          </div>

          <div class="form-group">
            <label>Photo</label>
            <input type="file" name="photo" class="form-control-file">
          </div>

          <div class="form-group">
            <label>Gallery</label>
            <input type="file" name="gallery[]" class="form-control-file" multiple>
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
<!-- Add Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="form-group text-center">
  <label>Location (Click on the map to select your area)</label>
  <div id="map" class="embed-responsive embed-responsive-16by9" style="height: 400px;"></div>
</div>
<input type="hidden" name="latitude" id="latitude">
<input type="hidden" name="longitude" id="longitude">



          <button type="submit" name="create" class="btn btn-primary btn-block">
            <i class="fa fa-plus-circle" aria-hidden="true"></i> CREATE
          </button>
        </form>

        <?php } else { ?>
        <div class="alert alert-danger text-center mt-3">
          Please subscribe to add a new boarding house.
        </div>
        <?php } ?>

        <br /><br />
      </div>
    </div>
  </div>

  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Add Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  // Default coordinates for Madridejos
  const defaultLocation = [11.2663, 123.7202];
  let map, marker;

  // Initialize the map
  map = L.map('map').setView(defaultLocation, 15); // Set view to Madridejos with zoom level 15

  // Add OpenStreetMap tiles
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);

  // Add a marker at the default location
  marker = L.marker(defaultLocation, { draggable: true }).addTo(map);

  // Update latitude and longitude fields on marker drag
  marker.on('dragend', function (e) {
    const latLng = e.target.getLatLng();
    document.getElementById('latitude').value = latLng.lat;
    document.getElementById('longitude').value = latLng.lng;
  });

  // Update marker position and fields when the map is clicked
  map.on('click', function (e) {
    const { lat, lng } = e.latlng;
    marker.setLatLng([lat, lng]); // Move marker to clicked location
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
  });

  // Initialize the latitude and longitude fields
  document.getElementById('latitude').value = defaultLocation[0];
  document.getElementById('longitude').value = defaultLocation[1];
</script>

  <!-- Custom JS -->
  <script>
  document.getElementById('payment_policy').addEventListener('change', function () {
    var policy = this.value;
    document.getElementById('downpayment-section').style.display = policy === 'downpayment' ? 'block' : 'none';
    document.getElementById('installment-section').style.display = policy === 'installment' ? 'block' : 'none';
  });

  document.querySelector('input[type="range"]').addEventListener('input', function() {
    document.getElementById('pricechanger').textContent = this.value;
    document.getElementById('price').value = this.value;
  });
  </script>

<?php include('footer.php'); ?>
