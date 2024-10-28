<?php
include('header.php');

$rental_id = $_GET['bh_id'];
$sql_edit = "SELECT * FROM rental WHERE rental_id='$rental_id'";
$result_edit = mysqli_query($dbconnection, $sql_edit);

if ($result_edit && mysqli_num_rows($result_edit) > 0) {
    $row_edit = mysqli_fetch_assoc($result_edit);
    $title = $row_edit['title'];
    $address = $row_edit['address'];
    $slots = $row_edit['slots'];
    $monthly = $row_edit['monthly'];
    $description = $row_edit['description'];
    $wifi = $row_edit['wifi'];
    $water = $row_edit['water'];
    $kuryente = $row_edit['kuryente'];
    $downpayment_amount = $row_edit['downpayment_amount'];
    $installment_months = $row_edit['installment_months'];
    $installment_amount = $row_edit['installment_amount'];
} else {
    echo "Error: Rental details not found.";
    exit; // or handle the error appropriately
}
if (isset($_POST["create"])) {
    // Escape special characters in input data
    $title = mysqli_real_escape_string($dbconnection, $_POST['title']);
    $address = mysqli_real_escape_string($dbconnection, $_POST['address']);
    $slots = mysqli_real_escape_string($dbconnection, $_POST['slots']);
    $monthly = floatval(str_replace(',', '', $_POST['monthly']));
    $description = mysqli_real_escape_string($dbconnection, $_POST['description']);

    // Check if latitude and longitude are set to construct map URL
    if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $map = "https://maps.google.com/maps?q=" . $latitude . "," . $longitude . "&t=&z=15&ie=UTF8&iwloc=&output=embed";
    } else {
        $map = ""; // Handle the case where latitude and longitude are not set
    }

    // File upload
    $photo = $_FILES['photo']['name'];
    $target = "../uploads/" . basename($photo);

    // Checkbox values
    $freewifi = isset($_POST['free_wifi']) ? 'yes' : 'no';
    $freewater = isset($_POST['free_water']) ? 'yes' : 'no';
    $freekuryente = isset($_POST['free_kuryente']) ? 'yes' : 'no';

    // Payment type selection
    $payment_type = $_POST['payment_type'];

    if ($payment_type === 'installment') {
        // Installment selected, clear downpayment data
        $installment_months = intval($_POST['installment_months']);
        $installment_amount = floatval(str_replace(',', '', $_POST['installment_amount']));
        $downpayment_amount = null; // Clear downpayment data
    } else if ($payment_type === 'downpayment') {
        // Downpayment selected, clear installment data
        $downpayment_amount = floatval(str_replace(',', '', $_POST['downpayment_amount']));
        $installment_months = null; // Clear installment months
        $installment_amount = null;  // Clear installment amount
    }

    // Update query
    $sql = "UPDATE rental SET 
        title='$title', 
        address='$address', 
        slots='$slots', 
        map='$map', 
        photo='$photo', 
        description='$description', 
        register1_id='$login_session', 
        monthly='$monthly', 
        wifi='$freewifi', 
        water='$freewater', 
        kuryente='$freekuryente', 
        downpayment_amount=" . ($downpayment_amount !== null ? "'$downpayment_amount'" : "NULL") . ", 
        installment_months=" . ($installment_months !== null ? "'$installment_months'" : "NULL") . ", 
        installment_amount=" . ($installment_amount !== null ? "'$installment_amount'" : "NULL") . " 
        WHERE rental_id='$rental_id'";

    if ($dbconnection->query($sql) === TRUE) {
        echo '<script>';
        echo 'Swal.fire({
            icon: "success",
            title: "Successfully Updated!",
            showConfirmButton: true,
            confirmButtonText: "OK",
        }).then(function() {
            window.location.href = "bhouse.php";
        });';
        echo '</script>';

        move_uploaded_file($_FILES['photo']['tmp_name'], $target);

        // Gallery upload
        if (!empty($_FILES['gallery']['name'][0])) {
            $totalfiles = count($_FILES['gallery']['name']);
            for ($i = 0; $i < $totalfiles; $i++) {
                $filename = $_FILES['gallery']['name'][$i];
                if (move_uploaded_file($_FILES["gallery"]["tmp_name"][$i], '../uploads/' . $filename)) {
                    $insert = "INSERT INTO gallery (file_name, rental_id) VALUES ('$filename', '$rental_id')";
                    mysqli_query($dbconnection, $insert);
                }
            }
        }
    } else {
        echo '<script>';
        echo 'Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error updating record: ' . $dbconnection->error . '",
            showConfirmButton: true,
            confirmButtonText: "OK"
        }).then(function() {
            window.location.href = "bhouse.php";
        });';
        echo '</script>';
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
                <label>ID</label>
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
                    <div class="form-group">
                        <label>Address</label>
                        <input name="address" type="text" class="form-control" value="<?php echo $address; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Number of Bedspacer</label>
                        <input name="slots" type="number" class="form-control" value="<?php echo $slots; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Price Monthly (₱<span id="pricechanger"><?php echo number_format($monthly, 2); ?></span>) </label>
                        <input type="hidden" id="price" name="monthly" value="<?php echo $monthly; ?>">
                        <input type="range" class="form-control" min="500" max="5000" value="<?php echo $monthly; ?>" step="100" oninput="updatePrice(this.value)">
                    </div>
                    
                   <div class="form-group">
    <label>Select Payment Type</label>
    <select id="paymentType" name="payment_type" class="form-control" onchange="togglePaymentOptions(this.value)">
        <option value="installment">Installment</option>
        <option value="downpayment">Downpayment</option>
    </select>
