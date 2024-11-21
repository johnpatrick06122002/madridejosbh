<?php include('header.php'); ?>
<head>
  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- Bootstrap CSS -->
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
<div class="form-group text-center">
  <label>Location (Madridejos Area)</label>
  <div class="embed-responsive embed-responsive-16by9">
    <iframe 
      src="https://maps.google.com/maps?q=11.2663,123.7202&t=&z=15&ie=UTF8&iwloc=&output=embed" 
      class="embed-responsive-item" 
      allowfullscreen 
      loading="lazy" 
      style="border:0;">
    </iframe>
  </div>
</div>



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
