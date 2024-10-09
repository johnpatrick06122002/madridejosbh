<?php include('header.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
// Fetch owner details
$owner_id = $_GET['owner_id'];
$sql_edit = "SELECT * FROM landlords WHERE id='$owner_id'";
$result_edit = mysqli_query($dbconnection, $sql_edit);

while ($row_edit = $result_edit->fetch_assoc()) {
    $name = $row_edit['name'];
    $email = $row_edit['email'];
    $address = $row_edit['Address'];
    $contact_number = $row_edit['contact_number'];
    $facebook = $row_edit['facebook'];
}
?>

<?php
if (isset($_POST["update"])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $facebook = $_POST['facebook'];

    $profile_photo = $_FILES['profile_photo']['name'];
    $target = "../uploadss/" . basename($profile_photo);

    if (!empty($profile_photo)) {
        move_uploadsed_file($_FILES['profile_photo']['tmp_name'], $target);
        $sql = "UPDATE landlords SET name='$name', email='$email', Address='$address', contact_number='$contact_number', facebook='$facebook', profile_photo='$profile_photo' WHERE id='$owner_id'";
    } else {
        $sql = "UPDATE landlords SET name='$name', email='$email', Address='$address', contact_number='$contact_number', facebook='$facebook' WHERE id='$owner_id'";
    }

    if ($dbconnection->query($sql) === TRUE) {
        echo '<script type="text/javascript">
            Swal.fire({
                icon: "success",
                title: "Updated",
                text: "Successfully Updated",
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "owner.php";
                }
            });
        </script>';
    } else {
        echo '<script type="text/javascript">
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Error updating record: ' . $dbconnection->error . '"
            });
        </script>';
    }
}
?>

<div class="row">
<div class="col-sm-2">
    <?php include('sidebar.php'); ?>
</div>

<div class="col-sm-9">
    <br />
    <h3>EDIT OWNER</h3>
    <br />
    <br />
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Full Name</label>
            <input name="name" type="text" class="form-control" value="<?php echo $name; ?>" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input name="email" type="email" class="form-control" value="<?php echo $email; ?>" required>
        </div>

        <div class="form-group">
            <label>Address</label>
            <input name="address" type="text" class="form-control" value="<?php echo $address; ?>" required>
        </div>

        <div class="form-group">
            <label>Contact Number</label>
            <input name="contact_number" type="text" class="form-control" value="<?php echo $contact_number; ?>" required>
        </div>

        <div class="form-group">
            <label>Facebook</label>
            <input name="facebook" type="url" class="form-control" value="<?php echo $facebook; ?>" required>
        </div>

        <div class="form-group">
            <label>Profile Photo</label>
            <input type="file" name="profile_photo" class="form-control">
        </div>

        <button type="submit" name="update" class="btn btn-primary"><i class="fa fa-pencil-square" aria-hidden="true"></i> UPDATE</button>
    </form>
</div>
</div>

<?php include('footer.php'); ?>