</div>

<div id="installmentSection" style="display: none;">
    <div class="form-group">
        <label>Installment Months</label>
        <select name="installment_months" class="form-control">
            <option value="2" <?php echo ($installment_months == 2) ? 'selected' : ''; ?>>2 months</option>
            <option value="3" <?php echo ($installment_months == 3) ? 'selected' : ''; ?>>3 months</option>
            <option value="4" <?php echo ($installment_months == 4) ? 'selected' : ''; ?>>4 months</option>
        </select>
    </div>
    <div class="form-group">
        <label>Installment Amount (₱<span id="installmentchanger"><?php echo number_format($installment_amount, 2); ?></span>)</label>
        <input type="hidden" id="installment" name="installment_amount" value="<?php echo $installment_amount; ?>">
        <input type="range" class="form-control" min="500" max="5000" value="<?php echo $installment_amount; ?>" step="100" oninput="updateInstallment(this.value)">
    </div>
</div>

<div id="downpaymentSection" style="display: none;">
    <div class="form-group">
        <label>Downpayment Amount</label>
        <input name="downpayment_amount" type="text" class="form-control" value="<?php echo number_format($downpayment_amount, 2); ?>" required>
    </div>
</div>
                    <br />
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col">
                                <input type="checkbox" name="free_wifi" <?php echo $wifi == 'yes' ? 'checked' : ''; ?>>
                                <label>Free Wifi</label><br>
                            </div>
                            <div class="col">
                                <input type="checkbox" name="free_water" <?php echo $water == 'yes' ? 'checked' : ''; ?>>
                                <label>Free Water</label><br>
                            </div>
                            <div class="col">
                                <input type="checkbox" name="free_kuryente" <?php echo $kuryente == 'yes' ? 'checked' : ''; ?>>
                                <label>Free Kuryente</label><br>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="form-group">
                        <label>Photo</label><br />
                        <input type="file" name="photo" accept=".png,.jpeg,.jpg" required>
                    </div>
                    <br />
                    <div class="form-group">
                        <label>Gallery</label><br />
                        <input type="file" name="gallery[]" multiple accept=".png,.jpeg,.jpg">
                    </div>
                </div>
               <div class="col">
                    <center>
                        <?php include('map.php'); ?>
                    </center>
                </div>
            </div>
            <br>
            <div class="form-group">
                <input type="submit" name="create" value="Update Boarding House" class="btn btn-primary btn-block">
            </div>
            
        </form>
    </div>
</div>
<script src="../assets/js/toggle.js"></script>
<script src="../assets/js/price.js"></script>
<script>
    function updatePrice(value) {
        document.getElementById('pricechanger').innerHTML = new Intl.NumberFormat().format(value);
        document.getElementById('price').value = value;
    }

   function togglePaymentOptions(value) {
        if (value === 'installment') {
            document.getElementById('installmentSection').style.display = 'block';
            document.getElementById('downpaymentSection').style.display = 'none';
        } else {
            document.getElementById('installmentSection').style.display = 'none';
            document.getElementById('downpaymentSection').style.display = 'block';
        }
    }

    // Function to update installment amount
    function updateInstallment(value) {
        document.getElementById('installmentchanger').textContent = parseFloat(value).toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('installment').value = value;
    }

    // Trigger the toggle function on page load if necessary
    document.addEventListener("DOMContentLoaded", function() {
        togglePaymentOptions(document.getElementById('paymentType').value);
    });
</script>
<?php include('footer.php'); ?>
