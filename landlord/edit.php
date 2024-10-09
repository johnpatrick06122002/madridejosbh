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
} else {
    echo "Error: Rental details not found.";
    exit; // or handle the error appropriately
}

if (isset($_POST["create"])) {
    $title = $_POST['title'];
    $address = $_POST['address'];
    $slots = $_POST['slots'];
    $monthly = floatval(str_replace(',', '', $_POST['monthly'])); // Ensure monthly is treated as a float

    // Construct the map URL based on latitude and longitude from POST
    if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $map = "https://maps.google.com/maps?q=".$latitude.",".$longitude."&t=&z=15&ie=UTF8&iwloc=&output=embed";
    } else {
        $map = ""; // Handle the case where latitude and longitude are not set
    }

    $description = $_POST['description'];
    $photo = $_FILES['photo']['name'];
    $target = "../uploadss/".basename($photo);

    $freewifi = isset($_POST['free_wifi']) ? 'yes' : 'no';
    $freewater = isset($_POST['free_water']) ? 'yes' : 'no';
    $freekuryente = isset($_POST['free_kuryente']) ? 'yes' : 'no';

    $sql = "UPDATE rental SET title='$title', address='$address', slots='$slots', map='$map', photo='$photo', description='$description', landlord_id='$login_session', monthly='$monthly', wifi='$freewifi', water='$freewater', kuryente='$freekuryente' WHERE rental_id='$rental_id'";

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

        move_uploadsed_file($_FILES['photo']['tmp_name'], $target);

        // Gallery
        if (!empty($_FILES['gallery']['name'][0])) {
            $totalfiles = count($_FILES['gallery']['name']);
            for ($i = 0; $i < $totalfiles; $i++) {
                $filename = $_FILES['gallery']['name'][$i];
                if (move_uploadsed_file($_FILES["gallery"]["tmp_name"][$i], '../uploadss/'.$filename)) {
                    $insert = "INSERT into gallery (file_name, rental_id) values('$filename', '$rental_id')";
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
                        <label>Photo</label>
                        <input type="file" name="photo">
                    </div>
                    <div class="form-group">
                        <label>Gallery</label>
                        <input type="file" name="gallery[]" multiple>
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
function updatePrice(value) {
    document.getElementById('price').value = value;
    document.getElementById('pricechanger').textContent = parseInt(value).toLocaleString();
}
</script>

<?php include('footer.php'); ?>
